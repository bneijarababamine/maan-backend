<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    protected $fillable = [
        'name', 'head_of_family', 'phone', 'address',
        'members_count', 'is_active', 'notes',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function activities()
    {
        return $this->hasMany(ActivityBeneficiary::class, 'beneficiary_id')
            ->where('beneficiary_type', 'family');
    }
}
