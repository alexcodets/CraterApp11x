<?php

namespace Crater\Models;

use Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Log;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'email',
        'phone',
        'customer_type',
        'first_name',
        'last_name',
        'status',
        'type',
        'website',
        'primary_contact_name',
        'followup_date',
        'last_contacted_date',
        'company_id',
    ];

    protected $appends = [
        'formattedLastDate',
        'formattedFollowDate',
    ];

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('company_name')) {
            $query->whereCompanyName($filters->get('company_name'));
        }
        if ($filters->get('email')) {
            $query->whereEmail($filters->get('email'));
        }
        if ($filters->get('status')) {
            $query->whereStatus($filters->get('status'));
        }
        if ($filters->get('type')) {
            $query->whereType($filters->get('type'));
        }
        if ($filters->get('customer_type')) {
            $query->whereCustomerType($filters->get('customer_type'));
        }

        if ($filters->get('phone')) {
            $query->wherePhone($filters->get('phone'));
        }

        if ($filters->get('last_contacted_date')) {
            $query->whereContactedDated($filters->get('last_contacted_date'));
        }

        if ($filters->get('followup_date')) {
            $query->whereFollowDated($filters->get('followup_date'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'name';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public function scopeWhereCompanyName($query, $company_name)
    {
        $query->orWhere('company_name', 'LIKE', '%'.$company_name.'%')
            ->orWhere('first_name', 'LIKE', '%'.$company_name.'%')
            ->orWhere('last_name', 'LIKE', '%'.$company_name.'%');
    }

    public function scopeWhereEmail($query, $email)
    {
        $query->where('email', 'LIKE', '%'.$email.'%');
    }

    public function scopeWherePhone($query, $phone)
    {
        $query->where('phone', 'LIKE', '%'.$phone.'%');
    }

    public function scopeWhereContactedDated($query, $dated)
    {
        \Log::debug('Entering scopeWhereContactedDated method');

        \Log::debug('Creating Carbon instance from dated', ['dated' => $dated]);
        $start = Carbon::createFromFormat('Y-m-d', $dated);

        \Log::debug('Carbon instance created', ['start' => $start]);

        // Convert to date format Y-m-d
        $formattedDate = $start->format('Y-m-d');
        \Log::debug('Formatted date', ['formattedDate' => $formattedDate]);

        \Log::debug('Adding where clause to query', ['last_contacted_date' => $formattedDate]);
        $query->whereDate('last_contacted_date', $formattedDate);

        \Log::debug('Exiting scopeWhereContactedDated method');
    }

    public function scopeWhereFollowDated($query, $dated)
    {
        $start = Carbon::createFromFormat('Y-m-d', $dated);
        $formattedDate = $start->format('Y-m-d');
        $query->where('followup_date', $formattedDate);
    }

    public function scopeWhereStatus($query, $status)
    {
        $query->where('status', $status);
    }

    public function scopeWhereType($query, $type)
    {
        $query->where('type', $type);
    }

    public function scopeWhereCustomerType($query, $customerType)
    {
        $query->where('customer_type', $customerType);
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public function getFormattedLastDateAttribute()
    {
        // Registrar el inicio del método
        Log::debug('Iniciando getFormattedLastDateAttribute');

        if (is_null($this->last_contacted_date)) {
            Log::debug('last_contacted_date es null');

            return null;
        }

        // Registrar el ID de la compañía
        Log::debug('company_id: '.$this->company_id);

        $dateFormat = Cache::remember('carbon_date_format_'.$this->company_id, 60 * 5, function () {
            Log::debug('Obteniendo formato de fecha de la configuración de la compañía');

            return CompanySetting::getSetting('carbon_date_format', $this->company_id);
        });

        // Registrar el formato de fecha obtenido
        Log::debug('Formato de fecha obtenido: '.$dateFormat);

        $formattedDate = Carbon::parse($this->last_contacted_date)->format($dateFormat);

        // Registrar la fecha formateada
        Log::debug('Fecha formateada: '.$formattedDate);

        return $formattedDate;
    }

    public function getFormattedFollowDateAttribute()
    {
        // Registrar el inicio del método
        Log::debug('Iniciando getFormattedLastDateAttribute');

        if (is_null($this->followup_date)) {
            Log::debug('followup_date es null');

            return null;
        }

        // Registrar el ID de la compañía
        Log::debug('company_id: '.$this->company_id);

        $dateFormat = Cache::remember('carbon_date_format_'.$this->company_id, 60 * 5, function () {
            Log::debug('Obteniendo formato de fecha de la configuración de la compañía');

            return CompanySetting::getSetting('carbon_date_format', $this->company_id);
        });

        // Registrar el formato de fecha obtenido
        Log::debug('Formato de fecha obtenido: '.$dateFormat);

        $formattedDate = Carbon::parse($this->followup_date)->format($dateFormat);

        // Registrar la fecha formateada
        Log::debug('Fecha formateada: '.$formattedDate);

        return $formattedDate;
    }
}
