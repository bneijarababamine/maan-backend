<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orphan extends Model
{
    protected $fillable = [
        'full_name', 'birth_date', 'gender', 'school_name', 'grade',
        'guardian_id', 'address',
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
        return (int) $this->birth_date->copy()->endOfYear()->diffInYears(now());
    }

    public function getBirthYearAttribute(): int
    {
        return (int) $this->birth_date->format('Y');
    }

    public function getIsAdultAttribute(): bool
    {
        return $this->age >= 18;
    }

    public function getMonthsUntil18Attribute(): ?int
    {
        if ($this->is_adult) return null;
        $turns18 = $this->birth_date->copy()->endOfYear()->addYears(18);
        return (int) now()->diffInMonths($turns18);
    }

    public function getDisplayNameAttribute(): string
    {
        $connector  = $this->gender === 'male' ? 'ould' : 'mint';
        $fatherName = $this->guardian?->father_name ?? '';
        return trim($this->full_name . ($fatherName ? " {$connector} {$fatherName}" : ''));
    }

    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }

    public function getGuardianNameAttribute(): ?string
    {
        return $this->guardian?->name;
    }

    public function getGuardianPhoneAttribute(): ?string
    {
        return $this->guardian?->phone;
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
