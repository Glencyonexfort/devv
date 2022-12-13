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
            @if(auth()->user()->hasRole('admin'))
            <li>
                <a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a>
            </li>
            @endif
            @if(auth()->user()->hasRole('driver'))
            <li>
                <a href="{{ route('driver.dashboard') }}">@lang('app.menu.home')</a>
            </li>
            @endif
            <li class="active">{{ $pageTitle }}</li>
        </ol>
    </div>
    <!-- /.breadcrumb -->
</div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/calculator/layout.css') }}" />
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
                            @if(auth()->user()->hasRole('admin'))
                            <li><a href="{{route('admin.list-jobs.edit-job', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.job_detail')</span></a>
                            </li>
                            <li><a href="{{route('admin.list-jobs.inventory', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.inventory')</span></a>
                            </li>
                            @if(isset($job->job_id))
                            <li><a href="{{route('admin.list-jobs.operations', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.operations')</span></a>
                            </li>
                            <li class="tab-current"><a href="#" style="text-align: center;"><span>@lang('modules.listJobs.invoice')</span></a></li>
                            <li><a href="{{route('admin.list-jobs.email', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.email')</span></a>
                            </li>
                            <li><a href="{{route('admin.list-jobs.attachment', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.attachments')</span></a></li>
                            <li><a href="{{route('admin.list-jobs.sms', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.sms')</span></a></li>
                            <li><a href="{{route('admin.list-jobs.insurance', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.insurance')</span></a></li>
                            @endif
                            @endif
                            @if(auth()->user()->hasRole('driver'))
                            <li>
                                <a 
                                    href="{{route('driver.list-jobs.edit-job', $job->job_id)}}" 
                                    style="text-align: center;">
                                    <span>@lang('modules.listJobs.job_detail')</span>
                                </a>
                            </li>
                            <li class="tab-current">
                                <a href="#" style="text-align: center;">
                                    <span>@lang('modules.listJobs.payment')</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </nav>
                </div>
                <div class="content-wrap hidden">
                    <section id="section-line-1" class="show">
                        {!! Form::open(['id'=>'generalForm','class'=>'ajax-form','method'=>'POST']) !!}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="white-box">
                                    <div class="simple_table">
                                        <div class="row">
                                            <div class="col-md-12 margin-bottom-20">
                                                <div class="btn_add_invoice">
                                                    @if(auth()->user()->hasRole('admin'))
                                                    <a href="{{route('admin.list-jobs.generate-invoice', $job->job_id)}}" class="btn btn-success" style="font-size: 16px;">@lang('modules.invoice.generate_invoice')</a>
                                                    @if($invoice)
                                                    @if($invoice->file_original_name && file_exists(public_path('invoice-files') . '/' . $invoice->file_original_name))
                                                    <a 
                                                        target="_blank" 
                                                        href="{{route('admin.list-jobs.view-invoice', $job->job_id)}}">
                                                        <img 
                                                            src="{{asset('img/icons/invoice_icon.png')}}" 
                                                            style="width: 36px;margin-left: 70px;">
                                                    </a>
                                                    @endif
                                                    <a 
                                                        style="margin-left: 10px;" 
                                                        href="{{route('admin.all-invoices.show', $invoice->id)}}"> Edit 
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    @endif
                                                    @endif
                                                    @if(auth()->user()->hasRole('driver'))
                                                    <a href="{{route('driver.list-jobs.generate-invoice', $job->job_id)}}" class="btn btn-success" style="font-size: 16px;">@lang('modules.invoice.generate_invoice')</a>
                                                    @if($invoice)
                                                    @if($invoice->file_original_name && file_exists(public_path('invoice-files') . '/' . $invoice->file_original_name))
                                                    <a 
                                                        target="_blank" 
                                                        href="{{route('driver.list-jobs.view-invoice', $job->job_id)}}">
                                                        <img 
                                                            src="{{asset('img/icons/invoice_icon.png')}}" 
                                                            style="width: 36px;margin-left: 70px;">
                                                    </a>
                                                    @endif
                                                    <a 
                                                        style="margin-left: 10px;" 
                                                        href="{{route('driver.all-invoices.show', $invoice->id)}}"> Edit 
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    @endif
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-12 margin-bottom-40">
                                                <div class="btn_add_inventory_list">
                                                    @if(auth()->user()->hasRole('admin'))
                                                    <a 
                                                        href="{{route('admin.list-jobs.generate-inventory-list', $job->job_id)}}" 
                                                        class="btn btn-success" 
                                                        style="font-size: 16px;">@lang('modules.invoice.generate_inventory_list')</a>
                                                    @if($invoice && $invoice->file && file_exists(public_path('invoice-files') . '/' . $invoice->file))
                                                    <a 
                                                        target="_blank" 
                                                        href="{{route('admin.list-jobs.view-inventory-list', $job->job_id)}}">
                                                        <img 
                                                            src="{{asset('img/icons/inventory_list.png')}}" 
                                                            style="width: 36px;margin-left: 30px;">
                                                    </a>
                                                    @endif
                                                    @endif
                                                    @if(auth()->user()->hasRole('driver'))
                                                    <a 
                                                        href="{{route('driver.list-jobs.generate-inventory-list', $job->job_id)}}" 
                                                        class="btn btn-success" 
                                                        style="font-size: 16px;">@lang('modules.invoice.generate_inventory_list')</a>
                                                    @if($invoice && $invoice->file && file_exists(public_path('invoice-files') . '/' . $invoice->file))
                                                    <a 
                                                        target="_blank" 
                                                        href="{{route('driver.list-jobs.view-inventory-list', $job->job_id)}}">
                                                        <img 
                                                            src="{{asset('img/icons/inventory_list.png')}}" 
                                                            style="width: 36px;margin-left: 30px;">
                                                    </a>
                                                    @endif
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-12 margin-bottom-20">
                                                <div class="btn_add_quote">
                                                    @if(auth()->user()->hasRole('admin'))
                                                    <a href="{{route('admin.list-jobs.generate-quote', $job->job_id)}}" class="btn btn-success" style="font-size: 16px;">@lang('modules.invoice.generate_quote')</a>
                                                    @if($job->quote_file_name && file_exists(public_path('invoice-files') . '/' . $invoice->quote_file_name))
                                                    <a target="_blank" href="{{route('admin.list-jobs.view-quote', $job->job_id)}}">
                                                        <img src="{{asset('img/icons/quote.png')}}" style="width: 36px;margin-left: 30px;">
                                                    </a>
                                                    @endif
                                                    @endif
                                                    @if(auth()->user()->hasRole('driver'))
                                                    <a href="{{route('driver.list-jobs.generate-quote', $job->job_id)}}" class="btn btn-success" style="font-size: 16px;">@lang('modules.invoice.generate_quote')</a>
                                                    @if($job->quote_file_name && file_exists(public_path('invoice-files') . '/' . $invoice->quote_file_name))
                                                    <a target="_blank" href="{{route('driver.list-jobs.view-quote', $job->job_id)}}">
                                                        <img src="{{asset('img/icons/quote.png')}}" style="width: 36px;margin-left: 30px;">
                                                    </a>
                                                    @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="tablee" width="100%" id="miscellaneous-table">
                                                        <thead>
                                                            <tr>
                                                                <th>@lang('modules.invoice.sr_no')</th>
                                                                <th>@lang('modules.invoice.job_id')</th>
                                                                <th>@lang('modules.invoice.item')</th>
                                                                <th>@lang('modules.invoice.description')</th>
                                                                <th class="text-center">@lang('modules.invoice.qty')</th>
                                                                <th class="text-center">@lang('modules.invoice.unit_price')</th>
                                                                <th class="text-center">@lang('modules.invoice.price')</th>
                                                            </tr>
                                                        <tbody>
                                                            @if($invoice_items)
                                                            <?php

                                                            if ($countInvItems && $countInvItems != '0') {
                                                                $trLoop = $countInvItems + 1;
                                                            } else {
                                                                $trLoop = 6;
                                                            }

                                                            /*for ($i = 1; $i < $trLoop; $i++) {
                                                                $createInvId = '9000' . $i*/
                                                                $i = 1;
                                        //@foreach ($invoice_items as $item)
                                                            ?>
                                                                <tr>
                                                                    <td>{{$i}}</td>
                                                                    <td>{{$job_id}}</td>
                                                                    <td>{{$invoice_items->item_name}}</td>
                                                                    <td>{{$invoice_items->item_summary}}</td>
                                                                    <td class="text-center">{{$invoice_items->quantity}}</td>
                                                                    <td class="text-center">{{$invoice_items->unit_price}}</td>
                                                                    <td class="text-center">{{$invoice_items->amount}}</td>
                                                                </tr>
                                                            <?php
                                                             ?>
                                                            @else
                                                                <tr><td colspan="7">No Record Found.</td></tr>
                                                            @endif
                                                        </tbody>
                                                        </thead>

                                                    </table>
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
                <div class="row dashboard-stats">
                    <div class="col-md-3 col-sm-6 hidden">
                        <a href="#">
                            <div class="white-box">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span class="bg-info-gradient bg-img">
                                                <img src="{{ asset('img/icons/dollar.png')}}" style="width:100%;">
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9 text-right">
                                        <span class="widget-title"> Total Amount</span><br>
                                        <span class="counter">{{$totalAmount ? $totalAmount : 0}}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 hidden">
                        <a href="#">
                            <div class="white-box">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span class="bg-success-gradient bg-img">
                                                <img src="{{ asset('img/icons/dollar.png')}}" style="width:100%;">
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9 text-right">
                                        <span class="widget-title"> Total Paid</span><br>
                                        <span class="counter">{{$paidAmount ? $paidAmount : 0}}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="#">
                            <div class="white-box">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div>
                                            <span class="bg-danger-gradient bg-img">
                                                <img src="{{ asset('img/icons/dollar.png')}}" style="width:100%;">
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-9 text-right">
                                        <span class="widget-title"> Total Due</span><br>
                                        <span class="counter">{{ $totalAmount-$paidAmount }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
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

</script>
@endpush