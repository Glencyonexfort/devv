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


@section('content')
<div class="row">
    <div class="col-md-12">
        <section>
            <div class="sttabs tabs-style-line">
                <div class="white-box">
                    <h3 style="color:#fb9678">Job# {{$job->job_number}}</h3>
                    <nav>
                        <ul>
                            <li><a href="{{route('admin.list-jobs.edit-job', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.job_detail')</span></a>
                            </li>
                            <li><a href="{{route('admin.list-jobs.inventory', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.inventory')</span></a>
                            </li>
                            @if(isset($job->job_id))
                            <li><a href="{{route('admin.list-jobs.operations', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.operations')</span></a>
                            </li>
                            <li><a href="{{route('admin.list-jobs.invoice', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.invoice')</span></a></li>
                            <li><a href="{{route('admin.list-jobs.email', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.email')</span></a>
                            </li>
                                <li><a href="{{route('admin.list-jobs.attachment', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.attachments')</span></a></li>
                                <li><a href="{{route('admin.list-jobs.sms', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.sms')</span></a></li>
                                <li class="tab-current"><a href="#" style="text-align: center;"><span>@lang('modules.listJobs.insurance')</span></a></li>
                            @endif
                        </ul>
                    </nav>
                </div>
                <div class="content-wrap">
                    <section id="section-line-1" class="show">
                        {!! Form::open(['id'=>'generalForm','class'=>'ajax-form','method'=>'POST']) !!}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="white-box">

                                    <?php if($tenant_details && $tenant_details->insurance_tab_enabled == 'Y'){

                                            if(!$request_id){
                                                $class = 'col-md-6';
                                            } else {
                                                $class = 'col-md-12';
                                            } 
                                        } else {
                                            $class = 'col-md-12';
                                        }
                                    ?>
                                    
                                    <div class="simple_table">
                                        
                                        <div class="row">

                                            <div class="<?php echo $class; ?> margin-bottom-20">
                                                <div class="btn_add_invoice">
                                                    <img src="{{asset('img/coverfreight-logo.png')}}" style="margin-left: 9px;">          
                                                </div>
                                            </div>

                                            @if($tenant_details && $tenant_details->insurance_tab_enabled == 'Y')

                                            @if(!$request_id)
                                            <div class="col-md-6 margin-bottom-20">
                                                <div class="btn_add_invoice">
                                                    <a href="{{route('admin.list-jobs.send-quote-to-customer', $job->job_id)}}" class="btn btn-success pull-right" style="font-size: 16px;margin-right: 10px;">@lang('modules.insurance.send_quote_to_customer')</a>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table-responsive col-md-5">
                                                    <table class="tablee pull-left" style="margin-right:20px;" width="100%" id="miscellaneous-table">
                                                        <thead>
                                                            <tr>
                                                                <th width="35%"></th>
                                                                <th width="65%"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td><strong>@lang('modules.insurance.name')</strong></td>
                                                                <td>{{ $lead_name }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>@lang('modules.insurance.email')</strong></td>
                                                                <td>{{ $crm_contact_email }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>@lang('modules.insurance.pickup_suburb')</strong></td>
                                                                <td>{{ $job->pickup_suburb }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>@lang('modules.insurance.delivery_suburb')</strong></td>
                                                                <td>{{ $job->delivery_suburb }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>@lang('modules.insurance.value_of_goods')</strong></td>
                                                                <td>{{ $job->goods_value }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>@lang('modules.insurance.comodity')</strong></td>
                                                                <td>Household goods and personal effects</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>@lang('modules.insurance.conveyance')</strong></td>
                                                                <td>Enclosed Truck / Container.</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>@lang('modules.insurance.reference')</strong></td>
                                                                <td>{{'T-'.auth()->user()->tenant_id.'-J-'.$job->job_number }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="table-responsive col-md-5 pull-right">
                                                    <table class="tablee pull-left" width="100%" id="miscellaneous-table">
                                                        <thead>
                                                            <tr>
                                                                <th width="50%"></th>
                                                                <th width="50%"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td><strong>@lang('modules.insurance.reference')</strong></td>
                                                                <td>
                                                                    @if($insurance_response != '' && $insurance_response->reference)
                                                                    {{$insurance_response->reference}}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>@lang('modules.insurance.premium')</strong></td>
                                                                <td>
                                                                    @if($insurance_response != '' && $insurance_response->premium)
                                                                    $ {{$insurance_response->premium}}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>@lang('modules.insurance.gst')</strong></td>
                                                                <td>
                                                                    @if($insurance_response != '' && $insurance_response->gst)
                                                                    $ {{$insurance_response->gst}}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <!-- <tr>
                                                                <td><strong>@lang('modules.insurance.fee')</strong></td>
                                                                <td>
                                                                    @if($insurance_response != '' && $insurance_response->fee)
                                                                    {{$insurance_response->fee}}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>@lang('modules.insurance.fee_gst')</strong></td>
                                                                <td>
                                                                    @if($insurance_response != '' && $insurance_response->fee_gst)
                                                                    {{$insurance_response->fee_gst}}
                                                                    @endif
                                                                </td>
                                                            </tr> -->
                                                            <tr>
                                                                <td><strong>@lang('modules.insurance.quote')</strong></td>
                                                                <td>
                                                                    @if($insurance_response != '' && $insurance_response->insurance_quote_id)
                                                                    {{$insurance_response->insurance_quote_id}}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>@lang('modules.insurance.comment')</strong></td>
                                                                <td>
                                                                    @if($insurance_response != '' && $insurance_response->comment)
                                                                    {{$insurance_response->comment}}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <h4 style="color:#ed4040;">To enable this tab, please contact Coverfreight on info@coverfreight.com.au or </h4>
                                    <h4 style="color:#ed4040;">07 3613 7901 or contact Onexfort support at support@onexfort.com</h4>
                                    @endif
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