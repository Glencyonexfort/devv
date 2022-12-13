@extends('layouts.app')

@section('page-title')
<div class="page-header page-header-light view_blade_page_header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex view_blade_page_padding">
            <h4>
            <i class="icon-calendar"></i>
                <span class="view_blade_page_span_header">{{ $pageTitle }} </span>
        </div>
    </div>
</div>
@endsection

@push('head-script')
    <meta name="csrf-token" content="{{ csrf_token() }}">
   
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/timepicker/bootstrap-timepicker.min.css') }}">

    <link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/multiselect/css/multi-select.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap-colorselector/bootstrap-colorselector.min.css') }}">

    <style>
        .table>thead>tr>th {
            border-bottom: #ffffff;
        }
        th{
            background: white;
        }
        td{
            padding: 5px 8px!important;
            border:1px solid #ccc;
        }
        .td-background{
            background: rgb(255 0 0 / 16%)
        }
    </style>
    
@endpush

@section('content')
    <div class="row">
        <div class="col-md-4 m-2">
            <div class="float-left">
                <button type="button" class="btn border border-1 left-arrow">
                    <i class="fa fa-angle-left"></i>
                </button>
                <button type="button" class="btn border border-1 right-arrow">
                    <i class="fa fa-angle-right"></i>
                </button>
            </div>
            <button type="button" style="margin-left: 20px;" class="btn border border-1 today">today</button>
        </div>
        <div class="col-md-6 m-2">
            <input id="datepicker" style="border-style: none; width: 160px;" type="text" value="{{ $display_date }}">
            <button onclick="show_dp();" class="btn border border-1">
                <i class="fa fa-calendar"></i>
            </button>
        </div>
    </div>
    <input type="hidden" id="date" value="{{ $today }}">
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
                <div class="table-responsive">
                    <table class="table table-xl" id="dailyDiary" style="width: 100%;width:100%;margin-bottom:1em;border-collapse: collapse;">
                        <colgroup>
                            <col span="1" style="width: 4%;">
                            <col span="1" style="width: 4%;">
                            <col span="1" style="width: 5%;">
                            <col span="1" style="width: 10%;">
                            <col span="1" style="width: 10%;">
                            <col span="1" style="width: 5%;">
                            <col id="starttime-col" span="1" style="width: 6%;">
                            <col id="finishtime-col" span="1" style="width: 6%;">
                            <col id="comment-col" span="1" style="width: 9%;">
                            <col id="vehicle-col" span="1" style="width: 5%;">
                            <col id="driver-col" span="1" style="width: 6%;">
                            <col span="1" style="width: 10%;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Job #</th>
                                <th>Leg #</th>
                                <th>Name</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Action</th>
                                <th>Est Start</th>
                                <th>Est Finish</th>
                                <th>Comment</th>
                                <th>Vehicle</th>
                                <th>Driver</th>
                                <th>Offsiders</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($new_data)
                                @foreach ($new_data as $leg)
                                    <tr>
                                        <td class="td-background">{{ $leg['job_id'] }}</td>
                                        <td class="td-background">{{ $leg['leg_number'] }}</td>
                                        <td class="td-background">{{ $leg['name'] }}</td>
                                        <td class="td-background">{{ Illuminate\Support\Str::limit($leg['from'], 20) }}</td>
                                        <td class="td-background">{{ Illuminate\Support\Str::limit($leg['to'], 20) }}</td>
                                        <td data-type="action" data-leg_id="{{ $leg['leg_id'] }}">{{ $leg['action'] }}</td>
                                        <td data-type="estStart" data-leg_id="{{ $leg['leg_id'] }}">{{ $leg['start'] }}</td>
                                        <td data-type="estFinish" data-leg_id="{{ $leg['leg_id'] }}">{{ $leg['finish'] }}</td>
                                        <td data-type="comment" data-leg_id="{{ $leg['leg_id'] }}">{{ $leg['comment'] }}</td>
                                        <td data-type="vehicle" data-leg_id="{{ $leg['leg_id'] }}">{{ $leg['vehicle'] }}</td>
                                        <td data-type="driver" data-leg_id="{{ $leg['leg_id'] }}">
                                            @foreach($drivers as $d)
                                                @if($d->id==$leg['driver'])
                                                    {{ $d->name }}
                                                    @break
                                                @endif
                                            @endforeach
                                        </td>
                                        <td data-type="offsiders" data-leg_id="{{ $leg['leg_id'] }}">
                                            @if($leg['offsiders'])
                                                <?php
                                                $offsiders = explode(',',$leg['offsiders']);
                                                ?>                            
                                                @foreach($offsiders as $sider)
                                                    @foreach($people as $p)
                                                        @if($p->id==$sider)
                                                            {{ $p->name }},
                                                            @break
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                            @endif
                                        </td>
                                    </tr>  
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-script')
    <script type="text/javascript" src="{{asset('assets/system_design/typeahead.js')}}"></script>
    <link rel="stylesheet" href="{{asset('assets/system_design/jquery-ui.css')}}">
    <script>
        $(function() {
            $( "#datepicker" ).datepicker();
        });

        function show_dp() {
            $( "#datepicker" ).datepicker('show'); //Show on click of button
        }

        $( "#datepicker" ).change(function() {

            var date = $('#datepicker').val();
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url: '{{ route('admin.jobs.get-daily-diary-data') }}',
                method: 'GET',
                data: {
                    '_token': _token,
                    'date': date,
                },
                dataType: "json",
                beforeSend: function () {
                    $.blockUI();
                },
                complete: function () {
                    $.unblockUI();
                },
                success: function (result) {
                    $('#datepicker').val(result.data.display_date);
                    $('#date').val(result.data.date);
                    if(result.data.new_data != null)
                    {
                        $("#dailyDiary > tbody > tr").remove();
                                
                        $.each(result.data.new_data, function(index, value){
                            var trHTML = '';
                            trHTML +=    '<tr>'+
                                    '<td style="background:rgb(238, 224, 224);">' + value.job_id + '</td>'+
                                    '<td style="background:rgb(238, 224, 224);">' + value.leg_number + '</td>'+
                                    '<td style="background:rgb(238, 224, 224);">' + value.name + '</td>'+
                                    '<td style="background:rgb(238, 224, 224);">' + value.from + '</td>'+
                                    '<td style="background:rgb(238, 224, 224);">' + value.to +'</td>'+
                                    '<td data-type="action" data-leg_id="'+ value.leg_id +'">' + value.action + '</td>'+
                                    '<td data-type="estStart" data-leg_id="'+ value.leg_id +'">' + value.start + '</td>'+
                                    '<td data-type="estFinish" data-leg_id="'+ value.leg_id +'">' + value.finish + '</td>'+
                                    '<td data-type="comment" data-leg_id="'+ value.leg_id +'">' + value.comment + '</td>'+
                                    '<td data-type="vehicle" data-leg_id="'+ value.leg_id +'">' + value.vehicle + '</td>'+
                                    '<td data-type="driver" data-leg_id="'+ value.leg_id +'">' + value.driver + '</td>'+
                                    '<td data-type="offsiders" data-leg_id="'+ value.leg_id +'">' + value.offsiders + '</td>'+
                                '</tr>';
                    
                            $('#dailyDiary').append(trHTML);
                        });
                    } else {
                        $("#dailyDiary > tbody > tr").remove();
                    }
                }
            });
        });

        $("body").off('click', '.today').on('click', '.today', function() {
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url: '{{ route('admin.jobs.get-daily-diary-today') }}',
                method: 'GET',
                data: {
                    '_token': _token,
                },
                dataType: "json",
                beforeSend: function () {
                    $.blockUI();
                },
                complete: function () {
                    $.unblockUI();
                },
                success: function (result) {
                    $('#datepicker').val(result.data.display_date);
                    $('#date').val(result.data.date);
                    if(result.data.new_data != null)
                    {
                        $("#dailyDiary > tbody > tr").remove();
                                
                        $.each(result.data.new_data, function(index, value){
                            var trHTML = '';
                            trHTML +=    '<tr>'+
                                    '<td style="background:rgb(238, 224, 224);">' + value.job_id + '</td>'+
                                    '<td style="background:rgb(238, 224, 224);">' + value.leg_number + '</td>'+
                                    '<td style="background:rgb(238, 224, 224);">' + value.name + '</td>'+
                                    '<td style="background:rgb(238, 224, 224);">' + value.from + '</td>'+
                                    '<td style="background:rgb(238, 224, 224);">' + value.to +'</td>'+
                                    '<td data-type="action" data-leg_id="'+ value.leg_id +'">' + value.action + '</td>'+
                                    '<td data-type="estStart" data-leg_id="'+ value.leg_id +'">' + value.start + '</td>'+
                                    '<td data-type="estFinish" data-leg_id="'+ value.leg_id +'">' + value.finish + '</td>'+
                                    '<td data-type="comment" data-leg_id="'+ value.leg_id +'">' + value.comment + '</td>'+
                                    '<td data-type="vehicle" data-leg_id="'+ value.leg_id +'">' + value.vehicle + '</td>'+
                                    '<td data-type="driver" data-leg_id="'+ value.leg_id +'">' + value.driver + '</td>'+
                                    '<td data-type="offsiders" data-leg_id="'+ value.leg_id +'">' + value.offsiders + '</td>'+
                                '</tr>';
                    
                            $('#dailyDiary').append(trHTML);
                        });
                    } else {
                        $("#dailyDiary > tbody > tr").remove();
                    }
                }
            });
        });

        $("body").off('click', '.right-arrow').on('click', '.right-arrow', function() {
            var _token = $('input[name="_token"]').val();
            var date = $('#date').val();
            $.ajax({
                url: '{{ route('admin.jobs.get-daily-diary-right-arrow') }}',
                method: 'GET',
                data: {
                    '_token': _token,
                    'date': date
                },
                dataType: "json",
                beforeSend: function () {
                    $.blockUI();
                },
                complete: function () {
                    $.unblockUI();
                },
                success: function (result) {
                    $('#datepicker').val(result.data.display_date);
                    $('#date').val(result.data.date);
                    if(result.data.new_data != null)
                    {
                        $("#dailyDiary > tbody > tr").remove();
                                
                        $.each(result.data.new_data, function(index, value){
                            var trHTML = '';
                            trHTML +=    '<tr>'+
                                    '<td style="background:rgb(238, 224, 224);">' + value.job_id + '</td>'+
                                    '<td style="background:rgb(238, 224, 224);">' + value.leg_number + '</td>'+
                                    '<td style="background:rgb(238, 224, 224);">' + value.name + '</td>'+
                                    '<td style="background:rgb(238, 224, 224);">' + value.from + '</td>'+
                                    '<td style="background:rgb(238, 224, 224);">' + value.to +'</td>'+
                                    '<td data-type="action" data-leg_id="'+ value.leg_id +'">' + value.action + '</td>'+
                                    '<td data-type="estStart" data-leg_id="'+ value.leg_id +'">' + value.start + '</td>'+
                                    '<td data-type="estFinish" data-leg_id="'+ value.leg_id +'">' + value.finish + '</td>'+
                                    '<td data-type="comment" data-leg_id="'+ value.leg_id +'">' + value.comment + '</td>'+
                                    '<td data-type="vehicle" data-leg_id="'+ value.leg_id +'">' + value.vehicle + '</td>'+
                                    '<td data-type="driver" data-leg_id="'+ value.leg_id +'">' + value.driver + '</td>'+
                                    '<td data-type="offsiders" data-leg_id="'+ value.leg_id +'">' + value.offsiders + '</td>'+
                                '</tr>';
                    
                            $('#dailyDiary').append(trHTML);
                        });
                    } else {
                        $("#dailyDiary > tbody > tr").remove();
                    }
                }
            });
        });

        $("body").off('click', '.left-arrow').on('click', '.left-arrow', function () {
            
            var _token = $('input[name="_token"]').val();
            var date = $('#date').val();
            $.ajax({
                url: '{{ route('admin.jobs.get-daily-diary-left-arrow') }}',
                method: 'GET',
                data: {
                    '_token': _token,
                    'date': date
                },
                dataType: "json",
                beforeSend: function () {
                    $.blockUI();
                },
                complete: function () {
                    $.unblockUI();
                },
                success: function (result) {
                    $('#datepicker').val(result.data.display_date);
                    $('#date').val(result.data.date);
                    if(result.data.new_data != null)
                    {
                        $("#dailyDiary > tbody > tr").remove();
                                
                        $.each(result.data.new_data, function(index, value){
                            var trHTML = '';
                            trHTML +=    '<tr>'+
                                    '<td style="background:rgb(238, 224, 224);">' + value.job_id + '</td>'+
                                    '<td style="background:rgb(238, 224, 224);">' + value.leg_number + '</td>'+
                                    '<td style="background:rgb(238, 224, 224);">' + value.name + '</td>'+
                                    '<td style="background:rgb(238, 224, 224);">' + value.from + '</td>'+
                                    '<td style="background:rgb(238, 224, 224);">' + value.to +'</td>'+
                                    '<td data-type="action" data-leg_id="'+ value.leg_id +'">' + value.action + '</td>'+
                                    '<td data-type="estStart" data-leg_id="'+ value.leg_id +'">' + value.start + '</td>'+
                                    '<td data-type="estFinish" data-leg_id="'+ value.leg_id +'">' + value.finish + '</td>'+
                                    '<td data-type="comment" data-leg_id="'+ value.leg_id +'">' + value.comment + '</td>'+
                                    '<td data-type="vehicle" data-leg_id="'+ value.leg_id +'">' + value.vehicle + '</td>'+
                                    '<td data-type="driver" data-leg_id="'+ value.leg_id +'">' + value.driver + '</td>'+
                                    '<td data-type="offsiders" data-leg_id="'+ value.leg_id +'">' + value.offsiders + '</td>'+
                                '</tr>';
                    
                            $('#dailyDiary').append(trHTML);
                        });
                    } else {
                        $("#dailyDiary > tbody > tr").remove();
                    }
                }
            });
        });
            
        $(document).on("dblclick","#dailyDiary tr td",function(e) {
            e.stopPropagation();      //<-------stop the bubbling of the event here
            e.preventDefault();
            var currentEle = $(this);
            var value = $(this).html();
            var newValue = $.trim(value);
            var type = $(this).data('type');
            var leg_id = $(this).data('leg_id');
            if(type === 'action')
            {
                openActionTab(currentEle, newValue);
            }
            if(type === 'estStart')
            {
                openEstStartTab(currentEle, newValue, leg_id);
            }
            if(type === 'estFinish')
            {
                openEstFinishTab(currentEle, newValue, leg_id);
            }
            if(type === 'comment')
            {
                openCommentTab(currentEle, newValue, leg_id);
            }
            if(type === 'vehicle')
            {
                openVehicleTab(currentEle, newValue, leg_id);
            }
            if(type === 'driver')
            {
                openDriverTab(currentEle, newValue, leg_id);
            }
            if(type === 'offsiders')
            {
                openOffsidersTab(currentEle, newValue, leg_id);
            }
        });
                

        function openActionTab(currentEle, value) 
        {
            $(currentEle).html( '<select style="width: 70px;" class="form-control thVal">'+
                                    '<option value="' + value + '">'+ value +'</option>'+
                                    '<option value="' + value + '1">'+ value +'1</option>'+
                                    '<option value="' + value + '2">'+ value +'2</option>'+
                                '</select>' );
            $(".thVal").focus();
            $(".thVal").on('change', function (event) {
                $(currentEle).html($(".thVal").val());

                var newVal = $(currentEle).html();
                //alert(newVal);
            });
        }

        function openEstStartTab(currentEle, value, leg_id) 
        {
            $("#starttime-col").css("width", "9%");
            $(currentEle).html('<input type="time" style="width: 100px;" class="form-control pickatime estStart" value="' + value + '">');
            $(".estStart").focus();
    
            $(".estStart").on('change', function (event) {
                $(currentEle).html($(".estStart").val());
                var newEstStart = $(currentEle).html();
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: '{{ route('admin.jobs.update-daily-diary') }}',
                    method: 'POST',
                    data: {
                        '_token': _token,
                        'newEstStart': newEstStart,
                        'leg_id': leg_id,
                        'tab': 'estStart'
                    },
                    dataType: "json",
                    beforeSend: function () {
                        $('.preloader').show();
                    },
                    complete: function () {
                        $('.preloader').hide();
                    },
                    success: function (result) {
                        if(result.success == 1)
                        {
                            $("#starttime-col").css("width", "6%");
                            $.toast({
                                heading: 'Success',
                                text: result.message,
                                icon: 'success',
                                position: 'top-right',
                                loader: false,
                                bgColor: '#00c292',
                                textColor: 'white'
                            });
                        }
                    }
                });
            });
        }

        function openEstFinishTab(currentEle, value, leg_id)
        {
            $("#finishtime-col").css("width", "9%");
            $(currentEle).html('<input type="time" style="width: 100px;" class="form-control pickatime estFinish" value="' + value + '" >');
            $(".estFinish").focus();
            $(".estFinish").on('change', function (event) {
                $(currentEle).html($(".estFinish").val());
                var newEstFinish = $(currentEle).html();
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: '{{ route('admin.jobs.update-daily-diary') }}',
                    method: 'POST',
                    data: {
                        '_token': _token,
                        'newEstFinish': newEstFinish,
                        'leg_id': leg_id,
                        'tab': 'estFinish'
                    },
                    dataType: "json",
                    beforeSend: function () {
                        $('.preloader').show();
                    },
                    complete: function () {
                        $('.preloader').hide();
                    },
                    success: function (result) {
                        if(result.success == 1)
                        {
                            $("#finishtime-col").css("width", "6%");
                            $.toast({
                                heading: 'Success',
                                text: result.message,
                                icon: 'success',
                                position: 'top-right',
                                loader: false,
                                bgColor: '#00c292',
                                textColor: 'white'
                            });
                        }
                    }
                });
            });

        }
        function openCommentTab(currentEle, value, leg_id) 
        {
            $(currentEle).html('<textarea class="form-control comment" style="padding: 2px 6px;">'+value+'</textarea>');
            $("#comment-col").css("width", "15%");
            $(".comment").focus();
            $(".comment").keyup(function (event) {
                if (event.keyCode == 13) { 
                    $(currentEle).html($(".comment").val());
                    var newComment = $(currentEle).html();
                    var _token = $('input[name="_token"]').val();
                    $.ajax({
                        url: '{{ route('admin.jobs.update-daily-diary') }}',
                        method: 'POST',
                        data: {
                            '_token': _token,
                            'newComment': newComment,
                            'leg_id': leg_id,
                            'tab': 'comment'
                        },
                        dataType: "json",
                        beforeSend: function () {
                        $('.preloader').show();
                    },
                    complete: function () {
                        $('.preloader').hide();
                    },
                        success: function (result) {
                            if(result.success == 1)
                            {
                                $("#comment-col").css("width", "9%");
                                $.toast({
                                    heading: 'Success',
                                    text: result.message,
                                    icon: 'success',
                                    position: 'top-right',
                                    loader: false,
                                    bgColor: '#00c292',
                                    textColor: 'white'
                                });
                            }
                        }
                    });
                }
            });
            $(".comment").on('change', function (event) {
                // if (event.keyCode == 13) { 
                    $(currentEle).html($(".comment").val());
                    var newComment = $(currentEle).html();
                    var _token = $('input[name="_token"]').val();
                    $.ajax({
                        url: '{{ route('admin.jobs.update-daily-diary') }}',
                        method: 'POST',
                        data: {
                            '_token': _token,
                            'newComment': newComment,
                            'leg_id': leg_id,
                            'tab': 'comment'
                        },
                        dataType: "json",
                        beforeSend: function () {
                        $('.preloader').show();
                    },
                    complete: function () {
                        $('.preloader').hide();
                    },
                        success: function (result) {
                            if(result.success == 1)
                            {
                                $("#comment-col").css("width", "9%");
                                $.toast({
                                    heading: 'Success',
                                    text: result.message,
                                    icon: 'success',
                                    position: 'top-right',
                                    loader: false,
                                    bgColor: '#00c292',
                                    textColor: 'white'
                                });
                            }
                        }
                    });
                // }
            });
        }
        function openVehicleTab(currentEle, value, leg_id) 
        {
            $("#vehicle-col").css("width", "12%");
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url: '{{ route('admin.jobs.get-daily-diary-vehicles') }}',
                method: 'GET',
                data: {
                    '_token': _token,
                },
                dataType: "json",
                beforeSend: function () {
                        $('.preloader').show();
                    },
                    complete: function () {
                        $('.preloader').hide();
                    },
                success: function (result) {
                    if(result.success == 1)
                    {
                        $(currentEle).html('<select id="vehicles" class="form-control">'+
                                        '<option>Select Vehicle</option>'+
                            '</select>');
                        $.each(result.vehicles, function (i, item) {
                            $('#vehicles').append($('<option>', { 
                                value: item.id,
                                text : item.vehicle_name 
                            }));
                        });
                        $("#vehicles").focus();
                        $("#vehicles").on('change', function (event) {
                            event.preventDefault();
                            $(currentEle).html($("#vehicles").val());

                            var vehicle_id = $(currentEle).html();
                            var _token = $('input[name="_token"]').val();
                            $.ajax({
                                url: '{{ route('admin.jobs.update-daily-diary') }}',
                                method: 'POST',
                                data: {
                                    '_token': _token,
                                    'vehicle_id': vehicle_id,
                                    'leg_id': leg_id,
                                    'tab': 'vehicle'
                                },
                                dataType: "json",
                                beforeSend: function () {
                        $('.preloader').show();
                    },
                    complete: function () {
                        $('.preloader').hide();
                    },
                                success: function (final) {
                                    if(final.success == 1)
                                    {
                                        $("#vehicle-col").css("width", "5%");
                                        $(currentEle).html(final.vehicle_name);
                                        $.toast({
                                            heading: 'Success',
                                            text: final.message,
                                            icon: 'success',
                                            position: 'top-right',
                                            loader: false,
                                            bgColor: '#00c292',
                                            textColor: 'white'
                                        });
                                    }
                                }
                            });
                            
                        });
                    }
                }
            });
        }
        function openDriverTab(currentEle, value, leg_id) 
        {
            $("#driver-col").css("width", "12%");
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url: '{{ route('admin.jobs.get-daily-diary-drivers') }}',
                method: 'GET',
                data: {
                    '_token': _token,
                },
                dataType: "json",
                beforeSend: function () {
                        $('.preloader').show();
                    },
                    complete: function () {
                        $('.preloader').hide();
                    },
                success: function (result) {
                    if(result.success == 1)
                    {
                        $(currentEle).html('<select id="driver_id" class="form-control">'+
                                        '<option>Select Driver</option>'+
                            '</select>');
                        $.each(result.drivers, function (i, item) {
                            $('#driver_id').append($('<option>', { 
                                value: item.id,
                                text : item.name 
                            }));
                        });
                        $("#driver_id").focus();
                        $("#driver_id").on('change', function (event) {
                            event.preventDefault();
                            $(currentEle).html($("#driver_id").val());

                            var driver_id = $(currentEle).html();
                            var _token = $('input[name="_token"]').val();
                            $.ajax({
                                url: '{{ route('admin.jobs.update-daily-diary') }}',
                                method: 'POST',
                                data: {
                                    '_token': _token,
                                    'driver_id': driver_id,
                                    'leg_id': leg_id,
                                    'tab': 'driver'
                                },
                                dataType: "json",
                                beforeSend: function () {
                        $('.preloader').show();
                    },
                    complete: function () {
                        $('.preloader').hide();
                    },
                                success: function (final) {
                                    if(final.success == 1)
                                    {
                                        $("#driver-col").css("width", "6%");
                                        $(currentEle).html(final.driver_name);
                                        $.toast({
                                            heading: 'Success',
                                            text: final.message,
                                            icon: 'success',
                                            position: 'top-right',
                                            loader: false,
                                            bgColor: '#00c292',
                                            textColor: 'white'
                                        });
                                    }
                                }
                            });
                            
                        });
                    }
                }
            });
        }
        function openOffsidersTab(currentEle, value, leg_id)
        {
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url: '{{ route('admin.jobs.get-daily-diary-offsiders') }}',
                method: 'GET',
                data: {
                    '_token': _token,
                },
                dataType: "json",
                beforeSend: function () {
                        $('.preloader').show();
                    },
                    complete: function () {
                        $('.preloader').hide();
                    },
                success: function (result) {
                    if(result.success == 1)
                    {
                        $(currentEle).html('<select id="offsiders_ids" style="padding: 0px;" multiple="multiple" class="form-control">'+
                                        '<option>Select Driver</option>'+
                            '</select>');
                        $.each(result.offsiders, function (i, item) {
                            $('#offsiders_ids').append($('<option>', { 
                                value: item.id+',',
                                text : item.name 
                            }));
                        });
                        $("#offsiders_ids").focus();
                        $("#offsiders_ids").keyup(function (event) {
                            event.preventDefault();
                            $(currentEle).html($("#offsiders_ids").val());

                            var offsiders_ids = $(currentEle).html();
                            var _token = $('input[name="_token"]').val();
                            $.ajax({
                                url: '{{ route('admin.jobs.update-daily-diary') }}',
                                method: 'POST',
                                data: {
                                    '_token': _token,
                                    'offsiders_ids': offsiders_ids,
                                    'leg_id': leg_id,
                                    'tab': 'offsiders'
                                },
                                dataType: "json",
                                beforeSend: function () {
                        $('.preloader').show();
                    },
                    complete: function () {
                        $('.preloader').hide();
                    },
                                success: function (final) {
                                    if(final.success == 1)
                                    {
                                        $(currentEle).html(final.offsiders_name);
                                        $.toast({
                                            heading: 'Success',
                                            text: final.message,
                                            icon: 'success',
                                            position: 'top-right',
                                            loader: false,
                                            bgColor: '#00c292',
                                            textColor: 'white'
                                        });
                                    }
                                }
                            });
                            
                        });
                    }
                }
            });
            // $(currentEle).html('<select class="form-control" name="action" multiple>'+
            //                         '<option value="' + value + '">'+ value +'</option>'+
            //                         '<option value="' + value + '">'+ value +'</option>'+
            //                         '<option value="' + value + '">'+ value +'</option>'+
            //                     '</select>');
            // $(".offsiders").focus();
            // $(".offsiders").keyup(function (event) {
            //     if (event.keyCode == 13) {
            //         alert("Ajax Call");
            //         $(currentEle).html($(".offsiders").val());
            //     }
            // });
        }
    </script>

@endpush