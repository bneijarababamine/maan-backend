<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDonationRequest;
use App\Http\Resources\DonationResource;
use App\Models\Bank;
use App\Models\Donation;
use App\Services\CloudinaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Donation::with(['donor', 'registeredBy']);

        if ($request->has('donor_id')) {
            $query->where('donor_id', $request->donor_id);
        }

        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $donations = $query->orderByDesc('donated_at')->paginate(20);

        return response()->json([
            'status'  => true,
            'message' => 'Liste des dons récupérée.',
            'data'    => DonationResource::collection($donations)->response()->getData(true),
        ]);
    }

    public function store(StoreDonationRequest $request): JsonResponse
    {
        $screenshots = $this->uploadScreenshots($request);

        $nextReceipt = (Donation::max('receipt_number') ?? 0) + 1;

        $donation = Donation::create([
            'receipt_number'       => $nextReceipt,
            'donor_id'             => $request->donor_id,
            'amount'               => $request->amount,
            'payment_method'       => $request->payment_method,
            'transaction_ref'      => $request->transaction_ref,
            'screenshot_url'       => $screenshots[0]['url'] ?? null,
            'screenshot_public_id' => $screenshots[0]['public_id'] ?? null,
            'screenshots'          => $screenshots ?: null,
            'registered_by'        => auth()->id(),
            'notes'                => $request->notes,
            'donated_at'           => $request->donated_at ?? now(),
        ]);

        Bank::adjustByMethod($request->payment_method, (float) $request->amount);

        return response()->json([
            'status'  => true,
            'message' => 'Don enregistré avec succès.',
            'data'    => new DonationResource($donation->load(['donor', 'registeredBy'])),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $donation = Donation::with(['donor', 'registeredBy'])->findOrFail($id);

        return response()->json([
            'status'  => true,
            'message' => 'Don récupéré.',
            'data'    => new DonationResource($donation),
        ]);
    }

    public function update(StoreDonationRequest $request, int $id): JsonResponse
    {
        $donation = Donation::findOrFail($id);

        // Handle screenshot updates
        $keepPublicIds = $request->input('keep_public_ids', []);
        $current = $donation->screenshots
            ?? ($donation->screenshot_url
                ? [['url' => $donation->screenshot_url, 'public_id' => $donation->screenshot_public_id]]
                : []);

        foreach ($current as $s) {
            if (!empty($s['public_id']) && !in_array($s['public_id'], $keepPublicIds)) {
                app(CloudinaryService::class)->delete($s['public_id']);
            }
        }

        $kept = array_values(array_filter($current, fn($s) => in_array($s['public_id'] ?? '', $keepPublicIds)));
        $newUploads = $this->uploadScreenshots($request);
        $screenshots = array_merge($kept, $newUploads);

        // Reverse old, apply new
        Bank::adjustByMethod($donation->payment_method, -(float) $donation->amount);
        Bank::adjustByMethod($request->payment_method, (float) $request->amount);

        $donation->update([
            'amount'               => $request->amount,
            'payment_method'       => $request->payment_method,
            'transaction_ref'      => $request->transaction_ref,
            'notes'                => $request->notes,
            'donated_at'           => $request->donated_at ?? $donation->donated_at,
            'screenshots'          => $screenshots ?: null,
            'screenshot_url'       => $screenshots[0]['url'] ?? null,
            'screenshot_public_id' => $screenshots[0]['public_id'] ?? null,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Don mis à jour.',
            'data'    => new DonationResource($donation->load(['donor', 'registeredBy'])),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $donation = Donation::findOrFail($id);

        Bank::adjustByMethod($donation->payment_method, -(float) $donation->amount);

        foreach ($donation->screenshots ?? [] as $s) {
            if (!empty($s['public_id'])) {
                app(CloudinaryService::class)->delete($s['public_id']);
            }
        }
        if (!$donation->screenshots && $donation->screenshot_public_id) {
            app(CloudinaryService::class)->delete($donation->screenshot_public_id);
        }

        $donation->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Don supprimé.',
            'data'    => null,
        ]);
    }

    private function uploadScreenshots(Request $request): array
    {
        $screenshots = [];
        if (($request->payment_method ?? '') === 'cash') return $screenshots;

        if ($request->hasFile('screenshots')) {
            foreach ($request->file('screenshots') as $file) {
                $uploaded = app(CloudinaryService::class)->upload($file, 'charity/screenshots');
                $screenshots[] = ['url' => $uploaded['url'], 'public_id' => $uploaded['public_id']];
            }
        }

        return $screenshots;
    }
}
