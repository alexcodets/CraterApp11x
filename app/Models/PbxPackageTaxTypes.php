<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PbxPackageTaxTypes extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pbx_package_id',
        'tax_types_id',
        'name',
        'percent',
        'compound_tax',
        'status'
    ];
}
