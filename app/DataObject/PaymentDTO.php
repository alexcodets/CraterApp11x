<?php

namespace Crater\DataObject;

use Crater\Services\Payment\Authorize\PaymentAuthorizeDO;
use Crater\Services\Payment\AuxVault\AuxVaultData;
use Crater\Services\Payment\Paypal\PaymentPaypalData;

class PaymentDTO
{
    protected PaymentAccountData $paymentAccount;

    protected UserData $user;

    protected PaymentAmountData $amount;

    public function __construct(PaymentAccountData $paymentAccount, UserData $user, PaymentAmountData $amount)
    {
        $this->paymentAccount = $paymentAccount;
        $this->user = $user;
        $this->amount = $amount;
    }

    public function getPaypalDO(): PaymentPaypalDO
    {
        return new PaymentPaypalDO($this->paymentAccount, $this->user, $this->amount);
    }

    public function getPaypalData(): PaymentPaypalData
    {
        return new PaymentPaypalData($this->paymentAccount, $this->user, $this->amount);
    }

    public function getAuthorizeDO(): PaymentAuthorizeDO
    {
        return new PaymentAuthorizeDO($this->paymentAccount, $this->user, $this->amount);
    }

    public function getAuxVaultDO(): AuxVaultData
    {
        return new AuxVaultData($this->paymentAccount, $this->user, $this->amount);
    }

    public function getBaseDO(): PaymentDO
    {
        return new PaymentDO($this->paymentAccount, $this->user, $this->amount);
    }

    public function setAmount(int $amount): void
    {
        //$this->amount = $amount;
    }

    public function updateAmount(int $amount)
    {
        $this->amount->amount = $amount;

    }
}
