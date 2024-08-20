<?php

namespace Crater\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PbxJobLog extends Model
{
    use HasFactory;
    use MassPrunable;

    public const LVL_DEBUG = 0;
    public const LVL_INFO = 1;
    public const LVL_WARNING = 2;
    public const LVL_ERROR = 3;

    protected $fillable = [
        'name',
        'lvl',
        'response',
        'data',
        'note',
        'pbx_service_id',
    ];

    protected $appends = [
        'formattedCreatedAt',
    ];

    public function getFormattedCreatedAtAttribute($value): string
    {
        return Carbon::parse($this->created_at)->format('Y-m-d m:s');
    }

    public function getJsonDataAttribute()
    {
        return json_decode($this->data);
    }

    // relacion con pbx_service
    public function pbxService(): BelongsTo
    {
        return $this->belongsTo(PbxServices::class);
    }

    // relacion con customer
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function scopeWherePbxService($query, $pbx_services_number)
    {
        if ($pbx_services_number) {
            return $query->whereHas('pbxService', function ($query) use ($pbx_services_number) {
                $query->where('pbx_services_number', 'like', "%{$pbx_services_number}%");
            });
        }

    }

    public function scopeWhereDate($query, $filters)
    {
        // Log::info($filters['to_date']);
        if ($filters['from_date'] && $filters['to_date']) {
            $date_from = Carbon::parse($filters['from_date']);
            $date_to = Carbon::parse($filters['to_date']);

            // whereBetween
            return $query->whereBetween('created_at', [$date_from, $date_to]);

        }

        return $query;

    }

    public function prunable()
    {
        return static::where('created_at', '<=', now()->subMonth());
    }
}
