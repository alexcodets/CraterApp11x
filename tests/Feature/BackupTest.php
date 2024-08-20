<?php

use Crater\Jobs\CreateBackupJob;
use Crater\Models\FileDisk;
use Crater\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\{getJson, postJson};

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);

    $user = User::first();
    $this->withHeaders([
        'company' => $user->company_id,
    ]);
    Sanctum::actingAs(
        $user,
        ['*']
    );
});

test('get backups', function () {
    $disk = FileDisk::factory()->create([
        'set_as_default' => true
    ]);

    $response = getJson("/api/v1/backups?disk={$disk->driver}&&file_disk_id={$disk->id}");

    $response->assertOk();
});

test('create backup', function () {
    Queue::fake();

    $disk = FileDisk::factory()->create();

    $data = [
        'option' => 'full',
        'file_disk_id' => $disk->id,
    ];

    postJson('/api/v1/backups', $data);

    Queue::assertPushed(CreateBackupJob::class);

    $response = getJson("/api/v1/backups?disk={$disk->driver}&&file_disk_id={$disk->id}");

    $response->assertStatus(200)->assertJson([
        'disks' => [
            'local'
        ]
    ]);
});
