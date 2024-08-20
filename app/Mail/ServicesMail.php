<?php

namespace Crater\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ServicesMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $subject;

    public $message;

    /**
     * Create a new message instance.
     *
     * @param $subject
     * @param $message
     * @param $corePbx
     */
    public function __construct($subject, $message, $data)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)->markdown('emails.send.service')->with([
            'my_message' => $this->message,
            'data' => $this->data
        ]);

    }
}
