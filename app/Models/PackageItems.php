<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageItems extends Model
{
    use HasFactory;

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'package_id',
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

    protected function casts(): array
    {
        return [
            'quantity' => 'float',
        ];
    }
}
