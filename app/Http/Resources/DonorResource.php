<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'full_name'        => $this->full_name,
            'gender'           => $this->gender,
            'phone'            => $this->phone,
            'whatsapp'         => $this->whatsapp,
            'address'          => $this->address,
            'profession'       => $this->profession,
            'is_member'        => $this->is_member,
            'member_id'        => $this->member_id,
            'total_donations'  => $this->total_donations,
            'created_at'       => $this->created_at->toISOString(),
            'updated_at'       => $this->updated_at->toISOString(),
        ];
    }
}
