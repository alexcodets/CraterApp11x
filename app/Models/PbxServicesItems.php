<?php

namespace Crater\Models;

use Crater\Traits\ModelPagination;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PbxServicesItems extends Model
{
    use HasFactory;
    use ModelPagination;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pbx_services_id',
        'item_group_id',
        'items_id',
        'company_id',
        'discount_type',
        'quantity',
        'discount',
        'discount_val',
        'price',
        'tax',
        'total',
        'status',
        'description',
        'name',
        'end_period_act',
        'end_period_number'
    ];

    // taxes
    public function taxes(): HasMany
    {
        return $this->hasMany(Tax::class, 'pbx_service_item_id');
    }

    public function taxes_per_item(): HasMany
    {
        return $this->hasMany(Tax::class, 'pbx_service_item_id')->where("invoice_item_id", null);
    }
}
