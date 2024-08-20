<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobBatch extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public function scopeForService(Builder $query, int $service = 1)
    {
        return $query->where('name', '=', "Import Cdr for Service: $service");
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
