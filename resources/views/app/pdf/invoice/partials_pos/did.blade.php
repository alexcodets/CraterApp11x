<!-- invoice -> is_edited == 0 -->

@if(isset($_dids) && $invoice->is_edited == 0)
@if(count($_dids) > 0) 
<table width="100%" class="items-table" cellspacing="0" border="0">
    <tr>
        <td class="item-table-title text-center" colspan="4">@lang('pdf_did_label')</td>
    </tr>
    <tr class="item-table-heading-row">
        <th width="50%" class="pl-0 text-left item-table-heading">@lang('pdf_number_label')</th>
        <th class="text-center item-table-heading">@lang('pdf_qty')</th>
        <th class="pl-10 text-right item-table-heading">@lang('pdf_pro_did_name_label')</th>
        <th class="text-right item-table-heading">@lang('pdf_pro_did_price_label')</th>
    </tr>

	@php
	$template_name = [];	
	$values = [];
	$QCP = [];
	$i_qcp = 0;
	@endphp
   
	@foreach($_dids->groupBy('custom_did_id') as $custom_did => $dids)    
        @php		    
    
            // template_name
            if($dids->first()->custom_did_id == null || $dids->first()->custom_did_id == 0)
            {
                $template_name[$i_qcp] =  $dids->first()->template_did_name;
            }
            if($dids->first()->custom_did_id != null || $dids->first()->custom_did_id != 0)
            {
                if($dids->first()->name_prefix != null)
                {
                    $template_name[$i_qcp] =  $dids->first()->name_prefix;
                }else{
                    $template_name[$i_qcp] =  "Custom DID";
                }                
            }         
            //

        	$rate = empty($custom_did)
                    ? '$' . implode(', $', array_unique(  $dids->pluck('template_did_rate')->toArray()))
                    : '$' . implode(', $',  array_unique(  $dids->pluck('custom_did_rate')->toArray()));

			$values = ["Quantity" => count($dids), "Charge" => $template_name[$i_qcp], "Price" => $rate];
			$QCP[$i_qcp] = $values;
			$i_qcp++;  

        @endphp        
    @endforeach

	@php		       
		$new_array = array();	
		$i=0;

		foreach($_dids->groupBy('custom_did_id') as $custom_did => $dids)
        {        
        	$new_array[$i]["dids"] = $dids; 
        	$i++;        		        
        }

		$groups=[];
		$x=0;
		$y=0;
		
		foreach($new_array as $n_a)
        {        
        	$y=0;
        	$sub_groups = [];
        	
        	foreach($n_a["dids"] as $na)
            {            
            	$sub_groups[$y] = $na->pbx_did_number;
            	$y++;            
            }
        	        
        	$groups[$x]["dids"] = $sub_groups;
        	$x++;        
        
        }

		$did_array_list = [];
		$conta_x=0;
		$conta_y=0;
		$cont_loop=0;
		$gs = [];		

		foreach($groups as $group)
        {              
        	$total = 0;
        	$count_groups = count($group["dids"]);
        	$did_array_list[$conta_x]["QCP"]= $QCP[$conta_x];        
        
			foreach($group["dids"] as $g)
            {                        
               	array_push($gs, $g);
            	$cont_loop++;
            	$total += 1;
            
                if($cont_loop == 4)
        		{
                	$did_array_list[$conta_x][$conta_y] = $gs;
					$cont_loop= 0;	
                	$conta_y++;
                	$gs = [];
				}
            	elseif ($count_groups == $total) {
    				$did_array_list[$conta_x][$conta_y] = $gs;
                	$cont_loop= 0;	
                	$conta_y++;
                	$gs = [];
				}           	
            	
            }  
        	$conta_x++; 
        }
	@endphp  
    
    @foreach ($did_array_list as $ero)
		@foreach ($ero as $do)
    <tr class="item-row">            	    
        <td
                class="pl-0 text-left item-cell"
                style="vertical-align: top;"
            >   
    			@if(!array_key_exists('Quantity', $do))
              		@foreach ($do as $ero)    
    					{{$ero}}	
					@endforeach                     
                @else                    
                    @lang('pdf_did_list'):
                @endif                
        </td>
         <td
                class="text-center item-cell"
                style="vertical-align: top;"
            >          
                @if(array_key_exists('Quantity', $do))
                	{{$do["Quantity"]}}
                @endif   
        </td>
       <td
                class="text-right item-cell"
                style="vertical-align: top;"
            >             
                @if(array_key_exists('Charge', $do))
                	{{$do["Charge"]}}
                @endif                
        </td>
         <td
                class="text-right item-cell"
                style="vertical-align: top;"
            >         
                @if(array_key_exists('Price', $do))
                	{{$do["Price"]}}
                @endif
        </td>                  
    </tr>
    	@endforeach   
    @endforeach   
</table>
<!--<div class="total-display-container">
    <table width="100%" cellspacing="0px" border="0" class="total-display-table @if(count($dids) > 12) page-break @endif">
        <tr>
            <td class="border-0 total-border-left total-table-attribute-label">
                Invoiced Dids:
            </td>
            <td
                class="py-8 border-0 total-border-right item-cell total-table-attribute-value"
                style="color: #5851D8"
            >
                {{$invoice->count_did}}
            </td>
        </tr>
        <tr>
            <td class="border-0 total-border-left total-table-attribute-label">
                @lang('pdf_subtotal')
            </td>
            <td
                class="py-8 border-0 total-border-right item-cell total-table-attribute-value"
                style="color: #5851D8"
            >
                {!! format_money_pdf($invoice->pbx_total_did, $_currency) !!}
            </td>
        </tr>
    </table>
</div>-->
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
        <th class="text-center item-table-heading">@lang('pdf_qty')</th>
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