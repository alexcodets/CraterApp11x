<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerPackageItem extends Model
{
    use HasFactory;
    use softDeletes;

    protected $fillable = [
        'customer_package_id',
        'item_id',
        'creator_id',
        'company_id',
        'name',
        'description',
        'quantity',
        'price',
        'discount_type',
        'discount',
        'discount_val',
        'tax',
        'total',
        'end_period_act',
        'end_period_number'
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'total' => 'integer',
            'discount' => 'float',
            'quantity' => 'float',
            'discount_val' => 'integer',
            'tax' => 'integer'
        ];
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(CustomerPackageTax::class);
    }
}
