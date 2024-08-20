<?php

namespace Crater\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class EmailLowNotification extends BaseMail
{
    use Queueable;
    use SerializesModels;

    private string $message;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $message, $company)
    {
        //
        //\Log::debug('');
        $this->subject = $subject;
        $this->message = $message;
        //$this->data = $data;
        $this->data = $this->getData($company);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): EmailLowNotification
    {
        //\Log::debug('Build');
        return $this->subject($this->subject)->markdown('emails.send.EmailLowNotification')->with([
            'my_message' => $this->message,
            'data' => $this->data
        ]);

    }
}
