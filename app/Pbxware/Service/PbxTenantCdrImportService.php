<?php

namespace Crater\Pbxware\Service;

use Carbon\Carbon;
use Crater\DataObject\CdrsDO;
use Crater\Helpers\Chronometer;
use Crater\Models\PbxCdrTenant;
use Crater\Models\PbxTenantCdr;
use Crater\Pbxware\PbxWareApi;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class PbxTenantCdrImportService
{
    public PbxCdrTenant $tenant;

    /**
     * @throws Throwable
     */
    public function import(PbxCdrTenant $tenant, PbxWareApi $api, Carbon $start, Carbon $end, $id, string $status = '8')
    {
        //Log::debug("Import Start");
        $this->tenant = $tenant;
        $this->id = $id;

        if ($this->checkCdrExist($api, $start, $end, $status) === false) {
            //Log::debug("CheckCdr - There is not CDR in the timelapese {$start->toDateTimeString()} - {$end->toDateTimeString()} for status: {$status}");
            return;
        }

        $chronos = new Chronometer();

        $chronos->start('Main');
        //$chronos->start('Trump');
        $limit = '1000';

        $trunksResponse = $api->getTrunks();
        $cacheKey = "PTImportCdrs_{$id}_trunk";
        //Log::debug($trunksResponse);
        if (! $trunksResponse['success']) {
            return $trunksResponse;
        }

        $trunks = $trunksResponse['data'];

        $actualTrunk = Cache::get($cacheKey, false);

        if ($actualTrunk !== false) {
            $key = array_search($actualTrunk, array_keys($trunks), true);
            if ($key !== false) {
                $trunks = array_slice($trunks, $key, null, true);
            }
        }

        //Log::debug("Before Trump");
        $chronos->end('Trump');

        $chronos->start('InOut');
        $outTotal = ['stored' => 0, 'total' => 0];
        $inTotal = ['stored' => 0, 'total' => 0];

        foreach ($trunks as $key => $value) {
            //TODO: Rolback
            //$cdrs['trunk'] = $key;
            Cache::put($cacheKey, $key, $seconds = '3600');

            //first run From and them run To.

            try {
                //Log::debug("------------- The work is starting ------------------------------");
                //Log::debug("Time for Job: {$id} - Status {$status} - Time Start: {$start->toDateTimeString()} - Time End {$end->toDateTimeString()}");

                $chronos->start('Out');
                $outStores = $this->outboundMain($api, $start, $end, $key, $status, $limit);
                $chronos->end('Out');
                //Log::debug("Outbound Calls time: {$chronos->formattedExecutionTime('Out')}");
                //Log::debug($outStores);
                $outTotal['stored'] += $outStores['stored'];
                $outTotal['total'] += $outStores['total'];

                $chronos->start('In');
                $inStores = $this->inboundMain($api, $start, $end, $key, $status, $limit);
                $chronos->end('In');
                //Log::debug("Inbound Calls time: {$chronos->formattedExecutionTime('In')}");
                //Log::debug($inStores);
                $inTotal['stored'] += $outStores['stored'];
                $inTotal['total'] += $outStores['total'];
            } catch (Throwable $th) {
                Log::error($th->getMessage());

                throw $th;
            }
        }

        $chronos->end('InOut');

        try {
            $chronos->start('Other');

            $otherStores = $this->otherMain($api, $start, $end, $status, $limit);

            $chronos->end('Other');
            //Log::debug("Other Calls time: {$chronos->formattedExecutionTime('Other')}");
        } catch (Throwable $th) {
            throw $th;
        }
        $chronos->end('Main');
        /*//Log::debug(

            [
                'InBound'    => array_merge($inTotal),
                'OuBound'    => array_merge($outTotal),
                'OtherBound' => array_merge($otherStores),
                'times'      => [
                    'inAndOut'  => $chronos->formattedExecutionTime('InOut'),
                    'otherTime' => $chronos->formattedExecutionTime('Other'),
                    'TotalTime' => $chronos->formattedExecutionTime('Main'),
                ],

            ]
        );*/
        //Log::debug("Total Process time: {$chronos->formattedExecutionTime('Main')}");

        return true;
    }

    public function inboundMain(PbxWareApi $api, Carbon $start, Carbon $end, $key, string $status = '8', $limit = 1000): array
    {
        $cacheKey = "PTImportCdrs_{$this->id}_trunk_{$key}_Inpage}";
        $nextPage = true;
        $page = Cache::get($cacheKey, 1);
        $cdrModel = new PbxTenantCdr();

        $total = [
            'stored' => 0,
            'total' => 0,
            'pages' => 0,
        ];

        do {

            [$cdrs, $nextPage, $page] = $this->inLine($api, $start, $end, $key, $limit, $status, $page);
            $total['stored'] += $this->storeCdr($cdrs, $cdrModel, 0);
            $total['total'] += count($cdrs);
            $total['pages'] += $nextPage ? 1 : 0;
            Cache::put($cacheKey, $page, $seconds = '3600');
        } while ($nextPage);

        Cache::forget($cacheKey);
        $total['pages'] += $total['total'] > 0 ? 1 : 0;

        return $total;
    }

    public function inLine(PbxWareApi $api, Carbon $start, Carbon $end, $trunk, $limit, $status, $page = 1)
    {

        $apiResponse = $api->getCDR($this->tenant->code, $start->format('M-d-Y'), $start->format('H:i:s'), $end->format('M-d-Y'), $end->format('H:i:s'), $limit, $status, $page, ['trunk' => $trunk]);

        if ($apiResponse['success'] === false) {
            throw new Exception($apiResponse['error'] ?? $apiResponse['message'], $apiResponse['code'] ?? null);
        }
        $result = $apiResponse['data'];
        $nextPage = $result['next_page'] ?? false;
        $cdrs = array_filter($result['csv'] ?? [], function ($value) {
            return $value[3] !== '0';
        });
        $page = $nextPage ? $page++ : $page;

        return [$cdrs, $nextPage, $page];
    }

    public function outboundMain(PbxWareApi $api, Carbon $start, Carbon $end, $key, string $status = '8', $limit = 1000): array
    {

        $cacheKey = "PTImportCdrs_{$this->id}_trunk_{$key}_Outpage}";
        $nextPage = true;
        $page = Cache::get($cacheKey, 1);
        $cdrModel = new PbxTenantCdr();
        $total = [
            'stored' => 0,
            'total' => 0,
            'pages' => 0,
        ];

        //Log::debug("Inside outboundMain");

        do {

            [$cdrs, $nextPage, $page] = $this->outLine($api, $start, $end, $key, $limit, $status, $page);
            $total['stored'] += $this->storeCdr($cdrs, $cdrModel, 1);
            $total['total'] += count($cdrs);
            $total['pages'] += $nextPage ? 1 : 0;
            Cache::put($cacheKey, $page, $seconds = '3600');
        } while ($nextPage);

        $total['pages'] += $total['total'] > 0 ? 1 : 0;

        Cache::forget($cacheKey);

        return $total;
    }

    public function outLine(PbxWareApi $api, Carbon $start, Carbon $end, $trunk, $limit, $status, $page = 1): array
    {
        $cdrs = [];
        $nextPage = true;
        $result = [];

        $apiResponse = $api->getCDR($this->tenant->code, $start->format('M-d-Y'), $start->format('H:i:s'), $end->format('M-d-Y'), $end->format('H:i:s'), $limit, $status, $page, ['trunkdst' => $trunk]);

        if ($apiResponse['success'] === false) {
            throw new Exception($apiResponse['error'] ?? $apiResponse['message'], $apiResponse['code'] ?? null);
        }

        $result = $apiResponse['data'];
        $nextPage = $result['next_page'] ?? false;
        //Log::debug("message");
        //Log::debug($result);
        $cdrs = array_filter($result['csv'] ?? [], function ($value) {
            return $value[3] !== '0';
        });
        $page = $nextPage ? $page++ : $page;

        return [$cdrs, $nextPage, $page];
    }

    /**
     *
     * @param array $cdrs
     * @param \Crater\Models\User $user
     * @return bool
     */
    public function storeCdr(array $cdrs, PbxTenantCdr $cdrModel, int $type = 0, $trunk = null): int
    {
        try {
            $stored = 0;
            //Log::debug("Inside Store");
            //Log::debug($cdrs);

            $cdrsDo = (new CdrsDO($cdrs))->cdrs;
            foreach ($cdrsDo as $cdr) {
                //Log::debug("Taka");
                $check = PbxTenantCdr::where('unique_id', $cdr['unique_id'])->where('start_date', $cdr['start_date'])
                    ->exists();

                if ($check === false) {
                    PbxTenantCdr::create([
                        'unique_id' => $cdr['unique_id'],
                        'start_date' => $cdr['start_date'],
                        'duration' => $cdr['duration'],
                        'from' => $cdr['from'],
                        'to' => $cdr['to'],
                        'billing_duration' => $cdr['billing_duration'],
                        'cost' => $cdr['cost'],
                        'status' => $cdr['status'],
                        'unique_id' => $cdr['unique_id'],
                        'type' => $type,
                        'trunk_id' => $trunk,
                        'pbx_cdr_tenant_id' => $this->tenant->id,

                    ]);
                    $stored++;
                }
            }

            return $stored;
        } catch (Throwable $th) {
            throw $th;
        }
    }

    public function otherMain(PbxWareApi $api, Carbon $start, Carbon $end, string $status = '8', $limit = 1000): array
    {
        $cacheKey = "PTImportCdrs_{$this->id}_Otherpage}";
        $nextPage = true;
        $page = Cache::get($cacheKey, 1);
        $cdrModel = new PbxTenantCdr();
        $total = [
            'stored' => 0,
            'total' => 0,
            'pages' => 0,
        ];

        do {

            [$cdrs, $nextPage, $page] = $this->otherLine($api, $start, $end, $limit, $status, $page);
            $total['stored'] += $this->storeCdr($cdrs, $cdrModel, 3);
            $total['total'] += count($cdrs);
            $total['pages'] += $nextPage ? 1 : 0;
            Cache::put($cacheKey, $page, $seconds = '3600');
        } while ($nextPage);

        $total['pages'] += $total['total'] > 0 ? 1 : 0;

        Cache::forget($cacheKey);

        return $total;
    }

    public function otherLine(PbxWareApi $api, Carbon $start, Carbon $end, $limit, $status, $page = 1)
    {
        $cdrs = [];
        $nextPage = true;
        $result = [];

        $apiResponse = $api->getCDR($this->tenant->code, $start->format('M-d-Y'), $start->format('H:i:s'), $end->format('M-d-Y'), $end->format('H:i:s'), $limit, $status, $page);

        if ($apiResponse['success'] === false) {
            //Log::debug("OtherLine ApiResponse - {$apiResponse}");
            throw new Exception($apiResponse['error'] ?? $apiResponse['message'], $apiResponse['code'] ?? null);
        }

        $result = $apiResponse['data'];
        //Log::debug("Limit: {$result['limit']}");
        //Log::debug("records: {$result['records']}");

        $nextPage = $result['next_page'] ?? false;
        $cdrs = array_filter($result['csv'] ?? [], function ($value) {
            return $value[3] !== '0';
        });
        $page = $nextPage ? $page++ : $page;

        return [$cdrs, $nextPage, $page];
    }

    public function checkCdrExist(PbxWareApi $api, Carbon $start, Carbon $end, string $status = '8'): bool
    {
        $page = 1;
        $limit = 2;
        $apiResponse = $api->getCDR($this->tenant->code, $start->format('M-d-Y'), $start->format('H:i:s'), $end->format('M-d-Y'), $end->format('H:i:s'), $limit, $status, $page);

        if ($apiResponse['success'] === false) {
            throw new Exception($apiResponse['error'] ?? $apiResponse['message'], $apiResponse['code'] ?? null);
        }

        if ($apiResponse['data']['csv'] == null || $apiResponse['data']['records'] == 0) {
            return false;
        }

        return true;

    }
}
