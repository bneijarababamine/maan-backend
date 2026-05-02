<?php

namespace App\Models;

use App\Services\CloudinaryService;
use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function (Donor $donor) {
            $cloudinary = app(CloudinaryService::class);

            foreach ($donor->donations()->get() as $donation) {
                Bank::adjustByMethod($donation->payment_method, -(float) $donation->amount);

                foreach ($donation->screenshots ?? [] as $s) {
                    if (!empty($s['public_id'])) {
                        $cloudinary->delete($s['public_id']);
                    }
                }
                if (empty($donation->screenshots) && $donation->screenshot_public_id) {
                    $cloudinary->delete($donation->screenshot_public_id);
                }
            }
        });
    }

    protected $fillable = [
        'full_name', 'gender', 'phone', 'whatsapp', 'address',
        'profession', 'is_member', 'member_id',
    ];

    protected $casts = ['is_member' => 'boolean'];

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function getTotalDonationsAttribute(): float
    {
        return (float) $this->donations()->sum('amount');
    }
}
