<?php

namespace Crater\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReminder extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $message, $data)
    {
        //
        $this->subject = $subject;
        $this->message = $message;
        // $this->corePbx = $corePbx;
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)->markdown('emails.send.PaymentReminderCredit')->with([
            'my_message' => $this->message,
            'data' => $this->data
        ]);

    }
}
