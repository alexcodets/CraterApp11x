<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class PbxDID extends Model
{
    use HasFactory;
    use SoftDeletes;

    // nombre de la tabla
    protected $table = 'pbx_did';

    protected $fillable = [
        'company_id',
        'creator_id',
        'pbx_tenant_id',
        'number',
        'server',
        'status',
        'api_id',
        'e164',
        'e164_2',
        'ext',
        'number2',
        'type',
        'trunk',
        'custom_did_id',
        'pbxdid_id',
        'pbx_server_id',
        'pbx_tenant_code',
        'didid',
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
        ];
    }

    public function totalCalls(): HasOne
    {
        return $this->hasOne(CallDetailRegisterTotal::class, 'pbx_did_id');
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(ProfileDID::class, 'number', 'did_number');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ProfileDID::class, 'number', 'did_number');
    }

    public function pbxTenant(): BelongsTo
    {
        return $this->belongsTo(PbxTenant::class);
    }

    public function pbxServer(): BelongsTo
    {
        return $this->belongsTo(PbxServers::class);
    }

    public function pbxServiceDid(): HasOne
    {
        return $this->hasOne(PbxServicesDID::class, 'pbx_did_id');
    }

    public function pbxServices(): BelongsToMany
    {
        return $this->belongsToMany(PbxServices::class, 'pbx_services_did', 'pbx_did_id', 'pbx_service_id');
    }

    public function pbxService(): \Illuminate\Database\Eloquent\Relations\HasOneThrough
    {
        return $this->hasOneThrough(PbxServices::class, PbxServicesDID::class, 'pbx_did_id', 'id', 'id', 'pbx_service_id');
    }

    public function getTypeNameAttribute(): ?string
    {
        $types = [
            'Extension',
            'Forward DID to Extension (Multi User)',
            'Ring Group',
            'IVR',
            'Queues',
            'External Number',
            'IVR tree',
        ];

        if (is_null($this->type) || ! is_numeric($this->type)) {
            return null;
        }

        return $this->type < count($types) ? $types[$this->type] : 'Unknown';

    }
}
