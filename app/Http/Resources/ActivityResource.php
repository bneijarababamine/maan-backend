<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'title_ar'         => $this->title_ar,
            'title_fr'         => $this->title_fr,
            'description_ar'   => $this->description_ar,
            'description_fr'   => $this->description_fr,
            'activity_type'    => $this->activity_type,
            'beneficiary_type' => $this->beneficiary_type,
            'payment_type'     => $this->payment_type ?? 'financial',
            'payment_method'   => $this->payment_method,
            'activity_date'    => $this->activity_date->format('Y-m-d'),
            'total_cost'       => (float) $this->total_cost,
            'created_by'       => $this->whenLoaded('creator', fn() => [
                'id'   => $this->creator->id,
                'name' => $this->creator->name,
            ]),
            'beneficiaries'    => $this->whenLoaded('beneficiaries', fn() =>
                $this->beneficiaries->map(fn($b) => [
                    'id'                   => $b->id,
                    'beneficiary_type'     => $b->beneficiary_type,
                    'beneficiary_id'       => $b->beneficiary_id,
                    'beneficiary_name'     => $b->beneficiary_type === 'orphan'
                        ? ($b->orphanEntity?->display_name ?? $b->orphanEntity?->full_name ?? null)
                        : ($b->familyEntity?->name ?? $b->familyEntity?->head_of_family ?? null),
                    'guardian_id'          => $b->beneficiary_type === 'orphan' ? ($b->orphanEntity?->guardian_id ?? null) : null,
                    'guardian_name'        => $b->beneficiary_type === 'orphan' ? ($b->orphanEntity?->guardian?->name ?? null) : null,
                    'guardian_father_name' => $b->beneficiary_type === 'orphan' ? ($b->orphanEntity?->guardian?->father_name ?? null) : null,
                    'value_received'       => (float) $b->value_received,
                    'notes'                => $b->notes,
                    'payment_method'       => $b->payment_method,
                    'screenshot_url'       => $b->screenshot_url,
                ])
            ),
            'items'            => $this->whenLoaded('items', fn() =>
                $this->items->map(fn($i) => [
                    'id'             => $i->id,
                    'name'           => $i->name,
                    'quantity'       => (float) $i->quantity,
                    'unit_value'     => (float) $i->unit_value,
                    'total'          => (float) $i->quantity * (float) $i->unit_value,
                    'payment_method' => $i->payment_method,
                ])
            ),
            'photos'           => $this->whenLoaded('photos', fn() =>
                $this->photos->map(fn($p) => [
                    'id'           => $p->id,
                    'photo_url'    => $p->photo_url,
                    'caption_ar'   => $p->caption_ar,
                    'caption_fr'   => $p->caption_fr,
                ])
            ),
            'created_at'       => $this->created_at->toISOString(),
            'updated_at'       => $this->updated_at->toISOString(),
        ];
    }
}
