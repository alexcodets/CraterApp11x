<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Retentions extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'concept',
        'minimium_base_per_unit',
        'minimium_base_in_currency',
        'type_of_minimium_base_in_currency',
        'percentage',
        'foreing',
        'country_id',
        'type_vat_regime',
        'great_contributor',
        'self_retaining',
        'vat_withholding_agent',
        'simple_tax_regime',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('concept')) {
            // $query->whereHostname($filters->get('hostname'));
            return $query->where('name', 'LIKE', '%'.$filters->get('concept').'%');
        }

        /* if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        } */
    }
}
