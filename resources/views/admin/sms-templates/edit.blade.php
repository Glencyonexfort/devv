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
                    <a href="{{ route('admin.sms-templates.index') }}" class="breadcrumb-item">{{ $pageTitle }}</a>
                    <span class="breadcrumb-item active">@lang('app.edit')</span>
                </div>
            </div>
        </div> -->
    </div>
    <!-- /page header and Breadcrumb-->
@endsection

@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/tagify-master/dist/tagify.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
@endpush

@section('content')
    <div class="content">
        <!-- Inner container -->
        <div class="d-md-flex align-items-md-start">
            @include('sections.admin_moving_setting_menu')
            <div style="flex:auto">
                <div class="card">
                    <div class="card-header bg-white header-elements-inline">
                        <h6 class="card-title">@lang('modules.smsTemplates.updateTitle')</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                {!! Form::open(['id' => 'updateTemplate', 'class' => 'ajax-form', 'method' => 'PUT']) !!}
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-12 ">
                                            <div class="form-group">
                                                <label>@lang('modules.smsTemplates.sms_template_name')</label>
                                                <input type="text" name="sms_template_name" id="sms_template_name"
                                                    value="{{ $template->sms_template_name }}" class="form-control"
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
                                    <!-- <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('modules.smsTemplates.available_dynamic_fields')</label>
                                        <div class="row">
                                            <div class="col-md-12">
                                                {job_id}, {first_name}, {last_name}, {pickup_address}, {delivery_address}, {job_date}, {phone}, {email}, {total_amount}, {total_paid}, {total_due}, {external_inventory_form}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>       -->
                                    <!-- <div class="row">
                                <div class="col-md-6 ">
                                    <div class="form-group">
                                        <label>@lang('modules.smsTemplates.email_subject')</label>
                                        <input type="text" name="email_subject" id="email_subject" value="{{ $template->email_subject }}" class="form-control" autocomplete="nope">
                                    </div>
                                </div>
                            </div>   -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>@lang('modules.emailTemplates.available_dynamic_fields') <span class="muted">(click to copy)</span></label>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <span id="d1" onclick="copyToClipboard('#d1')">{job_id}</span>,
                                                        <span id="d2" onclick="copyToClipboard('#d2')">{first_name}</span>,
                                                        <span id="d3" onclick="copyToClipboard('#d3')">{last_name}</span>,
                                                        <span id="d4" onclick="copyToClipboard('#d4')">{pickup_suburb}</span>,
                                                        <span id="d5" onclick="copyToClipboard('#d5')">{pickup_address}</span>,
                                                        <span id="d6" onclick="copyToClipboard('#d6')">{delivery_address}</span>,
                                                        <span id="d7" onclick="copyToClipboard('#d7')">{est_start_time}</span>,
                                                        <span id="d8" onclick="copyToClipboard('#d8')">{est_first_leg_start_time}</span>,
                                                        <span id="d9" onclick="copyToClipboard('#d9')">{delivery_suburb}</span>,
                                                        <span id="d10" onclick="copyToClipboard('#d10')">{user_first_name}</span>,
                                                        <span id="d11" onclick="copyToClipboard('#d11')">{user_last_name}</span>,
                                                        <span id="d12" onclick="copyToClipboard('#d12')">{mobile}</span>,
                                                        <span id="d13" onclick="copyToClipboard('#d13')">{email}</span>,
                                                        <span id="d14" onclick="copyToClipboard('#d14')">{total_amount}</span>,
                                                        <span id="d15" onclick="copyToClipboard('#d15')">{total_paid}</span>,
                                                        <span id="d16" onclick="copyToClipboard('#d16')">{total_due}</span>,
                                                        <span id="d17" onclick="copyToClipboard('#d17')">{external_inventory_form}</span>                                                    
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 ">
                                            <div class="form-group">
                                                <label>@lang('modules.smsTemplates.sms_message')</label>
                                                <textarea name="sms_message" id="sms_message" rows="5" class="form-control"
                                                    autocomplete="nope">{{ $template->sms_message }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Attach Quote</label>
                                                <div class="switchery-demo">
                                                    <input type="checkbox" id="attach_quote" name="attach_quote" value="Y" {{ $template->attach_quote == 'Y' ? 'checked=""' : '' }} class="js-switch " data-color="#f96262" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Attach Invoice</label>
                                                <div class="switchery-demo">
                                                    <input type="checkbox" id="attach_invoice" name="attach_invoice" value="Y" {{ $template->attach_invoice == 'Y' ? 'checked=""' : '' }} class="js-switch " data-color="#f96262" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label">@lang('modules.companies.active')</label>
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
                                        <a href="{{ route('admin.sms-templates.index') }}"
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
        function copyToClipboard(e){
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($(e).text()).select();
            document.execCommand("copy");
            //$temp.remove();
            $.toast({
                heading: 'Copied',
                        text: "Dynamic Field <b>"+ $temp.val() +"</b> copied",
                        icon: 'info',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#07a9ff',
                        textColor: 'white'
                    });
        }

        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function() {
            new Switchery($(this)[0], $(this).data());
        });
        
        $('.summernote').summernote({
            height: 200, // set editor height
            minHeight: null, // set minimum height of editor
            maxHeight: null, // set maximum height of editor
            focus: false // set focus to editable area after initializing summernote
        });
        $('#save-form').click(function() {
            $.easyAjax({
                url: "{{ route('admin.sms-templates.update', [$template->id]) }}",
                container: '#updateTemplate',
                type: "POST",
                redirect: true,
                data: $('#updateTemplate').serialize()
            })
        });

    </script>
@endpush
