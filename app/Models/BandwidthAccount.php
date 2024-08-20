<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BandwidthAccount extends Model
{
    use HasFactory;

    public function scopeActive($query)
    {
        return $query->where('enable', 1);
    }

    public function scopeSelected($query)
    {
        return $query->where('selected', 1);
    }
}
