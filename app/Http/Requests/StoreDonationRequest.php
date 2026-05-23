<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'donor_id'         => 'nullable|exists:donors,id|required_without:member_id',
            'member_id'        => 'nullable|exists:members,id|required_without:donor_id',
            'donation_type_id' => 'nullable|exists:donation_types,id',
            'year'             => 'nullable|integer|min:2000|max:2100',
            'amount'           => 'required|numeric|min:1',
            'payment_method'   => 'required|string|max:50',
            'transaction_ref'  => 'nullable|string|max:255',
            'screenshot'       => 'nullable|image|max:5120',
            'notes'            => 'nullable|string',
            'donated_at'       => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'donor_id.required_without'  => 'Le donateur ou l\'adhérent est obligatoire.',
            'member_id.required_without' => 'Le donateur ou l\'adhérent est obligatoire.',
            'donor_id.exists'            => 'Ce donateur n\'existe pas.',
            'member_id.exists'           => 'Cet adhérent n\'existe pas.',
            'amount.required'            => 'Le montant est obligatoire.',
            'amount.min'                 => 'Le montant doit être supérieur à 0.',
            'payment_method.required'    => 'Le mode de paiement est obligatoire.',
        ];
    }
}
