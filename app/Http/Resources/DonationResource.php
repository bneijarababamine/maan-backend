<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'receipt_number'  => $this->receipt_number,
            'donor_id'        => $this->donor_id,
            'donor'           => $this->whenLoaded('donor', fn() => [
                'id'        => $this->donor->id,
                'full_name' => $this->donor->full_name,
                'phone'     => $this->donor->phone,
            ]),
            'amount'          => (float) $this->amount,
            'payment_method'  => $this->payment_method,
            'transaction_ref' => $this->transaction_ref,
            'screenshot_url'  => $this->screenshot_url,
            'screenshots'     => $this->screenshots
                ?? ($this->screenshot_url ? [['url' => $this->screenshot_url, 'public_id' => $this->screenshot_public_id]] : []),
            'registered_by'   => $this->whenLoaded('registeredBy', fn() => [
                'id'   => $this->registeredBy->id,
                'name' => $this->registeredBy->name,
            ]),
            'notes'           => $this->notes,
            'donated_at'      => $this->donated_at->toISOString(),
            'created_at'      => $this->created_at->toISOString(),
        ];
    }
}
