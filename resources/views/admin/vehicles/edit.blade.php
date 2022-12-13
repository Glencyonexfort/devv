@extends('layouts.app')

@section('page-title')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="{{ $pageIcon }} mr-2"></i> <span class="font-weight-semibold">{{ $pageTitle }}</span></h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
</div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/tagify-master/dist/tagify.css') }}">
@endpush

@section('content')
<div class="content">
    <div class="d-md-flex align-items-md-start">
        @include('sections.removal_setting_menu')
        <div style="flex:auto">
            <div class="card">
                <div class="card-header bg-white header-elements-inline">
                    <h6 class="card-title">@lang('modules.vehicles.updateTitle')</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">

                            {!! Form::open(['id'=>'updateVehicle','class'=>'ajax-form','method'=>'PUT']) !!}
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-6 ">
                                        <div class="form-group">
                                            <label>@lang('modules.vehicles.vehicle_name')</label>
                                            <input type="text" name="vehicle_name" id="vehicle_name" value="{{$template->vehicle_name}}" class="form-control" autocomplete="nope">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <div class="form-group">
                                            <label>@lang('modules.vehicles.vehicle_description') </label>
                                            <input type="text" name="vehicle_description" id="vehicle_description" value="{{$template->vehicle_description}}" class="form-control" autocomplete="nope">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 ">
                                        <div class="form-group">
                                            <label>@lang('modules.vehicles.fuel_type')</label>
                                            <input type="text" name="fuel_type" id="fuel_type" value="{{$template->fuel_type}}" class="form-control" autocomplete="nope">
                                        </div>
                                    </div>
                                    <div class="col-md-6 ">
                                        <div class="form-group">
                                            <label>@lang('modules.vehicles.license_plate_number')</label>
                                            <input type="text" name="license_plate_number" id="license_plate_number" value="{{$template->license_plate_number}}" class="form-control" autocomplete="nope">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 ">
                                        <div class="form-group">
                                            <label>@lang('modules.vehicles.manufacturer')</label>
                                            <input type="text" name="manufacturer" id="manufacturer" value="{{$template->manufacturer}}" class="form-control" autocomplete="nope">
                                        </div>
                                    </div>
                                    <div class="col-md-3 ">
                                        <div class="form-group">
                                            <label>@lang('modules.vehicles.model')</label>
                                            <input type="text" name="model" id="model" value="{{$template->model}}" class="form-control" autocomplete="nope">
                                        </div>
                                    </div>
                                    <div class="col-md-3 ">
                                        <div class="form-group">
                                            <label>@lang('modules.vehicles.model_year')</label>
                                            <input type="text" name="model_year" id="model_year" value="{{$template->model_year}}" class="form-control" autocomplete="nope">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.vehicles.capacity_in_tons')</label>
                                            <input type="text" name="payload" id="payload" value="{{ $template->payload }}" class="form-control" autocomplete="nope">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.vehicles.cubic_capacity')</label>
                                            <input type="text" name="cubic_capacity" id="cubic_capacity" value="{{ $template->cubic_capacity }}" class="form-control" autocomplete="nope">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 ">
                                        <div class="form-group">
                                            <label>@lang('modules.vehicles.vehicle_colour')</label>
                                            <input type="color" id="vehicle_colour_picker" class="form-control" value="{{$template->vehicle_colour}}" style="padding: 0px;"/>
                                            <input type="text" name="vehicle_colour" id="vehicle_colour" class="form-control" value="{{$template->vehicle_colour}}"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.vehicleGroups.boxTitle')</label>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <select class="select2 form-control"
                                                        data-placeholder="Choose Vehicle Group" name="category">
                                                        @foreach ($vehicleGroups as $rs)
                                                            <option value="{{ $rs->group_name }}" @if ($rs->group_name == $template->category)
                                                                selected=""
                                                        @endif
                                                        >{{ $rs->group_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-info">
                                                <input id="active" name="active" value="Y" {{ $template->active=='Y'?'checked=""':'' }} type="checkbox">
                                                <label for="active">@lang('modules.companies.active')</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <hr>
                                        <button type="submit" id="save-form" class="btn btn-success"><i class="fa fa-check"></i> @lang('app.update')</button>
                                        <a href="{{ route('admin.vehicles.index') }}" class="btn btn-default">@lang('app.back')</a>
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('footer-script')
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/tagify-master/dist/tagify.js') }}"></script>
<script>
    $(document).ready(function() { 
        $("body").off('change', '#vehicle_colour_picker').on('change', '#vehicle_colour_picker', function(e) {
            $("#vehicle_colour").val($(this).val());
        });
        $("body").off('keyup', '#vehicle_colour').on('keyup', '#vehicle_colour', function(e) {
            $("#vehicle_colour_picker").val($(this).val());
        });
    });
    $('#save-form').click(function() {
        $.easyAjax({
            url: "{{route('admin.vehicles.update', [$template->id])}}",
            container: '#updateVehicle',
            type: "POST",
            redirect: true,
            data: $('#updateVehicle').serialize()
        })
    });
</script>
@endpush