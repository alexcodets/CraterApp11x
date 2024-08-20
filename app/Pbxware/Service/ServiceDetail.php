<?php

namespace Crater\Pbxware\Service;

use Carbon\Carbon;
use Crater\Models\CallDetailRegister;
use Crater\Models\CallDetailRegisterTotal;
use Crater\Models\CompanySetting;
use Crater\Models\CustomDidGroup;
use Crater\Models\PbxServices;
use Crater\Models\PbxServicesDID;
use Crater\Models\PbxServicesExtensions;
use Crater\Models\PbxServicesItems;
use Crater\Models\ProfileDidTollFree;
use Illuminate\Support\Facades\Cache;
use Log;

class ServiceDetail
{
    public function getServiceTotals($pbxServiceId): array
    {
        $cdr = new CallDetailRegister();
        $extensionIds = PbxServicesExtensions::with('extension')->where('pbx_service_id', $pbxServiceId)
            ->get()->pluck('extension')->pluck('id');
        $didIds = PbxServicesDID::with('did')->where('pbx_service_id', $pbxServiceId)
            ->get()->pluck('did')->pluck('id');
        $totals = [];
        $didQuery = CallDetailRegisterTotal::whereIn('pbx_did_id', $didIds);
        $extQuery = CallDetailRegisterTotal::whereIn('pbx_extension_id', $extensionIds);
        $totals['inbound'] = [
            'items' => $didQuery->get(),
            'total' => [
                'calls' => $didQuery->sum('calls'),
                'cost' => $didQuery->sum('cost'),
                'duration' => $cdr->getFormattedTimeAttribute($didQuery->sum('duration')),
                'total_duration' => $cdr->getFormattedTimeAttribute($didQuery->sum('total_duration')),
                'did' => $didQuery->count(),
            ],
        ];
        $totals['outbound'] = [
            'items' => $extQuery->get(),
            'total' => [
                'calls' => $extQuery->sum('calls'),
                'cost' => $extQuery->sum('cost'),
                'duration' => $cdr->getFormattedTimeAttribute($extQuery->sum('duration')),
                'total_duration' => $cdr->getFormattedTimeAttribute($extQuery->sum('total_duration')),
                'did' => $extQuery->count(),
            ],
        ];

        return $totals;
    }

    /**
     * @param int $pbx_service_id
     * @return array
     */
    public function getServiceExtensions($pbx_service_id, $limit): array
    {
        /** @var \Illuminate\Support\Collection $serviceExtensions */

        $serviceExtensions = PbxServicesExtensions::join('pbx_extensions', 'pbx_services_extensions.pbx_extension_id', '=', 'pbx_extensions.id')
            ->where('pbx_service_id', '=', $pbx_service_id)
            ->whereNULL('pbx_services_extensions.deleted_at')
            ->orderRelatedData('pbx_extensions', 'id', 'asc')->paginateData($limit);

        $array = $serviceExtensions->toArray();

        $service = PbxServices::findOrFail($pbx_service_id);
        $profile = $service->pbxPackage->profileExtensions;
        $cdr = new CallDetailRegister();

        $extensions = $serviceExtensions->pluck('extension');

        $extensions->map(function ($extension) use ($profile, $cdr, $pbx_service_id) {

            $priceExtension = PbxServicesExtensions::where('pbx_service_id', $pbx_service_id)->where('pbx_extension_id',  $extension->id)->first();

            if ($profile != null) {
                $extension['profile_name'] = $profile->name;
                $extension['profile_rate'] = $profile->rate;
                $extension['price'] = $profile->rate;
            } else {
                $extension['profile_name'] = '';
                $extension['profile_rate'] = 0;
            }

            if($priceExtension != null) {
                $extension['idTablePivot'] = $priceExtension->id;
            }

            $extension['idTablePivot'] = $priceExtension->id;
            if($priceExtension != null && $priceExtension->price != null) {
                // convert string to number
                $extension['price'] = floatval($priceExtension->price);
            } else {
                $extension['price'] = floatval($profile->rate);
            }

            $serviceobje = PbxServicesExtensions::where('pbx_service_id', '=', $pbx_service_id)
            ->where('pbx_extension_id', '=', $extension->id)
            ->whereNULL('deleted_at')
            ->first();

            if ($serviceobje != null) {
                $extension['invoice_prorate'] = $serviceobje->invoiced_prorate;
                $extension->cost_per_day = $serviceobje->cost_per_day;
                $extension->date_prorate = $serviceobje->date_prorate;
                $extension->prorate = $serviceobje->prorate;
            }
            if (isset($extension->totalCalls)) {
                $extension->total_calls = is_object($extension) ? (is_object($extension->totalCalls) ? $extension->totalCalls->calls : 0) : 0;
                $extension->total_minute_round = $cdr->getFormattedTimeAttribute($extension->totalCalls ? $extension->totalCalls->total_duration : 0);
                $extension->total_minutes = $cdr->getFormattedTimeAttribute($extension->totalCalls ? $extension->totalCalls->duration : 0);
            }
            unset($extension->totalCalls);

            return $extension;
        });

        $response['data']['data'] = $extensions;
        $qty = PbxServicesExtensions::where('pbx_service_id', $pbx_service_id)->count();
        $cost = 0;

        if ($profile != null) {
            $cost = $profile->rate;

        }
        $response['totals'] = [
            'extension' => $qty,
            'cost' => $qty * $cost,
        ];

        // pbx_services_extensions (Total)
        $pbx_services_extensions = PbxServicesExtensions::where('pbx_service_id', $pbx_service_id)->get('price');
        $total = 0;
        foreach ($pbx_services_extensions as $extension) {
            $total += $extension['price'];
        }
        $response['pbx_services_extensions'] = [
            'count_extensions' => $qty,
            'total_extensions' => $total,
        ];
        //

        if (isset($array['current_page'])) {
            $response['data']['current_page'] = $array['current_page'];
            $response['data']['first_page_url'] = $array['first_page_url'];
            $response['data']['from'] = $array['from'];
            $response['data']['last_page'] = $array['last_page'];
            $response['data']['links'] = $array['links'];
            $response['data']['next_page_url'] = $array['next_page_url'];
            $response['data']['path'] = $array['path'];
            $response['data']['per_page'] = $array['per_page'];
            $response['data']['prev_page_url'] = $array['prev_page_url'];
            $response['data']['to'] = $array['to'];
            $response['data']['total'] = $array['total'];
        }

        return $response;

    }

    /**
     * @param int $pbx_service_id
     * @return array
     */
    public function getServiceDids($pbx_service_id, $limit): array
    {

        $cdr = new CallDetailRegister();
        $service = PbxServices::find($pbx_service_id);
        $profile = $service->pbxPackage->profileDid;
        $custom_did_groups = [];
        if($profile != null) {
            $custom_did_groups = $service->pbxPackage->profileDid->itemGroups->pluck('id');
        }

        $customDidGroups = CustomDidGroup::where('status', 'A')->whereIn('id', $custom_did_groups)->with('customDids')->get();


        /** @var \Illuminate\Support\Collection $service_did */
        $service_did = PbxServicesDID::join('pbx_did', 'pbx_services_did.pbx_did_id', '=', 'pbx_did.id')
            ->where('pbx_service_id', '=', $pbx_service_id)
            ->whereNULL('pbx_services_did.deleted_at')
            ->orderRelatedData('pbx_did', 'id', 'asc')->paginateData($limit);

        //Log::debug($pbx_service_id);
        $service = PbxServices::findOrFail($pbx_service_id);
        $array = $service_did->toArray();

        $dids = $service_did->pluck('did');

        $dids->map(function ($did) use ($profile, $cdr, $pbx_service_id, $customDidGroups) {

            if ($did != null) {
                if ($profile != null) {
                    $prodileName = $customDidGroups->first(function ($group) use ($did) {
                        return collect($group->customDids)->first(function ($didGroup) use ($did) {
                            return $didGroup->prefijo == (int)$did->number;
                        });
                    });
                    $did['profile_name'] = $prodileName ? $prodileName->name : $profile->name;
                    $did['prodileName'] = $prodileName;
                    $did['profile_rate'] = number_format($profile->did_rate, 2);
                    ;
                    $did['profile_rate2'] = $profile->did_rate;

                } else {
                    $did['profile_name'] = '';
                    $did['profile_rate'] = 0;
                    $did['profile_rate2'] = 0;
                }

                $serviceobje = PbxServicesDID::where('pbx_service_id', '=', $pbx_service_id)
                    ->where('pbx_did_id', '=', $did->id)
                    ->whereNULL('deleted_at')
                    ->first();
                $did['name_prefix'] = "Default Template";
                if ($serviceobje != null) {
                    $did['pbx_service_did_id'] = $serviceobje->id;

                    // validate if the price of the DID is diferent of null, set of the price in the variables profile_rate and profile_rate2

                    if($serviceobje->price != null) {
                        $did['profile_rate'] = number_format($serviceobje->price, 2);
                        $did['profile_rate2'] = $serviceobje->price;
                    } elseif ($serviceobje->custom_did_id != null) {

                        $obj = ProfileDidTollFree::where("id", $serviceobje->custom_did_id)->first();
                        $did['profile_rate'] = number_format($obj->rate_per_minute, 2);
                        $did['profile_rate2'] = $obj->rate_per_minute;
                    }
                    $did['invoice_prorate'] = $serviceobje->invoiced_prorate;
                    $did->cost_per_day = $serviceobje->cost_per_day;
                    $did->date_prorate = $serviceobje->date_prorate;
                    $did->prorate = $serviceobje->prorate;

                    if($serviceobje->name_prefix) {
                        if($serviceobje->name_prefix != null) {
                            $did['name_prefix'] = $serviceobje->name_prefix;
                        }
                    }
                }
                $did->price = $serviceobje->price;

                $did->total_calls = $did->totalCalls->calls ?? 0;
                $did->total_minute_round = $cdr->getFormattedTimeAttribute($did->totalCalls ? $did->totalCalls->total_duration : 0);
                $did->total_minutes = $cdr->getFormattedTimeAttribute($did->totalCalls ? $did->totalCalls->duration : 0);

                unset($did->totalCalls);

                return $did;
            }
        });

        $response['data']['data'] = $dids;
        $qty = PbxServicesDID::where('pbx_service_id', $pbx_service_id)->whereNULL('deleted_at')->count();
        $listdid = PbxServicesDID::where('pbx_service_id', $pbx_service_id)->whereNULL('deleted_at')->get();

        $cost = 0;
        foreach ($listdid as $didp) {

            $obj = ProfileDidTollFree::where("id", $didp->custom_did_id)->first();
            // validate if of the price is null and sum of the value in the variable $cost, if the value is null sum of the price
            // of the template
            if($didp->price != null) {
                $cost = $cost + $didp->price;
            } elseif ($obj != null) {
                $cost = $cost + $obj->rate_per_minute;
            } else {
                $cost = $cost + $profile->did_rate;
            }
        }

        $response['totals'] = [
            'did' => $qty,
            'cost' => $cost,
        ];
        if (isset($array['current_page'])) {
            $response['data']['current_page'] = $array['current_page'];
            $response['data']['first_page_url'] = $array['first_page_url'];
            $response['data']['from'] = $array['from'];
            $response['data']['last_page'] = $array['last_page'];
            $response['data']['links'] = $array['links'];
            $response['data']['next_page_url'] = $array['next_page_url'];
            $response['data']['path'] = $array['path'];
            $response['data']['per_page'] = $array['per_page'];
            $response['data']['prev_page_url'] = $array['prev_page_url'];
            $response['data']['to'] = $array['to'];
            $response['data']['total'] = $array['total'];
        }

        return $response;
    }

    /**
     * @param $pbx_service_id
     * @return mixed
     */
    public function getServiceCdrs(int $pbx_service_id, $filters = null)
    {

        if($filters !== null) {
            $filters = json_decode($filters, true);
        }
        //limit, order, custom, only_custom, page, order_by, start_date, end_date
        /** @var PbxServices $service */
        $service = PbxServices::with('pbxPackage')->find($pbx_service_id);
        if ($service == null) {
            throw new \Exception("the ID: {$pbx_service_id} is not a valid PbxServices id", 404);
        }
        //call_detail_registers
        $cdr = new CallDetailRegister();
        $cdrs = $cdr;

        Cache::lock('get_cdr_'.$pbx_service_id, 10)->block(5, function () use ($cdr, $service, &$cdrs) {
            $cdrs = $cdr->setTable($cdr->firstOrCreateTableFromService($service));
        });
        $cdrs->timeZoneGlobal = $this->getTimeZone($service);


        $initialQuery = $cdrs->selectRaw("SUM(cost) as total_cost")
        ->select('start_date', 'from', 'to', 'status', 'unique_id', 'type', 'trunk_id', 'pbx_did_id', 'pbx_extension_id', 'international_rate_id', 'duration', 'billing_duration', 'round_duration', 'exclusive_cost', 'billed_at')
        ->groupBy('unique_id')
        ->selectRaw("SUM(duration) as total_duration")
        ->selectRaw("SUM(billing_duration) as total_billing_duration")
        ->selectRaw("SUM(round_duration) as total_round_duration")
        ->selectRaw("SUM(exclusive_seconds) as total_exclusive_seconds")
        ->selectRaw("SUM(exclusive_cost) as total_exclusive_cost");

        $perPage = isset($filters['perPage']) ? $filters['perPage'] : 10;
        $response['service_cdrs'] = $initialQuery->when($service->only_callrating == 0, function ($query) {
            return $query->billed();
        })->when(request()->input('custom', 1) == 0, function ($query) {
            return $query->onlyUnCustomRate();
        })->when(request()->input('only_custom', 0) == 1, function ($query) {
            return $query->onlyCustomRate()->with('customRate:id,name,rate_per_minute,prefix');
        })->when(request()->start_date, function ($query) {
            return $query->where('start_date', '>=', Carbon::createFromFormat('Y-m-d_H:i:s', request()->start_date)->unix());
        })->when(request()->end_date, function ($query) {
            return $query->where('start_date', '<=', Carbon::createFromFormat('Y-m-d_H:i:s', request()->end_date)->unix());
        })->when(request()->filters, function ($query) use ($service) {
            $filters = request()->filters;
            $filters = json_decode($filters, true);

            if(isset($filters['from'])) {
                $query->where('from', 'like', '%'.$filters['from'].'%');
            }
            if(isset($filters['to'])) {
                $query->where('to', 'like', '%'.$filters['to'].'%');
            }
            if(isset($filters['status']) && is_array($filters['status']) && count($filters['status']) > 0) {
                $query->whereIn('status', $filters['status']);
            }
            if(isset($filters['id'])) {
                $query->where('unique_id', 'like', '%'.$filters['id'].'%');
            }
            if(isset($filters['cdrType'])) {
                if($filters['cdrType']['value'] != 'all') {
                    $query->where('type', 'like', '%'.$filters['cdrType']['value'].'%');
                }
            }
            if(isset($filters['paid'])) {
                if($filters['paid']['value'] == 'Billed') {
                    $query->where('billed_at', '!=', null);
                }
                if($filters['paid']['value'] == 'Pending') {
                    $query->where('billed_at', null);
                }
            }

            if(isset($filters['type_custom'])) {
                Log::info($filters['type_custom']['value']);
                switch ($filters['type_custom']['value']) {
                    case 1:
                        // Only DCR
                        return $query->where('international_rate_id', '=', null)
                        ->orWhere('pbx_extension_id', '!=', null)
                        ->orWhere('pbx_did_id', '!=', null);

                        break;

                        break;
                    case 2:
                        //only custom.
                        return $query->where('international_rate_id', '!=', null)
                        ->where('pbx_extension_id', '=', null)
                        ->where('pbx_did_id', '=', null);

                        break;
                    case 0:
                    default:
                        //All including custom.
                        return $query;

                        break;
                }
            }
            if(isset($filters['dateRange']) && isset($filters['dateRange']['to']) && isset($filters['dateRange']['from'])) {
                $from = Carbon::createFromFormat('Y-m-d_H:i:s', $filters['dateRange']['from'], $service->timeZoneGlobal)->timestamp;
                $to = Carbon::createFromFormat('Y-m-d_H:i:s', $filters['dateRange']['to'], $service->timeZoneGlobal)->timestamp;

                $query->whereBetween('start_date', [$from, $to]);
            }

            return $query->where('from', 'like', '%'.$filters['from'].'%');
        })
        ->orderData($cdr->getTableNameFromService($service), 'start_date')->paginate($perPage);

        date_default_timezone_set($this->getTimeZone($service));
        $this->getCdrTotals($service, $cdrs, $cdr, $response);

        return $response;
    }

    public function setBackTimeZone()
    {
        $setting = CompanySetting::getSetting('time_zone', request()->header('company'));

        $timezone = config('app.timezone');

        if ($setting && $setting != null && $setting != $timezone) {
            config(['app.timezone' => $setting]);
            date_default_timezone_set($setting);
        }
    }

    public function getTimeZone(PbxServices $service): string
    {

        $seconds = 60 * 20;
        //Cache::forget('pbx_tmz_' . $service->id);
        //return cache()->remember('pbx_tmz_' . $service->id, $seconds, function () use ($service) {
        if ($time = $service->customer->timezone) {
            return $time;
        }

        if ($time = $service->PbxPackage->pbxServer->timezone) {
            return $time;
        }

        return 'UTC';
        //});
    }

    private function getCdrTotals(PbxServices $service, CallDetailRegister $cdrs, CallDetailRegister $cdr, array &$response)
    {

        $cdrTotals = $service->pbxCdrTotals();
        $currentTotalCdrs = $service->pbxCdrTotalsCurrent();
        $oldTotalCdrs = $service->pbxCdrTotalsNotCurrent();
        if (request()->input('only_custom', 0) == 1) {
            $cdrTotals = $cdrTotals->onlyCustomRate();
            $currentTotalCdrs = $currentTotalCdrs->onlyCustomRate();
            $oldTotalCdrs = $oldTotalCdrs->onlyCustomRate();
            $cdrs = $cdrs->onlyCustomRate();
        }
        if (request()->input('custom', 1) == 0 && request()->input('only_custom', 0) == 0) {
            $cdrTotals = $cdrTotals->onlyUnCustomRate();
            $currentTotalCdrs = $currentTotalCdrs->onlyUnCustomRate();
            $oldTotalCdrs = $oldTotalCdrs->onlyUnCustomRate();
            $cdrs = $cdrs->onlyUnCustomRate();
        }

        $current = [
            'cdr' => $currentTotalCdrs->sum('calls'),
            'time' => $cdr->getFormattedTimeAttribute($currentTotalCdrs->sum('total_duration')),
            'cost' => $currentTotalCdrs->sum('cost'),
            'exclusive_cost' => $currentTotalCdrs->sum('exclusive_cost'),
            'exclusive_time' => $cdr->getFormattedTimeAttribute($currentTotalCdrs->sum('exclusive_seconds')),
        ];
        $old = [
            'cdr' => $oldTotalCdrs->sum('calls'),
            'time' => $cdr->getFormattedTimeAttribute($oldTotalCdrs->sum('total_duration')),
            'cost' => $oldTotalCdrs->sum('cost'),
            'exclusive_cost' => $oldTotalCdrs->sum('exclusive_cost'),
            'exclusive_time' => $cdr->getFormattedTimeAttribute($oldTotalCdrs->sum('exclusive_seconds')),
        ];

        $response['totals'] = [
            'cdr' => $cdrs->count(),
            'seconds' => $cdrs->calculated()->sum('round_duration'),
            'current' => $current,
            'old' => $old,
            'all' => [
                'cdr' => $current['cdr'] + $old['cdr'],
                'time' => $cdr->getFormattedTimeAttribute($cdrTotals->sum('total_duration')),
                'cost' => $current['cost'] + $old['cost'],
                'exclusive_cost' => $current['exclusive_cost'] + $old['exclusive_cost'],
                'exclusive_time' => $cdr->getFormattedTimeAttribute($currentTotalCdrs->sum('exclusive_seconds') + $oldTotalCdrs->sum('exclusive_seconds')),
            ],
            'billed_cdr' => $current['cdr'],
            'billed_time' => $current['time'],
            'billed_cost' => $current['exclusive_cost'],
            'billed_seconds' => $currentTotalCdrs->sum('total_duration'),
        ];

        $response['totals'] = array_merge($response['totals'], $this->getInclusive($service, $currentTotalCdrs, $cdr));
    }

    public function getInclusive(PbxServices $service, $cdrTotals, $cdr): array
    {

        if ($service->cap_total == 0) {

            return [
                'total_cost' => $cdrTotals->sum('cost'),
                'inclusive_minutes' => 0,
                'inclusive_minutes_consumed' => 0,
                'overtime' => 0,
            ];
        }

        $time = $cdrTotals->sum('total_duration') / 60;

        if ($service->cap_total > $time) {

            return [
                'total_cost' => 0,
                'inclusive_minutes' => $service->cap_total,
                'inclusive_minutes_consumed' => $time,
                'overtime' => 0,
            ];
        }

        $overtime = $time - $service->cap_total;

        return [
            'inclusive_minutes' => $service->cap_total,
            'inclusive_minutes_consumed' => $service->cap_total,
            'overtime' => $cdr->getFormattedTimeAttribute($overtime * 60),
            'total_cost' => $cdrTotals->sum('exclusive_cost'),
        ];

    }

    public function getServiceOutboundCdrs($pbx_service_id, $limit = 20)
    {
        $service = PbxServices::with('pbxPackage')->find($pbx_service_id);

        return $this->getCdrs($service)->outbound()->paginateData($limit);
    }

    public function getServiceInboundCdrs($pbx_service_id, $limit = 20)
    {
        $service = PbxServices::with('pbxPackage')->find($pbx_service_id);

        return $this->getCdrs($service)->inbound()->paginateData($limit);
    }

    private function getCdrs(PbxServices $service): CallDetailRegister
    {
        $cdr = new CallDetailRegister();

        return $cdr->setTable($cdr->firstOrCreateTableFromService($service));
    }

    /**
     * @param $pbx_service_id
     * @param int $limit
     * @return mixed
     */
    public function getServiceItems($pbx_service_id, $limit = 10)
    {
        return PbxServicesItems::where('pbx_services_id', $pbx_service_id)
            ->with('taxes')
            ->whereNull('deleted_at')
            ->orderData('pbx_services_items', 'id', 'asc')->paginateData($limit);
    }
}
