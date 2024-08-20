<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PbxServicesTaxTypesCdr extends Model
{
    use HasFactory;

    protected $table = 'pbx_services_tax_types_cdr';

    public function pbxTaxTypesHistory(): MorphMany
    {
        return $this->morphMany(HistoryCallIndiTaxType::class, 'taxable');
    }
}
