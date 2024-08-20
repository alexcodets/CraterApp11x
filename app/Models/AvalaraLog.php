<?php

namespace Crater\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Log;

class AvalaraLog extends Model
{
    use HasFactory;
    public const INVOICE_MANUAL = 0;
    public const INVOICE_SERVICE = 1;
    public const STATUS_ERROR = 0;
    public const STATUS_SUCCESS = 1;
    public const OPERATION_CALC = 0;
    public const OPERATION_VOID = 1;
    public const OPERATION_REVERSE = 2;
    public const OPERATION_EDIT = 3;

    public const COMMIT = 4;
    public const UNCOMMIT = 5;

    protected $fillable = [
        'invoice_id',
        'type',
        'status',
        'pbx_service_id',
        'user_id',
        'avalara_log_id',
    ];

    protected $appends = [
        'formattedCreatedAt','time', 'typeName', 'statusName'
    ];

    public function getFormattedCreatedAtAttribute($value): string
    {
        return Carbon::parse($this->created_at)->format('Y-m-d');
    }

    public function getDateAttribute($value): string
    {
        return Carbon::parse($this->created_at)->isoFormat('MMMM Do YYYY, h:mm:ss a');
    }

    public function getTimeAttribute()
    {
        return $this->procesing_time / 100;
    }

    // relacion con invoice incluyendo los elementos eliminados logicamente (soft delete)
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class)->withTrashed();
    }

    // relacion con pbx_service
    public function pbxService(): BelongsTo
    {
        return $this->belongsTo(PbxServices::class);
    }

    // relacion con customer
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getTypeName(string $type): string
    {
        $types = [
            self::INVOICE_MANUAL => 'Invoice',
            self::INVOICE_SERVICE => 'Service Invoice',
        ];

        return $types[$type];
    }

    public function getStatusName(string $status): string
    {
        $statuses = [
            self::STATUS_ERROR => 'Error',
            self::STATUS_SUCCESS => 'Success',
        ];

        return $statuses[$status];
    }

    public function getTypeNameAttribute()
    {
        return $this->getTypeName($this->type);
    }

    public function getStatusNameAttribute()
    {
        return $this->getStatusName($this->status);
    }

    public function getResponseJsonAttribute()
    {
        return json_decode($this->response);
    }

    public function getJsonResponseAttribute()
    {
        return json_decode($this->response);
    }

    public function getRequestJsonAttribute()
    {
        return json_decode($this->request);
    }

    public function getJsonRequestAttribute()
    {
        return json_decode($this->request);
    }

    public function scopeToday()
    {
        return $this->whereDate('created_at', now()->startOfDay());
    }

    public function scopeThisMonth()
    {
        return $this->whereDate('created_at', '>', now()->startOfMonth());
    }

    public function scopeLastMonth()
    {
        return $this->whereDateBetween('created_at', now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth());
    }

    public function scopeLast30Days()
    {
        return $this->whereDate('created_at', '>', now()->subDays(30));
    }

    public function scopeAllTime()
    {
        return $this->whereNotNull('created_at');
    }

    public function scopeApplyFiltersLogs($query, array $filters)
    {


    }

    public function scopeWhereCustomer($query, $customer)
    {
        if($customer) {
            return $query->where('user_id', $customer);
        }
    }

    public function scopeWhereInvoice($query, $invoice_number)
    {
        if($invoice_number) {
            return $query->whereHas('invoice', function ($query) use ($invoice_number) {
                $query->where('invoice_number', 'like', "%{$invoice_number}%");
            });
        }

    }

    public function scopeWherePbxService($query, $pbx_services_number)
    {
        if($pbx_services_number) {
            return $query->whereHas('pbxService', function ($query) use ($pbx_services_number) {
                $query->where('pbx_services_number', 'like', "%{$pbx_services_number}%");
            });
        }

    }

    public function scopeWhereDates($query, $filters)
    {
        // Log::info($filters['to_date']);
        if ($filters['from_date'] && $filters['to_date']) {
            $date_from = Carbon::parse($filters['from_date']);
            $date_to = Carbon::parse($filters['to_date']);

            // whereBetween
            return $query->whereBetween('created_at', [$date_from, $date_to]);

        }

    }

    // scope orderColumn
    public function scopeOrderColumn($query, $column, $order)
    {
        // varificar si las variables vienen vacias
        if ($column && $order) {
            if($column == 'formattedCreatedAt') {
                $column = 'created_at';
            }

            return $query->orderBy($column, $order);
        }
    }
}
