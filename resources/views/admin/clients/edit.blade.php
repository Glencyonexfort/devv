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
                <li><a href="{{ route('admin.clients.index') }}">{{ $pageTitle }}</a></li>
                <li class="active">@lang('app.edit')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-inverse">
                <div class="panel-heading"> @lang('modules.customer.updateTitle')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        {{--                        {!! Form::open(['id'=>'updateClient','class'=>'ajax-form','method'=>'PUT']) !!}--}}
                        <form id="updateCustomer" method="POST" action="{{url('/admin/clients/update-customer/'.$customerDetail->id)}}">
                            {{csrf_field()}}
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">@lang('app.first_name')</label>
                                            <input
                                                    type="text"
                                                    id="first_name"
                                                    name="first_name"
                                                    value="{{$customerDetail->first_name}}"
                                                    class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">@lang('app.last_name')</label>
                                            <input
                                                    type="text"
                                                    id="last_name"
                                                    name="last_name"
                                                    value="{{$customerDetail->last_name}}"
                                                    class="form-control" >
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('app.email') (@lang('modules.customer.emailNote'))</label>
                                            <input
                                                    type="email"
                                                    name="email"
                                                    id="email"
                                                    value="{{$customerDetail->email}}"
                                                    class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('app.mobile')</label>
                                            <input
                                                    type="tel"
                                                    name="mobile"
                                                    id="mobile"
                                                    value="{{$customerDetail->mobile}}"
                                                    class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('app.phone')</label>
                                            <input
                                                    type="tel"
                                                    name="phone"
                                                    id="phone"
                                                    value="{{$customerDetail->phone}}"
                                                    class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('app.alt_phone')</label>
                                            <input
                                                    type="tel"
                                                    name="alt_phone"
                                                    id="alt_phone"
                                                    value="{{$customerDetail->alt_phone}}"
                                                    class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <label>@lang('app.note')</label>
                                        <div class="form-group">
                                            <textarea
                                                    name="note"
                                                    id="note"
                                                    class="form-control" rows="5">{!! $customerDetail->notes  !!}</textarea>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="form-actions">
                                <input type="submit" class="btn btn-success" value="@lang('app.update')">
                                <a
                                        href="{{ route('admin.clients.index') }}"
                                        class="btn btn-default">@lang('app.back')</a>
                            </div>
                        </form>
                        {{--{!! Form::close() !!}--}}
                    </div>
                </div>
            </div>
        </div>
    </div>    <!-- .row -->

@endsection

@push('footer-script')
    <script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script>
        $(".date-picker").datepicker({
            todayHighlight: true,
            autoclose: true
        });

        {{--$('#save-form').click(function () {--}}
        {{--$.easyAjax({--}}
        {{--url: '{{route('admin.clients.update', [$userDetail->id])}}',--}}
        {{--container: '#updateClient',--}}
        {{--type: "POST",--}}
        {{--redirect: true,--}}
        {{--data: $('#updateClient').serialize()--}}
        {{--})--}}
        {{--});--}}
    </script>
@endpush