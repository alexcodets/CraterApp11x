<?php

namespace Crater\Mail;

use Crater\Models\PbxServerTenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class TenantPendingActivationMail extends BaseMail implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    private PbxServerTenant $tenant;

    public function __construct(PbxServerTenant $tenant)
    {
        $this->tenant = $tenant;
        $this->data = $this->getData($tenant->company);
    }

    public function build(): self
    {

        return $this->markdown('emails.tenant-pending-activation')
            ->with(['tenant' => $this->tenant,
                    'data' => $this->data]);
    }
}
