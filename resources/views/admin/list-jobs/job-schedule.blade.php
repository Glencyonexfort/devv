@extends('layouts.app')

@section('page-title')
<div class="page-header page-header-light view_blade_page_header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex view_blade_page_padding">
            <h4>
            <i class="icon-calendar"></i>
                <span class="view_blade_page_span_header">{{ $pageTitle }} </span>
        </div>
    </div>
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
                <div class="row mb-1">
                    <div class="col-md-6">
                        <b>Left Band - Payment Status</b><br/> <span class="static_unpaid">Unpaid</span><span class="static_partial">Partially Paid</span><span class="static_paid">Paid</span>
                    </div>
                    <div class="col-md-6">                    
                        <b>Right Band - Job/Leg Status</b><br/> <span class="static_new">New, Awaiting Confirmation</span><span class="static_confirm">Confirmed</span><span class="static_complete">Completed</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <b>Vehicle Group:</b><br/>
                        <select class="select2 form-control"
                            data-placeholder="Vehicle Group" id="vehicleGroups">
                            <option value="all">All</option>
                            @foreach ($vehicleGroups as $rs)
                                <option value="{{ $rs->group_name }}" @if ($rs->group_name == $vehicleGroupFilter)
                                    selected=""
                            @endif>{{ $rs->group_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- <div class="col-md-1">
                        @if($vehicleGroupFilter!="all")
                        <a id="vehicle_group_reset" class="btn btn-lg btn-inverse mt-3" style="cursor: pointer">Reset</a>
                        @endif
                    </div> --}}
                </div>
                <hr/>
                <div id="calendar"></div>
            </div>
        </div>
    </div>
    <input type="hidden" id="firstDay" value="{{ $firstDay }}">
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
{{-- <script src='{{asset('assets/system_design/fullCalendar/moment.min.js')}}'></script>
<script src='{{asset('assets/system_design/fullCalendar/jquery.min.js')}}'></script>
<script src='{{asset('assets/system_design/fullCalendar/jquery-ui.min.js')}}'></script> --}}
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
    background:#171e27 !important;
    height: 30px;
    color: white;
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
  color: #12112b;
  padding-left: 5px;
  }

  .fc-cell-content>.fc-icon{
    height: 33px;
    width: 32px;
    margin-left: 0px;
    margin-right: -7px;
    background:url({{asset('assets/system_design/images/truck-icon.png')}});
  }
 .tooltipevent{
    width: 280px;
    background-color: #12112b;
    color: #fff;
    padding: 5px 10px;
    position: absolute;
    z-index: 10001;
    text-align: left;
  }
.fc-divider{
    background:#171e27 !important;
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
.fc-event {
    margin-top: 2px !important;
    color: #171e27!important;
    border-top: none!important;
    border-bottom: none!important;
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
      firstDay: $('#firstDay').val(),
      slotDuration: "00:30:00",
      views: {
        day: {
            titleFormat: 'dddd, MMMM D, YYYY'
        }        
      },
      //titleFormat:'MMM D YYYY',
      resourceLabelText: 'Vehicles',
      resourceAreaWidth: '12%',
      resourceGroupField: 'groupId',
      resources: '{{route('admin.list-jobs.calendar-resources', [$vehicleGroupFilter])}}',
      events: '{{route('admin.list-jobs.calendar-events', [$vehicleGroupFilter])}}',

      eventMouseover: function(calEvent, jsEvent) {
            var tooltip = '<div class="tooltipevent">' + calEvent.mousehover_title + '</div>';
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
        var leg_id          = event.id;
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

           data:{leg_id:leg_id, vehicle_id:vehicle_id, job_date:job_date, start_time:start_time, end_time:end_time},

           success:function(data){

              console.log(data.success);

           }

        });


      },
        
        eventResize: function(event,date,resourceId) { 
        //called when an event (already on the calendar) is moved
        var job_id          = event.job_id;
        var leg_id          = event.id;
        var vehicle_id      = event.resourceId;
        var job_date        = event.start.format('YYYY-MM-DD');
        var start_time      = event.start.format('HH:mm:ss');        

        var defaultDuration = moment.duration($('#calendar').fullCalendar('option', 'defaultTimedEventDuration')); 
        var end             = event.end || event.start.clone().add(defaultDuration); 
        //console.log('end is ' + end.format('hh:mm:A'));
        var end_time        = end.format('HH:mm:ss');
        //console.log(leg_id);
        var posted_id;
        var ajax_data;
        $.ajax({

           headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           },

           type:'POST',

           url:"{{route('admin.list-jobs.updateScheduleEvent')}}",

           data:{leg_id:leg_id, vehicle_id:vehicle_id, job_date:job_date, start_time:start_time, end_time:end_time},

           success:function(data){

              console.log(data.success);

           }

        });


      },

      eventClick: function(event,date,resourceId) { //calEvent, jsEvent, view
        var job_id          = event.job_id;
        //window.location.href = 'view-job/'+job_id;
        window.open(
          '/admin/moving/view-job/'+job_id,
          '_blank' // <- This is what makes it open in a new window.
        );
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
                console.log(d);
            }
        });


        $("body").off('change', '#vehicleGroups').on('change', '#vehicleGroups', function(e) {
            var vehicle_group = $(this).val();
            // var hostname = window.location.hostname;
            // var url = window.location.pathname;
            window.location.href = "/admin/moving/job-schedule/" + vehicle_group;
        });

        $("body").off('click', '#vehicle_group_reset').on('click', '#vehicle_group_reset', function(e) {
            window.location.href = "/admin/moving/job-schedule";
        });
  });


    </script>

@endpush