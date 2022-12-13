@extends('layouts.app')

@section('page-title')
    <div class="row bg-title" style="padding: 2px 15px 0px;margin-bottom: 5px;">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ $pageTitle }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <!-- <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ $pageTitle }}</li>
            </ol>
        </div> -->
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
    <meta name="csrf-token" content="{{ csrf_token() }}">
   
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/timepicker/bootstrap-timepicker.min.css') }}">

    <link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/multiselect/css/multi-select.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap-colorselector/bootstrap-colorselector.min.css') }}">
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
<!--                {{--<div class="row">--}}
                {{--<h3 class="box-title col-md-3">@lang('app.menu.jobSchedule')</h3>--}}

                {{--<div class="col-md-9">--}}
                {{--<a href="#" data-toggle="modal" data-target="#my-event" class="btn btn-sm btn-success waves-effect waves-light  pull-right">--}}
                {{--<i class="ti-plus"></i> @lang('modules.events.addEvent')--}}
                {{--</a>--}}

                {{--</div>--}}

                {{--</div>--}}-->


                <div id="calendar"></div>
            </div>
        </div>
    </div>
    <!-- .row -->

    <!-- BEGIN MODAL -->
<!--    {{--<div class="modal fade bs-modal-md in" id="my-event" role="dialog" aria-labelledby="myModalLabel"--}}
    {{--aria-hidden="true">--}}
    {{--<div class="modal-dialog modal-lg" id="modal-data-application">--}}
    {{--<div class="modal-content">--}}
    {{--<div class="modal-header">--}}
    {{--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>--}}
    {{--<h4 class="modal-title"><i class="icon-plus"></i> @lang('modules.events.addEvent')</h4>--}}
    {{--</div>--}}
    {{--<div class="modal-body">--}}
    {{--{!! Form::open(['id'=>'createEvent','class'=>'ajax-form','method'=>'POST']) !!}--}}
    {{--<div class="form-body">--}}
    {{--<div class="row">--}}
    {{--<div class="col-md-6 ">--}}
    {{--<div class="form-group">--}}
    {{--<label>@lang('modules.events.eventName')</label>--}}
    {{--<input type="text" name="event_name" id="event_name" class="form-control">--}}
    {{--</div>--}}
    {{--</div>--}}

    {{--<div class="col-md-2 ">--}}
    {{--<div class="form-group">--}}
    {{--<label>@lang('modules.sticky.colors')</label>--}}
    {{--<select id="colorselector" name="label_color">--}}
    {{--<option value="bg-info" data-color="#5475ed" selected>Blue</option>--}}
    {{--<option value="bg-warning" data-color="#f1c411">Yellow</option>--}}
    {{--<option value="bg-purple" data-color="#ab8ce4">Purple</option>--}}
    {{--<option value="bg-danger" data-color="#ed4040">Red</option>--}}
    {{--<option value="bg-success" data-color="#00c292">Green</option>--}}
    {{--<option value="bg-inverse" data-color="#4c5667">Grey</option>--}}
    {{--</select>--}}
    {{--</div>--}}
    {{--</div>--}}

    {{--<div class="col-md-4 ">--}}
    {{--<div class="form-group">--}}
    {{--<label>@lang('modules.events.where')</label>--}}
    {{--<input type="text" name="where" id="where" class="form-control">--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}

    {{--<div class="row">--}}
    {{--<div class="col-xs-12 ">--}}
    {{--<div class="form-group">--}}
    {{--<label>@lang('app.description')</label>--}}
    {{--<textarea name="description" id="description" class="form-control"></textarea>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="row">--}}
    {{--<div class="col-xs-6 col-md-3 ">--}}
    {{--<div class="form-group">--}}
    {{--<label>@lang('modules.events.startOn')</label>--}}
    {{--<input type="text" name="start_date" id="start_date" class="form-control">--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="col-xs-5 col-md-3">--}}
    {{--<div class="input-group bootstrap-timepicker timepicker">--}}
    {{--<label>&nbsp;</label>--}}
    {{--<input type="text" name="start_time" id="start_time"--}}
    {{--class="form-control">--}}
    {{--</div>--}}
    {{--</div>--}}

    {{--<div class="col-xs-6 col-md-3">--}}
    {{--<div class="form-group">--}}
    {{--<label>@lang('modules.events.endOn')</label>--}}
    {{--<input type="text" name="end_date" id="end_date" class="form-control">--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="col-xs-5 col-md-3">--}}
    {{--<div class="input-group bootstrap-timepicker timepicker">--}}
    {{--<label>&nbsp;</label>--}}
    {{--<input type="text" name="end_time" id="end_time"--}}
    {{--class="form-control">--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="row">--}}
    {{--<div class="col-xs-12"  id="attendees">--}}
    {{--<div class="form-group">--}}
    {{--<label class="col-xs-3 m-t-10">@lang('modules.events.addAttendees')</label>--}}
    {{--<div class="col-xs-7">--}}
    {{--<div class="checkbox checkbox-info">--}}
    {{--<input id="all-employees" name="all_employees" value="true"--}}
    {{--type="checkbox">--}}
    {{--<label for="all-employees">@lang('modules.events.allEmployees')</label>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="form-group">--}}
    {{--<select class="select2 m-b-10 select2-multiple " multiple="multiple"--}}
    {{--data-placeholder="@lang('modules.messages.chooseMember'), @lang('modules.projects.selectClient')" name="user_id[]">--}}
    {{--@foreach($employees as $emp)--}}
    {{--<option value="{{ $emp->id }}">{{ ucwords($emp->name) }} @if($emp->id == $user->id)--}}
    {{--(YOU) @endif</option>--}}
    {{--@endforeach--}}
    {{--</select>--}}

    {{--</div>--}}
    {{--</div>--}}

    {{--</div>--}}

    {{--<div class="row">--}}
    {{--<div class="form-group">--}}
    {{--<div class="col-xs-6">--}}
    {{--<div class="checkbox checkbox-info">--}}
    {{--<input id="repeat-event" name="repeat" value="yes"--}}
    {{--type="checkbox">--}}
    {{--<label for="repeat-event">@lang('modules.events.repeat')</label>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}

    {{--<div class="row" id="repeat-fields" style="display: none">--}}
    {{--<div class="col-xs-6 col-md-3 ">--}}
    {{--<div class="form-group">--}}
    {{--<label>@lang('modules.events.repeatEvery')</label>--}}
    {{--<input type="number" min="1" value="1" name="repeat_count" class="form-control">--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="col-xs-6 col-md-3">--}}
    {{--<div class="form-group">--}}
    {{--<label>&nbsp;</label>--}}
    {{--<select name="repeat_type" id="" class="form-control">--}}
    {{--<option value="day">@lang('app.day')</option>--}}
    {{--<option value="week">@lang('app.week')</option>--}}
    {{--<option value="month">@lang('app.month')</option>--}}
    {{--<option value="year">@lang('app.year')</option>--}}
    {{--</select>--}}
    {{--</div>--}}
    {{--</div>--}}

    {{--<div class="col-xs-6 col-md-3">--}}
    {{--<div class="form-group">--}}
    {{--<label>@lang('modules.events.cycles') <a class="mytooltip" href="javascript:void(0)"> <i class="fa fa-info-circle"></i><span class="tooltip-content5"><span class="tooltip-text3"><span class="tooltip-inner2">@lang('modules.events.cyclesToolTip')</span></span></span></a></label>--}}
    {{--<input type="text" name="repeat_cycles" id="repeat_cycles" class="form-control">--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}

    {{--</div>--}}
    {{--{!! Form::close() !!}--}}

    {{--</div>--}}
    {{--<div class="modal-footer">--}}
    {{--<button type="button" class="btn btn-white waves-effect" data-dismiss="modal">@lang('app.close')</button>--}}
    {{--<button type="button" class="btn btn-success save-event waves-effect waves-light">@lang('app.submit')</button>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}

    {{--Ajax Modal--}}
    {{--<div class="modal fade bs-modal-md in" id="eventDetailModal" role="dialog" aria-labelledby="myModalLabel"--}}
    {{--aria-hidden="true">--}}
    {{--<div class="modal-dialog modal-lg" id="modal-data-application">--}}
    {{--<div class="modal-content">--}}
    {{--<div class="modal-header">--}}
    {{--<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>--}}
    {{--<span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>--}}
    {{--</div>--}}
    {{--<div class="modal-body">--}}
    {{--Loading...--}}
    {{--</div>--}}
    {{--<div class="modal-footer">--}}
    {{--<button type="button" class="btn default" data-dismiss="modal">Close</button>--}}
    {{--<button type="button" class="btn blue">Save changes</button>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{-- /.modal-content --}}
    {{--</div>--}}
    {{-- /.modal-dialog --}}
    {{--</div>--}}
    {{--Ajax Modal Ends--}}-->

@endsection

@push('footer-script')

    <script>
        var taskEvents = [
                @foreach($events as $event)
            {
                id: '{{ ucfirst($event->id) }}',
                title: '{{ ucfirst($event->event_name) }}',
                start: '{{ $event->start_date_time }}',
                end:  '{{ $event->end_date_time }}',
                className: '{{ $event->label_color }}'
            },
            @endforeach
        ];
        var getEventDetail = function (id) {
            var url = '{{ route('admin.events.show', ':id')}}';
            url = url.replace(':id', id);
            $('#modelHeading').html('Event');
            $.ajaxModal('#eventDetailModal', url);
        }
        var calendarLocale = '{{ $global->locale }}';
    </script>

    
    <link href='{{asset('assets/system_design/fullCalendar/fullcalendar.min.css')}}' rel='stylesheet' />
<link href='{{asset('assets/system_design/fullCalendar/fullcalendar.print.min.css')}}' rel='stylesheet' media='print' />
<link href='{{asset('assets/system_design/fullCalendar/scheduler.min.css')}}' rel='stylesheet' />
<!-- <link href='https://bootswatch.com/3/cyborg/bootstrap.min.css' rel='stylesheet' /> -->
<script src='{{asset('assets/system_design/fullCalendar/moment.min.js')}}'></script>
<script src='{{asset('assets/system_design/fullCalendar/jquery.min.js')}}'></script>
<script src='{{asset('assets/system_design/fullCalendar/jquery-ui.min.js')}}'></script>
<script src='{{asset('assets/system_design/fullCalendar/fullcalendar.min.js')}}'></script>
<script src='{{asset('assets/system_design/fullCalendar/scheduler.min.js')}}'></script>
<script type="text/javascript" src="{{asset('assets/system_design/typeahead.js')}}"></script>

<link rel="stylesheet" href="{{asset('assets/system_design/jquery-ui.css')}}">
<style type="text/css">
    .fc-expander {
    color: white !important;
}
.fc-rows tr td.fc-widget-content div {
  height: 50px !important;
  text-align: left;
}

.fc-divider {
    width: 1px !important;
}
.fc-ltr .fc-time-area .fc-slats td {
    /*border-right-width: 0;*/
    border: 0px !important;
}
.fc-agenda-slots td div {
     height: 42px !important;
}
.fc-rows tr {
height: 35px;
}
.job_event_green {
  /*background-color: #32CD32;*/
  border-right: 8px solid #32CD32 !important;
  /*border-right: 0px;*/
  border-bottom: 0px;
  border-top: 0px;
  color: black !important;
  padding-left: 5px;
}
.border_darkgreen{
  border-left: 8px solid #D8F1D6 !important;
}
 .tooltipevent{
    width: 120px;background-color: black;color: #fff;text-align: center;padding: 5px 0;border-radius: 6px;position:absolute;z-index:10001;
  }
.border_red{
  border-left: 8px solid #E01A1F !important;
}
.job_event_darkgreen {
  /*background-color: #D8F1D6;*/
  border-right: 8px solid #D8F1D6 !important;
  /*border-right: 0px;*/
  border-bottom: 0px;
  border-top: 0px;
  color: black !important;
  padding-left: 5px;
}
.job_event_yellow {
  /*background-color: #fec107;*/
  border-right: 8px solid #fec107 !important;/*#FFCC00;*/
  /*border-right: 0px;*/
  border-bottom: 0px;
  border-top: 0px;
  color: black !important;
  padding-left: 5px;
  color: #000;
}
.job_event_blue {
  /*background-color: #3a87ad;*/
  border-right: 8px solid #3a87ad !important;/*#0066FF;*/
  color:#fff !important;
  /*border-right: 0px;*/
  border-bottom: 0px;
  border-top: 0px;
  color: black !important;
  padding-left: 5px;
}
.job_event_red {
  /*background-color: #F1AEAF;*/
  border-right: 8px solid #F1AEAF !important;/*#E01A1F;*/
  color:#fff;
  /*border-right: 0px;*/
  border-bottom: 0px;
  border-top: 0px;
  color: black !important;
  padding-left: 5px;
}
/*.fc-event, .fc-event:hover {
    color: #2D66B3;
    text-decoration: none;
}*/

.unavailability_event { 
  background-color: #F1AEAF;
  border-left: 4px solid #E01A1F;
  border-right: 0px;
  border-bottom: 0px;
  border-top: 0px;
  color: black;
  padding-left: 5px;
  }

  .fc-cell-content>.fc-icon{
    height: 33px;
    width: 32px;
    margin-left: 0px;
    margin-right: -7px;
    background:url({{asset('assets/system_design/images/teams-icon.png')}});
  }

.fc-divider{
    background:#2C2C2C !important;
    height: 30px;
    color: white;
}

.ui-datepicker-trigger {
  height: 24px;
  width: 24px;
  border: 0px;
  position: revert !important;
}
.fc-center {
  vertical-align: sub !important;
}
.fc-icon:after {
  font-family: revert !important;
  line-height: 0;
}
</style>
    <script>


    $('#calendar').fullCalendar({
      
     

      defaultView: 'timelineDay',//
      //defaultDate: calendarViewDate,
      
      //defaultView: 'agendaDay', 
      minTime: '06:00:00',
      maxTime: '20:00:00',
      editable: true,
      droppable: true,  // this allows things to be dropped onto the calendar
      navLinks: true, // can click day/week names to navigate views
      //disableDragging: true,
     
      aspectRatio: 1.8,
      progressiveEventRendering: true,
      scrollTime: '00:00', // undo default 6am scrollTime
      header: {
        left: 'prev,next, today',
        center: 'title',
        right: 'timelineDay,agendaWeek,month' //timelineThreeDays, 
      }, //agendaWeek,oneWeek, timelineTwoWeeks,
      firstDay: 1,
      slotDuration: "00:30:00",
      views: {
        day: {
            titleFormat: 'dddd, MMMM D, YYYY'
        }        
      },
      //titleFormat:'MMM D YYYY',
      resourceLabelText: 'Teams',
      resourceAreaWidth: '12%',
      resourceGroupField: 'cities',
      resources: '{{route('admin.list-jobs-cleaning.calendar-resources')}}',
      events: '{{route('admin.list-jobs-cleaning.calendar-events')}}',

      eventMouseover: function(calEvent, jsEvent) {
            var tooltip = '<div class="tooltipevent">' + calEvent.title + '</div>';
            $("body").append(tooltip);
            $(this).mouseover(function(e) {
                $(this).css('z-index', 10000);
                $('.tooltipevent').fadeIn('500');
                $('.tooltipevent').fadeTo('10', 1.9);
            }).mousemove(function(e) {
                $('.tooltipevent').css('top', e.pageY + 10);
                $('.tooltipevent').css('left', e.pageX + 20);
            });
        },

        eventMouseout: function(calEvent, jsEvent) {
             $(this).css('z-index', 8);
             $('.tooltipevent').remove();
        },

        eventDrop: function(event,date,resourceId) { 
        //called when an event (already on the calendar) is moved
        var job_id          = event.job_id;
        var vehicle_id      = event.resourceId;
        var job_date        = event.start.format('YYYY-MM-DD');
        var start_time      = event.start.format('HH:mm:ss');        

        var defaultDuration = moment.duration($('#calendar').fullCalendar('option', 'defaultTimedEventDuration')); 
        var end             = event.end || event.start.clone().add(defaultDuration); 
        //console.log('end is ' + end.format('hh:mm:A'));
        var end_time        = end.format('HH:mm:ss');
        //console.log(job_id);
        var posted_id;
        var ajax_data;
        $.ajax({

           headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           },

           type:'POST',

           url:"{{route('admin.list-jobs.updateScheduleEvent')}}",

           data:{job_id:job_id, vehicle_id:vehicle_id, job_date:job_date, start_time:start_time, end_time:end_time},

           success:function(data){

              console.log(data.success);

           }

        });


      },
        
        eventResize: function(event,date,resourceId) { 
        //called when an event (already on the calendar) is moved
        var job_id          = event.job_id;
        var vehicle_id      = event.resourceId;
        var job_date        = event.start.format('YYYY-MM-DD');
        var start_time      = event.start.format('HH:mm:ss');        

        var defaultDuration = moment.duration($('#calendar').fullCalendar('option', 'defaultTimedEventDuration')); 
        var end             = event.end || event.start.clone().add(defaultDuration); 
        //console.log('end is ' + end.format('hh:mm:A'));
        var end_time        = end.format('HH:mm:ss');
        //console.log(job_id);
        var posted_id;
        var ajax_data;
        $.ajax({

           headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           },

           type:'POST',

           url:"{{route('admin.list-jobs.updateScheduleEvent')}}",

           data:{job_id:job_id, vehicle_id:vehicle_id, job_date:job_date, start_time:start_time, end_time:end_time},

           success:function(data){

              console.log(data.success);

           }

        });


      },

      eventClick: function(event,date,resourceId) { //calEvent, jsEvent, view
        var job_id          = event.job_id;
        window.location.href = 'view-job/'+job_id;
      },

      
    });

        /*document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: [ 'interaction', 'resourceTimeline' ],
                header: {
                    left: 'prev,next',
                    center: 'title',
                    right: 'timelineDay,agendaWeek,month'
                    //'resourceTimelineDay,resourceTimelineWeek,resourceTimelineMonth'
                },
                timeZone: 'UTC',
                axisFormat: 'H:mm',
                minTime: '06:00',
                maxTime: '19:00',
                defaultView: 'agendaWeek',
                aspectRatio: 1.5,
                editable: true,
                resourceLabelText: 'VEHICLES',
                resourceAreaWidth: '12%',
                resourceGroupField: 'cities',
                resources: '{{route('admin.list-jobs.calendar-resources')}}',
                events: '{{route('admin.list-jobs.calendar-events')}}'
            });
            calendar.render();
        });*/
    </script>


    <!--{{--    <script src="{{ asset('js/job-schedule-calendar.js') }}"></script>--}}-->

    
    <script src="{{ asset('plugins/bower_components/timepicker/bootstrap-timepicker.min.js') }}"></script>

    <script src="{{ asset('js/cbpFWTabs.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/multiselect/js/jquery.multi-select.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap-colorselector/bootstrap-colorselector.min.js') }}"></script>

    <script>
        jQuery('#start_date, #end_date').datepicker({
            autoclose: true,
            todayHighlight: true
        })
        $('#colorselector').colorselector();
        $('#start_time, #end_time').timepicker();
        $(".select2").select2({
            formatNoMatches: function () {
                return "{{ __('messages.noRecordFound') }}";
            }
        });
        function addEventModal(start, end, allDay){
            if(start){
                var sd = new Date(start);
                var curr_date = sd.getDate();
                if(curr_date < 10){
                    curr_date = '0'+curr_date;
                }
                var curr_month = sd.getMonth();
                curr_month = curr_month+1;
                if(curr_month < 10){
                    curr_month = '0'+curr_month;
                }
                var curr_year = sd.getFullYear();
                $('#start_date').val(curr_month+'/'+curr_date+'/'+curr_year);
                var ed = new Date(start);
                var curr_date = sd.getDate();
                if(curr_date < 10){
                    curr_date = '0'+curr_date;
                }
                var curr_month = sd.getMonth();
                curr_month = curr_month+1;
                if(curr_month < 10){
                    curr_month = '0'+curr_month;
                }
                var curr_year = ed.getFullYear();
                $('#end_date').val(curr_month+'/'+curr_date+'/'+curr_year);
                $('#start_date, #end_date').datepicker('destroy');
                jQuery('#start_date, #end_date').datepicker({
                    autoclose: true,
                    todayHighlight: true
                })
            }
            $('#my-event').modal('show');
        }
        $('.save-event').click(function () {
            $.easyAjax({
                url: '{{route('admin.events.store')}}',
                container: '#modal-data-application',
                type: "POST",
                data: $('#createEvent').serialize(),
                success: function (response) {
                    if(response.status == 'success'){
                        window.location.reload();
                    }
                }
            })
        })
        $('#repeat-event').change(function () {
            if($(this).is(':checked')){
                $('#repeat-fields').show();
            }
            else{
                $('#repeat-fields').hide();
            }
        })
    </script>


    <script type="text/javascript">
        
         $(document).ready(function() {

 var custom_buttons = '<div style="display:inline-block;margin-left: 5px;" class="fc-button-next ui-state-default ui-corner-left ui-corner-right">' + '<span>' + '<input type="hidden" id="date_picker" value="" />' + '</span>' + '</div>';
 //alert(custom_buttons);
        $('.fc-center').after(custom_buttons);


        $("#date_picker").datepicker({
            showOn: "button",
            buttonImage: "{{ asset('assets/system_design/icons/calendar.gif') }}",
            buttonImageOnly: true,
            buttonText: "Select date",
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            //numberOfMonths: 2,
            onSelect: function(dateText, inst) {
                var d = $("#date_picker").datepicker("getDate");
                $('#calendar').fullCalendar('gotoDate', d);
            }
        });
  });


    </script>

@endpush