<?php

namespace Crater\Pbxware;

class PbxWareVersion
{
    /* Status update for user become Available since that version 6.6.1.0 */
    public const VERSION_API_TENANT_STATUS_CHANGE = '6.6.1.0';
    /* Did can use any destiny, including non extension destiny  */
    public const VERSION_API_DID_DESTINY_CHANGE = '6.7.1.0';

    public function canChangeTenantStatus($version): bool
    {
        return (version_compare(strtok($version, ' '), self::VERSION_API_TENANT_STATUS_CHANGE, '>=')) == true;
    }
}
