<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PbxServers extends Model
{
    use HasFactory;
    use SoftDeletes;

    // nombre de la tabla
    protected $table = 'pbx_servers';

    protected $fillable = ['company_id', 'creator_id', 'country_id', 'server_label', 'hostname', 'ssl_port', 'api_key', 'national_dialing_code', 'international_dialing_code', 'status', 'timezone'];

    // para el softDelete
    //Status values (8 ⇒ "Answered", 4 ⇒ "Not Answered", 2 ⇒ "Busy", 1 ⇒ "Failed") 0 is all (made up value)
    public const STATUS = [8, 4, 2, 1];
    public const STATUS_ACTIVE = 'A';
    public const STATUS_INACTIVE = 'I';

    public function getTimezoneKeyAttribute()
    {
        return $this->timezone ? $this->timezone : 'UTC';
    }

    // relacion de uno a muchos con el modelo PbxServerCdrStatus
    public function cdrStatus(): HasMany
    {
        return $this->hasMany(PbxServerCdrStatus::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function pbxCdrTenants(): HasMany
    {
        return $this->hasMany(PbxCdrTenant::class, 'pbx_server_id');
    }

    public function pbxTenants(): HasMany
    {
        return $this->hasMany(PbxTenant::class, 'pbx_server_id');
    }

    public function pbxServerTenants(): HasMany
    {
        return $this->hasMany(PbxServerTenant::class, 'pbx_server_id');
    }

    public function scheduleLogs(): MorphMany
    {
        return $this->morphMany(ScheduleLog::class, 'model');
    }

    public function downLogs(): MorphMany
    {
        return $this->morphMany(ScheduleLog::class, 'model')->where('module_name', 'PbxCheckConnectionService')
            ->where('message', 'Server Is Down');
    }

    public function getStatusArrayAttribute(): \Illuminate\Support\Collection
    {
        $base = $this->cdrStatus(['id', 'pbx_servers_id', 'status'])->pluck('status');
        if ($base->isEmpty()) {
            return collect(8);
        }
        if ($base->contains(0)) {
            return collect(self::STATUS);
        }

        return $base;
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('hostname')) {
            $query->whereHostname($filters->get('hostname'));
        }

        if ($filters->get('status')) {
            $query->where('status', $filters->get('status'));
        }

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('company_id', $company_id);
    }

    public function scopeWhereSearch($query, $search)
    {
        $query->where('name', 'LIKE', '%'.$search.'%');
    }

    public function scopeWhereHostname($query, $hostname)
    {
        return $query->where('name', 'LIKE', '%'.$hostname.'%');
    }
}
