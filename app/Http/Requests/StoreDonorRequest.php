<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonorRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'full_name'  => 'required|string|max:255',
            'gender'     => 'nullable|in:male,female',
            'phone'      => 'required|string|max:20',
            'whatsapp'   => 'nullable|string|max:20',
            'address'    => 'nullable|string|max:500',
            'profession' => 'nullable|string|max:255',
            'is_member'  => 'nullable|boolean',
            'member_id'  => 'nullable|exists:members,id',
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Le nom complet est obligatoire.',
            'phone.required'     => 'Le téléphone est obligatoire.',
            'member_id.exists'   => 'Ce membre n\'existe pas.',
        ];
    }
}
