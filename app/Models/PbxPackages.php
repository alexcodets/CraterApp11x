<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class PbxPackages extends Model
{
    use HasFactory;
    use SoftDeletes;

    // nombre de la tabla
    public const STATUS = [8, 4, 2, 1];

    protected $table = 'pbx_packages';

    protected $fillable = [
        'company_id', 'creator_id', 'taxes_id', 'package_tax_groups_id',
        'item_group_id', 'pbx_server_id', 'pbx_package_name', 'html',
        'text', 'status', 'qty_available', 'client_limit', 'client_limit',
        'type_time_increment', 'did', 'extensions', 'call_ratings', 'package_discount',
        'type', 'value_discount', 'discount', 'modify_server', 'rate', 'national_dialing_code',
        'international_dialing_code', 'inclusive_minutes', 'status_payment', 'minutes_increments',
        'rate_per_minutes', 'packages_number', 'unmetered', 'discount_term_type', 'discount_start_date',
        'discount_end_date',
        //'prefixrate_groups_id', 'prefixrate_groups_outbound_id',
        'discount_time_units',
        'avalara_options',
        'avalara_price',
        'avalara_extension',
        'avalara_did',
        'avalara_callrating',
        'avalara_items',
        'discount_term',
        'template_did_id',
        'template_extension_id',
        'automatic_suspension',
        'suspension_type',
        'all_cdrs',
        'custom_app_rate_id',
        'avalara_custom_app_rate_item_id',
        'avalara_extension_item_id',
        'avalara_did_item_id',
        'cdr_items_id',
        'custom_destinations_item_id',
        'inter_custom_destinations_item_id',
        'toll_free_custom_destinations_item_id',
        'avalara_custom_app_rate_items',
        'avalara_services_price_item_id',
        'avalara_additional_charges_item_id',
        'avalara_services_price_item',
        'avalara_additional_charges_item',
        'avalaraBundle',
        'bundleTransaction',
        'bundleService',
        'apply_tax_type',
        'update_child_services',
    ];

    // para el softDelete
    public function cdrStatus(): HasMany
    {
        return $this->hasMany(PbxPackagesCdrStatus::class);
    }

    public function taxTypes(): BelongsToMany
    {
        return $this->belongsToMany(TaxType::class, 'pbx_package_tax_types', 'pbx_package_id', 'tax_types_id')
            ->withPivot('id', 'status');
    }

    public function taxTypesCdr(): BelongsToMany
    {
        // pbxPackageTaxTypesCdr
        return $this->belongsToMany(TaxType::class, 'pbx_package_tax_types_cdrs', 'pbx_package_id', 'tax_types_id')
            ->withPivot('id', 'status');
    }

    public function customAppRate(): BelongsTo
    {
        return $this->belongsTo(customAppRate::class, 'custom_app_rate_id');
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(PbxServers::class, 'pbx_server_id', 'id');
    }

    public function profileDid(): BelongsTo
    {
        return $this->belongsTo(ProfileDID::class, 'template_did_id', 'id');
    }

    public function profileDidWithCharges(): BelongsToMany
    {
        return $this->belongsToMany(ProfileDID::class, 'aditional_charges', 'profile_did_id', 'profile_did_id')
            ->withPivot('id', 'amount');
    }

    public function profileExtensions(): BelongsTo
    {
        return $this->belongsTo(ProfileExtensions::class, 'template_extension_id', 'id');
    }

    public function pbxPackagesTaxTypes(): HasMany
    {
        return $this->hasMany(PbxPackageTaxTypes::class, 'pbx_package_id', 'id');
    }

    public function items(): BelongsToMany
    {
        // return $this->belongsToMany(Item::class, 'pbx_package_items', 'pbx_package_id', 'items_id')
        // ->withPivot('id', 'items_id', 'item_group_id', 'price', 'total', 'quantity', 'discount_type', 'discount', 'discount_val', 'tax', 'description');
        // use PbxPackageItem::class
        return $this->hasMany(PbxPackageItem::class, 'pbx_package_id', 'id');
    }

    public function itemGroups(): HasMany
    {
        return $this->hasMany(PbxPackageItemGroup::class, 'pbx_package_id', 'id');
    }

    public function avalaraDid(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'avalara_did_item_id');
    }

    public function avalaraCdr(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'cdr_items_id');
    }

    public function avalaraExtension(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'avalara_extension_item_id');
    }

    public function avalaraInternational(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'inter_custom_destinations_item_id');
    }

    public function avalaraCustomDestination(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'custom_destinations_item_id');
    }

    public function avalaraTollFree(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'custom_destinations_item_id');
    }

    public function avalaraServicesPrice(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'avalara_services_price_item_id');
    }

    public function avalaraAdditionalCharges(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'avalara_additional_charges_item_id');
    }

    public function avalaraAppRate(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'avalara_custom_app_rate_item_id');
    }

    public function pbxServer(): BelongsTo
    {
        return $this->belongsTo(PbxServers::class);
    }

    public function pbxService(): HasOne
    {
        return $this->hasOne(PbxServices::class, 'pbx_package_id');
    }

    // pbxServices
    public function pbxServices(): HasMany
    {
        return $this->hasMany(PbxServices::class, 'pbx_package_id');
    }

    public function profileExtension(): BelongsTo
    {
        return $this->belongsTo(ProfileExtensions::class, 'template_extension_id');
    }

    public function profileDID2(): BelongsTo
    {
        return $this->belongsTo(ProfileDID::class, 'template_did_id');
    }

    public function prefixrateGroup(): BelongsTo
    {
        return $this->belongsTo(PrefixRateGroups::class, 'prefixrate_groups_id');
    }

    public function prefixrateGroupOutbound(): BelongsTo
    {
        return $this->belongsTo(PrefixRateGroups::class, 'prefixrate_groups_outbound_id');
    }

    // Filtro para buscador en la vista principal
    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('pbx_package_name')) {
            $query->WherePBXPackageName($filters->get('pbx_package_name'));
        }

        if ($filters->get('packages_number')) {
            $query->WherePackagesNumber($filters->get('packages_number'));
        }

        if ($filters->get('status_payment')) {
            $query->WherePBXType($filters->get('status_payment'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'name';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public function scopeWherePBXPackageName($query, $pbx_package_name)
    {
        return $query->where('pbx_package_name', 'LIKE', '%'.$pbx_package_name.'%');
    }

    public function scopeWherePBXType($query, $pbx_package_type)
    {
        return $query->where('status_payment', 'LIKE', '%'.$pbx_package_type.'%');
    }

    public function scopeWherePackagesNumber($query, $packages_number)
    {
        return $query->where('packages_number', 'LIKE', '%'.$packages_number.'%');
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopeWhereSearch($query, $search)
    {
        foreach (explode(' ', $search) as $term) {
            $query->where(function ($query) use ($term) {
                $query->where('pbx_package_name', 'LIKE', '%'.$term.'%');
            });
        }
    }

    public function getCallRateAttribute()
    {
        return $this->rate_per_minutes / 100;
    }

    public function customDidGroupultra()
    {
        return $this->id;
    }

    public function getStatusArrayAttribute(): \Illuminate\Support\Collection
    {
        $base = $this->cdrStatus(['id', 'pbx_packages_id', 'status'])->pluck('status');
        if ($base->contains(0)) {
            return collect(self::STATUS);
        }

        return $base;
    }
}
