<?php

namespace Crater\Jobs;

use Crater\Mail\PaymentFailed;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     *
     * @return void
     */
    public $title;

    public $newMessage;

    public $email;

    public $data;

    public function __construct($title, $newMessage, $email)
    {
        $this->title = $title;
        $this->newMessage = $newMessage;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        /* //Log::debug("pago fallido");
        //Log::debug($this->newMessage);

        //Log::debug($this->email);

        //Log::debug($this->title); */
        $company = Company::find(1);
        // Validation seetings send emails
        $settingSendMail = CompanySetting::select('value', 'id')->where('option', 'send_email_deactive')->where('company_id', $company->id)->get();
        if ($settingSendMail != null && count($settingSendMail) > 0) {
            if($settingSendMail[0]->value === 'NO' || $settingSendMail[0]->value === false) {
                return;
            }
        }
        $this->data = $company;
        $this->data['company'] = $company;
        Mail::to($this->email)->send(new PaymentFailed($this->title, $this->newMessage, $this->data));
        //bbc copy
        $bbcmail = CompanySetting::select('value', 'id')->where('option', 'payment_bbc_email')->where('company_id', $company->id)->first();

        if ($bbcmail != null) {
            if ($bbcmail->value != "") {
                Mail::to($bbcmail->value)->send(new PaymentFailed($this->title, $this->newMessage, $this->data));
            }

        }
    }
}
