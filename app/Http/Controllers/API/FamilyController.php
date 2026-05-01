<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFamilyRequest;
use App\Http\Resources\FamilyResource;
use App\Models\Family;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FamilyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Family::query();

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('head_of_family', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        $families = $query->orderBy('head_of_family')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Liste des familles récupérée.',
            'data'    => FamilyResource::collection($families),
        ]);
    }

    public function store(StoreFamilyRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (isset($data['representative_name'])) {
            $data['head_of_family'] = $data['representative_name'];
            unset($data['representative_name']);
        }
        if (!isset($data['name']) && isset($data['head_of_family'])) {
            $data['name'] = $data['head_of_family'];
        }

        $family = Family::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Famille créée avec succès.',
            'data'    => new FamilyResource($family),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $family = Family::findOrFail($id);

        return response()->json([
            'status'  => true,
            'message' => 'Famille récupérée.',
            'data'    => new FamilyResource($family),
        ]);
    }

    public function update(StoreFamilyRequest $request, int $id): JsonResponse
    {
        $family = Family::findOrFail($id);
        $data = $request->validated();
        if (isset($data['representative_name'])) {
            $data['head_of_family'] = $data['representative_name'];
            unset($data['representative_name']);
        }
        if (!isset($data['name']) && isset($data['head_of_family'])) {
            $data['name'] = $data['head_of_family'];
        }
        $family->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'Famille mise à jour.',
            'data'    => new FamilyResource($family),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        Family::findOrFail($id)->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Famille supprimée.',
            'data'    => null,
        ]);
    }
}
