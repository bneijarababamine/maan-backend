<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'full_name'      => 'sometimes|required|string|max:255',
            'phone'          => 'sometimes|required|string|max:20',
            'whatsapp'       => 'nullable|string|max:20',
            'address'        => 'sometimes|required|string|max:500',
            'profession'     => 'nullable|string|max:255',
            'join_date'      => 'sometimes|required|date',
            'monthly_amount' => 'nullable|numeric|min:0',
            'is_active'      => 'sometimes|boolean',
            'notes'          => 'nullable|string',
        ];
    }
}
