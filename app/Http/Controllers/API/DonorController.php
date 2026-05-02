<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDonorRequest;
use App\Http\Resources\DonationResource;
use App\Http\Resources\DonorResource;
use App\Models\Bank;
use App\Models\Donor;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Donor::query();

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('full_name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('is_member')) {
            $query->where('is_member', filter_var($request->is_member, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $donors = $query->orderBy('full_name')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Liste des donateurs récupérée.',
            'data'    => DonorResource::collection($donors),
        ]);
    }

    public function store(StoreDonorRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data = array_merge($data, $this->resolveMemberStatus($data['phone'] ?? null));

        $donor = Donor::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Donateur créé avec succès.',
            'data'    => new DonorResource($donor),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $donor = Donor::findOrFail($id);

        return response()->json([
            'status'  => true,
            'message' => 'Donateur récupéré.',
            'data'    => new DonorResource($donor),
        ]);
    }

    public function update(StoreDonorRequest $request, int $id): JsonResponse
    {
        $donor = Donor::findOrFail($id);
        $data = array_merge($request->validated(), $this->resolveMemberStatus($request->validated()['phone'] ?? null));
        $donor->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'Donateur mis à jour.',
            'data'    => new DonorResource($donor),
        ]);
    }

    private function resolveMemberStatus(?string $phone): array
    {
        if (!$phone) return ['is_member' => false, 'member_id' => null];
        $member = Member::where('phone', $phone)->first();
        return $member
            ? ['is_member' => true,  'member_id' => $member->id]
            : ['is_member' => false, 'member_id' => null];
    }

    public function destroy(int $id): JsonResponse
    {
        $donor = Donor::findOrFail($id);

        $totals = $donor->donations()
            ->selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get();

        foreach ($totals as $row) {
            $check = Bank::canDeduct($row->payment_method, (float) $row->total);
            if (!$check['ok']) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Solde insuffisant.',
                    'error'     => 'insufficient_balance',
                    'bank_fr'   => $check['bank_fr'],
                    'bank_ar'   => $check['bank_ar'],
                    'available' => $check['available'],
                    'required'  => $check['required'],
                ], 422);
            }
        }

        $donor->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Donateur supprimé.',
            'data'    => null,
        ]);
    }

    public function donations(int $id): JsonResponse
    {
        $donor = Donor::findOrFail($id);
        $donations = $donor->donations()->with('donor')->orderByDesc('donated_at')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Dons du donateur récupérés.',
            'data'    => DonationResource::collection($donations),
        ]);
    }
}
