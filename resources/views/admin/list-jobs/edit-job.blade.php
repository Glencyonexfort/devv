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
                                <li class="tab-current"><a href="" style="text-align: center;"><span>@lang('modules.listJobs.job_detail')</span></a></li>
                                <li><a href="{{route('admin.list-jobs.inventory', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.inventory')</span></a></li>
                                @if(isset($job->job_id))
                                    <li><a href="{{route('admin.list-jobs.operations', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.operations')</span></a></li>
                                    <li><a href="{{route('admin.list-jobs.invoice', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.invoice')</span></a></li>
                                    <li><a href="{{route('admin.list-jobs.email', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.email')</span></a></li>
                                    <li><a href="{{route('admin.list-jobs.attachment', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.attachments')</span></a></li>
                                    <li><a href="{{route('admin.list-jobs.sms', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.sms')</span></a></li>
                                    <li><a href="{{route('admin.list-jobs.insurance', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.insurance')</span></a></li>
                                @endif
                                @endif
                                @if(auth()->user()->hasRole('driver'))
                                <li class="tab-current"><a href="" style="text-align: center;"><span>@lang('modules.listJobs.job_detail')</span></a></li>
                                @if(isset($job->job_id))
                                    <li>
                                        <a 
                                            href="{{route('driver.list-jobs.payment', $job->job_id)}}" 
                                            style="text-align: center;">
                                            <span>@lang('modules.listJobs.payment')</span>
                                        </a>
                                    </li>
                                @endif
                                @endif
                            </ul>
                        </nav>
                    </div>
                    <div class="content-wrap">
                        <section id="section-line-1" class="show">
                            {!! Form::open(['id'=>'generalForm','class'=>'ajax-form', 'method'=>'POST']) !!}
                            @if(auth()->user()->hasRole('admin'))
                            <input type="hidden" name="_method" value="PUT">
                            @endif
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="white-box">
                                        <div class="table-responsive">
                                            <div class="row{{auth()->user()->hasRole('driver') ? ' hidden' : ''}}">
                                                <div class="col-md-6">
                                                    <h5>@lang('modules.listJobs.total_cubic_meters')</h5>
                                                    <div class="form-group">
                                                        <input type="number" step="any" value="{{ $job->total_cbm }}" class="form-control" name="total_cbm" id="total_cbm" data-style="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h5>@lang('modules.listJobs.goods_value') <span class="required">*</span></h5>
                                                    <div class="form-group">
                                                        <input 
                                                            type="number" 
                                                            step="any" 
                                                            value="{{ $job->goods_value ?: 0 }}" 
                                                            class="form-control" 
                                                            name="goods_value" 
                                                            id="goods_value" 
                                                            data-style="form-control" 
                                                            required="">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row {{auth()->user()->hasRole('driver') ? ' hidden' : ''}}">
                                                <div class="col-md-6">
                                                    <h5>@lang('modules.listJobs.company') <span class="required">*</span></h5>
                                                    <div class="form-group">
                                                        <select class="select2 form-control" placeholder="@lang('app.select')" name="company_id" required="">
                                                            @foreach($companies as $company)
                                                                <option value="{{ $company->id }}" @if($company->id == $job->company_id)
                                                                selected=""
                                                                        @endif
                                                                >{{ ucwords($company->company_name) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h5>@lang('modules.listJobs.job_template') <span class="required">*</span></h5>
                                                    <div class="form-group">
                                                        <select class="select2 form-control" placeholder="@lang('app.select')" name="job_template" required="">
                                                            @foreach($job_templates as $job_template)
                                                                <option value="{{ $job_template->id }}" @if($job_template->id == $job->job_template_id)
                                                                selected=""
                                                                        @endif
                                                                >{{ ucwords($job_template->job_template_name) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row {{auth()->user()->hasRole('driver') ? ' hidden' : ''}}">
                                                <div class="col-md-6">
                                                    <h5>@lang('modules.listJobs.job_status')</h5>
                                                    <div class="form-group">
                                                        <select class="form-control selectpicker" name="job_status" id="job_status" data-style="form-control">
                                                            @foreach($job_status as $rs)
                                                                <option value="{{ $rs->options }}" @if($rs->options == $job->job_status)
                                                                selected=""
                                                                        @endif
                                                                >{{ ucwords($rs->options) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h5>@lang('modules.listJobs.job_type')</h5>
                                                    <div class="form-group">
                                                        <!--{{--<label class="control-label">@lang('modules.listJobs.job_type')</label>--}}-->
                                                        <select class="form-control selectpicker" name="job_type" id="job_type" data-style="form-control">
                                                            @foreach($job_type as $rs)
                                                                <option value="{{ $rs->options }}" @if($rs->options == $job->job_type)
                                                                selected=""
                                                                        @endif
                                                                >{{ ucwords($rs->options) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <h3 class="box-title b-b"><i class="fa fa-calendar"></i> @lang('modules.listJobs.when')</h3>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h5>@lang('modules.listJobs.what_date_you_like_to_move')</h5>
                                                    <div class="form-group">

                                                        @if(auth()->user()->hasRole('driver'))
                                                            {{ $job->job_date->format($global->date_format) }}
                                                        @else

                                                        <input 
                                                            class="form-control" 
                                                            name="job_date" 
                                                            id="job_date" 
                                                            value="{{ $job->job_date->format($global->date_format) }}" 
                                                            data-style="form-control">
                                                        @endif

                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="white-box">
                                        <h3 class="box-title b-b"><i class="fa fa-user"></i> @lang('modules.listJobs.customer')</h3>
                                        <div class="table-responsive">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    
                                                        @if(auth()->user()->hasRole('driver'))
                                                        <strong class="pull-left" style="margin-right: 10px;">
                                                            @lang('modules.listJobs.first_name'):
                                                        </strong>
                                                            <span class="pull-left" style="padding-bottom:20px;">{{ $customer->first_name }}</span>
                                                        @else
                                                        <h5>@lang('modules.listJobs.first_name')<span class="required">*</span></h5>
                                                        <div class="form-group">

                                                        <input class="form-control" value="{{ $customer->first_name }}" name="first_name" id="first_name" data-style="form-control">
                                                        </div>

                                                        @endif
                                                        
                                                    
                                                </div>
                                                <div class="col-md-6">

                                                        @if(auth()->user()->hasRole('driver'))
                                                        <strong class="pull-left" style="margin-right: 10px;">@lang('modules.listJobs.last_name'):</strong>
                                                            <span>{{ $customer->last_name }}</span>
                                                        @else
                                                        <h5>@lang('modules.listJobs.last_name')</h5>
                                                            <div class="form-group">
                                                                <input class="form-control" value="{{ $customer->last_name }}" name="last_name" id="last_name" data-style="form-control">
                                                            </div>
                                                        
                                                        @endif

                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    @if(auth()->user()->hasRole('driver'))
                                                        <strong class="pull-left" style="margin-right: 10px;">@lang('modules.listJobs.phone'):</strong>
                                                            <span class="pull-left" style="padding-bottom:20px;">{{ $customer->phone }}</span>
                                                    @else

                                                    <h5>@lang('modules.listJobs.phone')</h5>
                                                    <div class="form-group">
                                                        <input class="form-control" value="{{ $customer->phone }}" name="phone" id="phone" data-style="form-control">
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="col-md-6">
                                                     @if(auth()->user()->hasRole('driver'))
                                                        <strong class="pull-left" style="margin-right: 10px;">@lang('modules.listJobs.mobile'):</strong>
                                                            <span>{{ $customer->mobile }}</span>

                                                    @else
                                                    <h5>@lang('modules.listJobs.mobile')</h5>
                                                    <div class="form-group">
                                                        <input class="form-control" value="{{ $customer->mobile }}" name="mobile" id="mobile" data-style="form-control">
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="col-md-12{{auth()->user()->hasRole('driver') ? ' hidden' : ''}}">
                                                    <h5>@lang('modules.listJobs.email')</h5>
                                                    <div class="form-group">
                                                        <input class="form-control" value="{{ $customer->email }}" name="email" id="email" data-style="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="white-box">
                                        <div class="table-responsive">
                                            <h3 class="box-title b-b"><i class="fa fa-clock-o"></i> @lang('modules.listJobs.pickup_details')</h3>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    @if(auth()->user()->hasRole('driver'))
                                                    <h5 style="font-weight: 600;">@lang('modules.listJobs.address'):</h5>
                                                        <span class="pull-left" style="padding-bottom:15px;">{{ $job->pickup_address }}</span>
                                                    @else 
                                                    <h5>@lang('modules.listJobs.address')</h5>
                                                    <div class="form-group">
                                                        <input class="form-control" value="{{ $job->pickup_address }}" name="pickup_address" id="pickup_address" data-style="form-control">
                                                    </div>
                                                    @endif

                                                </div>
                                                <div class="col-md-12{{auth()->user()->hasRole('driver') ? ' hidden' : ''}}">
                                                    <h5>@lang('modules.listJobs.pickup_suburb') <span class="required">*</span></h5>
                                                    <div class="form-group">
                                                        <input 
                                                            class="form-control" 
                                                            value="{{ $job->pickup_suburb }}" 
                                                            name="pickup_suburb" 
                                                            id="pickup_suburb" 
                                                            data-style="form-control"
                                                            required="">
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    @if(auth()->user()->hasRole('driver'))
                                                    <h5 style="font-weight: 600;">@lang('modules.listJobs.pickup_property_type'):</h5>
                                                        <span class="pull-left" style="padding-bottom:15px;">{{ $job->pickup_property_type }}</span>
                                                    @else 

                                                    <h5>@lang('modules.listJobs.pickup_property_type')</h5>
                                                    <div class="form-group">
                                                        <input class="form-control" value="{{ $job->pickup_property_type }}" name="pickup_property_type" id="pickup_property_type" data-style="form-control">
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="col-md-12">
                                                    @if(auth()->user()->hasRole('driver'))
                                                    <h5 style="font-weight: 600;">@lang('modules.listJobs.number_of_bedrooms'):</h5>
                                                        <span class="pull-left" style="padding-bottom:15px;">{{ $job->pickup_bedrooms }}</span>
                                                    @else 

                                                    <h5>@lang('modules.listJobs.number_of_bedrooms')</h5>
                                                    <div class="form-group">
                                                        <input type="number" step="1" maxlength="2" value="{{ $job->pickup_bedrooms }}" class="form-control" name="pickup_bedrooms" id="pickup_bedrooms" data-style="form-control">
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="col-md-12">
                                                    @if(auth()->user()->hasRole('driver'))
                                                    <h5 style="font-weight: 600;">@lang('modules.listJobs.access_restrictions'):</h5>
                                                        <span class="pull-left" style="padding-bottom:15px;">{{ $job->pickup_access_restrictions }}</span>
                                                    @else 

                                                    <h5>@lang('modules.listJobs.access_restrictions')</h5>
                                                    <div class="form-group">
                                                        <textarea class="form-control" name="pickup_access_restrictions" id="pickup_access_restrictions" rows="4">{{ $job->pickup_access_restrictions }}</textarea>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="white-box">
                                        <div class="table-responsive">
                                            <h3 class="box-title b-b"><i class="fa fa-clock-o"></i> @lang('modules.listJobs.dropoff_details')</h3>
                                            <div class="row">
                                                <div class="col-md-12">

                                                    @if(auth()->user()->hasRole('driver'))
                                                    <h5 style="font-weight: 600;">@lang('modules.listJobs.address'):</h5>
                                                        <span class="pull-left" style="padding-bottom:15px;">{{ $job->drop_off_address }}</span>
                                                    @else 

                                                    <h5>@lang('modules.listJobs.address')</h5>
                                                    <div class="form-group">
                                                        <input class="form-control" value="{{ $job->drop_off_address }}" name="drop_off_address" id="drop_off_address" data-style="form-control">
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="col-md-12{{auth()->user()->hasRole('driver') ? ' hidden' : ''}}">
                                                    <h5>@lang('modules.listJobs.delivery_suburb') <span class="required">*</span></h5>
                                                    <div class="form-group">
                                                        <input 
                                                            class="form-control" 
                                                            value="{{ $job->delivery_suburb }}" 
                                                            name="delivery_suburb" 
                                                            id="delivery_suburb" 
                                                            data-style="form-control"
                                                            required="">
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    @if(auth()->user()->hasRole('driver'))
                                                    <h5 style="font-weight: 600;">@lang('modules.listJobs.dropoff_property_type'):</h5>
                                                        <span class="pull-left" style="padding-bottom:15px;">{{ $job->drop_off_property_type }}</span>
                                                    @else 

                                                    <h5>@lang('modules.listJobs.dropoff_property_type')</h5>
                                                    <div class="form-group">
                                                        <input class="form-control" value="{{ $job->drop_off_property_type }}" name="drop_off_property_type" id="drop_off_property_type" data-style="form-control">
                                                    </div>
                                                     @endif
                                                </div>
                                                <div class="col-md-12">
                                                    @if(auth()->user()->hasRole('driver'))
                                                    <h5 style="font-weight: 600;">@lang('modules.listJobs.number_of_bedrooms'):</h5>
                                                        <span class="pull-left" style="padding-bottom:15px;">{{ $job->drop_off_bedrooms }}</span>
                                                    @else 

                                                    <h5>@lang('modules.listJobs.number_of_bedrooms')</h5>
                                                    <div class="form-group">
                                                        <input type="number" step="1" maxlength="2" value="{{ $job->drop_off_bedrooms }}" class="form-control" name="drop_off_bedrooms" id="drop_off_bedrooms" data-style="form-control">
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="col-md-12">

                                                    @if(auth()->user()->hasRole('driver'))
                                                    <h5 style="font-weight: 600;">@lang('modules.listJobs.access_restrictions'):</h5>
                                                        <span class="pull-left" style="padding-bottom:15px;">{{ $job->drop_off_access_restrictions }}</span>
                                                    @else 

                                                    <h5>@lang('modules.listJobs.access_restrictions')</h5>
                                                    <div class="form-group">
                                                        <textarea class="form-control" name="drop_off_access_restrictions" id="drop_off_access_restrictions" rows="4">{{ $job->drop_off_access_restrictions }}</textarea>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row{{auth()->user()->hasRole('driver') ? ' hidden' : ''}}">
                                <div class="col-md-6">
                                    <div class="white-box">
                                        <div class="table-responsive">
                                            <h3 class="box-title b-b"><i class="fa fa-money"></i> @lang('modules.listJobs.payment')</h3>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h5>@lang('modules.listJobs.price_structure')</h5>
                                                            <div class="form-group">
                                                                <select class="form-control selectpicker" name="price_structure" id="price_structure" data-style="form-control">
                                                                    @foreach($price_structure as $rs)
                                                                        <option value="{{ $rs->options }}" @if($rs->options == $job->price_structure)
                                                                        selected=""
                                                                                @endif
                                                                        >{{ ucwords($rs->options) }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h5>@lang('modules.listJobs.fixed_other_rate')</h5>
                                                            <div class="form-group">
                                                                <input class="form-control" value="{{ $job->fixed_other_rate }}" name="fixed_other_rate" id="fixed_other_rate" data-style="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h5>@lang('modules.listJobs.hourly_rate')</h5>
                                                            <div class="form-group">
                                                                <input type="number" class="form-control" value="{{ $job->hourly_rate }}" name="hourly_rate" id="hourly_rate" data-style="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h5>@lang('modules.listJobs.total_amount')</h5>
                                                            <div class="form-group">
                                                                <input type="number" class="form-control" value="{{ $job->total_amount }}" name="total_amount" id="total_amount" data-style="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h5>@lang('modules.listJobs.deposit_required')</h5>
                                                            <div class="form-group">
                                                                <input type="number" class="form-control" value="{{ $job->deposit_agreed }}" name="deposit_required" id="deposit_required" data-style="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <h5>@lang('modules.listJobs.payment_instructions')</h5>
                                                            <div class="form-group">
                                                                <textarea class="form-control" name="payment_instructions" id="payment_instructions" rows="4">{{ $job->payment_instructions }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="white-box">
                                        <div class="table-responsive">
                                            <h3 class="box-title b-b">&nbsp;</h3>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h5>@lang('modules.listJobs.payment_status')</h5>
                                                    <div class="form-group">
                                                        <select disabled="" class="form-control selectpicker" name="payment_status" id="payment_status" data-style="form-control">
                                                            @foreach($payment_status as $rs)
                                                                <option value="{{ $rs->options }}">{{ ucwords($rs->options) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h5>@lang('modules.listJobs.lead_info')</h5>
                                                    <div class="form-group">
                                                        <select class="form-control selectpicker" name="lead_info" id="lead_info" data-style="form-control">
                                                            @foreach($lead_info as $rs)
                                                                <option value="{{ $rs->options }}" @if($rs->options == $job->lead_info)
                                                                selected=""
                                                                        @endif
                                                                >{{ ucwords($rs->options) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <h3 class="box-title b-b"><i class="fa fa-bus"></i> @lang('modules.listJobs.vehicle')</h3>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h5>@lang('modules.listJobs.vehicle')</h5>
                                                    <div class="form-group">
                                                        <select class="form-control selectpicker" name="vehicle_id" id="vehicle_id" data-style="form-control">
                                                            @foreach($vehicles as $rs)
                                                                <option value="{{ $rs->id }}" @if($rs->id == $job->vehicle_id)
                                                                selected=""
                                                                        @endif
                                                                >{{ ucwords($rs->vehicle_name) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <h3 class="box-title b-b">&nbsp;</h3>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h5>@lang('modules.listJobs.other_details')</h5>
                                                    <div class="form-group">
                                                        <textarea class="form-control" name="other_instructions" id="other_instructions" rows="4">{{ $job->other_instructions }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="white-box">
                                        <div class="table-responsive">
                                            <div class="form-actions">
                                                <button 
                                                    type="submit" 
                                                    id="save-form" 
                                                    class="btn btn-success{{auth()->user()->hasRole('driver') ? ' hidden' : ''}}">
                                                    <i class="fa fa-check"></i>
                                                    @lang('modules.listJobs.save_booking')
                                                </button>
                                                 <a 
                                                    href="{{route('admin.list-jobs.index')}}" 
                                                   class="btn btn-default{{auth()->user()->hasRole('driver') ? ' hidden' : ''}}">@lang('app.cancel')</a> 

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
    <script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    @if($global->locale == 'en')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.{{ $global->locale }}-AU.min.js"></script>
    @elseif($global->locale == 'pt-br')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.pt-BR.min.js"></script>
    @else
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.{{ $global->locale }}.min.js"></script>
    @endif
    <script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script>
        $(function() {
            $('#job_date').datepicker({
                toggleActive: true,
                format: '{{ $global->date_picker_format }}',
                language: '{{ $global->locale }}',
                autoclose: true
            });
            $('#save-form').click(function() {
                $.easyAjax({
                    @if(auth()->user()->hasRole('admin'))
                    url: "{{route('admin.list-jobs.update', [$job->job_id])}}",
                    @endif
                    @if(auth()->user()->hasRole('driver'))
                    url: "{{route('driver.list-jobs.update', [$job->job_id])}}",
                    @endif
                    container: '#generalForm',
                    type: "POST",
                    redirect: true,
                    data: $('#generalForm').serialize()
                });
            });
            $('#change-status').click(function() {
                var id = $(this).data('id');
                $.easyAjax({
                    @if(auth()->user()->hasRole('admin'))
                    url: "{{route('admin.list-jobs.update', [$job->job_id])}}",
                    @endif
                    @if(auth()->user()->hasRole('driver'))
                    url: "{{url('driver/moving/list-jobs/change-status')}}/"+id,
                    @endif
                    container: '#generalForm',
                    type: "POST",
                    redirect: true,
                    data: $('#generalForm').serialize()
                });
            });
        });
    </script>
@endpush