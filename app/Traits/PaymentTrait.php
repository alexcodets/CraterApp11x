<?php

namespace Crater\Traits;

use Crater\Helpers\General;
use Crater\Models\CompanySetting;
use Crater\Models\Payment;
use Log;

trait PaymentTrait
{
    private function freshPaymentNumber(): string
    {
        return $this->getNextPaymentNumberWithPrefix();
    }

    private function getNextPaymentNumber(): string
    {
        Log::debug('GNPN:Start process '.now()->toTimeString());

        $prefix = $this->getPrefix();
        $payment = Payment::where('payment_number', 'LIKE', $prefix.'-%')
            ->orderBy('payment_number', 'desc')
            ->first('payment_number')->payment_number ?? 0;

        if ($payment !== 0) {
            $payment = explode('-', $payment);
            $payment = $payment[1];
        }

        //\Log::debug("Pre Int Val = $payment");
        $payment = intval($payment) + 1;
        //\Log::debug("Post Int Val = $payment");

        $paymentNumber = sprintf('%06d', $payment);
        //\Log::debug("complete number = $paymentNumber");

        if (Payment::where('payment_number', $prefix.'-'.$paymentNumber)->count() != 0) {
            Log::error("Going Atomic!!! for Payment Number $paymentNumber with number $payment");
            $paymentNumber = $this->getNextPaymentNumberNuclearOption($payment, $prefix);
        }

        Log::debug('GNPN:Returning '.now()->toTimeString());
        Log::debug('GNPN:PaymentNumber '.$paymentNumber);

        return $paymentNumber;
    }

    public function getNextPaymentNumberWithPrefix(): string
    {
        return $this->getPrefix().'-'.$this->getNextPaymentNumber();
    }

    public function getNextPaymentNumberNuclearOption(int $baseValue, string $prefix): string
    {
        $paymentNumber = sprintf('%06d', $baseValue);

        while (Payment::where('payment_number', $prefix.'-'.$paymentNumber)->count() != 0) {
            $baseValue++;
            $paymentNumber = sprintf('%06d', $baseValue);
        }

        return $paymentNumber;

    }

    public function getPrefix()
    {
        return CompanySetting::where('option', 'payment_prefix')
            ->where('company_id', $this->user->company_id)->first()->value ?? 'PAY';
    }

    private function checkUniquenessInPayment(Payment $payment): Payment
    {
        while (Payment::where('unique_hash', $payment->unique_hash)->count() != 1) {
            $payment->unique_hash = General::generateUniqueId();
            $payment->save();
            Log::debug('Run Run');
        }

        while (Payment::where('payment_number', $payment->payment_number)->count() != 1) {
            $payment->payment_number = $this->freshPaymentNumber();
            Log::debug('Ron Ron');
            Log::debug($payment->payment_number);

            $payment->save();
        }

        return $payment;
    }
}
