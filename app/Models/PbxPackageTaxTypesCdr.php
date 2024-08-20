<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PbxPackageTaxTypesCdr extends Model
{
    use HasFactory;

    protected $fillable = [
        'pbx_package_id',
        'tax_types_id',
        'name',
        'percent',
        'compound_tax',
        'status',
    ];

    public function pbxTaxTypesHistory(): MorphMany
    {
        return $this->morphMany(HistoryCallIndiTaxType::class, 'taxable');
    }
}
