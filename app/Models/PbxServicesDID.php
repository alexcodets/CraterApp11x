<?php

namespace Crater\Models;

use Crater\Traits\ModelPagination;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PbxServicesDID extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ModelPagination;

    // nombre de la tabla
    protected $table = 'pbx_services_did';

    protected $fillable = [
        'company_id',
        'creator_id',
        'pbx_service_id',
        'pbx_did_id',
        'custom_did_id',
        'cost_per_day',
        'prorate',
        'date_prorate',
        'invoiced_prorate',
        'name_prefix',
        'price'
         // 'pbx_profile_did_id'

    ];

    // para el softDelete
    public function did(): BelongsTo
    {
        return $this->belongsTo(PbxDID::class, 'pbx_did_id');
    }

    public function customDid(): BelongsTo
    {
        return $this->belongsTo(ProfileDidTollFree::class, 'custom_did_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(PbxServices::class, 'pbx_service_id');
    }
}
