<?php

namespace Crater\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PbxServicesMail extends Mailable
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
    public function __construct($subject, $message, $corePbx, $data)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->corePbx = $corePbx;
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->corePbx) {
            return $this->subject($this->subject)->markdown('emails.send.pbxService')->with([
                'my_message' => $this->message,
                'data' => $this->data
            ]);

        } else {
            return $this->subject($this->subject)->markdown('emails.send.service')->with([
                'my_message' => $this->message
            ])->from(config('mail.from.address'), 'PBX services');
        }
    }
}
