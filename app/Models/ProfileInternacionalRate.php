<?php

namespace Crater\Models;

use Auth;
use Carbon\Carbon;
use Crater\Traits\ModelPagination;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ProfileInternacionalRate extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ModelPagination;

    protected $appends = [
        'countryName',
        'FormattedPrefixGroups',
    ];

    protected $fillable = ['prefix', 'name', 'country_id', 'status', 'category', 'rate_per_minute', 'typecustom', 'from', 'to', 'company_id', 'creator_id'];

    protected $table = "international_rate";

    public function getCountryNameAttribute($value)
    {

        if ($this->country != null) {
            return $this->country->name;
        } else {
            return "N/A";
        }
    }

    public function scopeWherePrefixId($query, $prefix_id)
    {
        $query->orWhere('international_rate.id', $prefix_id);
    }

    public function scopeWhereCountryName($query, $country_name)
    {
        $query->whereHas('country', function ($q) use ($country_name) {
            $q->where('countries.name', 'like', '%'.$country_name.'%');
        });
    }

    public function scopeApplyFilters($query, array $filters)
    {

        $filters = collect($filters);

        if ($filters->get('prefix')) {
            $query->wherePrefix($filters->get('prefix'));
        }

        if ($filters->get('name')) {
            $query->whereName($filters->get('name'));
        }

        if ($filters->get('country_id')) {
            $query->whereCountry($filters->get('country_id'));
        }

        if ($filters->get('prefix_id')) {
            $query->wherePrefixId($filters->get('prefix_id'));
        }

        if ($filters->get('country_name')) {
            $query->whereCountryName($filters->get('country_name'));
        }

        //
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
        //

        /*
        if ($filters->get('prefixrate_groups_id')) {
        $query->whereCustom($filters->get('prefixrate_groups_id'));
        }
         */

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'note';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    /*
    public function scopeWherePrefix($query, $prefix)
    {
    return $query->where('international_rate.prefix', "like", "%" . $prefix . "%");
    }*/

    public function scopeWhereName($query, $name)
    {
        return $query->where('international_rate.name', "like", "%".$name."%");
    }

    /* Buscador Country */
    public function scopeWhereCountry($query, $country_id)
    {
        if ($country_id == 999999) {
            return $query->where('international_rate.country_id', null);
        } else {
            return $query->where('international_rate.country_id', $country_id);
        }
    }

    // <Filter by PrefixType == 'P' => By prefix // PrefixType == 'FT' => By From/To
    public function scopeWherePrefixType($query, $prefix_type)
    {
        return $query->where('international_rate.typecustom', 'LIKE', '%'.$prefix_type.'%');
    }

    // PrefixType == 'P'
    public function scopeWherePrefix($query, $prefix)
    {
        return $query->where('international_rate.prefix', 'LIKE', '%'.$prefix.'%');
    }

    // PrefixType == 'FT'
    public function scopeWhereFrom($query, $from)
    {
        return $query->where('international_rate.from', 'LIKE', '%'.$from.'%');
    }

    public function scopeWhereTo($query, $to)
    {
        return $query->where('international_rate.to', 'LIKE', '%'.$to.'%');
    }
    // />

    // Buscador por orden
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

    public static function validarCategory($number)
    {
        if ($number == 1) {
            return "C";
        } elseif ($number == 2) {
            return "T";
        }

        return "I";
    }

    public static function createRate($request)
    {
        $custom_destinations = [];
        if ($request->has('listCsv')) {
            if (count($request->listCsv) > 0) {
                foreach ($request->listCsv as $item) {

                    $data['typecustom'] = $item[0];
                    $data['prefix'] = $item[1];
                    $data['from'] = $item[2];
                    $data['to'] = $item[3];
                    $data['name'] = $item[4];
                    $data['rate_per_minute'] = $item[5];
                    $data['category'] = $item[6];
                    $data['country_id'] = self::getCountryIdByCode($item[7]);

                    //\Log::debug($data['category'] = $item[6]);

                    /* Carga Prefix */
                    // if ($request->prefijo === "prefix") {
                    //     $data['prefix'] = $item[0];
                    // }
                    // if ($request->prefijo === "name") {
                    //     $data['prefix'] = $item[1];
                    // }
                    // if ($request->prefijo === "rate_per_minute") {
                    //     $data['prefix'] = $item[2];
                    // }
                    // if ($request->prefijo === "category") {
                    //     $data['prefix'] = $item[3];
                    // }

                    // /* Carga Name */
                    // if ($request->name === "prefix") {
                    //     $data['name'] = $item[0];
                    // }
                    // if ($request->name === "name") {
                    //     $data['name'] = $item[1];
                    // }
                    // if ($request->name === "rate_per_minute") {
                    //     $data['name'] = $item[2];
                    // }
                    // if ($request->name === "category") {
                    //     $data['prefix'] = $item[3];
                    // }
                    // /* Carga rate_per_minutes */
                    // if ($request->rate_per_minutes === "prefix") {
                    //     $data['rate_per_minute'] = $item[0];
                    // }
                    // if ($request->rate_per_minutes === "name") {
                    //     $data['rate_per_minute'] = $item[1];
                    // }
                    // if ($request->rate_per_minutes === "rate_per_minute") {
                    //     $data['rate_per_minute'] = $item[2];
                    // }
                    // if ($request->rate_per_minutes === "category") {
                    //     $data['rate_per_minute'] = $item[3];
                    // }
                    // /* Carga category */
                    // if ($request->category === "prefix") {
                    //     $data['category'] = self::validarCategory($item[0]);
                    // }
                    // if ($request->category === "name") {
                    //     $data['category'] = self::validarCategory($item[1]);
                    // }
                    // if ($request->category === "rate_per_minute") {
                    //     $data['category'] = self::validarCategory($item[2]);
                    // }
                    // if ($request->category === "category") {
                    //     $data['category'] = self::validarCategory($item[3]);
                    // }
                    $data['status'] = $request->status;
                    /*    $data['prefixrate_groups_id'] = $request->prefixrate_groups_id; */
                    $data['company_id'] = Auth::user()->company_id;
                    $data['creator_id'] = Auth::user()->id;

                    array_push($custom_destinations, $data);

                    /*$internacional = ProfileInternacionalRate::create($data);

                if ($request->has('prefixrate_groups_id')) {
                self::createPrefixGroups($internacional, $request);
                }*/
                }

                if (ProfileInternacionalRate::insert($custom_destinations)) {
                    $lastId = ProfileInternacionalRate::orderByDesc('id')->first()->id;
                    $ids = range($lastId - count($custom_destinations) + 1, $lastId);
                    self::assignCustomDestinationsByImport($request, $ids);

                    return response()->json([
                        'success' => true,
                    ]);
                }

                return false;
            }

            return false;
        } else {
            if ($request->has('multiple')) {
                foreach ($request->multiple as $prefixe) {
                    $data = [];
                    $data['prefix'] = $prefixe['prefijo'];
                    $data['name'] = $prefixe['name'];
                    $data['rate_per_minute'] = $prefixe['rate_per_minutes'];
                    $data['typecustom'] = $prefixe['typecustom'];
                    $data['from'] = $prefixe['from'];
                    $data['to'] = $prefixe['to'];
                    /* $data['prefixrate_groups_id']=$request->prefixrate_groups_id; */
                    $data['country_id'] = $request->country_id;
                    $data['status'] = $request->status;
                    $data['category'] = $request->category;
                    $data['company_id'] = Auth::user()->company_id;
                    $data['creator_id'] = Auth::user()->id;
                    $internacional = ProfileInternacionalRate::create($data);
                    if ($request->has('prefixrate_groups_id')) {
                        self::createPrefixGroups($internacional, $request);
                    }
                }
            } else {
                $data = [];
                $data['prefix'] = $request->prefijo;
                $data['name'] = $request->name;
                $data['rate_per_minute'] = $request->rate_per_minutes;
                $data['typecustom'] = $request->typecustom;
                $data['from'] = $request->from;
                $data['to'] = $request->to;
                /* $data['prefixrate_groups_id']=$request->prefixrate_groups_id; */
                $data['country_id'] = $request->country_id;
                $data['status'] = $request->status;
                $data['category'] = $request->category;
                $data['company_id'] = Auth::user()->company_id;
                $data['creator_id'] = Auth::user()->id;
                $internacional = ProfileInternacionalRate::create($data);
                if ($request->has('prefixrate_groups_id')) {
                    self::createPrefixGroups($internacional, $request);
                }
            }

            return $internacional;
        }
    }

    public static function getCountryIdByCode($code)
    {
        if ($code) {
            $country_id = DB::table('countries')->where('code', $code)->pluck('id');

            return count($country_id) > 0 ? $country_id[0] : null;
        } else {
            return null;
        }
    }

    public static function assignCustomDestinationsByImport($request, $dest_ids)
    {
        if ($request->type_group == 'existing_group') {
            $custom_group = PrefixRateGroups::find($request->prefixrate_groups_id[0]['id']);
        } else {
            $custom_group = PrefixRateGroups::create([
                'name' => $request->prefix_group_name,
                'type' => $request->prefix_group_type['value'],
                'company_id' => $request->header('company'),
            ]);
        }

        foreach ($dest_ids as $dest) {
            $custom_group->customDestinations()
                ->attach(
                    $dest,
                    [
                        'company_id' => $request->header('company'),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                );
        }
    }

    public static function createPrefixGroups($item, $request)
    {

        \Log::debug("---------------------------------------------");

        \Log::debug("entro en if 2");
        \Log::debug($item);
        \Log::debug($request->all());
        $idscustomgroup = PrefixGroup::all()->pluck('id')->toArray();
        if ($request->prefixrate_groups_id != null) {
            foreach ($idscustomgroup as $idgroup) {

                $item->ratePrefixGroups()->updateExistingPivot($idgroup, ['deleted_at' => Carbon::now()]);
            }

            foreach ($request->prefixrate_groups_id as $group) {

                $custom_group = PrefixGroup::find($group['id']);
                $order = 0;

                if (isset($request['id'])) {

                    $ids = $custom_group->PrefixGroup()->get()->pluck('pivot.int_rate_id')->toArray();
                    if ($custom_group) {
                        $orders = $custom_group->PrefixGroup()->get()->pluck('pivot.order')->toArray();
                        // validar si el array contiene elementos
                        if (count($orders) > 0) {
                            $order = max($orders) + 1;
                        }
                    }

                    $custom_group->PrefixGroup()->attach(
                        $item->id,
                        [
                            'order' => $order,
                            'company_id' => $request->header('company'),
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]
                    );

                } else {

                    if ($custom_group) {
                        $orders = $custom_group->PrefixGroup()->get()->pluck('pivot.order')->toArray();
                        $order = max($orders) + 1;
                    }
                    $item->ratePrefixGroups()
                        ->attach(
                            $group['id'],
                            [
                                'order' => $order,
                                'company_id' => $request->header('company'),
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ]
                        );
                }
            }

        }

    }

    public static function deletePrefixGroups($item)
    {
        foreach ($item->ratePrefixGroups as $group) {
            $item->ratePrefixGroups()->updateExistingPivot($group->id, ['deleted_at' => Carbon::now()]);
        }
    }

    public function ratePrefixGroups(): BelongsToMany
    {
        return $this->belongsToMany(PrefixRateGroups::class, 'international_rate_prefixrate_group', 'int_rate_id', 'prefixrate_id')
            ->whereNull('international_rate_prefixrate_group.deleted_at')
            ->withTimestamps();
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function getFormattedPrefixGroupsAttribute($value)
    {

        $int_rate_ids = DB::table('international_rate_prefixrate_group')
            ->where('int_rate_id', $this->id)
            ->where('deleted_at', null)
            ->pluck('prefixrate_id');

        if (count($int_rate_ids) != 0) {
            $listgruops = PrefixRateGroups::whereIN("id", $int_rate_ids)->pluck("name")->toarray();
            $listname = "";

            foreach ($listgruops as $name) {
                $listname = $listname."".$name;
                $listname = $listname."<br />";
            }

            return $listname;
        }

        return "N/A";
    }
}
