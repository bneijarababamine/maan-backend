<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrphanRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $currentYear = now()->year;
        return [
            'full_name'      => 'required|string|max:255',
            'birth_year'     => "required|integer|min:1980|max:{$currentYear}",
            'gender'         => 'required|in:male,female',
            'school_name'    => 'nullable|string|max:255',
            'grade'          => 'nullable|string|max:100',
            'guardian_id'    => 'required|exists:guardians,id',
            'photo'          => 'nullable|image|max:5120',
            'notes'          => 'nullable|string',
            'is_active'      => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required'       => 'Le nom complet est obligatoire.',
            'birth_date.required'      => 'La date de naissance est obligatoire.',
            'birth_date.before'        => 'La date de naissance doit être dans le passé.',
            'gender.required'          => 'Le genre est obligatoire.',
            'gender.in'                => 'Le genre doit être male ou female.',
            'guardian_phone.required_without' => 'Le téléphone du tuteur est obligatoire si vous ne sélectionnez pas un tuteur existant.',
            'guardian_name.required_if' => 'Le nom du tuteur est obligatoire si le tuteur n\'existe pas.',
            'address.required'         => 'L\'adresse est obligatoire.',
        ];
    }
}

