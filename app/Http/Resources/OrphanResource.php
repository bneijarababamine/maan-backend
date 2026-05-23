<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrphanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'full_name'           => $this->full_name,
            'display_name'        => $this->display_name,
            'birth_date'          => $this->birth_date->format('Y-m-d'),
            'birth_year'          => $this->birth_year,
            'age'                 => $this->age,
            'gender'              => $this->gender,
            'school_name'         => $this->school_name,
            'grade'               => $this->grade,
            'guardian_id'         => $this->guardian_id,
            'guardian'            => $this->whenLoaded('guardian', fn() => $this->guardian ? [
                'id'          => $this->guardian->id,
                'name'        => $this->guardian->name,
                'father_name' => $this->guardian->father_name,
                'phone'       => $this->guardian->phone,
                'whatsapp'    => $this->guardian->whatsapp,
                'address'     => $this->guardian->address,
            ] : null),
            'guardian_name'       => $this->guardian_name,
            'guardian_phone'      => $this->guardian_phone,
            'address'             => $this->address,
            'photo_url'           => $this->photo_url,
            'is_active'           => $this->is_active,
            'is_adult'            => $this->is_adult,
            'months_until_18'     => $this->months_until_18,
            'deactivated_reason'  => $this->deactivated_reason,
            'deactivated_at'      => $this->deactivated_at?->toISOString(),
            'notes'               => $this->notes,
            'siblings'            => $this->whenLoaded('siblings', fn() =>
                $this->siblings->map(fn($s) => [
                    'id'           => $s->id,
                    'full_name'    => $s->full_name,
                    'display_name' => $s->display_name,
                    'birth_year'   => $s->birth_year,
                    'gender'       => $s->gender,
                    'photo_url'    => $s->photo_url,
                    'is_active'    => $s->is_active,
                ])
            ),
            'created_at'          => $this->created_at->toISOString(),
            'updated_at'          => $this->updated_at->toISOString(),
        ];
    }
}
