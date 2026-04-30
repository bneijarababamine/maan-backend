<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContributionRequest;
use App\Http\Resources\ContributionResource;
use App\Models\Bank;
use App\Models\Contribution;
use App\Models\ContributionMonth;
use App\Models\Member;
use App\Services\CloudinaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContributionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Contribution::with(['member', 'months', 'registeredBy']);

        if ($request->has('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $contributions = $query->orderByDesc('paid_at')->paginate(20);

        return response()->json([
            'status'  => true,
            'message' => 'Liste des cotisations récupérée.',
            'data'    => ContributionResource::collection($contributions)->response()->getData(true),
        ]);
    }

    public function store(StoreContributionRequest $request): JsonResponse
    {
        try {
            $contribution = DB::transaction(function () use ($request) {
                foreach ($request->months as $month) {
                    $exists = ContributionMonth::whereHas('contribution', fn($q) =>
                        $q->where('member_id', $request->member_id)
                    )->where('year', $month['year'])->where('month', $month['month'])->exists();

                    if ($exists) {
                        throw new \Exception("Le mois {$month['month']}/{$month['year']} a déjà été payé.");
                    }
                }

                $screenshots = $this->uploadScreenshots($request);

                $member = Member::findOrFail($request->member_id);
                $amountPerMonth = $member->monthly_amount ?? 200;

                $nextReceipt = (Contribution::max('receipt_number') ?? 0) + 1;

                $contribution = Contribution::create([
                    'receipt_number'       => $nextReceipt,
                    'member_id'            => $request->member_id,
                    'months_count'         => count($request->months),
                    'amount_per_month'     => $amountPerMonth,
                    'total_amount'         => count($request->months) * $amountPerMonth,
                    'payment_method'       => $request->payment_method,
                    'transaction_ref'      => $request->transaction_ref,
                    'screenshot_url'       => $screenshots[0]['url'] ?? null,
                    'screenshot_public_id' => $screenshots[0]['public_id'] ?? null,
                    'screenshots'          => $screenshots ?: null,
                    'registered_by'        => auth()->id(),
                    'notes'                => $request->notes,
                    'paid_at'              => $request->paid_at ?? now(),
                ]);

                foreach ($request->months as $month) {
                    ContributionMonth::create([
                        'contribution_id' => $contribution->id,
                        'year'            => $month['year'],
                        'month'           => $month['month'],
                    ]);
                }

                // Credit the corresponding bank account
                Bank::adjustByMethod($request->payment_method, (float) $contribution->total_amount);

                return $contribution->load(['member', 'months', 'registeredBy']);
            });

            return response()->json([
                'status'  => true,
                'message' => 'Cotisation enregistrée avec succès.',
                'data'    => new ContributionResource($contribution),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
                'data'    => null,
            ], 422);
        }
    }

    public function show(int $id): JsonResponse
    {
        $contribution = Contribution::with(['member', 'months', 'registeredBy'])->findOrFail($id);

        return response()->json([
            'status'  => true,
            'message' => 'Cotisation récupérée.',
            'data'    => new ContributionResource($contribution),
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'months'           => 'required|array|min:1',
            'months.*.year'    => 'required|integer',
            'months.*.month'   => 'required|integer|min:1|max:12',
            'payment_method'   => 'required|string|max:50',
            'paid_at'          => 'nullable|date',
            'notes'            => 'nullable|string',
            'transaction_ref'  => 'nullable|string',
        ]);

        try {
            $contribution = DB::transaction(function () use ($request, $id) {
                $contribution = Contribution::with('months')->findOrFail($id);

                foreach ($request->months as $month) {
                    $exists = ContributionMonth::whereHas('contribution', fn($q) =>
                        $q->where('member_id', $contribution->member_id)->where('id', '!=', $id)
                    )->where('year', $month['year'])->where('month', $month['month'])->exists();

                    if ($exists) {
                        throw new \Exception("Le mois {$month['month']}/{$month['year']} est déjà payé dans une autre cotisation.");
                    }
                }

                // Handle screenshot updates
                $keepPublicIds = $request->input('keep_public_ids', []);
                $current = $contribution->screenshots
                    ?? ($contribution->screenshot_url
                        ? [['url' => $contribution->screenshot_url, 'public_id' => $contribution->screenshot_public_id]]
                        : []);

                foreach ($current as $s) {
                    if (!empty($s['public_id']) && !in_array($s['public_id'], $keepPublicIds)) {
                        app(CloudinaryService::class)->delete($s['public_id']);
                    }
                }

                $kept = array_values(array_filter($current, fn($s) => in_array($s['public_id'] ?? '', $keepPublicIds)));
                $newUploads = $this->uploadScreenshots($request);
                $screenshots = array_merge($kept, $newUploads);

                // Reverse old bank credit, apply new one
                Bank::adjustByMethod($contribution->payment_method, -(float) $contribution->total_amount);
                $newTotal = count($request->months) * $contribution->amount_per_month;

                $contribution->months()->delete();

                $contribution->update([
                    'months_count'         => count($request->months),
                    'total_amount'         => count($request->months) * $contribution->amount_per_month,
                    'payment_method'       => $request->payment_method,
                    'transaction_ref'      => $request->transaction_ref,
                    'notes'                => $request->notes,
                    'paid_at'              => $request->paid_at ?? $contribution->paid_at,
                    'screenshots'          => $screenshots ?: null,
                    'screenshot_url'       => $screenshots[0]['url'] ?? null,
                    'screenshot_public_id' => $screenshots[0]['public_id'] ?? null,
                ]);

                foreach ($request->months as $month) {
                    ContributionMonth::create([
                        'contribution_id' => $contribution->id,
                        'year'            => $month['year'],
                        'month'           => $month['month'],
                    ]);
                }

                // Credit new bank account
                Bank::adjustByMethod($request->payment_method, $newTotal);

                return $contribution->fresh(['member', 'months', 'registeredBy']);
            });

            return response()->json([
                'status'  => true,
                'message' => 'Cotisation mise à jour.',
                'data'    => new ContributionResource($contribution),
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => null], 422);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $contribution = Contribution::findOrFail($id);

        // Reverse bank credit
        Bank::adjustByMethod($contribution->payment_method, -(float) $contribution->total_amount);

        foreach ($contribution->screenshots ?? [] as $s) {
            if (!empty($s['public_id'])) {
                app(CloudinaryService::class)->delete($s['public_id']);
            }
        }
        if (!$contribution->screenshots && $contribution->screenshot_public_id) {
            app(CloudinaryService::class)->delete($contribution->screenshot_public_id);
        }

        $contribution->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Cotisation supprimée.',
            'data'    => null,
        ]);
    }

    private function uploadScreenshots(Request $request): array
    {
        $screenshots = [];
        if ($request->payment_method === 'cash') return $screenshots;

        if ($request->hasFile('screenshots')) {
            foreach ($request->file('screenshots') as $file) {
                $uploaded = app(CloudinaryService::class)->upload($file, 'charity/screenshots');
                $screenshots[] = ['url' => $uploaded['url'], 'public_id' => $uploaded['public_id']];
            }
        }

        return $screenshots;
    }
}
