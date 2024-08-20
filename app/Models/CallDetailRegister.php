<?php

namespace Crater\Models;

use Crater\Models\actions\CallDetailRegisterAction;
use Crater\Traits\BindsDynamically;
use Crater\Traits\ModelFormatingTime;
use Crater\Traits\ModelPagination;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallDetailRegister extends Model
{
    use HasFactory;
    use BindsDynamically;
    use CallDetailRegisterAction;
    use ModelPagination;
    use ModelFormatingTime;

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $appends = ['formatted_start_date', 'formatted_duration', 'formatted_billing_duration', 'formatted_round_duration','getFormattedExcusiveCost'];

    //protected $dates   = ['start_date'];
    public $status = [
        8 => "Answered", 4 => "Not Answered", 2 => "Busy", 1 => "Failed",
    ];

    public $typeName = [
        0 => 'Inbound',
        1 => 'Outbound',
    ];

    public $timeFormat = '';

    public $durationFormat = '';

    public function getTimeZoneGlobalAttribute()
    {
        return null;
    }

    public function scopeInbound($query)
    {
        return $query->where('type', 0);
    }

    public function scopeOutbound($query)
    {
        return $query->where('type', 1);
    }

    public function scopeUncalculated($query)
    {
        return $query->whereNull('billed_at');
    }

    public function scopeCalculated($query)
    {
        return $query->whereNotNull('billed_at');
    }

    public function scopeOnlyUnCustomRate($query)
    {
        return $query->whereNull('international_rate_id');
    }

    public function scopeOnlyCustomRate($query)
    {
        return $query->whereNotNull('international_rate_id');
    }

    public function scopeBilled($query)
    {
        return $query->where(function ($query2) {
            return $query2->WhereNotNull('pbx_did_id')->orWhereNotNull('pbx_extension_id')->orWhereNotNull('international_rate_id');
        });
    }

    public function scopePending($query)
    {
        return $query->where(function ($query2) {
            return $query2->WhereNull('pbx_did_id')->WhereNull('pbx_extension_id')->WhereNull('international_rate_id');
        });
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

    public function getFormattedStartDateAttribute()
    {
        return $this->getFormattedDateAttribute($this->start_date ?? 0, $this->timeZoneGlobal);
    }

    public function getFormattedDurationAttribute()
    {
        return $this->getFormattedTimeAttribute($this->total_duration ?? 0, $this->timeZoneGlobal);
    }

    public function getFormattedBillingDurationAttribute()
    {
        return $this->getFormattedTimeAttribute($this->total_billing_duration ?? 0, $this->timeZoneGlobal);
    }

    public function getFormattedRoundDurationAttribute()
    {
        return $this->getFormattedTimeAttribute($this->total_round_duration ?? 0, $this->timeZoneGlobal);
    }

    public function getGetFormattedExcusiveCostAttribute()
    {
        $nombre_format_francais = number_format($this->total_exclusive_cost, 5);

        return  $nombre_format_francais ;
    }
}
