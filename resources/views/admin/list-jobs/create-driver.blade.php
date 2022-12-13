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
                <li class="active">{{ $pageTitle }}</a>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/tagify-master/dist/tagify.css') }}">
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-inverse">
                <div class="panel-heading"> @lang('modules.drivers.createTitle')</div>
                <div class="panel-wrapper collapse in show" aria-expanded="true">
                    <div class="panel-body">
                        {!! Form::open(['id'=>'createDriver','class'=>'ajax-form','method'=>'POST']) !!}
                            <div class="form-body">
                                <div class="row">

                                   <!--  <div class="col-md-12 ">
                                        <div class="form-group">
                                            <label>@lang('modules.drivers.addDriverInfo')</label>
                                            <input type="text" name="driver_info" id="driver_info" class="form-control" autocomplete="nope">
                                        </div>
                                    </div> -->

                                    <div class="col-md-6 ">
                                        <div class="form-group">
                                            <label>@lang('modules.drivers.driverName')</label>
                                            <input type="text" name="name" id="name" class="form-control" autocomplete="nope">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.drivers.driverEmail')</label>
                                            <input type="email" readonly onfocus="this.removeAttribute('readonly');" onblur="this.setAttribute('readonly','');" name="email" id="email" class="form-control" autocomplete="nope" >
                                            <span class="help-block">@lang('modules.drivers.driverNote')</span>
                                        </div>
                                    </div>
                                    <!--/span-->
                                </div>
                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.drivers.driverPassword')</label>
                                            <input type="password" style="display: none">
                                            <input type="password" autocomplete=off name="password" id="password" class="form-control" autocomplete="nope" readonly onfocus="this.removeAttribute('readonly');" onblur="this.setAttribute('readonly','');">
                                            <span class="help-block"> @lang('modules.drivers.passwordNote') </span>
                                        </div>
                                    </div>
                                    <!--/span-->

                                    <!-- <div class="col-xs-12 col-md-6 m-t-20">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-info">
                                                <input id="random_password" name="random_password" value="true"
                                                       type="checkbox" >
                                                <label for="random_password">@lang('modules.client.generateRandomPassword')</label>
                                            </div>
                                        </div>
                                    </div> -->
                                    <!--/span-->
                                </div>
                                <!--/row-->

                            </div>
                            <div class="form-actions">
                                <button type="submit" id="save-form" class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.save')</button>
                                <button type="reset" class="btn btn-default">@lang('app.reset')</button>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>    <!-- .row -->

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

<script>

    $("#joining_date, #end_date").datepicker({
        format: '{{ $global->date_picker_format }}',
        todayHighlight: true,
        autoclose: true
    });

    $('#save-form').click(function () {
        var data = $('#createDriver').serialize();
        console.log(data);
        $.easyAjax({
            url: '{{route("admin.list-jobs.store-driver")}}',
            container: '#createDriver',
            type: "POST",
            redirect: true,
            data: $('#createDriver').serialize()
        })
    });

    $('#random_password').change(function () {
        var randPassword = $(this).is(":checked");

        if(randPassword){
            $('#password').val('{{ str_random(8) }}');
            $('#password').attr('readonly', 'readonly');
        }
        else{
            $('#password').val('');
            $('#password').removeAttr('readonly');
        }
    });
</script>
@endpush
