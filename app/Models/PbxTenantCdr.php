<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PbxTenantCdr extends Model
{
    use HasFactory;
    use MassPrunable;

    protected $fillable = ['from', 'to', 'start_date', 'billing_duration', 'status', 'unique_id', 'type', 'trunk_id', 'pbx_cdr_tenant_id'];

    public $timestamps = false;

    /**
     * Get the user that owns the PbxTenantCdr
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(PbxCdrTenant::class, 'pbx_cdr_tenant_id');
    }

    public function prunable(): Builder
    {
        return static::where('start_date', '<', now()->subDays(100)->timestamp);
    }
}
