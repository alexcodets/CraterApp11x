<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const BILLING_TYPE = 'billing';
    public const SHIPPING_TYPE = 'shipping';

    protected $guarded = ['id'];

    protected $appends = [
        'CountryName',
        'StateName',
    ];

    public function getCountryNameAttribute()
    {
        $name = $this->country ? $this->country->name : null;

        return $name;
    }

    public function getStateNameAttribute()
    {
        $name = $this->state ? $this->state->name : null;

        return $name;
    }

    // Paginador
    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function avalaralocation()
    {
        return AvalaraLocation::where("addresses_id", $this->id)->first();
    }

    public function toLocationRequest(): array
    {
        return [
            'country' => $this->country->code,
            'state' => $this->state->code,
            'county' => null,
            'city' => $this->city,
            'zip' => $this->zip,
            'best_match' => true,
            'limit' => 1,
        ];
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);
        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'country_id';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }
}
