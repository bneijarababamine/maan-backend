<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'title_ar', 'title_fr', 'description_ar', 'description_fr',
        'activity_type', 'beneficiary_type', 'activity_date',
        'payment_type', 'payment_method', 'total_cost', 'created_by',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'total_cost'    => 'decimal:2',
    ];

    public function beneficiaries()
    {
        return $this->hasMany(ActivityBeneficiary::class);
    }

    public function items()
    {
        return $this->hasMany(ActivityItem::class);
    }

    public function photos()
    {
        return $this->hasMany(ActivityPhoto::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
