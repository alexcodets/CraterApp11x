<?php

namespace Crater\Models;

use Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PbxCategorie extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'company_id', 'description'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    public function did(): HasMany
    {
        return $this->hasMany(ProfileDID::class);
    }

    public function getFormattedCreatedAtAttribute($value)
    {
        $dateFormat = Cache::remember('carbon_date_format_'.$this->company_id, 60 * 5, function () {
            return CompanySetting::getSetting('carbon_date_format', $this->company_id);
        });

        return Carbon::parse($this->created_at)->format($dateFormat);
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('company_id', $company_id);
    }

    public function scopeWhereCategory($query, $category_id)
    {
        $query->orWhere('id', $category_id);
    }

    public function scopeWhereSearch($query, $search)
    {
        $query->where('name', 'LIKE', '%'.$search.'%');
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('category_id')) {
            $query->whereCategory($filters->get('category_id'));
        }

        if ($filters->get('company_id')) {
            $query->whereCompany($filters->get('company_id'));
        }

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }
}
