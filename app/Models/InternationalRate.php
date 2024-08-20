<?php

namespace Crater\Models;

use Crater\Traits\ModelPagination;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternationalRate extends Model
{
    use HasFactory;
    use ModelPagination;

    protected $table = 'international_rate';

    protected $fillable = ['company_id', 'country_id', 'creator_id', 'status', 'rate_per_minute', 'prefix'];

    public const CATEGORY_CUSTOM = 'C';
    public const CATEGORY_INTERNATIONAL = 'I';
    public const CATEGORY_TOLL_FREE = 'T';

    public function scopeOrderByLength($query, $order = 'desc')
    {
        return $query->orderByRaw('CHAR_LENGTH(prefix) desc');
    }

    // Filters
    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('name')) {
            $query->WhereName($filters->get('name'));
        }

        if ($filters->get('category')) {
            $query->WhereCategory($filters->get('category'));
        }

        if ($filters->get('country_id')) {
            $query->WhereCountry($filters->get('country_id'));
        }

        if ($filters->get('prefix_type') == 'P') {
            $query->WherePrefixType($filters->get('prefix_type'));
            if ($filters->get('prefix')) {
                $query->WherePrefix($filters->get('prefix'));
            }
        }

        if ($filters->get('prefix_type') == 'FT') {
            $query->WherePrefixType($filters->get('prefix_type'));
            if ($filters->get('from')) {
                $query->WhereFrom($filters->get('from'));
            }
            if ($filters->get('to')) {
                $query->WhereTo($filters->get('to'));
            }
        }
    }

    // Filter by Category
    public function scopeWhereCategory($query, $category)
    {
        return $query->where('category', 'LIKE', '%'.$category.'%');
    }

    // Filter by Name
    public function scopeWhereName($query, $name)
    {
        return $query->where('name', 'LIKE', '%'.$name.'%');
    }

    // Filter by Country
    public function scopeWhereCountry($query, $country_id)
    {
        return $query->where('country_id', 'LIKE', '%'.$country_id.'%');
    }

    // <Filter by PrefixType == 'P' => By prefix // PrefixType == 'FT' => By From/To
    public function scopeWherePrefixType($query, $prefix_type)
    {
        return $query->where('typecustom', 'LIKE', '%'.$prefix_type.'%');
    }

    // PrefixType == 'P'
    public function scopeWherePrefix($query, $prefix)
    {
        return $query->where('prefix', 'LIKE', '%'.$prefix.'%');
    }

    // PrefixType == 'FT'
    public function scopeWhereFrom($query, $from)
    {
        return $query->where('from', 'LIKE', '%'.$from.'%');
    }

    public function scopeWhereTo($query, $to)
    {
        return $query->where('to', 'LIKE', '%'.$to.'%');
    }
    // />

    /*
    public function prefixrate_groups()
    {
        return $this->belongsToMany(PrefixRateGroups::class);
    }
    */


}
