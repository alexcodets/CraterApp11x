<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomRateGroupItems extends Model
{
    use HasFactory;

    protected $table = 'international_rate_prefixrate_group';

    protected $fillable = ['company_id', 'prefixrate_id', 'int_rate_id'];

    public function customRate(): BelongsTo
    {
        return $this->belongsTo(CustomRate::class, 'int_rate_id');
    }

    public function customRateGroup(): BelongsTo
    {
        return $this->belongsTo(CustomRateGroup::class, 'prefixrate_id');
    }
}
