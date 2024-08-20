<?php

namespace Crater\Models;

use Auth;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageGroups extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'package_groups';

    protected $fillable = ['company_id', 'name', 'description', 'allow_upgrades', 'status'];

    public function scopeWhereStatus($query, $status)
    {
        $query->where('status', $status);
    }

    public function scopeWhereSearch($query, $search)
    {
        foreach (explode(' ', $search) as $term) {
            $query->where(function ($query) use ($term) {
                $query->where('name', 'LIKE', '%'.$term.'%');
            });
        }
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

    public static function showPackageGroup($id)
    {
        // $packages = DB::table('package_group')
        // ->join('packages', 'packages.id', '=', 'package_group.packages_id')
        // ->where('package_groups_id', $id)
        // ->where('deleted_at', null)
        // ->get();

        $packages = PackageGroup::join('packages', 'packages.id', 'package_group.packages_id')
        ->select('packages.id', 'packages.name')
        ->where('package_groups_id', $id)
        ->where('package_group.deleted_at', '=', null)
        ->get();

        // PackageGroups::leftJoin('package_group', 'package_group.package_groups_id', 'package_groups.id')
        // ->leftJoin('packages', 'packages.id', 'package_group.packages_id')
        // ->select('packages.id', 'packages.name', 'package_group.order')
        // ->where('package_groups.id', $id)->get();

        return $packages;
    }

    public static function createPackageGroup($request)
    {
        $data = $request->validated();
        $data['company_id'] = Auth::user()->company_id;
        $data['status'] = 'A';
        $package_groups = PackageGroups::create($data);

        return $package_groups;
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

    public static function addPackages($array, $id)
    {
        $response = [];

        $array->each(function ($item) use ($id, $response) {
            $response[] = DB::table('package_group')->insert([
                'package_groups_id' => $id,
                'packages_id' => $item['id'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        });

        return $response;
    }
}
