<?php

namespace Crater\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// models

class PushNotificationsLogs extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'date',
        'message',
        'notification_sent',
        'log_message',
        'notification_data'
    ];

    protected $appends = [
        'formattedCreatedAt',
    ];

    // relations //
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // attributes //
    public function getFormattedCreatedAtAttribute($value)
    {
        $dateFormat = CompanySetting::getSetting('carbon_date_format', 1);

        return Carbon::parse($this->created_at)->format($dateFormat);
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('customer')) {
            $query->whereCustomer($filters->get('customer'));
        }

        if ($filters->get('date')) {
            $query->whereDate($filters->get('date'));
        }

        if ($filters->get('message')) {
            $query->whereMessage($filters->get('message'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'id';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'desc';
            $query->whereOrder($field, $orderBy);
        }

    }

    public function scopewhereCustomer($query, $customer_name)
    {
        $query->whereHas('customer', function ($query) use ($customer_name) {
            $query->where('name', 'LIKE', '%'.$customer_name.'%');
        });
    }

    public function scopewhereDate($query, $date)
    {
        return $query->where('date', 'LIKE', '%'.$date.'%');
    }

    public function scopewhereMessage($query, $message)
    {
        return $query->where('message', 'LIKE', '%'.$message.'%');
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
