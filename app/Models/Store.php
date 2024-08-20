<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'company_id',
        'user_id',
    ];

    // Filters 27 Jul Alejo
    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('name')) {
            $query->whereName($filters->get('name'));
        }

        if ($filters->get('description')) {
            $query->whereDescription($filters->get('description'));
        }

        if ($filters->get('company_name')) {
            $query->whereCompanyName($filters->get('company_name'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'name';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }

    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopeWhereName($query, $name)
    {
        return $query->where('stores.name', 'LIKE', '%'.$name.'%');
    }

    public function scopeWhereDescription($query, $description)
    {
        return $query->where('stores.description', 'LIKE', '%'.$description.'%');
    }

    public function scopeWhereCompanyName($query, $companyName)
    {
        return $query->where('companies.name', 'LIKE', '%'.$companyName.'%');
    }


    // /Filters 27 Jul Alejo

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_store');
    }

    public function itemsGroups(): BelongsToMany
    {
        return $this->belongsToMany(ItemGroup::class, 'item_group_store');
    }

    public function storeItem(): BelongsToMany
    {
        return $this->belongsToMany(Item::class);
    }
}
