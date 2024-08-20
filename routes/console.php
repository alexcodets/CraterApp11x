<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Schedule::command('cache:prune-stale-tags')->hourly();
Schedule::command('estimate:check_status')
    ->dailyAt('02:03')->withoutOverlapping();

Schedule::command('pbx:importCDRs --hours=1')
    ->everyTenMinutes()
    ->environments(App::environment())->withoutOverlapping();

Schedule::command('check:service:renewal_date')
    ->everyMinute()
    ->environments(App::environment())->withoutOverlapping();

Schedule::command('pbx:importCDRs --hours=12')
    ->everySixHours()
    ->environments(App::environment())->withoutOverlapping();

Schedule::command('pbx:importCDRs --hours=24')
    ->dailyAt('01:01')
    ->environments(App::environment())->withoutOverlapping();

Schedule::command('invoices:check_status')
    ->dailyAt('03:02');

Schedule::command('invoice:send:draft')
    ->everyMinute()
    ->environments(App::environment())->withoutOverlapping();

Schedule::command('check:pbx-service:renewal_date')
    ->everyMinute()
    ->environments(App::environment())->withoutOverlapping();

Schedule::command('suspend:service')
    ->dailyAt('02:30')
    ->environments(App::environment());

Schedule::command('Reactive:Pbxservices')
    ->everyMinute()
    ->environments(App::environment())->withoutOverlapping();

Schedule::command('Reactive:Services')
    ->everyMinute()
    ->environments(App::environment())->withoutOverlapping();

Schedule::command('suspend:pbxService')
    ->everyMinute()
    ->environments(App::environment())->withoutOverlapping();

Schedule::command('Invoice:notice')
    ->everyMinute()
    ->environments(App::environment())->withoutOverlapping();

Schedule::command('payments:payment_accounts:reminder')
    ->everyMinute()
    ->environments(App::environment())->withoutOverlapping();

Schedule::command('Invoice:Autodebit')
    ->everyMinute()
    ->environments(App::environment())->withoutOverlapping();

Schedule::command('Invoice:AutoDebitChargeOverdue')
    ->dailyAt('4:14')
    ->environments(App::environment())->withoutOverlapping();

Schedule::command('expense:template-generate')
    ->everyMinute()
    ->environments(App::environment())->withoutOverlapping();

////////////

Schedule::command('pbx:calculateCDRs')
// ->everyThirtyMinutes()
    ->everyFiveMinutes()
    ->environments(App::environment())->withoutOverlapping();

Schedule::command('prepaid:cdrcharge')
// ->everyThirtyMinutes()
    ->everyTenMinutes()
    ->environments(App::environment())->withoutOverlapping();

Schedule::command('SuspendPrepaidServicesPBX')
// ->everyFifteenMinutes()
    ->everyFifteenMinutes()
    ->environments(App::environment())->withoutOverlapping();

Schedule::command('pbx:TenantImportCDRs --hours=1')
    ->hourly()
    ->environments(App::environment())->withoutOverlapping();

Schedule::command('queue:retry all')->everySixHours()->withoutOverlapping();

Schedule::command('pbx:importCDRs:BeforeCreateService')
    ->dailyAt('4:01')
    ->environments(App::environment());

Schedule::command('pbx:TenantImportCDRs --hours=24')
    ->dailyAt('5:01')
    ->environments(App::environment())->withoutOverlapping();

Schedule::command('model:prune')->daily()->withoutOverlapping();
Schedule::command('pbx:updateExtensionStatus')->daily()->withoutOverlapping();
// Schedule::command('pbx:serviceMainUpdate')->daily();

Schedule::command('invoice:generateLateFee')->everyMinute();

Schedule::command('pbx:checkConnection')
    ->hourly()
    ->environments(App::environment())->withoutOverlapping();

Schedule::command('queue:prune-batches --hours=48')
    ->dailyAt('01:20');

Schedule::command('single-use:tenant-from-tenant')
    ->dailyAt('03:19');
//--cancelled=72; for laravel 9+
