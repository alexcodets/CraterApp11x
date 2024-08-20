<?php

namespace Crater\Models;

use Crater\Models\actions\PbxTenantAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PbxTenant extends Model
{
    use HasFactory;
    use SoftDeletes;
    use PbxTenantAction;

    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;
    public const STATUS_INCOMPLETE = 2;

    // nombre de la tabla
    protected $table = 'pbx_tenant';

    protected $fillable = [
        'company_id',
        'creator_id',
        'name',
        'code',
        'tenantid',
        'details',
        'tenantid',
        'pbx_server_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'did_details' => 'array',
            'tenant_details' => 'array',
            'details' => 'array',
        ];
    }

    public function getConfigurationAttribute()
    {
        return json_decode($this->config);
    }

    public function pbxService(): HasOne
    {
        return $this->hasOne(PbxServices::class, 'pbx_tenant_id');
    }

    public function settings(): MorphMany
    {
        return $this->morphMany(GeneralSetting::class, 'setting');
    }

    public function pbxExtensions(): HasMany
    {
        return $this->hasMany(PbxExtensions::class);
    }

    public function pbxDids(): HasMany
    {
        return $this->hasMany(PbxDID::class);
    }

    public function pbxServer(): BelongsTo
    {
        return $this->belongsTo(PbxServers::class, 'pbx_server_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scheduleLogs(): MorphMany
    {
        return $this->morphMany(ScheduleLog::class, 'model');
    }
}
