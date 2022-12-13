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
                                <li><a href="{{route('admin.list-jobs.edit-job', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.job_detail')</span></a></li>
                                <li><a href="{{route('admin.list-jobs.inventory', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.inventory')</span></a></li>
                                <li class="tab-current"><a href="#" style="text-align: center;"><span>@lang('modules.listJobs.operations')</span></a></li>
                                <li><a href="{{route('admin.list-jobs.invoice', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.invoice')</span></a></li>
                                <li><a href="{{route('admin.list-jobs.email', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.email')</span></a></li>
                                <li><a href="{{route('admin.list-jobs.attachment', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.attachments')</span></a></li>
                                <li><a href="{{route('admin.list-jobs.sms', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.sms')</span></a></li>
                                <li><a href="{{route('admin.list-jobs.insurance', $job->job_id)}}" style="text-align: center;"><span>@lang('modules.listJobs.insurance')</span></a></li>
                            </ul>
                        </nav>
                    </div>
                    <div class="white-box">
                        {!! Form::open(['id'=>'generalForm','class'=>'ajax-form','method'=>'POST']) !!}
                        <div class="row">
                            <div class="col-md-4">
                                <h5 class="text-right">@lang('modules.operations.no_of_legs')</h5>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="number" disabled="" step="any" value="{{ $job_total_legs }}" class="form-control" name="total_cbm" id="total_cbm" data-style="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div><a href="{{route('admin.list-jobs.operations-add-leg', $job->job_id)}}" class="btn btn-success"><i class="ti-plus"></i> @lang('modules.operations.add_new_leg')</a></div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover toggle-circle default footable-loaded footable" id="listing-table">
                                <thead>
                                <tr>
                                    <th>&nbsp;</th>
                                    <th>@lang('modules.operations.leg_no')</th>
                                    <th>@lang('modules.operations.date')</th>
                                    <th>@lang('modules.operations.address')</th>
                                    <th>@lang('modules.operations.driver')</th>
                                    <th>@lang('modules.operations.vehicle')</th>
                                    <th>@lang('modules.operations.job_type')</th>
                                    <th>@lang('modules.operations.leg_status')</th>
                                    <th>@lang('modules.operations.dispatch_notes')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(count($job_legs) > 0)
                                    @foreach($job_legs as $jleg)
                                        <tr>
                                            <td><a href="{{route('admin.list-jobs.operations-delete-leg', [$job->job_id, $jleg->id])}}" class="btn btn-danger"><i class="ti-trash"></i></a></td>
                                            <td>
                                                {{$jleg->leg_number}}
                                                <input type="hidden" name="jlegs[{{$jleg->id}}]" value="{{$jleg->id}}">
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <input class="form-control leg_date" name="leg_date[{{$jleg->id}}]" id="leg_date" value="{{ $jleg->leg_date?$jleg->leg_date->format($global->date_format):$job->job_date->format($global->date_format) }}" data-style="form-control">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" style="margin-bottom:10px;">
                                                    <textarea class="form-control" placeholder="@lang('modules.operations.from')"  name="pickup_address[{{$jleg->id}}]" id="pickup_address" rows="2">{{ $jleg->pickup_address }}</textarea>
                                                </div>
                                                <div class="form-group" style="margin-bottom:0px;">
                                                    <textarea class="form-control" placeholder="@lang('modules.operations.to')"  name="drop_off_address[{{$jleg->id}}]" id="drop_off_address" rows="2">{{ $jleg->drop_off_address }}</textarea>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <select class="form-control selectpicker" name="driver_id[{{$jleg->id}}]" id="driver_id" data-style="form-control">
                                                        @foreach($drivers as $rs)
                                                            <option value="{{ $rs->id }}" @if($rs->id == $jleg->driver_id) selected="" @endif
                                                            >{{ ucwords($rs->name) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <select class="form-control selectpicker" name="vehicle_id[{{$jleg->id}}]" id="vehicle_id" data-style="form-control">
                                                        @php
                                                            $this_vechicle_id = $jleg->vehicle_id ? $jleg->vehicle_id : $job->vehicle_id;
                                                        @endphp
                                                        @foreach($vehicles as $rs)
                                                            <option value="{{ $rs->id }}" @if($rs->id == $this_vechicle_id)
                                                            selected=""
                                                                    @endif
                                                            >{{ ucwords($rs->vehicle_name) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <select class="form-control selectpicker" name="job_type[{{$jleg->id}}]" id="job_type" data-style="form-control">
                                                        @php
                                                            $this_job_type = $jleg->job_type ? $jleg->job_type : $job->job_type;
                                                        @endphp
                                                        @foreach($job_type as $rs)
                                                            <option value="{{ $rs->options }}" @if($rs->options == $this_job_type)
                                                            selected=""
                                                                    @endif
                                                            >{{ ucwords($rs->options) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <select class="form-control selectpicker" name="leg_status[{{$jleg->id}}]" id="leg_status" data-style="form-control">
                                                        @foreach($leg_status as $rs)
                                                            <option value="{{ $rs->options }}" @if($rs->options == $jleg->leg_status)
                                                            selected=""
                                                                    @endif
                                                            >{{ ucwords($rs->options) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" style="margin-bottom:10px;">
                                                    <textarea class="form-control" placeholder="@lang('modules.operations.dispatch_notes')" name="dispatch_notes[{{$jleg->id}}]" id="dispatch_notes" rows="2">{{ $jleg->notes }}</textarea>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8">@lang('messages.noRecordFound')</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                        {!! Form::close() !!}
                    </div>
                    <div class="white-box">
                        <div class="table-responsive">
                            <div class="form-actions">
                                <button type="submit" id="save-form" class="btn btn-success"><i class="fa fa-check"></i>
                                    @lang('modules.operations.save')
                                </button>
                                <a href="{{route('admin.list-jobs.index')}}" class="btn btn-default">@lang('app.cancel')</a>
                            </div>
                        </div>
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
    <script src="{{ asset('bootstrap/jquery.bootstrap-touchspin.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.leg_date').datepicker({
                toggleActive: true,
                format: '{{ $global->date_picker_format }}',
                language: '{{ $global->locale }}',
                autoclose: true
            });
            $('#save-form').click(function() {
                $.easyAjax({
                    url: "{{route('admin.list-jobs.operations-save-data', [$job->job_id])}}",
                    container: '#generalForm',
                    type: "POST",
                    redirect: true,
                    data: $('#generalForm').serialize()
                });
            });
        });
    </script>
@endpush