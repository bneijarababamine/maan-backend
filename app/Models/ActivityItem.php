<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityItem extends Model
{
    protected $fillable = ['activity_id', 'name', 'quantity', 'unit_value', 'payment_method'];

    protected $casts = [
        'quantity'   => 'decimal:2',
        'unit_value' => 'decimal:2',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
