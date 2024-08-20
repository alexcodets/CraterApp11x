<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = [ 'formattedCreatorName'];

    public function getformattedCreatorNameAttribute($value)
    {

        if (is_null($this->creator_id)) {
            return "N/A";
        }

        $user = User::where("id", $this->creator_id)->first();

        if($user) {
            return $user->name;
        } else {
            return "N/A";
        }

    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('type')) {
            $query->whereType($filters->get('type'));
        }

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'id';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopeWhereSearch($query, $search)
    {
        $query->where('name', 'LIKE', '%'.$search.'%');
    }

    public function scopeWhereType($query, $type)
    {
        return $query->where('type', $type);
    }
}
