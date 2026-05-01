<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BankTransfer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankTransferController extends Controller
{
    public function index(): JsonResponse
    {
        $transfers = BankTransfer::with(['fromBank', 'toBank', 'creator'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $transfers->map(fn($t) => [
                'id'            => $t->id,
                'from_bank'     => ['id' => $t->fromBank->id, 'name_fr' => $t->fromBank->name_fr, 'name_ar' => $t->fromBank->name_ar, 'logo' => $t->fromBank->logo],
                'to_bank'       => ['id' => $t->toBank->id,   'name_fr' => $t->toBank->name_fr,   'name_ar' => $t->toBank->name_ar,   'logo' => $t->toBank->logo],
                'amount'        => (float) $t->amount,
                'notes'         => $t->notes,
                'created_by'    => $t->creator?->name,
                'created_at'    => $t->created_at->toISOString(),
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'from_bank_id' => 'required|exists:banks,id',
            'to_bank_id'   => 'required|exists:banks,id|different:from_bank_id',
            'amount'       => 'required|numeric|min:0.01',
            'notes'        => 'nullable|string|max:500',
        ]);

        $from = Bank::findOrFail($request->from_bank_id);

        if ((float) $from->balance < (float) $request->amount) {
            return response()->json([
                'status'  => false,
                'message' => 'Solde insuffisant dans le compte source.',
                'data'    => null,
            ], 422);
        }

        $transfer = BankTransfer::create([
            'from_bank_id' => $request->from_bank_id,
            'to_bank_id'   => $request->to_bank_id,
            'amount'       => $request->amount,
            'notes'        => $request->notes,
            'created_by'   => auth()->id(),
        ]);

        $from->decrement('balance', $request->amount);
        Bank::findOrFail($request->to_bank_id)->increment('balance', $request->amount);

        $transfer->load(['fromBank', 'toBank']);

        return response()->json([
            'status'  => true,
            'message' => 'Transfert effectué avec succès.',
            'data'    => [
                'id'         => $transfer->id,
                'from_bank'  => ['id' => $transfer->fromBank->id, 'name_fr' => $transfer->fromBank->name_fr, 'name_ar' => $transfer->fromBank->name_ar, 'logo' => $transfer->fromBank->logo],
                'to_bank'    => ['id' => $transfer->toBank->id,   'name_fr' => $transfer->toBank->name_fr,   'name_ar' => $transfer->toBank->name_ar,   'logo' => $transfer->toBank->logo],
                'amount'     => (float) $transfer->amount,
                'notes'      => $transfer->notes,
                'created_at' => $transfer->created_at->toISOString(),
            ],
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $transfer = BankTransfer::findOrFail($id);

        $request->validate([
            'from_bank_id' => 'required|exists:banks,id',
            'to_bank_id'   => 'required|exists:banks,id|different:from_bank_id',
            'amount'       => 'required|numeric|min:0.01',
            'notes'        => 'nullable|string|max:500',
        ]);

        // Reverse old transfer
        Bank::findOrFail($transfer->from_bank_id)->increment('balance', $transfer->amount);
        Bank::findOrFail($transfer->to_bank_id)->decrement('balance', $transfer->amount);

        // Check new source balance
        $newFrom = Bank::findOrFail($request->from_bank_id);
        if ((float) $newFrom->balance < (float) $request->amount) {
            // Re-apply original to keep balances consistent
            Bank::findOrFail($transfer->from_bank_id)->decrement('balance', $transfer->amount);
            Bank::findOrFail($transfer->to_bank_id)->increment('balance', $transfer->amount);

            return response()->json([
                'status'  => false,
                'message' => 'Solde insuffisant dans le compte source.',
                'data'    => null,
            ], 422);
        }

        // Apply new transfer
        $newFrom->decrement('balance', $request->amount);
        Bank::findOrFail($request->to_bank_id)->increment('balance', $request->amount);

        $transfer->update([
            'from_bank_id' => $request->from_bank_id,
            'to_bank_id'   => $request->to_bank_id,
            'amount'       => $request->amount,
            'notes'        => $request->notes,
        ]);

        $transfer->load(['fromBank', 'toBank']);

        return response()->json([
            'status'  => true,
            'message' => 'Transfert modifié avec succès.',
            'data'    => [
                'id'         => $transfer->id,
                'from_bank'  => ['id' => $transfer->fromBank->id, 'name_fr' => $transfer->fromBank->name_fr, 'name_ar' => $transfer->fromBank->name_ar, 'logo' => $transfer->fromBank->logo],
                'to_bank'    => ['id' => $transfer->toBank->id,   'name_fr' => $transfer->toBank->name_fr,   'name_ar' => $transfer->toBank->name_ar,   'logo' => $transfer->toBank->logo],
                'amount'     => (float) $transfer->amount,
                'notes'      => $transfer->notes,
                'created_at' => $transfer->created_at->toISOString(),
            ],
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $transfer = BankTransfer::findOrFail($id);

        // Reverse balances
        Bank::findOrFail($transfer->from_bank_id)->increment('balance', $transfer->amount);
        Bank::findOrFail($transfer->to_bank_id)->decrement('balance', $transfer->amount);

        $transfer->delete();

        return response()->json(['status' => true, 'message' => 'Transfert supprimé.']);
    }
}
