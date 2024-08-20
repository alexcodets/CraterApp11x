<?php

namespace Crater\Pbxware\Service;

use Carbon\Carbon;
use Crater\Models\CallDetailRegister;
use Crater\Models\PbxServers;
use Crater\Models\PbxServices;
use Crater\Models\User;
use Crater\Traits\ModelFormatingTime;
use Illuminate\Support\Facades\Cache;

class PbxCdrService
{
    use ModelFormatingTime;

    public function get(PbxServices $pbxService, array $data): array
    {
        //limit, order, custom, only_custom, page, order_by, start_date, end_date
        $pbxService = $pbxService->load(['pbxPackage', 'pbxPackage.pbxServer', 'customer']);
        //call_detail_registers
        $cdr = new CallDetailRegister();
        $cdrs = $cdr;

        Cache::lock('get_cdr_'.$pbxService->id, 6)->block(4, function () use ($cdr, $pbxService, &$cdrs) {
            $cdrs = $cdr->setTable($cdr->firstOrCreateTableFromService($pbxService));
        });

        $timeZone = $this->getTimeZone($pbxService->customer, $pbxService->pbxPackage->pbxServer);
        $cdrs->timeZoneGlobal = $timeZone;

        $cdrQuery = $this->getBaseQuery($cdrs);
        $response['items'] = ($this->applyFilter($cdrQuery, $pbxService))->orderData($cdr->getTableNameFromService($pbxService))
        ->paginateData(10);

        $response['totals'] = $this->getTotals($this->applyFilter($cdrs, $pbxService));

        date_default_timezone_set($timeZone);

        return $response;
        //1630617444
        //1630617461
    }

    public function getTimeZone(User $customer, PbxServers $pbxServer): string
    {

        if ($time = $customer->timezone) {
            return $time;
        }

        return $pbxServer->timezone ?? 'UTC';

    }

    public function getTotals($query): array
    {
        return [
            'quantity' => $query->count(),
            'time' => $this->getFormattedTimeAttribute($query->sum('round_duration')),
            // 'cost'     => number_format($query->sum('exclusive_cost'), 5),
            'cost' => $query->sum('exclusive_cost'),
        ];
    }

    private function getBaseQuery($cdr)
    {
        return $cdr->selectRaw("SUM(cost) as total_cost")
            ->select(['start_date', 'from', 'to', 'status', 'unique_id', 'type', 'trunk_id', 'pbx_did_id', 'pbx_extension_id', 'international_rate_id', 'duration', 'billing_duration', 'round_duration', 'exclusive_cost', 'billed_at'])
            ->groupBy('unique_id')
            ->selectRaw("SUM(duration) as total_duration")
            ->selectRaw("SUM(billing_duration) as total_billing_duration")
            ->selectRaw("SUM(round_duration) as total_round_duration")
            ->selectRaw("SUM(exclusive_seconds) as total_exclusive_seconds")
            ->selectRaw("SUM(exclusive_cost) as total_exclusive_cost");
    }

    public function applyFilter($cdrs, $pbxService)
    {
        return $cdrs
        ->when(request()->start_date, function ($query) {
            return $query->where('start_date', '>=', Carbon::createFromFormat('Y-m-d_H:i:s', request()->start_date)->unix());
        })
        ->when(request()->end_date, function ($query) {
            return $query->where('start_date', '<=', Carbon::createFromFormat('Y-m-d_H:i:s', request()->end_date)->unix());
        })
        ->when(request()->from, function ($query) {
            return $query->where('from', 'like', '%'.request()->from.'%');
        })
        ->when(request()->to, function ($query) {
            return $query->where('to', 'like', '%'.request()->to.'%');
        })
        ->when(request()->id, function ($query) {
            return $query->where('unique_id', 'like', '%'.request()->id.'%');
        })
        ->when(request()->status, function ($query) {
            return $query->whereIn('status', array_map('ucfirst', explode(',', request()->status)));
        })

        ->when(request()->cdrType, function ($query) {
            $option = intval(request()->cdrType);

            switch ($option) {
                case 2:
                    //inbound
                    return $query->where('type', 0);
                case 1:
                    //ouboutn
                    return $query->where('type', 1);
                case 3:
                default:
                    //All including custom.
                    return $query;
            }

        })->when(request()->type_custom, function ($query) {
            switch (request()->type_custom) {
                case 1:
                    //All but custom.
                    return $query->onlyUnCustomRate();
                case 2:
                    //only custom.
                    return $query->onlyCustomRate()->with('customRate:id,name,rate_per_minute,prefix');
                case 3:
                default:
                    //All including custom.
                    return $query;
            }

        })->when(request()->check, function ($query) {
            if (request()->check === 'all') {
                return $query;
            }

            return request()->check === 'calculated' ? $query->calculated() : $query->unCalculated();
        })->when(request()->paid, function ($query) use ($pbxService) {

            switch (request()->paid) {
                case 'billed':
                    if ($pbxService->only_callrating == 0) {
                        return $query->billed();
                    }

                    return $query->calculated();

                case 'pending':
                    if ($pbxService->only_callrating == 0) {
                        return $query->pending();
                    }

                    return $query->unCalculated();

                case 'all':
                default:
                    return $query;
            }
        });

    }
}
