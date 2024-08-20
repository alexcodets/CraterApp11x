<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxAgency extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tax_agencies';

    protected $fillable = [
        'name',
        'number',
        'email',
        'website',
        'phone',
        'note',
    ];

    // para el softDelete
    /* protected $maps = [
    'name' => 'name_tax_agency',
    'phone' => 'phone_tax_agency'
    ];
    protected $append = ['name_tax_agency', 'phone_tax_agency'];

    public function getname_tax_agencyAttribute()
    {
    return $this->attributes['name'];
    }
    public function getphone_tax_agencyAttribute()
    {
    return $this->attributes['phone'];
    } */

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public function taxAgencyAddress(): HasMany
    {
        return $this->hasMany(Address::class, 'tax_agency_id', 'id');
    }

    public function scopeWhereName($query, $name)
    {
        return $query->where('tax_agencies.name', 'LIKE', '%'.$name.'%');
    }

    public function scopeWhereNumber($query, $number)
    {
        return $query->where('tax_agencies.number', 'LIKE', '%'.$number.'%');
    }

    public function scopeWhereCountry($query, $country_id)
    {
        return $query->select('tax_agencies.*')
            ->leftJoin('addresses', 'tax_agencies.id', '=', 'addresses.tax_agency_id')
            ->where('addresses.country_id', $country_id)->get();

    }

    public function scopeWhereState($query, $state_id)
    {
        return $query->select('tax_agencies.*')
            ->leftJoin('addresses', 'tax_agencies.id', '=', 'addresses.tax_agency_id')
            ->where('addresses.state_id', $state_id)->get();
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('name')) {
            $query->whereName($filters->get('name'));
        }

        if ($filters->get('number')) {
            $query->whereNumber($filters->get('number'));
        }

        if ($filters->get('country_id')) {
            $query->whereCountry($filters->get('country_id'));
        }

        if ($filters->get('state_id')) {
            $query->whereState($filters->get('state_id'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'number';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }
}
