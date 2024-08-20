<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstimateTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['path', 'view', 'name'];

    public function estimates(): HasMany
    {
        return $this->hasMany(Estimate::class);
    }

    public function getPathAttribute($value)
    {
        return url($value);
    }
}
