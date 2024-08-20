<?php

namespace Crater\Models;

use Crater\Traits\ModelPagination;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PbxServicesExtensions extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ModelPagination;

    // nombre de la tabla
    protected $table = 'pbx_services_extensions';

    protected $fillable = [
        'company_id',
        'creator_id',
        'pbx_service_id',
        'pbx_extension_id',
        'cost_per_day',
        'prorate',
        'date_prorate',
        'invoiced_prorate',
        'price',
    ];

    // para el softDelete
    public function extension(): BelongsTo
    {
        return $this->belongsTo(PbxExtensions::class, 'pbx_extension_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(PbxServices::class, 'pbx_service_id');
    }

    // departament
    public function departments(): BelongsTo
    {
        // return $this->belongsTo(CustomSearch::class, 'pbx_extension_id', 'custom_search_id');
        return $this->hasMany(pbxExtensionCustomSearch::class, 'pbx_extension_id');
    }
}
