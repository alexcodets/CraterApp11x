<?php

namespace Crater\Services\Payment\Stripe\Exceptions;

use Exception;
use Throwable;

class StripeConfigurationModelNotFoundException extends Exception
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->message = 'No Stripe Configuration found';
    }
}
