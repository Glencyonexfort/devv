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
            <li><a href="{{ route('admin.companies.index') }}">{{ $pageTitle }}</a></li>
            <li class="active">@lang('app.edit')</li>
        </ol>
    </div>
    <!-- /.breadcrumb -->
</div>
@endsection

@push('head-script')
<link rel="stylesheet"
      href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/tagify-master/dist/tagify.css') }}">
@endpush

@section('content')

<div class="row">
    <div class="col-md-12">

        <div class="panel panel-inverse">
            <div class="panel-heading"> @lang('modules.companies.updateCompany')</div>
            <div class="panel-wrapper collapse in" aria-expanded="true">
                <div class="panel-body">
                    {!! Form::open(['id'=>'updateCompany','class'=>'ajax-form','method'=>'PUT']) !!}
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-6 ">
                                <div class="form-group">
                                    <label>@lang('modules.companies.company_name')</label>
                                    <input type="text" name="company_name" id="company_name" value="{{ $company->company_name }}" class="form-control" autocomplete="nope">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('modules.companies.email')</label>
                                    <input type="email" name="email" id="email" value="{{ $company->email }}" class="form-control" autocomplete="nope">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('modules.companies.contact_name')</label>
                                    <input type="text" name="contact_name" id="contact_name" value="{{ $company->contact_name }}" class="form-control" autocomplete="nope">
                                </div>
                            </div>
                            <div class="col-md-6 ">
                                <div class="form-group">
                                    <label>@lang('modules.companies.phone')</label>
                                    <input type="text" name="phone" id="phone" value="{{ $company->phone }}" class="form-control" autocomplete="nope">
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('modules.companies.sms_number')</label>
                                    <input type="text" name="sms_number" id="sms_number" value="{{ $company->sms_number }}" class="form-control" autocomplete="nope">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('modules.companies.address')</label>
                                    <input type="text" name="address" id="address" value="{{ $company->address }}" class="form-control" autocomplete="nope">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('modules.companies.abn')</label>
                                    <input type="text" name="abn" id="abn" value="{{ $company->abn }}" class="form-control" autocomplete="nope">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 ">
                                <div class="form-group">
                                    <div class="checkbox checkbox-info">
                                        <input id="default1" name="default1" value="Y"  {{ $company->default1=='Y'?'checked=""':'' }} type="checkbox">
                                        <label for="default1">@lang('modules.companies.default')</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="checkbox checkbox-info">
                                        <input id="active" name="active" value="Y"  {{ $company->active=='Y'?'checked=""':'' }} type="checkbox">
                                        <label for="active">@lang('modules.companies.active')</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label>@lang('modules.companies.companyLogo')</label>
                                <div class="form-group">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                            @if(empty($company->logo))
                                            <img src="http://via.placeholder.com/200x150.png?text=@lang('modules.companies.companyLogo')"   alt=""/>
                                            @else
                                            <img src="{{ asset('user-uploads/company-logo/'.$company->logo) }}" alt=""/>
                                            @endif
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"></div>
                                        <div>
                                            <span class="btn btn-info btn-file">
                                                <span class="fileinput-new"> @lang('app.selectImage') </span>
                                                <span class="fileinput-exists"> @lang('app.change') </span>
                                                <input type="file" id="image" name="image"> </span>
                                            <a href="javascript:;" class="btn btn-danger fileinput-exists"
                                               data-dismiss="fileinput"> @lang('app.remove') </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" id="save-form" class="btn btn-success"><i
                                class="fa fa-check"></i> @lang('app.update')</button>
                        <a href="{{ route('admin.companies.index') }}" class="btn btn-default">@lang('app.back')</a>
                    </div>
                    {!! Form::close() !!}
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
$('#save-form').click(function () {
    $.easyAjax({
        url: '{{route('admin.companies.update', [$company->id])}}',
        container: '#updateCompany',
        type: "POST",
        redirect: true,
        file: (document.getElementById("image").files.length == 0) ? false : true,
        data: $('#updateCompany').serialize()
    })
});
</script>
@endpush

