@component('emails.base', compact('data'))
    The Tenant: {{ $tenant->name }}, has a pending activation. Please complete the activation.
    @component('mail::button', ['url' => route('pbx-server.tenant.index', [$tenant->pbxServer->id])])
        Tenant List
    @endcomponent
    Thank you for using our application!,
    {{ config('app.name') }}
@endcomponent
