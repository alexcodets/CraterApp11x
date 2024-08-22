<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const string BILLING_TYPE = 'billing';
    public const string SHIPPING_TYPE = 'shipping';

    protected $guarded = ['id'];

    protected $appends = [
        'country_name',
        'state_name',
    ];

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

    public function avalaraLocation(): AvalaraLocation
    {
        return AvalaraLocation::where('addresses_id', $this->id)->first();
    }

    public function toLocationRequest(): array
    {
        return [
            'country'    => $this->country->code,
            'state'      => $this->state->code,
            'county'     => null,
            'city'       => $this->city,
            'zip'        => $this->zip,
            'best_match' => true,
            'limit'      => 1,
        ];
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);
        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'country_id';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            return $query->whereOrder($field, $orderBy);
        }
        return $query;
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy): void
    {
        $query->orderBy($orderByField, $orderBy);
    }

    protected function countryName(): Attribute
    {
        return new Attribute(
            get: fn () => $this?->country?->name,
        );
    }

    protected function stateName(): Attribute
    {
        return new Attribute(
            get: fn () => $this?->state?->name
        );
    }
}
