<?php

namespace Crater\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileDidTollFree extends Model
{
    use HasFactory;

    protected $table = 'profile_did_toll_frees';

    protected $guarded = [
        'id'
    ];

    protected $fillable = ['prefijo', 'status','toll_free_category_id','rate_per_minute','company_id','creator_id'];

    protected $appends = [
        'category_name'
    ];

    public function getCategoryNameAttribute($value)
    {

        if($this->category != null) {
            return $this->category->name;
        } else {
            return "N/A";
        }

    }

    public function scopewhereStatus($query, $name)
    {
        return $query->where('status',  $name);
    }

    public function scopewherePrefijo($query, $name)
    {
        return $query->where('prefijo',  'LIKE', '%'.$name.'%');
    }

    public function scopewhereTollefree($query, $name)
    {
        return $query->where('toll_free_category_id',  $name);
    }

    public function scopeApplyFilters($query, array $filters)
    {

        $filters = collect($filters);

        if ($filters->get('status')) {
            $query->whereStatus($filters->get('status'));
        }

        if ($filters->get('prefijo')) {
            $query->wherePrefijo($filters->get('prefijo'));
        }

        if ($filters->get('toll_free_category_id')) {
            $query->whereTollefree($filters->get('toll_free_category_id'));
        }


        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'dd';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    // Buscador por orden
    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {

        if($orderByField == "category_name") {
            $orderByField = "toll_free_category_id";
        }
        $query->orderBy($orderByField, $orderBy);
    }

    // Paginador
    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public static function createProfileDidTollFree($request)
    {

        if(isset($request['multiple'])) {
            foreach($request['multiple'] as $custom) {
                $data["status"] = $request->status;
                $data["prefijo"] = $custom['prefijo'];
                $data["toll_free_category_id"] = $request->toll_free_category_id;
                $data["rate_per_minute"] = $custom['rate_per_minutes'];
                $data['company_id'] = Auth::user()->company_id;
                $data['creator_id'] = Auth::user()->id;
                $ProfileDidTollFree = ProfileDidTollFree::create($data);
            }

            return $ProfileDidTollFree;
        } else {
            $data["status"] = $request->status;
            $data["prefijo"] = $request->prefijo;
            $data["toll_free_category_id"] = $request->toll_free_category_id;
            $data["rate_per_minute"] = $request->rate_per_minutes;
            $data['company_id'] = Auth::user()->company_id;
            $data['creator_id'] = Auth::user()->id;
            $ProfileDidTollFree = ProfileDidTollFree::create($data);

            return $ProfileDidTollFree;
        }
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PbxCategorie::class, 'toll_free_category_id');
    }
}
