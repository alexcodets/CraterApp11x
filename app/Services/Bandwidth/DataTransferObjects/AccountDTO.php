<?php

namespace Crater\Services\Bandwidth\DataTransferObjects;

use Crater\Models\BandwidthAccount;

class AccountDTO
{
    public string $url;

    public string $userName;

    public string $password;

    public string $accountId;

    public function __construct(BandwidthAccount $account)
    {
        $this->userName = $account->user_name;
        $this->password = $account->password;
        $this->url = $account->url;
        $this->accountId = $account->accountid;
    }

    public function toArray(): array
    {
        return [
            $this->url, $this->userName, $this->password, $this->accountId,
        ];
    }
}
