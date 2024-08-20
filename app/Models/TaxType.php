<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'percent',
        'company_id',
        'compound_tax',
        'collective_tax',
        'description',
        'state_id',
        'country_id',
        'tax_category_id',
        'tax_agency_id',
        'city',
        'county',
        'for_cdr'
    ];

    protected function casts(): array
    {
        return [
            'percent' => 'float'
        ];
    }

    public function taxes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Tax::class);
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('company_id', $company_id);
    }

    public function scopeWhereTaxType($query, $tax_type_id)
    {
        $query->orWhere('id', $tax_type_id);
    }

    public function scopeWhereForCdr($query, $forCdr)
    {
        $query->where('for_cdr', $forCdr);
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if (array_key_exists("for_cdr", $filters->toArray())) {
            $query->whereForCdr($filters->get('for_cdr'));
        }

        if ($filters->get('tax_type_id')) {
            $query->whereTaxType($filters->get('tax_type_id'));
        }

        if ($filters->get('company_id')) {
            $query->whereCompany($filters->get('company_id'));
        }

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'payment_number';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopeWhereSearch($query, $search)
    {
        $query->where('name', 'LIKE', '%'.$search.'%');
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public function scopeWhereState($query, $state_id)
    {
        if ($state_id) {
            $query->where('state_id', $state_id);
        }
    }

    public function scopeWhereCountry($query, $country_id)
    {
        if ($country_id) {
            $query->where('country_id', $country_id);
        }
    }

    public function scopeWhereAgency($query, $tax_agency_id)
    {
        if ($tax_agency_id) {
            $query->where('tax_agency_id', $tax_agency_id);
        }
    }

    public function scopeWhereCategory($query, $tax_category_id)
    {
        if ($tax_category_id) {
            $query->where('tax_category_id', $tax_category_id);
        }
    }

    public function scopeWhereCity($query, $city)
    {
        if ($city) {
            $query->where('city', $city);
        }
    }

    public function scopeWhereCounty($query, $county)
    {
        if ($county) {
            $query->where('county', $county);
        }
    }
}
