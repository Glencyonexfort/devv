<article class="mt-5">  
    <hr/> 
    <h3>Estimate</h3>  
    <p class="muted">Generate an estimate in this section only if you want the Storage Invoice separate from the Removals job invoice</p>
    <?php
        $estimateID = ($quote)?$quote->id:0;
    ?>
    <input id="Storage_estimateID" value="{{ $estimateID }}" type="hidden"/>
    <table class="inventory">
        <thead>
            <tr>
                <th><span >Item Name & Description</span></th>
                <th><span >Tax</span></th>
                <th style="text-align: right;"><span >Unit Price</span></th>
                <th style="text-align: right;"><span >Qty</span></th>
                <th style="text-align: right;"><span style="margin-right: 22px;">Total Inc Tax</span>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $total_excl_tax=0;
                $total_incl_tax=0;
                $rate_percent=0;
                $total_tax=0;
            ?>
            @if(isset($quoteItem))
            @foreach($quoteItem as $item)
                <?php 
                    $total_excl_tax+=($item->unit_price*$item->quantity);
                    $total_tax += $item->amount - ($item->unit_price*$item->quantity);
                ?>
                <tr id="s_estimate_line_div_view_{{ $item->id }}" class="s_estimate_line_div">
                    <td>
                        <span>{{ $item->name }}</span><br/>
                        <span>{!! html_entity_decode(nl2br($item->description)) !!}</span>

                    </td>
                    <td>
                        <span>
                            @foreach($taxs as $tax)
                            @if($tax->id==$item->tax_id)
                                {{ $tax->tax_name }}
                                <?php 
                                    $rate_percent = $tax->rate_percent;
                                ?>
                                @break
                            @endif
                            @endforeach
                        </span>
                    </td>
                    <td>
                        <span data-prefix>{{$global->currency_symbol}}{{ number_format((float)$item->unit_price, 2, '.', '') }}</span>
                    </td>
                    <td>
                        <span>{{ number_format((float)$item->quantity, 2, '.', '') }}</span>
                    </td>
                    <td>
                        <span data-prefix style="margin-right: 22px;">
                            {{$global->currency_symbol}}{{ number_format((float)$item->amount, 2, '.', '') }}
                            <div class="list-icons float-right">
                                <div class="dropdown">
                                    <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown"><i class="icon-menu"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="#" class="dropdown-item edit_s_estimate_btn" data-toggle="modal" data-target="#call" data-id="{{ $item->id }}"><i class="icon-pencil"></i> Edit</a>
                                            <a href="#" class="delete_s_estimate_btn dropdown-item" data-id="{{ $item->id }}" style="color: #ff0000"><i class="icon-trash"></i> Delete</a>        
                                        </div>
                                </div>
                            </div> 
                        </span>
                    </td>
                </tr>
                <tr id="s_estimate_line_div_edit_{{ $item->id }}" class="bgblu hidden" data-row="0">
                    <td>
                        <span>
                            <div class="form-group">
                                <select id="s_product_edit_{{ $item->id }}" class="s_product_edit form-control" data-row="{{ $item->id }}">
                                    <option value="0"></option>
                                    @foreach($products as $product)
                                    <option value="{{ $product->name }}" data-pid="{{ $product->id }}" data-desc="{{ $product->description }}" data-price="{{ $product->price }}" data-tax="{{ $product->tax_id }}" data-type="{{ $product->product_type }}"
                                        @if($product->name == $item->name)
                                        selected=""
                                        @endif
                                        >{{ $product->name }}</option>
                                    @endforeach
                                </select>
                                <textarea id="s_prod_description_edit_{{ $item->id }}" class="form-control mt-1" name="description" placeholder="Description">{{ $item->description }}</textarea>
                            </div>
                        </span>
                    </td>
                    <td>
                        <span>
                            <div class="form-group">
                                <select id="s_prod_tax_edit_{{ $item->id }}" name="tax" data-placeholder="TAX" class="s_prod_tax_edit form-control" data-row="{{ $item->id }}">
                                    <option value="0" data-rate="0"></option>
                                    @foreach($taxs as $tax)
                                    <option value="{{ $tax->id }}" data-rate="{{ $tax->rate_percent }}"
                                        @if($tax->id == $item->tax_id)
                                        selected=""
                                        @endif
                                        >{{ $tax->tax_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </span>
                    </td>
                    <td>
                        <span>
                            <div class="form-group">
                                <div class="input-group input-group-sm">    
                                    <input id="s_prod_price_edit_{{ $item->id }}" type="number" name="price" class="s_prod_price_edit form-control" data-row="{{ $item->id }}" value="{{ $item->unit_price }}"> 
                                </div>
                            </div>
                        </span>
                    </td>
                    <td>
                        <span>
                            <div class="form-group">
                                <div class="input-group input-group-sm">    
                                    <input id="s_prod_qty_edit_{{ $item->id }}" type="number" name="qty" class="s_prod_qty_edit form-control" data-row="{{ $item->id }}" value="{{ $item->quantity }}"> 
                                </div>
                            </div>
                    </span>
                    </td>
                    <td>
                        {{-- <div id="prod_total_edit_{{ $item->id }}" class="font-weight-bold" style="text-align: right;margin-right: 38px;">{{ $item->amount }}
                        </div> --}}
                        <input type="hidden" id="s_prod_type_edit_field_{{ $item->id }}" class="s_prod_type_edit form-control" data-row="{{ $item->id }}" value="{{ $item->type }}"/>
                        <input type="text" id="s_prod_total_edit_field_{{ $item->id }}" class="s_prod_total_edit form-control" data-row="{{ $item->id }}" value="{{ $item->amount }}"/>
                        <div class="d-flex justify-content-start align-items-center m-t-10">
                            <button type="button" class="btn btn-light cancel_update_s_estimate_btn" data-id="{{ $item->id }}"> Cancel</button>
                            <button type="button" class="btn btn-success ml-2 update_s_estimate_btn" data-id="{{ $item->id }}"> Update</button>
                        </div>
                    </td>
                </tr>
            @endforeach
            @endif
            <tr id="s_estimate_line_div_new" class="bgblu s_estimate_line_div hidden" data-row="0">
                <td>
                    <span>
                        <div class="form-group">
                            <select class="form-control s_product_new">
                                <option value="0"></option>
                                @foreach($products as $product)
                                <option value="{{ $product->name }}" data-pid="{{ $product->id }}" data-desc="{{ $product->description }}" data-price="{{ $product->price }}" data-tax="{{ $product->tax_id }}" data-type="{{ $product->product_type }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                            <textarea class="s_prod_description_new form-control mt-1" name="description" placeholder="Description"></textarea>
                        </div>
                    </span>
                </td>
                <td>
                    <span>
                        <div class="form-group">
                            <select name="tax" data-placeholder="TAX" class="s_prod_tax_new form-control">
                                <option value="0" data-rate="0"></option>
                                @foreach($taxs as $tax)
                                <option value="{{ $tax->id }}" data-rate="{{ $tax->rate_percent }}">{{ $tax->tax_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </span>
                </td>
                <td>
                    <span>
                        <div class="form-group">
                            <div class="input-group input-group-sm">    
                                <input type="number" name="price" class="form-control s_prod_price_new" value=""> 
                            </div>
                        </div>
                    </span>
                </td>
                <td>
                    <span>
                        <div class="form-group">
                            <div class="input-group input-group-sm">    
                                <input type="number" name="qty" class="form-control s_prod_qty_new" value=""> 
                            </div>
                        </div>
                    </span>
                </td>
                <td>
                    {{-- <div class="prod_total_new font-weight-bold" style="text-align: right;margin-right: 38px;">
                    </div>
                    <input type="hidden" class="prod_total_new_field"/> --}}
                    <input type="number" class="s_prod_total_new_field form-control"/>
                    <input type="hidden" class="s_prod_type_new_field form-control"/>
                    <div class="d-flex justify-content-start align-items-center m-t-10">
                        <button type="button" class="btn btn-light cancel_s_estimate_btn"> Cancel</button>
                        <button type="button" class="btn btn-success ml-2 save_s_estimate_btn"> Save</button>
                    </div>
                </td>
            </tr>
    </tbody>                
</table>
<div class="float-left">
    <button id="add_s_estimate_line" type="button" class="btn plus_btn"><i class="icon-plus3"></i></button>
</div>

<table class="balance">
    <tr>
        <th><span >Total (excl tax)</span></th>
        <td><span id="grand_total_excl_tax">{{$global->currency_symbol}}{{ number_format((float)$total_excl_tax, 2, '.', '') }}</span></td>
    </tr>
    @if($quote)
    <tr>
        <th><span>
            Discount
                <span id="storage_discount_type_label" style="margin-right: 22px;text-transform:capitalize;font-weight: 500">
                    @if($quote->discount>0)
                        {{ ucfirst($quote->discount_type) }}
                    @endif
                </span>
                <input type="hidden" id="storage_discount_type_field"/>
                <div class="list-icons float-right">
                    <div class="dropdown">
                        <a href="javascript:void(0)" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown"><i class="icon-menu"></i></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="javascript:void(0)" class="dropdown-item storage_discount_type_option" data-val="fixed"><i class="icon-coin-dollar"></i> Fixed</a>
                                <a href="javascript:void(0)" class="dropdown-item storage_discount_type_option" data-val="percent"><i class="icon-percent"></i> Percent</a>        
                            </div>
                    </div>
                </div>
            </span>
        </th>
        <td>
            <span>
                <div id="storage_discount_value" class="form-group hidden">
                    <div class="input-group input-group-sm">    
                        <input type="number" id="storage_discount_value_field" class="form-control" value="{{ $quote->discount }}"/> 
                    </div>
                    <div class="d-flex justify-content-start align-items-center m-t-10">
                        <button id="cancel_storage_discount_btn" type="button" class="btn btn-light"> Cancel</button>
                        <button id="save_storage_discount_btn" type="button" class="btn btn-success ml-2"> Save</button>
                    </div>
                </div>
                <div id="storage_discount_label">
                    @if($quote->discount_type=="percent")
                        {{ $quote->discount.'%' }}
                        <?php
                            $total_excl_tax_after_discount = $total_excl_tax - ($quote->discount/100 * $total_excl_tax);
                        ?>                        
                    @else
                        {{$global->currency_symbol}}{{ number_format((float)$quote->discount, 2, '.', '') }}
                        <?php
                            $total_excl_tax_after_discount = $total_excl_tax - $quote->discount;
                        ?>
                    @endif
                </div>
            </span>
        </td>
    </tr>
    <?php
        $total_incl_tax = $total_tax + $total_excl_tax_after_discount;
    ?>
    <tr>
        <th><span >Total (excl tax) after discount</span></th>
        <td><span id="grand_total_excl_tax">{{$global->currency_symbol}}{{ number_format((float)$total_excl_tax_after_discount, 2, '.', '') }}</span></td>
    </tr>
    @endif
    <tr>
        <th><span >Tax</span></th>
        <td><span id="grand_total_tax">{{$global->currency_symbol}}{{ number_format((float)($total_tax), 2, '.', '') }}</span></td>
    </tr>
    <tr>
        <th><span>Total (incl tax)</span></th>
        <td><span id="grand_total_incl_tax">{{$global->currency_symbol}}{{ number_format((float)$total_incl_tax, 2, '.', '') }}</span></td>
    </tr>
</table>
</article> 