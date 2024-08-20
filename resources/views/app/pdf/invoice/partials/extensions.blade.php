<!-- invoice -> is_edited == 0 -->
<!-- -->
<!--
@if(isset($_extensions) && $invoice->is_edited == 0)
@if(count($_extensions) > 0) 

<table width="100%" class="items-table" cellspacing="0" border="0">
    <tr>
        <td class="item-table-title" colspan="4">@lang('pdf_extensions_label')</td>
    </tr>
    <tr class="item-table-heading-row">
        <th width="50%" class="pl-0 text-left item-table-heading">@lang('pdf_name_label')</th>
        <th class="text-center item-table-heading">@lang('pdf_quantity_label')</th>
        <th class="pl-10 text-right item-table-heading">@lang('pdf_pro_ext_name_label')</th>
        <th class="text-right item-table-heading">@lang('pdf_pro_ext_price_label')</th>
    </tr>
    
    @php 
        $template_name = [];	
	    $values = [];
	    $QCP = [];
	    $i_qcp = 0;
        $acc = 0;
        $subtotal = 0;   

        foreach ($_extensions->groupBy('template_extension_rate') as $pbx_extension_ext => $extensions)
        {            
        	$rate = empty($pbx_extension_ext)
                    ? '$' . implode(', $', array_unique(  $extensions->pluck('template_extension_rate')->toArray()))
                    : '$' . implode(', $',  array_unique(  $extensions->pluck('template_extension_rate')->toArray()));			
			
			// template_name
			$template_name[$i_qcp] =  $extensions->first()->template_extension_name;
            
            // Calcular Subtotal Extensions
            $price = empty($pbx_extension_ext)
                    ? array_unique(  $extensions->pluck('template_extension_rate')->toArray())
                    : array_unique(  $extensions->pluck('template_extension_rate')->toArray());				
			
            $count = count($extensions);
            $acc = $count * $price [0];

            $subtotal+= $acc;
             // Calcular Subtotal Extensions

			$values = ["Quantity" => count($extensions), "Charge" => $template_name[$i_qcp], "Price" => $rate];
			$QCP[$i_qcp] = $values;
			$i_qcp++;   
        } 

    @endphp    

    @php

        $new_array = array();	
		$i=0;

		foreach($_extensions->groupBy('template_extension_rate') as $pbx_extension_ext => $extensions)
        {                   
        	$new_array[$i]["extensions"] = $extensions; 
        	$i++;        		        
        }

		$groups=[];
		$x=0;
		$y=0;
		
		foreach($new_array as $n_a)
        {        
        	$y=0;
        	$sub_groups = [];           
        	
        	foreach($n_a["extensions"] as $na)
            {            
            	$sub_groups[$y] = $na->pbx_extension_ext;
            	$y++;            
            }
        	        
        	$groups[$x]["extensions"] = $sub_groups;
        	$x++;               
        
        }

        $extensions_array_list = [];
		$conta_x=0;
		$conta_y=0;
		$cont_loop=0;
		$gs = [];		

		foreach($groups as $group)
        {              
        	$total = 0;
        	$count_groups = count($group["extensions"]);
        	$extensions_array_list[$conta_x]["QCP"]= $QCP[$conta_x];        
        
			foreach($group["extensions"] as $g)
            {                        
               	array_push($gs, $g);
            	$cont_loop++;
            	$total += 1;
            
                if($cont_loop == 12)
        		{
                	$extensions_array_list[$conta_x][$conta_y] = $gs;
					$cont_loop= 0;	
                	$conta_y++;
                	$gs = [];
				}
            	elseif ($count_groups == $total) {
    				$extensions_array_list[$conta_x][$conta_y] = $gs;
                	$cont_loop= 0;	
                	$conta_y++;
                	$gs = [];
				}           	
            	
            }  
        	$conta_x++; 
        }        
    @endphp

    @foreach ($extensions_array_list as $primer_arr)
        @foreach ($primer_arr as $segundo_arr)
            <tr class="item-row">            	    
                <td
                        class="pl-0 text-left item-cell"
                        style="vertical-align: top;"
                    >   
                        @if(!array_key_exists('Quantity', $segundo_arr))
                            @foreach ($segundo_arr as $tercer_arr)    
                                {{$tercer_arr}}	
                            @endforeach                     
                        @else                    
                            @lang('pdf_extension_list'):
                        @endif                
                </td>
                <td
                        class="text-center item-cell"
                        style="vertical-align: top;"
                    >          
                        @if(array_key_exists('Quantity', $segundo_arr))
                            {{$segundo_arr["Quantity"]}}
                        @endif   
                </td>
            <td
                        class="text-right item-cell"
                        style="vertical-align: top;"
                    >             
                        @if(array_key_exists('Charge', $segundo_arr))
                            {{$segundo_arr["Charge"]}}
                        @endif                
                </td>
                <td
                        class="text-right item-cell"
                        style="vertical-align: top;"
                    >        
                                              
                        @if(array_key_exists('Price', $segundo_arr))
                            {{$segundo_arr["Price"]}}
                        @endif                        
                </td>                  
            </tr>
        @endforeach   
    @endforeach   

</table>
-->

<!--
<div class="total-display-container">
    <table width="100%" cellspacing="0px" border="0" class="total-display-table @if(count($_extensions) > 12) page-break @endif">
        <tr>
            <td class="border-0 total-border-left total-table-attribute-label">
                Invoiced Extensions:
            </td>
            <td
                class="py-8 border-0 total-border-right item-cell total-table-attribute-value"
                style="color: #5851D8"
            >
                {{$invoice->count_extension}}  
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
             
                {!! format_money_pdf($invoice->pbx_total_extension , $_currency) !!}   

            </td>
        </tr>
    </table>
</div>-->
<!--
@endif
@endif
-->

<!-- invoice -> is_edited == 1 or 0-->

@if(isset($invoice_pbx_extension_detail) && ($invoice->is_edited == 1 || $invoice->is_edited == 0))
@if(count($invoice_pbx_extension_detail) > 0) 
<table width="100%" class="items-table" cellspacing="0" border="0">
    <tr>
        <td class="item-table-title" colspan="4">@lang('pdf_extensions_label')</td>
    </tr>
    <tr class="item-table-heading-row">
        <th width="50%" class="pl-0 text-left item-table-heading">@lang('pdf_template_name')</th>
        <th class="text-center item-table-heading">@lang('pdf_quantity')</th>
        <th class="pl-10 text-right item-table-heading">@lang('pdf_price')</th>
        <th class="text-right item-table-heading">@lang('pdf_total')</th>
    </tr>

    @foreach ($invoice_pbx_extension_detail as $extension)        
            <tr class="item-row">            	    
                <td
                        class="pl-0 text-left item-cell"
                        style="vertical-align: top;"
                    >   
                    {{$extension->name}} 
                </td>
                <td
                        class="text-center item-cell"
                        style="vertical-align: top;"
                    >          
                    {{$extension->quantity}}  
                </td>
            <td
                        class="text-right item-cell"
                        style="vertical-align: top;"
                    > 
                    {!! format_money_pdf($extension->price * 100, $_currency) !!}                 
                </td>
                <td
                        class="text-right item-cell"
                        style="vertical-align: top;"
                    >  
                    {!! format_money_pdf($extension->total * 100, $_currency) !!}
                </td>                  
            </tr> 
    @endforeach   
</table>
@endif
@endif