<?php

namespace Crater\Models;

use Cache;
use Carbon\Carbon;
use Crater\Traits\ModelPagination;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class PrefixGroup extends Model
{
    use HasFactory;
    use softDeletes;
    use ModelPagination;

    protected $table = 'prefixrate_groups';

    protected $guarded = [
        'id'
    ];

    protected $appends = [
        'formattedCreatedAt',
    ];

    protected $fillable = [
        'company_id', 'name', 'description', 'status', 'type'
    ];

    public function getFormattedCreatedAtAttribute($value)
    {
        $dateFormat = Cache::remember('carbon_date_format_'.$this->company_id, 60 * 5, function () {
            return CompanySetting::getSetting('carbon_date_format', $this->company_id);
        });

        return Carbon::parse($this->created_at)->format($dateFormat);
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
        $query->where('company_id', $company_id);
    }

    public function scopeWhereName($query, $name)
    {
        return $query->where('name', 'LIKE', '%'.$name.'%');
    }

    public function scopeWhereDescription($query, $description)
    {
        return $query->where('description', 'LIKE', '%'.$description.'%');
    }

    public function scopeWhereType($query, $type)
    {
        return $query->where('type', 'LIKE', '%'.$type.'%');
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
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

        if ($filters->get('type')) {
            $query->whereType($filters->get('type'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'name';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public function prefixes(): HasMany
    {
        return $this->hasMany(ProfileInternacionalRate::class, 'prefixrate_groups_id');
    }

    public static function createPrefixGroup($request)
    {
        $prefixGroup = self::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request['status']['value'],
            'type' => $request['type']['value'],
            'company_id' => $request->header('company')
        ]);
        self::createInternacionalPrefix($prefixGroup, $request);
        /* self::associatePrefixes($prefixGroup, $request); */

        return $prefixGroup;
    }

    public static function updatePrefixGroup($request, $prefixGroup)
    {
        $prefixGroup->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request['status']['value'],
            'type' => $request['type']['value'],
            'company_id' => $request->header('company'),
        ]);

        /* // Liberar los prefijos asociados
        self::disassociatePrefixes($prefixGroup);

        // Asociar nuevos nuevos prefijos
        self::associatePrefixes($prefixGroup, $request); */

        // self::deleteInternacionalRate($prefixGroup);

        // self::createInternacionalPrefix($prefixGroup, $request);

        // updateInternacionalRate
        //self::updateInternacionalRate($prefixGroup, $request);

        return $prefixGroup;
    }

    public static function deletePrefixGroup($ids)
    {
        foreach ($ids as $id) {
            $prefixGroup = self::find($id);

            LogsModule::createLog(
                "Prefixes Groups",
                "delete",
                "/admin/corePBX/billing-templates/prefix-groups/delete",
                $id,
                Auth::user()->name,
                Auth::user()->email,
                Auth::user()->role,
                Auth::user()->company_id,
                "PrefixGroup: ".$prefixGroup->name
            );

            self::deleteInternacionalRate($prefixGroup); // Eliminar toll free did asociados
            //self::disassociatePrefixes($prefixGroup);  Liberar los prefijos asociados

            $prefixGroup->delete(); // Eliminar grupo de prefijos
        }

        return true;
    }

    public static function associatePrefixes($prefixGroup, $request)
    {
        foreach ($request->prefixes as $prefix) {
            $prefix = ProfileInternacionalRate::find($prefix['prefix_id']);
            $prefix->prefixrate_groups_id = $prefixGroup->id;
            $prefix->save();
        }
    }

    public static function disassociatePrefixes($prefixGroup)
    {
        foreach ($prefixGroup->prefixes as $prefix) {
            $prefix->prefixrate_groups_id = null;
            $prefix->save();
        }
    }

    public function PrefixGroup(): BelongsToMany
    {
        return $this->belongsToMany(InternationalRate::class, 'international_rate_prefixrate_group', 'prefixrate_id', 'int_rate_id')
            ->whereNull('international_rate_prefixrate_group.deleted_at')
            ->withPivot('order', 'id')
            ->withTimestamps()->limit(1000)->orderBy('order');
    }

    public static function createInternacionalPrefix($prefixGroup, $request)
    {
        // foreach con index
        foreach ($request->prefixes as $index => $list) {
            $prefixGroup->PrefixGroup()
                ->attach(
                    $list['prefix_id'],
                    [
                        'order' => $index,
                        'company_id' => $request->header('company'),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]
                );
        }
    }

    public static function deleteInternacionalRate($prefixGroup)
    {
        foreach ($prefixGroup->PrefixGroup as $list) {
            $prefixGroup->PrefixGroup()->updateExistingPivot($list->id, ['deleted_at' => Carbon::now()]);
        }
    }

    public static function updateInternacionalRate($prefixGroup, $request)
    {
        // Extraer los todos los id relacionados de la base de datos
        $dbPrefixes = $prefixGroup->PrefixGroup()->get();

        // Extraer los todos los id de relacion del request
        $idRequest = [];
        foreach ($request->prefixes as $list) {
            if(isset($list['pivot']) && $list['pivot']['id'] != '') {
                $idRequest[] = $list['pivot']['id'];
            }
        }

        // verificar si los id de la base de datos no estan en el request para eliminar
        foreach ($dbPrefixes as $index => $dbPrefixe) {
            if(! in_array($dbPrefixe['pivot']['id'], $idRequest)) {
                $prefixGroup->PrefixGroup()->updateExistingPivot($dbPrefixe['id'], ['deleted_at' => Carbon::now()]);
            }
        }

        // verificar si los id del request no estan en la base de datos para crear y si estan actualizar
        foreach ($request->prefixes as $index => $list) {
            if(isset($list['pivot']) && $list['pivot']['id'] != '') {
                $prefixGroup->PrefixGroup()->updateExistingPivot($list['id'], ['order' => $index]);
            } else {
                $prefixGroup->PrefixGroup()->attach(
                    $list['prefix_id'],
                    [
                        'order' => $index,
                        'company_id' => $request->header('company'),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]
                );
            }
        }
    }
}
