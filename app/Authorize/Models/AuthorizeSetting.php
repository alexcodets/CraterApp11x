<?php

namespace Crater\Authorize\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuthorizeSetting extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'authorize_settings';

    protected $fillable = ['currency', 'status', 'company_id', 'login_id', 'transaction_key', 'payment_API', 'payment_account_validation_mode', 'test_mode', 'developer_mode', 'creator_id',
    'enable_identification_verification',
    'enable_fee_charges','name'];

    protected $appends = [
        'paymentsfeesarray',
    ];

    public function getpaymentsfeesarrayAttribute()
    {

    }

    // Filtro para buscador en la vista principal
    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('login_id')) {
            $query->WhereLoginID($filters->get('login_id'));
        }

        if ($filters->get('currency')) {
            $query->WhereCurrency($filters->get('currency'));
        }

        if ($filters->get('payment_API')) {
            $query->WherePaymentAPI($filters->get('payment_API'));
        }

        if ($filters->get('payment_account_validation_mode')) {
            $query->wherePaymentAccount($filters->get('payment_account_validation_mode'));
        }

        if ($filters->get('test_mode')) {
            $query->whereTestMode($filters->get('test_mode'));
        }

        if ($filters->get('developer_mode')) {
            $query->whereDeveloperMode($filters->get('developer_mode'));
        }

        if ($filters->get('status')) {
            $query->whereStatus($filters->get('status'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'name';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    // Buscador por titulo
    public function scopeWhereLoginID($query, $login_id)
    {
        return $query->where('login_id', 'LIKE', '%'.$login_id.'%');
    }

    // Buscador por primer nombre
    public function scopeWhereCurrency($query, $currency)
    {
        return $query->where('currency', 'LIKE', '%'.$currency.'%');
    }

    // Buscador por apellido
    public function scopeWherePaymentAPI($query, $payment_API)
    {
        return $query->where('payment_API', 'LIKE', '%'.$payment_API.'%');
    }

    // Buscador por Descripcion
    public function scopeWherePaymentAccount($query, $payment_account_validation_mode)
    {
        return $query->where('payment_account_validation_mode', 'LIKE', '%'.$payment_account_validation_mode.'%');
    }

    // Buscador por Descripcion
    public function scopeWhereTestMode($query, $test_mode)
    {
        return $query->where('test_mode', 'LIKE', '%'.$test_mode.'%');
    }

    // Buscador por Descripcion
    public function scopeWhereDeveloperMode($query, $developer_mode)
    {
        return $query->where('developer_mode', 'LIKE', '%'.$developer_mode.'%');
    }

    // Buscador por orden
    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    // Buscador por nombre
    public function scopeWhereSearch($query, $search)
    {
        foreach (explode(' ', $search) as $term) {
            $query->where(function ($query) use ($term) {
                $query->where('login_id', 'LIKE', '%'.$term.'%');
            });
        }
    }

    // Buscador por estatus
    public function scopeWhereStatus($query, $status)
    {
        $query->where('status', $status);
    }

    // Paginador
    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }
}
