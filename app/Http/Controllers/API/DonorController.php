<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDonorRequest;
use App\Http\Resources\DonationResource;
use App\Http\Resources\DonorResource;
use App\Models\Donor;
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
        $donor = Donor::create($request->validated());

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
        $donor->update($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Donateur mis à jour.',
            'data'    => new DonorResource($donor),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        Donor::findOrFail($id)->delete();

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
