<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityBeneficiary extends Model
{
    protected $fillable = [
        'activity_id', 'beneficiary_type', 'beneficiary_id',
        'value_received', 'notes',
    ];

    protected $casts = ['value_received' => 'decimal:2'];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function orphanEntity()
    {
        return $this->belongsTo(Orphan::class, 'beneficiary_id');
    }

    public function familyEntity()
    {
        return $this->belongsTo(Family::class, 'beneficiary_id');
    }
}
