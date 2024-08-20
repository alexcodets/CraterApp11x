<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class PbxServerTenant extends Model
{
    use SoftDeletes;
    use HasFactory;

    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;
    public const STATUS_INCOMPLETE = 2;

    protected $fillable = [
        'name',
        'tenant_code',
        'tenant_id',
        'status',
        'pbx_server_id',
        'creator_id',
        'company_id',
    ];

    public function pbxServer(): BelongsTo
    {
        return $this->belongsTo(PbxServers::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function pbxDid(): HasMany
    {
        return $this->hasMany(PbxDID::class, 'pbx_tenant_code', 'tenant_code')
            ->where('pbxdid_id', 'tenant_id');
    }

    public function pbxExtensions(): HasMany
    {
        return $this->hasMany(PbxExtensions::class, 'pbx_tenant_code', 'tenant_code')
            ->where('pbxext_id', 'tenant_id');
    }

    public function pbxTenant(): HasOne
    {
        return $this->hasOne(PbxTenant::class, 'code', 'tenant_code')
            ->where('tenantid', $this->tenant_id)
            ->where('pbx_server_id', $this->pbx_server_id);
    }

    protected function getStatusNameAttribute(): string
    {
        $status = [
            self::STATUS_ACTIVE => 'Active/Completed',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_INCOMPLETE => 'Incomplete',
        ];

        return $status[$this->status];
    }

    protected function getIsBeingUsedAttribute(): bool
    {
        return PbxTenant::query()
            ->where('pbx_server_id', $this->pbx_server_id)
            ->where('tenantid', $this->tenant_id)
            ->where('pbx_server_id', $this->pbx_server_id)
            ->exists();

    }

    protected function getHasAPbxTenantAttribute(): bool
    {
        return PbxTenant::query()
            ->where('pbx_server_id', $this->pbx_server_id)
            ->where('tenantid', $this->tenant_id)
            ->where('pbx_server_id', $this->pbx_server_id)
            ->exists();
    }

    protected function getPbxTenantListAttribute(): array
    {
        return PbxTenant::query()
            ->where('pbx_server_id', $this->pbx_server_id)
            ->where('tenantid', $this->tenant_id)
            ->where('pbx_server_id', $this->pbx_server_id)
            ->pluck('id')->toArray();
    }
}
