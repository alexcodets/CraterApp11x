<!DOCTYPE html>
<html>

<head>
    <title>Estimate</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <style type="text/css">
       /* -- Base -- */
       body {
            font-family: "DejaVu Sans";
        }

        html {
            margin: 0px;
            padding: 0px;
            margin-top: 50px;
        }

        .text-center {
            text-align: center
        }

        .hr-details {
            height: 1px;
            border-top: 3px solid #25c1c0ff;
            margin-bottom: 10px;

        }
        .title-details {
            font-weight: 400;
            font-size: 14px;
            color: black;
        }
        .box-border {
            padding-bottom: 5px;
            @php
                echo "border-bottom: 1px solid ".$colorInvoice.";";
            @endphp
            margin-bottom: 10px;
            width: 200px;
        }
        .box-details{
            /* width: 240px !important;
            height: 125px; */
            border: 1px solid #FFFFFF;
        }


        hr {
            margin: 0 30px 0 30px;
            color: rgba(0, 0, 0, 0.2);
            border: 0.5px solid #EAF1FB;
        }

        /* -- Header -- */

        .header-bottom-divider {
            color: rgba(0, 0, 0, 0.2);
            position: absolute;
            top: 90px;
            left: 0px;
            width: 100%;
        }

        .header-container {
            position: absolute;
            width: 94%;
            height: 90px;
            left: 26px;
            top: -50px;
        }
        .number-invoice {
            font-style:normal !important;
            font-weight:normal !important;
            font-size:17pt !important;
            font-family: "DejaVu Sans";
            color:#2c2c2c !important;
            word-spacing:-0.0332em;
            letter-spacing: -0.0118px;
            margin-top: 35px !important;
        }
        .center-horizontal{
            width: 94%;
            margin: 0 auto;
        }

        .header-logo {
            max-width: 75px;
            margin-top: 20px;
            text-transform: capitalize;
            color: #817AE3;
        }

        .header {
            font-size: 16.5px;
            color: rgba(0, 0, 0, 0.7);
        }

        .content-wrapper {
            display: block;
            margin-top: 0px;
            padding-top: 30px;
            padding-bottom: 15px;
        }

        .company-address-container {
            padding-top: 15px;
            padding-left: 30px;
            float: left;
            width: 30%;
            text-transform: capitalize;
            margin-bottom: 2px;
        }

        .company-address-container h1 {
            font-size: 13px;
            line-height: 22px;
            letter-spacing: 0.05em;
            margin-bottom: 0px;
            margin-top: 10px;
        }

        .company-address {
            margin-top: 2px;
            text-align: left;
            font-size: 10px;
            line-height: 15px;
            color: #595959;
            width: 280px;
            word-wrap: break-word;
        }

        .invoice-details-container {
            float: right;
            padding: 10px 30px 0 0;
        }

        .attribute-label {
            font-size: 10px;
            line-height: 18px;
            padding-right: 40px;
            text-align: left;
            color: #4A4A4A;
        }

        .attribute-value {
            font-size: 10px;
            line-height: 18px;
            text-align: right;
        }

        /* -- Shipping -- */

        .shipping-address-container {
            float: right;
            padding-left: 40px;
            width: 160px;
        }
        .shipping-address {
            font-size: 10px;
            line-height: 15px;
            color: #595959;
            padding: 45px 0px 0px 40px;
            margin: 0px;
            width: 160px;
            word-wrap: break-word;
        }

        /* -- Billing -- */

        .billing-address-container {
            padding-top: 50px;
            float: left;
            padding-left: 30px;
        }

        .billing-address-label {
            font-size: 10px;
            line-height: 18px;
            padding: 0px;
            margin-top: 27px;
            margin-bottom: 0px;
        }

        .billing-address-name {
            max-width: 160px;
            font-size: 13px;
            line-height: 22px;
            padding: 0px;
            margin: 0px;
        }

        .billing-address {
            font-size: 10px;
            line-height: 15px;
            color: #595959;
            padding: 45px 0px 0px 30px;
            margin: 0px;
            width: 160px;
            word-wrap: break-word;
        }

        /* -- Pbx Service -- */

        .pbx-service-container {
            float: right;
        }

        .pbx-service {
            font-size: 10px;
            line-height: 15px;
            color: #595959;
            padding: 45px 30px 0px 40px;
            margin: 0px;
            word-wrap: break-word;
        }

        /* -- Items Table -- */

        .items-table {
            margin-top: 2px;
            padding: 0px 30px 0px 30px;
            page-break-before: avoid;
            page-break-after: auto;
        }

        .items-table hr {
            height: 0.1px;
        }

        .item-table-title {
            font-weight:bold;
            color: #ffffff;
            text-align:left;
            @php
                echo "background-color: ".$colorInvoice.";";
            @endphp
            padding-left: 10;
            font-size: 10px;
        }
        .item-table-heading {
            font-size: 10.5;
            text-align: center;
            padding: 5px;
            color: #4A4A4A;
        }

        tr.item-table-heading-row th {
            border-bottom: 0.620315px solid #E8E8E8;
            font-size: 10px;
            line-height: 18px;
        }

        tr.item-row td {
            font-size: 10px;
            line-height: normal;
        }

        .item-cell {
            font-size: 11px;
            text-align: center;
            padding: 5px;
            /*padding-top: 10px;*/
            color: #040405;
        }

        .item-description {
            color: #595959;
            font-size: 8px;
            line-height: 10px;
        }

        /* -- Total Display Table -- */

        .total-display-container {
            padding: 0 25px;
        }

        .total-display-table {
            border-top: none;
            box-sizing: border-box;
            page-break-inside: avoid;
            page-break-before: auto;
            page-break-after: auto;
            margin-left: 500px;
        }

        .total-table-attribute-label {
            font-size: 11px;
            color: #4A4A4A;
            text-align: right;
            padding-left: 10px;
        }

        .total-table-attribute-value {
            font-weight: bold;
            text-align: right;
            font-size: 11px;
            @php
                echo "color: ".$colorInvoice."!important;";
            @endphp

            padding-right: 10px;
        }

        /* -- Notes -- */

        .notes {
            font-size: 10px;
            color: #595959;
            margin-top: 15px;
            margin-left: 30px;
            width: 442px;
            text-align: left;
            page-break-inside: avoid;
        }

        .notes2 {
            font-size: 10px;
            color: #595959;
            margin-top: 15px;
            margin-left: 30px;
            margin-right: 25px;
            width: 100%;
            page-break-inside: avoid;
        }

        .notes-label {
            font-size: 13px;
            line-height: 22px;
            letter-spacing: 0.05em;
            color: #040405;
            width: 108px;
            height: 19.87px;
            padding-bottom: 10px;
        }

        /* -- Helpers -- */

        .text-primary {
            @php
                echo "color: ".$colorInvoice.";";
            @endphp
        }

        .text-center {
            text-align: center
        }

        table .text-left {
            text-align: left;
        }

        table .text-right {
            text-align: right;
        }

        .border-0 {
            border: none;
        }

        .py-2 {
            padding-top: 2px;
            padding-bottom: 2px;
        }

        .py-8 {
            padding-top: 8px;
            padding-bottom: 8px;
        }

        .py-3 {
            padding: 3px 0;
        }

        .pr-20 {
            padding-right: 20px;
        }

        .pr-10 {
            padding-right: 10px;
        }

        .pl-20 {
            padding-left: 20px;
        }

        .pl-10 {
            padding-left: 10px;
        }

        .pl-0 {
            padding-left: 0;
        }
        .text-details{
            font-size: 10px;
            color: #595959;

        }

        .test-border{
            border: 1px solid black
        }
    </style>
</head>

<body>
    <div class="header-container">


        <table width="100%">
            <tr>
                <td class="text-left">
                    <h3 class="number-invoice"
                    >@lang('pdf_estimate_number') {{$estimate->estimate_number}}</h3>
                </td>
                <td class="text-right">
                    @if(true)
                        <img class="header-logo" src="{{ $logo }}" alt="Company Logo">
                    @else
                        @if($estimate->user->company)
                            <h3 class="header-logo"> {{$estimate->user->company->name}} </h3>

                        @endif
                    @endif
                </td>
            </tr>
        </table>

    </div>

    <div class="content-wrapper center-horizontal" style="margin-top: 60px;">
        <table width="100%">
            <tr width="100%">
                <td  style="vertical-align:top ;">
                        <div class="box-details ">
                            <div class="box-border">
                                <span class="title-details">@lang('pdf_estimate_information'):</span>
                            </div>
                            <div class="text-details">
                                <span style="font-weight: bold;">@lang('pdf_customer_number'):</span>
                                <span>{{$estimate->user->customcode}}</span>
                            </div>

                            <div class="text-details">
                                <span style="font-weight: bold;">@lang('pdf_customer_name'):</span>
                                <span>{{$estimate->user->name}}</span>
                            </div>

                            <div class="text-details">
<span style="font-weight: bold;">@lang('pdf_assigned_to_name'):</span>
<span>{{ $estimate->assignedTo ? $estimate->assignedTo->name : 'N/A' }}</span>
</div>


                            <div class="text-details">
                                <span style="font-weight: bold;">@lang('pdf_status'):</span>
                                <span>{{$estimate->status}}</span>
                            </div>
                            <div class="text-details">
                                <span style="font-weight: bold;">@lang('pdf_estimate_date'):</span>
                                <span>{{$estimate->formattedEstimateDate}}</span>
                            </div>
                            <div class="text-details">
                                <span style="font-weight: bold;">@lang('pdf_estimate_expire_date'):</span>
                                <span>{{$estimate->formattedExpiryDate}}</span>
                            </div>
                        </div>
                </td>
                <td style="vertical-align:top ;">
                    <div class="box-details ">
                        <div class="box-border" style="margin-bottom: 0px !important">
                            <span class="title-details">@lang('pdf_estimate_from'):</span>
                        </div>
                        <div class="text-details" style="margin-bottom:10px;">
                            {!! $company_address !!}
                        </div>
                    </div>
                </td>
                <td style="vertical-align:top ;" >
                    <div class="box-details " style="margin-bottom: 0px !important">
                        <div class="box-border" style="width: 240px !important">
                            <span class="title-details">@lang('pdf_estimate_to'):</span>
                        </div>
                        <div class="text-details">
                            <!-- Bill to -->
                            @if($billing_address)

                                {!! $billing_address !!}
                            @endif

                            <!-- shipping_address -->
                            @if($estimate->user->add_shipping_addres == 1 ||$estimate->user->add_shipping_addres == true )
                                @if($shipping_address)
                                    @lang('pdf_ship_to')
                                    {!! $shipping_address !!}
                                @endif
                            @endif
                        </div>
                    </div>
                    <!-- <hr class="hr-details"> -->
                </td>
            </tr>
        </table>
    </div>

    <div class="wrapper">
        <div class="customer-address-container">

            <div style="clear: both;"></div>
            </div>
            <div style="position:relative">
                @include('app.pdf.estimate.partials.table')
            </div>
            <div class="notes">
                @if($notes)
                    <div class="notes-label">
                        @lang('pdf_notes')
                    </div>
                    {!! $notes !!}
                @endif
            </div>

            <div  class="notes2">
                @if($Footer)
                <br>
                <hr>
                    {!! $Footer !!}
                @endif
            </div>
        </div>
    </div>
</body>

</html>
