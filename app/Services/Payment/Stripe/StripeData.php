<?php

namespace Crater\Services\Payment\Stripe;

use Crater\DataObject\PaymentAccountData;
use Crater\DataObject\PaymentDO;
use Crater\DataObject\UserData;
use Omnipay\Common\CreditCard;

class StripeData extends PaymentDO
{
    public function __construct(PaymentAccountData $paymentAccount, UserData $user, float $amount)
    {
        parent::__construct($paymentAccount, $user, $amount);
    }

    public function getCreditCardData(): array
    {
        return [
            'number' => $this->paymentAccount->decrypted_card_number,
            'expiryMonth' => $this->paymentAccount->decrypted_expiration_date->month ?? null,
            'expiryYear' => $this->paymentAccount->decrypted_expiration_date->year ?? null,
            'cvv' => $this->paymentAccount->decrypted_cvv
        ];
    }

    public function getPurchaseData(CreditCard $card): array
    {
        return [
            'amount' => $this->amount / 100,
            'currency' => 'USD',
            'card' => $card,
        ];

    }
}
