<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerPackageDiscount extends Model
{
    use HasFactory;
    use softDeletes;

    protected $fillable = [
        'customer_package_id',
        'discount_type',
        'discount',
        'discount_val',
        'start_date',
        'end_date',
        'term_type',
        'term',
        'time_unit_number'
    ];

    protected function casts(): array
    {
        return [
            'discount' => 'float',
            'discount_val' => 'integer',
        ];
    }
}
