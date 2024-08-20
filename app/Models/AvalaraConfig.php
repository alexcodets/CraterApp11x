<?php

namespace Crater\Models;

use Crypt;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvalaraConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'conexion',
        'user_name',
        'password',
        'client_id',
        'url',
        'host',
        'company_id',
        'bscl',
        'svcl',
        'fclt',
        'reg',
        'frch',
        'item_did_id',
        'item_cdr_id',
        'item_extension_id',
        'item_custom_id',
        'item_international_id',
        'item_toll_free_id',
        'Profile_ID',
        'company_identifier',
        'account_reference',
        'is_default',
        'services_price_item_id',
        'additional_charges_item_id',
        'invm',
        'dtl',
        'summ',
        'retnb',
        'retext',
        'incrf'
    ];

    public function did(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_did_id');
    }

    public function cdr(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_cdr_id');
    }

    public function extension(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_extension_id');
    }

    public function international(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_international_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function setPasswordAttribute($value)
    {
        if (empty($value)) {
            //No se permite tener vacio el password.
            return;
        }
        $longitud = strlen($this->attributes['password'] ?? 0);

        if ($longitud !== 200 or $value != Crypt::decryptString($this->attributes['password'])) {
            $this->attributes['password'] = Crypt::encryptString($value);
        }

    }

    public function getPasswordDecodeAttribute($value)
    {
        if (strlen($this->attributes['password']) >= 200) {
            return Crypt::decryptString($this->attributes['password']);
        } else {
            return $this->attributes['password'];
        }
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('company_id', $company_id);
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('conexion')) {
            $query->whereConexion($filters->get('conexion'));
        }

        if ($filters->get('user_name')) {
            $query->whereUserName($filters->get('user_name'));
        }

        if ($filters->get('client_id')) {
            $query->whereQty($filters->get('client_id'));
        }

        if ($filters->get('url')) {
            $query->whereQty($filters->get('url'));
        }

        if ($filters->get('host')) {
            $query->whereQty($filters->get('host'));
        }

        if ($filters->get('status')) {
            $query->whereQty($filters->get('status'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {

            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'id';

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

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        return $query->orderBy($orderByField, $orderBy);
    }

    public function scopeWhereConexion($query, $conexion)
    {
        return $query->where('conexion', 'LIKE', '%'.$conexion.'%');
    }

    public function scopeWhereUserName($query, $user_name)
    {
        return $query->where('user_name', 'LIKE', '%'.$user_name.'%');
    }

    public function scopeActive($query, $active = 'A')
    {
        return $query->where('status', $active);
    }

    public function getCfgAttribute(): array
    {
        return array_filter([
            //"retnb"  => $this->retnb ?? true,
            //"retext" => $this->retext ?? true,
            //"incrf"  => $this->incrf ?? true,
            'retext' => false,
            'retnb' => false,
            'incrf' => true,
        ], function ($v) {
            return ! is_null($v);
        });

    }
}
