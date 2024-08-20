<?php

namespace Crater\Models;

use Crypt;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contacts extends Model
{
    use HasFactory;

    protected $fillable = [
        'allow_receive_emails', 'name', 'last_name', 'phone', 'email', 'position', 'customer_id',
        'type', 'log_in_credentials', 'password', 'repeat_password', 'invoices', 'estimates', 'payments',
        'tickets', 'payments_accounts', 'reports', 'email_estimates', 'email_invoices', 'email_payments', 'email_services',
        'email_pbx_services', 'email_tickets',
    ];

    protected $appends = [
        'PasswordDecode',

    ];

    public function getPasswordDecodeAttribute()
    {

        try {
            $decrypted = Crypt::decryptString($this->attributes['password']);
            // Deserializar la cadena si es necesario
            if (preg_match('/^s:\d+:"(.*)";$/', $decrypted, $matches)) {
                return $matches[1];
            }

            return $decrypted;
        } catch (\Throwable $th) {
            return '';
        }
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);
        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'country_id';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }
}
