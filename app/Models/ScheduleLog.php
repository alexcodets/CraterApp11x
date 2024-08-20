<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ScheduleLog extends Model
{
    use HasFactory;

    public const LVL_DEUG = 0;
    public const LVL_INFO = 1;
    public const LVL_WARNING = 2;
    public const LVL_ERROR = 3;

    protected $fillable = [
        'module_name', 'extra_data', 'message', 'lvl', 'model_type', 'model_id'
    ];

    public function getNameAttribute()
    {
        return $this->module_name;
    }

    public function getDataAttribute()
    {
        return json_decode($this->extra_data);
    }

    public function getMetaDataAttribute()
    {
        return json_decode($this->extra_data);
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
