<?php

namespace Crater\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class CustomDidGroup extends Model
{
    use HasFactory;
    use softDeletes;

    public const DB_FIELDS = [
        'prefijo' => 'Prefix',
        'toll_free_category_id' => 'Category',
        'rate_per_minute' => 'Rate'
    ];

    protected $guarded = [
        'id'
    ];

    protected $fillable = [
        'name', 'description', 'company_id', 'status', 'type'
    ];

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

    public function scopeWhereStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeWhereType($query, $type)
    {
        return $query->where('type', $type);
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

        if ($filters->get('status')) {
            $query->whereStatus($filters->get('status'));
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

    public function customDids(): BelongsToMany
    {
        return $this->belongsToMany(ProfileDidTollFree::class, 'toll_free_custom_did_group', 'custom_did_group_id', 'toll_free_did_id')
            ->whereNull('toll_free_custom_did_group.deleted_at')
            ->withTimestamps();
    }

    public static function createCustomDidGroup($request)
    {
        $customDidGroup = self::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request['status']['value'],
            'type' => $request['type']['value'],
            'company_id' => $request->header('company')
        ]);

        self::createTollFreeDids($customDidGroup, $request);

        return $customDidGroup;
    }

    public static function updateCustomDidGroup($request, $customDidGroup)
    {
        $customDidGroup->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request['status']['value'],
            'type' => $request['type']['value'],
            'company_id' => $request->header('company')
        ]);

        // Eliminar toll free did asociados
        self::deleteTollFreeDids($customDidGroup);

        // Asociar nuevos toll free did
        self::createTollFreeDids($customDidGroup, $request);

        return $customDidGroup;
    }

    public static function deleteCustomDidGroup($ids)
    {
        foreach ($ids as $id) {
            $customDidGroup = self::find($id);

            LogsModule::createLog(
                "Custom did Groups",
                "delete",
                "/admin/corePBX/billing-templates/custom-did-groups/delete",
                $id,
                Auth::user()->name,
                Auth::user()->email,
                Auth::user()->role,
                Auth::user()->company_id,
                "Custom Did Group: ".$customDidGroup->name
            );

            self::deleteTollFreeDids($customDidGroup); // Eliminar toll free did asociados

            $customDidGroup->delete(); // Eliminar custom did group
        }

        return true;
    }

    public static function createTollFreeDids($customDidGroup, $request)
    {
        foreach ($request->customDids as $did) {
            $customDidGroup->customDids()
                ->attach(
                    $did['custom_did_id'],
                    [
                        'company_id' => $request->header('company'),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]
                );
        }
    }

    public static function deleteTollFreeDids($customDidGroup)
    {
        foreach ($customDidGroup->customDids as $did) {
            $customDidGroup->customDids()->updateExistingPivot($did->id, ['deleted_at' => Carbon::now()]);
        }
    }
}
