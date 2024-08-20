<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaypalSetting extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'paypal_settings';

    protected $fillable = ['merchant_id', 'public_key', 'status', 'email', 'private_key', 'enviroment', 'creator_id','enable_identification_verification',
    'enable_fee_charges','name'];

    // Filtro para buscador en la vista principal
    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('merchant_id')) {
            $query->whereMerchantID($filters->get('merchant_id'));
        }

        if ($filters->get('public_key')) {
            $query->wherePublicKey($filters->get('public_key'));
        }

        if ($filters->get('email')) {
            $query->whereEmail($filters->get('email'));
        }

        if ($filters->get('private_key')) {
            $query->wherePrivateKey($filters->get('private_key'));
        }

        if ($filters->get('enviroment')) {
            $query->whereEnviroment($filters->get('enviroment'));
        }

        if ($filters->get('status')) {
            $query->whereStatus($filters->get('status'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'id';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    // Buscador por nombre
    public function scopeWhereSearch($query, $search)
    {
        foreach (explode(' ', $search) as $term) {
            $query->where(function ($query) use ($term) {
                $query->where('paypal_id', 'LIKE', '%'.$term.'%');
            });
        }
    }

    // Buscador por merchant ID
    public function whereMerchantID($query, $login_id)
    {
        return $query->where('merchant_id', 'LIKE', '%'.$login_id.'%');
    }

    // Buscador por public key
    public function wherePublicKey($query, $public_key)
    {
        return $query->where('public_key', 'LIKE', '%'.$public_key.'%');
    }

    // Buscador por email
    public function scopeWhereEmail($query, $payment_API)
    {
        return $query->where('email', 'LIKE', '%'.$email.'%');
    }

    // Buscador por private key
    public function wherePrivateKey($query, $private_key)
    {
        return $query->where('private_key', 'LIKE', '%'.$private_key.'%');
    }

    // Buscador por enviroment
    public function whereEnviroment($query, $enviroment)
    {
        return $query->where('enviroment', 'LIKE', '%'.$enviroment.'%');
    }

    // Buscador por estatus
    public function scopeWhereStatus($query, $status)
    {
        $query->where('status', $status);
    }

    // Buscador por orden
    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
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
}
