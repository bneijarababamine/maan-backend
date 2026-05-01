<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'full_name'      => 'required|string|max:255',
            'gender'         => 'nullable|in:male,female',
            'phone'          => ['required', 'string', 'max:20', Rule::unique('members', 'phone')->ignore($this->route('member'))],
            'whatsapp'       => 'nullable|string|max:20',
            'address'        => 'nullable|string|max:500',
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
            'phone.unique'       => 'Ce numéro de téléphone est déjà utilisé.',

            'join_date.required' => 'La date d\'adhésion est obligatoire.',
            'join_date.date'     => 'La date d\'adhésion doit être une date valide.',
        ];
    }
}
