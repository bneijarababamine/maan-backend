<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrphanRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'full_name'      => 'required|string|max:255',
            'birth_date'     => 'required|date|before:today',
            'gender'         => 'required|in:male,female',
            'school_name'    => 'nullable|string|max:255',
            'grade'          => 'nullable|string|max:100',
            'guardian_name'  => 'required|string|max:255',
            'guardian_phone' => ['required', 'string', 'max:20', Rule::unique('orphans', 'guardian_phone')->ignore($this->route('orphan'))],
            'address'        => 'required|string|max:500',
            'photo'          => 'nullable|image|max:5120',
            'notes'          => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required'      => 'Le nom complet est obligatoire.',
            'birth_date.required'     => 'La date de naissance est obligatoire.',
            'birth_date.before'       => 'La date de naissance doit être dans le passé.',
            'gender.required'         => 'Le genre est obligatoire.',
            'gender.in'               => 'Le genre doit être male ou female.',
            'guardian_name.required'  => 'Le nom du tuteur est obligatoire.',
            'guardian_phone.required' => 'Le téléphone du tuteur est obligatoire.',
            'guardian_phone.unique'   => 'Ce numéro de téléphone est déjà utilisé.',
            'address.required'        => 'L\'adresse est obligatoire.',
        ];
    }
}
