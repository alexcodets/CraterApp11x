<table width="100%" class="items-table" cellspacing="0" border="0">
    <tr>
        <td class="item-table-title" colspan="5">@lang('pdf_items_label')</td>
    </tr>
    <tr class="item-table-heading-row">
        <th width="2%" class="pr-20 text-right item-table-heading">#</th>
        <th width="40%" class="pl-0 text-left item-table-heading">@lang('pdf_items_label')</th>
        <th class="pr-20 text-right item-table-heading">@lang('pdf_quantity_label')</th>
        <th class="pr-20 text-right item-table-heading">@lang('pdf_price_label')</th>
        @if($invoice->discount_per_item === 'YES')
            <th class="pl-10 text-right item-table-heading">@lang('pdf_label_avalara')</th>
        @endif
        <th class="text-right item-table-heading">@lang('pdf_label_avalara')</th>
    </tr>
    @php
        $index = 1;
    @endphp
    @foreach ($_avalaraTaxes as $tax)
        <tr class="item-row">
            <td
                class="pr-20 text-right item-cell"
                style="vertical-align: top;"
            >
                {{$index}}
            </td>
            <td
                class="pl-0 text-left item-cell"
                style="vertical-align: top;"
            >
                <span>{{ $tax->name }}</span><br>
                <span class="item-description">{!! nl2br(htmlspecialchars('futuro')) !!}</span>
            </td>
            <td
                class="pr-20 text-right item-cell"
                style="vertical-align: top;"
            >
                {{$tax->tax / 100}}
            </td>
            <td
                class="pr-20 text-right item-cell"
                style="vertical-align: top;"
            >
                {!! format_money_pdf($item->price, $_currency) !!}
            </td>

            @if($invoice->discount_per_item === 'YES')
                <td
                    class="pl-10 text-right item-cell"
                    style="vertical-align: top;"
                >
                    @if($item->discount_type === 'fixed')
                        {!! format_money_pdf($item->discount_val, $_currency) !!}
                    @endif
                    @if($item->discount_type === 'percentage')
                        {{$item->discount}}%
                    @endif
                </td>
            @endif

            <td
                class="text-right item-cell"
                style="vertical-align: top;"
            >
                {!! format_money_pdf($item->total, $_currency) !!}
            </td>
        </tr>
        @php
            $index += 1;
        @endphp
    @endforeach
</table>

<div class="total-display-container">
    <table width="100%" cellspacing="0px" border="0" class="total-display-table @if(count($_items) > 12) page-break @endif">
        <!--<tr>
            <td class="border-0 border-left-top total-table-attribute-label">
                @lang('pdf_count')
            </td>
            <td class="py-2 border-0 border-right-top item-cell total-table-attribute-value">
                {{ count($_items) }}
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
                class="py-8 border-0 total-border-right item-cell total-table-attribute-value"
                style="color: #5851D8"
            >
                {!! format_money_pdf($invoice->pbx_total_items, $_currency) !!}
            </td>
        </tr>
    </table>
</div>


