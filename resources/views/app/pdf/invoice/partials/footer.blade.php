<hr class="item-cell-table-hr" style="margin-top: 2em">

<div class="total-display-container">
    <table width="100%" cellspacing="0px" border="0" class="total-display-table @if($_cdr->count() > 12) page-break @endif">
        @if(count($_items) && $service->allow_items)
            <tr>
                <td class="border-0 total-table-attribute-label">@lang('pdf_total_items')</td>
                <td class="py-2 border-0 item-cell total-table-attribute-value">
                    {!! format_money_pdf($invoice->pbx_total_items, $_currency) !!}
                </td>
            </tr>            
        @endif

        <!-- EXTENSIONS -->

        @if(count($invoice_pbx_extension_detail) > 0 && ($invoice->is_edited == 1 || $invoice->is_edited == 0))
            <tr>
                <td class="border-0 total-table-attribute-label">@lang('pdf_extensions_total')</td>
                <td class="py-2 border-0 item-cell total-table-attribute-value">
                    {!! format_money_pdf($total_invoice_pbx_extension_detail * 100 , $_currency) !!}
                </td>
            </tr>
        @endif

        <!-- DIDS -->

        @if(count($_dids) && $service->allow_did && $invoice->is_edited == 0)
            <tr>
                <td class="border-0 total-table-attribute-label">@lang('pdf_did_total')</td>
                <td class="py-2 border-0 item-cell total-table-attribute-value">
                    {!! format_money_pdf($invoice->pbx_total_did, $_currency) !!}
                </td>
            </tr>
        @endif

        @if(count($invoice_pbx_did_detail) > 0 && $invoice->is_edited == 1)
            <tr>
                <td class="border-0 total-table-attribute-label">@lang('pdf_did_total')</td>
                <td class="py-2 border-0 item-cell total-table-attribute-value">
                    {!! format_money_pdf($total_invoice_pbx_did_detail * 100 , $_currency) !!}
                </td>
            </tr>
        @endif

        <!-- -->       

        @if(count($_charges) && $service->allow_aditionalcharges)
            <tr>
                <td class="border-0 total-table-attribute-label">@lang('pdf_charges_total')</td>
                <td class="py-2 border-0 item-cell total-table-attribute-value">
                    {!! format_money_pdf($invoice->pbx_total_aditional_charges, $_currency) !!}
                </td>
            </tr>
        @endif

        @if($service->allow_customapp && $invoice->pbx_total_apprate > 0)
            <tr>
                <td class="border-0 total-table-attribute-label">@lang('pdf_apprate_total')</td>
                <td class="py-2 border-0 item-cell total-table-attribute-value">
                    {!! format_money_pdf($invoice->pbx_total_apprate, $_currency) !!}
                </td>
            </tr>
        @endif

        @if($_cdr->count() > 0 && $service->allow_usagesummary)
            <tr>
                <td class="border-0 total-table-attribute-label">@lang('pdf_additional_usage_cdr')</td>                
                <td class="py-2 border-0 item-cell total-table-attribute-value">
                    {!! format_money_pdf($invoice->pbx_total_cdr, $_currency) !!}
                </td>
            </tr>
        @endif


        @if($invoice->pbx_packprice > 0 )
            <tr>
                <td class="border-0 total-table-attribute-label">@lang('pdf_pack_price')</td>
                <td class="py-2 border-0 item-cell total-table-attribute-value">
                    {!! format_money_pdf($invoice->pbx_packprice, $_currency) !!}
                </td>
            </tr>
        @endif

        @if($invoice->retention_total != 0 && $invoice->retention === 'YES')
            <tr>
                <td class="border-0 total-table-attribute-label">@lang('pdf_retention_total')</td>
                <td class="py-2 border-0 item-cell total-table-attribute-value">
                    {!! format_money_pdf($invoice->retention_total, $_currency) !!}
                </td>
            </tr>
        @endif

        <tr>
            <td class="border-0 total-table-attribute-label">@lang('pdf_subtotal')</td>
            <td class="py-2 border-0 item-cell total-table-attribute-value">
                {!! format_money_pdf($invoice->sub_total, $_currency) !!}
            </td>
        </tr>

        @isset($_avalaraTaxes)
        @if($_avalaraTaxes->isNotEmpty())
            <tr>
                <td class="border-0 total-table-attribute-label">@lang('pdf_avalara_taxes')</td>
                <td class="py-2 border-0 item-cell total-table-attribute-value">
                    {!! format_money_pdf($invoice->avalara_total_tax , $_currency) !!}
                </td>
            </tr>
        @endif
        @endisset

        @if ($invoice->tax_per_item === 'YES')
            @for ($i = 0; $i < count($labels); $i++)
                <tr>
                    <td class="border-0 total-table-attribute-label">
                        {{$labels[$i]}}
                    </td>
                    <td class="py-2 border-0 item-cell total-table-attribute-value">
                        {!! format_money_pdf($taxes[$i], $_currency) !!}
                    </td>
                </tr>
            @endfor
        @else
            @foreach ($invoice->taxes as $tax)
                <tr>
                    <td class="border-0 total-table-attribute-label">
                        {{$tax->name.' ('.$tax->percent.'%)'}}
                    </td>
                    <td class="py-2 border-0 item-cell total-table-attribute-value">
                        {!! format_money_pdf($tax->amount, $_currency) !!}
                    </td>
                </tr>
            @endforeach
        @endif

        @if ($invoice->pbx_service_id != 0 && $invoice->pbx_service_id != NULL && $invoice->tax_per_item === 'YES' )
        @foreach ($invoice->taxes as $tax)
                <tr>
                    <td class="border-0 total-table-attribute-label">
                        {{$tax->name.' ('.$tax->percent.'%)'}}
                    </td>
                    <td class="py-2 border-0 item-cell total-table-attribute-value">
                        {!! format_money_pdf($tax->amount, $_currency) !!}
                    </td>
                </tr>
            @endforeach
        @endif

        @if($invoice->discount > 0)
            @if ($invoice->discount_per_item === 'NO')
                <tr>
                    <td class="border-0 total-table-attribute-label">
                        @if($invoice->discount_type === 'fixed')
                            @lang('pdf_discount_label')
                        @endif
                        @if($invoice->discount_type === 'percentage')
                            @lang('pdf_discount_label') ({{$invoice->discount}}%)
                        @endif
                    </td>
                    <td class="py-2 border-0 item-cell total-table-attribute-value" >
                        @if($invoice->discount_type === 'fixed')
                            {!! format_money_pdf($invoice->discount_val, $_currency) !!}
                        @endif
                        @if($invoice->discount_type === 'percentage')
                            {!! format_money_pdf($invoice->discount_val, $_currency) !!}
                        @endif
                    </td>
                </tr>
            @endif
        @endif

        <tr>
            <td class="py-3"></td>
            <td class="py-3"></td>
        </tr>
        <tr>
            <td class="border-0 total-border-left total-table-attribute-label">
                @lang('pdf_total')
            </td>
            <td
                class="py-8 border-0 total-border-right item-cell total-table-attribute-value"
                style="color: #5851D8"
            >
                {!! format_money_pdf($invoice->total, $_currency)!!}
            </td>
        </tr>
    </table>
</div>
