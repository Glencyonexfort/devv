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
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
@endpush

@section('content')
<div class="content">
    <div class="d-md-flex align-items-md-start">
        @include('sections.removal_setting_menu')
        <div style="flex:auto">
            <div class="card">
                <div class="card-header bg-white header-elements-inline">
                    <h6 class="card-title">@lang('modules.jobTemplates.createTitle')</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            {!! Form::open(['id'=>'createTemplate','class'=>'ajax-form','method'=>'POST']) !!}
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-6 ">
                                        <div class="form-group">
                                            <label>@lang('modules.jobTemplates.job_template_name')</label>
                                            <input type="text" name="job_template_name" id="job_template_name" class="form-control" autocomplete="nope">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.companies.company_name')</label>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <select class="select2 form-control" data-placeholder="Choose Client" name="company_id">
                                                        @foreach($companies as $company)
                                                        <option value="{{ $company->id }}">{{ ucwords($company->company_name) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('modules.jobTemplates.pickup_instructions')</label>
                                            <textarea name="pickup_instructions" id="pickup_instructions" class="summernote"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('modules.jobTemplates.drop_off_instructions')</label>
                                            <textarea name="drop_off_instructions" id="drop_off_instructions" class="summernote"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('modules.jobTemplates.payment_instructions')</label>
                                            <textarea name="payment_instructions" id="payment_instructions" class="summernote"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-info">
                                                <input id="default1" name="default1" value="Y" type="checkbox">
                                                <label for="default1">@lang('modules.jobTemplates.default')</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <hr>
                                        <button type="submit" id="save-form" class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.save')</button>
                                        <a href="{{route('admin.job-templates.index')}}" class="btn btn-default">@lang('app.cancel')</a>
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
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script>
    $('.summernote').summernote({
        height: 200, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
        focus: false // set focus to editable area after initializing summernote
    });

    $('#save-form').click(function() {
        $.easyAjax({
            url: "{{route('admin.job-templates.store')}}",
            container: '#createTemplate',
            type: "POST",
            redirect: true,
            data: $('#createTemplate').serialize()
        })
    });
</script>
@endpush