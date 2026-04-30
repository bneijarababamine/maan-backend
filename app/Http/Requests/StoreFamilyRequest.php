<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFamilyRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'                => 'nullable|string|max:255',
            'representative_name' => 'required|string|max:255',
            'phone'               => 'required|string|max:20',
            'address'             => 'nullable|string|max:500',
            'members_count'       => 'nullable|integer|min:1',
            'is_active'           => 'nullable|boolean',
            'notes'               => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'representative_name.required' => 'Le nom du représentant est obligatoire.',
            'phone.required'               => 'Le téléphone est obligatoire.',
        ];
    }
}
