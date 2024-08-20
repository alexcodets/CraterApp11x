<?php

namespace Crater\Mail;

use Crater\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class SimpleMail extends BaseMail
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $subject, string $body, Company $company)
    {
        $this->data = $this->getData($company);
        $this->body = $body;
        $this->subject = $subject;

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
}
