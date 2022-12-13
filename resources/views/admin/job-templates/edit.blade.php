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
<style>
    .files input {
        outline: 2px dashed #92b0b3;
        outline-offset: -10px;
        -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
        transition: outline-offset .15s ease-in-out, background-color .15s linear;
        padding: 120px 0px 85px 35%;
        text-align: center !important;
        margin: 0;
        width: 100% !important;
    }

    .files input:focus {
        outline: 2px dashed #92b0b3;
        outline-offset: -10px;
        -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
        transition: outline-offset .15s ease-in-out, background-color .15s linear;
        border: 1px solid #92b0b3;
    }

    .files {
        position: relative
    }

    .files:after {
        pointer-events: none;
        position: absolute;
        top: 60px;
        left: 0;
        width: 50px;
        right: 0;
        height: 56px;
        content: "";
        background-image: url(https://image.flaticon.com/icons/png/128/109/109612.png);
        display: block;
        margin: 0 auto;
        background-size: 100%;
        background-repeat: no-repeat;
    }

    .color input {
        background-color: #f1f1f1;
    }

    .files:before {
        position: absolute;
        bottom: 10px;
        left: 0;
        pointer-events: none;
        width: 100%;
        right: 0;
        height: 57px;
        content: " or drag it here. ";
        display: block;
        margin: 0 auto;
        color: #2ea591;
        font-weight: 600;
        text-transform: capitalize;
        text-align: center;
    }

    .attachment-img {
        width: 24px;
        margin-right: 3px;
    }
</style>
@endpush

@section('content')

<div class="content">
    <div class="d-md-flex align-items-md-start">
        @include('sections.removal_setting_menu')
        <div style="flex:auto">
            <div class="card">
                <div class="card-header bg-white header-elements-inline">
                    <h6 class="card-title">@lang('modules.jobTemplates.updateTitle')</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">

                            {!! Form::open(['id'=>'updateTemplate','method'=>'PUT','files' => true,'url' => 'admin/moving-settings/job-templates/'.$template->id]) !!}
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-6 ">
                                        <div class="form-group">
                                            <label>@lang('modules.jobTemplates.job_template_name')</label>
                                            <input type="text" name="job_template_name" id="job_template_name" value="{{$template->job_template_name}}" class="form-control" autocomplete="nope">
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
                                                        <option value="{{ $company->id }}" @if($company->id == $template->company_id)
                                                            selected=""
                                                            @endif
                                                            >{{ ucwords($company->company_name) }}</option>
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
                                            <textarea name="pickup_instructions" id="pickup_instructions" class="summernote">{{$template->pickup_instructions}}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('modules.jobTemplates.drop_off_instructions')</label>
                                            <textarea name="drop_off_instructions" id="drop_off_instructions" class="summernote">{{$template->drop_off_instructions}}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('modules.jobTemplates.payment_instructions')</label>
                                            <textarea name="payment_instructions" id="payment_instructions" class="summernote">{{$template->payment_instructions}}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group files">
                                            <label>@lang('modules.listJobs.standard_attachments')</label>
                                            <input type="file" class="form-control" name="standard_attachments[]" multiple="">
                                        </div>

                                        <div class="form-group">
                                            @if($template_attachments)
                                            <h3>Attachments</h3>
                                            <ul>
                                                @foreach($template_attachments as $attachment)

                                                <?php
                                                $destinationPath = public_path('/job-template');
                                                $checkFileExists = $destinationPath . '/' . $attachment->attachment_file_name;
                                                if (file_exists($checkFileExists)) { ?>
                                                    <li raggable='true' style='margin-bottom: 15px;'>
                                                        <a href="javascript:">

                                                            <img src="{{asset('img/icons/attach.png')}}" class="attachment-img">{{$attachment->attachment_file_name}}
                                                        </a>


                                                        <a href="javascript:;" style="margin-left: 10px;" data-attachment-id="{{ $attachment->id }}" class="btn btn-danger delete-attachment-btn"><i class="fa fa-times delete-attachment-btn"> </i></a>
                                                    </li>
                                                <?php } ?>

                                                @endforeach
                                            </ul>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-info">
                                                <input id="default1" name="default1" value="Y" {{ $template->default1=='Y'?'checked=""':'' }} type="checkbox">
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
                                        <button type="submit" id="save-form" class="btn btn-success"><i class="fa fa-check"></i> @lang('app.update')</button>
                                        <a href="{{ route('admin.job-templates.index') }}" class="btn btn-default">@lang('app.back')</a>
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
<script src="{{ asset('plugins/tagify-master/dist/tagify.js') }}"></script>
<script src="{{ asset('bootstrap/jquery.bootstrap-touchspin.js')}}"></script>
<script src="{{ asset('plugins/tagify-master/dist/tagify.js') }}"></script>
<script>
    $('.summernote').summernote({
        height: 200, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
        focus: false // set focus to editable area after initializing summernote
    });

    $(function() {

        $('body').on('click', '.delete-attachment-btn', function() {
            var id = $(this).data('attachment-id');
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover the deleted attachment!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm) {
                if (isConfirm) {

                    var url = "{{ route('admin.job-templates.destroy-attachment',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";
                    $.easyAjax({
                        type: 'DELETE',
                        url: url,
                        data: {
                            '_token': token
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                window.location.reload();
                            }
                        }
                    });
                }
            });
        });
    });
    //$('#save-form').click(function () {
    //    $.easyAjax({
    //        url: '{{route('admin.job-templates.update', [$template->id])}}',
    //        container: '#updateTemplate',
    //        type: "POST",
    //        redirect: true,
    //        processData: false,
    //        contentType: false,
    //        data: $('#updateTemplate').serialize()
    //    })
    //});
</script>
@endpush