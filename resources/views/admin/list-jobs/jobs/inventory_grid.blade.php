<style>
    .btn-default{
        background-color: #f6f5f5!important;
        border: 1px solid #e4e7ea!important;
        border-radius: 0px!important;
        padding: 3px 11px!important;
        height: 38px!important;
        width: 38px!important;
        font-size: 20px!important;
    }
    .bootstrap-touchspin-down{
        border-right: 0px!important;
    }
    .bootstrap-touchspin-up{
        border-left: 0px!important;
    }
</style>
<div class="content-wrap">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <div class="calculator_list">
                        {!! Form::open(['id'=>'generalForm','class'=>'ajax-form','method'=>'POST']) !!}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="bottom_box">
                                        <input type="text" id="answer" name="answer" class="form-control" placeholder="0 CBM (@lang('modules.listJobs.approx'))">
                                        <p class="muted">@lang('modules.listJobs.click_on_calculate')</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <span style="margin-left: 50px;">
                                        <button type="button" class="btn btn-sm btn-light inventoryPdfGenerate" data-jobid="{{$job_id}}" data-type="Moving"><i class="icon-clipboard3"></i> Generate Inventory PDF</button>
                                        <button type="button" id="inventoryPdfDownload" class="btn btn-sm btn-light ml-2 inventoryPdfDownload" data-jobid="{{$job_id}}" @if (empty($job) || $job->inventory_file_name == null) disabled @endif><i class="icon-file-pdf"></i> Download</button>
                                    </span>
                                </div>
                                <div class="col-md-2" style="text-align: right;">
                                    <div class="btn_calculate">
                                        <input class="btn-calculate btn bg-teal-400" type="button" value="@lang('modules.listJobs.calculate')" style="font-size: 16px;">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="panel-group inventory" id="accordion" style="width: 100%">
                                    <?php
                                    $maincount = 0;
                                    $count = 0;
                                    $now = "";
                                    foreach ($inventory_groups as $grp) {
                                    $maincount = $grp->group_id; ?>
                                    <div class="panel panel-danger">
                                        <div class="panel-heading inventory-panel-heading">
                                            <h4 class="panel-title" style="text-transform: capitalize">
                                                <a role="button" data-toggle="collapse" data-parent="#accordion" data-target="#collapse<?php echo $maincount; ?>" href="#collapse<?php echo $maincount; ?>" aria-expanded="true" aria-controls="collapse<?php echo $maincount; ?>">
                                                    <i class="more-less icon-down-open highlight"></i> <?php
                                                         $grp->group_name; 
                                                         $sor->sort($grp);
                                                         echo "$sor";

                                                         ?> 
                                                    <span class="item_count pull-right" id="group_count_<?php echo $maincount; ?>" style="font-weight: normal">
                                                        0 <span class="muted">item selected</span>
                                                    </span>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapse<?php echo $maincount; ?>" class="panel-collapse collapse full-width">
                                            <div class="panel-body">
                                                <div class="margin-top">
                                                    <?php
                                                    foreach ($getInventoryItems as $item) {
                                                    if ($grp->group_id == $item->group_id) {
                                                    $count = $item->id;
                                                    ?>

                                                    <div class="form-group row">
                                                        <label class="col-lg-5 col-form-label" for="quote_iteam<?php echo $count; ?>" style="text-align: right">
                                                            <?php echo $item->item_name; ?></label>
                                                            <div class="col-sm-7">
                                                        <div class="input-group bootstrap-touchspin" style="width: 150px;">
                                                            <input type="text" id="quote_item<?php echo $count; ?>" name="<?php echo $count; ?>" class="inventory_qty form-control touchspin-step" style="display: block;text-align: center;">
                                                            <a href="javascript:void(0)" style="color: black;"><i data-id="<?php echo $count; ?>" class="reset fa fa-trash ml-3 mt-2"></i></a>
                                                        </div>
                                                    </div>
                                                    </div>
                                                    <?php
                                                    }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="btn_calculate pull-right"><input id="btn-calculate" class="btn-calculate btn bg-teal-400" type="button" value="@lang('modules.listJobs.calculate')" style="font-size: 16px;"></div>
                                </div>
                            </div>
                        {!! Form::close() !!}
                    </div>
                    <form id="miscellanceous_data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <br/>
                                    <p class="job-label-txt job-status green-status">
                                        Miscellaneous Items
                                        <span class="item_count pull-right">
                                            <span class="text-xlight" id="totalItems" >{{ count($miscllanceous_items) }} items</span> 
                                        </span>
                                    </p>
                                <div class="table-responsive">
                                        <table class="tablee" width="100%" id="miscellaneous-table">
                                            <thead>
                                            <tr>
                                                <th>Item Name</th>
                                                <th>CBM</th>
                                                <th>Quantity</th>
                                                <th style="width: 10%;"></th>
                                            </tr>
                                            <tbody>
                                                @if (count($miscllanceous_items))
                                                    @foreach ($miscllanceous_items as $item)
                                                        <tr style="background-color: white;" id="tr_{{ $item->id }}">
                                                            <td><input type="text" name="name[]" placeholder="Item #" value="{{ $item->misc_item_name }}" class="input form-control" style="width: 100%"></td>
                                                            <td><input type="text" name="cbm[]" placeholder="cbm" value="{{ $item->misc_item_cbm }}" class="input form-control" style="width: 80px;"></td>
                                                            <td><input type="text" name="quantity[]" placeholder="quantity" value="{{ $item->quantity }}" class="input form-control" style="width: 80px;"></td>
                                                            <td>
                                                                <a class="btn removeItem" data-inv_id="{{ $item->id }}" data-job_id="{{ $job_id }}"><i class="icon-trash"></i></a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    @for ($i = 1; $i < 6; $i++)
                                                        <tr style="background-color: white;">
                                                            <td><input type="text" name="name[]" placeholder="Item # <?php echo $i; ?>" class="input form-control" style="width: 100%"></td>
                                                            <td><input type="text" name="cbm[]" placeholder="cbm" class="input form-control" style="width: 80px;"></td>
                                                            <td><input type="text" name="quantity[]" placeholder="quantity" class="input form-control" style="width: 80px;"></td>
                                                            <td></td>
                                                        </tr>
                                                    @endfor
                                                @endif
                                            </tbody>
                                            </thead>

                                        </table>
                                        <div class="float-left">
                                            <button id="btn_add_rows" type="button" class="btn btn-light"><i class="icon-plus3"></i></button>
                                        </div>
                                        
                                    
                                </div>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-sm-12">
                                <div class="pull-right"><input class="btn bg-teal-400" type="submit" value="@lang('modules.listJobs.calculate')" style="font-size: 16px;"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</div>

@push('footer-script')
    <script src="{{ asset('bootstrap/jquery.bootstrap-touchspin.js')}}"></script>
    <script type="text/javascript">
        function deleteMiscItem(job_id, inv_id) {
            $.easyAjax({
                url: "{{route('admin.list-jobs.delete-inventory-data')}}",
                container: '#generalForm',
                redirect: true,
                type: "POST",
                dataType: "json",
                data: {
                    "inv_id": inv_id,
                    "job_id": job_id,
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data) {
                    if (data.success == 1) {
                        $('#tr_' + inv_id).remove();
                        $('#misc_count').html(data.countInvItems + ' <span class="muted">items</span>');
                        $('#answer').val(data.totalCBM + " CBM (@lang('modules.listJobs.approx'))");
                    }
                    //console.log(data);
                },
                error: function(xhr, status, error) {
                    console.log(xhr);
                }
            });
        }
        function serialize(that) {
            $.each($(that).serializeArray(), function(idx, el) {
                out += delim + el.name + '=' + el.value;
                delim = "&"; // Trick to correctly append delimiter
            });
            //console.log(out);
        }
        calculate_items();
        function calculate_items() {
            // for each panel-body group
            $(".panel").each(function(index) {
                //console.log('PANEL ' +  index);
                var total_panel_qty = 0;
                var panel = $(this);
                var quantities = panel.find('input');
                quantities.each(function(index) {
                    var quantity_input = $(this);
                    //console.log(quantity_input.val());
                    if (quantity_input.val() > 0) {
                        total_panel_qty += parseInt(quantity_input.val());
                    } else {
                        quantity_input.val('');
                    }
                });
                //console.log('total: ' + total_panel_qty);
                if (total_panel_qty <= 0) {
                    panel.find('.item_count:first').html('<span class="text-xlight"> ' + total_panel_qty + ' <span class="muted">items selected</span></span>');
                } else if (total_panel_qty == 1) {
                    panel.find('.item_count:first').html('<i class="icon-checkmark4 success"></i> ' + total_panel_qty + ' <span class="muted">item selected</span>');
                } else if (total_panel_qty > 1) {
                    panel.find('.item_count:first').html('<i class="icon-checkmark4 success"></i> ' + total_panel_qty + ' <span class="muted">items selected</span>');
                }
            });
            $(".panel input").TouchSpin({
                //verticalbuttons: true,
                //verticalupclass: 'glyphicon glyphicon-plus',
                //verticaldownclass: 'glyphicon glyphicon-minus',
                //buttondown_class: "btn btn-default",
                //buttonup_class: "btn btn-default"
            });
        }
        function init_inventory_data() {
            //alert(event_id);
            $.easyAjax({
                url: "{{route('admin.list-jobs.get-inventory-data')}}",
                container: '#generalForm',
                type: "POST",
                redirect: true,
                dataType: "json",
                data: {
                    "job_id": "{{ $job->job_id }}",
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data) {
                    var SelectionData = data.inventoryCalc;
                    var totalItems = 0
                    if (SelectionData) {
                        $.each(SelectionData, function(index, elem) {
                            var calcInventory_id = elem.inventory_id.toString();
                            var sub_str = calcInventory_id.substr(0, 4);
                            var $div = $(".calculator_list input[name='" + calcInventory_id + "']").val(elem.quantity);
                            if (sub_str == '9000') {
                                totalItems++;
                                var $div = $(".calculator_list input[name='" + calcInventory_id + "_name']").val(elem.misc_item_name);
                                var $div = $(".calculator_list input[name='" + calcInventory_id + "_cbm']").val(elem.misc_item_cbm);
                            }
                        });
                        var groupCount = data.inventoryCalcCountbyGroupID;
                        $.each(groupCount, function(index, groupDetail) {
                            $("#group_count_" + groupDetail.group_id).html(groupDetail.count + ' <span class="muted">items selected</span>');
                        });
                        $(".totalItems").html(totalItems + ' <span class="muted">items</span>');
                        $('#answer').val(data.totalCBM + " CBM (Approx)");
                    }
                    // $("#btn-calculate").click();
                },
                error: function(xhr, status, error) {
                    console.log(xhr);
                }
            });
        }
        $(document).ready(function() {
            $(".panel-body input").change(function() {
                calculate_items();
            });
            $('#btn_add_rows').on("click", function() {
                var rowCount = $('#miscellaneous-table tr').length;
                for (var i = 1; i < 6; i++) {
                    $('#miscellaneous-table tr:last').after('<tr id="tr_9000' + rowCount + '" style="background-color: white;"><td><input type="text" name="name[]" placeholder="Item # ' + rowCount + '" class="input form-control" style="width: 100%"></td><td><input type="text" name="cbm[]" class="input form-control" style="width: 80px;"></td><td><input type="text" name="quantity[]' + rowCount + '" class="input form-control"  style="width: 80px;"></td><td></td></tr>');
                    rowCount++;
                }
            });
            $('#miscellanceous_data').on('submit', function (e) {
                e.preventDefault();
                var job_id = "{{$job_id}}";
                if (job_id) {
                    $.ajax({
                        url: "{{route('admin.list-jobs.save-inventory-miscellanceous-data', [$job_id])}}",
                        method: 'POST',
                        data: $('#miscellanceous_data').serialize(),
                        dataType: "json",
                        beforeSend: function() {
                            $.blockUI();
                        },
                        complete: function() {
                            $.unblockUI();
                        },
                        success: function(result) {
                            if (result.error == 0) {
                                $('#answer').val(result.totalCBM + " CBM (@lang('modules.listJobs.approx'))");
                                $('#calculated_cbm').val(result.totalCBM);
                                $('#totalItems').text(result.totalItems+ " items");
                                $.showToastr("{{ __('messages.inventoryDataSavedSuccessfull') }}", 'success', '');
                            } else {
                                //Notification....
                                $.toast({
                                    heading: 'Error',
                                    text: result.message,
                                    icon: 'error',
                                    position: 'top-right',
                                    loader: false,
                                    bgColor: '#fb9678',
                                    textColor: 'white'
                                });
                                //..
                            }
                        },
                    });
                }

            });
            $('.btn-calculate').on("click", function() {
                var inputs = $('.calculator_list').find('input[type="text"]');
                var inputsVal = [];
                var delim = '';
                var totalVal;
                //console.log(inputs);
                inputs.each(function() {
                    //console.log($(this).attr('name'));
                    //console.log($(this).val());
                    if ($(this).attr('name') != 'answer' && $(this).attr('name') != 'undefined' && $(this).val().length > 0) {
                        var str = $(this).attr('name');
                        var afterSub = str.substr(0, 3);
                        //if(afterSub !='900'){
                        //inputsVal += ' ' + $(this).attr('name') + '="' + $(this).val() + '"';
                        inputsVal += delim + $(this).attr('name') + '=' + $(this).val();
                        delim = "&"; // Trick to correctly append delimiter
                        totalVal += $(this).val();
                        //}
                    }
                });
                $('#calculator_data').val(inputsVal);
                var job_id = "{{$job->job_id}}";
                if (job_id) {
                    $.easyAjax({
                        url: "{{route('admin.list-jobs.save-inventory-data', [$job->job_id])}}",
                        container: '#generalForm',
                        type: "POST",
                        redirect: true,
                        dataType: "json",
                        data: {
                            "calc_data": inputsVal,
                            "job_id": job_id,
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function(data) {
                            if (data) {
                                $('#answer').val(data.totalCBM + " CBM (@lang('modules.listJobs.approx'))");
                                $('#calculated_cbm').val(data.totalCBM);
                                // $('#special_item_notes').val(data.special_item_notes);
                                $.showToastr("{{ __('messages.inventoryDataSavedSuccessfull') }}", 'success', '');
                            }
                            console.log(data);
                        },
                        error: function(xhr, status, error) {
                            console.log(xhr);
                        }
                    });
                }
            });
            init_inventory_data();
        });
        $('.reset').on('click', function() {
            var id = $(this).data('id');
            $('#quote_item'+id).val(0);
        });
        $('.removeItem').on('click', function () {
            var job_id = $(this).data('job_id');
            var inv_id = $(this).data('inv_id');
            $.ajax({
                url: "{{route('admin.list-jobs.delete-inventory-miscllanceous-data')}}",
                method: 'POST',
                data: {
                    "inv_id": inv_id,
                    "job_id": job_id,
                    "_token": "{{ csrf_token() }}"
                },
                dataType: "json",
                beforeSend: function() {
                    $.blockUI();
                },
                complete: function() {
                    $.unblockUI();
                },
                success: function(result) {
                    if (result.error == 0) {
                            $('#tr_' + inv_id).remove();
                            $('#answer').val(result.totalCBM + " CBM (@lang('modules.listJobs.approx'))");
                            $('#calculated_cbm').val(result.totalCBM);
                            $('#totalItems').text(result.totalItems+ " items");
                            $.toast({
                                heading: 'Success',
                                text: result.message,
                                icon: 'success',
                                position: 'top-right',
                                loader: false,
                                bgColor: '#00c292',
                                textColor: 'white'
                            });
                            //..
                    } else {
                        //Notification....
                        $.toast({
                            heading: 'Error',
                            text: 'Something Went Wrong',
                            icon: 'error',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#fb9678',
                            textColor: 'white'
                        });
                        //..
                    }
                },
            });
        });
    </script>
@endpush