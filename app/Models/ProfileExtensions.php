<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfileExtensions extends Model
{
    use HasFactory;
    use SoftDeletes;

    // nombre de la tabla
    protected $table = 'profile_extensions';

    protected $fillable = ['company_id', 'creator_id', 'name', 'description', 'rate', 'minutes_cap', 'minutes_increments', 'type_time_increment', 'outbound_per_minute_rate', 'inbound_per_minute_rate', 'extension_balance', 'minimum_extension_balance', 'status_payment','extensions_number','unmetered','status'];

    // para el softDelete
    public function aditionalCharges(): HasMany
    {
        return $this->hasMany(AdditionalCharges::class, 'profile_extension_id', 'id');
    }

    public function aditionalChargesA(): HasMany
    {
        return $this->hasMany(AdditionalCharges::class, 'profile_extension_id', 'id')->where("status", "=", 1);
    }

    // Filtro para buscador en la vista principal
    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('name')) {
            $query->WhereName($filters->get('name'));
        }

        if ($filters->get('extensions_number')) {
            $query->WhereExtensionsNumber($filters->get('extensions_number'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'name';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public function scopeWhereName($query, $name)
    {
        return $query->where('name', 'LIKE', '%'.$name.'%');
    }

    public function scopeWhereExtensionsNumber($query, $extensions_number)
    {
        return $query->where('extensions_number', 'LIKE', '%'.$extensions_number.'%');
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopeWhereSearch($query, $search)
    {
        foreach (explode(' ', $search) as $term) {
            $query->where(function ($query) use ($term) {
                $query->where('name', 'LIKE', '%'.$term.'%');
            });
        }
    }
}
