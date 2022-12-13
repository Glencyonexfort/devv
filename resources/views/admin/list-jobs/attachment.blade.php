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
<style>
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
    .files input:focus{     outline: 2px dashed #92b0b3;  outline-offset: -10px;
        -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
        transition: outline-offset .15s ease-in-out, background-color .15s linear; border:1px solid #92b0b3;
    }
    .files{ position:relative}
    .files:after {  pointer-events: none;
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
    .color input{ background-color:#f1f1f1;}
    .files:before {
        position: absolute;
        bottom: 10px;
        left: 0;  pointer-events: none;
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
                            <li><a href="{{route('admin.list-jobs.email', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.email')</span></a></li>
                            <li class="tab-current"><a href="{{route('admin.list-jobs.attachment', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.attachments')</span></a>
                            </li>
                            <li><a href="{{route('admin.list-jobs.sms', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.sms')</span></a></li>
                            <li><a href="{{route('admin.list-jobs.insurance', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.insurance')</span></a>
                            </li>
                            @endif
                        </ul>
                    </nav>
                </div>
                <div class="content-wrap">
                    <section id="section-line-1" class="show">                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="white-box">
                                    <div class="simple_table">
                                        {!! Form::open(['id'=>'generalForm','method'=>'POST', 'enctype'=>'multipart/form-data','url' => 'admin/moving/attachment/'.$job->job_id, 'file' => true]) !!}
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group files">
                                                    <label>Upload Your File </label>
                                                    <input type="file" class="form-control" name="attachment">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <button type="submit" id="save-form" class="btn btn-success"><i class="fa fa-upload"></i> Upload</button>
                                        </div>
                                        {!! Form::close() !!}

                                    @if($attachments)
                                        <div class="row" style="margin-top: 20px;">
                                            <div class="col-md-12">
                                                <h3>Attachments</h3>
                                                
                                        <ul>
                                        @foreach($attachments as $attachment)
                                            <?php 
                                            $destinationPath = public_path('/user-uploads/tenants/'.auth()->user()->tenant_id);
                                            $checkFileExists = $destinationPath . '/' . $attachment->log_details;
                                            if(file_exists($checkFileExists)){ ?>
                                            <li raggable='true' class='invoice-li'  style='margin-bottom: 15px;'>
                                                <a target='_blank' href="{{route('admin.list-jobs.view-attachment', $attachment->id)}}">
                                                    
                                                    <img src="{{asset('img/icons/attach.png')}}" 
                                                         class="attachment-img">{{$attachment->log_details}}
                                                </a>
                                            </li>
                                            <?php } ?>
                                        @endforeach
                                        </ul>
                       

                                            </div>
                                        </div>
                                         @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </section>

                    <section id="section-line-2" class="show">
                        
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
    var customer_email = '{{$job->customer->email}}';
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

    $(document).on('change', '#email_template', function() {
        var $this = $(this);
        var template_id = $this.val();
        var url = "{{ route('admin.list-jobs.get-email-template', ':id') }}";
        url = url.replace(':id', template_id);
                        var token = "{{ csrf_token() }}";

        $.easyAjax({
            type: 'POST',
            url: url,
            data: {
                '_token': token,
                'id': template_id
            },
            success: function(response) {
                if (response.status == "success") {
                    $('#email_subject').val(response.subject);
                    // $('#email_body').val(response.body);
                    $(".summernote").summernote('code', response.body);
                }
            }
        });
    });
</script>
@endpush