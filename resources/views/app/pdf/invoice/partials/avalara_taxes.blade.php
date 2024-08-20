<table width="100%" class="items-table" cellspacing="0" border="0">	
    <tr>
        <td class="item-table-title" colspan="5">@lang('pdf_label_avalara')</td>
    </tr>
    <tr class="item-table-heading-row">
        <th width="2%" class="pr-20 text-right item-table-heading">#</th>
        <th width="75%" class="pl-0 text-left item-table-heading">@lang('taxes')</th>
        <th class="pr-20 text-right item-table-heading">@lang('pdf_price_label')</th>
    </tr>

	@php
		$i = 1;
		$total = 0;		      
    @endphp
         
     @if(isset($avalaraTaxesDeFinito))    
     	@if(count($avalaraTaxesDeFinito) > 0)     
    		@foreach ($avalaraTaxesDeFinito as $tax)			 			
    	
    		<tr class="item-row">    
        		<td class="pr-20 text-right item-cell" style="vertical-align: top;">
            		{{$i++}}
        		</td>  
                               
        		<td class="pl-0 text-left item-cell" style="vertical-align: top;">
            		<span>{{ $tax["name"] }} - {{ $tax["lvl"] }}</span><br>              
                
                @foreach ($tax["items"] as $item)
                	@if(count($tax["items"]) > 0)
                		<span class="item-description">
                            {{ $item["name"] }}
                        </span><br>
            		@endif         
                 @endforeach
                
        		</td>
        		<td class="text-right item-cell" style="vertical-align: top;">                	
            		{!! format_money_pdf($tax["total"], $_currency) !!}	
					
					@php
                    	$total = $total + $tax['total']
                    @endphp
					
        		</td>                
    		</tr>                 
     
    		@endforeach                     
     	@endif
    @endif 
        
</table>

<div class="total-display-container">
    <table width="100%" cellspacing="0px" border="0"
        class="total-display-table @if(count($_items) > 12) page-break @endif">
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
       <!-- <tr>
            <td class="border-0 total-border-left total-table-attribute-label">
                @lang('pdf_subtotal')
            </td>
            <td class="py-8 border-0 total-border-right item-cell total-table-attribute-value" style="color: #5851D8">
                {!! format_money_pdf($total, $_currency) !!}
            </td>
        </tr>-->
    </table>
</div>

