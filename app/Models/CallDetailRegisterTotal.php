<?php

namespace Crater\Models;

use Crater\Traits\BindsDynamically;
use Crater\Traits\ModelFormatingTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallDetailRegisterTotal extends Model
{
    use HasFactory;
    use BindsDynamically;
    use ModelFormatingTime;

    protected $guarded = ['id'];

    protected $appends = ['formatted_duration', 'formatted_total_duration'];

    public function scopeInbound($query)
    {
        return $query->where('type', 0);
    }

    public function scopeOutbound($query)
    {
        return $query->where('type', 1);
    }

    public function scopeCurrent($query)
    {
        return $query->whereNull('invoice_id');
    }

    public function scopeOnlyUnCustomRate($query)
    {
        return $query->whereNull('international_rate_id');
    }

    public function scopeOnlyCustomRate($query)
    {
        return $query->whereNotNull('international_rate_id');
    }

    public function did(): BelongsTo
    {
        return $this->belongsTo(PbxDID::class, 'pbx_did_id');
    }

    public function internationalRate(): BelongsTo
    {
        return $this->belongsTo(InternationalRate::class, 'international_rate_id');
    }

    public function customRate(): BelongsTo
    {
        return $this->belongsTo(InternationalRate::class, 'international_rate_id');
    }

    public function extension(): BelongsTo
    {
        return $this->belongsTo(PbxExtensions::class, 'pbx_extension_id');
    }

    public function getFormattedDurationAttribute()
    {
        return $this->getFormattedTimeAttribute($this->duration ?? 0);
    }

    public function getFormattedTotalDurationAttribute()
    {
        return $this->getFormattedTimeAttribute($this->total_duration ?? 0);
    }

    public function getNumberForOutBound()
    {
        // 1 = outbound
        return $this->number ?? $this->extension->number;
    }

    public function getNumerForInBound()
    {
        //0 is inbound
        return $this->number ?? $this->did->number;
    }

    public function getTypeCallAttribute()
    {
        if ($this->pbx_did_id) {
            return 'Did';
        }
        if ($this->pbx_extension_id) {
            return 'Extension';
        }
        if ($this->international_rate_id) {
            return 'Custom Destination';
        }

        return 'General';
    }
}
