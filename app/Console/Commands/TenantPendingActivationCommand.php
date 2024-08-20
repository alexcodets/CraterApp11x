<?php

namespace Crater\Console\Commands;

use Crater\Mail\TenantPendingActivationMail;
use Crater\Models\CompanySetting;
use Crater\Models\PbxServerTenant;
use Crater\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

class TenantPendingActivationCommand extends Command
{
    protected $signature = 'tenant:pending-activation-notified
                            {--id : if the id is provided, only one tenant will be notified}';

    protected $description = 'Notify about pending activation of tenant';

    public function handle(): void
    {
        $id = $this->option('id');

        $tenants = PbxServerTenant::query()
            ->where('status', 2)
            ->when($id, function ($query) use ($id) {
                return $query->where('id', $id);
            })
            ->get();


        $users = User::query()
            ->where(function (Builder $query) {
                $query->where('role', '!=', 'customer')
                    ->whereNull('role2')
                    ->where('pbx_notification', true);
            })->orWhere(function (Builder $query) {
                $query->where('role', 'super admin')
                    ->whereNull('role2');
            })->get();

        foreach ($tenants as $tenant) {
            $settings = CompanySetting::query()
                ->where('option', 'server_notification')
                ->where('company_id', $tenant->company_id)
                ->get();

            foreach ($settings as $setting) {
                Mail::to($setting->value)
                    ->queue(new TenantPendingActivationMail($tenant));
            }

            foreach ($users as $user) {
                Mail::to($user)
                    ->queue(new TenantPendingActivationMail($tenant));
            }

        }
    }
}
