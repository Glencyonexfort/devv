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
                    <h6 class="card-title">@lang('modules.emailSequences.updateTitle')</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            {!! Form::open(['id'=>'updateSequence','class'=>'ajax-form','method'=>'PUT']) !!}
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('modules.emailSequences.sequence_name')</label>
                                            <input type="text" name="sequence_name" id="sequence_name" value="{{$sequence->sequence_name}}" class="form-control" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <div class="form-group">
                                            <label>@lang('modules.emailSequences.description')</label>
                                            <textarea name="sequence_description" id="sequence_description" rows="2" class="form-control">{{$sequence->sequence_description}}</textarea>
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
                                                            <option value="{{ $company->id }}">
                                                                {{ ucwords($company->company_name) }}
                                                            </option>
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
                                            {{-- <label>@lang('modules.emailSequences.check_frequency')</label> --}}
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <select class="select2 form-control" data-placeholder="Choose Frequency" name="check_frequency" hidden="true">
                                                        <option value="240">4 hours</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                                <div class="row hide">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">@lang('modules.emailSequences.is_opportunity')</label>
                                            <div class="switchery-demo">
                                                <input type="checkbox" id="is_opportunity" name="is_opportunity" value="Y" {{ $sequence->is_opportunity=='Y'?'checked=""':'' }} class="js-switch " data-color="#f96262" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 is_not_oppertuniy_row">
                                        <div class="form-group">
                                            <label>@lang('modules.emailSequences.sys_job_type')</label>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <select class="select2 form-control" name="sys_job_type">
                                                        @foreach($sys_job_type as $rs)
                                                        <option value="{{ $rs->options }}" @if($rs->options == $sequence->sys_job_type)
                                                            selected=""
                                                            @endif
                                                            >{{ $rs->options }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 ">
                                        <div class="form-group">
                                            <label>@lang('modules.emailSequences.date_check')</label>                                            
                                            <select class="select2 form-control" data-placeholder="Choose Sequence Type" id="sequence_type" name="sequence_type">
                                                <option value="Status Date">Status Date</option>
                                                <option value="Job Date" @if('Job Date' == $sequence->sequence_type)
                                                    selected=""
                                                    @endif>Job Date</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 status_date_box">
                                        <div class="form-group">
                                            <label>@lang('modules.emailSequences.status_date_send_email_after_number_of_days')</label>
                                            <input type="number" step="1" max="999" maxlength="3" name="days_after_initial_status" id="days_after_initial_status" value="{{$sequence->days_after_initial_status}}" class="form-control" autocomplete="off" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 job_date_box" style="display: none;">
                                        <div class="form-group">
                                            <label>@lang('modules.emailSequences.job_date_send_email_before_after_nunber_of_days')</label>
                                            <input type="number" step="1" max="999" maxlength="3" name="days_before_after_job_date" id="days_before_after_job_date" value="{{$sequence->days_before_after_job_date}}" class="form-control" autocomplete="off" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row is_not_oppertuniy_row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.emailSequences.initial_status')</label>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <select class="select2 form-control" name="initial_status">
                                                        @foreach($job_status as $rs)
                                                        <option value="{{ $rs->options }}" @if($rs->options == $sequence->initial_status)
                                                            selected=""
                                                            @endif
                                                            >{{ $rs->options }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.emailSequences.post_status')</label>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <select class="select2 form-control" name="post_status">
                                                        @foreach($job_status as $rs)
                                                        <option value="{{ $rs->options }}" @if($rs->options == $sequence->post_status)
                                                            selected=""
                                                            @endif
                                                            >{{ $rs->options }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row is_oppertuniy_row" style="display:none;">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.emailSequences.initial_status')</label>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <select class="select2 form-control" name="pipeline_status">
                                                        @foreach($pipeline_statuses as $rs)
                                                        <option value="{{ $rs->pipeline_status }}" @if($rs->pipeline_status == $sequence->initial_status)
                                                            selected=""
                                                            @endif
                                                            >{{ $rs->pipeline_status }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.emailSequences.post_status')</label>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <select class="select2 form-control" name="pipeline_status2">
                                                        @foreach($pipeline_statuses as $rs)
                                                        <option value="{{ $rs->pipeline_status }}" @if($rs->pipeline_status == $sequence->post_status)
                                                            selected=""
                                                            @endif
                                                            >{{ $rs->pipeline_status }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row hide">
                                            <div class="col-sm-12 col-md-12 col-xs-12">
                                                <div class="form-group">
                                                    <label class="control-label">@lang('modules.emailSequences.send_email')</label>
                                                    <div class="switchery-demo">
                                                        <input type="checkbox" id="send_email" id="send_email" name="send_email" {{ $sequence->send_email=='Y'?'checked=""':'' }} value="Y" class="js-switch " data-color="#f96262" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>@lang('modules.emailSequences.email_templates')</label>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <select class="select2 form-control" name="email_template_id">
                                                                @foreach($email_templates as $rs)
                                                                <option value="{{ $rs->id }}" @if($rs->id == $sequence->email_template_id)
                                                                    selected=""
                                                                    @endif
                                                                    >{{ $rs->email_template_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 ">
                                                <div class="form-group">
                                                    <label>@lang('modules.emailSequences.from_email')

                                                    </label>               
                                                    <input type="text" name="from_email" id="from_email" placeholder="contact@yourcompany.com" value="{{ $sequence->from_email }}" class="form-control" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 ">
                                                <div class="form-group">
                                                    <label>@lang('modules.emailSequences.from_email_name')</label>
                                                    <!-- <label>From Email Name(example: Contact, YourCompany)</label> -->
                                                    <input type="text" name="from_email_name" id="from_email_name" placeholder="Contact, YourCompany" value="{{ $sequence->from_email_name }}" class="form-control" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row hide">
                                            <div class="col-sm-12 col-md-12 col-xs-12">
                                                <div class="form-group">
                                                    <label class="control-label">@lang('modules.emailSequences.send_sms')</label>
                                                    <div class="switchery-demo">
                                                        <input type="checkbox" id="send_sms" id="send_sms" name="send_sms" {{ $sequence->send_sms=='Y'?'checked=""':'' }} value="Y" class="js-switch " data-color="#f96262" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>@lang('modules.emailSequences.sms_templates')</label>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <select class="select2 form-control" name="sms_template_id">
                                                                @foreach($sms_templates as $rs)
                                                                <option value="{{ $rs->id }}" @if($rs->id == $sequence->sms_template_id)
                                                                    selected=""
                                                                    @endif
                                                                    >{{ $rs->sms_template_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 ">
                                                <div class="form-group">
                                                    <label>@lang('modules.emailSequences.from_number_name')</label>
                                                    <input type="text" name="from_sms_number_name" id="from_sms_number_name" value="{{ $sequence->from_sms_number_name }}" class="form-control" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row hide">
                                    <div class="col-sm-12 col-md-12 col-xs-12">
                                        <div class="form-group">
                                            <label class="control-label">@lang('modules.emailSequences.active')</label>
                                            <div class="switchery-demo">
                                                <input type="checkbox" id="active" id="active" name="active" {{ $sequence->active=='Y'?'checked=""':'' }} value="Y" class="js-switch " data-color="#f96262" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <hr>
                                    <button type="submit" id="save-form" class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.update')</button>
                                    <a href="{{route('admin.email-sequences.index')}}" class="btn btn-default">@lang('app.cancel')</a>
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
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script>
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    $('.js-switch').each(function() {
        new Switchery($(this)[0], $(this).data());
    });
    $(".js-switch").change(function() {
        var $this = $(this);
        if ($this.prop('id') == 'is_opportunity') {
            $('.is_oppertuniy_row').toggle();
            $('.is_not_oppertuniy_row').toggle();
        }
    });
    $('#updateSequence').submit(function() {
        $.easyAjax({
            url: "{{route('admin.email-sequences.update', [$sequence->id])}}",
            container: '#updateSequence',
            type: "POST",
            redirect: true,
            data: $('#updateSequence').serialize(),
            beforeSend: function() {
                        $.blockUI();
                    },
            complete: function() {
                        $.unblockUI();
                    },
            success: function(result) {
                        if (result.error == 1) {
                            swal({
                                title: "Error",
                                text: result.message,
                                type: "error",
                                button: "OK",
                            });
                        } else {
                            //..
                            window.location.replace("{{ route('admin.email-sequences.index') }}");
                        }
                }   
        })
    });
    $(document).ready(function() {
        @if($sequence->is_opportunity == 'Y')
        $('.is_oppertuniy_row').toggle();
        $('.is_not_oppertuniy_row').toggle();
        @endif

        $('#sequence_type').on('change', function(){
            var $this = $(this);
            if($this.val() == 'Job Date'){
                $('.status_date_box').hide();
                $('.job_date_box').show();
                $('#days_after_initial_status').prop('required', false);
                $('#days_before_after_job_date').prop('required', true);
            }else{
                $('.job_date_box').hide();
                $('.status_date_box').show();
                $('#days_after_initial_status').prop('required', true);
                $('#days_before_after_job_date').prop('required', false);
            }
        });
        $('#sequence_type').trigger('change');
    });
</script>
@endpush