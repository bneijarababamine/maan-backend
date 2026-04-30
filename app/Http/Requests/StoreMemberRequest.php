<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'full_name'      => 'required|string|max:255',
            'gender'         => 'nullable|in:male,female',
            'phone'          => 'required|string|max:20',
            'whatsapp'       => 'nullable|string|max:20',
            'address'        => 'required|string|max:500',
            'profession'     => 'nullable|string|max:255',
            'join_date'      => 'required|date',
            'monthly_amount' => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Le nom complet est obligatoire.',
            'phone.required'     => 'Le téléphone est obligatoire.',
            'address.required'   => 'L\'adresse est obligatoire.',
            'join_date.required' => 'La date d\'adhésion est obligatoire.',
            'join_date.date'     => 'La date d\'adhésion doit être une date valide.',
        ];
    }
}
