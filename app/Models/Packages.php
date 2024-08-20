<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Packages extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'package_number',
        'name',
        'html',
        'packages_discount',
        'discount_general_type',
        'discount_general',
        'text',
        'qty',
        'module_id',
        'client_qty',
        'taxable',
        'single_term',
        'status',
        'company_id',
        'prorata_day',
        'prorata_cuoff',
        'value_discount',
        'discounts',
        'status_payment'
    ];

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('company_id', $company_id);
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('name')) {
            $query->whereName($filters->get('name'));
        }

        if ($filters->get('package_number')) {
            $query->whereNumber($filters->get('package_number'));
        }

        if ($filters->get('status_payment')) {
            $query->whereStatusPayment($filters->get('status_payment'));
        }

        if ($filters->get('module')) {
            $query->whereModule($filters->get('module'));
        }

        if ($filters->get('qty')) {
            $query->whereQty($filters->get('qty'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {

            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'id';

            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';

            $query->whereOrder($field, $orderBy);
        }
    }

    public function scopeWhereName($query, $name)
    {
        return $query->where('name', 'LIKE', '%'.$name.'%');
    }

    public function scopeWhereNumber($query, $number)
    {
        return $query->where('package_number', 'LIKE', '%'.$number.'%');
    }

    public function scopeWhereStatusPayment($query, $status)
    {
        return $query->where('status_payment', 'LIKE', '%'.$status.'%');
    }

    public function scopeWhereModule($query, $module)
    {
        return $query->orWhere('module_id', $module);
    }

    public function scopeWhereQty($query, $qty)
    {
        return $query->orWhere('qty', $qty);
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        return $query->orderBy($orderByField, $orderBy);
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public function packageNames(): HasMany
    {
        return $this->hasMany(PackageNames::class, 'package_id', 'id');
    }

    public function packageDescriptions(): HasMany
    {
        return $this->hasMany(PackageDescriptions::class, 'package_id', 'id');
    }

    public function taxTypes(): BelongsToMany
    {
        return $this->belongsToMany(TaxType::class, 'package_tax_types', 'package_id', 'tax_types_id')
        ->withPivot('id', 'status');
    }

    public function packagesTaxTypes(): HasMany
    {
        return $this->hasMany(PackageTaxTypes::class, 'package_id', 'id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PackageItems::class, 'package_id', 'id');
    }

    public function itemGroups(): HasMany
    {
        return $this->hasMany(PackageItemGroups::class, 'package_id', 'id');
    }

    ////////////////// SERVICE //////////////////

    public function Service(): HasOne
    {
        return $this->hasOne(CustomerPackage::class, 'package_id');
    }

    public function Services(): HasMany
    {
        return $this->hasMany(CustomerPackage::class, 'package_id');
    }

    //
}
