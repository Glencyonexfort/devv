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
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
@endpush

@section('content')

<div class="content">
    <div class="d-md-flex align-items-md-start">
        @include('sections.admin_setting_menu')
        <div style="flex:auto">
            <div class="card">
                <div class="card-header bg-white header-elements-inline">
                    <h6 class="card-title">@lang('modules.companies.createTitle')</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            {!! Form::open(['id'=>'createCompany','class'=>'ajax-form','method'=>'POST']) !!}
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-6 ">
                                        <div class="form-group">
                                            <label>@lang('modules.companies.company_name')</label>
                                            <input type="text" name="company_name" id="company_name" class="form-control" autocomplete="nope">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.companies.email')</label>
                                            <input type="email" name="email" id="email" class="form-control" autocomplete="nope">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.companies.contact_name')</label>
                                            <input type="text" name="contact_name" id="contact_name" class="form-control" autocomplete="nope">
                                        </div>
                                    </div>
                                    <div class="col-md-6 ">
                                        <div class="form-group">
                                            <label>@lang('modules.companies.phone')</label>
                                            <input type="text" name="phone" id="phone" class="form-control" autocomplete="nope">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.companies.sms_number')</label>
                                            <input type="text" name="sms_number" id="sms_number" class="form-control" autocomplete="nope">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.companies.address')</label>
                                            <input type="text" name="address" id="address" class="form-control" autocomplete="nope">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.companies.abn')</label>
                                            <input type="text" name="abn" id="abn" class="form-control" autocomplete="nope">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('modules.companies.terms_and_conditions')</label>
                                            <textarea name="payment_terms" id="payment_terms" class="summernote"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('modules.companies.customer_sign_off_checklist')</label>
                                            <textarea name="customer_sign_off_checklist" id="customer_sign_off_checklist" class="summernote"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('modules.companies.customer_pre_job_checklist')</label>
                                            <textarea name="customer_pre_job_checklist" id="customer_pre_job_checklist" class="summernote"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Work Order Instructions</label>
                                            <textarea name="work_order_instructions" id="work_order_instructions" class="summernote"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>POD Instructions</label>
                                            <textarea name="pod_instructions" id="pod_instructions" class="summernote"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 ">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-info">
                                                <input id="default1" name="default1" value="Y" type="checkbox">
                                                <label for="default1">@lang('modules.companies.default')</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-info">
                                                <input id="active" name="active" value="Y" type="checkbox">
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
                                                    <img src="http://via.placeholder.com/200x150.png?text=@lang('modules.companies.companyLogo')" alt="" />
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"></div>
                                                <div>
                                                    <span class="btn btn-info btn-file">
                                                        <span class="fileinput-new"> @lang('app.selectImage') </span>
                                                        <span class="fileinput-exists"> @lang('app.change') </span>
                                                        <input type="file" id="image" name="image"> </span>
                                                    <a href="javascript:;" class="btn btn-danger fileinput-exists" data-dismiss="fileinput"> @lang('app.remove') </a>
                                                </div>
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
                                        <a href="{{route('admin.companies.index')}}" class="btn btn-default">@lang('app.cancel')</a>
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
<script src="{{ asset('bootstrap/jquery.bootstrap-touchspin.js')}}"></script>
<script src="{{ asset('plugins/tagify-master/dist/tagify.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script>
    $('.summernote').summernote({
        height: 200, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
        focus: false, // set focus to editable area after initializing summernote
        toolbar: [
            ['font', ['bold', 'underline']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol']],
            ['insert', ['link', 'picture']],
            ['view', ['codeview','fullscreen']],
        ],
    });
    $('#save-form').click(function() {
        $.easyAjax({
            url: "{{route('admin.companies.store')}}",
            container: '#createCompany',
            type: "POST",
            redirect: true,
            file: (document.getElementById("image").files.length == 0) ? false : true,
            data: $('#createCompany').serialize()
        })
    });
</script>
@endpush