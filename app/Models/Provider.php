<?php

namespace Crater\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Log;

class Provider extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['first_name', 'last_name', 'email',  'country_id', 'state_id', 'city', 'street', 'zip_code', 'description', 'phone', 'mobile', 'fax', 'other', 'webside', 'terms', 'opening_balance', 'as_of', 'account_no', 'business_no', 'track_payments', 'default_expense_account', 'company_id', 'creator_id', 'status', 'providers_number'];

    // Filtro para buscador en la vista principal
    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }
        if ($filters->get('provider')) {
            $filters->put('provider', explode(',', $filters->get('provider')));
            $query->whereProvider($filters->get('provider'));
        }

        if ($filters->get('status')) {
            $query->whereStatus($filters->get('status'));
        }
        //
        if ($filters->get('providers_number')) {
            $query->WhereProvidersNumber($filters->get('providers_number'));
        }

        if ($filters->get('title')) {
            $query->WhereTitle($filters->get('title'));
        }

        if ($filters->get('phone')) {
            $query->WherePhone($filters->get('phone'));
        }

        if ($filters->get('email')) {
            $query->whereEmail($filters->get('email'));
        }
        //

        /*
        if ($filters->get('first_name')) {
            $query->WhereFirstName($filters->get('first_name'));
        }

        if ($filters->get('last_name')) {
            $query->WhereLastName($filters->get('last_name'));
        }
        */

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'name';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    // Buscador por providers number
    public function scopeWhereProvidersNumber($query, $providers_number)
    {
        return $query->where('providers_number', 'LIKE', '%'.$providers_number.'%');
    }

    // Buscador por titulo
    public function scopeWhereTitle($query, $title)
    {
        return $query->where('title', 'LIKE', '%'.$title.'%');
    }

    public function scopeWhereProvider($query, $provider)
    {
        \Log::debug($provider);

        return $query->whereIn('id',  $provider);
    }

    // Buscador por phone
    public function scopeWherePhone($query, $phone)
    {
        return $query->where('phone', 'LIKE', '%'.$phone.'%');
    }

    // Buscador por Email
    public function scopeWhereEmail($query, $email)
    {
        return $query->where('email', 'LIKE', '%'.$email.'%');
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
                $query->where('first_name', 'LIKE', '%'.$term.'%');
            });
        }
    }

    // Buscador por estatus 'A'
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

    // Creando proveedor
    public static function createProvider($request)
    {
        $request['suffix'] = "null";
        $request['display_name'] = "null";
        $data = $request->validated();
        $data['company_id'] = Auth::user()->company_id;
        $data['creator_id'] = Auth::user()->id;

        //Log::debug($data);
        $provider = Provider::create($data);
        $number = CompanySetting::getSetting('prov_prefix', $request->header('company')) ?? 'PRV';
        $provider->providers_number = $number."-000".$provider->id;
        $provider->title = $data['title'] ;
        $provider->save();

        return $provider;
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'providers_id');
    }
}
