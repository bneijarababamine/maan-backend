<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title_ar'         => 'required|string|max:255',
            'title_fr'         => 'required|string|max:255',
            'description_ar'   => 'nullable|string',
            'description_fr'   => 'nullable|string',
            'activity_type'    => 'required|in:school_fees,eid_help,food_basket,winter_clothes,ramadan,other',
            'beneficiary_type' => 'required|in:orphans,families,general',
            'activity_date'    => 'required|date',
            'payment_type'     => 'nullable|in:financial,in_kind',
            'payment_method'   => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'title_ar.required'         => 'Le titre en arabe est obligatoire.',
            'title_fr.required'         => 'Le titre en français est obligatoire.',
            'activity_type.required'    => 'Le type d\'activité est obligatoire.',
            'activity_type.in'          => 'Type d\'activité invalide.',
            'beneficiary_type.required' => 'Le type de bénéficiaire est obligatoire.',
            'beneficiary_type.in'       => 'Type de bénéficiaire invalide.',
            'activity_date.required'    => 'La date de l\'activité est obligatoire.',
        ];
    }
}
