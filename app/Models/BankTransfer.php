<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankTransfer extends Model
{
    protected $fillable = ['from_bank_id', 'to_bank_id', 'amount', 'notes', 'created_by'];

    protected $casts = ['amount' => 'decimal:2'];

    public function fromBank()
    {
        return $this->belongsTo(Bank::class, 'from_bank_id');
    }

    public function toBank()
    {
        return $this->belongsTo(Bank::class, 'to_bank_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
