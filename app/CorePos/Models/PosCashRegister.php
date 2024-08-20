<?php

namespace Crater\CorePos\Models;

use Crater\Models\CashHistory;
use Crater\Traits\ModelPagination;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PosCashRegister extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ModelPagination;

    protected $table = 'cash_register';
    //protected $guarded = ['id'];

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('name')) {
            $query->WhereName($filters->get('name'));
        }

        if ($filters->get('description')) {
            $query->WhereDescription($filters->get('description'));
        }

        if ($filters->get('store_name')) {
            $query->whereStoreName($filters->get('store_name'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'id';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    // Filter by Name
    public function scopeWhereName($query, $name)
    {
        return $query->where('cash_register.name', 'LIKE', '%'.$name.'%');
    }

    // Filter by Description
    public function scopeWhereDescription($query, $description)
    {
        return $query->where('cash_register.description', 'LIKE', '%'.$description.'%');
    }

    // Filter by Store Name
    public function scopeWhereStoreName($query, $store_name)
    {
        return $query->where('stores.name', 'LIKE', '%'.$store_name.'%');
    }

    // Order By
    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function cashHistory(): HasMany
    {
        return $this->hasMany(CashHistory::class, 'cash_register_id');
    }

    public function table(): BelongsToMany
    {
        return $this->belongsToMany(Table::class, 'cash_register_table_table_pivot', 'cash_register_id', 'table_id');
    }
}
