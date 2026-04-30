<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContributionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'receipt_number'   => $this->receipt_number,
            'member_id'        => $this->member_id,
            'member'           => $this->whenLoaded('member', fn() => [
                'id'        => $this->member->id,
                'full_name' => $this->member->full_name,
                'phone'     => $this->member->phone,
            ]),
            'months_count'     => $this->months_count,
            'amount_per_month' => (float) $this->amount_per_month,
            'total_amount'     => (float) $this->total_amount,
            'payment_method'   => $this->payment_method,
            'transaction_ref'  => $this->transaction_ref,
            'screenshot_url'   => $this->screenshot_url,
            'screenshots'      => $this->screenshots
                ?? ($this->screenshot_url ? [['url' => $this->screenshot_url, 'public_id' => $this->screenshot_public_id]] : []),
            'registered_by'    => $this->whenLoaded('registeredBy', fn() => [
                'id'   => $this->registeredBy->id,
                'name' => $this->registeredBy->name,
            ]),
            'months'           => $this->whenLoaded('months', fn() =>
                $this->months->map(fn($m) => ['year' => $m->year, 'month' => $m->month])
            ),
            'notes'            => $this->notes,
            'paid_at'          => $this->paid_at->toISOString(),
            'created_at'       => $this->created_at->toISOString(),
        ];
    }
}
