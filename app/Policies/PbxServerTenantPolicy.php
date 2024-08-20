<?php

namespace Crater\Policies;

use Crater\Models\PbxDID;
use Crater\Models\PbxExtensions;
use Crater\Models\PbxServerTenant;
use Crater\Models\PbxServices;
use Crater\Models\PbxTenant;
use Crater\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PbxServerTenantPolicy
{
    use HandlesAuthorization;

    //    public function viewAny(User $user): bool {}

    public function view(User $user, PbxServerTenant $pbxServerTenant): bool
    {
        return $user->company_id === $pbxServerTenant->company_id;
    }

    public function viewService(User $user, PbxServerTenant $pbxServerTenant, PbxServices $pbxServices, PbxTenant $pbxTenant): bool
    {
        if ($user->company_id !== $pbxServerTenant->company_id || $pbxServices->pbx_tenant_id !== $pbxTenant->id) {
            return false;
        }

        return $pbxServerTenant->tenant_id === $pbxTenant->tenantid && $pbxServerTenant->pbx_server_id === $pbxTenant->pbx_server_id
            && $pbxServerTenant->tenant_code == $pbxTenant->code;

    }

    public function viewExtension(User $user, PbxServerTenant $pbxServerTenant, PbxExtensions $extensions): bool
    {
        if ($user->company_id !== $pbxServerTenant->company_id) {
            return false;
        }

        return $pbxServerTenant->tenant_id === $extensions->pbx_tenant_code && $pbxServerTenant->pbx_server_id === $extensions->pbx_server_id;

    }

    public function viewDid(User $user, PbxServerTenant $pbxServerTenant, PbxDID $did): bool
    {
        if ($user->company_id !== $pbxServerTenant->company_id) {
            return false;
        }

        return $pbxServerTenant->tenant_id === $did->pbx_tenant_code && $pbxServerTenant->pbx_server_id === $did->pbx_server_id;

    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, PbxServerTenant $pbxServerTenant): bool
    {
    }

    public function delete(User $user, PbxServerTenant $pbxServerTenant): bool
    {
    }

    public function restore(User $user, PbxServerTenant $pbxServerTenant): bool
    {
    }

    public function forceDelete(User $user, PbxServerTenant $pbxServerTenant): bool
    {
    }
}
