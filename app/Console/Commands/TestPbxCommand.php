<?php

namespace Crater\Console\Commands;

use Crater\Helpers\Chronometer;
use Crater\Models\CallDetailRegister;
use Crater\Models\CallDetailRegisterTotal;
use Crater\Models\PbxServices;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class TestPbxCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pbx:Testing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {

        //$service = PbxServices::first();
        $this->pbxImportTesting();

        return 0;
    }

    public function pbxImportTesting()
    {
        $chronos = new Chronometer();
        $chronos->start('Main');
        $cdr = new CallDetailRegister();
        $cdr->setTable($cdr->firstOrCreateTableFromService(PbxServices::first()));

        $chronos->start('First_or');
        $values = [];
        $columns = ['from' , 'to', 'start_date', 'duration', 'billing_duration', 'cost', 'status', 'unique_id', 'type', 'trunk_id'];
        for ($i = 0; $i < 300; $i++) {
            /*
            $cdr->firstOrCreate([
                'unique_id'  => '09001728',
                'start_date' => 123456,
            ], [
                'from'             => 123,
                'to'               => 321,
                'start_date'       => 123456,
                'duration'         => 15,
                'billing_duration' => 15,
                'cost'             => 0,
                'status'           => 8,
                'unique_id'        => '0900172',
                'type'             => 1,
                'trunk_id'         => 1,
                //'user_id'          => $user->id,
                //company_id => $user->company_id
            ]);
            */

            if ($cdr->where('unique_id', '0900172')->where('start_date', 123456)->count() == 0) {
                $values[] = [123, 321, 123456, 15, 15, 0, 8, '0900172', 1, 1];
            }
        }

        $this->info(count($values));
        if ($values) {
            batch()->insert($cdr, $columns, $values);
        }



        $chronos->end('First_or');
        $chronos->end('Main');
        //Log::debug($chronos->formattedExecutionTime('First_or'));
        $this->info($chronos->formattedExecutionTime('First_or'));
        //Log::debug('--------');
        $this->info('-------');
        //Log::debug($chronos->formattedExecutionTime('Main'));
        $this->info($chronos->formattedExecutionTime('Main'));


    }
    /*--PbxCalculate Testion

    public function handle(): int
    {
        $chronos = new Chronometer;
        $chronos->start('Main');
        $service = PbxServices::first();
        $cdr     = new CallDetailRegister();
        $cdr->setTable($cdr->firstOrCreateTableFromService($service));
        //$out = $cdr->first();
        $this->info('Start Now');


        $chronos->start('round');
        $cdr->outbound()->chunkById(300, function ($outbounds) use ($service, $chronos) {
            $this->updateOutboundsOnlyCr($outbounds);
        }, $column = 'id');

        $chronos->end('round');
        $this->info('Round ----------------------------');
        $this->info($chronos->formattedExecutionTime('round'));

        $chronos->start('round');
        $cdr->inbound()->chunkById(300, function ($outbounds) use ($service, $chronos) {
            $this->updateOutboundsOnlyCr($outbounds);
        }, $column = 'id');

        $chronos->end('round');
        $this->info('Round ----------------------------');
        $this->info($chronos->formattedExecutionTime('round'));

        $chronos->end('Main');
        $this->info($chronos->formattedExecutionTime('Main'));
        $this->info($chronos->totalExecution('Main'));
        return 0;
    }

    public function updateOutboundsOnlyCr(Collection $outbounds = null)
    {
        foreach ($outbounds as $out) {
            $cdrTotal = CallDetailRegisterTotal::firstOrNew(['pbx_did_id' => null, 'invoice_id' => null, 'number' => 'Favour Ugochi (1111)'], ['type' => 0]);

            if ($cdrTotal->calls > 0) {
                $cdrTotal->duration          = $cdrTotal->duration;
                $cdrTotal->total_duration    = $cdrTotal->total_duration;
                $cdrTotal->cost              = $cdrTotal->cost;
                $cdrTotal->exclusive_cost    = $cdrTotal->exclusive_cost;
                $cdrTotal->exclusive_seconds = $cdrTotal->exclusive_seconds;
            }

            $cdrTotal->calls = $cdrTotal->calls;
            $cdrTotal->save();

            $out->update([
                'cost'              => $out->cost,
                'round_duration'    => $out->round_duration,
                'pbx_extension_id'  => $out->pbx_extension_id,
                'billed_at'         => $out->billed_at,
                'exclusive_cost'    => $out->exclusive_cost,
                'exclusive_seconds' => $out->exclusive_seconds,
            ]);
        }
        return true;
    }

    public function FunctionName()
    {

        for ($i = 0; $i < 9250; $i++) {
            //now();
            $cdrTotal = CallDetailRegisterTotal::firstOrNew(['pbx_did_id' => null, 'invoice_id' => null, 'number' => 'Favour Ugochi (1111)'], ['type' => 0]);

            if ($cdrTotal->calls > 0) {
                $cdrTotal->duration          = $cdrTotal->duration;
                $cdrTotal->total_duration    = $cdrTotal->total_duration;
                $cdrTotal->cost              = $cdrTotal->cost;
                $cdrTotal->exclusive_cost    = $cdrTotal->exclusive_cost;
                $cdrTotal->exclusive_seconds = $cdrTotal->exclusive_seconds;
            }

            $cdrTotal->calls = $cdrTotal->calls;
            $cdrTotal->save();

            $out->update([
                'cost'              => $out->cost,
                'round_duration'    => $out->round_duration,
                'pbx_extension_id'  => $out->pbx_extension_id,
                'billed_at'         => $out->billed_at,
                'exclusive_cost'    => $out->exclusive_cost,
                'exclusive_seconds' => $out->exclusive_seconds,
            ]);
        }

        $chronos->end('Main');
        $this->info($chronos->formattedExecutionTime('Main'));
        $this->info($chronos->totalExecution('Main'));
        return 0;
    }
    */
}
