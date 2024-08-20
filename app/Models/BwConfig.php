<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class BwConfig extends Model
{
    use HasFactory;
    use softDeletes;

    protected $guarded = [ 'id' ];

    protected $fillable = [ 'account_name', 'account_id', 'url', 'user', 'password', 'is_default' ];

    protected $hidden = ['password',];

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'account_id';
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

    public static function createBwConfig($request)
    {
        $config = self::create([
            'account_name' => $request->account_name,
            'account_id' => $request->account_id,
            'url' => $request->url,
            'user' => $request->user,
            'password' => Crypt::encryptString($request->password),

        ]);

        return $config;
    }

    public function updateBwConfig($request)
    {
        if (! empty($request->password)) {
            $data = $request->all();
            $data['password'] = Crypt::encryptString($data['password']);
        } else {
            $data = $request->except('password');
        }
        $this->update($data);

        return BwConfig::find($this->id);
    }
}
