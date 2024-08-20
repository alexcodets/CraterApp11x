<?php

use Crater\Http\Controllers\V1\Modules\AvalaraModuleController;
use Crater\Http\Controllers\V1\Modules\ModuleController;

Route::get('/{name}/check', [ModuleController::class, 'index'])->name('general');
Route::get('/check-modules', [ModuleController::class, 'getModules']);
Route::get('/avalara/full_check', [AvalaraModuleController::class, 'index'])->name('avalaraFull');
