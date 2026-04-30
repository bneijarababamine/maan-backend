<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wilaya;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WilayaController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data'   => Wilaya::orderBy('name_fr')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name_fr' => 'required|string|max:100',
            'name_ar' => 'required|string|max:100',
        ]);

        $wilaya = Wilaya::create($request->only('name_fr', 'name_ar'));

        return response()->json(['status' => true, 'data' => $wilaya], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name_fr'   => 'sometimes|string|max:100',
            'name_ar'   => 'sometimes|string|max:100',
            'is_active' => 'sometimes|boolean',
        ]);

        $wilaya = Wilaya::findOrFail($id);
        $wilaya->update($request->only('name_fr', 'name_ar', 'is_active'));

        return response()->json(['status' => true, 'data' => $wilaya]);
    }

    public function destroy(int $id): JsonResponse
    {
        Wilaya::findOrFail($id)->delete();

        return response()->json(['status' => true, 'data' => null]);
    }
}
