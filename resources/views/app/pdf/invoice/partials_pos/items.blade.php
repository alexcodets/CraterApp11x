<table width="100%" class="items-table line" >
    <tr>
        <td class="item-table-title text-center" colspan="6">@lang('pdf_items_label')</td>
    </tr>
    <tr class="item-table-heading-row">
        <th width="20%" class="  text-left item-table-heading">@lang('pdf_items_label')</th>
        <th  width="1%" class="  text-right item-table-heading">@lang('pdf_qty')</th>
        <th  class=" text-right item-table-heading">@lang('pdf_price_label')</th>
        @if($invoice->discount_per_item === 'YES')
            <th  class="  text-right item-table-heading">@lang('pdf_discount_label')</th>
        @endif
        <th  class="text-right item-table-heading">@lang('pdf_amount_label')</th>
    </tr>
    @php
        $index = 1;
    @endphp
    @foreach ($_items as $item)
        <tr class="item-row">
           
            <td
                class=" text-left item-cell"
                style="vertical-align: top;"
            >
                <span>{{ $item->name }}</span><br>
                <span class="item-description">{!! nl2br(htmlspecialchars($item->description)) !!}</span>
            </td>
            <td
                class=" text-right item-cell"
                style="vertical-align: top;"
            >
                {{$item->quantity}}
            </td>
            <td
                class=" text-right item-cell"
                style="vertical-align: top;"
            >
                {!! format_money_pdf($item->price, $_currency) !!}
            </td>

            @if($invoice->discount_per_item === 'YES')
                <td
                    class=" text-right item-cell"
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

<div class="">
    <table width="100%" >
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
      <!--  <tr>
            <td class="border-0 total-border-left total-table-attribute-label">
                @lang('pdf_subtotal')
            </td>
            <td
                class="py-8 border-0 total-border-right item-cell total-table-attribute-value"
                style="color: #5851D8"
            >
                {!! format_money_pdf($invoice->pbx_total_items, $_currency) !!}
            </td>
        </tr>-->
    </table>
</div>


