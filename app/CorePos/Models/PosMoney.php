<?php

namespace Crater\CorePos\Models;

use Crater\Models\PaymentMethod;
use Crater\Traits\ModelPagination;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Log;

class PosMoney extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ModelPagination;

    protected $table = 'pos_money';
    //protected $guarded = ['id'];

    public function paymentMethods(): BelongsToMany
    {
        return $this->belongsToMany(PaymentMethod::class, 'pos_money_payment_methods', 'pos_money_id', 'payment_method_id');
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);
        Log::debug($filters);

        if ($filters->get('money_name')) {
            $query->whereMoneyName($filters->get('money_name'));
        }

        if ($filters->get('is_coin')) {
            $query->whereIsCoin($filters->get('is_coin'));
        }

        if ($filters->get('currency_name')) {
            $query->whereCurrencyName($filters->get('currency_name'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'pos_money.id';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    // Filter by Money Name
    public function scopeWhereMoneyName($query, $money_name)
    {
        return $query->where('pos_money.name', 'LIKE', '%'.$money_name.'%');
    }

    // Filter by Is Coin
    public function scopeWhereIsCoin($query, $is_coin)
    {
        // return $query->where('pos_money.is_coin', '=', $is_coin );
        return $query->where('pos_money.is_coin', '=', $is_coin ? 1 : 0);
    }

    // Filter by Currency Name
    public function scopeWhereCurrencyName($query, $currency_name)
    {
        return $query->where('currencies.name', 'LIKE', '%'.$currency_name.'%');
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy("pos_money.id", $orderBy);
    }
}
