<?php

namespace Crater\Mail;

use Crater\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Request;

class FailedPaymentMail extends BaseMail
{
    use Queueable;
    use SerializesModels;

    protected string $defaultSubject = 'Payment Rejected';

    protected string $defaultBody = 'Payment Rejected';

    protected User $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, array $paymentData)
    {
        $this->updateOptions($paymentData['mode']);

        $this->user = $user;
        $this->body = $this->getFinalBody($user->company, $user);
        $this->body = $this->additionalBodyUpdate($user, $paymentData);
        $this->data = $this->getData($user->company);
        $this->subject = $this->getFinalSubject($user->company, $user);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        return $this->markdown('emails.send.general')
            ->with([
                'message' => $this->body,
                'data' => $this->data,
            ]);
    }

    public function updateOptions(string $type)
    {
        switch ($type) {
            case 'ACH':
                $this->optionSubject = 'payment_ach_declined_subject';
                $this->optionBody = 'payment_ach_declined';
                $this->defaultSubject = 'ACH payment declined';
                $mode = 'ACH';

                break;
            case 'CC':
                $this->optionSubject = 'payment_credit_card_rejected_subject';
                $this->optionBody = 'payment_credit_card_rejected';
                $this->defaultSubject = 'Payment with credit card rejected';
                $mode = 'Credit Card';

                break;
            default:
                // default code
                break;
        }
    }

    public function additionalBodyUpdate($user, $data)
    {

        $search = [
            '{PAYMENT_DATE}', '{PAYMENT_NUMBER}', '{PAYMENT_MODE}', '{PAYMENT_AMOUNT}',
            '{CARD_NUMBER}', '{CREDIT_CARD}',
            '{CUSTOMER_LOGIN}', '{PAYMENT_LINK}',
            '{TRANSACTION}',
            '{GATEWAY}', '{TRANSACTION_NUMBER}'

        ];

        $replace = [
            $data['date'], 'N/A', $data['mode'], number_format($data['amount'] ?? 0, 2),
            $data['card_number'], $data['cvv'], $data['expirationDate'],
            Request::root().'/login', '',
            'No Present',
            $data['extra_data']['payment_gateway'], $data['extra_data']['transaction_number']
        ];

        return str_replace($search, $replace, $this->body);
    }
}
