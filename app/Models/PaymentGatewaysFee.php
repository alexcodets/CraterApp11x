<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGatewaysFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'company_id',
        'amount',
        'payment_gateway',
        'authorize_setting_id',
        'aux_vault_setting_id',
        'paypal_settings_id'
    ];

    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_FIXED = 'fixed';

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('company_id', $company_id);
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);
        \Log::debug($filters);

        if (array_key_exists("name", $filters->toArray())) {
            $query->whereName($filters->get('name'));
        }

        if ($filters->get('payment_gateway')) {
            $query->wherePaymentGateway($filters->get('payment_gateway'));
        }

        if ($filters->get('authorize_setting_id')) {
            $query->whereAuthotizesetting($filters->get('authorize_setting_id'));
        }

        if ($filters->get('aux_vault_setting_id')) {
            $query->whereAuxvault($filters->get('aux_vault_setting_id'));
        }

        if ($filters->get('paypal_settings_id')) {
            $query->wherePaypal($filters->get('paypal_settings_id'));
        }




        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'name';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public function scopeWhereName($query, $search)
    {
        $query->where('name', 'LIKE', '%'.$search.'%');
    }

    public function scopeWherePaymentGateway($query, $search)
    {
        $query->where('payment_gateway', 'LIKE', '%'.$search.'%');
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopeWhereAuthotizesetting($query, $authorize_setting_id)
    {
        $query->where('authorize_setting_id', $authorize_setting_id);
    }

    public function scopeWhereAuxvault($query, $aux_vault_setting_id)
    {
        $query->where('aux_vault_setting_id', $aux_vault_setting_id);
    }

    public function scopeWherePaypal($query, $paypal_settings_id)
    {
        $query->where('paypal_settings_id', $paypal_settings_id);
    }
}
