<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'donor_id', 'amount', 'payment_method', 'transaction_ref',
        'screenshot_url', 'screenshot_public_id', 'screenshots',
        'registered_by', 'notes', 'donated_at',
    ];

    protected $casts = [
        'donated_at'  => 'datetime',
        'amount'      => 'decimal:2',
        'screenshots' => 'array',
    ];

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }
}
