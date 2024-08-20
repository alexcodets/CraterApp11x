<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PbxServicesTaxTypes extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pbx_services_id',
        'tax_types_id',
        'name',
        'percent',
        'compound_tax',
        'status',
        'amount'
    ];

    public function pbxTaxTypesHistory(): MorphMany
    {
        return $this->morphMany(HistoryCallIndiTaxType::class, 'taxable');
    }
}
