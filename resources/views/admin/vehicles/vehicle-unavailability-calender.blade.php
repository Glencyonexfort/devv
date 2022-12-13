@extends('layouts.app')
@section('page-title')
@push('head-script')

@endpush
<style type="text/css">
    .row{
        width: 100%!important;
    }

    .btn-float.btn-link{
        padding: .6rem;
        border: 1px solid #912a4e;
        color: #912a4e;
        border-radius: 0px;
    }
    .btn-float.btn-link:hover{
        opacity: 0.8;
    }
    .vehicle-tab-active{
        background-color: #912a4e;
        color: #fff!important;
    }
</style>
<div class="page-header page-header-light view_blade_page_header">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex view_blade_page_padding">
                <h4>
                    <i class="icon-truck"></i>
                    <span class="view_blade_page_span_header">Vehicle Unavailability</span>
            </div>
            <div class="header-elements d-none">
                <div class="d-flex justify-content-center">
                    <a href="{{ route('admin.vehicle-unavailability') }}" class="btn btn-link btn-float" title="List View"><i class="icon-list"></i></a>
                    <a href="{{ route('admin.vehicle-unavailability-calender',['id'=>0]) }}" class="btn btn-link btn-float vehicle-tab-active" title="Calender View"><i class="icon-calendar"></i></a>
                </div>
            </div>
        </div>
        <div class="card" style="border:0px;">
            <div class="card-body pb-0" style="display: block!important;">
                <div class="row" style="background: #fbfbfb;padding: 10px;margin-bottom: 15px;margin-right:0px;">
                    <form action="" style="width: 100%">
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="txt14 w400">Vehicles</label>
                                        <select class="form-control" name="id" id="vehicle_id">
                                            @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}"
                                                @if($vehicle->id==$vehicle_id)selected=''@endif>{{ $vehicle->vehicle_name }}</option>
                                            @endforeach
                                    
                                    </select>
                                </div>
                                
                            </div>
                            <div class="form-group col-md-4" style="margin-top: 28px!important;">
                                <label class="control-label">&nbsp;</label>
                                <button id="apply_filter" type="button" class="btn btn-success wide-btn"><i class="fa fa-check"></i> Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')

<div class="white-box">
    <div class="fullcalendar-event-colors"></div>
</div>
@endsection
<?php
// echo '<pre>';
//     print_r($unavailable_vehicles_list);
?>
@push('footer-script')
    <script src="{{ asset('newassets/global_assets/js/plugins/ui/fullcalendar/core/main.min.js') }}"></script>
	<script src="{{ asset('newassets/global_assets/js/plugins/ui/fullcalendar/daygrid/main.min.js') }}"></script>
	<script src="{{ asset('newassets/global_assets/js/plugins/ui/fullcalendar/timegrid/main.min.js') }}"></script>
    <script src="{{ asset('newassets/global_assets/js/plugins/ui/fullcalendar/interaction/main.min.js') }}"></script>
<script>
    $('body').on('click', '#apply_filter', function(){
        var id = $("#vehicle_id").val();
        var url = '{{ route('admin.vehicle-unavailability-calender',':id') }}';
        url = url.replace(':id', id);
        location.href = url;
    });
var FullCalendarStyling = function() {

// External events
var _componentFullCalendarStyling = function() {
    if (typeof FullCalendar == 'undefined') {
        console.warn('Warning - Fullcalendar files are not loaded.');
        return;
    }

    // Define element
    var calendarEventColorsElement = document.querySelector('.fullcalendar-event-colors');

    // Initialize
    if(calendarEventColorsElement) {    
        var id = {{ $vehicle_id }};
        var url = '{{ route('admin.vehicle-unavailability.calender-data',':id') }}';
        url = url.replace(':id', id);
        var calendarEventColorsInit = new FullCalendar.Calendar(calendarEventColorsElement, {
            plugins: [ 'dayGrid', 'interaction' ],
            header: {
                left: 'prev,next',
                center: 'title'
            },
            defaultDate: '{{ date("Y-m-d") }}',
            editable: true,
            displayEventEnd:true,
            events: url ,
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
        }).render();
    }
};
return {
    init: function() {
        _componentFullCalendarStyling();
    }
}
}();


// Initialize module
// ------------------------------

document.addEventListener('DOMContentLoaded', function() {
FullCalendarStyling.init();
});

</script>
@endpush