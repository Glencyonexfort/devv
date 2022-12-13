<article>                    
    <table class="payment">
        <thead>
            <tr>
                <th><span >Payment Method</span></th>
                <th><span >Reference / Notes</span></th>
                <th><span >Paid On</span></th>
                <th style="text-align: right;"><span style="margin-right: 22px;">Amount</span></th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $total_payments=0;
            ?>
            @if($payment_items)
            @foreach($payment_items as $item)
                <?php 
                    $total_payments+=$item->amount;
                ?>
                <tr id="payment_line_div_view_{{ $item->id }}" class="payment_line_div">
                    <td>
                        <span>{{ $item->gateway }}</span><br/>
                    </td>
                    <td>
                        <span>{{ $item->remarks }}</span>
                    </td>
                    <td>
                        <span>
                            {{ date($global->date_format,strtotime($item->paid_on)) }}
                        </span>
                    </td>
                    <td>
                        <span data-prefix style="margin-right: 22px;">
                            {{$global->currency_symbol}}{{ number_format((float)$item->amount, 2, '.', '') }}
                            <div class="list-icons float-right">
                                <div class="dropdown">
                                    <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown"><i class="icon-menu"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="#" class="dropdown-item edit_payment_btn" data-toggle="modal" data-target="#call" data-id="{{ $item->id }}"><i class="icon-pencil"></i> Edit</a>
                                            <a href="#" class="delete_payment_btn dropdown-item" data-id="{{ $item->id }}" style="color: #ff0000"><i class="icon-trash"></i> Delete</a>        
                                        </div>
                                </div>
                            </div> 
                        </span>
                    </td>
                </tr>
                <tr id="payment_line_div_edit_{{ $item->id }}" class="bgblu hidden" data-row="0">
                    <td>
                        <span>
                            <div class="form-group">
                                <select id="payment_method_edit_{{ $item->id }}" class="payment_method_edit form-control" data-row="{{ $item->id }}">
                                    <option value="0"></option>
                                    @foreach($payment_methods as $method)
                                    <option value="{{ $method->name }}" data-desc="{{ $method->description }}"
                                        @if($method->name == $item->gateway)
                                        selected=""
                                        @endif
                                        >{{ $method->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </span>
                    </td>
                    <td>
                        <span>
                            <textarea id="payment_description_edit_{{ $item->id }}" class="form-control mt-1" name="description" placeholder="Description">
                                {{ $item->remarks }}
                            </textarea>
                        </span>
                    </td>
                    <td>
                        <span>
                            <div class="form-group">
                                
                            </div>
                        </span>
                    </td>
                    <td>
                        <input type="text" id="payment_total_edit_field_{{ $item->id }}" class="form-control" value="{{ $item->amount }}"/>
                        <div class="d-flex justify-content-start align-items-center m-t-10">
                            <button type="button" class="btn btn-light cancel_update_payment_btn" data-id="{{ $item->id }}"> Cancel</button>
                            <button type="button" class="btn btn-success ml-2 update_payment_btn" data-id="{{ $item->id }}"> Update</button>
                        </div>
                    </td>
                </tr>
            @endforeach
            @endif
<tr id="payment_line_div_new" class="bgblu payment_line_div hidden" data-row="0">
    <td>
        <span>
            <div class="form-group">
                <select class="form-control payment_method_new">
                    <option value="0"></option>
                    @foreach($payment_methods as $method)
                    <option value="{{ $method->name }}" data-desc="{{ $method->description }}">{{ $method->name }}</option>
                    @endforeach
                </select>
            </div>
        </span>
    </td>
    <td>
        <span>
            <textarea class="payment_description_new form-control mt-1" name="description" value="" placeholder="Description">
            </textarea>
        </span>
    </td>
    <td>
        <span>
            <input name="payment_paidon_date" type="text" class="form-control daterange-single" value="{{ date('d/m/Y') }}">
        </span>
    </td>
    <td>
        <input type="text" class="form-control payment_total_new_field"/>
        <div class="d-flex justify-content-start align-items-center m-t-10">
            <button type="button" class="btn btn-light cancel_payment_btn"> Cancel</button>
            <button type="button" class="btn btn-success ml-2 save_payment_btn"> Save</button>
        </div>
    </td>
</tr>

</tbody>                
</table>
<div class="float-left">
    <button id="add_payment_line" type="button" class="btn plus_btn"><i class="icon-plus3"></i></button>
</div>
<table class="balance">
    <tr>
        <th><span >Payments</span></th>
        <td><span id="grand_total_payment">{{$global->currency_symbol}}{{ number_format((float)$total_payments, 2, '.', '') }}</span></td>
    </tr>
    <tr>
        <th><span >Balance</span></th>
        <td><span id="grand_total_balance">{{$global->currency_symbol}}{{ number_format((float)($totalAmount-$total_payments), 2, '.', '') }}</span></td>
    </tr>
</table>
</article>  