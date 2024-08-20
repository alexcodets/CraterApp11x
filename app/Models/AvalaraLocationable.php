<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AvalaraLocationable extends Model
{
    use HasFactory;

    public function locationable(): MorphTo
    {
        return $this->morphTo();
    }
}
