<?php

namespace Crater\Models;

use Auth;
use Carbon\Carbon;
use Crater\Traits\ModelPagination;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Log;

class PbxServices extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ModelPagination;

    // nombre de la tabla
    protected $table = 'pbx_services';

    protected $guarded = ['id'];

    public const TERM_DAILY = 'daily';
    public const TERM_WEEKLY = 'weekly';
    public const TERM_MONTHLY = 'monthly';
    public const TERM_BIMONTHLY = 'bimonthly';
    public const TERM_QUARTERLY = 'quarterly';
    public const TERM_BIANNUAL = 'biannual';
    public const TERM_YEARLY = 'yearly';
    public const TERM_ONE_TIME = 'one time';

    protected $fillable = [
        'company_id',
        'creator_id',
        'customer_id',
        'cap_extension',
        'cap_total',
        'pbx_package_id',
        'status',
        'term',
        'date_begin',
        'renewal_date',
        'pbx_tenant_id',
        'allow_discount',
        'allow_pbxpackages',
        'allow_items',
        'allow_extensions',
        'allow_did',
        'allow_aditionalcharges',
        'allow_usagesummary',
        'auto_suspension',
        'only_callrating',
        'allow_discount_value',
        'discount_term_type',
        'allow_discount_type',
        'date_from',
        'date_to',
        'time_period',
        'time_period_value',
        'time_period_type',
        'pbxpackages_price',
        'sub_total',
        'total',
        'total_prorate',
        'tax',
        'prefixrate_groups_id',
        'prefixrate_groups_outbound_id',
        'inclusive_minutes_seconds_consumed',
        'tax_type_id',
        'suspension_type',
        'custom_app_rate_id',
        'allow_customapp',
        'addresses_id',
        'main_update',
        'allow_pbx_packages_update',
        'apply_tax_type',
        'discount_val'
    ];

    //
    protected $appends = [
        'formattedCreatedAt',
        'formattedActivationDate',
        'formattedRenewalDate',
    ];

    protected $dateFormat;

    public function getFormattedCreatedAtAttribute($value)
    {
        $this->dateFormat = $this->dateFormat ?? CompanySetting::getSetting('carbon_date_format', $this->company_id);

        return Carbon::parse($this->created_at)->format($this->dateFormat);
    }

    public function getFormattedRenewalDateAttribute($value)
    {
        return Carbon::parse($this->renewal_date)->format($this->dateFormat);
    }

    public function getFormattedActivationDateAttribute($value)
    {
        return Carbon::parse($this->date_begin)->format($this->dateFormat);
    }

    public function pbxPackage(): BelongsTo
    {
        return $this->belongsTo(PbxPackages::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'addresses_id');
    }

    public function pbxServicesAppRate(): HasMany
    {
        return $this->hasMany(PbxServicesAppRate::class, 'pbx_service_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(PbxTenant::class, 'pbx_tenant_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function pbxServiceExtensions(): HasMany
    {
        return $this->hasMany(PbxServicesExtensions::class, 'pbx_service_id');
    }

    public function pbxExtensions(): BelongsToMany
    {
        return $this->belongsToMany(PbxExtensions::class, 'pbx_services_extensions', 'pbx_service_id', 'pbx_extension_id');
    }

    public function pbxServiceTaxTypes(): HasMany
    {
        return $this->hasMany(PbxServicesTaxTypes::class, 'pbx_services_id');
    }

    public function pbxServiceTaxTypesCdrs(): HasMany
    {
        return $this->hasMany(PbxServicesTaxTypesCdr::class, 'pbx_services_id');
    }

    public function pbxTaxTypesHistory(): HasMany
    {
        return $this->hasMany(HistoryCallIndiTaxType::class, 'pbx_services_id');
    }

    /* dids */
    public function pbxServiceDids(): HasMany
    {
        return $this->hasMany(PbxServicesDID::class, 'pbx_service_id');
    }

    public function avalaraLogs(): HasMany
    {
        return $this->hasMany(AvalaraLog::class, 'pbx_service_id');
    }

    public function getPbxCdrs()
    {
        $cdr = new CallDetailRegister();
        $cdr->setTable($cdr->firstOrCreateTableFromService($this));

        return $cdr;
    }

    public function pbxCdrTotals(): HasMany
    {
        return $this->hasMany(CallDetailRegisterTotal::class);
    }

    public function pbxCdrTotalsCurrent(): HasMany
    {
        return $this->hasMany(CallDetailRegisterTotal::class)->whereNull('invoice_id');
    }

    public function pbxCdrTotalsCurrentbalance(): HasMany
    {
        return $this->hasMany(CallDetailRegisterTotal::class)->where("exclusive_cost", ">", "exclusive_cost_paid");
    }

    public function pbxCdrTotalsNotCurrent(): HasMany
    {
        return $this->hasMany(CallDetailRegisterTotal::class)->whereNotNull('invoice_id');
    }

    public function pbxCdrTotalsForInvoice($invoice_id): HasMany
    {
        return $this->hasMany(CallDetailRegisterTotal::class)->where('invoice_id', $invoice_id)->whereNull('international_rate_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * If not category is selected it will return all customCdr
     *
     * @param int $invoice_id
     * @param string|null $category
     * @return HasMany
     */
    public function pbxCustomCdrTotalsForInvoice(int $invoice_id, string $category = null): HasMany
    {
        return $this->hasMany(CallDetailRegisterTotal::class)->where('invoice_id', $invoice_id)
            ->whereHas('customRate', function ($query) use ($category) {
                $query->when($category, function ($q, $category) {
                    $q->where('category', '=', $category);
                });
            });

    }

    public function items(): HasMany
    {
        return $this->hasMany(PbxServicesItems::class, 'pbx_services_id');
    }

    public function getItems(): HasMany
    {
        return $this->hasMany(PbxServicesItems::class);
    }

    public function getCallDetailRegisterTotal(): HasMany
    {
        return $this->hasMany(CallDetailRegisterTotal::class, 'pbx_services_id', 'id')->whereNull('invoice_id');
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(PbxServicesTaxTypes::class, 'pbx_services_id');
    }

    public function CustomRateGroupItems(): HasMany
    {
        return $this->hasMany(CustomRateGroupItems::class, 'prefixrate_id', 'prefixrate_groups_id');
    }

    public function customInboundRates(): HasMany
    {
        return $this->hasMany(CustomRate::class, 'prefixrate_groups_id', 'prefixrate_groups_id');
    }

    public function customOutboundRates(): HasMany
    {
        return $this->hasMany(CustomRate::class, 'prefixrate_groups_id', 'prefixrate_groups_outbound_id');
    }

    public function internationalRates(): HasMany
    {
        return $this->hasMany(InternationalRate::class, 'prefixrate_groups_id', 'prefixrate_groups_id');
    }

    public function internationalInboundRates(): HasMany
    {
        return $this->hasMany(InternationalRate::class, 'prefixrate_groups_id', 'prefixrate_groups_id');
    }

    public function internationalOutboundRates(): HasMany
    {
        return $this->hasMany(InternationalRate::class, 'prefixrate_groups_id', 'prefixrate_groups_outbound_id');
    }

    public function avalaraExemptions(): HasMany
    {
        return $this->hasMany(AvalaraExemption::class);
    }

    public function taxType(): BelongsTo
    {
        return $this->belongsTo(TaxType::class);
    }

    public function cdrTax(): BelongsTo
    {
        return $this->belongsTo(TaxType::class, 'tax_type_id');
    }

    public function taxTypesCdr(): BelongsToMany
    {
        return $this->belongsToMany(TaxType::class, 'pbx_services_tax_types_cdr', 'pbx_services_id', 'tax_types_id')
            ->withPivot('id', 'status');
    }

    public function customOutboundGroup(): BelongsTo
    {
        return $this->belongsTo(PrefixRateGroups::class, 'prefixrate_groups_outbound_id');
    }

    public function customInboundGroup(): BelongsTo
    {
        return $this->belongsTo(PrefixRateGroups::class, 'prefixrate_groups_id');
    }

    public function customInboundGroups(): BelongsToMany
    {
        return $this->belongsToMany(PrefixRateGroups::class, 'pbx_services_prefixrate_groups', 'pbx_service_id', 'prefixrate_group_id')
            ->where('pbx_services_prefixrate_groups.type', PbxServicePrefixRateGroup::TYPE_INBOUND);
    }

    public function customGroups(): BelongsToMany
    {
        return $this->belongsToMany(PrefixRateGroups::class, 'pbx_services_prefixrate_groups', 'pbx_service_id', 'prefixrate_group_id');
    }

    public function customOutboundGroups(): BelongsToMany
    {
        return $this->belongsToMany(PrefixRateGroups::class, 'pbx_services_prefixrate_groups', 'pbx_service_id', 'prefixrate_group_id')
        ->where('pbx_services_prefixrate_groups.type', PbxServicePrefixRateGroup::TYPE_OUTBOUND);

    }

    public function customInboundItems(): HasManyThrough
    {
        return $this->hasManyThrough(CustomRateGroupItems::class, PbxServicePrefixRateGroup::class, 'pbx_service_id', 'prefixrate_id', 'id', 'prefixrate_group_id')
            ->where('type', PbxServicePrefixRateGroup::TYPE_INBOUND);
    }

    public function customOutboundItems(): HasManyThrough
    {
        return $this->hasManyThrough(CustomRateGroupItems::class, PbxServicePrefixRateGroup::class, 'pbx_service_id', 'prefixrate_id', 'id', 'prefixrate_group_id')
            ->where('type', PbxServicePrefixRateGroup::TYPE_OUTBOUND);
    }

    public function appRates(): HasMany
    {
        return $this->hasMany(PbxServicesAppRate::class, 'pbx_service_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'pbx_service_id');
    }

    public function scheduleLogs(): MorphMany
    {
        return $this->morphMany(ScheduleLog::class, 'model');
    }

    public function jobLogs(): HasMany
    {
        return $this->hasMany(PbxJobLog::class, 'pbx_service_id');
    }

    public function getpbxCdrTotals($column = 'cost')
    {
        return $this->pbxCdrTotals()->sum($column);
    }

    public function getCustomOutboundRates($number = false)
    {

        $prefixgroup = PrefixRateGroups::where("id", $number)->wherenull("deleted_at")->first();

        if ($prefixgroup != null) {
            $ids = CustomRateGroupItems::where('prefixrate_id', $number)->wherenull("deleted_at")->get('int_rate_id')->pluck('int_rate_id');
            $test = CustomRate::whereIn('id', $ids)->get();

            return CustomRate::whereIn('id', $ids);
        } else {
            $ids = [];

            return CustomRate::whereIn('id', $ids);
        }
    }

    public function getCustomInboundRates($number = false)
    {

        $prefixgroup = PrefixRateGroups::where("id", $number)->wherenull("deleted_at")->first();

        if ($prefixgroup != null) {
            $ids = CustomRateGroupItems::where('prefixrate_id', $number)->wherenull("deleted_at")->get('int_rate_id')->pluck('int_rate_id');
            $test = CustomRate::whereIn('id', $ids)->get();

            return CustomRate::whereIn('id', $ids);
        } else {
            $ids = [];

            return CustomRate::whereIn('id', $ids);
        }
    }

    public static function updatePbxServices($request, $service)
    {
        $parameters = $request->parameters;
        Log::debug('Request', ['request pbx service' => $parameters]);

        //
        $data = [
            'pbx_package_id' => $parameters['pbx_package_id'],
            //'tax_by' => $parameters['tax_type']['value'],
            'allow_discount' => $parameters['allow_discount'] ? 1 : 0,
            'allow_aditionalcharges' => $parameters['allow_aditionalcharges'] ? 1 : 0,
            'allow_did' => $parameters['allow_did'] ? 1 : 0,
            'allow_extensions' => $parameters['allow_extensions'] ? 1 : 0,
            'allow_items' => $parameters['allow_items'] ? 1 : 0,
            'allow_pbxpackages' => $parameters['allow_pbxpackages'] ? 1 : 0,
            'allow_usagesummary' => $parameters['allow_usagesummary'] ? 1 : 0,
            'allow_customapp' => $parameters['allow_customapp'] ? 1 : 0,
            'auto_suspension' => $parameters['auto_suspension'],
            'only_callrating' => $parameters['only_callrating'],
            'allow_discount_value' => $parameters['allow_discount_value'],
            'allow_discount_type' => $parameters['allow_discount_type'],
            'cap_extension' => $parameters['cap_extension'],
            'cap_total' => $parameters['cap_total'],
            'date_from' => $parameters['date_from'],
            'date_to' => $parameters['date_to'],
            'time_period' => $parameters['time_period'],
            'discount_term_type' => $parameters['time_period_value'],
            'pbx_tenant_id' => $parameters['pbx_tenant_id'],
            'custom_app_rate_id' => $parameters['custom_app_rate_id'],
            'suspension_type' => $parameters['suspension_type'],
            'pbx_services_number' => $parameters['pbx_services_number'],
            'pbxpackages_price' => $parameters['pbxpackages_price'] * 100,
            'discount_val' => $parameters['discount_val'],
            'sub_total' => $parameters['sub_total'],
            'total' => $parameters['total'],
            'total_prorate' => $parameters['total_prorate'],
            'tax' => $parameters['tax'],
            'status' => $parameters['status'],
            'term' => $parameters['term'],
            'date_begin' => $parameters['date_begin'],
            'renewal_date' => $parameters['renewal_date'],
            'call_rating_preview' => $parameters['call_rating_preview'],
            'inclusive_minutes_seconds_consumed' => $parameters['inclusive_minutes_seconds_consumed'],
            'prefixrate_groups_id' => $parameters['prefixrate_groups_id'],
            'prefixrate_groups_outbound_id' => $parameters['prefixrate_groups_outbound_id'],
            'time_period_type' => $parameters['time_period_type'],
            'time_period_value' => $parameters['time_period_type'],
            'addresses_id' => $parameters['addresses_id'],
            'allow_pbx_packages_update' => $parameters['allow_pbx_packages_update'],
            'main_update' => $parameters['main_update'],
        ];
        if ($parameters['taxCdr'] && $parameters['taxCdr']['name'] != 'none') {
            $data['tax_type_id'] = $parameters['taxCdr']['id'];
        }

        \Log::debug("parametros");
        \Log::debug($parameters);
        $pbxService = $service::find($parameters['pbx_services_id']);
        $pbxService->update($data);

        if ($service->status == 'A' && empty($service->date_begin)) {
            $service->date_begin = Carbon::parse($service->created_at)->format('Y-m-d');
            $service->save();
        }

        $pbxtenantcode = 0;
        $pbxtenant = PbxTenant::where("id", $pbxService->pbx_tenant_id)->first();
        if ($pbxtenant != null) {
            $pbxtenantcode = $pbxtenant->code;
        }

        self::deleteAssociations($pbxService);
        self::createItems($parameters, $pbxService);
        self::createTaxes($parameters, $pbxService);

        if ($pbxService->status != 'C') {
            self::createExtensions($parameters, $pbxService, $pbxtenantcode);
            self::createDids($parameters, $pbxService, $pbxtenantcode);
        }

        return $pbxService;
    }

    public static function deleteAssociations($service)
    {
        //Log::debug($service->pbxServiceExtensions()->delete());
        // Se eliminan las extensiones asociados al servicio
        // if ($service->pbxServiceExtensions()->exists()) {
        //     $service->pbxServiceExtensions()->delete();
        // }

        // Se eliminan las dids asociados al servicio
        // if ($service->pbxServiceDids()->exists()) {
        //     $service->pbxServiceDids()->delete();
        // }

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
    }

    public static function deleteExtensionsAndDids($pbxService)
    {
        // Se eliminan las extensiones asociados al servicio
        if ($pbxService->pbxServiceExtensions()->exists()) {
            $pbxService->pbxServiceExtensions()->delete();
        }

        // Se eliminan las dids asociados al servicio
        if ($pbxService->pbxServiceDids()->exists()) {
            $pbxService->pbxServiceDids()->delete();
        }
    }

    public static function createExtensions($c_Pck, $service, $pbxtenantcode)
    {
        $idPbxExtExistente = [];
        foreach ($c_Pck['extensions'] as $ext) {
            //  //Log::debug(".......extre");
            //Log::debug($ext);
            $ext['company_id'] = $service->company_id;
            $ext['creator_id'] = Auth::id();
            $idPbxExt = 0;
            $PbxExt = PbxExtensions::select('id')->where('pbxext_id', '=', $ext['pbxext_id'])->where('pbx_server_id', '=', $ext['pbx_server_id'])->where('pbx_tenant_code', '=', $pbxtenantcode)->get();
            //Log::debug($PbxExt);
            //  $PbxExt = [];
            // data pbx ext
            $paramsExt = [];
            $paramsExt['company_id'] = $service->company_id;
            $paramsExt['creator_id'] = Auth::id();
            $paramsExt['pbx_tenant_id'] = $c_Pck['pbx_tenant_id'];
            $paramsExt['name'] = $ext['name'];
            $paramsExt['email'] = $ext['email'];
            $paramsExt['status'] = $ext['status'];
            $paramsExt['api_id'] = $ext['id'];
            $paramsExt['ext'] = $ext['ext'];
            $paramsExt['linenum'] = $ext['linenum'];
            $paramsExt['location'] = $ext['location'];
            $paramsExt['macaddress'] = $ext['macaddress'];
            $paramsExt['protocol'] = $ext['protocol'];
            $paramsExt['ua_fullname'] = $ext['ua_fullname'];
            $paramsExt['ua_id'] = $ext['ua_id'];
            $paramsExt['ua_name'] = $ext['ua_name'];
            $paramsExt['date_prorate'] = isset($ext['date_prorate']) || null;
            $paramsExt['prorate'] = isset($ext['prorate']) || null;
            $paramsExt['cost_per_day'] = $ext['cost_per_day'];

            $paramsExt['pbxext_id'] = $ext['pbxext_id'];
            $paramsExt['pbx_server_id'] = $ext['pbx_server_id'];
            $paramsExt['pbx_tenant_code'] = $pbxtenantcode;
            if (sizeof($PbxExt) <= 0) {

                $resPbxExt = PbxExtensions::create($paramsExt);
                $idPbxExt = $resPbxExt->id;

            } else {
                /* $result = PbxExtensions::where('api_id', '=', $ext['api_id'])->delete();
                if ($result){
                    $resPbxExt = PbxExtensions::create($paramsExt);
                    $idPbxExt = $resPbxExt->id;
                } */
                $idPbxExt = $PbxExt[0]['id'];
            }

            $ext['pbx_extension_id'] = $idPbxExt;
            /* $paramsExt['date_prorate'] = isset($ext['date_prorate']) || null;
            $paramsExt['prorate'] = isset($ext['prorate']) || null;
            $paramsExt['cost_per_day'] = $ext['cost_per_day']; */
            $service->pbxServiceExtensions()->updateOrCreate(['pbx_extension_id' => $idPbxExt], $ext);
            $idPbxExtExistente[] = $idPbxExt;
        }
        // search val for array $idPbxExtExistente
        $idPbxExtNoExistente = array_diff($service->pbxServiceExtensions->pluck('pbx_extension_id')->toArray(), $idPbxExtExistente);
        // delete val for array $idPbxExtNoExistente
        $service->pbxServiceExtensions()->whereIn('pbx_extension_id', $idPbxExtNoExistente)->delete();
    }

    public static function createDids($c_Pck, $service, $pbxtenantcode)
    {

        $idPbxDidExistente = [];
        foreach ($c_Pck['dids'] as $did) {
            //Log::debug("edicion----");
            //Log::debug($did);
            $did['company_id'] = $service->company_id;
            $did['creator_id'] = Auth::id();
            $idPbxDid = 0;
            $pbxDid = PbxDID::select('id')->where('pbxdid_id', '=', $did['pbxdid_id'])->where('pbx_server_id', '=', $did['pbx_server_id'])->where('pbx_tenant_code', '=', $pbxtenantcode)->get();

            //Log::debug($pbxDid);
            // data pbx did
            $paramsDid = [];
            $paramsDid['company_id'] = $service->company_id;
            $paramsDid['creator_id'] = Auth::id();
            $paramsDid['pbx_tenant_id'] = $c_Pck['pbx_tenant_id'];
            $paramsDid['api_id'] = $did['id'];
            $paramsDid['number'] = $did['number'];
            $paramsDid['server'] = $did['server'];
            $paramsDid['status'] = $did['status'];
            $paramsDid['trunk'] = $did['trunk'];
            $paramsDid['type'] = $did['type'];
            $paramsDid['number2'] = $did['number2'];
            $paramsDid['ext'] = $did['ext'];
            $paramsDid['e164'] = $did['e164'];
            $paramsDid['e164_2'] = $did['e164_2'];

            $paramsDid['pbxdid_id'] = $did['pbxdid_id'];
            $paramsDid['pbx_server_id'] = $did['pbx_server_id'];
            $paramsDid['pbx_tenant_code'] = $pbxtenantcode;
            $paramsDid['name_prefix'] = $did['template_name'];
            /* $paramsDid['date_prorate'] = isset($did['date_prorate']) || null;
            $paramsDid['prorate'] = isset($did['prorate']) || null;
            $paramsDid['cost_per_day'] = $did['cost_per_day']; */

            if (sizeof($pbxDid) <= 0) {
                // guardar did
                $resPbxDid = PbxDID::create($paramsDid);
                $idPbxDid = $resPbxDid->id;

            } else {
                $idPbxDid = $pbxDid[0]['id'];
                /* $result = PbxDID::where('api_id', '=', $did['id'])->delete();
                if ($result){
                    $resPbxDid = PbxDID::create($paramsDid);
                    $idPbxDid = $resPbxDid->id;
                } */
                //Log::debug($result);
            }

            $did['pbx_did_id'] = $idPbxDid;
            $did['name_prefix'] = $did['template_name'];

            if (array_key_exists('custom_did_group_id', $did)) {
                // Should evaluate to FALSE
                if ($did['custom_did_group_id'] == 0) {
                    $did['custom_did_id'] = null;
                } else {
                    $did['custom_did_id'] = $did['custom_did_group_id'];
                }
            }

            //Log::debug($did);
            //Log::debug($idPbxDid);
            $service->pbxServiceDids()->updateOrCreate(['pbx_did_id' => $idPbxDid], $did);
            $idPbxDidExistente[] = $idPbxDid;
        }

        // search val for array $idPbxExtExistente
        $idPbxDidNoExistente = array_diff($service->pbxServiceDids->pluck('pbx_did_id')->toArray(), $idPbxDidExistente);
        // delete val for array $idPbxDidNoExistente
        $service->pbxServiceDids()->whereIn('pbx_did_id', $idPbxDidNoExistente)->delete();

    }

    public static function createItems($c_Pck, $service)
    {
        foreach ($c_Pck['items'] as $item) {
            $item['company_id'] = $service->company_id;
            $item['creator_id'] = Auth::id();
            // print_r($item);

            $service_item = $service->items()->create($item);
            // Taxes for item
            if (array_key_exists('taxes', $item) && $item['taxes']) {
                foreach ($item['taxes'] as $tax) {
                    $tax['company_id'] = $service->company_id;
                    $tax['creator_id'] = Auth::id();
                    if (gettype($tax['amount']) !== "NULL") {
                        $service_item->taxes()->create($tax);
                    }
                }
            }
        }
    }

    public static function createTaxes($c_Pck, $service)
    {
        if (array_key_exists('taxes', $c_Pck) && $c_Pck['taxes']) {
            foreach ($c_Pck['taxes'] as $tax) {

                $tax['company_id'] = $service->company_id;
                $tax['creator_id'] = Auth::id();
                $tax['pbx_services_id'] = $c_Pck['pbx_services_id'];
                // $tax['tax_types_id'] =
                if (gettype($tax['amount']) !== "NULL") {
                    // $tax['amount'] = ( $tax['amount'] * 100);
                    $service->taxes()->create($tax);
                }
            }
        }
    }

    public function tickets(): MorphToMany
    {
        return $this->morphToMany(CustomerTicket::class, 'service_ticket', 'service_tickets', 'service_id', 'customer_ticket_id');
    }

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
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'name';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }

    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }
}
