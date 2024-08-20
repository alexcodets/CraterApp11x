<?php

namespace Crater\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MobileLoginLogs extends Model
{
    use HasFactory;

    protected $fillable = [
        'system_name',
        'system_version',
        'device_name',
        'is_tablet',
        'serial_number',
        'brand',
        'device_id',
        'device_type',
        'unique_id',
        'manufacturer',
        'api_level',
        'mac_address',
        'firebase_code',
        'customer_id',
        'session_start'
    ];

    protected $appends = ['formattedCreatedAt'];

    public function customer(): HasMany
    {
        return $this->hasMany(User::class, 'id', 'customer_id');
    }

    public function getFormattedCreatedAtAttribute($value)
    {
        $dateFormat = CompanySetting::getSetting('carbon_date_format_with_hour', 1);

        return Carbon::parse($this->created_at)->format($dateFormat);
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('customer')) {
            $query->whereCustomer($filters->get('customer'));
        }

        if ($filters->get('session_start')) {
            $query->whereDate($filters->get('session_start'));
        }

        if ($filters->get('operating_system')) {
            $query->whereSystemName($filters->get('operating_system'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'id';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'desc';
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

    public function scopeWhereCustomer($query, $name)
    {
        return $query->leftJoin('users', 'mobile_login_logs.customer_id', 'users.id')->where('users.name', 'LIKE', '%'.$name.'%');
    }

    public function scopeWhereDate($query, $date)
    {
        return $query->where('session_start', 'LIKE', '%'.$date.'%');
    }

    public function scopeWhereSystemName($query, $system)
    {
        return $query->where('system_name', 'LIKE', '%'.$system.'%');
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        if($orderByField == "name") {
            $query->orderBy("customer_id", $orderBy);
        } else {
            $query->orderBy($orderByField, $orderBy);
        }
    }
}
