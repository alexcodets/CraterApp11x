<?php

namespace Crater\Mail;

use Crater\Models\EmailLog;
use Crater\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendInvoiceMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public array $data = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        EmailLog::create([
            'from' => $this->data['from'],
            'to' => $this->data['to'],
            'subject' => $this->data['subject'],
            'body' => $this->data['body'],
            'mailable_type' => Invoice::class,
            'mailable_id' => $this->data['invoice']['id'],
            'company_id' => $this->data['company']['id'],
        ]);

        return $this->from($this->data['from'])
            ->subject($this->data['subject'])
            ->markdown('emails.send.invoice', ['data', $this->data]);
    }
}
