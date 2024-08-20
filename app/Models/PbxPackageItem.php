<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PbxPackageItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pbx_package_id',
        'item_group_id',
        'discount_type',
        'quantity',
        'discount',
        'discount_val',
        'price',
        'tax',
        'total',
        'items_id',
        'status',
        'description',
        'company_id',
        'end_period_act',
        'end_period_number'
    ];

    // taxes
    public function taxes(): HasMany
    {
        return $this->hasMany(Tax::class, 'pbx_package_item_id', 'id');
    }
}
