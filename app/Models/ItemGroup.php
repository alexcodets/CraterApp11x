<?php

namespace Crater\Models;

use Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ItemGroup extends Model implements HasMedia
{
    use HasFactory;
    use softDeletes;
    use InteractsWithMedia;

    protected $guarded = [
        'id'
    ];

    protected $appends = [
        'formattedCreatedAt',
        'picture'
    ];

    protected $fillable = [
        'name', 'description','no_taxable','company_id', 'status', 'item_category_id', 'allow_pos'
    ];

    public function getFormattedCreatedAtAttribute($value)
    {
        $dateFormat = Cache::remember('carbon_date_format_'.$this->company_id, 60 * 5, function () {
            return CompanySetting::getSetting('carbon_date_format', $this->company_id);
        });

        return Carbon::parse($this->created_at)->format($dateFormat);
    }

    public function getPictureAttribute()
    {
        $picture = $this->getMedia('item_group_picture')->first();

        if ($picture) {
            return asset($picture->getUrl());
        }

        return 0;
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_group_items', 'item_group_id', 'item_id')
            ->whereNull('item_group_items.deleted_at')

            ->withTimestamps();
    }

    public function itemCategories(): BelongsToMany
    {
        return $this->belongsToMany(itemCategories::class, 'item_groups_item_categories', 'item_group_id', 'item_category_id');
    }

    public function scopeWhereSearch($query, $search)
    {
        foreach (explode(' ', $search) as $term) {
            $query->where(function ($query) use ($term) {
                $query->where('name', 'LIKE', '%'.$term.'%')
                    ->orWhere('description', 'LIKE', '%'.$term.'%');
            });
        }
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('item_groups.company_id', $company_id);
    }

    public function scopeWhereName($query, $name)
    {
        return $query->where('name', 'LIKE', '%'.$name.'%');
    }

    public function scopeWhereDescription($query, $description)
    {
        return $query->where('description', 'LIKE', '%'.$description.'%');
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('name')) {
            $query->whereName($filters->get('name'));
        }

        if ($filters->get('description')) {
            $query->whereDescription($filters->get('description'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'name';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public static function createItemGroup($request)
    {
        $itemGroup = self::create([
            'name' => $request->name,
            'description' => $request->description,
            'no_taxable' => $request->no_taxable,
            'company_id' => $request->header('company'),
            //'item_category_id' => $request->item_category,
            'allow_pos' => $request->allow_pos == true ? 1 : 0
        ]);

        self::createItems($itemGroup, $request);

        // item_groups_item_categories
        if(count($request->item_categories) > 0) {
            foreach ($request->item_categories as $category_id) {
                \DB::table('item_groups_item_categories')->insert([
                    'item_group_id' => $itemGroup->id,
                    'item_category_id' => $category_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

            }
        }

        return $itemGroup;
    }

    public static function updateItemGroup($request, $itemGroup)
    {
        $itemGroup->update([
            'name' => $request->name,
            'description' => $request->description,
            'no_taxable' => $request->no_taxable,
            'company_id' => $request->header('company'),
            //'item_category_id' => $request->item_category,
            'allow_pos' => $request->allow_pos == true ? 1 : 0
        ]);

        \DB::table('item_groups_item_categories')->where('item_group_id', $itemGroup->id)->delete();

        // item_groups_item_categories
        if(count($request->item_categories) > 0) {
            foreach ($request->item_categories as $category_id) {
                \DB::table('item_groups_item_categories')->insert([
                    'item_group_id' => $itemGroup->id,
                    'item_category_id' => $category_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

            }
        }

        // Eliminar los items asociados
        self::deleteItems($itemGroup);

        // Asociar nuevos items
        self::createItems($itemGroup, $request);

        return $itemGroup;
    }

    public static function deleteItemGroup($ids)
    {
        foreach ($ids as $id) {
            $itemGroup = self::find($id);

            LogsModule::createLog(
                "Item Groups",
                "delete",
                "admin/item-groups/delete",
                $id,
                Auth::user()->name,
                Auth::user()->email,
                Auth::user()->role,
                Auth::user()->company_id,
                "ItemGroup: ".$itemGroup->name
            );

            self::deleteItems($itemGroup); // Eliminar los items asociados

            $itemGroup->delete(); // Eliminar grupo de items
        }

        return true;
    }

    public static function createItems($itemGroup, $request)
    {
        foreach ($request->items as $item) {
            $itemGroup->items()
                ->attach(
                    $item['item_id'],
                    [
                        'company_id' => $request->header('company'),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]
                );
        }
    }

    public static function deleteItems($itemGroup)
    {
        foreach ($itemGroup->items as $item) {
            $itemGroup->items()->updateExistingPivot($item->id, ['deleted_at' => Carbon::now()]);
        }
    }
}
