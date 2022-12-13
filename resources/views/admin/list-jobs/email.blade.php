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
                    <h3 style="color:#fb9678">Job# {{$job->job_number}}</h3>
                    <nav>
                        <ul>
                            <li><a href="{{route('admin.list-jobs.edit-job', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.job_detail')</span></a></li>
                            <li><a href="{{route('admin.list-jobs.inventory', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.inventory')</span></a></li>
                            @if(isset($job->job_id))
                            <li><a href="{{route('admin.list-jobs.operations', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.operations')</span></a></li>
                            <li><a href="{{route('admin.list-jobs.invoice', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.invoice')</span></a></li>
                            <li class="tab-current"><a href="{{route('admin.list-jobs.email', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.email')</span></a></li>
                            <li><a href="{{route('admin.list-jobs.attachment', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.attachments')</span></a></li>
                            <li><a href="{{route('admin.list-jobs.sms', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.sms')</span></a></li>
                            <li><a href="{{route('admin.list-jobs.insurance', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.insurance')</span></a></li>
                            @endif
                        </ul>
                    </nav>
                </div>
                <div class="content-wrap">
                    <section id="section-line-1" class="show">
                        {!! Form::open(['id'=>'generalForm','method'=>'POST','url' => 'admin/moving/email/'.$job->job_id]) !!}
                        <input type="hidden" name="job_id" value="{{$job->job_id}}">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="white-box">
                                    <div class="simple_table">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-info">
                                                        <input type="checkbox" id="email_customer" name="email_customer" value="1">
                                                        <label for="default1">@lang('modules.email.email_customer')</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <h5>@lang('modules.email.to')</h5>
                                                    <input type="text" required class="form-control" name="to" id="to" data-style="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <h5>@lang('modules.email.subject')</h5>
                                                    <input type="text" required class="form-control" name="email_subject" id="email_subject" data-style="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <h5>@lang('modules.email.cc')</h5>
                                                    <input type="text" class="form-control" name="cc" id="cc" data-style="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <h5>@lang('modules.email.bcc')</h5>
                                                    <input type="text" class="form-control" name="bcc" id="bcc" data-style="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <textarea name="email_body" id="email_body" class="summernote"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <button type="submit" id="save-form" class="btn btn-success"><i class="fa fa-send"></i> @lang('modules.email.send')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="white-box">
                                    <div class="simple_table">
                                        <div class="row">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label>@lang('modules.email.email_templates')</label>
                                                    <select class="select2 form-control" data-placeholder="Choose Client" name="email_template" id="email_template">
                                                        <option value="" disabled selected> - Select - </option>
                                                        @foreach($email_templates as $template)
                                                            <option value="{{ $template->id }}">{{ ucwords($template->email_template_name) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="white-box">
                                    <div class="simple_table">
                                        <div class="row">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <div class="pull-left" style="width:100%">
                                                    <label>@lang('modules.email.email_attachments')</label>
                                                    <span class="pull-right"><input style="width: 25px;margin-top: 0px;height: 20px;margin-left: 6px;" id="attachments_select_all" type="checkbox"></span>
                                                        <span class="pull-right">Select All</span>
                                                    </div>  

                                                    <div id="attachments" style="border: 1px solid gray;overflow-y: scroll; height:430px;">
                                                        <ul id='attachmentsul' style='list-style: none;margin-left: 10px;padding: 5px;'>
    @if($invoice && $invoice->file_original_name && file_exists(public_path('invoice-files') . '/' . $invoice->file_original_name))

    <li raggable='true' class='invoice-li'  style='margin-bottom: 15px;'>
        <a target='_blank' href="{{route('admin.list-jobs.view-invoice', $job->job_id)}}">
            <input 
                class='attachment-checkbox' 
                type='checkbox' 
                name='files[]' 
                value="{{public_path('invoice-files') . '/' . $invoice->file_original_name}}"  />
            <img 
                src="{{asset('img/icons/invoice_icon.png')}}" 
                class="attachment-img">{{$invoice->file_original_name}}
        </a>
    </li>
    @endif

    @if($invoice && $invoice->file && file_exists(public_path('invoice-files') . '/' . $invoice->file))

    <li raggable='true' class='invoice-li'  style='margin-bottom: 15px;'><a target='_blank' href="{{route('admin.list-jobs.view-inventory-list', $job->job_id)}}"><input class='attachment-checkbox' type='checkbox' name='files[]' value="{{public_path('invoice-files') . '/' . $invoice->file}}"  /><img src="{{asset('img/icons/inventory_list.png')}}" class="attachment-img">{{$invoice->file}}</a></li>
    @endif

    @if($invoice && $invoice->quote_file_name && file_exists(public_path('invoice-files') . '/' . $invoice->quote_file_name))

    <li raggable='true' class='invoice-li'  style='margin-bottom: 15px;'><a target='_blank' href="{{route('admin.list-jobs.view-quote', $job->job_id)}}"><input class='attachment-checkbox' type='checkbox' name='files[]' value="{{public_path('invoice-files') . '/' . $job->quote_file_name}}"  /><img src="{{asset('img/icons/quote.png')}}" class="attachment-img">{{$job->quote_file_name}}</a></li>
    @endif

    @if($attachments)
        @foreach($attachments as $attachment)
        <?php 
        $destinationPath = public_path('/user-uploads/tenants/'.auth()->user()->tenant_id);
        $checkFileExists = $destinationPath . '/' . $attachment->log_details;
        if(file_exists($checkFileExists)){ ?>
        <li raggable='true' class='invoice-li'  style='margin-bottom: 15px;'>
            <a target='_blank' href="{{route('admin.list-jobs.view-attachment', $attachment->id)}}">
                <input class='attachment-checkbox' type='checkbox' name='files[]' 
                       value="{{$checkFileExists}}"  />
                <img src="{{asset('img/icons/attach.png')}}" 
                     class="attachment-img">{{$attachment->log_details}}
            </a>
        </li>
    <?php } ?>
        @endforeach
    @endif

    @if($job_template_attachments)
        @foreach($job_template_attachments as $attachment)
        <?php 
        $destinationPath = public_path('/job-template');
        $checkFileExists = $destinationPath . '/' . $attachment->attachment_file_name;
        if(file_exists($checkFileExists)){ ?>
        <li raggable='true' class='invoice-li'  style='margin-bottom: 15px;'>
            <a target='_blank' href="{{route('admin.list-jobs.view-job-template-attachment', $attachment->id)}}">
                <input class='attachment-checkbox' type='checkbox' name='files[]' 
                       value="{{$checkFileExists}}"  />
                <img src="{{asset('img/icons/attach.png')}}" 
                     class="attachment-img">{{$attachment->attachment_file_name}}
            </a>
        </li>
    <?php } ?>
        @endforeach
    @endif
                                                        </ul>
                                                           
                                                    </div>                                                 
                                                </div>
                                            </div>
                                        </div>
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
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script type="text/javascript">
    var customer_email = '{{$crm_contact_email}}';
    $('.summernote').summernote({
        height: 200, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
        focus: false // set focus to editable area after initializing summernote
    });
    $(document).on('change', '#email_customer', function() {
        var $this = $(this);
        if ($this.is(":checked") == true) {
            $('#to').val(customer_email);
        } else {
            $('#to').val('');
        }
    });

    $(document).on('click', '#attachments_select_all', function() {

      if ($(this).is(':checked')) {
          
          $('#attachmentsul').find(':checkbox').each(function()
          {
             $(this).prop('checked', true);
          }); 
      } else {

          $('#attachmentsul').find(':checkbox').each(function()
          {
             $(this).prop('checked', false);
          }); 

      }


    });

    $(document).on('change', '#email_template', function() {
        var $this = $(this);
        var template_id = $this.val();
        var url = "{{ route('admin.list-jobs.get-email-template', ':id') }}";
        url = url.replace(':id', template_id);
                        var token = "{{ csrf_token() }}";
        var job_id = "{{$job->job_id}}";
        console.log(job_id);
        $.easyAjax({
            type: 'POST',
            url: url,
            data: {
                '_token': token,
                'id': template_id,
                'job_id' : job_id
            },
            success: function(response) {
                console.log(response);
                if (response.status == "success") {
                    $('#email_subject').val(response.subject);
                    // $('#email_body').val(response.body);
                    console.log(response.body);
                    $(".summernote").summernote('code', response.body);
                }
            }
        });
    });
</script>
@endpush