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
                <li class="active">@lang('app.menu.projects')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection


@section('content')

    <div class="row">


        <div class="col-md-12">
            <div class="white-box">

                <div class="row">
                    <div class="col-xs-6 b-r"> <strong>@lang('modules.employees.fullName')</strong> <br>
                        <p class="text-muted">{{ ucwords($customer->first_name.' '.$customer->last_name) }}</p>
                    </div>
                    <div class="col-xs-6 b-r"> <strong>@lang('app.email')</strong> <br>
                        <p class="text-muted">{{ $customer->email }}</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-xs-6"> <strong>@lang('app.phone')</strong> <br>
                        <p class="text-muted">{{ $customer->phone }}</p>
                    </div>
                    <div class="col-xs-6"> <strong>@lang('app.altPhone')</strong> <br>
                        <p class="text-muted">{{ $customer->alt_phone }}</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-xs-6"> <strong>@lang('app.mobile')</strong> <br>
                        <p class="text-muted">{{ $customer->mobile}}</p>
                    </div>
                    <div class="col-xs-6"> <strong>@lang('app.notes')</strong> <br>
                        <p class="text-muted">{{ $customer->notes}}</p>
                    </div>
                </div>

                {{--Custom fields data--}}
                @if(isset($fields))
                    <div class="row">
                        <hr>
                        @foreach($fields as $field)
                            <div class="col-md-4">
                                <strong>{{ ucfirst($field->label) }}</strong> <br>
                                <p class="text-muted">
                                    @if( $field->type == 'text')
                                        {{$clientDetail->custom_fields_data['field_'.$field->id] ?? '-'}}
                                    @elseif($field->type == 'password')
                                        {{$clientDetail->custom_fields_data['field_'.$field->id] ?? '-'}}
                                    @elseif($field->type == 'number')
                                        {{$clientDetail->custom_fields_data['field_'.$field->id] ?? '-'}}

                                    @elseif($field->type == 'textarea')
                                        {{$clientDetail->custom_fields_data['field_'.$field->id] ?? '-'}}

                                    @elseif($field->type == 'radio')
                                        {{ !is_null($clientDetail->custom_fields_data['field_'.$field->id]) ? $clientDetail->custom_fields_data['field_'.$field->id] : '-' }}
                                    @elseif($field->type == 'select')
                                        {{ (!is_null($clientDetail->custom_fields_data['field_'.$field->id]) && $clientDetail->custom_fields_data['field_'.$field->id] != '') ? $field->values[$clientDetail->custom_fields_data['field_'.$field->id]] : '-' }}
                                    @elseif($field->type == 'checkbox')
                                        {{ !is_null($clientDetail->custom_fields_data['field_'.$field->id]) ? $field->values[$clientDetail->custom_fields_data['field_'.$field->id]] : '-' }}
                                    @elseif($field->type == 'date')
                                        {{ isset($clientDetail->dob)?Carbon\Carbon::parse($clientDetail->dob)->format($global->date_format):Carbon\Carbon::now()->format($global->date_format)}}
                                    @endif
                                </p>

                            </div>
                        @endforeach
                    </div>
                @endif

                {{--custom fields data end--}}

            </div>
        </div>

        <div class="col-md-12">

            <section>
                <div class="sttabs tabs-style-line">
                    <div class="white-box">
                        <nav>
                            <ul>
                                <li class="tab-current">
                                    <a href="{{ route('admin.clients.projects', $customer->id) }}">
                                        <span>@lang('app.menu.jobs')</span>
                                    </a>
                                <li>
                                    <a href="{{ route('admin.clients.invoices', $customer->id) }}">
                                        <span>@lang('app.menu.invoices')</span>
                                    </a>
                                </li>
                                {{--<li>--}}
                                {{--<a href="{{ route('admin.contacts.show', $client->id) }}">--}}
                                {{--<span>@lang('app.menu.contacts')</span>--}}
                                {{--</a>--}}
                                {{--</li>--}}
                            </ul>
                        </nav>
                    </div>
                    <div class="content-wrap">
                        <section id="section-line-1" class="show">
                            <div class="row">


                                <div class="col-md-12">
                                    <div class="white-box">
                                        <h3 class="box-title b-b"><i class="fa fa-layers"></i> @lang('app.menu.jobs')</h3>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th>@lang('modules.listJobs.job_no')</th>
                                                    <th>@lang('modules.listJobs.job_date')</th>
                                                    <th>@lang('modules.listJobs.pickup_address')</th>
                                                    <th>@lang('modules.listJobs.delivery_address')</th>
                                                    <th>&nbsp;</th>
                                                </tr>
                                                </thead>
                                                <tbody id="timer-list">
                                                @forelse($jobs as $key=>$job)
                                                    <tr>
                                                        <td>{{ $job->job_number }}</td>
                                                        <td>{{ $job->job_date->format($global->date_format) }}</td>
                                                        <td>{{ $job->pickup_address ? $job->pickup_address : 'N/A' }}</td>
                                                        <td>{{ $job->drop_off_address ? $job->drop_off_address : 'N/A' }}</td>
                                                        <td>
                                                            <a
                                                                    href="{{ route("admin.list-jobs.edit-job", $job->job_id) }}"
                                                                    class="label label-info">@lang('modules.client.viewDetails')</a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5">@lang('messages.noJobFound')</td>
                                                    </tr>
                                                @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </section>
                    </div><!-- /content -->
                </div><!-- /tabs -->
            </section>
        </div>


    </div>
    <!-- .row -->

@endsection