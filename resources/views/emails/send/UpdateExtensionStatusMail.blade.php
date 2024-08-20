@component('emails.base', [ 'data' => $data ])
@if($message)
    {{ $message }}
    ----
@endif

# Extensions
## {{ $title }}:
@forelse ($extensions as $ext)
- **Name**: {{ $ext['name'] }}, **Ext**: {{ $ext['ext'] }}, **Status**: {{ $ext['status'] }}
@empty
#### --Somethings missing here--
@endforelse
@endcomponent
