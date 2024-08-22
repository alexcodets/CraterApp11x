<?php

namespace Tests\Feature\Console\Commands;

use Crater\Mail\ServiceMainUpdateMail;
use Crater\Models\Company;
use Crater\Models\PbxDID;
use Crater\Models\PbxExtensions;
use Crater\Models\PbxServicesDID;
use Crater\Models\PbxServicesExtensions;
use Crater\Models\PbxTenant;
use Crater\Models\User;
use Database\Seeders\InternationalRateSeeder;
use Database\Seeders\PbxConfPrefix;
use Database\Seeders\PbxPackageServerSeeder;
use Database\Seeders\PbxServicesSeeder;
use Database\Seeders\PbxUserSeeder;
use Database\Seeders\UsersTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Storage;
use Tests\TestCase;

class PbxServiceMainUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function seeding(): void
    {

        $didArray = [
            36 =>
                [
                    'number' => '4035555876',
                    'number2' => null,
                    'server' => '42',
                    'trunk' => '60',
                    'type' => '-',
                    'ext' => '',
                    'e164' => null,
                    'e164_2' => null,
                    'status' => 'enabled',
                ],
            39 =>
                [
                    'number' => '4035552552',
                    'number2' => null,
                    'server' => '42',
                    'trunk' => '60',
                    'type' => '-',
                    'ext' => '',
                    'e164' => null,
                    'e164_2' => null,
                    'status' => 'enabled',
                ],
            42 =>
                [
                    'number' => '4035558584',
                    'number2' => null,
                    'server' => '42',
                    'trunk' => '60',
                    'type' => '-',
                    'ext' => '',
                    'e164' => null,
                    'e164_2' => null,
                    'status' => 'enabled',
                ],
            45 =>
                [
                    'number' => '4035555868',
                    'number2' => null,
                    'server' => '42',
                    'trunk' => '60',
                    'type' => '-',
                    'ext' => '',
                    'e164' => null,
                    'e164_2' => null,
                    'status' => 'enabled',
                ],
            48 =>
                [
                    'number' => '4035555910',
                    'number2' => null,
                    'server' => '42',
                    'trunk' => '60',
                    'type' => '-',
                    'ext' => '',
                    'e164' => null,
                    'e164_2' => null,
                    'status' => 'enabled',
                ],
            51 =>
                [
                    'number' => '4035556210',
                    'number2' => null,
                    'server' => '42',
                    'trunk' => '60',
                    'type' => '-',
                    'ext' => '',
                    'e164' => null,
                    'e164_2' => null,
                    'status' => 'enabled',
                ],
            54 =>
                [
                    'number' => '4035550343',
                    'number2' => null,
                    'server' => '42',
                    'trunk' => '60',
                    'type' => '-',
                    'ext' => '',
                    'e164' => null,
                    'e164_2' => null,
                    'status' => 'enabled',
                ],
            56 =>
                [
                    'number' => '8775550149',
                    'number2' => null,
                    'server' => '42',
                    'trunk' => '60',
                    'type' => '-',
                    'ext' => '',
                    'e164' => null,
                    'e164_2' => null,
                    'status' => 'enabled',
                ],
            59 =>
                [
                    'number' => '8335550182',
                    'number2' => null,
                    'server' => '42',
                    'trunk' => '60',
                    'type' => '-',
                    'ext' => '',
                    'e164' => null,
                    'e164_2' => null,
                    'status' => 'enabled',
                ]
        ];

        $extArray = [
            3 =>
                [

                    'name' => 'Peter Griffin7',
                    'email' => 'none@careonecomm.com',
                    'ext' => '2001',
                    'protocol' => 'sip',
                    'location' => 'local',
                    'ua_id' => '50',
                    'ua_name' => 'generic_sip',
                    'ua_fullname' => 'Generic SIP',
                    'status' => 'disabled',
                    'macaddress' => '',
                    'linenum' => '',
                ]

        ];

        Http::fake([
            // Stub a series of responses for endpoints...
            'pbxdev.careonecomm.com/*' => Http::sequence()
                ->push(["server_name" => "pbx6"])
                ->push($didArray)
                ->push($extArray)

        ]);

    }

    public function test_it_works(): void
    {
        Mail::fake();
        $this->seed([
            UsersTableSeeder::class,
            PbxConfPrefix::class,
            PbxPackageServerSeeder::class,
            //PbxDidAndExtSeeder::class,
            PbxUserSeeder::class,
            InternationalRateSeeder::class,
            //PbxServicesSeeder::class,
        ]);
        //$user = User::first();
        $clientData = json_decode(Storage::disk('seed')->get('client.json'), true);
        $user = User::where('email', $clientData[0]['client']['email'])->first();
        $tenant = PbxTenant::first();
        $company = Company::first();
        $company->settings()->create(['option' => 'server_notification', 'value' => 'mail@careone.com']);

        PbxDID::factory()->times(3)
            ->create([
                'type' => 'Queues',
                'pbx_tenant_id' => $tenant->id,
                'company_id' => $user->company_id,
                'creator_id' => $user->id,
            ]);

        PbxExtensions::factory()->times(2)
            ->create([
                'pbx_tenant_id' => $tenant->id,
                'company_id' => $user->company_id,
                'creator_id' => $user->id,
            ]);

        User::factory()->count(2)->create([
            'pbx_notification' => 1,
            'company_id' => $company->id,
        ]);

        User::factory()->count(2)->create([
            'pbx_notification' => 0,
            'company_id' => $company->id,
        ]);

        $this->seeding();

        $this->seed(PbxServicesSeeder::class);

        $this->artisan('pbx:serviceMainUpdate')->assertExitCode(0);
        //ese 3 no me convence en count extension.
        expect(PbxDID::count())->toBe(9)
            ->and(PbxDID::withTrashed()->whereNotNull('deleted_at')->count())->toBe(3)
            ->and(PbxServicesDID::count())->toBe(0)
            ->and(PbxServicesDID::withTrashed()->whereNotNull('deleted_at')->count())->toBe(3)
            ->and(PbxExtensions::count())->toBe(1)
            ->and(PbxExtensions::withTrashed()->whereNotNull('deleted_at')->count())->toBe(2)
            ->and(PbxServicesExtensions::count())->toBe(0)
            ->and(PbxServicesExtensions::withTrashed()->whereNotNull('deleted_at')->count())->toBe(2);

        Mail::assertSent(ServiceMainUpdateMail::class, 3);

    }
}
