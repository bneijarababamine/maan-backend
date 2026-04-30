<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'full_name', 'gender', 'phone', 'whatsapp', 'address',
        'profession', 'join_date', 'monthly_amount', 'is_active', 'notes',
    ];

    protected $casts = [
        'join_date'      => 'date',
        'is_active'      => 'boolean',
        'monthly_amount' => 'decimal:2',
    ];

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }

    public function donor()
    {
        return $this->hasOne(Donor::class);
    }

    public function getUnpaidMonthsAttribute(): array
    {
        // Use eager-loaded relationship when available to avoid N+1 queries
        if ($this->relationLoaded('contributions')) {
            $paidKeys = collect($this->contributions)->flatMap(
                fn ($c) => collect($c->months)->map(fn ($m) => "{$m->year}-{$m->month}")
            )->unique()->toArray();
        } else {
            $paidKeys = ContributionMonth::whereHas('contribution', fn ($q) =>
                $q->where('member_id', $this->id)
            )->get()->map(fn ($m) => "{$m->year}-{$m->month}")->toArray();
        }

        $unpaid  = [];
        $current = $this->join_date->copy()->startOfMonth();
        $now     = now()->startOfMonth();

        while ($current <= $now) {
            $key = $current->format('Y-n');
            if (!in_array($key, $paidKeys)) {
                $unpaid[] = ['year' => $current->year, 'month' => $current->month];
            }
            $current->addMonth();
        }

        return $unpaid;
    }
}
