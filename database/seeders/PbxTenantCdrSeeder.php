<?php

namespace Database\Seeders;

use Crater\Models\PbxCdrTenant;
use Crater\Models\PbxExtensions;
use Crater\Models\PbxTenant;
use Crater\Models\PbxTenantCdr;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PbxTenantCdrSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $tenants = PbxTenant::all();
        $now = now()->format('Y-m-d');

        foreach ($tenants as $tenant) {

            $pbxCdrTenant = PbxCdrTenant::firstOrCreate(
                [
                'code' => $tenant->code,
                'pbx_server_id' => $tenant->pbx_server_id ?? 1,
                'tenantid' => $tenant->tenantid,
            ],
                ['date_begin' => $tenant->date_begin ?? $now]
            );

            if ($pbxCdrTenant->date_begin > $tenant->date_begin) {
                $pbxCdrTenant->date_begin = $tenant->date_begin;
                $pbxCdrTenant->save();
            }

        }

        $items = collect(json_decode(Storage::disk('seed')->get('cdrs.json'), true));
        $tenant = PbxCdrTenant::first();

        $items = $items->map(function ($item, $key) use ($tenant) {
            unset($item['cost']);

            return array_merge($item, ['pbx_cdr_tenant_id' => $tenant->id]);
        });

        PbxTenantCdr::insert($items->toArray());

        $items = json_decode(Storage::disk('seed')->get('cdrs-extra.json'), true);

        foreach (array_chunk($items, 250) as $values) {
            $items = (collect($values))->map(function ($item, $key) use ($tenant) {
                unset($item['cost']);

                return array_merge($item, ['pbx_cdr_tenant_id' => $tenant->id]);
            });
            PbxTenantCdr::insert($items->toArray());
        }

        PbxTenant::where('id', 1)->update(['pbx_server_id' => 1]);
        PbxExtensions::where('id', '<', 25)->update(
            [
                'pbx_server_id' => 1,
                'pbx_tenant_code' => 42,
            ]
        );
    }
}
