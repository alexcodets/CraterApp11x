<?php

namespace Crater\Models;

use Crater\Traits\ModelPagination;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PbxServicesAppRate extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ModelPagination;

    protected $fillable = ['app_name', 'quantity', 'costo', 'pbx_package_id', 'pbx_service_id'];

    public function pbxService(): BelongsTo
    {
        return $this->belongsTo(PbxServices::class, 'pbx_service_id');
    }
}
