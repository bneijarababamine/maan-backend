<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'full_name'      => $this->full_name,
            'gender'         => $this->gender,
            'phone'          => $this->phone,
            'whatsapp'       => $this->whatsapp,
            'address'        => $this->address,
            'profession'     => $this->profession,
            'join_date'      => $this->join_date->format('Y-m-d'),
            'monthly_amount' => (float) $this->monthly_amount,
            'is_active'      => $this->is_active,
            'notes'          => $this->notes,
            'unpaid_months'  => $this->unpaid_months,
            'created_at'     => $this->created_at->toISOString(),
            'updated_at'     => $this->updated_at->toISOString(),
        ];
    }
}
