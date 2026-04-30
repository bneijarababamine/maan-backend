<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'donor_id'        => 'required|exists:donors,id',
            'amount'          => 'required|numeric|min:1',
            'payment_method'  => 'required|string|max:50',
            'transaction_ref' => 'nullable|string|max:255',
            'screenshot'      => 'nullable|image|max:5120',
            'notes'           => 'nullable|string',
            'donated_at'      => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'donor_id.required'       => 'Le donateur est obligatoire.',
            'donor_id.exists'         => 'Ce donateur n\'existe pas.',
            'amount.required'         => 'Le montant est obligatoire.',
            'amount.min'              => 'Le montant doit être supérieur à 0.',
            'payment_method.required' => 'Le mode de paiement est obligatoire.',
            'payment_method.in'       => 'Mode de paiement invalide.',
        ];
    }
}
