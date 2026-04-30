<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityPhoto extends Model
{
    protected $fillable = [
        'activity_id', 'photo_url', 'photo_public_id',
        'caption_ar', 'caption_fr',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
