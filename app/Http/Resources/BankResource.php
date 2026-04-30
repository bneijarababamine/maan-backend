<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name_fr'   => $this->name_fr,
            'name_ar'   => $this->name_ar,
            'logo'      => $this->logo,
            'balance'   => (float) $this->balance,
            'is_active' => $this->is_active,
            'created_at'=> $this->created_at->toISOString(),
            'updated_at'=> $this->updated_at->toISOString(),
        ];
    }
}
