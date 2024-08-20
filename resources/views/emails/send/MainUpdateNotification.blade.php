@component('emails.base', [ 'data' => $data ])
@if($message)
{{ $message }}

----

@endif

# Extensions
## New Extensions:
@forelse ($ext['new'] as $new)
- **Name**: {{ $new['name'] }}, **Ext**: {{ $new['ext'] }}, **Status**: {{ $new['status'] }}
@empty
#### --No New extension--
@endforelse
## Deleted Extensions:
@forelse ($ext['deleted'] as $del)
- **Name**: {{ $del['name'] }}, **Ext**: {{ $del['ext'] }}, **Status**: {{ $del['status'] }}
@empty
#### --No New extension--
@endforelse

# Did
## New Did:
@forelse ($did['new'] as $new)
- **Type**: {{ $new['type'] }}, **Number**: {{ $new['number'] }}, **Status**: {{ $new['status'] }}
@empty
#### --No New extension--
@endforelse
## Deleted Did:
@forelse ($did['deleted'] as $del)
- **Type**: {{ $del['type'] }}, **Ext**: {{ $del['number'] }}, **Status**: {{ $del['status'] }}
@empty
#### --No New extension--
@endforelse

@endcomponent
