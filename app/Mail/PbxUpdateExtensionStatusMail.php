<?php

namespace Crater\Mail;

use Crater\Models\PbxTenant;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class PbxUpdateExtensionStatusMail extends BaseMail
{
    use Queueable;
    use SerializesModels;

    protected string $defaultSubject = 'Ext status Synchronization update';

    protected string $defaultBody = '';

    protected string $optionSubject = 'service_main_update_notification_subject';

    protected string $optionBody = 'service_main_update_notification_body';

    protected string $title;

    protected array $ext;

    protected PbxTenant $tenant;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $ext, PbxTenant $tenant, string $type)
    {
        $this->ext = $ext;
        $this->updateOptions($type);
        $this->body = $this->getFinalBody($tenant->company, $tenant->company->user);
        $this->body = $this->additionalUpdate($tenant, $this->body);
        $this->data = $this->getData($tenant->company);
        $this->subject = $this->getFinalSubject($tenant->company, $tenant->company->user);
        $this->subject = $this->additionalUpdate($tenant, $this->subject);

    }

    public function updateOptions(string $type)
    {
        switch ($type) {
            case 'down':
                $this->optionBody = 'pbx_server_emailbody_down';
                $this->optionSubject = 'server_subject_down';
                $this->defaultSubject = 'The Extension was deactivated';
                $this->title = 'Extension now disabled list';

                //$this->defaultBody =  "The pbx extension with the name: <b> {$extension->name} </b>, is back again";
                break;
            case 'up':
                $this->optionBody = 'pbx_server_emailbody_up';
                $this->optionSubject = 'server_subject_up';
                $this->defaultSubject = 'The Extension was activated';
                $this->title = 'Extension now enabled list';

                //$this->defaultBody =  "The pbx extension with the name: <b> {$extension->name} </b>, is back again";
                break;
            default:
                // default code
                break;
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        return $this->markdown('emails.send.UpdateExtensionStatusMail')
            //->text('emails.text.send.UpdateExtensionStatusMail')
            ->with([
                'message' => $this->body,
                'extensions' => $this->ext,
                'title' => $this->title,
                'data' => $this->data,
            ])->from(config('mail.from.address'), 'PBX services');
    }

    public function additionalUpdate(PbxTenant $tenant, $data)
    {

        $search = [
            '{HOST_IP}', '{SERVER_LABEL}',
        ];

        $replace = [
            $tenant->pbxServer->hostname, $tenant->pbxServer->server_label,
        ];

        return str_replace($search, $replace, $data);
    }
}
