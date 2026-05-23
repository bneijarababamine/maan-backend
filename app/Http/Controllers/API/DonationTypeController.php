<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DonationType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonationTypeController extends Controller
{
    public function index(): JsonResponse
    {
        $types = DonationType::orderBy('name_fr')->get();
        return response()->json(['status' => true, 'data' => $types]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name_fr'   => 'required|string|max:255',
            'name_ar'   => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $type = DonationType::create($validated);
        return response()->json(['status' => true, 'data' => $type], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $type = DonationType::findOrFail($id);
        $validated = $request->validate([
            'name_fr'   => 'required|string|max:255',
            'name_ar'   => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $type->update($validated);
        return response()->json(['status' => true, 'data' => $type]);
    }

    public function destroy(int $id): JsonResponse
    {
        DonationType::findOrFail($id)->delete();
        return response()->json(['status' => true, 'data' => null]);
    }
}
