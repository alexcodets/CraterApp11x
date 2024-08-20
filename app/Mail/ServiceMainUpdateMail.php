<?php

namespace Crater\Mail;

use Crater\Models\Company;
use Crater\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class ServiceMainUpdateMail extends BaseMail
{
    use Queueable;
    use SerializesModels;

    protected array $ext;

    protected array $did;

    protected string $body;

    protected string $defaultSubject = 'Ext and DID Synchronization update';

    protected string $defaultBody = '';

    protected string $optionSubject = 'service_main_update_notification_subject';

    protected string $optionBody = 'service_main_update_notification_body';

    protected array $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $ext, array $did, Company $company, User $user)
    {
        $this->ext = $ext;
        $this->did = $did;
        $this->body = $this->getFinalBody($company, $user);
        $this->data = $this->getData($company);
        $this->subject = $this->getFinalSubject($company, $user);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): ServiceMainUpdateMail
    {
        //return $this->view('view.name');
        //return $this->subject($this->getFinalSubject($this->company))->markdown('emails.send.EmailLowNotification')->with([
        return $this->markdown('emails.send.MainUpdateNotification')
            //->text('emails.text.send.MainUpdateNotification')
            ->with([

                'message' => $this->body,
                'ext' => $this->ext,
                'did' => $this->did,
                'data' => $this->data,
            ]);
    }
}
