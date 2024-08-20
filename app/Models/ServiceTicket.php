<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceTicket extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['service_id', 'service_type'];

    public function service(): MorphTo
    {
        return $this->morphTo();
    }
}
