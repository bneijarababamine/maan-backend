<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = ['name_fr', 'name_ar', 'logo', 'balance', 'is_active'];

    protected $casts = [
        'balance'   => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public static function findByMethod(string $method): ?self
    {
        return self::whereRaw('LOWER(name_fr) = ?', [strtolower($method)])->first();
    }

    public static function canDeduct(string $method, float $amount): array
    {
        $bank = self::findByMethod($method);
        if (!$bank) {
            return ['ok' => true];
        }
        $available = (float) $bank->balance;
        if ($available - $amount < 0) {
            return [
                'ok'        => false,
                'bank_fr'   => $bank->name_fr,
                'bank_ar'   => $bank->name_ar,
                'available' => $available,
                'required'  => $amount,
            ];
        }
        return ['ok' => true];
    }

    public static function adjustByMethod(string $method, float $delta): void
    {
        $bank = self::findByMethod($method);
        if ($bank) {
            $bank->increment('balance', $delta);
        }
    }
}
