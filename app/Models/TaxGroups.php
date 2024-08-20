<?php

namespace Crater\Models;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxGroups extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tax_groups';

    protected $fillable = ['name', 'description', 'status', 'country_id', 'state_id',
    'city',
    'county'];

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('name')) {
            $query->whereName($filters->get('name'));
        }
        if ($filters->get('country')) {
            $query->whereCountry($filters->get('country'));
        }
        if ($filters->get('state')) {
            $query->whereState($filters->get('state'));
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

    public function scopeWhereName($query, $name)
    {
        return $query->where('tax_groups.name', 'LIKE', '%'.$name.'%');
    }

    public function scopeWhereCountry($query, $name)
    {
        return $query->where('countries.name', 'LIKE', '%'.$name.'%');
    }

    public function scopeWhereState($query, $name)
    {
        return $query->where('states.name', 'LIKE', '%'.$name.'%');
    }

    public function scopeWhereDescription($query, $description)
    {
        return $query->where('description', 'LIKE', '%'.$description.'%');
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

    public function scopeWhereStatus($query, $status)
    {
        $query->where('status', $status);
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public static function createTaxGroup($request)
    {
        $data = $request->validated();
        $tax_groups = TaxGroups::create($data);
        $tax_groups->city = $request->input('city');
        $tax_groups->county = $request->input('county');
        $tax_groups->save();

        return $tax_groups;
    }

    public static function addTaxGroup($array, $id)
    {
        $response = [];

        $array->each(function ($item) use ($id, $response) {
            $response[] = DB::table('tax_group_types')->insert([
                'tax_groups_id' => $id,
                'tax_types_id' => $item['id'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        });

        return $response;
    }

    public static function showTaxGroup($id)
    {
        $taxes = TaxGroups::leftJoin('tax_group', 'tax_group.tax_groups_id', 'tax_groups.id')
        ->leftJoin('tax_types', 'tax_types.id', 'tax_group.taxes_id')
        ->where('tax_groups.id', $id)->whereNull('tax_group.deleted_at')->get();

        return $taxes;
    }

    public function taxTypes(): HasMany
    {
        return $this->hasMany(TaxType::class, 'id', 'tax_types_id');
    }

    public static function updateTaxGroups($array, $id)
    {
        $response = [];
        DB::table('tax_group')->where('tax_groups_id', $id)->delete();

        $array->each(function ($item) use ($id, $response) {

            $response[] = DB::table('tax_group')->insert([
                'tax_groups_id' => $id,
                'taxes_id' => $item['id'],
            ]);

        });

        return $response;
    }

    public function taxGroupsTaxTypes(): BelongsToMany
    {
        return $this->belongsToMany(TaxType::class, 'tax_group', 'tax_groups_id', 'taxes_id')
        ->withPivot('id');
    }
}
