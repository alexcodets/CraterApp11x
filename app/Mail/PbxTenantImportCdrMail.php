<?php

namespace Crater\Mail;

use Crater\Models\PbxCdrTenant;
use Crater\Models\PbxServers;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class PbxTenantImportCdrMail extends BaseMail
{
    use Queueable;
    use SerializesModels;

    private PbxCdrTenant $tenant;

    private string $error;

    private PbxServers $server;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(PbxCdrTenant $tenant, string $error)
    {
        $this->tenant = $tenant;
        $this->error = $error;
        $this->server = $tenant->pbxServer;
        $this->data = $this->getData($tenant->pbxServer->company);
        $this->subject = __('pbxImportCdrs.errors.mail.subject');

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): PbxTenantImportCdrMail
    {
        return $this->markdown('emails.send.PbxTenantImportCdr')
            ->with([
                'error' => $this->error,
                'tenant' => $this->tenant,
                'server' => $this->server,
                'data' => $this->data,
            ])->from(config('mail.from.address'), 'PBX services');
    }
}
