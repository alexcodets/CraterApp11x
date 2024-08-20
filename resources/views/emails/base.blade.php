@component('mail::layout')
@slot('css')
.footer-link {
color: {{$data['PRIMARY_COLOR']}} !important;
}
.button {
max-width: 250px !important;
border-top: 10px solid {{$data['PRIMARY_COLOR']}} !important;
background-color: {{$data['PRIMARY_COLOR']}} !important;
border-right: 18px solid {{$data['PRIMARY_COLOR']}} !important;
border-bottom: 10px solid {{$data['PRIMARY_COLOR']}} !important;
border-left: 18px  solid {{$data['PRIMARY_COLOR']}} !important;
}
@endslot
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])

@if($data['company']['logo'])
<img class="header-logo" src="{{asset($data['company']['logo'])}}" alt="{{$data['company']['name']}}">
@else
{{$data['company']['name']}}
@endif
@endcomponent
@endslot

{{-- Body --}}
{!! $slot !!}

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
Supported by <a class="footer-link" href="{{config('app.url')}}">{{$data['company']['name']}}</a>  © {{ date('Y') }}  @lang('All rights reserved.')
@endcomponent
@endslot

@endcomponent
