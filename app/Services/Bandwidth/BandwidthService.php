<?php

namespace Crater\Services\Bandwidth;

use Crater\Services\Bandwidth\DataTransferObjects\AccountDTO;
use Crater\Services\Bandwidth\Traits\ApiTrait;

class BandwidthService
{
    use ApiTrait;

    public string $url;

    public string $base_url;

    public string $userName;

    public string $password;

    public string $accountId;

    public function __construct(AccountDTO $dto)
    {
        $this->updateConfig($dto);
    }

    public function sites()
    {
        try {
            return $this->getRequest('sites');
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

    public function checkCredentials()
    {
        try {
            $this->getRequest('sites');

            return $this->responseSuccess('The credentials are correct');
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }
}
