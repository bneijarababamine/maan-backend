<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientMedication extends Model
{
    protected $fillable = [
        'patient_id', 'name', 'image_url', 'image_public_id',
        'price', 'quantity', 'payment_method', 'consumed_at', 'notes',
    ];

    protected $casts = [
        'consumed_at' => 'date',
        'price'       => 'decimal:2',
        'quantity'    => 'decimal:2',
    ];

    public function patient()
    {
        return $this->belongsTo(ChronicPatient::class, 'patient_id');
    }
}
