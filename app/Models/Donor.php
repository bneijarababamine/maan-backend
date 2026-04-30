<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    protected $fillable = [
        'full_name', 'gender', 'phone', 'whatsapp', 'address',
        'profession', 'is_member', 'member_id',
    ];

    protected $casts = ['is_member' => 'boolean'];

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function getTotalDonationsAttribute(): float
    {
        return (float) $this->donations()->sum('amount');
    }
}
