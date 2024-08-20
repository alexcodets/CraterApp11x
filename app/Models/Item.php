<?php

namespace Crater\Models;

use Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Item extends Model implements HasMedia
{
    use HasFactory;
    use softDeletes;
    use InteractsWithMedia;

    protected $guarded = ['id'];

    protected $appends = [
        'formattedCreatedAt',
        'unit_name',
        'picture',
    ];

    protected $fillable = [
        'allow_pos',
        'allow_taxes',
        'price',
        'name',
        'description',
        'allow_price',
        'no_taxable',
        'avalara_bool',
        'avalara_type',
        'avalara_service_type',
        'avalara_payment_type',
        'retentions_bool',
        'retentions_id',
        'company_id',
        'unit_id',
        'creator_id',
        'item_number',
        'status',
        'item_category_id',
        'avalara_sale_type',
        'avalara_discount_type',
        'avalara_tax_inclusion',
        'item_category_id'
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
        ];
    }

    public const AVALARA_PAYMENT_TYPES = [
        'TAXABLE_AMOUNT', 'LINES',
    ];

    public const AVALARA_PAYMENT_TYPE_TAXABLE = 'TAXABLE_AMOUNT';
    public const AVALARA_PAYMENT_TYPE_LINES = 'LINES';

    public function getPictureAttribute()
    {
        $picture = $this->getMedia('item_picture')->first();

        if ($picture) {
            return asset($picture->getUrl());
        }

        return 0;
    }

    public function getUnitNameAttribute()
    {

        if ($this->unit != null) {
            return $this->unit->name;
        } else {
            return "N/A";
        }
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\Crater\Models\User::class, 'creator_id');
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(Tax::class)
                    ->where('invoice_item_id', null)
                    ->where('estimate_item_id', null)
                    ->where('package_id', null)
                    ->where('package_item_id', null)
                    ->where('pbx_package_id', null)
                    ->where('pbx_package_item_id', null)
                    ->where('pbx_service_item_id', null);
    }

    public function taxesPBX(): HasMany
    {
        return $this->hasMany(Tax::class);
    }

    public function avalaraServiceType(): BelongsTo
    {
        return $this->belongsTo(AvalaraServiceType::class, 'avalara_service_type');
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function estimateItems(): HasMany
    {
        return $this->hasMany(EstimateItem::class);
    }

    public function itemGroups(): BelongsToMany
    {
        return $this->belongsToMany(ItemGroup::class, 'item_group_items', 'item_id', 'item_group_id')
            ->whereNull('item_group_items.deleted_at')
            ->withTimestamps()
            ->limit(1);
    }

    // relacion con la tabla avalara_configs Did hasMany
    public function avalaraConfigItemsDid(): HasMany
    {
        return $this->hasMany(AvalaraConfig::class, 'item_did_id', 'id');
    }

    // relacion con la tabla retentions HasMany
    public function retentions(): BelongsTo
    {
        return $this->belongsTo(Retentions::class);
    }

    // relacion con la tabla avalara_configs cdr hasMany
    public function avalaraConfigItemsCdr(): HasMany
    {
        return $this->hasMany(AvalaraConfig::class, 'item_cdr_id', 'id');
    }

    // relacion con la tabla avalara_configs extension hasMany
    public function avalaraConfigItemsExtension(): HasMany
    {
        return $this->hasMany(AvalaraConfig::class, 'item_extension_id', 'id');
    }

    public function itemCategories(): BelongsToMany
    {
        return $this->belongsToMany(itemCategories::class, 'items_item_categories', 'item_id', 'item_category_id');
    }

    public function itemCategory(): BelongsTo
    {
        return $this->belongsTo(itemCategories::class);
    }

    public function itemStore(): BelongsToMany
    {
        return $this->belongsToMany(Store::class);
    }

    public function itemSection(): BelongsToMany
    {
        return $this->belongsToMany(PosSection::class, 'item_section', 'item_id', 'section_id');
    }

    // relacion con la tabla avalara_configs international hasMany
    public function avalaraConfigItemsInternational(): HasMany
    {
        return $this->hasMany(AvalaraConfig::class, 'item_international_id', 'id');
    }

    public function getType($query, $transaction, int $service)
    {
        return $query->whereHas('avalaraServiceType', function (Builder $query2) use ($service, $transaction) {
            $query2->where('service_type', $service)->where('avalara_transaction_types', $transaction);
        });
    }

    public function scopeTheType($query, $transaction, int $service)
    {
        return $query->whereHas('avalaraServiceType', function (Builder $query2) use ($service, $transaction) {
            $query2->where('service_type', $service)->where('avalara_transaction_types', $transaction);
        });
    }

    public function scopeCdr($query)
    {
        return $this->getType($query, 19, 48);
    }

    public function scopeDid($query)
    {
        return $this->getType($query, '19', 21);
    }

    public function scopeExtension($query)
    {
        return $this->getType($query, '19', 41);
    }

    public function scopeWhereSearch($query, $search)
    {
        return $query->where('items.name', 'LIKE', '%'.$search.'%');
    }

    public function scopeWhereIsPos($query, $isPos)
    {
        return $query->where('items.allow_pos', $isPos);
    }

    public function scopeWherePrice($query, $price)
    {
        return $query->where('items.price', $price);
    }

    public function scopeWhereUnit($query, $unit_id)
    {
        return $query->where('items.unit_id', $unit_id);
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopeWhereCategoryItemsPos($query, $categories_id)
    {
        $query->join('items_item_categories', 'items.id', '=', 'items_item_categories.item_id')
        ->whereIn('items_item_categories.item_category_id', $categories_id);
    }

    public function scopeWhereItem($query, $item_id)
    {
        $query->orWhere('id', $item_id);
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('price')) {
            $query->wherePrice($filters->get('price'));
        }

        if ($filters->get('unit_id')) {
            $query->whereUnit($filters->get('unit_id'));
        }

        if ($filters->get('item_id')) {
            $query->whereItem($filters->get('item_id'));
        }
        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'name';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
        if ($filters->get('avalara_bool') == 'true') {
            $query->where('avalara_bool', true);
        }
        if ($filters->get('is_pos')) {
            $query->whereIsPos($filters->get('is_pos'));
        }

        if ($filters->get('categories_id')) {
            $query->whereCategoryItemsPos($filters->get('categories_id'));
        }

        /*if ($filters->get('avalara_bool') == 'false') {
            $query->where('avalara_bool', false);
        }*/
    }

    public function scopeApplyFiltersReport($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('units_id')) {
            if (preg_match('~[0-9]+~', $filters->get('units_id'))) {
                $filters->put('units_id', explode(',', $filters->get('units_id')));
                $query->whereIn('unit_id', $filters->get('units_id'));
            }
        }

        if ($filters->get('categories_id')) {
            if (preg_match('~[0-9]+~', $filters->get('categories_id'))) {
                $filters->put('categories_id', explode(',', $filters->get('categories_id')));
                $items_ids = DB::table('items_item_categories')
                                        ->whereIn('item_category_id', $filters->get('categories_id'))
                                        ->pluck('item_id');
                $query->whereIn('id', $items_ids);
            }
        }

        if ($filters->get('groups_id')) {
            if (preg_match('~[0-9]+~', $filters->get('groups_id'))) {
                $filters->put('groups_id', explode(',', $filters->get('groups_id')));
                $items_ids = DB::table('item_group_items')
                                        ->whereIn('item_group_id', $filters->get('groups_id'))
                                        ->pluck('item_id');
                $query->whereIn('id', $items_ids);
            }
        }

        if ($filters->get('item')) {
            if (preg_match('~[0-9]+~', $filters->get('item'))) {
                $filters->put('item', explode(',', $filters->get('item')));
                $query->whereIn('id', $filters->get('item'));
            }
        }

    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
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
        $query->where('items.company_id', $company_id);
    }

    public static function createItem($request)
    {
        $data = $request->validated();
        $data['company_id'] = $request->header('company');
        $data['creator_id'] = Auth::id();
        $data['allow_taxes'] = $request->allow_taxes === true;
        $data['no_taxable'] = $request->no_taxable === true;
        $data['avalara_bool'] = $request->avalara_bool;
        $data['avalara_type'] = $request->avalara_type;
        $data['avalara_service_type'] = $request->avalara_service_type;
        $data['item_category_id'] = $request->item_category_id;
        //
        $data['avalara_sale_type'] = $request->avalara_sale_type != null
                                     ? $request->avalara_sale_type['value']
                                     : "Retail";
        $data['avalara_discount_type'] = $request->avalara_discount_type != null
                                         ? $request->avalara_discount_type['value']
                                         : 0;
        //
        $data['avalara_payment_type'] = $request->avalara_payment_type;
        $data['retentions_bool'] = $request->retentions_bool;
        $data['retentions_id'] = $request->retentions_id;
        $data['tax_inclusion'] = $request->tax_inclusion == true ? 1 : 0;
        // $data['item_category_id'] = $request->item_category;
        $data['allow_pos'] = $request->allow_pos == true ? 1 : 0;


        $item = self::create($data);

        if ($request->has('taxes')) {
            foreach ($request->taxes as $tax) {
                $tax['company_id'] = $request->header('company');
                $item->taxes()->create($tax);
            }
        }

        if ($request->has('item_groups')) {
            self::createItemGroups($item, $request);
        }

        $item = self::with('taxes', 'unit')->find($item->id);
        $number = CompanySetting::getSetting('item_prefix', $request->header('company')) ?? 'ITM';

        $item->item_number = $number."-000".$item->id;
        // Se agrega esta propiedad directamente al modelo para su consulta en el front
        //$item->unit_name = isset($item->unit) ? $item->unit->name : '';
        $item->save();

        if(isset($request->item_categories[0])) {
            foreach ($request->item_categories as $category) {
                DB::table('items_item_categories')->insert([
                    'item_id' => $item->id,
                    'item_category_id' => $category['id'],
                    'created_at' => Carbon::now()->format('Y-m-d'),
                ]);
            }
        }

        return $item;
    }

    public function updateItem($request)
    {
        $result = [
            'success' => false,
            'message' => '',
            'data' => null,
        ];
        $data = $request->validated();
        $data['allow_taxes'] = $request->allow_taxes === true;
        $data['no_taxable'] = $request->no_taxable === true;
        $data['avalara_bool'] = $request->avalara_bool;
        $data['avalara_type'] = $request->avalara_type;
        $data['avalara_service_type'] = $request->avalara_service_type;
        $data['avalara_payment_type'] = $request->avalara_payment_type;
        $data['avalara_sale_type'] = $request->avalara_sale_type['value'];
        $data['avalara_discount_type'] = $request->avalara_discount_type['value'];
        $data['retentions_bool'] = $request->retentions_bool;
        $data['retentions_id'] = $request->retentions_id;
        $data['item_category_id'] = $request->item_category;
        $data['allow_pos'] = $request->allow_pos == true ? 1 : 0 ;
        $data['tax_inclusion'] = $request->tax_inclusion == true ? 1 : 0;
        $data['item_category_id'] = $request->item_category_id;

        // validar si avalara_bool cambia a false y tiene configuraciones asociadas
        if ($this->avalara_bool == true && $data['avalara_bool'] == false) {
            //validar si tiene estas relaciones avalaraConfigItemsDid, avalaraConfigItemsCdr, avalaraConfigItemsExtension, avalaraConfigItemsInternational
            if ($this->avalaraConfigItemsDid()->count() > 0 || $this->avalaraConfigItemsCdr()->count() > 0 || $this->avalaraConfigItemsExtension()->count() > 0 || $this->avalaraConfigItemsInternational()->count() > 0) {
                $result['message'] = 'You cannot change a false avalara option as it is being used in avalara';

                return $result;
            }
        }
        $this->update($data);
        $this->taxes()->delete();

        if ($request->has('taxes')) {
            foreach ($request->taxes as $tax) {
                $tax['company_id'] = $request->header('company');
                $this->taxes()->create($tax);
            }
        }

        // Eliminar los grupos asociados
        self::deleteItemGroups($this);

        if ($request->has('item_groups')) {
            // Asociar nuevos grupos
            self::createItemGroups($this, $request);
        }


        $result['success'] = true;
        $result['data'] = Item::with('taxes')->find($this->id);

        // delete all relationship items with item_categories
        DB::table('items_item_categories')->where('item_id', $this->id)->delete();

        // validate if exists array with data of item_categories
        if(isset($request->item_categories[0])) {
            foreach ($request->item_categories as $category) {
                // create of relationship item with item_categories
                DB::table('items_item_categories')->insert([
                    'item_id' => $this->id,
                    'item_category_id' => $category['id'],
                    'created_at' => Carbon::now()->format('Y-m-d'),
                ]);
            }
        }

        return $result;
    }

    public static function createItemGroups($item, $request)
    {
        foreach ($request->item_groups as $group) {

            $item->itemGroups()
                ->attach(
                    $group['item_group_id'],
                    [
                        'company_id' => $request->header('company'),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                );
        }
    }

    public static function deleteItemGroups($item)
    {
        foreach ($item->itemGroups as $group) {
            // validar si el item pertenece a la tabla avalara_configs
            Log::info('item: '.$group);


            // $item->itemGroups()->updateExistingPivot($group->id, ['deleted_at' => Carbon::now()]);
        }
    }
}
