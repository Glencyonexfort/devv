@extends('layouts.app')

@section('page-title')
    <!-- Page header and Breadcrumb -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><i class="{{ $pageIcon }}"></i> <span class="font-weight-semibold"> {{ $pageTitle }}</span></h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>
        </div>

        <!-- <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
                <div class="d-flex">
                    <div class="breadcrumb">
                        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item"> <i class="icon-home2 mr-2"></i> @lang('app.menu.home')</a>
                        <a href="{{ route('admin.email-templates.index') }}" class="breadcrumb-item">{{ $pageTitle }}</a>
                        <span class="breadcrumb-item active">@lang('app.edit')</span>
                    </div>
                </div>
            </div> -->
    </div>
    <!-- /page header and Breadcrumb-->
@endsection

@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/tagify-master/dist/tagify.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/calculator/layout.css') }}" />
    <style>        
        .note-group-select-from-files {
            display: none;
        }
        .attachment-img {
            width: 24px;
            margin-right: 3px;
        }

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
            pointer-events: none;
            position: absolute;
            top: 35px;
            left: 200px;
            width: 100%;
            content: "\f0ee";
            font-family: FontAwesome;
            display: block;
            margin: 0 auto;
            background-size: 100%;
            background-repeat: no-repeat;
            font-size: 80px;
            opacity: 0.4;
        }

        .color input {
            background-color: #f1f1f1;
        }

        .files:before {
            position: absolute;
            bottom: 0px;
            font-size: 22px;
            left: 0;
            pointer-events: none;
            width: 100%;
            right: 0;
            height: 57px;
            content: " or drag it here. ";
            display: block;
            margin: 0 auto;
            color: #999;
            font-weight: 600;
            text-align: center;
        }

        .checkbox-info {
            padding-left: 0px !important;
        }

        .attachment-link {
            font-size: 16px;
            color: #f44336 !important;
            font-weight: 400;
        }

    </style>
@endpush

@section('content')
    <div class="content">
        <!-- Inner container -->
        <div class="d-md-flex align-items-md-start">
            @include('sections.admin_moving_setting_menu')
            <div style="flex:auto">
                <div class="card">
                    <div class="card-header bg-white header-elements-inline">
                        <h6 class="card-title">@lang('modules.emailTemplates.updateTitle')</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                {!! Form::open(['id' => 'updateTemplate', 'class' => 'ajax-form', 'method' => 'PUT']) !!}
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-12 ">
                                            <div class="form-group">
                                                <label>@lang('modules.emailTemplates.email_template_name')</label>
                                                <input type="text" name="email_template_name" id="email_template_name"
                                                    value="{{ $template->email_template_name }}" class="form-control"
                                                    autocomplete="nope">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>@lang('modules.companies.company_name')</label>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <select class="select2 form-control"
                                                            data-placeholder="Choose Client" name="company_id">
                                                            @foreach ($companies as $company)
                                                                <option value="{{ $company->id }}" @if ($company->id == $template->company_id)
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
                                                <label>@lang('modules.emailTemplates.available_dynamic_fields')</label>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        {job_id}, {first_name}, {last_name}, {pickup_suburb},
                                                        {delivery_suburb}, {pickup_address}, {delivery_address}, {pickup_access}, {drop_off_access}, {job_date}, {mobile}, {email}, {total_amount},
                                                        {total_paid}, {total_due}, {external_inventory_form}, {external_inventory_form_button}, {inventory_list}, {book_now_button}, 
                                                        {user_first_name}, {user_last_name}, {est_start_time}, {est_first_leg_start_time}, {user_email_signature}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>From Email Name</label>
                                                <input type="text" name="from_email_name" id="from_email_name"
                                                    value="{{ $template->from_email_name }}" class="form-control"
                                                    autocomplete="nope">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 ">
                                            <div class="form-group">
                                                <label>From Email ID</label>
                                                <input type="text" name="from_email" id="from_email"
                                                    value="{{ $template->from_email }}" class="form-control"
                                                    autocomplete="nope">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 ">
                                            <div class="form-group">
                                                <label>@lang('modules.emailTemplates.email_subject')</label>
                                                <input type="text" name="email_subject" id="email_subject"
                                                    value="{{ $template->email_subject }}" class="form-control"
                                                    autocomplete="nope">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 ">
                                            <div class="form-group">
                                                <label>@lang('modules.emailTemplates.email_body')</label>
                                                <textarea name="email_body" id="email_body" rows="5"
                                                    class="summernote form-control"
                                                    autocomplete="nope">{{ $template->email_body }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">Attach Quote</label>
                                                <div class="switchery-demo">
                                                    <input type="checkbox" id="attach_quote" name="attach_quote" value="Y" {{ $template->attach_quote == 'Y' ? 'checked=""' : '' }} class="js-switch " data-color="#f96262" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">Attach Invoice</label>
                                                <div class="switchery-demo">
                                                    <input type="checkbox" id="attach_invoice" name="attach_invoice" value="Y" {{ $template->attach_invoice == 'Y' ? 'checked=""' : '' }} class="js-switch " data-color="#f96262" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">Attach Work Order</label>
                                                <div class="switchery-demo">
                                                    <input type="checkbox" id="attach_work_order" name="attach_work_order" value="Y" {{ $template->attach_work_order == 'Y' ? 'checked=""' : '' }} class="js-switch " data-color="#f96262" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">Attach Insurance Quote</label>
                                                @if($coverFreight_connected==false)
                                                <small id="attach_insurance" class="form-text text-muted" style="margin-top: -12px;"><a href="{{ route('admin.coverfreight.index') }}">Connect CoverFreight</a> to enable this checkbox</small>
                                                @endif
                                                <div class="switchery-demo">
                                                    <input type="checkbox" id="attach_insurance" name="attach_insurance" value="Y" {{ $template->attach_insurance == 'Y' ? 'checked=""' : '' }} class="js-switch " data-color="#f96262" {{ $coverFreight_connected==false?'disabled':'' }}/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">Attach Proof of Delivery</label>
                                                <div class="switchery-demo">
                                                    <input type="checkbox" id="attach_pod" name="attach_pod" value="Y" {{ $template->attach_pod == 'Y' ? 'checked=""' : '' }} class="js-switch " data-color="#f96262" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">Attach Storage Invoice</label>
                                                <div class="switchery-demo">
                                                    <input type="checkbox" id="attach_storage_invoice" name="attach_storage_invoice" value="Y" {{ $template->attach_storage_invoice == 'Y' ? 'checked=""' : '' }} class="js-switch " data-color="#f96262" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div id="attachment_grid">
                                                @include('admin.email-templates.attachment_grid')
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group files">
                                                <label>Upload Your File </label>
                                                <input id="attachment" type="file" class="form-control" name="attachment">
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="row">
                                        <div class="col-md-6">
                                            <input id="upload_attachment" type="button" class="btn btn-outline-danger"
                                                value="Upload" style="width: 100%;" />
                                        </div>
                                    </div><br /> --}}
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label">@lang('modules.emailTemplates.active')</label>
                                                <div class="switchery-demo">
                                                    <input type="checkbox" id="active" id="active" name="active" value="Y"
                                                        {{ $template->active == 'Y' ? 'checked=""' : '' }}
                                                        class="js-switch " data-color="#f96262" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <hr>
                                        <button type="submit" id="save-form" class="btn btn-success"><i
                                                class="fa fa-check"></i> @lang('app.update')</button>
                                        <a href="{{ route('admin.email-templates.index') }}"
                                            class="btn btn-default">@lang('app.back')</a>
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
    <script src="{{ asset('bootstrap/jquery.bootstrap-touchspin.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
    <script>
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function() {
            new Switchery($(this)[0], $(this).data());
        });

        $('.summernote').summernote({
            height: 200, // set editor height
            minHeight: null, // set minimum height of editor
            maxHeight: null, // set maximum height of editor
            focus: true, // set focus to editable area after initializing summernote
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']],
            ]
        });

        $('#save-form').click(function() {
            $.easyAjax({
                url: "{{ route('admin.email-templates.update', [$template->id]) }}",
                container: '#updateTemplate',
                type: "POST",
                redirect: true,
                data: $('#updateTemplate').serialize()
            })
        });

        $('body').on('change', '#attachment', function(e) {
            e.preventDefault();
            var formData = new FormData();
            formData.append('attachment', $('#attachment')[0].files[0]);
            formData.append('templateid', "{{ $template->id }}");
            formData.append('_token', "{{ csrf_token() }}");
            $.ajax({
                url: "/admin/moving-settings/storeTemplateAttachment",
                method: 'post',
                processData: false,
                contentType: false,
                data: formData,
                dataType: "json",
                beforeSend: function() {
                    $.blockUI();
                },
                complete: function() {
                    $.unblockUI();
                },
                success: function(result) {

                    if (result.error == 0) {
                        $('#attachment_grid').html(result.html);
                        $('#attachment').val('');
                        //Notification....
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
                            text: result.message,
                            icon: 'error',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#fb9678',
                            textColor: 'white'
                        });
                        //..
                    }
                }
            });
        });

        $('body').on('click', '.remove-attachment', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var _token = "{{ csrf_token() }}";
            $.ajax({
                url: "/admin/moving-settings/removeTemplateAttachment",
                method: 'post',
                data: {
                    'id': id,
                    '_token': _token
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
                        $('#attachment_grid').html(result.html);
                        //Notification....
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
                            text: result.message,
                            icon: 'error',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#fb9678',
                            textColor: 'white'
                        });
                        //..
                    }
                }
            });
        });

    </script>
@endpush
