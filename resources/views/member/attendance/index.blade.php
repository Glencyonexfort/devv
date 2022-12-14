@extends('layouts.member-app')

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
                <li><a href="{{ route('member.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ $pageTitle }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/morrisjs/morris.css') }}">

<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">

<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css"> -->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<style>
    .al-center {
        text-align: center;
    }
    .bt-border {
        border-bottom: 1px #cccccc solid;
    }
</style>
@endpush

@section('content')

    @if($user->can('add_attendance'))
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <a href="{{ route('member.attendances.create') }}"
                   class="btn btn-success btn-sm">@lang('modules.attendance.markAttendance') <i class="fa fa-plus"
                                                                                                aria-hidden="true"></i></a>
            </div>
        </div>
    </div>
    <!-- .row -->
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="white-box p-b-0 bg-inverse text-white">
                <div class="row">
                    <div class="col-md-4">
                        <label class="control-label">@lang('app.selectDateRange')</label>

                        <div class="input-daterange input-group" id="date-range">
                            <input type="text" class="form-control" id="start-date" placeholder="@lang('app.startDate')"
                                   value="{{ $startDate->format($global->date_format) }}"/>
                            <span class="input-group-addon bg-info b-0 text-white">@lang('app.to')</span>
                            <input type="text" class="form-control" id="end-date" placeholder="@lang('app.endDate')"
                                   value="{{ $endDate->format($global->date_format) }}"/>
                        </div>
                    </div>

                    @if($user->can('view_attendance'))
                        <div class="col-md-4">

                            <div class="form-group">
                                <label class="control-label">@lang('modules.timeLogs.employeeName')</label>
                                <select class="select2 form-control" data-placeholder="Choose Employee" id="user_id" name="user_id">
                                    @foreach($employees as $employee)
                                        <option @if($userId == $employee->id) selected @endif value="{{ $employee->id }}">{{ ucwords($employee->name) }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="col-md-3">
                            <div class="form-group m-t-25">
                                <button type="button" id="apply-filter" class="btn btn-success btn-block">@lang('app.apply')</button>
                            </div>
                        </div>
                    @endif


                </div>

            </div>
        </div>

        <div class="col-md-12">
            <div class="row">
                <div class="col-md-3">
                    <div class="white-box bg-inverse">
                        <h3 class="box-title text-white">@lang('modules.attendance.totalWorkingDays')</h3>
                        <ul class="list-inline two-part">
                            <li><i class="icon-clock text-white"></i></li>
                            <li class="text-right"><span id="totalWorkingDays" class="counter text-white">{{ $totalWorkingDays }}</span></li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="white-box bg-success">
                        <h3 class="box-title text-white">@lang('modules.attendance.daysPresent')</h3>
                        <ul class="list-inline two-part">
                            <li><i class="icon-clock text-white"></i></li>
                            <li class="text-right"><span id="daysPresent" class="counter text-white">{{ $daysPresent }}</span></li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="white-box bg-danger">
                        <h3 class="box-title text-white">@lang('app.days') @lang('modules.attendance.late')</h3>
                        <ul class="list-inline two-part">
                            <li><i class="icon-clock text-white"></i></li>
                            <li class="text-right"><span id="daysLate" class="counter text-white">{{ $daysLate }}</span></li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="white-box bg-warning">
                        <h3 class="box-title text-white">@lang('modules.attendance.halfDay')</h3>
                        <ul class="list-inline two-part">
                            <li><i class="icon-clock text-white"></i></li>
                            <li class="text-right"><span id="halfDays" class="counter text-white">{{ $halfDays }}</span></li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="white-box bg-info">
                        <h3 class="box-title text-white">@lang('app.days') @lang('modules.attendance.absent')</h3>
                        <ul class="list-inline two-part">
                            <li><i class="icon-clock text-white"></i></li>
                            <li class="text-right"><span id="absentDays" class="counter text-white">{{ (($totalWorkingDays - $daysPresent) < 0) ? '0' : ($totalWorkingDays - $daysPresent) }}</span></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="white-box bg-primary">
                        <h3 class="box-title text-white"> @lang('modules.attendance.holidays')</h3>
                        <ul class="list-inline two-part">
                            <li><i class="icon-clock text-white"></i></li>
                            <li class="text-right"><span id="holidayDays" class="counter text-white">{{ $holidays }}</span></li>
                        </ul>
                    </div>
                </div>
                @if(!$checkHoliday)
                    <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-wrapper collapse in">
                            <div class="panel-body">
                                @if($todayTotalClockin < $maxAttandenceInDay)
                                <div class="col-xs-6">
                                    <h3>@lang('modules.attendance.clock_in')</h3>
                                </div>
                                <div class="col-xs-6">
                                    <h3>@lang('modules.attendance.clock_in') IP</h3>
                                </div>
                                <div class="col-xs-6">
                                    @if(is_null($currenntClockIn))
                                        {{ \Carbon\Carbon::now()->timezone($global->timezone)->format($global->time_format) }}
                                    @else
                                        {{ $currenntClockIn->clock_in_time->timezone($global->timezone)->format($global->time_format) }}
                                    @endif
                                </div>
                                <div class="col-xs-6">
                                    {{ $currenntClockIn->clock_in_ip ?? request()->ip() }}
                                </div>

                                @if(!is_null($currenntClockIn) && !is_null($currenntClockIn->clock_out_time))
                                    <div class="col-xs-6 m-t-20">
                                        <label for="">@lang('modules.attendance.clock_out')</label>
                                        <br>{{ $currenntClockIn->clock_out_time->timezone($global->timezone)->format($global->time_format) }}
                                    </div>
                                    <div class="col-xs-6 m-t-20">
                                        <label for="">@lang('modules.attendance.clock_out') IP</label>
                                        <br>{{ $currenntClockIn->clock_out_ip }}
                                    </div>
                                @endif

                                <div class="col-xs-8 m-t-20">
                                    <label for="">@lang('modules.attendance.working_from')</label>
                                    @if(is_null($currenntClockIn))
                                        <input type="text" class="form-control" id="working_from" name="working_from">
                                    @else
                                        <br> {{ $currenntClockIn->working_from }}
                                    @endif
                                </div>

                                <div class="col-xs-4 m-t-20">
                                    <label class="m-t-30">&nbsp;</label>
                                    @if(is_null($currenntClockIn))
                                        <button class="btn btn-success btn-sm" id="clock-in">@lang('modules.attendance.clock_in')</button>
                                    @endif
                                    @if(!is_null($currenntClockIn) && is_null($currenntClockIn->clock_out_time))
                                        <button class="btn btn-danger btn-sm" id="clock-out">@lang('modules.attendance.clock_out')</button>
                                    @endif
                                </div>
                                @else
                                <div class="col-xs-12">
                                    <div class="alert alert-info">@lang('modules.attendance.maxColckIn')</div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

            </div>

        </div>

        <div class="col-md-12">
            <div class="white-box">

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>@lang('app.date')</th>
                            <th>@lang('app.status')</th>
                            <th>@lang('modules.attendance.clock_in')</th>
                            <th>@lang('modules.attendance.clock_out')</th>
                            <th>@lang('app.others')</th>
                        </tr>
                        </thead>
                        <tbody id="attendanceData">
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>


@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>

<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>

<script src="{{ asset('plugins/bower_components/waypoints/lib/jquery.waypoints.js') }}"></script>
<script src="{{ asset('plugins/bower_components/counterup/jquery.counterup.min.js') }}"></script>

<script>
    var startDate = '{{ $startDate->format('Y-m-d') }}';
    var endDate = '{{ $endDate->format('Y-m-d') }}';

    $('.input-daterange-datepicker').daterangepicker({
        buttonClasses: ['btn', 'btn-sm'],
        cancelClass: 'btn-inverse',
        "locale": {
            "applyLabel": "{{ __('app.apply') }}",
            "cancelLabel": "{{ __('app.cancel') }}",
            "daysOfWeek": [
                "{{ __('app.su') }}",
                "{{ __('app.mo') }}",
                "{{ __('app.tu') }}",
                "{{ __('app.we') }}",
                "{{ __('app.th') }}",
                "{{ __('app.fr') }}",
                "{{ __('app.sa') }}"
            ],
            "monthNames": [
                "{{ __('app.january') }}",
                "{{ __('app.february') }}",
                "{{ __('app.march') }}",
                "{{ __('app.april') }}",
                "{{ __('app.may') }}",
                "{{ __('app.june') }}",
                "{{ __('app.july') }}",
                "{{ __('app.august') }}",
                "{{ __('app.september') }}",
                "{{ __('app.october') }}",
                "{{ __('app.november') }}",
                "{{ __('app.december') }}",
            ]
        }
    })

    // $('.input-daterange-datepicker').on('apply.daterangepicker', function (ev, picker) {
    //     startDate = picker.startDate.format('YYYY-MM-DD');
    //     endDate = picker.endDate.format('YYYY-MM-DD');
    //     showTable();
    // });

    jQuery('#date-range').datepicker({
        toggleActive: true,
        format: '{{ $global->date_picker_format }}',
        language: '{{ $global->locale }}',
        autoclose: true
    });

    $('#apply-filter').click(function () {
       showTable();
    });

    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    var table;

    function showTable() {

        var startDate = $('#start-date').val();
        var endDate = $('#end-date').val();

        $('body').block({
            message: '<p style="margin:0;padding:8px;font-size:24px;">Just a moment...</p>'
            , css: {
                color: '#fff'
                , border: '1px solid #fb9678'
                , backgroundColor: '#fb9678'
            }
        });

        var userId = $('#user_id').val();
        if (typeof userId === 'undefined') {
            userId = '{{ $userId}}';
        }

        var token = "{{ csrf_token() }}";

        //refresh counts
        var url = '{!!  route('member.attendances.refreshCount') !!}';

        $.easyAjax({
            type: 'POST',
            data: {
                '_token': token,
                startDate: startDate,
                endDate: endDate,
                userId: userId
            },
            url: url,
            success: function (response) {
                $('#daysPresent').html(response.daysPresent);
                $('#daysLate').html(response.daysLate);
                $('#halfDays').html(response.halfDays);
                $('#totalWorkingDays').html(response.totalWorkingDays);
                $('#absentDays').html(response.absentDays);
                $('#holidayDays').html(response.holidays);
                initConter();
            }
        });

        //refresh datatable
        var url2 = '{!!  route('member.attendances.employeeData') !!}';

        $.easyAjax({
            type: 'POST',
            url: url2,
            data: {
                '_token': token,
                startDate: startDate,
                endDate: endDate,
                userId: userId
            },
            success: function (response) {
                $('#attendanceData').html(response.data);
            }
        });
    }

    $('#attendanceData').on('click', '.delete-attendance', function(){
        var id = $(this).data('attendance-id');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted attendance record!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {

                var url = "{{ route('member.attendances.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
//                                    swal("Deleted!", response.message, "success");
                            window.location.reload();
                        }
                    }
                });
            }
        });
    });


    function initConter() {
        $(".counter").counterUp({
            delay: 100,
            time: 1200
        });
    }

    showTable();

</script>
<script>
    $('#clock-in').click(function () {
        var workingFrom = $('#working_from').val();

        var token = "{{ csrf_token() }}";

        $.easyAjax({
            url: '{{route('member.attendances.store')}}',
            type: "POST",
            data: {
                working_from: workingFrom,
                _token: token
            },
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        })
    })

    @if(!is_null($currenntClockIn))
    $('#clock-out').click(function () {

        var token = "{{ csrf_token() }}";

        $.easyAjax({
            url: '{{route('member.attendances.update', $currenntClockIn->id)}}',
            type: "PUT",
            data: {
                _token: token
            },
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        })
    })
    @endif

</script>

@endpush