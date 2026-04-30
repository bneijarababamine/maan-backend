<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FamilyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'name'                 => $this->name,
            'representative_name'  => $this->head_of_family,
            'head_of_family'       => $this->head_of_family,
            'phone'                => $this->phone,
            'address'        => $this->address,
            'members_count'  => $this->members_count,
            'is_active'      => $this->is_active,
            'notes'          => $this->notes,
            'created_at'     => $this->created_at->toISOString(),
            'updated_at'     => $this->updated_at->toISOString(),
        ];
    }
}
