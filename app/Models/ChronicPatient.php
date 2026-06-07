<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChronicPatient extends Model
{
    protected $fillable = [
        'full_name', 'gender', 'birth_date', 'phone', 'whatsapp',
        'disease_name', 'notes', 'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active'  => 'boolean',
    ];

    public function medications()
    {
        return $this->hasMany(PatientMedication::class, 'patient_id');
    }

    public function getTotalSpentAttribute(): float
    {
        return (float) $this->medications()->selectRaw('SUM(price * quantity) as total')->value('total') ?? 0;
    }
}
