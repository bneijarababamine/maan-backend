<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuardianResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'father_name'   => $this->father_name,
            'phone'         => $this->phone,
            'whatsapp'      => $this->whatsapp,
            'address'       => $this->address,
            'notes'         => $this->notes,
            'is_active'     => $this->is_active,
            'orphans_count' => $this->whenCounted('orphans', fn() => $this->orphans_count),
            'orphans'       => $this->whenLoaded('orphans', fn() =>
                $this->orphans->map(fn($o) => [
                    'id'           => $o->id,
                    'full_name'    => $o->full_name,
                    'display_name' => $o->display_name,
                    'birth_year'   => $o->birth_year,
                    'age'          => $o->age,
                    'gender'       => $o->gender,
                    'school_name'  => $o->school_name,
                    'grade'        => $o->grade,
                    'address'      => $o->address,
                    'photo_url'    => $o->photo_url,
                    'is_active'    => $o->is_active,
                ])
            ),
            'created_at'    => $this->created_at->toISOString(),
            'updated_at'    => $this->updated_at->toISOString(),
        ];
    }
}
