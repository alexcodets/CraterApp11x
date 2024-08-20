<?php

namespace Crater\Pbxware\Service;

use Carbon\CarbonImmutable;
use Crater\Http\Requests\CustomSearchReportRequest;
use Crater\Models\CallDetailRegister;
use Crater\Models\PbxExtensions;
use Crater\Models\PbxTenantCdr;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class CustomSearchCdrService
{
    public array $serviceFields = ['pbx_services.id', 'pbx_services_number', 'pbx_tenant_id', 'pbx_package_id', 'status'];

    /**
     * @throws Exception
     */
    public function run(CustomSearchReportRequest $request): array
    {
        switch ($request->typeSearch) {
            case 'Tenant':
                return $this->searchByTenant($request);
            case 'Department':
                return $this->searchByDepartment($request);
            case 'Extension':
                return $this->searchByExtension($request);
        }

        return [];
    }

    /**
     * @throws Exception
     */
    public function searchByTenant(CustomSearchReportRequest $request): array
    {
        $tenants = collect($request->tenant)->unique();

        //Log::debug("Entro aqui");
        //Log::debug($tenants);

        $extensions = PbxExtensions::query();
        $extensions->where(function ($query) use ($tenants) {

            $query->where(function ($query) use ($tenants) {
                $query->where('pbx_tenant_id', $tenants[0]['tenantid']);
                $query->where('pbx_server_id', $tenants[0]['pbx_server_id']);
            });
            $tenants->shift();

            foreach ($tenants as $tenant) {
                $query->orWhere(function ($query) use ($tenant) {
                    $query->where('pbx_tenant_id', $tenant['tenantid']);
                    $query->where('pbx_server_id', $tenant['pbx_server_id']);
                });
            }
        });

        //Log::debug("Entro aqui 2");
        $status = $request->get('includeServicesSuspended', 0) ? ['A', 'S'] : ['A'];

        $extensions = $extensions->whereHas('pbxService', function ($query) use ($status) {
            $query->whereIn('status', $status);
        })->with(
            [
                'pbxService.pbxPackage:id,pbx_server_id',
                'pbxService.tenant:id,tenantid,code,pbx_server_id',
                'customSearches:id,name',
                'pbxService' => fn ($query) => $query->whereIn('status', $status)
                    ->select(['pbx_services.id', 'pbx_services_number', 'pbx_tenant_id', 'pbx_package_id', 'status']),
            ]
        )->orderBy('name')->get(['id', 'pbx_server_id', 'pbx_tenant_id', 'name', 'ext']);

        //return [$extensions->dd()];
        return $this->response($extensions);

    }

    /**
     * @throws Exception
     */
    public function searchByExtension(CustomSearchReportRequest $request): array
    {
        $extensionsId = collect($request->extensions)->unique();
        $extensions = PbxExtensions::query();
        $status = $request->get('includeServicesSuspended', 0) ? ['A', 'S'] : ['A'];

        $extensions = $extensions->whereIn('id', $extensionsId)
            ->whereHas('pbxService', function ($query) use ($status) {
                $query->whereIn('status', $status);
            })->with(
                [
                    'pbxService.pbxPackage:id,pbx_server_id',
                    'pbxService.tenant:id,tenantid,code,pbx_server_id',
                    'customSearches:id,name',
                    'pbxService' => fn ($query) => $query->whereIn('status', $status)
                        ->select(['pbx_services.id', 'pbx_services_number', 'pbx_tenant_id', 'pbx_package_id', 'status']),
                ]
            )->orderBy('name')->get(['id', 'pbx_server_id', 'pbx_tenant_id', 'name', 'ext']);

        return $this->response($extensions);
    }

    /**
     * @throws Exception
     */
    public function searchByDepartment(CustomSearchReportRequest $request): array
    {
        $departments = collect($request->departments);

        $extensions = PbxExtensions::query();
        $status = $request->get('includeServicesSuspended', 0) ? ['A', 'S'] : ['A'];

        $extensions = $extensions->whereHas('customSearches', function ($query) use ($departments) {
            $query->whereIn('custom_searches.id', $departments);
        })->whereHas('pbxService', function ($query) use ($status) {
            $query->whereIn('status', $status);
        })->with(
            [
                'pbxService.pbxPackage:id,pbx_server_id',
                'pbxService.tenant:id,tenantid,code,pbx_server_id',
                'customSearches:id,name',
                'pbxService' => fn ($query) => $query->whereIn('status', $status)
                    ->select(['pbx_services.id', 'pbx_services_number', 'pbx_tenant_id', 'pbx_package_id', 'status']),
            ]
        )->orderBy('name')->get(['id', 'pbx_server_id', 'pbx_tenant_id', 'name', 'ext']);

        return $this->response($extensions);

    }

    /**
     * @throws Exception
     */
    private function response(Collection $extensions): array
    {
        $start = CarbonImmutable::createFromFormat('Y-m-d_H:i:s', request()->startDate); //->unix();
        $end = CarbonImmutable::createFromFormat('Y-m-d_H:i:s', request()->endDate); //->unix();
        //2021-08-28_10:12:12
        $days = $start->diffInDays($end);
        $start = $start->unix();
        $end = $end->unix();
        $today = now()->startOfDay()->unix();
        $last24Hours = now()->subHours(24)->unix();

        $response = [];
        $check = [];
        foreach ($extensions as $ext) {
            //return [$ext->pbxService[0]];
            $response['data'][] = $this->getTenantCdrs($ext, $start, $end, $today, $last24Hours, $days);

        }
        //return $extensions->pluck('name')->toArray();

        if ($response === []) {
            if ($extensions->isNotEmpty()) {

                $ext = implode(', ', $extensions->pluck('name')->toArray());

                throw new Exception("Extension: {$extensions->count()} - Ext: {$ext}", Response::HTTP_NOT_FOUND);
            }

            throw new Exception('Not CDR Found', Response::HTTP_NOT_FOUND);
        }

        $response['extensions']['quantity'] = $extensions->count();
        $response['extensions']['ext'] = $extensions->pluck(['name']);

        return $response;
    }

    #Deprecated
    private function check(array &$check, string $key): bool
    {

        if (array_key_exists($key, $check)) {
            return $check[$key];
        }

        return $check[$key] = Schema::hasTable($key);

    }

    #Deprecated
    private function buildQueryResponse(PbxExtensions $ext, CallDetailRegister $cdr, string $start, string $end, string $today, string $last24Hours, int $days): array
    {
        $baseInbound = "case when type = '1' and start_date > ? and start_date < ?";
        $baseOutbound = "case when type = '0' and start_date > ? and start_date < ?";
        $baseGlobal = "case when start_date > ? and start_date < ?";

        //$response = $cdr->where('pbx_extension_id', $ext->id)
        //Inbound
        $response = $cdr->where(function ($query) use ($ext) {
            $query->where('from', $ext->fullName);
            $query->orWhere('to', $ext->fullName);
            $query->orWhere('pbx_extension_id', $ext->id);
        });
        //[$start, $end]

        $response = $response
            ->selectRaw("count(case when type = '1' and start_date > ? then 1 end) as inbound_today", [$today])
            ->selectRaw("count(case when type = '1' and start_date > ? then 1 end) as inbound_last_24", [$last24Hours])
            ->selectRaw("count({$baseInbound} then 1 end) as inbound_calls", [$start, $end])
            ->selectRaw("sum({$baseInbound} then round_duration/60 else 0 end) as inbound_total_time", [$start, $end])
            ->selectRaw("avg({$baseInbound} then round_duration else 0 end) as inbound_time_per_call", [$start, $end])
            ->selectRaw("sum({$baseInbound} then exclusive_cost else 0 end) as inbound_total_cost", [$start, $end])
            ->selectRaw("count({$baseInbound} and status = 'Answered' then 1 end) as inbound_call_answer", [$start, $end])
            ->selectRaw("count({$baseInbound} and status = 'Not Answered' then 1 end) as inbound_call_unanswer", [$start, $end])
            ->selectRaw("count({$baseInbound} and status = 'Busy' then 1 end) as inbound_call_busy", [$start, $end])
            ->selectRaw("count({$baseInbound} and status = 'Failed' then 1 end) as inbound_call_failed", [$start, $end])
            //outbound
            ->selectRaw("count(case when type = '0' and start_date > ? then 1 end) as outbound_today", [$today])
            ->selectRaw("count(case when type = '0' and start_date > ? then 1 end) as outbound_last_24", [$last24Hours])
            ->selectRaw("count({$baseOutbound} then 1 end) as outbound_calls", [$start, $end])
            ->selectRaw("sum({$baseOutbound} then round_duration/60 else 0 end) as outbound_total_time", [$start, $end])
            ->selectRaw("avg({$baseOutbound} then round_duration else 0 end) as outbound_time_per_call", [$start, $end])
            ->selectRaw("sum({$baseOutbound} then exclusive_cost else 0 end) as outbound_total_cost", [$start, $end])
            ->selectRaw("count({$baseOutbound} and status = 'Answered' then 1 end) as outbound_call_answer", [$start, $end])
            ->selectRaw("count({$baseOutbound} and status = 'Not Answered' then 1 end) as outbound_call_unanswer", [$start, $end])
            ->selectRaw("count({$baseOutbound} and status = 'Busy' then 1 end) as outbound_call_busy", [$start, $end])
            ->selectRaw("count({$baseOutbound} and status = 'Failed' then 1 end) as outbound_call_failed", [$start, $end])
            // Global
            ->selectRaw("count(case when start_date > ? then 1 end) as global_today", [$today])
            ->selectRaw("count(case when start_date > ? then 1 end) as global_last_24", [$last24Hours])
            ->selectRaw("count({$baseGlobal} then 1 end) as global_calls", [$start, $end])
            ->selectRaw("sum({$baseGlobal} then round_duration/60 else 0 end) as global_total_time", [$start, $end])
            ->selectRaw("avg({$baseGlobal} then round_duration else 0 end) as global_time_per_call", [$start, $end])
            ->selectRaw("sum({$baseGlobal} then exclusive_cost else 0 end) as global_total_cost", [$start, $end])
            ->selectRaw("count({$baseGlobal} and status = 'Answered' then 1 end) as global_call_answer", [$start, $end])
            ->selectRaw("count({$baseGlobal} and status = 'Not Answered' then 1 end) as global_call_unanswer", [$start, $end])
            ->selectRaw("count({$baseGlobal} and status = 'Busy' then 1 end) as global_call_busy", [$start, $end])
            ->selectRaw("count({$baseGlobal} and status = 'Failed' then 1 end) as global_call_failed", [$start, $end])
            ->first();

        $temporalResponse = $this->getGeneralCall($ext, $cdr, $start, $end, $today, $last24Hours, $days);

        //->selectRaw("sum({$baseOutbound} then cost else 0 end) as outbound_total_total_cost")

        //->first();
        /* @var PbxTenantCdr $response */

        return $this->pretifyResponse($response, $ext, $temporalResponse);

    }

    #Deprecated
    public function getGeneralCall(PbxExtensions $ext, CallDetailRegister $cdr, string $start, string $end, string $today, string $last24Hours, int $days): PbxTenantCdr
    {
        $basegeneral = "case when type = '3' and start_date > ? and start_date < ?";

        $response = PbxTenantCdr::where(function ($query) use ($ext) {
            $query->where('from', $ext->fullName);
            $query->orWhere('to', $ext->fullName);
        })->whereHas('tenant', function ($query) use ($ext) {
            $query->where('tenantid', $ext->pbxService[0]->tenant->tenantid)
                ->where('code', $ext->pbxService[0]->tenant->code)
                ->where('pbx_server_id', $ext->pbxService[0]->tenant->pbx_server_id);
        });

        return $response
            ->selectRaw("count(case when type = '3' and start_date > ? then 1 end) as general_today", [$today])
            ->selectRaw("count(case when type = '3' and start_date > ? then 1 end) as general_last_24", [$last24Hours])
            ->selectRaw("count({$basegeneral} then 1 end) as general_calls", [$start, $end])
            ->selectRaw("sum({$basegeneral} then billing_duration/60 else 0 end) as general_total_time", [$start, $end])
            ->selectRaw("avg({$basegeneral} then billing_duration else 0 end) as general_time_per_call", [$start, $end])
            ->selectRaw("count({$basegeneral} and status = 'Answered' then 1 end) as general_call_answer", [$start, $end])
            ->selectRaw("count({$basegeneral} and status = 'Not Answered' then 1 end) as general_call_unanswer", [$start, $end])
            ->selectRaw("count({$basegeneral} and status = 'Busy' then 1 end) as general_call_busy", [$start, $end])
            ->selectRaw("count({$basegeneral} and status = 'Failed' then 1 end) as general_call_failed", [$start, $end])
            //->selectRaw("sum({$basegeneral} then exclusive_cost else 0 end) as general_total_cost")
            ->first();

    }

    public function getTenantCdrs(PbxExtensions $ext, string $start, string $end, string $today, string $last24Hours, int $days): array
    {

        $baseInbound = "case when type = '0' and start_date > ? and start_date < ?";
        $baseOutbound = "case when type = '1' and start_date > ? and start_date < ?";
        $baseGlobal = "case when start_date > ? and start_date < ?";
        $globalInbound = "case when type = '3' and `to` = ? and start_date > ? and start_date < ?";
        $globalOutbound = "case when type = '3' and `from` = ? and start_date > ? and start_date < ?";
        //[$ext->full_name, $start, $end]
        //$baseGeneral  = "case when type = '2' and start_date > '{$start}' and start_date < '{$end}'";
        //start end
        //[$ext->full_name, $start, $end]


        $response = PbxTenantCdr::where(function ($query) use ($ext) {
            $query->where('from', $ext->fullName);
            $query->orWhere('to', $ext->fullName);
        });

        $response = $response
            //Inbound
            ->selectRaw("count(case when type = '0' and start_date > ? then 1 end) as inbound_today", [$today])
            ->selectRaw("count(case when type = '0' and start_date > ? then 1 end) as inbound_last_24", [$last24Hours])
            ->selectRaw("count({$baseInbound} then 1 end) as inbound_calls", [$start, $end])
            ->selectRaw("sum({$baseInbound} then billing_duration/60 else 0 end) as inbound_total_time", [$start, $end])
            ->selectRaw("avg({$baseInbound} then billing_duration else 0 end) as inbound_time_per_call", [$start, $end])
            ->selectRaw("count({$baseInbound} and status = 'Answered' then 1 end) as inbound_call_answer", [$start, $end])
            ->selectRaw("count({$baseInbound} and status = 'Not Answered' then 1 end) as inbound_call_unanswer", [$start, $end])
            ->selectRaw("count({$baseInbound} and status = 'Busy' then 1 end) as inbound_call_busy", [$start, $end])
            ->selectRaw("count({$baseInbound} and status = 'Failed' then 1 end) as inbound_call_failed", [$start, $end])
            //outbound
            ->selectRaw("count(case when type = '1' and start_date > ? then 1 end) as outbound_today", [$today])
            ->selectRaw("count(case when type = '1' and start_date > ? then 1 end) as outbound_last_24", [$last24Hours])
            ->selectRaw("count({$baseOutbound} then 1 end) as outbound_calls", [$start, $end])
            ->selectRaw("sum({$baseOutbound} then billing_duration/60 else 0 end) as outbound_total_time", [$start, $end])
            ->selectRaw("avg({$baseOutbound} then billing_duration else 0 end) as outbound_time_per_call", [$start, $end])
            ->selectRaw("count({$baseOutbound} and status = 'Answered' then 1 end) as outbound_call_answer", [$start, $end])
            ->selectRaw("count({$baseOutbound} and status = 'Not Answered' then 1 end) as outbound_call_unanswer", [$start, $end])
            ->selectRaw("count({$baseOutbound} and status = 'Busy' then 1 end) as outbound_call_busy", [$start, $end])
            ->selectRaw("count({$baseOutbound} and status = 'Failed' then 1 end) as outbound_call_failed", [$start, $end])
            // Global
            ->selectRaw("count(case when start_date > ? then 1 end) as global_today", [$today])
            ->selectRaw("count(case when start_date > ? then 1 end) as global_last_24", [$last24Hours])
            ->selectRaw("count({$baseGlobal} then 1 end) as global_calls", [$start, $end])
            ->selectRaw("sum({$baseGlobal} then billing_duration/60 else 0 end) as global_total_time", [$start, $end])
            ->selectRaw("avg({$baseGlobal} then billing_duration else 0 end) as global_time_per_call", [$start, $end])
            ->selectRaw("count({$baseGlobal} and status = 'Answered' then 1 end) as global_call_answer", [$start, $end])
            ->selectRaw("count({$baseGlobal} and status = 'Not Answered' then 1 end) as global_call_unanswer", [$start, $end])
            ->selectRaw("count({$baseGlobal} and status = 'Busy' then 1 end) as global_call_busy", [$start, $end])
            ->selectRaw("count({$baseGlobal} and status = 'Failed' then 1 end) as global_call_failed", [$start, $end])
            ->first();

        //Log::debug($response);
        $general = PbxTenantCdr::query()

            //Inbound
            ->selectRaw(
                "count(case when type = '3' and `to` = ? and start_date > ? then 1 end) as inbound_today",
                [$ext->full_name, $today]
            )
            ->selectRaw(
                "count(case when type = '3' and `to` = ? and start_date > ? then 1 end) as inbound_last_24",
                [$ext->full_name, $last24Hours]
            )
            ->selectRaw("count({$globalInbound} then 1 end) as inbound_calls", [$ext->full_name, $start, $end])
            ->selectRaw("sum({$globalInbound} then billing_duration/60 else 0 end) as inbound_total_time", [$ext->full_name, $start, $end])
            ->selectRaw("avg({$globalInbound} then billing_duration else 0 end) as inbound_time_per_call", [$ext->full_name, $start, $end])
            ->selectRaw("count({$globalInbound} and status = 'Answered' then 1 end) as inbound_call_answer", [$ext->full_name, $start, $end])
            ->selectRaw("count({$globalInbound} and status = 'Not Answered' then 1 end) as inbound_call_unanswer", [$ext->full_name, $start, $end])
            ->selectRaw("count({$globalInbound} and status = 'Busy' then 1 end) as inbound_call_busy", [$ext->full_name, $start, $end])
            ->selectRaw("count({$globalInbound} and status = 'Failed' then 1 end) as inbound_call_failed", [$ext->full_name, $start, $end])
            //outbound
            ->selectRaw(
                "count(case when type = '3' and `from` = ? and start_date > ? then 1 end) as outbound_today",
                [$ext->full_name, $today]
            )
            ->selectRaw(
                "count(case when type = '3' and `from` = ? and start_date > ? then 1 end) as outbound_last_24",
                [$ext->full_name, $last24Hours]
            )
            ->selectRaw("count({$globalOutbound} then 1 end) as outbound_calls", [$ext->full_name, $start, $end])
            ->selectRaw("sum({$globalOutbound} then billing_duration/60 else 0 end) as outbound_total_time", [$ext->full_name, $start, $end])
            ->selectRaw("avg({$globalOutbound} then billing_duration else 0 end) as outbound_time_per_call", [$ext->full_name, $start, $end])
            ->selectRaw("count({$globalOutbound} and status = 'Answered' then 1 end) as outbound_call_answer", [$ext->full_name, $start, $end])
            ->selectRaw("count({$globalOutbound} and status = 'Not Answered' then 1 end) as outbound_call_unanswer", [$ext->full_name, $start, $end])
            ->selectRaw("count({$globalOutbound} and status = 'Busy' then 1 end) as outbound_call_busy", [$ext->full_name, $start, $end])
            ->selectRaw("count({$globalOutbound} and status = 'Failed' then 1 end) as outbound_call_failed", [$ext->full_name, $start, $end])
            ->first();

        return $this->pretifyResponse($response, $ext, $general);
    }

    private function pretifyResponse(PbxTenantCdr $response, PbxExtensions $ext, PbxTenantCdr $pbxTenantCdr): array
    {
        return [
            'extension' => $ext->ext,
            'extensionName' => $ext->fullName,

            'service' => $ext->pbxService[0]->pbx_services_number,
            'department' => $ext->customSearches[0]->name ?? 'N/A',
            'items' => [
                'inbound' => [
                    'today' => $response->inbound_today + $pbxTenantCdr->inbound_today,
                    'last24Hours' => $response->inbound_last_24 + $pbxTenantCdr->inbound_last_24,
                    'totalCalls' => $response->inbound_calls + $pbxTenantCdr->inbound_calls,
                    'totalTime' => $response->inbound_total_time + $pbxTenantCdr->inbound_total_time,
                    'timePerCall' => $response->inbound_time_per_call + $pbxTenantCdr->inbound_time_per_call,
                    'totalCost' => 0, //$response->inbound_total_cost + $pbxTenantCdr->inbound_total_cost,
                    'callAnswered' => $response->inbound_call_answer + $pbxTenantCdr->inbound_call_answer,
                    'callUnaswered' => $response->inbound_call_unanswer + $pbxTenantCdr->inbound_call_unanswer,
                    'callBusy' => $response->inbound_call_busy + $pbxTenantCdr->inbound_call_busy,
                    'callFailed' => $response->inbound_call_failed + $pbxTenantCdr->inbound_call_failed,
                ],
                'outbound' => [
                    'today' => $response->outbound_today + $pbxTenantCdr->outbound_today,
                    'last24Hours' => $response->outbound_last_24 + $pbxTenantCdr->outbound_last_24,
                    'totalCalls' => $response->outbound_calls + $pbxTenantCdr->outbound_calls,
                    'totalTime' => $response->outbound_total_time + $pbxTenantCdr->outbound_total_time,
                    'timePerCall' => $response->outbound_time_per_call + $pbxTenantCdr->outbound_time_per_call,
                    'totalCost' => 0, //$response->outbound_total_cost + $pbxTenantCdr->outbound_total_cost,
                    'callAnswered' => $response->outbound_call_answer + $pbxTenantCdr->outbound_call_answer,
                    'callUnaswered' => $response->outbound_call_unanswer + $pbxTenantCdr->outbound_call_unanswer,
                    'callBusy' => $response->outbound_call_busy + $pbxTenantCdr->outbound_call_busy,
                    'callFailed' => $response->outbound_call_failed + $pbxTenantCdr->outbound_call_failed,

                ],

                'global' => [
                    'today' => $response->global_today,
                    'last24Hours' => $response->global_last_24,
                    'totalCalls' => $response->global_calls,
                    'totalTime' => $response->global_total_time,
                    'timePerCall' => $response->global_time_per_call,
                    'totalCost' => $response->global_total_cost,
                    'callAnswered' => $response->global_call_answer,
                    'callUnaswered' => $response->global_call_unanswer,
                    'callBusy' => $response->global_call_busy,
                    'callFailed' => $response->global_call_failed,
                ],
            ],

        ];

    }
}
