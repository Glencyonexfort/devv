<article>    
    {{-- Start:: Material Issues items --}}                
        <table class="inventory">
            <thead>
                <tr>
                    <th><span >Item Name & Description</span></th>
                    <th style="text-align: right;"><span >Qty</span></th>
                    <th style="text-align: right;"><span style="margin-right: 22px;">Date Issued</span>                                                               
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
                @if(!$material_return_items->isEmpty())
                @foreach($material_return_items as $item)
                    <tr id="material_return_line_div_view_{{ $item->id }}" class="material_return_line_div">
                        <td>
                            <span>{{ $item->name }}</span><br/>
                            <span>{!! html_entity_decode(nl2br($item->description)) !!}</span>
    
                        </td>
                        <td>
                            <span>{{ number_format((float)$item->quantity, 2, '.', '') }}</span>
                        </td>
                        <td>
                            <span data-prefix style="margin-right: 22px;">
                                {{-- {{$global->currency_symbol}}{{ number_format((float)$item->amount, 2, '.', '') }} --}}
                                <div class="list-icons float-right">
                                    <div class="dropdown">
                                        <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown"><i class="icon-menu"></i></a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a href="#" class="dropdown-item edit_material_return_btn" data-toggle="modal" data-target="#call" data-id="{{ $item->id }}"><i class="icon-pencil"></i> Edit</a>
                                                <a href="#" class="delete_material_return_btn dropdown-item" data-id="{{ $item->id }}" style="color: #ff0000"><i class="icon-trash"></i> Delete</a>        
                                            </div>
                                    </div>
                                </div>
                                <span>{{ date('m/d/Y', strtotime($item->created_date)) }}</span> 
                            </span>
                        </td>
                    </tr>
                    <tr id="material_return_line_div_edit_{{ $item->id }}" class="bgblu hidden" data-row="0">
                        <td>
                            <span>
                                <div class="form-group">
                                    <select id="material_return_product_edit_{{ $item->id }}" class="material_return_product_edit form-control" data-row="{{ $item->id }}">
                                        <option value="0"></option>
                                        @foreach($products as $product)
                                        <option value="{{ $product->name }}" data-pid="{{ $product->id }}" data-desc="{{ $product->description }}" data-price="{{ $product->price }}" data-tax="{{ $product->tax_id }}" data-type="{{ $product->product_type }}" data-product_type="{{ $product->customer_type }}"
                                            @if($product->id == $item->item_id)
                                            selected
                                            @endif
                                            >{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                    <textarea id="material_return_description_edit_{{ $item->id }}" class="form-control mt-1" name="description" placeholder="Description">{!! html_entity_decode(nl2br($item->description)) !!}</textarea>
                                </div>
                            </span>
                        </td>
                        <td>
                            <span>
                                <div class="form-group">
                                    <div class="input-group input-group-sm">    
                                        <input id="material_return_qty_edit_{{ $item->id }}" type="number" name="qty" class="material_return_qty_edit form-control" data-row="{{ $item->id }}" value="{{ $item->quantity }}"> 
                                    </div>
                                </div>
                            </span>
                        </td>
                        <td>
                            <p id="edit_date_{{ $item->id }}">{{ date('m/d/Y', strtotime($item->created_date)) }}</p>
                            <input type="hidden" id="job_id" class="material_return_item_type_new" value="{{ $job_id }}"/>
                            <input type="hidden" id="lead_id" class="material_return_item_type_new" value="{{ $crmlead->id }}"/>
                            <input type="hidden" id="material_return_item_type_edit_{{ $item->id }}" value=""/>
                            <div class="d-flex justify-content-start align-items-center m-t-10">
                                <button type="button" class="btn btn-light cancel_update_material_return_btn" data-id="{{ $item->id }}"> Cancel</button>
                                <button type="button" class="btn btn-success ml-2 update_material_return_btn" data-id="{{ $item->id }}"> Update</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="3">No record available !</td>
                </tr>
                @endif 
    
                <tr id="material_return_line_div_new" class="bgblu material_return_line_div hidden" data-row="0">
                    <td>
                        <span>
                            <div class="form-group">
                                <select class="form-control material_return_new">
                                    <option value="0"></option>
                                    @foreach($products as $product)
                                        @foreach ($material_issue_items as $issue)
                                            @if ($issue->item_id == $product->id)
                                                <option value="{{ $product->name }}" data-pid="{{ $product->id }}" data-desc="{{ $product->description }}" data-price="{{ $product->price }}" data-tax="{{ $product->tax_id }}" data-type="{{ $product->product_type }}" data-product_type="{{ $product->customer_type }}" >{{ $product->name }}</option>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </select>
                                <textarea class="material_return_description_new form-control mt-1" name="description" placeholder="Description"></textarea>
                            </div>
                        </span>
                    </td>
                    <td>
                        <span>
                            <div class="form-group">
                                <div class="input-group input-group-sm">    
                                    <input type="number" name="qty" class="form-control material_return_qty_new" value=""> 
                                </div>
                            </div>
                        </span>
                    </td>
                    <td>
                        <p class="return_date"></p>
                        <input type="hidden" id="job_id" class="material_return_item_type_new" value="{{ $job_id }}"/>
                        <input type="hidden" id="lead_id" class="material_return_item_type_new" value="{{ $crmlead->id }}"/>
                        <div class="d-flex justify-content-start align-items-center m-t-10">
                            <button type="button" class="btn btn-light cancel_material_return_btn"> Cancel</button>
                            <button type="button" class="btn btn-success ml-2 save_material_return_btn"> Save</button>
                        </div>
                    </td>
                </tr>
    
        </tbody>                
        </table>
    <div class="float-left">
        <button id="add_material_return_line" type="button" class="btn plus_btn"><i class="icon-plus3"></i></button>
    </div>
</article>
    {{-- End:: Material Issues items --}}