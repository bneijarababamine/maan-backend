<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\GuardianResource;
use App\Http\Resources\OrphanResource;
use App\Models\Guardian;
use App\Models\Orphan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuardianController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Guardian::query();

        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        $guardians = $query->withCount('orphans')->orderBy('name')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Liste des tuteurs récupérée.',
            'data'    => GuardianResource::collection($guardians),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $guardian = Guardian::with('orphans')->findOrFail($id);

        return response()->json([
            'status'  => true,
            'message' => 'Tuteur récupéré.',
            'data'    => new GuardianResource($guardian),
        ]);
    }

    public function checkByPhone(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $guardian = Guardian::where('phone', $request->phone)->first();

        if ($guardian) {
            return response()->json([
                'status'  => true,
                'message' => 'Tuteur trouvé.',
                'exists'  => true,
                'data'    => new GuardianResource($guardian),
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Tuteur non trouvé.',
            'exists'  => false,
            'data'    => null,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'phone'       => 'required|string|unique:guardians,phone',
            'whatsapp'    => 'nullable|string|max:20',
            'address'     => 'nullable|string',
            'notes'       => 'nullable|string',
        ]);

        $guardian = Guardian::create($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Tuteur créé avec succès.',
            'data'    => new GuardianResource($guardian),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $guardian = Guardian::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'phone'       => "required|string|unique:guardians,phone,{$id}",
            'whatsapp'    => 'nullable|string|max:20',
            'address'     => 'nullable|string',
            'notes'       => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);

        $guardian->update($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Tuteur mis à jour.',
            'data'    => new GuardianResource($guardian),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $guardian = Guardian::findOrFail($id);
        $guardian->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Tuteur supprimé.',
            'data'    => null,
        ]);
    }

    public function orphans(int $id): JsonResponse
    {
        $guardian = Guardian::findOrFail($id);
        $orphans = $guardian->orphans()->with('guardian')->orderBy('full_name')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Orphelins du tuteur récupérés.',
            'data'    => OrphanResource::collection($orphans),
        ]);
    }
}
