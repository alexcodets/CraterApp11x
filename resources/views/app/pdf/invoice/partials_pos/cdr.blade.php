@php
$cdrs_1 = $invoice->pbxCdr()->where('type', 1)->get(['id','number', 'invoice_id', 'rate', 'total_duration', 'cost', 'exclusive_cost']);
$cdrs_0 = $invoice->pbxCdr()->where('type', 0)->get(['id','number', 'invoice_id', 'rate', 'total_duration', 'cost', 'exclusive_cost']);
@endphp

@if(count($cdrs_1) > 0 || count($cdrs_0) > 0)
<table width="100%" class="items-table" cellspacing="0" border="0">	 

    <tr>
        <td class="item-table-title text-center" colspan="5">@lang('pdf_cdr_label')</td>
    </tr>	

	@if(count($cdrs_1) > 0)
    <tr class="item-table-heading-row">
        <th class="pl-0 text-left item-table-heading">@lang('pdf_from_label')</th>
        <th class="pl-0 text-center item-table-heading">@lang('pdf_type_label')</th>
        <th class="pl-0 text-center item-table-heading">@lang('pdf_rate_per_minute')</th>
        <th class="pl-10 text-right item-table-heading">@lang('pdf_rating_duration')</th>
        <th class="text-right item-table-heading">@lang('pdf_amount_label_cdr')</th>
    </tr>
    @foreach ($cdrs_1 as $cdr)
    <tr class="item-row">
        <td
            class="pl-0 text-left item-cell"
            style="vertical-align: top;"
        >
            {{ $cdr->getNumerForInBound() }}
        </td>
        <td
            class="pl-0 text-center item-cell"
            style="vertical-align: top;"
        >
            {{ 'Outbound' }}
        </td>
        <td
            class="pl-0 text-center item-cell"
            style="vertical-align: top;"
        >
            {{ $_currency->symbol . number_format((float)$cdr->rate, 5, '.', ',') }}
        </td>
        <td
            class="pr-20 text-right item-cell"
            style="vertical-align: top;"
        >
            {{ $cdr->getFormattedTimeAttribute($cdr->total_duration) }}
        </td>
        <td
            class="text-right item-cell"
            style="vertical-align: top;"
        >
            {{ $_currency->symbol . number_format((float)$cdr->exclusive_cost, 5, '.', ',') }}
        </td>
    </tr>
    @endforeach   
    <tr>
        <td style="padding-top: 10px" colspan="4"></td>
    </tr>
    @endif
        
    @if(count($cdrs_0) > 0)
    <tr class="item-table-heading-row">
        <th class="pl-0 text-left item-table-heading">@lang('pdf_to_label')</th>
        <th class="pl-0 text-center item-table-heading">@lang('pdf_type_label')</th>
        <th class="pl-0 text-center item-table-heading">@lang('pdf_rate_per_minute')</th>
        <th class="pl-10 text-right item-table-heading">@lang('pdf_rating_duration')</th>
        <th class="text-right item-table-heading">@lang('pdf_amount_label_cdr')</th>
    </tr>
    @foreach ($cdrs_0 as $cdr)
        <tr class="item-row">
            <td
            class="pl-0 text-left item-cell"
            style="vertical-align: top;"
        >
            {{ $cdr->getNumberForOutBound() }}
        </td>
            <td
                class="pl-0 text-center item-cell"
                style="vertical-align: top;"
            >
                {{ 'Inbound' }}
            </td>
            <td
                class="pl-0 text-center item-cell"
                style="vertical-align: top;"
            >
                {{ $_currency->symbol . number_format((float)$cdr->rate, 5, '.', ',') }}
            </td>
            <td
                class="pr-20 text-right item-cell"
                style="vertical-align: top;"
            >
                {{ $cdr->getFormattedTimeAttribute($cdr->total_duration) }}
            </td>
            <td
                class="text-right item-cell"
                style="vertical-align: top;"
            >
                {{ $_currency->symbol . number_format((float)$cdr->exclusive_cost, 5, '.', ',') }}
            </td>
        </tr>
    @endforeach
    @endif
            
</table>
@endif

<!--<div class="total-display-container">
    <table width="100%" cellspacing="0px" border="0" class="total-display-table @if($_cdr->count() > 12) page-break @endif">
        <tr>
            <td class="border-0 total-border-left total-table-attribute-label">
                @lang('pdf_subtotal')
            </td>
            <td
                class="py-8 border-0 total-border-right item-cell total-table-attribute-value"
                style="color: #5851D8"
            >
                {!! format_money_pdf($invoice->pbx_total_cdr, $_currency) !!}
            </td>
        </tr>
    </table>
</div>-->


