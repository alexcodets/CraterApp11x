<?php

namespace Crater\Pbxware\Service;

use Carbon\Carbon;
use Crater\DataObject\CdrsDO;
use Crater\Helpers\Chronometer;
use Crater\Models\CallDetailRegister;
use Crater\Models\PbxServices;
use Crater\Models\User;
use Crater\Pbxware\PbxWareApi;
use Log;

class PbxWareService
{
    public function importCdrsByTrunk(PbxWareApi $api, PbxServices $pbxServices, Carbon $start, Carbon $end, CallDetailRegister $cdr, string $status = '8')
    {
        $chronos = new Chronometer();
        $chronos->start('main');
        $chronos->start('getTrunks');
        $trunksResponse = $api->getTrunks();
        $chronos->end('getTrunks');
        //Log::debug($trunksResponse);
        if ($trunksResponse['success'] == false) {
            return $trunksResponse;
        }
        $trunks = $trunksResponse['data'];

        $limit = '1000';
        $chronos->start('Cdrs');
        $totals = ['insert' =>
            ['inbound' => 0, 'outbound' => 0],
            'search' =>
            ['inbound' => 0, 'outbound' => 0],
            'trunks' => count($trunks),
        ];

        foreach ($trunks as $key => $value) {
            //TODO: Rolback
            $cdrStatus = [];
            $cdrs = [];
            $response = [];
            //$cdrs['trunk'] = $key;

            //first run From and them run To.
            $response = $this->cdrOutboundPage($api, $pbxServices->tenant->code, $start, $end, $key, $limit, $status);

            if ($response['success'] === false) {
                return $response;
            }

            $cdrs = $response['cdrs'];

            $totals['search']['outbound'] += count($cdrs);

            $cdrStatus['from'] = $this->storeCdr($cdrs, $pbxServices, $cdr, 1, $key);

            if ($cdrStatus['from']['success'] === false) {
                return $cdrStatus['from'];
            }

            $totals['insert']['outbound'] += $cdrStatus['from']['insert'];

            $cdrs = [];
            $cdrStatus = [];
            $response = null;

            $response = $this->cdrInboundPage($api, $pbxServices->tenant->code, $start, $end, $key, $limit, $status);

            if ($response['success'] === false) {
                return $response;
            }
            $cdrs = $response['cdrs'];
            $totals['search']['inbound'] += count($cdrs);
            $cdrStatus['to'] = $this->storeCdr($cdrs, $pbxServices, $cdr, 0, $key);

            if ($cdrStatus['to']['success'] === false) {
                return $cdrStatus['to'];
            }

            $totals['insert']['inbound'] += $cdrStatus['to']['insert'];

        }
        $chronos->end('Cdrs');

        $chronos->end('main');

        try {
            $time = [
                'main' => $chronos->formattedExecutionTime('main'),
                'cdrs' => $chronos->formattedExecutionTime('Cdrs'),
                'trunks' => $chronos->formattedExecutionTime('getTrunks'),
                'totals' => $totals,
            ];
        } catch (\Throwable $th) {
            //throw $th;
            //Log::debug($th->getMessage());
            $time = [
                'main' => 0,
                'cdrs' => 0,
                'trunks' => 0,
                'totals' => $totals,
            ];
        }

        return [
            'success' => true,
            'data' => [],
            'time' => $time,
        ];

    }

    /**
     *
     * @param array $cdrs
     * @param \Crater\Models\User $user
     * @return array
     */
    public function storeCdr(array $cdrs, PbxServices $pbxServices, CallDetailRegister $cdrModel, int $type = 0, $trunk = null): array
    {
        try {
            $cdrsDo = new CdrsDO($cdrs);
            $count = 0;
            $columns = ['from' , 'to', 'start_date', 'duration', 'billing_duration', 'cost', 'status', 'unique_id', 'type', 'trunk_id'];
            $values = [];
            foreach ($cdrsDo->cdrs as $cdr) {

                $cdrModel->firstOrCreate([
                    'unique_id' => $cdr['unique_id'],
                    'start_date' => $cdr['start_date'],
                ], [
                    'from' => $cdr['from'],
                    'to' => $cdr['to'],
                    'start_date' => $cdr['start_date'],
                    'duration' => $cdr['duration'],
                    'billing_duration' => $cdr['billing_duration'],
                    'cost' => $cdr['cost'],
                    'status' => $cdr['status'],
                    'unique_id' => $cdr['unique_id'],
                    'type' => $type,
                    'trunk_id' => $trunk,
                    //'user_id'          => $user->id,
                    //company_id => $user->company_id
                ]);

                /*if ($cdrModel->where('unique_id', $cdr['unique_id'])->where('start_date', $cdr['start_date'])->doesntExist()) {
                    $values[] = [$cdr['from'], $cdr['to'], $cdr['start_date'], $cdr['duration'], $cdr['billing_duration'], $cdr['cost'], $cdr['status'], $cdr['unique_id'], $type, $trunk];
                    if (count($values) == 500) {
                        batch()->insert($cdrModel, $columns, $values);
                        $count += 500;
                        $values = [];
                    }
                }¨*/

            }

            /* if (count($values) > 0) {
                 batch()->insert($cdrModel, $columns, $values);
                 $count += count($values);
             }*/


            return ['success' => true, 'insert' => $count];
        } catch (\Throwable $th) {
            //throw $th;
            return [
                'success' => false,
                'message' => $th->getMessage(),
                'code' => $th->getCode(),
            ];
        }
    }

    /**
     *
     * @param \Crater\Pbxware\PbxWareApi $api
     * @return array
     */
    public function getCdrss(PbxWareApi $api, int $tenant, $start, $end): array//
    {
        //Tenant, $starttime, $endtime, user, tenant
        try {

            $trunksResponse = $api->getTrunks();
            if ($trunksResponse['success'] == false) {
                return $trunksResponse;
            }
            $trunks = $trunksResponse['data'];

            $from = $this->cdrOutbound($api, $tenant, $start, $end, $trunks);
            if ($from['success'] == false) {
                return $from;

                return [
                    'success' => false,
                    'message' => $from['message'],
                    'cdrs' =>
                    [
                        'from' => $from,
                    ],
                ];
            }

            $to = $this->cdrInbound($api, $tenant, $start, $end, $trunks);
            if ($to['success'] == false) {
                return [
                    'success' => false,
                    'message' => $to['message'],
                    'cdrs' =>
                    [
                        'from' => $from,
                        'to' => $to,
                    ],
                ];
            }

            return [
                'success' => true,
                'cdrs' =>
                [
                    'from' => $from,
                    'to' => $to,
                ],
            ];

        } catch (\Throwable $th) {
            return [
                'success' => false,
                'message' => $th->getMessage().' - Th getCdrs',
                'code' => $th->getCode(),
            ];
        }

    }

    public function cdrOutbound(PbxWareApi $api, int $tenant, Carbon $start, Carbon $end, $trunks): array
    {
        //saliente-out
        $limit = '1000';
        $status = '8';
        $cdrs = [];
        foreach ($trunks as $key => $value) {
            $response = $this->cdrOutboundPage($api, $tenant, $start, $end, $key, $limit, $status);
            if ($response['success'] === false) {
                return $response;
            }
            $cdrs['trunks'][$key]['cdrs']['from'];
        }

        return ['success' => true, 'cdrs' => $cdrs];
    }

    public function cdrInbound(PbxWareApi $api, int $tenant, Carbon $start, Carbon $end, $trunks): array
    {

        //entrante-in
        $limit = '1000';
        $status = '8';
        $next_page = true;
        $result = [];
        $page = 1;
        $cdrs = [];
        foreach ($trunks as $key => $value) {
            $next_page = true;
            $result = [];
            $page = 1;
            while ($next_page) {
                $apiResponse = $api->getCDR($tenant, $start->format('M-d-Y'), $start->format('H:i:s'), $end->format('M-d-Y'), $end->format('H:i:s'), $limit, $status, $page, ['trunk' => $key]);

                if ($apiResponse['success'] === false) {
                    return $apiResponse;
                }

                $result = $apiResponse['data'];
                $next_page = $result['next_page'] ?? false;
                $cdrs = array_merge($cdrs, $result['csv'] ?? []);
                $page++;
            }
        }

        return ['success' => true, 'cdrs' => $cdrs];
    }

    public function cdrOutboundPage(PbxWareApi $api, int $tenant, Carbon $start, Carbon $end, $trunk, $limit, $status)
    {
        $cdrs = [];
        $next_page = true;
        $result = [];
        $page = 1;
        $current_cdr = [];
        while ($next_page) {
            $apiResponse = $api->getCDR($tenant, $start->format('M-d-Y'), $start->format('H:i:s'), $end->format('M-d-Y'), $end->format('H:i:s'), $limit, $status, $page, ['trunkdst' => $trunk]);

            if ($apiResponse['success'] === false) {
                return $apiResponse;
            }
            $result = $apiResponse['data'];
            $next_page = $result['next_page'] ?? false;
            $current_cdr = array_filter($result['csv'] ?? [], function ($value) {
                return $value[3] !== '0';
            });
            $cdrs = array_merge($cdrs, $current_cdr ?? []);
            $page++;

        }

        return ['success' => true, 'cdrs' => $cdrs];
    }

    public function cdrInboundPage(PbxWareApi $api, int $tenant, Carbon $start, Carbon $end, $trunk, $limit, $status): array
    {

        $cdrs = [];
        $next_page = true;
        $result = [];
        $page = 1;
        $current_cdr = [];
        while ($next_page) {
            $apiResponse = $api->getCDR($tenant, $start->format('M-d-Y'), $start->format('H:i:s'), $end->format('M-d-Y'), $end->format('H:i:s'), $limit, $status, $page, ['trunk' => $trunk]);

            if ($apiResponse['success'] === false) {
                return $apiResponse;
            }
            $result = $apiResponse['data'];
            $next_page = $result['next_page'] ?? false;
            $current_cdr = array_filter($result['csv'] ?? [], function ($value) {
                return $value[3] !== '0';
            });
            $cdrs = array_merge($cdrs, $current_cdr ?? []);
            $page++;
        }

        return ['success' => true, 'cdrs' => $cdrs];
    }
}
