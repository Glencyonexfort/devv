@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ $pageTitle }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ $pageTitle }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>


@endsection

@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/calculator/layout.css') }}" />
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <section>
                <div class="sttabs tabs-style-line">
                    <div class="white-box">
                        <h3 style="color:#fb9678">Job# {{$job->job_number}}</h3>
                        <nav>
                            <ul>
                                <li><a href="{{route('admin.list-jobs.edit-job', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.job_detail')</span></a></li>
                                <li class="tab-current"><a href="#" style="text-align: center;"><span>@lang('modules.listJobs.inventory')</span></a></li>
                                @if(isset($job->job_id))
                                    <li><a href="{{route('admin.list-jobs.operations', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.operations')</span></a></li>
                                    <li><a href="{{route('admin.list-jobs.invoice', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.invoice')</span></a></li>
                                    <li><a href="{{route('admin.list-jobs.email', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.email')</span></a></li>
                                    <li><a href="{{route('admin.list-jobs.attachment', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.attachments')</span></a></li>
                                    <li><a href="{{route('admin.list-jobs.sms', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.sms')</span></a></li>
                                    <li><a href="{{route('admin.list-jobs.insurance', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.insurance')</span></a></li>
                                    <!-- <li><a href="#" style="text-align: center;"><span>@lang('modules.listJobs.sms_notes')</span></a></li> -->
                                @endif
                            </ul>
                        </nav>
                    </div>
                    <div class="content-wrap">
                        <section id="section-line-1" class="show">
                            {!! Form::open(['id'=>'generalForm','class'=>'ajax-form','method'=>'POST']) !!}
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="white-box">
                                        <div class="calculator_list">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="bottom_box"><input type="text" id="answer" name="answer" placeholder="0 CBM (@lang('modules.listJobs.approx'))">
                                                        <span>@lang('modules.listJobs.click_on_calculate')</span></div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="btn_calculate">
                                                        <!-- <button type="reset" id="submit_job_btn" class="btn waves-effect waves-light btn-outline-danger" value="Reset"> <i class="fa fa-check"></i> Reset</button> -->

                                                        <!-- <button type="reset" class="btn waves-effect waves-light btn-outline-danger" value="Reset" style="padding: 0px;font-size: 16px;">Reset</button> -->
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="btn_calculate"><input id="btn-calculate" class="btn-calculate btn btn-success" type="button" value="@lang('modules.listJobs.calculate')" style="font-size: 16px;"></div>
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
                                                            <h4 class="panel-title">
                                                                <a role="button" data-toggle="collapse" data-parent="#accordion" data-target="#collapse<?php echo $maincount; ?>" href="#collapse<?php echo $maincount; ?>" aria-expanded="true" aria-controls="collapse<?php echo $maincount; ?>">
                                                                    <i class="more-less icon-down-open highlight"></i> <?php echo $grp->group_name; ?> <span class="item_count pull-right" id="group_count_<?php echo $maincount; ?>">0
                                                                        <span class="hidden-xs">items selected</span></span>
                                                                </a>
                                                            </h4>
                                                        </div>
                                                        <div id="collapse<?php echo $maincount; ?>" class="panel-collapse collapse ">
                                                            <div class="panel-body">
                                                                <div class="margin-top">
                                                                    <?php
                                                                    foreach ($getInventoryItems as $item) {
                                                                    if ($grp->group_id == $item->group_id) {
                                                                    $count = $item->id;
                                                                    ?>

                                                                    <div class="form-group col-sm-4 pull-left">
                                                                        <label class="control-label col-sm-12" for="quote_iteam<?php echo $count; ?>">
                                                                            <?php echo $item->item_name; ?></label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="quote_item<?php echo $count; ?>" name="<?php echo $count; ?>" class="inventory_qty form-control">
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
                                                <div class="col-md-3 pull-right">
                                                    <div class="btn_calculate"><input id="btn-calculate" class="btn-calculate btn btn-success" type="button" value="@lang('modules.listJobs.calculate')" style="font-size: 16px;"></div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div id="btn_add_rows" class="btn btn-info"><i class="fa fa-plus"></i> @lang('modules.listJobs.add_5_rows')
                                                    <!-- <input id="btn_add_rows" type="button" value="Add 5 Rows" style="padding: 0px;font-size: 16px;"> -->
                                                    </div>

                                                    <h4 class="panel-title" style="font-size: 17px;">
                                                        <a role="button" href="javascript:" style="padding-left: 0px;">
                                                            Miscellaneous Items <span class="item_count pull-right" id="misc_count"> <span class="text-xlight totalItems"><?php echo $countInvItems; ?> <span class="hidden-xs">items</span></span> </span>
                                                        </a>
                                                    </h4>
                                                    <div class="table-responsive">
                                                        <table class="tablee" width="100%" id="miscellaneous-table">
                                                            <thead>
                                                            <tr>
                                                                <th>Item Name</th>
                                                                <th>CBM</th>
                                                                <th>Quantity</th>
                                                                <th></th>
                                                            </tr>
                                                            <tbody>
                                                            <?php
                                                            if ($countInvItems && $countInvItems != '0') {
                                                                $trLoop = $countInvItems + 1;
                                                            } else {
                                                                $trLoop = 6;
                                                            }
                                                            for ($i = 1; $i < $trLoop; $i++) {
                                                            $createInvId = '9000' . $i
                                                            ?>
                                                            <tr style="background-color: white;" id="tr_9000<?php echo $i; ?>">
                                                                <td><input type="text" name="9000<?php echo $i; ?>_name" placeholder="Item # <?php echo $i; ?>" id="9000<?php echo $i; ?>_name" class="input form-control" style="width: 100%"></td>
                                                                <td><input type="text" id="9000<?php echo $i; ?>_cbm" name="9000<?php echo $i; ?>_cbm" class="input form-control" style="width: 80px;"></td>
                                                                <td><input type="text" name="9000<?php echo $i; ?>" class="input form-control" style="width: 80px;"></td>
                                                                <td>
                                                                    @if ($countInvItems && $countInvItems != '0')
                                                                        <a class="btn btn-danger" data-toggle="modal" data-target="#myModal" onclick="deleteMiscItem('<?php echo $job->job_id; ?>','<?php echo $createInvId; ?>')"><i class="fa fa-trash"></i></a>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <?php
                                                            } ?>
                                                            </tbody>
                                                            </thead>

                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pull-right">
                                                    <div class="btn_calculate"><input id="btn-calculate" class="btn-calculate btn btn-success" type="button" value="@lang('modules.listJobs.calculate')" style="font-size: 16px;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <!-- <div class="row">
                            <div class="col-md-12">
                                <div class="white-box">
                                    <div class="table-responsive">
                                        <div class="form-actions">
                                            <button type="submit" id="save-form" class="btn btn-success"><i class="fa fa-check"></i>
                                                @lang('modules.listJobs.save_booking')
                                </button>
                                <a href="{{route('admin.list-jobs.index')}}" class="btn btn-default">@lang('app.cancel')</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                            {!! Form::close() !!}
                        </section>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <!-- .row -->
@endsection

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
                        $('#misc_count').html(data.countInvItems + ' <span class="hidden-xs">items</span>');
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
                    panel.find('.item_count:first').html('<span class="text-xlight">' + total_panel_qty + ' <span class="hidden-xs">items selected</span></span>');
                } else if (total_panel_qty == 1) {
                    panel.find('.item_count:first').html('<i class="icon-check success"></i>' + total_panel_qty + ' <span class="hidden-xs">item selected</span>');
                } else if (total_panel_qty > 1) {
                    panel.find('.item_count:first').html('<i class="icon-check success"></i>' + total_panel_qty + ' <span class="hidden-xs">items selected</span>');
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
                            $("#group_count_" + groupDetail.group_id).html(groupDetail.count + ' <span class="hidden-xs">items selected</span>');
                        });
                        $(".totalItems").html(totalItems + ' <span class="hidden-xs">items</span>');
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
                    $('#miscellaneous-table tr:last').after('<tr id="tr_9000' + rowCount + '" style="background-color: white;"><td><input type="text" name="9000' + rowCount + '_name" placeholder="Item # ' + rowCount + '" class="input form-control" style="width: 100%"></td><td><input type="text" name="9000' + rowCount + '_cbm" class="input form-control" style="width: 80px;"></td><td><input type="text" name="9000' + rowCount + '" class="input form-control"  style="width: 80px;"></td><td></td></tr>');
                    rowCount++;
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
    </script>
@endpush