<?php

namespace Crater\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfileDID extends Model
{
    use HasFactory;
    use SoftDeletes;

    // nombre de la tabla
    protected $table = 'profile_did';

    protected $fillable = [
        'company_id', 'creator_id',
        'name', 'description', 'status',
        'did_rate', 'toll_free_did_rate',
        'international_did_rate',
        'international_inbound_per_minute_rate',
        'inbound_per_minute_rate',
        'inbound_per_minute_rate_value',
        'emergency_services_rate',
        'emergency_services_rate_value',
        'emergency_services_address',
        'emergency_services_city',
        'emergency_services_state',
        'emergency_services_zip',
        'cnam_rate',
        'cnam_name',
        'cnam_price',
        'did_number',
        'unmetered'
    ];

    // para el softDelete
    public function category(): BelongsTo
    {
        return $this->belongsTo(PbxCategorie::class, 'toll_free_category_id');
    }

    public function aditionalCharges(): HasMany
    {
        return $this->hasMany(AditionalCharges::class, 'profile_did_id', 'id');
    }

    public function aditionalChargesA(): HasMany
    {
        return $this->hasMany(AditionalCharges::class, 'profile_did_id', 'id')->where("status", "=", 1);
    }

    public function itemGroups(): BelongsToMany
    {
        return $this->belongsToMany(CustomDidGroup::class, 'profile_did_custom_did_groups', 'profile_did_id', 'custom_did_group_id')
            ->whereNull('profile_did_custom_did_groups.deleted_at')
            ->withTimestamps();
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

        if ($filters->get('did_number')) {
            $query->WhereDIDNumber($filters->get('did_number'));
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

    public function scopeWhereDIDNumber($query, $did_number)
    {
        return $query->where('did_number', 'LIKE', '%'.$did_number.'%');
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

    public static function createItemGroups($item, $request)
    {
        foreach ($request->item_groups as $group) {
            $item->itemGroups()
                ->attach(
                    $group['custom_did_group_id'],
                    [
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                );
        }
    }

    public static function deleteItemGroups($item)
    {
        foreach ($item->itemGroups as $group) {
            $item->itemGroups()->updateExistingPivot($group->id, ['deleted_at' => Carbon::now()]);
        }
    }
}
