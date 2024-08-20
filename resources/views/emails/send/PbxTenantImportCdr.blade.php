@component('emails.base', [ 'data' => $data ])

# A error has been found while attempting to import CDR.

## Tenant
- **Name** {{ $tenant->tenant->name }}
- **TenantId** {{ $tenant->tenantid }}
- **Code** {{ $tenant->code }}

## Server
- **Label** {{ $server->server_label }}
- **HostName** {{ $server->hostname }}
@if($error)
## Error:
# {{ $error }}
@endif

@endcomponent
