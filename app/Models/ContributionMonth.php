<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContributionMonth extends Model
{
    protected $fillable = ['contribution_id', 'year', 'month'];

    public function contribution()
    {
        return $this->belongsTo(Contribution::class);
    }
}
