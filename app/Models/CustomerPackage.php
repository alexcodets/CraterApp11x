<?php

namespace Crater\Models;

use Auth;
use Cache;
use Carbon\Carbon;
use Crater\Traits\ModelPagination;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Log;

class CustomerPackage extends Model
{
    use HasFactory;
    use softDeletes;
    use ModelPagination;

    public const TERM_DAILY = 'daily';
    public const TERM_WEEKLY = 'weekly';
    public const TERM_MONTHLY = 'monthly';
    public const TERM_BIMONTHLY = 'bimonthly';
    public const TERM_QUARTERLY = 'quarterly';
    public const TERM_BIANNUAL = 'biannual';
    public const TERM_YEARLY = 'yearly';
    public const TERM_ONE_TIME = 'one time';

    protected $guarded = ['id'];

    protected $fillable = [
        'customer_id',
        'package_id',
        'creator_id',
        'company_id',
        'tax_by',
        'allow_discount',
        'discount_by',
        'discount_type',
        'discount',
        'discount_val',
        'sub_total',
        'total',
        'tax',
        'status',
        'term',
        'activation_date',
        'renewal_date',
        'service_auto_suspension',
        'date_prev',
        'addresses_id',
    ];

    protected $appends = [
        'formattedCreatedAt',
        'formattedActivationDate',
        'formattedRenewalDate',
        'FormattedAddress',
    ];

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        // if ($filters->get('name')) {
        //     $query->whereName($filters->get('name'));
        // }

        // if ($filters->get('description')) {
        //     $query->whereDescription($filters->get('description'));
        // }

        // if ($filters->get('company_name')) {
        //     $query->whereCompanyName($filters->get('company_name'));
        // }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'customer_packages.id';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function getFormattedCreatedAtAttribute($value)
    {
        $dateFormat = Cache::remember('carbon_date_format_'.$this->company_id, 60 * 5, function () {
            return CompanySetting::getSetting('carbon_date_format', $this->company_id);
        });

        return Carbon::parse($this->created_at)->format($dateFormat);
    }

    public function getFormattedActivationDateAttribute($value)
    {
        $dateFormat = Cache::remember('carbon_date_format_'.$this->company_id, 60 * 5, function () {
            return CompanySetting::getSetting('carbon_date_format', $this->company_id);
        });

        return Carbon::parse($this->activation_date)->format($dateFormat);
    }

    public function getFormattedAddressAttribute()
    {
        // Si $this->addresses_id es null, busca por customer_id y type 'billing'
        if (is_null($this->addresses_id)) {
            $address = Address::where('user_id', $this->customer_id)
                ->where('type', 'billing')
                ->first();
        } else {
            // Si $this->addresses_id no es null, busca por id
            $address = Address::where('id', $this->addresses_id)->first();

            // Si no se encuentra la dirección por id, busca por customer_id y type 'billing'
            if ($address == null) {
                $address = Address::where('user_id', $this->customer_id)
                    ->where('type', 'billing')
                    ->first();
            }
        }

        // Retorna el registro encontrado o 'N/A' si no se encuentra ninguno
        return $address ?? 'N/A';
    }

    public function getRenewalDate($param)
    {
        $activation_date = Carbon::parse($param);
        $renewal_date = '';
        switch ($this->term) {
            case self::TERM_DAILY:
                $renewal_date = $activation_date->addDay();

                break;
            case self::TERM_WEEKLY:
                $renewal_date = $activation_date->addWeek();

                break;
            case self::TERM_MONTHLY:
                $renewal_date = $activation_date->addMonth();

                break;
            case self::TERM_BIMONTHLY:
                $renewal_date = $activation_date->addMonths(2);

                break;
            case self::TERM_QUARTERLY:
                $renewal_date = $activation_date->addQuarter();

                break;
            case self::TERM_BIANNUAL:
                $renewal_date = $activation_date->addMonths(6);

                break;
            case self::TERM_YEARLY:
                $renewal_date = $activation_date->addYear();

                break;
            case self::TERM_ONE_TIME:
                $renewal_date = Carbon::now();

                break;
        }

        return $renewal_date->format('Y-m-d');
    }

    public function getFormattedRenewalDateAttribute($value)
    {
        $dateFormat = Cache::remember('carbon_date_format_'.$this->company_id, 60 * 5, function () {
            return CompanySetting::getSetting('carbon_date_format', $this->company_id);
        });

        return Carbon::parse($this->renewal_date)->format($dateFormat);
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Packages::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CustomerPackageItem::class);
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(CustomerPackageTax::class);
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(CustomerPackageDiscount::class);
    }

    public function tickets(): MorphToMany
    {
        return $this->morphToMany(CustomerTicket::class, 'service', 'service_tickets', 'service_id', 'customer_ticket_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'customer_packages_id');
    }

    public static function updateCustomerPackage($request, $service)
    {

        if ($request->has('oneKey')) {
            $data[$request->key] = $request->get($request->key);
            $service->update($data);
        } else {
            $parameters = $request->parameters;
            Log::debug($parameters);
            $c_Pck = $request->packageCustomer;

            //validando direccion
            $addresses_id = null;

            if (array_key_exists("address", $parameters)) {

                $variable = $parameters['address'];
                if (array_key_exists("id", $variable)) {
                    $addresses_id = $parameters['address']['id'];
                }

            }

            $data = [
                'addresses_id' => $addresses_id,
                'tax_by' => $parameters['tax_type']['value'],
                'allow_discount' => $parameters['allow_discount'],
                'discount_by' => $parameters['discount_type']['value'],
                'discount_type' => $c_Pck['discount_type'],
                'discount' => $c_Pck['discount'],
                'discount_val' => $c_Pck['discount_val'],
                'sub_total' => $c_Pck['sub_total'],
                'total' => $c_Pck['total'],
                'tax' => $c_Pck['tax'],
                'status' => $parameters['status']['value'],
                'term' => $parameters['term']['value'],
                'activation_date' => $parameters['date_begin'],
                'renewal_date' => $parameters['renewal_date'],
                'service_auto_suspension' => $parameters['service_auto_suspension'],
            ];

            $service->update($data);

            if ($service->status == 'A' && empty($service->activation_date)) {
                $service->activation_date = Carbon::parse($service->created_at)->format('Y-m-d');
                $service->save();
            }

            self::deleteAssociations($service);
            self::createItems($c_Pck, $service);
            self::createTaxes($c_Pck, $service);
            self::createDiscounts($parameters, $service);
        }

        return $service;
    }

    public static function createItems($c_Pck, $service)
    {
        foreach ($c_Pck['items'] as $item) {
            $item['company_id'] = $service->company_id;
            $item['creator_id'] = Auth::id();
            $service_item = $service->items()->create($item);

            // Taxes for item
            $amounttax = 0;
            if (array_key_exists('taxes', $item) && $item['taxes']) {
                foreach ($item['taxes'] as $tax) {
                    $tax['company_id'] = $service->company_id;
                    $tax['creator_id'] = Auth::id();
                    if (gettype($tax['amount']) !== "NULL") {
                        $service_item->taxes()->create($tax);
                        $amounttax = $amounttax + $tax['amount'];
                    }
                }
            }

            $service_item->tax = $amounttax;
            $service_item->save();
        }
    }

    public static function createTaxes($c_Pck, $service)
    {
        if (array_key_exists('taxes', $c_Pck) && $c_Pck['taxes']) {
            foreach ($c_Pck['taxes'] as $tax) {
                $tax['company_id'] = $service->company_id;
                $tax['creator_id'] = Auth::id();
                if (gettype($tax['amount']) !== "NULL") {
                    $service->taxes()->create($tax);
                }
            }
        }
    }

    public static function createDiscounts($parameters, $service)
    {
        if ($parameters['allow_discount'] && (! empty($parameters['discount_start_date']) || ! empty($parameters['discount_time_units']))) {
            $service->discounts()->create([
                'creator_id' => Auth::id(),
                'company_id' => $service->company_id,
                'discount_type' => $service->discount_type,
                'discount' => $service->discount,
                'discount_val' => $service->discount_val,
                'term_type' => $parameters['discount_term_type']['value'],
                'start_date' => $parameters['discount_start_date'],
                'end_date' => $parameters['discount_end_date'],
                'time_unit_number' => $parameters['discount_time_units'],
                'term' => $parameters['discount_term']['value'],
            ]);
        }
    }

    public static function deleteCustomerPackage($ids)
    {
        foreach ($ids as $id) {
            $service = self::find($id);

            LogsModule::createLog(
                "Services",
                "delete",
                "admin/services/delete",
                $id,
                Auth::user()->name,
                Auth::user()->email,
                Auth::user()->role,
                Auth::user()->company_id,
                "Service: ".$service->package->name
            );

            self::deleteAssociations($service);

            $service->status = 'C';
            $service->save();

            $service->delete(); // Eliminar servicio
        }
    }

    public static function deleteAssociations($service)
    {
        // Items asociados al servicio
        if ($service->items()->exists()) {
            foreach ($service->items as $item) {
                // Se eliminan los impuestos de esos items
                if ($item->taxes()->exists()) {
                    $item->taxes()->delete();
                }
            }
            // Se eliminan los items
            $service->items()->delete();
        }

        // Se eliminan los impuestos asociados al servicio
        if ($service->taxes()->exists()) {
            $service->taxes()->delete();
        }

        // Se eliminan los descuentos asociados al servicio
        if ($service->discounts()->exists()) {
            $service->discounts()->delete();
        }
    }
}
