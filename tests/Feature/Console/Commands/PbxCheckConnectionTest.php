<?php

use Crater\Mail\PbxServerNotification;
use Crater\Models\CompanySetting;
use Crater\Models\PbxServers;
use Crater\Models\ScheduleLog;
use Database\Seeders\CountriesTableSeeder;
use Database\Seeders\DefaultSettingsSeeder;
use Database\Seeders\DemoSeeder;
use Database\Seeders\PbxDidAndExtSeeder;
use Database\Seeders\PbxPackageServerSeeder;
use Database\Seeders\StatesSeeder;
use Database\Seeders\UsersTableSeeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->seed([
        UsersTableSeeder::class,
        DefaultSettingsSeeder::class,
        CountriesTableSeeder::class,
        StatesSeeder::class,
        PbxPackageServerSeeder::class,
        PbxDidAndExtSeeder::class,
        DemoSeeder::class,
    ]);

    Storage::fake();
    Mail::fake();
});

test('it tries several times to send email', function () {
    settingsWrong();
    $this->artisan('pbx:checkConnection');
    //Log::debug(ScheduleLog::first());

    expect(ScheduleLog::first())
        ->not->toBeNull()
        ->module_name->toBe('PbxCheckConnectionService')
        ->message->toBe(__('comandos.checkConnection.down'))
        ->data->down->mail->sent->toBeFalse()
        ->lvl->toBe(2);

    $this->artisan('pbx:checkConnection');
    //Log::debug(ScheduleLog::first());

    expect(ScheduleLog::first())
        ->not->toBeNull()
        ->module_name->toBe('PbxCheckConnectionService')
        ->message->toBe(__('comandos.checkConnection.down'))
        ->data->down->mail->sent->toBeFalse()
        ->data->down->mail->tries->toBe(2)
        ->lvl->toBe(2);
});

test('it need a mail and permissions setting to send email', function () {
    //Test that activate_notification and server_notification are required.
    settingsWrong();
    $this->artisan('pbx:checkConnection');
    //Log::debug(ScheduleLog::first());

    expect(ScheduleLog::first())
        ->not->toBeNull()
        ->module_name->toBe('PbxCheckConnectionService')
        ->message->toBe(__('comandos.checkConnection.down'))
        ->data->down->mail->sent->toBeFalse()
        ->data->down->mail->error->toBe(__('comandos.checkConnection.errors.mail.notification_deactivated'))
        ->lvl->toBe(2);

    Mail::assertNothingSent();

    CompanySetting::where('option', '=', 'activate_notification')->update([
        'value' => 1
    ]);
    CompanySetting::where('option', '=', 'server_notification')->update([
        'value' => null
    ]);

    $this->artisan('pbx:checkConnection');
    //Log::debug(ScheduleLog::first());

    expect(ScheduleLog::first())
        ->not->toBeNull()
        ->module_name->toBe('PbxCheckConnectionService')
        ->message->toBe(__('comandos.checkConnection.down'))
        ->data->down->mail->sent->toBeFalse()
        ->data->down->mail->tries->toBe(2)
        ->data->down->mail->error->toBe(__('comandos.checkConnection.errors.mail.no_email'))
        ->lvl->toBe(2);

    Mail::assertNothingSent();

    CompanySetting::where('option', '=', 'server_notification')->update([
        'value' => 'corebill@bill.core'
    ]);

    $this->artisan('pbx:checkConnection');
    //Log::debug(ScheduleLog::first());

    expect(ScheduleLog::first())
        ->not->toBeNull()
        ->module_name->toBe('PbxCheckConnectionService')
        ->message->toBe(__('comandos.checkConnection.down'))
        ->data->down->mail->sent->toBeTrue()
        ->data->down->mail->tries->toBe(3)
        ->lvl->toBe(2);

    Mail::assertSent(PbxServerNotification::class);
});

it('Notifies when the api key is wrong', function () {
    //Also work to test it send email.
    Http::fake([
        'pbxdev.careonecomm.com/*' => Http::response(
            [
                'error' => 'Invalid API key.'
            ],
            500,
            []
        ),
    ]);
    PbxServers::where('id', '>', 0)->update(['api_key' => 123456789]);

    $this->artisan('pbx:checkConnection');
    expect(ScheduleLog::first())
        ->not->toBeNull()
        ->module_name->toBe('PbxCheckConnectionService')
        ->message->toBe(__('comandos.checkConnection.down'))
        ->data->down->mail->sent->toBeTrue()
        ->data->errors->reason_down->toBe('Invalid API key.')
        ->lvl->toBe(2);

    Mail::assertSent(PbxServerNotification::class);
});

test('Only 1 mail is send in 24 hours', function () {
    //per 24 hours
    //Test that activate_notification and server_notification are required.
    failHttp();
    $this->artisan('pbx:checkConnection');
    //Log::debug(ScheduleLog::first());

    expect(ScheduleLog::first())
        ->not->toBeNull()
        ->module_name->toBe('PbxCheckConnectionService')
        ->message->toBe(__('comandos.checkConnection.down'))
        ->data->down->mail->sent->toBeTrue()
        ->lvl->toBe(2);

    $this->artisan('pbx:checkConnection');
    //Log::debug(ScheduleLog::first());

    expect(ScheduleLog::first())
        ->not->toBeNull()
        ->module_name->toBe('PbxCheckConnectionService')
        ->message->toBe(__('comandos.checkConnection.down'))
        ->data->down->mail->sent->toBeTrue()
        ->lvl->toBe(2);

    Mail::assertSent(PbxServerNotification::class, 1);

});

test('that 1 mail is send each 24 hours', function () {

    // Travel to an explicit time...
    $this->travelTo(now()->subHours(25));
    // Return back to the present time...
    failHttp();
    $this->artisan('pbx:checkConnection');
    //Log::debug(ScheduleLog::first());

    expect(ScheduleLog::first())
        ->not->toBeNull()
        ->module_name->toBe('PbxCheckConnectionService')
        ->message->toBe(__('comandos.checkConnection.down'))
        ->data->down->mail->sent->toBeTrue()
        ->lvl->toBe(2);

    $this->travelBack();
    $this->artisan('pbx:checkConnection');
    //Log::debug(ScheduleLog::first());

    expect(ScheduleLog::first())
        ->not->toBeNull()
        ->module_name->toBe('PbxCheckConnectionService')
        ->message->toBe(__('comandos.checkConnection.down'))
        ->data->down->mail->sent->toBeTrue()
        ->data->down->mail->tries->toBe(2)
        ->lvl->toBe(2);

    $this->artisan('pbx:checkConnection');

    Mail::assertSent(PbxServerNotification::class, 2);

});

test('down work when status was already I', function () {
    failHttp();

    PbxServers::where('id', '>', 0)->update(['status' => 'I']);
    $this->artisan('pbx:checkConnection');
    //Log::debug(ScheduleLog::first());

    $this->artisan('pbx:checkConnection');
    //Log::debug(ScheduleLog::first());

    expect(ScheduleLog::first())
        ->not->toBeNull()
        ->module_name->toBe('PbxCheckConnectionService')
        ->data->down->mail->sent->toBeTrue()
        ->message->toBe(__('comandos.checkConnection.down'))
        ->lvl->toBe(2)
        ->and(PbxServers::first())
        ->status->toBe('I');

    Mail::assertSent(PbxServerNotification::class, 1);

});

test('up works when there is not a schedule log', function () {
    goodHttp();

    PbxServers::where('id', '>', 0)->update(['status' => 'I']);
    $this->artisan('pbx:checkConnection');
    //Log::debug(ScheduleLog::first());

    expect(ScheduleLog::first())
        ->not->toBeNull()
        ->module_name->toBe('PbxCheckConnectionService')
        ->data->down->mail->sent->toBeFalse()
        ->data->up->mail->sent->toBeTrue()
        ->message->toBe(__('comandos.checkConnection.up'))
        ->lvl->toBe(2)
        ->and(PbxServers::first())
        ->status->toBe('A');

    Mail::assertSent(PbxServerNotification::class, 1);


});

test('when the server is up again another mail is send and status is change', function () {
    theGoodBadAndEvil();
    $this->artisan('pbx:checkConnection');
    //Log::debug(ScheduleLog::first());

    $this->artisan('pbx:checkConnection');
    //Log::debug(ScheduleLog::first());

    expect(ScheduleLog::first())
        ->not->toBeNull()
        ->module_name->toBe('PbxCheckConnectionService')
        ->data->down->mail->sent->toBeTrue()
        ->data->up->mail->sent->toBeTrue()
        ->message->toBe(__('comandos.checkConnection.up'))
        ->lvl->toBe(2)
        ->and(PbxServers::first())
        ->status->toBe('A');

    Mail::assertSent(PbxServerNotification::class, 2);

});

function settingsWrong()
{
    //PbxServers::where('id', '>', 0)->update(['api_key' => 123456789]);
    CompanySetting::where('option', '=', 'activate_notification')->update([
        'value' => 0
    ]);

    failHttp();
    //pbxware.tenant.configuration
}

function failHttp()
{
    Http::fake([
        'pbxdev.careonecomm.com/*' => Http::response(
            [
                'error' => 'algo fallo'
            ],
            500,
            []
        ),
    ]);
}

function goodHttp()
{
    Http::fake([
        'pbxdev.careonecomm.com/*' => Http::response(
            [
                "server_name" => "pbx6",
            ],
            200,
            []
        ),
    ]);
}

function theGoodBadAndEvil()
{
    Http::fake([
        // Stub a series of responses for endpoints...
        'pbxdev.careonecomm.com/*' => Http::sequence()
            ->push(['error' => 'algo fallo'], 500)
            ->push(["server_name" => "pbx6"], 200)
    ]);

}
