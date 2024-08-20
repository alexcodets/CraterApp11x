<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CustomSearch extends Model
{
    use HasFactory;

    // fillable
    protected $fillable = [
        'name',
        'description',
        'pbx_tenant_id',
        'company_id',
        'created_id',
    ];

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        // if ($filters->get('name')) {
        //     $query->whereName($filters->get('name'));
        // }

        // if ($filters->get('description')) {
        //     $query->whereDescription($filters->get('description'));
        // }

        // if ($filters->get('company_name')) {
        //     $query->whereCompanyName($filters->get('company_name'));
        // }

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

    // relations
    public function pbxTenant(): BelongsTo
    {
        return $this->belongsTo(PbxTenant::class);
    }

    public function pbxExtension(): BelongsToMany
    {
        return $this->belongsToMany(PbxExtensions::class, 'pbx_extension_custom_searches', 'custom_search_id', 'pbx_extension_id');
    }

    public function createUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_id');
    }
}
