<table width="100%" class="items-table" >
    <tr class="item-table-heading-row">
        {{-- <th width="1%" class="text-right item-table-heading">#</th> --}}
        <th width="23%" class=" text-left item-table-heading">@lang('pdf_items_label')</th>
        <th width="1%" class=" text-right item-table-heading">@lang('pdf_invoice_quantity')</th>
        <th class="text-right item-table-heading">@lang('pdf_price_label')</th>
        @if ($invoice->discount_per_item === 'YES')
            <th class=" text-right item-table-heading">@lang('pdf_discount_label')</th>
        @endif
        <th class="text-right item-table-heading">@lang('pdf_amount_label')</th>
    </tr>
    @php
        $index = 1;
    @endphp

    @if (isset($_items))
        @foreach ($_items as $item)
            <tr class="item-row">
                {{-- <td class=" text-right item-cell" style="vertical-align: top;">
                    {{ $index }}
                </td> --}}
                <td class=" text-left item-cell" style="vertical-align: top;">
                    <span>{{ $item->name }}</span><br>
                    <span class="item-description">{!! nl2br(htmlspecialchars($item->description)) !!}</span>
                </td>
                <td class=" text-right item-cell" style="vertical-align: top;">
                    {{ $item->quantity }}
                </td>
                <td class=" text-right item-cell" style="vertical-align: top;">
                    {!! format_money_pdf($item->price, $invoice->user->currency) !!}
                </td>

                @if ($invoice->discount_per_item === 'YES')
                    <td class=" text-right item-cell" style="vertical-align: top;">
                        @if ($item->discount_type === 'fixed')
                            {!! format_money_pdf($item->discount_val, $invoice->user->currency) !!}
                        @endif
                        @if ($item->discount_type === 'percentage')
                            {{ $item->discount }}%
                        @endif
                    </td>
                @endif

                <td class="text-right item-cell" style="vertical-align: top;">
                    {!! format_money_pdf($item->total, $invoice->user->currency) !!}
                </td>
            </tr>
            @php
                $index += 1;
            @endphp
        @endforeach
    @endif
</table>

@isset($_avalaraTaxes)
    @if ($_avalaraTaxes->isNotEmpty())
        @include('app.pdf.invoice.partials.avalara_taxes')
    @endif
@endisset

<hr class="item-cell-table-hr">
<div class="total-display-container" >
    @if (isset($_items))
        <table width="100%" 
           >
            <tr >
                <td class="border-0 total-table-attribute-label">@lang('pdf_subtotal')</td>
                <td class=" item-cell total-table-attribute-value">
                    {!! format_money_pdf($invoice->sub_total, $invoice->user->currency) !!}
                </td>
            </tr>

            @if ($invoice->tax_per_item === 'YES')
                @for ($i = 0; $i < count($labels); $i++)
                    <tr>
                        <td class="border-0 total-table-attribute-label">
                            {{ $labels[$i] }}
                        </td>
                        <td class="py-2 border-0 item-cell total-table-attribute-value">
                            {!! format_money_pdf($taxes[$i], $invoice->user->currency) !!}
                        </td>
                    </tr>
                @endfor
            @else
                @foreach ($invoice->taxes as $tax)
                    <tr>
                        <td class="border-0 total-table-attribute-label">
                            {{ $tax->name . ' (' . $tax->percent . '%)' }}
                        </td>
                        <td class="py-2 border-0 item-cell total-table-attribute-value">
                            {!! format_money_pdf($tax->amount, $invoice->user->currency) !!}
                        </td>
                    </tr>
                @endforeach
            @endif
            @isset($_avalaraTaxes)
                @if ($_avalaraTaxes->isNotEmpty())
                    <tr>
                        <td class="border-0 total-table-attribute-label">@lang('pdf_total_avalara')</td>
                        <td class="py-2 border-0 item-cell total-table-attribute-value">
                            {!! format_money_pdf($invoice->avalara_total_tax, $_currency) !!}
                        </td>
                    </tr>
                @endif
            @endisset

            @if ($invoice->discount > 0)
                @if ($invoice->discount_per_item === 'NO')
                    <tr>
                        <td class="border-0 total-table-attribute-label">
                            @if ($invoice->discount_type === 'fixed')
                                @lang('pdf_discount_label')
                            @endif
                            @if ($invoice->discount_type === 'percentage')
                                @lang('pdf_discount_label') ({{ $invoice->discount }}%)
                            @endif
                        </td>
                        <td class="py-2 border-0 item-cell total-table-attribute-value">
                            @if ($invoice->discount_type === 'fixed')
                                {!! format_money_pdf($invoice->discount_val, $invoice->user->currency) !!}
                            @endif
                            @if ($invoice->discount_type === 'percentage')
                                {!! format_money_pdf($invoice->discount_val, $invoice->user->currency) !!}
                            @endif
                        </td>
                    </tr>
                @endif
            @endif            

            @if ($invoice->tip_val > 0)
                <tr>
                    <td class="border-0 total-table-attribute-label">
                        @if ($invoice->tip_type === 'fixed')
                            @lang('pdf_tip_label')
                        @endif
                        @if ($invoice->tip_type === 'percentage')
                            @lang('pdf_tip_label') ({{ $invoice->tip }}%)
                        @endif
                    </td>
                    <td class="py-2 border-0 item-cell total-table-attribute-value">
                        @if ($invoice->tip_type === 'fixed')
                            {!! format_money_pdf($invoice->tip_val, $invoice['user']->currency) !!}
                        @endif
                        @if ($invoice->tip_type === 'percentage')
                            {!! format_money_pdf($invoice->tip_val, $invoice['user']->currency) !!}
                        @endif
                    </td>
                </tr>
            @endif 
                
            @if ($invoice->retention_total != 0 && $invoice->retention === 'YES')
                <tr>
                    <td class="border-0 total-border-left total-table-attribute-label">
                        @lang('pdf_retention')
                    </td>
                    <td class="py-8 border-0 total-border-right item-cell total-table-attribute-value"
                        style="color: #5851D8">
                        {!! format_money_pdf($invoice->retention_total, $invoice->user->currency) !!}
                    </td>
                </tr>
            @endif
            <tr>
                <td class="  total-border-left total-table-attribute-label">
                    @lang('pdf_total')
                </td>
                <td class=" total-border-right item-cell total-table-attribute-value"
                    style="color: #5851D8">
                    {!! format_money_pdf($invoice->total, $invoice->user->currency) !!}
                </td>
            </tr>
        </table>
    @endif
</div>
