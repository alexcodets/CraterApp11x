<!-- invoice -> is_edited == 0 -->

@if(isset($dids_group) && $invoice->is_edited == 0)
@if(count($dids_group) > 0) 
<table width="100%" class="items-table" cellspacing="0" border="0">
    <tr>
        <td class="item-table-title" colspan="4">@lang('pdf_did_label')</td>
    </tr>
    <tr class="item-table-heading-row">
        <th width="50%" class="pl-0 text-left item-table-heading">@lang('pdf_number_label')</th>
        <th class="text-center item-table-heading">@lang('pdf_quantity_label')</th>
        <th class="pl-10 text-right item-table-heading">@lang('pdf_pro_did_name_label')</th>
        <th class="text-right item-table-heading">@lang('pdf_pro_did_price_label')</th>
    </tr>

    @foreach ($dids_group as $group)        
            <tr class="item-row">            	    
                <td
                        class="pl-0 text-left item-cell"
                        style="vertical-align: top;"
                    >  
                    Did List:
                    <br>
                    @foreach ($group["dids"] as $did)  
                        @foreach ($did as $d)  
                            {{  $d  }}  
                        @endforeach  
                    <br>
                    @endforeach                        
                </td>
                <td
                        class="text-center item-cell"
                        style="vertical-align: top;"
                    >          
                    {{ $group->qty }}  
                </td>
            <td
                        class="text-right item-cell"
                        style="vertical-align: top;"
                    > 
                    {{ $group->name_prefix }}                               
                </td>
                <td
                        class="text-right item-cell"
                        style="vertical-align: top;"
                    >  
                    {!! format_money_pdf($group->price * 100, $_currency) !!}
                </td>                  
            </tr> 
    @endforeach   
</table>
@endif
@endif

<!-- invoice -> is_edited == 1 -->

@if(isset($invoice_pbx_did_detail) && $invoice->is_edited == 1)
@if(count($invoice_pbx_did_detail) > 0) 
<table width="100%" class="items-table" cellspacing="0" border="0">
    <tr>
        <td class="item-table-title" colspan="4">@lang('pdf_did_label')</td>
    </tr>
    <tr class="item-table-heading-row">
        <th width="50%" class="pl-0 text-left item-table-heading">@lang('pdf_template_name')</th>
        <th class="text-center item-table-heading">@lang('pdf_quantity')</th>
        <th class="pl-10 text-right item-table-heading">@lang('pdf_price')</th>
        <th class="text-right item-table-heading">@lang('pdf_total')</th>
    </tr>

    @foreach ($invoice_pbx_did_detail as $did)        
            <tr class="item-row">            	    
                <td
                        class="pl-0 text-left item-cell"
                        style="vertical-align: top;"
                    >   
                    {{$did->name}} 
                </td>
                <td
                        class="text-center item-cell"
                        style="vertical-align: top;"
                    >          
                    {{$did->quantity}}  
                </td>
            <td
                        class="text-right item-cell"
                        style="vertical-align: top;"
                    > 
                    {!! format_money_pdf($did->price * 100, $_currency) !!}                 
                </td>
                <td
                        class="text-right item-cell"
                        style="vertical-align: top;"
                    >  
                    {!! format_money_pdf($did->total * 100, $_currency) !!}
                </td>                  
            </tr> 
    @endforeach   
</table>
@endif
@endif