<?php

namespace Tests\Feature\Console\Commands;

use Crater\Mail\PbxUpdateExtensionStatusMail;
use Crater\Models\Company;
use Crater\Models\PbxExtensions;
use Crater\Models\PbxServers;
use Crater\Models\ScheduleLog;
use Database\{Seeders\PbxDidAndExtSeeder, Seeders\PbxPackageServerSeeder, Seeders\UsersTableSeeder};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PbxUpdateExtensionStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            UsersTableSeeder::class,
            PbxPackageServerSeeder::class,
            PbxDidAndExtSeeder::class,
        ]);

        Mail::fake();
    }

    public function test_it_requires_credentials_to_work(): void
    {
        PbxServers::where('id', '>', 0)->update(['api_key' => 123456789]);
        $this->artisan('pbx:updateExtensionStatus');

        expect(ScheduleLog::first())
            ->not->toBeNull()
            ->module_name->toBe('pbx:updateExtensionStatus')
            ->lvl->toBe(3);
    }

    public function test_it_update_the_extension_status_even_if_not_email_is_send(): void
    {
        expect(PbxExtensions::first())
            ->status->toBe('enabled');

        Http::fake([
            'pbxdev.careonecomm.com/*' => Http::response(
                [
                    3 => [
                        'status' => 'disabled'
                    ]
                ],
                200,
                []
            ),
        ]);

        $this->artisan('pbx:updateExtensionStatus');
        Mail::assertNothingSent();
        expect(PbxExtensions::first())
            ->status->toBe('disabled');

    }

    public function test_it_requires_both_body_and_email_to_send_mail(): void
    {
        $company = Company::first();

        Http::fake([
            'pbxdev.careonecomm.com/*' => Http::response(
                [
                    3 => [
                        'status' => 'disabled'
                    ]
                ],
                200,
                []
            ),
        ]);

        Mail::fake();
        $company->settings()->create([
            'option' => 'activate_notification',
            'value' => true,
        ]);
        $company->settings()->create([
            'option' => 'pbx_ext_subject_down',
            'value' => 'Is down down down',
        ]);

        $this->artisan('pbx:updateExtensionStatus');//->expectsOutput(__('comandos.updateExtensionStatus.errors.email.receipt_null'));
        Mail::assertNothingSent();

        PbxExtensions::where('id', '>', 0)->update(['status' => 'enabled']);

        $company->settings()->create([
            'option' => 'server_notification',
            'value' => 'admin@craterapp.com',
        ]);

        //here
        $this->artisan('pbx:updateExtensionStatus');//->expectsOutput(__('comandos.updateExtensionStatus.errors.email.body_null'));
        Mail::assertSent(PbxUpdateExtensionStatusMail::class);

    }

    public function test_it_tries_and_tries_to_send_mail(): void
    {
        $company = Company::first();
        Http::fake([
            'pbxdev.careonecomm.com/*' => Http::response(
                [
                    3 => [
                        'status' => 'disabled'
                    ]
                ],
                200,
                []
            ),
        ]);

        //Mail::fake();

        $this->artisan('pbx:updateExtensionStatus');
        Mail::assertNothingSent();

        expect(ScheduleLog::first())
            ->not->toBeNull()
            ->module_name->toBe('pbx:updateExtensionStatus')
            ->lvl->toBe(3)
            ->data->disabled->email->sent->toBeFalse();
        $this->travel(5)->minutes();
        $this->artisan('pbx:updateExtensionStatus');
        Mail::assertNothingSent();
        //Log::debug('First Log', ScheduleLog::first()->toArray());

        expect(ScheduleLog::first())
            ->not->toBeNull()
            ->module_name->toBe('pbx:updateExtensionStatus')
            ->lvl->toBe(3)
            ->data->disabled->email->sent->toBeFalse()
            ->data->disabled->email->tries->toBe(2);
        $this->travel(10)->minutes();
        $this->artisan('pbx:updateExtensionStatus');
        Mail::assertNothingSent();

        expect(ScheduleLog::first())
            ->not->toBeNull()
            ->module_name->toBe('pbx:updateExtensionStatus')
            ->lvl->toBe(3)
            ->data->disabled->email->sent->toBeFalse()
            ->data->disabled->email->tries->toBe(3);

        $company->settings()->create([
            'option' => 'activate_notification',
            'value' => true,
        ]);
        $company->settings()->create([
            'option' => 'server_notification',
            'value' => 'admin@craterapp.com',
        ]);
        $this->travel(15)->minutes();
        $this->artisan('pbx:updateExtensionStatus');
        Mail::assertSent(PbxUpdateExtensionStatusMail::class, 1);

        expect(ScheduleLog::first())
            ->not->toBeNull()
            ->module_name->toBe('pbx:updateExtensionStatus')
            ->lvl->toBe(3)
            ->data->disabled->email->sent->toBeTrue()
            ->data->disabled->email->tries->toBe(4);

    }

    public function test_it_kinda_works_using_fake(): void
    {
        $company = Company::first();
        $company->settings()->create([
            'option' => 'pbx_ext_subject_down',
            'value' => 'admin@craterapp.com',
        ]);
        $company->settings()->create([
            'option' => 'pbx_ext_body_down',
            'value' => 'La extension esta down down.',
        ]);
        $company->settings()->create([
            'option' => 'activate_notification',
            'value' => true,
        ]);
        $company->settings()->create([
            'option' => 'server_notification',
            'value' => 'admin@craterapp.com',
        ]);
        Http::fake([
            // Stub a series of responses for endpoints...
            'pbxdev.careonecomm.com/*' => Http::sequence()
                ->push([3 => ['status' => 'disabled']], 200)
                ->push([3 => ['status' => 'enabled']], 200)
        ]);
        Mail::fake();

        $this->artisan('pbx:updateExtensionStatus')->assertSuccessful();
        Mail::assertSent(PbxUpdateExtensionStatusMail::class, 1);

        expect(PbxExtensions::first())
            ->status->toBe('disabled')
            ->and(ScheduleLog::first())
            ->message->toBe('disabled')
            ->data->disabled->sent->true;

        $this->travel(5)->minutes();
        $this->artisan('pbx:updateExtensionStatus')->assertSuccessful();
        Mail::assertSent(PbxUpdateExtensionStatusMail::class, 2);

        expect(PbxExtensions::first())
            ->status->toBe('enabled')
            ->and(ScheduleLog::first())
            ->message->toBe('enabled')
            ->data->enabled->sent->true;

    }

    //TODO: Test it wont run to fast (is kinda done).

}
