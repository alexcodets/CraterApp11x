<?php

namespace Crater\Pbxware\Service;

use Crater\Helpers\Chronometer;
use Crater\Models\CallDetailRegister;
use Crater\Models\CallDetailRegisterTotal;
use Crater\Models\CustomRate;
use Crater\Models\PbxServices;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PbxService
{
    private PbxServices $service;

    private $onlyCallRating;

    private $now;

    private Chronometer $chronos;

    public function calculateTotal(PbxServices $service)
    {
        //Service -> Package -> Server
        //11 minutos.
        Cache::lock("calculate_cdr_{$service->id}")->block(10, function () use ($service) {
            \DB::connection()->disableQueryLog();
            $service->is_calculating = true;
            $service->save();
            $this->chronos = new Chronometer();
            $this->chronos->start('Main');
            $this->now = now();
            //$service->load(['tenant:id,name,code', 'customer:id,balance', 'pbxPackage']);
            $this->service = $service;

            $this->onlyCallRating = (object) ['id' => null, 'pbx_extension_id' => null, 'pbx_did_id' => null];
            $cdr = new CallDetailRegister();
            $cdr->setTable($cdr->firstOrCreateTableFromService($service));

            $totalTable = 'call_detail_register_totals';
            $cdrTotal = new CallDetailRegisterTotal();
            $cdrTotal->setTable($totalTable);

            $this->chronos->start('outboundChunk');
            $servicesExt = $this->service->pbxServiceExtensions()->has('extension')->with('extension:id,name,ext,pbx_tenant_id')->get(['id', 'pbx_service_id', 'pbx_extension_id']);


            $internationalRates = $this->service->customOutboundItems()->orderBy('order')->has('customRate')->with('customRate')->get();
            //Log::debug("custom outbound");
            //Log::debug($internationalRates);
            //$internationalRates = $this->service->getCustomOutboundRates($this->service->prefixrate_groups_outbound_id)->orderByLength()->get();

            $cdr->outbound()->uncalculated()->chunkById(350, function ($outbounds) use ($cdrTotal, $cdr, $servicesExt, $internationalRates) {
                //$this->chronos->start('round');
                $values = $this->updateOutboundsGeneric($outbounds, $cdrTotal, $servicesExt, $internationalRates);

                batch()->update($cdr, $values, 'id');

                //$this->chronos->end('round');
                //Log::debug([
                //    'Round_Out' => $this->chronos->totalExecution('round'),
                //]);
            }, $column = 'id');
            //unset($servicesExt);
            unset($internationalRates);
            $this->chronos->end('outboundChunk');

            $this->chronos->start('inboundChunk');
            $servicesDid = $this->service->pbxServiceDids()->has('did')->with('did:id,number,pbx_tenant_id')->get(['id', 'pbx_service_id', 'pbx_did_id']);
            $internationalRates = $this->service->customInboundItems()->orderBy('order')->has('customRate')->with('customRate')->get();
            //$internationalRates = $this->service->getCustomInboundRates($this->service->prefixrate_groups_id)->orderByLength()->get();

            //Log::debug("custom inbount");
            //Log::debug($internationalRates);
            $cdr->inbound()->uncalculated()->chunkById(350, function ($inbounds) use ($cdrTotal, $cdr, $servicesDid, $servicesExt, $internationalRates) {
                //$this->chronos->start('round');
                $values = $this->updateInboundsGeneric($inbounds, $cdrTotal, $servicesDid, $internationalRates, $servicesExt);

                batch()->update($cdr, $values, 'id');
                //$this->chronos->end('round');
                //Log::debug([
                //    'Round_In' => $this->chronos->totalExecution('round'),
                //]);
            }, $column = 'id');
            unset($servicesExt);
            $this->chronos->end('inboundChunk');
            $this->chronos->end('Main');

            $this->service->is_calculating = false;
            $service->save();

            $this->service->save();

            /* //Log::debug([
            'Main' => $this->chronos->totalExecution('Main'),
            'InBound' => $this->chronos->totalExecution('inboundChunk'),
            'OutBound' => $this->chronos->totalExecution('outboundChunk'),
            ]);*/
            return [
                'success' => true,
                'time' => [
                    'Main' => $this->chronos->totalExecution('Main'),
                    'InBound' => $this->chronos->totalExecution('inboundChunk'),
                    'OutBound' => $this->chronos->totalExecution('outboundChunk'),
                ],
            ];
        });

        return ['success' => false];
    }

    public function getCustomRate(Collection $customRates, $cdr)
    {
        return $customRates->first(function ($value) use ($cdr) {

            if ($value->customRate->typecustom === CustomRate::TYPE_CUSTOM_PREFIX) {
                return str_starts_with($cdr->to, $value->customRate->prefix);
            }

            if (is_null($value->customRate->from)) {
                return false;
            }

            if (is_null($value->customRate->to)) {
                return str_contains($cdr->from, $value->customRate->from);
            }

            return str_contains($cdr->from, $value->customRate->from) && str_contains($cdr->to, $value->customRate->to);
        })->customRate ?? null;
    }

    public function updateOutboundsGeneric(Collection $outbounds = null, CallDetailRegisterTotal $cdrTotal, Collection $servicesExt, Collection $customRates)
    {
        $values = [];
        $serviceExt = null;
        $customRate = null;
        // Order = only_callrating -> customRate -> extension
        foreach ($outbounds as $out) {
            $serviceExt = null;
            $customRate = null;
            $number = null;

            //$rate = $customRate->rate_per_minute ?? $this->service->pbxPackage->rate_per_minutes;
            $baseRate = $this->service->pbxPackage->rate_per_minutes;

            if ($this->service->only_callrating) {
                $values[] = $this->updateOutbound($out, $cdrTotal, $this->onlyCallRating, $out->from, $baseRate, null);

                continue;
            }

            $customRate = $this->getCustomRate($customRates, $out);

            if ($customRate !== null) {
                $values[] = $this->updateOutbound($out, $cdrTotal, $this->onlyCallRating, $out->to, $customRate->rate_per_minute, $customRate->id);

                continue;
            }

            $serviceExt = $servicesExt->first(function ($value, $key) use ($out) {
                return $value->extension->full_name == $out->from || $value->extension->full_name == $out->to;
            }) ?? null;

            if ($serviceExt !== null) {
                $values[] = $this->updateOutbound($out, $cdrTotal, $serviceExt, $serviceExt->extension->fullName ?? $out->to, $baseRate, null);

                continue;
            }

            $values[] = $this->billed($out->id);
        }

        return $values;
    }

    public function updateOutbound($out, $cdrTotal, $extension, $number, $rate, $customRateId = null, $type = 1)
    {

        $totalTime = $this->timeUpdate($this->service->pbxPackage->minutes_increments ?? 60, $out->duration, $this->service->pbxPackage->type_time_increment);
        $total = $this->callCost($totalTime, $rate);
        // If the CDR is custom, them the inclusive seconds wont be taken into account.
        $exclusiveTime = $customRateId ? $totalTime : $this->getExclusiveMinutes($totalTime);
        $exclusivePrice = $this->callCost($exclusiveTime, $rate);

        $values = [
            'id' => $out->id,
            'cost' => $total,
            'round_duration' => $totalTime,
            'pbx_extension_id' => $extension->pbx_extension_id,
            'billed_at' => $this->now,
            'exclusive_cost' => $exclusivePrice,
            'exclusive_seconds' => $exclusiveTime,
            'international_rate_id' => $customRateId,
        ];

        $this->updateOrCreateTotals(
            [
                'pbx_extension_id' => $extension->pbx_extension_id,
                'invoice_id' => null,
                'number' => $number,
                'type' => $type,
                'rate' => $rate,
                'pbx_services_id' => $this->service->id,
            ],
            [
                'pbx_extension_id' => $extension->pbx_extension_id,
                'invoice_id' => null,
                'number' => $number,
                'type' => $type,
                'rate' => $rate,
                'pbx_services_id' => $this->service->id,
                'calls' => 1,
                'duration' => $out->billing_duration,
                'total_duration' => $totalTime,
                'cost' => $total,
                'exclusive_cost' => $exclusivePrice,
                'exclusive_seconds' => $exclusiveTime,
                'international_rate_id' => $customRateId,
            ],
            [
                'calls' => \DB::raw('calls + 1 '),
                'duration' => \DB::raw('duration + '.$out->billing_duration),
                'total_duration' => \DB::raw('total_duration + '.$totalTime),
                'cost' => \DB::raw('cost + '.$total),
                'exclusive_cost' => \DB::raw('exclusive_cost + '.$exclusivePrice),
                'exclusive_seconds' => \DB::raw('exclusive_seconds + '.$exclusiveTime),
                'prepaid_check' => 0,
            ],
            $cdrTotal
        );

        return $values;
    }

    public function updateInboundsGeneric(Collection $inbounds = null, CallDetailRegisterTotal $cdrTotal, Collection $servicesDid, Collection $customRates, Collection $servicesExt)
    {
        $values = [];
        $customRate = null;
        $serviceDid = null;
        foreach ($inbounds as $in) {

            $customRate = null;
            $serviceDid = null;
            $number = null;
            $baseRate = $this->service->pbxPackage->rate_per_minutes;

            if ($this->service->only_callrating) {
                $values[] = $this->updateOutbound($in, $cdrTotal, $this->onlyCallRating, $in->to, $baseRate, null);

                continue;
            }

            $customRate = $this->getCustomRate($customRates, $in);

            if ($customRate !== null) {
                $values[] = $this->updateInbound($in, $cdrTotal, $this->onlyCallRating, $in->to, $baseRate, $customRate->id);

                continue;
            }


            $serExtension = $servicesExt->first(function ($value, $key) use ($in) {
                return $value->extension->full_name == $in->from || $value->extension->full_name == $in->to;
            }) ?? null;
            if ($serExtension !== null) {
                $values[] = $this->updateOutbound($in, $cdrTotal, $serExtension, $serExtension->extension->full_name ?? $in->to, $baseRate, null, 0);

                continue;
            }

            $serviceDid = $servicesDid->first(function ($value, $key) use ($in) {
                return $value->did->number == $in->to;
            }) ?? null;

            if ($serviceDid !== null) {
                $values[] = $this->updateInbound($in, $cdrTotal, $serviceDid, $in->to, $baseRate, null);

                continue;
            }

            if ($this->service->pbxPackage->all_cdrs == 1) {
                $values[] = $this->updateInbound($in, $cdrTotal, $this->onlyCallRating, $number ?? $in->to, $baseRate, null);

                continue;
            }

            $values[] = $this->billed($in->id);
        }

        return $values;
    }

    public function updateInbound($in, CallDetailRegisterTotal $cdrTotal, $did, $number, $rate, $customRateId, $type = 0)
    {

        $totalTime = $this->timeUpdate($this->service->pbxPackage->minutes_increments ?? 60, $in->duration, $this->service->pbxPackage->type_time_increment);
        $total = $this->callCost($totalTime, $rate);
        // If the CDR is custom, them the inclusive seconds wont be taken into account.
        $exclusiveTime = $customRateId ? $totalTime : $this->getExclusiveMinutes($totalTime);
        $exclusivePrice = $this->callCost($exclusiveTime, $rate);
        $values = [
            'id' => $in->id,
            'cost' => $total,
            'round_duration' => $totalTime,
            'billed_at' => $this->now,
            'pbx_did_id' => $did->pbx_did_id,
            'exclusive_cost' => $exclusivePrice,
            'exclusive_seconds' => $exclusiveTime,
            'international_rate_id' => $customRateId,
        ];

        $this->updateOrCreateTotals(
            [
                'pbx_did_id' => $did->pbx_did_id,
                'invoice_id' => null,
                'number' => $number,
                'type' => $type,
                'rate' => $rate,
                'pbx_services_id' => $this->service->id,

            ],
            [
                'pbx_did_id' => $did->pbx_did_id,
                'invoice_id' => null,
                'number' => $number,
                'type' => $type,
                'rate' => $rate,
                'pbx_services_id' => $this->service->id,
                'calls' => 1,
                'duration' => $in->billing_duration,
                'total_duration' => $totalTime,
                'cost' => $total,
                'exclusive_cost' => $exclusivePrice,
                'exclusive_seconds' => $exclusiveTime,
                'international_rate_id' => $customRateId,
            ],
            [
                'calls' => \DB::raw('calls + 1 '),
                'duration' => \DB::raw('duration + '.$in->billing_duration),
                'total_duration' => \DB::raw('total_duration + '.$totalTime),
                'cost' => \DB::raw('cost + '.$total),
                'exclusive_cost' => \DB::raw('exclusive_cost + '.$exclusivePrice),
                'exclusive_seconds' => \DB::raw('exclusive_seconds + '.$exclusiveTime),

            ],
            $cdrTotal
        );

        return $values;
    }

    public function timeUpdate(int $timeIncrement, int $duration, string $timeLapse = 'sec')
    {

        if ($timeLapse == 'sec') {
            $temp = ceil($duration / $timeIncrement);
            $time = $timeIncrement * $temp;

            return $time;
        }

        $minuteFraction = ($timeIncrement * 60);
        $temp = ceil($duration / $minuteFraction);
        $time = $minuteFraction * $temp;

        return $time;
    }

    public function callCost($time, $cost, $timeLapse = 'min')
    {
        //Cent to dollar
        //$cost = $cost * 100;
        if ($timeLapse == 'min') {
            return $cost * ($time / 60);
        }

        return $cost * $time;
    }

    private function getExclusiveMinutes($value)
    {

        $this->service->inclusive_minutes_seconds_consumed;
        $result = $this->service->inclusive_minutes_seconds_consumed - $value;

        if ($result >= 0) {
            $this->service->inclusive_minutes_seconds_consumed = $result;

            return 0;
        }

        $this->service->inclusive_minutes_seconds_consumed = 0;

        return $result * -1;
    }

    private function updateOrCreateTotals(array $findValues, array $createValues, array $updateValues, CallDetailRegisterTotal $model)
    {

        $updated = DB::table('call_detail_register_totals')->where($findValues)->update($updateValues);

        if ($updated) {

            return;
        }

        $model->create($createValues);
    }

    public function billed($id)
    {
        return [
            'id' => $id,
            'billed_at' => $this->now,
        ];
    }
}
