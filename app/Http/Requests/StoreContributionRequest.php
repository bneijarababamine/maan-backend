<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContributionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'member_id'       => 'required|exists:members,id',
            'months'          => 'required|array|min:1',
            'months.*.year'   => 'required|integer|min:2000|max:2100',
            'months.*.month'  => 'required|integer|min:1|max:12',
            'payment_method'  => 'required|string|max:50',
            'transaction_ref' => 'nullable|string|max:255',
            'screenshot'      => 'nullable|image|max:5120',
            'notes'           => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'member_id.required'      => 'Le membre est obligatoire.',
            'member_id.exists'        => 'Ce membre n\'existe pas.',
            'months.required'         => 'Veuillez sélectionner au moins un mois.',
            'months.*.year.required'  => 'L\'année est obligatoire pour chaque mois.',
            'months.*.month.required' => 'Le mois est obligatoire.',
            'payment_method.required' => 'Le mode de paiement est obligatoire.',
            'payment_method.in'       => 'Mode de paiement invalide.',
            'screenshot.image'        => 'Le fichier doit être une image.',
            'screenshot.max'          => 'L\'image ne doit pas dépasser 5 Mo.',
        ];
    }
}
