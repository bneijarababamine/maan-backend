<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientMedication extends Model
{
    protected $fillable = [
        'patient_id', 'name', 'image_url', 'image_public_id',
        'price', 'quantity', 'payment_method',
        'start_date', 'duration_value', 'duration_unit', 'notes',
    ];

    protected $casts = [
        'start_date'     => 'date',
        'price'          => 'decimal:2',
        'quantity'       => 'decimal:2',
        'duration_value' => 'integer',
    ];

    public function getEndDateAttribute(): \Carbon\Carbon
    {
        $start = $this->start_date ?? now();
        return match ($this->duration_unit) {
            'weeks'  => $start->copy()->addWeeks($this->duration_value ?? 1),
            'months' => $start->copy()->addMonths($this->duration_value ?? 1),
            default  => $start->copy()->addDays($this->duration_value ?? 1),
        };
    }

    public function getDaysRemainingAttribute(): int
    {
        return (int) now()->startOfDay()->diffInDays($this->end_date->startOfDay(), false);
    }

    public function patient()
    {
        return $this->belongsTo(ChronicPatient::class, 'patient_id');
    }
}
