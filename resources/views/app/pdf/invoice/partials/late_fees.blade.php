@if(isset($lateFees))
@if(count($lateFees) > 0)  
<table width="100%" class="items-table" cellspacing="0" border="0">
    <tr>
        <td class="item-table-title" colspan="7">@lang('pdf_late_fees')</td>
    </tr>
    <tr class="item-table-heading-row">
        <th width="2%" class="pr-20 text-right item-table-heading"> # </th>
		<th class="pl-0 text-left item-table-heading">@lang('pdf_amount')</th>  
		<th class="pl-0 text-left item-table-heading">@lang('pdf_type_label')</th>
        <th class="pl-0 text-left item-table-heading">@lang('pdf_notice')</th>   
        <th class="pl-0 text-left item-table-heading">@lang('pdf_subtotal')</th>       
        <th class="pl-0 text-left item-table-heading">@lang('pdf_taxamount')</th>
		<th class="text-right item-table-heading">@lang('pdf_total')</th>
    </tr>
    @php
        $index = 1;
    @endphp
    @foreach ($lateFees as $lateFee)
        <tr class="item-row">
            <td
                class="pl-0 pr-20 text-left item-cell"
                style="vertical-align: top;"
            >
                {{$index}}
            </td>
            
             <td
                class="pl-0 text-left item-cell"
                style="vertical-align: top;"
            >
                {{ $lateFee->amount }}
            </td>           
            
            <td
                class="pl-0 pr-20 text-left item-cell"
                style="vertical-align: top;"
            >
                @if($lateFee->type == 0)
                    @lang('pdf_percentage')
            	@endif
            	@if($lateFee->type == 1)
                    @lang('pdf_fixed')
            	@endif
            </td>     
            
            <td
                class="pl-0 pr-20 text-left item-cell"
                style="vertical-align: top;"
            >
                <span>

                @lang($lateFee->notice)
                </span>
            </td>   
            
             <td
                class="pl-0 pr-20 text-left item-cell"
                style="vertical-align: top;"
            >
            	{!! format_money_pdf($lateFee->subtotal, $_currency) !!}
            </td>           

            <td
                class="pl-0 pr-20 text-left item-cell"
                style="vertical-align: top;"
            >
                {!! format_money_pdf($lateFee->tax_amount, $_currency) !!}
            </td>
            <td
                class="text-right item-cell"
                style="vertical-align: top;"
            >
                {!! format_money_pdf($lateFee->total, $_currency) !!}
            </td>
        </tr>
        @php
            $index += 1;
        @endphp
    @endforeach
</table>

<div class="total-display-container">
    <table width="100%" cellspacing="0px" border="0" class="total-display-table @if(count($lateFees) > 12) page-break @endif">
        <!--<tr>
            <td class="border-0 border-left-top total-table-attribute-label">
                @lang('pdf_count')
            </td>
            <td class="py-2 border-0 border-right-top item-cell total-table-attribute-value">
                {{ count($lateFees) }}
            </td>
        </tr>
        <tr>
            <td class="py-3"></td>
            <td class="py-3"></td>
        </tr>-->
        <tr>
            <td class="border-0 total-border-left total-table-attribute-label">
                @lang('pdf_subtotal')
            </td>
            <td
                class="py-10 border-0 total-border-right item-cell total-table-attribute-value"
                style="color: #5851D8"
            >
                {!! format_money_pdf($totalFees, $_currency) !!}
            </td>
        </tr>
    </table>
</div>
@endif
@endif

