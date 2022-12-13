<?php
if($company){
    $logo=$company->logo;
    $company_name=$company->company_name;
    $company_phone=$company->phone;
    $company_email=$company->email;
}else{
    $logo='';
    $company_name='';
    $company_phone='';
    $company_email='';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>{{ $company_name }} - Inventory Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <link href="//stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Poppins" />
    <link rel="icon" href="{{ asset('favicon/favicon.png') }}" />
    <style>
        html, body {
          height: 100%;
          margin: 0;
          font-family: "Poppins" , sans-serif !important;
          font-weight: 400;
          font-size: 14px;
        }
        .title-heading{
            font-family: "Poppins" , sans-serif !important;
            font-weight: bold;
            letter-spacing: -1px;
        }
        .title-subheading{
            font-family: "Poppins" , sans-serif !important;
            color: #333;
            letter-spacing: -2px;
        }
        .title-subheading2{
            font-family: "Poppins" , sans-serif !important;
            font-size: 26px;
            text-align: center;
            font-weight: bold;
            padding-top: 2rem;           
        }
        .full-height {
          height: 100%;
        }
        .btn-default{
            color: #6cd4ca;
        background: none!important;
        border:none!important;
        border-radius: 0px!important;
        padding: 3px 11px!important;
        height: 38px!important;
        width: 38px!important;
        font-size: 20px!important;
    }
    .btn-default:hover{
        color: #6cd4ca;
        background: none!important;
        border:none!important;
        border-radius: 0px!important;
        padding: 3px 11px!important;
        height: 38px!important;
        width: 38px!important;
        font-size: 20px!important;
    }
    #inventory_success_div{
        text-align: center; 
        height: 430px;
        vertical-align: middle;
        display: table-cell;
        padding: 0rem 9rem;
        border:1px solid #ebebeb;
        border-radius: 6px;
        padding-top: 6px;
        background: linear-gradient(to right, #397496, #55d9d1), linear-gradient(to right, #397496, #55d9d1);
        background-size: 100% 6px;
        background-position: top 0 left 0,top 6px left 0;
        background-repeat: no-repeat;
        background-color: #fff;
    }
    .inventory_qty{
        display: block;text-align: center;padding: 18px 2px;border: 1px solid #ebebeb;width: 98%!important;
    }
        table{
            width: 100%;
            padding-bottom: 3px;
            background: linear-gradient(to right, #397496, #55d9d1), linear-gradient(to right, #397496, #55d9d1);
            background-size: 100% 3px;
            background-position: bottom 0 left 0,bottom 3px left 0;
            background-repeat: no-repeat;
            background-color: #397496;
            border-top-left-radius:6px;
            border-top-right-radius:6px;
        }
        .miss-table-head{
            background: linear-gradient(to right, #55d9d1, #397496), linear-gradient(to right, #55d9d1, #397496);
        }
        .miss-input-field{
            width: 100%;
            border: 1px solid #e9ecf3;
            font-weight: normal;
            padding: 1.3rem 20px;
            border-radius: 3px;
            background: #fafbfe;
            -webkit-box-shadow: none;
            box-shadow: none;
            -webkit-transition: none;
            -o-transition: none;
            -webkit-transition: none;
            transition: none;
            transition: none;
     transition:none;
        }
        #miscellaneous-table thead>tr>td{
            padding: 16px;
        }
        .addess_label{
            color: #55d9d1;
        }
        .top-table-padding{
            padding: 2rem 3rem;
        }
        .top-row-padding{
            padding: 0 3rem;
        }
        td, th {
            padding: 10px;
            color:#fff;
            border: none;
        }  
        .page-top{
            color:#111;
            background-color:#fff; 
            padding: 10px;
            border: none!important;
        }   
        .page-top-right{
            float: right;
            margin-top: 16px;
            font-size: 22px;
            font-weight: bold;
        }
        .content-wrap{
            padding: 2px 22px;
        }
        .submit-btn{
            background-color: #6cd4ca;
            color: #fff;
            font-size: 14px!important;
            font-weight: normal;
            padding: 8px 38px;
            border-radius:2rem; 
        }
        .submit-btn:hover{
            color: #fff;
            background-color: #397596;
            opacity: 0.9;
        }
        footer{
            background-color: #397496;
            padding-top: 18px;
            padding-bottom: 22px;
            color: white;
        }
        .inv-title{            
            border: none!important; 
            padding: 20px 15px!important;           
        }  
        .inv-div{
            background-color: #fff!important;
            padding: 34px;
            border: 1px solid #ebebeb;
            border-bottom-left-radius: 6px;
            border-bottom-right-radius: 6px;
            margin-bottom: 4rem;
        }
        .noborder{
            border: none!important;
            -webkit-box-shadow: none!important;
            box-shadow: none!important;
        }
        .font14{
            font-size: 14px!important;
        }
        .ml2rem{
            margin-left: 2rem;
        }
        .line-icon{
            width: 14px;margin-right: 10px;
        }
        #btn_add_rows{
            display: flex;
            color: #b7b7b7;
            text-decoration: none;
            cursor: pointer;
        }
        #btn_add_rows:hover{
            color: #555;
        }
        .bootstrap-touchspin {
            width: 150px;
        }
        @media only screen and (max-width: 768px) {
            .container {
                width: 100%;
            }
            .panel-heading {
                text-align: center;
            }
            .bootstrap-touchspin {
                width: 100%;
            }
            .form-group {
                text-align: center;
            }
        }
        </style>
</head>

<body style="background-color:#fafbfe">
    <nav class="navbar navbar-default navbar-top page-top" style="margin-bottom: 30px;">
        <div class="container">
        <div>
            <img class="top-logo" style="height: 62px;" src="{{ request()->getSchemeAndHttpHost().'/user-uploads/company-logo/'.$logo }}"/>
            <span class="page-top-right"><img src="{{ request()->getSchemeAndHttpHost().'/img/icons/multi-inv.png' }}" style="width: 26px;margin-right: 10px;"/> {{ $company_phone }}</span>
        </div>
        </div>
    </nav>
<div class="container">
    <div class="row">
        <div class="col-sm-12 col-sm-offset-0   col-md-10 col-md-offset-1   col-lg-8 col-lg-offset-2" style="margin-bottom: 2rem">
            <div id="inventory_success_div" style="display: none">
                <img src="{{ request()->getSchemeAndHttpHost().'/img/icons/multi-inv.png' }}" style="width: 60px;"/>
                <h1 style="font-size: 26px;font-weight: 900;">
                    Thank you for <br/>submitting your inventory
                </h1>
                <h3 style="font-size: 14px;line-height: 20px;">
                    
                    <br/><br/>
                If you have any questions or would like to discuss your move, <br/>
                please give us a call on <strong>{{ $company_phone }}</strong>
                or email <a href = "mailto: {{ $company_email }}">{{ $company_email }}</a>
                </h3>
            </div>
            <div id="inventory_form_div">
            <div class="row">
            <table>
                <tbody>
                    <tr>
                    <td width="30%" class="top-table-padding"><strong>JOB #  {{ $job->job_number }}</strong></td>
                    <td class="text-right"></td>
                </tr>
                <tr>
                    <td class="top-row-padding">{{ $lead->name }}</td>
                    <td class="top-row-padding">Job Date {{ date('d/m/Y', strtotime($job->job_date)) }}</td>
                </tr>
                <tr>
                    <td width="50%" class="top-table-padding" style="padding-top:10px">From: <span class="addess_label">{{ $job->pickup_suburb.', '.$job->pickup_state }}</span> To: <span class="addess_label">{{ $job->delivery_suburb.', '.$job->drop_off_state }}</span></td>
                    <td class="top-row-padding"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row inv-div">
        <p class="title-subheading2">
            Inventory Details
        </p>
        <p style="text-align:center;margin-bottom: 2rem;font-size: 12px">
            Select the items and quantities you need moved. You can update this later if your requirements change.
        </p>
        <div class="row">
            <div class="content-wrap">
                <section id="section-line-1" class="show">
                    
                    <input type="hidden" id="answer" name="answer" class="form-control" placeholder="">
                    <div class="row">
                        <div class="col-md-12 content-wrap">
                            <div class="white-box">
                                <div class="calculator_list">
                                    {!! Form::open(['id'=>'generalForm','class'=>'ajax-form','method'=>'POST']) !!}                                    
                                        <div class="row" style="border-bottom: 1px dotted #ebebeb;">
                                            <div class="panel-group inventory" id="accordion" style="width: 100%">
                                                <?php
                                                $maincount = 0;
                                                $count = 0;
                                                $now = "";
                                                foreach ($inventory_groups as $grp) {
                                                $maincount = $grp->group_id; 
                                                console.log($maincount );
                                                $bgcolor = ($maincount % 2 == 0)?'#fff':'#fbfbfb';
                                                ?>
                                                <div class="panel panel-default noborder">
                                                    <div class="panel-heading inventory-panel-heading inv-title" style="background:{{ $bgcolor }}">
                                                        <h4 class="panel-title font14" style="text-transform: capitalize">
                                                            <a role="button" data-toggle="collapse" data-parent="#accordion" data-target="#collapse<?php echo $maincount; ?>" href="#collapse<?php echo $maincount; ?>" aria-expanded="true" aria-controls="collapse<?php echo $maincount; ?>">
                                                                <i class="more-less icon-down-open highlight"></i> <?php echo $grp->group_name; ?> 
                                                                <span class="item_count pull-right" id="group_count_<?php echo $maincount; ?>" style="font-weight: normal">
                                                                    <img src="{{ request()->getSchemeAndHttpHost().'/img/icons/single-inv.png' }}" class="line-icon"/> 0 <span class="muted">item selected <i class="fa fa-angle-down ml2rem"></i></span>
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
                                                                    <label class="col-lg-5 col-form-label" for="quote_iteam<?php echo $count; ?>" style="text-align: right;font-weight: normal;padding-top: 9px;">
                                                                        <?php echo $item->item_name; ?></label>
                                                                        <div class="col-sm-7">
                                                                    <div class="input-group bootstrap-touchspin">
                                                                        <input type="text" id="quote_item<?php echo $count; ?>" name="<?php echo $count; ?>" class="inventory_qty form-control touchspin-step">
                                                                        <a href="javascript:void(0)" style="color: black;margin-top: 10px;position: absolute;"><i data-id="<?php echo $count; ?>" class="reset fa fa-trash ml-3 mt-2"></i></a>
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
                                            {{-- <div class="col-sm-12" style="text-align: right">
                                                <div class="btn_calculate"><input id="btn-calculate" class="btn submit-btn" type="button" value="Submit"></div>
                                            </div> --}}
                                        </div>
                                    {!! Form::close() !!}
                                </div>
                                <form id="miscellanceous_data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12" style="padding: 0!important;">
                                            <br/>
                                                <p class="job-label-txt job-status green-status" style="color: #111">
                                                    Miscellaneous Items
                                                    <span class="item_count pull-right">
                                                         <span class="text-xlight" id="totalItems">{{ count($miscllanceous_items) }} items</span> 
                                                    </span>
                                                </p>
                                            <div class="table-responsive" style="border:1px solid #ebebeb;border-radius:6px;overflow: hidden;">
                                                <table class="tablee" width="100%" id="miscellaneous-table">
                                                    <thead class="miss-table-head">
                                                    <tr>
                                                        <td>Item Name</td>
                                                        <td>CBM</td>
                                                        <td>Quantity</td>
                                                    </tr>
                                                    <tbody>
                                                    @if (count($miscllanceous_items))
                                                        @foreach ($miscllanceous_items as $item)
                                                            <tr style="background-color: white;" id="tr_{{ $item->id }}">
                                                                <td style="padding: 10px 6px 10px 26px;width:60%"><input type="text" name="name[]" placeholder="Item #" value="{{ $item->misc_item_name }}" class="input form-control miss-input-field" style="width: 100%"></td>
                                                                <td style="padding: 10px 6px;width:20%"><input type="text" name="cbm[]" placeholder="cbm" value="{{ $item->misc_item_cbm }}" class="input form-control miss-input-field" style="width: 80px;"></td>
                                                                <td style="padding: 10px 26px 10px 6px;width:20%"><input type="text" name="quantity[]" placeholder="quantity" value="{{ $item->quantity }}" class="input form-control miss-input-field" style="width: 80px;"></td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        @for ($i = 0; $i < 2; $i++)
                                                            <tr style="background-color: white;">
                                                                <td style="padding: 10px 6px 10px 26px;width:60%"><input type="text" name="name[]" placeholder="Item # <?php echo $i; ?>" class="input form-control miss-input-field"></td>
                                                                <td style="padding: 10px 6px;width:20%"><input type="text" name="cbm[]" placeholder="cbm" class="input form-control miss-input-field"></td>
                                                                <td style="padding: 10px 26px 10px 6px;width:20%"><input type="text" name="quantity[]" placeholder="quantity" class="input form-control miss-input-field"></td>
                                                                <td></td>
                                                            </tr>
                                                        @endfor
                                                    @endif
                                                    </tbody>
                                                    </thead>
            
                                                </table>
                                                <div class="row" style="padding: 8px 30px;">
                                                    <div class="col-sm-6" style="text-align: left">
                                                        <a id="btn_add_rows"><i class="fa fa-plus-circle" style="font-size: 20px;margin-right: 15px;"></i> Add rows</a>
                                                    </div>
                                                    <div class="col-sm-6" style="text-align: right">
                                                        <div><input id="btn-calculate" class="btn submit-btn" type="button" value="Submit"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                </form>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>
</div>
<footer>
    <div class="container">
        {{-- <span style="float: left"><a style="color: white;" href="javascript:">Terms &amp; Conditions</a></span> --}}
        <span style="float: right;">© {{ date('Y') }} {{ $company_name }}</span>
    </div>
</footer>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.js"></script>
  <script src="{{ request()->getSchemeAndHttpHost()}}/bootstrap/jquery.bootstrap-touchspin.js"></script>
    <script type="text/javascript">
    $('.reset').on('click', function() {
            var id = $(this).data('id');
            $('#quote_item'+id).val(0);
        });
        function deleteMiscItem(job_id, inv_id) {
            $.ajax({
                url: "/delete-inventory-data-external",
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
                        $('#answer').val(data.totalCBM + " CBM (Approx)");
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
                    panel.find('.item_count:first').html('<span class="text-xlight"><img src="{{ request()->getSchemeAndHttpHost().'/img/icons/single-inv.png' }}" class="line-icon"/> ' + total_panel_qty + ' <span class="muted">items selected</span> <i class="fa fa-angle-down ml2rem"></i></span>');
                } else if (total_panel_qty == 1) {
                    panel.find('.item_count:first').html('<i class="icon-checkmark4 success"></i> <img src="{{ request()->getSchemeAndHttpHost().'/img/icons/single-inv.png' }}" class="line-icon"/> ' + total_panel_qty + ' <span class="muted">item selected <i class="fa fa-angle-down ml2rem"></i></span>');
                } else if (total_panel_qty > 1) {
                    panel.find('.item_count:first').html('<i class="icon-checkmark4 success"></i> <img src="{{ request()->getSchemeAndHttpHost().'/img/icons/single-inv.png' }}" class="line-icon"/> ' + total_panel_qty + ' <span class="muted">items selected <i class="fa fa-angle-down ml2rem"></i></span>');
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
            $.ajax({
                url: "/get-inventory-data-external/{{ $job->job_id }}",
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
                            $("#group_count_" + groupDetail.group_id).html('<img src="{{ request()->getSchemeAndHttpHost()."/img/icons/single-inv.png" }}" class="line-icon"/>'+ groupDetail.count + ' <span class="muted">items selected <i class="fa fa-angle-down ml2rem"></i></span>');
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
                for (var i = 1; i < 3; i++) {
                    $('#miscellaneous-table tr:last').after('<tr style="background-color: white;"><td style="padding: 10px 6px 10px 26px;width:60%"><input type="text" name="name[]" placeholder="Item # ' + rowCount + '" class="input form-control miss-input-field"></td><td style="padding: 10px 6px;width:20%"><input type="text" name="cbm[]" placeholder="cbm" class="input form-control miss-input-field"></td><td style="padding: 10px 26px 10px 6px;width:20%"><input type="text" name="quantity[]" placeholder="quantity" class="input form-control miss-input-field"></td></tr>');
                    rowCount++;
                }
            });
            $('#btn-calculate').on("click", function(e) {
                e.preventDefault();
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
                var tenant_id = "{{$tenant_id}}";
                var lead_id = "{{$lead->id}}";
                if (job_id) {
                    $.ajax({
                        url: "/save-inventory-data-external/{{$job->job_id}}",
                        // container: '#generalForm',
                        method: "POST",
                        dataType: "json",
                        data: {
                            "calc_data": inputsVal,
                            "job_id": job_id,
                            "tenant_id": tenant_id,
                            "lead_id": lead_id,
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function(data) {
                            $.ajax({
                                url: "/save-inventory-miscellanceous-data-external/{{$job->job_id}}",
                                method: 'POST',
                                data: $('#miscellanceous_data').serialize() + '&job_id='+job_id + '&tenant_id='+tenant_id + "&_token={{ csrf_token() }}",
                                dataType: "json",
                                success: function(result) {
                                    $("#inventory_form_div").toggle();
                                    $("#inventory_success_div").toggle();
                                },
                            });
                        },
                        error: function(xhr, status, error) {
                            console.log(xhr);
                        }
                    });
                }
            });
            init_inventory_data();

            $('.pt-cv-view .panel-title').off('click');

            $('.panel-collapse').on('shown.bs.collapse', function (e) {
            var $panel = $(this).closest('.panel');
            $('html,body').animate({
                scrollTop: $panel.offset().top
                }, 500); 
            });
            // $('#miscellanceous_data').on('submit', function (e) {
            //     e.preventDefault();
            //     var job_id = "{{$job->job_id}}";
            //     // alert(job_id);
            //     if (job_id) {
            //         $.ajax({
            //             url: "{{route('admin.list-jobs.save-inventory-miscellanceous-data', [$job->job_id])}}",
            //             method: 'POST',
            //             data: $('#miscellanceous_data').serialize(),
            //             dataType: "json",
            //             beforeSend: function() {
            //                 $.blockUI();
            //             },
            //             complete: function() {
            //                 $.unblockUI();
            //             },
            //             success: function(result) {
            //                 if (result.error == 0) {
            //                     $('#answer').val(result.totalCBM + " CBM (@lang('modules.listJobs.approx'))");
            //                     $('#calculated_cbm').val(result.totalCBM);
            //                     $('#totalItems').text(result.totalItems+ " items");
            //                     // $.showToastr("{{ __('messages.inventoryDataSavedSuccessfull') }}", 'success', '');
            //                 } else {
            //                     //Notification....
            //                     $.toast({
            //                         heading: 'Error',
            //                         text: result.message,
            //                         icon: 'error',
            //                         position: 'top-right',
            //                         loader: false,
            //                         bgColor: '#fb9678',
            //                         textColor: 'white'
            //                     });
            //                     //..
            //                 }
            //             },
            //         });
            //     }

            // });
        });
    </script>
</body>
</html>