<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BankResource;
use App\Models\Bank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index(): JsonResponse
    {
        $banks = Bank::orderBy('name_fr')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Liste des banques récupérée.',
            'data'    => BankResource::collection($banks),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name_fr'   => 'required|string|max:100',
            'name_ar'   => 'required|string|max:100',
            'logo'      => 'nullable|string|max:100',
            'balance'   => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $bank = Bank::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Banque créée avec succès.',
            'data'    => new BankResource($bank),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $bank = Bank::findOrFail($id);

        $data = $request->validate([
            'name_fr'   => 'sometimes|required|string|max:100',
            'name_ar'   => 'sometimes|required|string|max:100',
            'logo'      => 'nullable|string|max:100',
            'balance'   => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $bank->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'Banque mise à jour.',
            'data'    => new BankResource($bank),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        Bank::findOrFail($id)->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Banque supprimée.',
            'data'    => null,
        ]);
    }
}
