<?php

namespace Crater\Models;

use Crater\Models\actions\PbxExtensionAction;
use Crater\Traits\ModelPagination;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PbxExtensions extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ModelPagination;
    use PbxExtensionAction;

    // nombre de la tabla
    protected $table = 'pbx_extensions';

    protected $fillable = [
        'company_id',
        'creator_id',
        'name',
        'email',
        'status',
        'details',
        'pbx_tenant_id',
        'api_id',
        'ext',
        'linenum',
        'location',
        'auto_provisioning',
        'macaddress',
        'dhcp',
        'static_ip',
        'time_zone',
        'protocol',
        'ua_fullname',
        'ua_id',
        'ua_name',
        'extensionid',
        'pbxext_id',
        'pbx_server_id',
        'pbx_tenant_code',
        'pbxext_id',
        'pin',
        'invoiced_prorate',

    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ext_details' => 'array',
            'tenant_details' => 'array',
        ];
    }

    public function totalCalls(): HasOne
    {
        return $this->hasOne(CallDetailRegisterTotal::class, 'pbx_extension_id');
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(ProfileExtensions::class, 'ext', 'extensions_number');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ProfileExtensions::class, 'ext', 'extensions_number');
    }

    public function pbxService(): BelongsToMany
    {
        return $this->belongsToMany(PbxServices::class, 'pbx_services_extensions', 'pbx_extension_id', 'pbx_service_id');
    }

    public function pbxSingleService(): HasOneThrough
    {
        return $this->hasOneThrough(PbxServices::class, PbxServicesExtensions::class, 'pbx_extension_id', 'pbx_services.id', 'id', 'pbx_service_id');
    }

    public function pbxServices(): BelongsToMany
    {
        return $this->belongsToMany(PbxServices::class, 'pbx_services_extensions', 'pbx_extension_id', 'pbx_service_id');
    }

    public function customSearches(): BelongsToMany
    {
        return $this->belongsToMany(CustomSearch::class, 'pbx_extension_custom_searches', 'pbx_extension_id', 'custom_search_id');
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(CustomSearch::class, 'pbx_extension_custom_searches', 'pbx_extension_id', 'custom_search_id');
    }

    public function pbxTenant(): BelongsTo
    {
        return $this->belongsTo(PbxTenant::class);
    }

    public function pbxServer(): BelongsTo
    {
        return $this->belongsTo(PbxServers::class, 'pbx_server_id');
    }

    public function scheduleLogs(): MorphMany
    {
        return $this->morphMany(ScheduleLog::class, 'model');
    }

    public function scheduleStatusLogs(): MorphMany
    {
        return $this->morphMany(ScheduleLog::class, 'model')->where('module_name', '=', 'pbx:updateExtensionStatus');
    }

    public function disabledLogs(): MorphMany
    {
        return $this->morphMany(ScheduleLog::class, 'model')->where('module_name', 'pbx:updateExtensionStatus')
            ->where('message', 'disabled');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->name} ({$this->ext})";
    }
}
