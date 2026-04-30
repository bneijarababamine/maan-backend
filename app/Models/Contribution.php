<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contribution extends Model
{
    protected $fillable = [
        'member_id', 'months_count', 'amount_per_month', 'total_amount',
        'payment_method', 'transaction_ref', 'screenshot_url',
        'screenshot_public_id', 'screenshots', 'registered_by', 'notes', 'paid_at',
    ];

    protected $casts = [
        'paid_at'          => 'datetime',
        'amount_per_month' => 'decimal:2',
        'total_amount'     => 'decimal:2',
        'screenshots'      => 'array',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function months()
    {
        return $this->hasMany(ContributionMonth::class);
    }

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }
}
