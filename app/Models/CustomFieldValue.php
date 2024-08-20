<?php

namespace Crater\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CustomFieldValue extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    protected $appends = [
        'defaultAnswer'
    ];

    public function setDateAnswerAttribute($value)
    {
        $this->attributes['date_answer'] = Carbon::createFromFormat('Y-m-d', $value);
    }

    public function setTimeAnswerAttribute($value)
    {
        $this->attributes['time_answer'] = date("H:i:s", strtotime($value));
    }

    public function setDateTimeAnswerAttribute($value)
    {
        $this->attributes['date_time_answer'] = Carbon::createFromFormat('Y-m-d H:i', $value);
    }

    public function getDefaultAnswerAttribute()
    {
        $value_type = getCustomFieldValueKey($this->type);

        return $this->$value_type;
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function customField(): BelongsTo
    {
        return $this->belongsTo(CustomField::class);
    }

    public function customFieldValuable(): MorphTo
    {
        return $this->morphTo();
    }
}
