<?php

use Illuminate\Support\Facades\Facade;

return [


    'aliases' => Facade::defaultAliases()->merge([
        'Batch' => Mavinoo\Batch\BatchFacade::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
        'Flash' => Laracasts\Flash\Flash::class,
        'Pusher' => Pusher\Pusher::class,
        'PDF' => Barryvdh\DomPDF\Facade::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
    ])->toArray(),

];
