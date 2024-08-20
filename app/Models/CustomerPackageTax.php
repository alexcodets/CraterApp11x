<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerPackageTax extends Model
{
    use HasFactory;
    use softDeletes;

    protected $fillable = [
        'tax_type_id',
        'customer_package_id',
        'customer_package_item_id',
        'creator_id',
        'company_id',
        'name',
        'amount',
        'percent',
        'compound_tax'
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'percent' => 'float'
        ];
    }
}
