<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'option', 'value', 'active'
    ];

    public function getjsonValuesAttribute()
    {
        return json_decode(json_decode($this->value));
    }
}
