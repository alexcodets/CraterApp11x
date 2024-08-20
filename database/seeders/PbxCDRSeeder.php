<?php

namespace Database\Seeders;

use Crater\Helpers\Chronometer;
use Crater\Models\CallDetailRegister;
use Crater\Models\PbxServices;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PbxCDRSeeder extends Seeder
{
    public function run(): void
    {
        $chronos = new Chronometer();
        $chronos->start('process');

        $chronos->start('set_cdr');
        $service = PbxServices::first();
        $cdr = new CallDetailRegister();
        $cdr->setTable($cdr->firstOrCreateTableFromService($service));
        $chronos->end('set_cdr');

        $chronos->start('cdr');
        $items = json_decode(Storage::disk('seed')->get('cdrs.json'), true);
        $cdr->insert($items);
        $chronos->end('cdr');
        $chronos->start('extra_cdr');

        $items = json_decode(Storage::disk('seed')->get('cdrs-extra.json'), true);

        $chronos->start('chunk');
        foreach (array_chunk($items, 350) as $values) {
            $cdr->insert($values);
        }
        $chronos->end('chunk');
        $chronos->end('extra_cdr');

        $chronos->end('process');
        \Log::debug([
            'Process' => $chronos->formattedExecutionTime('process'),
            'setting_model' => $chronos->formattedExecutionTime('set_cdr'),
            'first_insert' => $chronos->formattedExecutionTime('cdr'),
            'extra_cdr' => $chronos->formattedExecutionTime('extra_cdr'),
            'chunk' => $chronos->formattedExecutionTime('chunk'),
        ]);

    }
}
