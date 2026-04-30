<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orphan extends Model
{
    protected $fillable = [
        'full_name', 'birth_date', 'gender', 'school_name', 'grade',
        'guardian_name', 'guardian_phone', 'address',
        'photo_url', 'photo_public_id', 'is_active',
        'deactivated_reason', 'deactivated_at', 'notes',
    ];

    protected $casts = [
        'birth_date'      => 'date',
        'is_active'       => 'boolean',
        'deactivated_at'  => 'datetime',
    ];

    public function getAgeAttribute(): int
    {
        return $this->birth_date->age;
    }

    public function getIsAdultAttribute(): bool
    {
        return $this->birth_date->age >= 18;
    }

    public function getMonthsUntil18Attribute(): ?int
    {
        if ($this->is_adult) return null;
        return (int) now()->diffInMonths($this->birth_date->copy()->addYears(18));
    }

    public function siblings()
    {
        return $this->belongsToMany(
            Orphan::class, 'orphan_siblings', 'orphan_id', 'sibling_id'
        );
    }

    public function activities()
    {
        return $this->hasMany(ActivityBeneficiary::class, 'beneficiary_id')
            ->where('beneficiary_type', 'orphan');
    }
}
