<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PbxCdrTenant extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'tenantid', 'pbx_server_id', 'date_begin', 'status'];

    protected function casts(): array
    {
        return [
            'date_begin' => 'datetime:Y-m-d',
            'job_active_at' => 'datetime:Y-m-d',
            'last_update' => 'datetime:Y-m-d',
        ];
    }

    public function pbxServer(): BelongsTo
    {
        return $this->belongsTo(PbxServers::class);
    }

    public function tenant(): HasOne
    {
        return $this->hasOne(PbxTenant::class, 'tenantid', 'tenantid');
    }

    public function cdrs(): HasMany
    {
        return $this->hasMany(PbxTenantCdr::class);
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);
        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'created_at';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }
}
