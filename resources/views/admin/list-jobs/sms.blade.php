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
            <li class="active">{{ $pageTitle }}</li>
        </ol>
    </div>
    <!-- /.breadcrumb -->
</div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/calculator/layout.css') }}" />
<style type="text/css">
    .attachment-checkbox{
    width: 30px;
    height: 16px;
    margin-top: 14px;
    }
    .attachment-img {
    width: 24px;
    margin-top: -6px;
    margin-right: 3px;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <section>
            <div class="sttabs tabs-style-line">
                <div class="white-box">
@if (session('success'))
  <div class="alert alert-success alert-dismissable custom-success-box" style="margin: 15px;">
     <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
     <strong> {{ session('success') }} </strong>
  </div>
@endif
@if (session('error'))
  <div class="alert alert-danger alert-dismissable custom-success-box" style="margin: 15px;">
     <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
     <strong> {{ session('error') }} </strong>
  </div>
@endif

                    <h3 style="color:#fb9678">Job# {{$job->job_number}}</h3>
                    <nav>
                        <ul>
                            <li><a href="{{route('admin.list-jobs.edit-job', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.job_detail')</span></a></li>
                            <li><a href="{{route('admin.list-jobs.inventory', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.inventory')</span></a></li>
                            @if(isset($job->job_id))
                            <li><a href="{{route('admin.list-jobs.operations', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.operations')</span></a></li>
                            <li><a href="{{route('admin.list-jobs.invoice', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.invoice')</span></a></li>
                            <li><a href="{{route('admin.list-jobs.email', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.email')</span></a></li>
                            <li><a href="{{route('admin.list-jobs.attachment', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.attachments')</span></a></li>
                            <li class="tab-current"><a href="{{route('admin.list-jobs.sms', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.sms')</span></a></li>
                            <li><a href="{{route('admin.list-jobs.insurance', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.insurance')</span></a></li>
                            @endif
                        </ul>
                    </nav>
                </div>
                <div class="content-wrap">
                    
                    <section id="section-line-1" class="show">
                        {!! Form::open(['id'=>'generalForm','method'=>'POST','url' => 'admin/moving/sms/'.$job->job_id]) !!}
                        <input type="hidden" name="job_id" value="{{$job->job_id}}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="white-box">
                                    <span class="pull-left"><strong style="font-size: 16px;">Available Credits:  {{ $tenant_details->sms_credit }}</strong></span>
                                    <span class="pull-right"><a href="{{ route('admin.sms-credits.index') }}" class="btn btn-success">Buy Credits</a></span>

                                    @if($tenant_details->sms_credit > 0)
                                    <div class="simple_table">
                                        <div class="row">
                                            <div class="col-md-12" style="margin-top:12px;">
                                                <div class="form-group">
                                                    <div class="">              
                                                        <label for="default1" style="margin-right: 15px;" >@lang('modules.sms.sms_from')</label>
                                                        <input type="radio" class="sms_from_radio" name="sms_customer_from" value="{{ $companies->sms_number }}" checked>
                                                        <span style="margin-right: 15px;">{{ $companies->sms_number }}</span>

                                                        <input type="radio" class="sms_from_radio" name="sms_customer_from" value="{{ $companies->company_name }}">
                                                        {{ $companies->company_name }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info">
                                                        <input type="checkbox" id="sms_customer" name="sms_customer" value="1">
                                                        <label for="default1">@lang('modules.sms.sms_customer')</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <h5>@lang('modules.sms.sms_template')</h5>
                                                    <select class="form-control" name="sms_template_id" id="sms_template_id">
                                                        <option  value="" disabled selected>@lang('modules.sms.select_sms_template')</option>
                                                    <?php foreach ($sms_templates as $value) { ?>
                                                        <option value="<?php echo $value->id; ?>"><?php echo $value->sms_template_name; ?></option>
                                                    <?php } ?>
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                        </div>   

                                        <div class="row">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <h5>@lang('modules.sms.sms_from')</h5>
                                                    <input type="text" required name="sms_from" id="sms_from" class="form-control" maxlength="11" value="{{ $companies->sms_number }}" placeholder="From number/name here...">
                                                    <note>Max 11 characters are allowed</note>
                                                </div>
                                            </div>
                                        </div>   

                                        <div class="row">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <h5>@lang('modules.sms.sms_number')</h5>
                                                    <input type="text" required name="sms_number" id="sms_number" class="form-control" placeholder="Enter number here...">
                                                </div>
                                            </div>
                                        </div>                                        
                                        
                                        <div class="row">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <h5>@lang('modules.smsTemplates.sms_message')
                                                        <note class="pull-right" style="font-size:13px"> (160 characters will cost 1 credit)</note></h5>

                                                    <textarea  name="sms_message" id="sms_message" rows="5" class="form-control" onkeyup="countChar(this)" placeholder="Type your sms here..." autocomplete="nope"></textarea>
                                                    <note class='pull-left'>Cost = <span id='costspan'>1</span> credit</note>
                                                    <note class='pull-right'><span id='totalWords'>0</span> characters</note>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="total_credits" id="total_credits" value="">
                                        <div style="margin-top: 12px;">
                                            <button type="submit" id="save-form" class="btn btn-success"><i class="fa fa-send"></i> @lang('modules.email.send')</button>
                                        </div>
                                        @else
                                    <div class="row">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                   Please buy credit to use SMS feature. <a href="{{ route('admin.sms-credits.index') }}" >Click here</a> to buy SMS credits.
                                                </div>
                                            </div>
                                        </div>       
                                @endif

                                    </div>

                                    

                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </section>                 


                </div>
            </div>
        </section>
    </div>
</div>
<!-- .row -->
@endsection

@push('footer-script')
<script src="{{ asset('bootstrap/jquery.bootstrap-touchspin.js')}}"></script>
<script type="text/javascript">
    function countChar(val) {
        var len = $('#sms_message').val().length;//val.value.length;
        var credits = 0;
        if(len>0){
             credits = Math.ceil(len/160);
        }
        $('#costspan').text(credits);
        $('#totalWords').text(len);
        $('#total_credits').val(credits);
        
      };

    var customer_phone = '{{$crm_contact_phone}}';
    $(document).on('change', '#sms_customer', function() {
        var $this = $(this);
        if ($this.is(":checked") == true) {
            $('#sms_number').val(customer_phone);
        } else {
            $('#sms_number').val('');
        }
    });

    $(document).on('click', '.sms_from_radio', function() {
        var radioValue = $("input[name='sms_customer_from']:checked").val();
        
        if(radioValue){
            var subStr = radioValue.substring(0, 11);
            $('#sms_from').val(subStr);
        }
    });

    $(document).on('change', '#sms_template_id', function() {
        var $this = $(this);
        var template_id = $this.val();
        var url = "{{ route('admin.list-jobs.get-sms-template', ':id') }}";
        url = url.replace(':id', template_id);
                        var token = "{{ csrf_token() }}";
        var job_id = "{{$job->job_id}}";
        //console.log(template_id);
        $.easyAjax({
            type: 'POST',
            url: url,
            data: {
                '_token': token,
                'id': template_id,
                //'job_id' : job_id
            },
            success: function(response) {
                console.log(response);
                if (response.status == "success") {
                    $('#sms_message').val(response.sms_message);
                    countChar();
                    //console.log(response.body);
                }
            }
        });
    });
</script>
@endpush