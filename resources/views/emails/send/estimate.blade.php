@component('mail::layout')
<style type="text/css">
      .footer-link {

            @php
                echo "color: ".$data['PRIMARY_COLOR']."!important;";
            @endphp


        }


        .button {
            max-width: 250px !important;

                @php
                echo " background-color: ".$data['PRIMARY_COLOR']."!important;";
            @endphp

            
            @php
                echo "border-top: 10px solid ".$data['PRIMARY_COLOR']."!important;";
            @endphp

            @php
                echo "border-right: 18px solid ".$data['PRIMARY_COLOR']."!important;";
            @endphp

            @php
                echo "border-bottom: 10px solid ".$data['PRIMARY_COLOR']."!important;";
            @endphp

            @php
                echo "border-left: 18px  solid ".$data['PRIMARY_COLOR']."!important;";
            @endphp

            }
       </style>


    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => ''])
        @if($data['company']['logo'])
            <img class="header-logo" src="{{asset($data['company']['logo'])}}" alt="{{$data['company']['name']}}">
        @else
            {{$data['company']['name']}}
        @endif
        @endcomponent
    @endslot

    {{-- Body --}}
   

    {{-- Subcopy --}}
    @slot('subcopy')
        @component('mail::subcopy')
            {!! $data['body'] !!}
            <!-- @component('mail::button', ['url' => url('/customer/estimates/pdf/'.$data['estimate']['unique_hash'])])
                View Estimate
            @endcomponent -->
        @endcomponent
    @endslot

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            Supported by <a class="footer-link" href="{{config('app.url')}}">{{$data['company']['name']}}</a>  © {{ date('Y') }}  @lang('All rights reserved.')
        @endcomponent
    @endslot
@endcomponent

