@extends('layouts.app')

@section('page-title')
<style type="text/css">
    #listing-table_length {
        float: left;
    }

/*    #listing-table_filter {
        float: right;
    }*/

    .dataTables_filter>label:after {
        top: 67% !important;
    }

    .dataTable thead .sorting:before {
        display: none !important;
    }

    .dropdown-menu>li>a {
        display: block !important;
    }
    .dataTables_filter>label:after{
        display: none!important;
    }
    .row{
        width: 100%!important;
    }

</style>
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="{{ $pageIcon }} mr-2"></i> <span class="font-weight-semibold">{{ $pageTitle }}</span></h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-list"></i></a>
        </div>
    </div>

    <!-- search -->
    <div class="card" style="border:0px;">
        <div class="card-header header-elements-inline" style="border:0px;margin: 0 14px;">
            <h6 class="card-title">Search Filters</h6>
            <div class="header-elements">
                <div class="list-icons">
                    <a class="list-icons-item" data-action="collapse"></a>
                </div>
            </div>
        </div>

        <div class="card-body pb-0">
            <div class="row" style="display:''; background: #fbfbfb;padding: 10px;margin-bottom: 15px;margin-right:0px; margin: 10px;" id="div-filters">
            <?php
                $sorting_order_array = ['created_at'=>'Created Date', 'id'=>'Lead', 'lead_status'=>'Status'];
            ?>
            <form action="" id="filter-form" style="width: 100%">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                        <label class="txt14 w400">@lang('app.report.dateRange')</label>
                        <div class="input-daterange input-group" id="created-date-range">
                            <input type="text" class="form-control" name="created_date_start" id="created_date_start" placeholder="@lang('app.startDate')" value="{{ $from_date }}" />
                            <span class="input-group-prepend">
                                <span class="input-group-text prepend-txt">@lang('app.to')</span>
                            </span>
                            <input type="text" class="form-control" name="created_date_end" id="created_date_end" placeholder="@lang('app.endDate')" value="{{ $to_date }}" />
                        </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="txt14 w400 col-lg-12">@lang('app.menu.vehicle')</label>
                            <div>
                                <div class="col-lg-12 pull-left">
                                    <select class="form-control" name="vehicle_id" id="sorting_order">
                                        <option value="">@lang('app.report.noneSelected')</option>
                                        @foreach ($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="txt14 w400 col-lg-12">@lang('app.status')</label>
                            <div>
                                <div class="col-lg-12 pull-left">
                                    <select class="form-control" name="status" id="sorting_order">
                                        <option value="">@lang('app.report.noneSelected')</option>
                                        @foreach ($status as $sta)
                                            <option value="{{ $sta->list_option }}">{{ $sta->list_option }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-4" style="margin-top: 28px!important;">
                        <label class="control-label">&nbsp;</label>
                        <button type="button" id="apply-filters" class="btn btn-success wide-btn"><i class="fa fa-check"></i> @lang('app.apply')</button>
                        <button type="button" id="reset-filters" class="btn bg-slate-700 wide-btn"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>
    <!-- /search area -->
</div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <div class="row mb-3">
                <div class="col-md-8">
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#add_new_trip_popup">Add New Trip <i class="icon-plus3 ml-2"></i></button>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <label class="col-form-label col-md-3">Search:</label>
                        <div class="col-md-9">
                            <input type="text" id="search" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive trip-table">
                <table class="w3-table w3-striped w3-border table-bordered table-hover toggle-circle" id="tableData">
                    <tr>
                        <th style="width: 30%">Trip</th>
                        <th style="width: 15%">Dates</th>
                        <th style="width: 25%">Capacity Loading</th>
                        <th style="width: 23%">Jobs</th>
                        <th style="width: 7%">Action</th>
                    </tr>
                    @if ($trips != null)
                        @foreach ($trips as $trip)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.backloading.assignJob', ['trip_id'=>$trip['id']]) }}">{{ $trip['trip_name'] }}</a> {{ $trip['start_city'] }}, {{ $trip['finish_city'] }} {{ $trip['license_plate_number'] }} {{ $trip['vehicle_name'] }} {{ $trip['waybill_number'] }}
                                </td>
                                <td>
                                    {{ date('d/m/y',strtotime($trip['start_date'])) }} - {{ date('d/m/y',strtotime($trip['finish_date'])) }}
                                </td>
                                <td>
                                    <div class="card card-body border-top-primary">
                                        <div class="progress rounded-pill" style="height: 20px;">
                                            <div class="progress-bar bg-teal" style="width: {{ $trip['capacity_loading'] }}%">
                                                <span>{{ $trip['capacity_loading'] }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                <td>
                                    @if ($trip['all_jobs'] != null)
                                        @foreach ($trip['all_jobs'] as $job)
                                            <a href="{{ route("admin.list-jobs.view-job", $job['job_id']) }}" target="_blank" type="button" class="btn btn-primary">{{ $job['job_number'] }}</a> 
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    <div class="list-icons float-right">
                                        <div class="dropdown">
                                            <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown"><i class="icon-menu"></i></a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a href="#" class="delete_backloading_trip dropdown-item" data-tripid="{{ $trip['id'] }}" style="color: #ff0000"><i class="icon-trash"></i> Delete</a>        
                                                </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
<!-- .row -->

<!-- START: Create New Trip Popup -->
<div id="add_new_trip_popup" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="border: 1px solid #f0f0f0;padding-bottom:10px">
                <span style="font-size:25px; font-weight: 500;">Create New Trip</span>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="add_new_trip_form">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Trip Name</label>
                                <input type="text" name="trip_name" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>From City</label>
                                <input type="text" name="start_city" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>To City</label>
                                <input type="text" name="finish_city" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Estimate Start Date</label>
                                <div class="input-group">
                                    <span class="input-group-prepend">
                                        <span class="input-group-text"><i class="icon-calendar52"></i></span>
                                    </span>
                                    <input type="date" id="start_date" name="start_date" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Estimate Finish Date</label>
                                <div class="input-group">
                                    <span class="input-group-prepend">
                                        <span class="input-group-text"><i class="icon-calendar52"></i></span>
                                    </span>
                                    <input type="date" id="finish_date" name="finish_date" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Vehicle</label>
                                <select name="vehicle_id" class="form-control">
                                    <option value=""></option>
                                    @if (count($vehicles))
                                        @foreach ($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_name }} {{ $vehicle->license_plate_number }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Driver</label>
                                <select name="driver_id" class="form-control">
                                    <option value=""></option>
                                    @if (count($drivers))
                                        @foreach ($drivers as $driver)
                                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Trip Notes</label>
                                <textarea name="trip_notes" id="trip_notes" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer mt-3" style="background-color: #f5f5f5!important;padding: 10px 20px!important;">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                        <button id="create_new_trip" type="button" class="btn btn-success">Create Trip</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /END Create New Trip Popup -->

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
<script src="{{ asset('newassets/global_assets/js/plugins/forms/selects/bootstrap_multiselect.js') }}"></script>

<script>
var table;
$(function() {

    jQuery('#created-date-range, #removal-date-range').datepicker({
        toggleActive: true,
        format: '{{ $global->date_picker_format }}',
        language: '{{ $global->locale }}',
        autoclose: true
    });

    $('#apply-filters').click(function(e) {                
        e.preventDefault();
        if($('#created_date_start').val())
        {
            $.ajax({
                type: "GET",
                url: '{{ route('admin.backloading-getData') }}',
                data: $('#filter-form').serialize(),
                beforeSend: function() {
                    $(".preloader").show();
                },
                complete: function() {
                    $(".preloader").hide();
                },
                success: function (response) {
                    if(response.error == 0)
                    {
                        $('.trip-table').html(response.html);
                    }
                    else
                    {
                       //Notification....
                        $.toast({
                            heading: 'Error',
                            text: 'Something went wrong',
                            icon: 'error',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#fb9678',
                            textColor: 'white'
                        });
                        //..
                    }

                },
                error: function(error){
                    alert("Something Went Wrong");
                }
            });
        }
    });
        
    $('#reset-filters').click(function() {
        $('#filter-form')[0].reset();
        $('#user_id').val('').selectpicker('refresh');
        $("#tableData tr td").detach();
        location.reload(true);
        // loadTable();
    });

    //START Create New Trip
    $('body').on('click', '#create_new_trip', function () {
        var start = new Date($('#start_date').val());
        var finish = new Date($('#finish_date').val());
        if(finish >= start)
        {
            $.ajax({
                url: "{{ route('admin.backloading.store') }}",
                method: 'POST',
                data: $('#add_new_trip_form').serialize(),
                dataType: "json",
                beforeSend: function() {
                    $(".preloader").show();
                },
                complete: function() {
                    $(".preloader").hide();
                },
                success: function(result) {
                    if (result.error == 0) {
                        // $('#storage_tab_btn').click();
                        //Notification....
                        $.toast({
                            heading: 'Success',
                            text: result.message,
                            icon: 'success',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#00c292',
                            textColor: 'white'
                        });
                        //..
                        window.location.reload();
                    } else {
                        //Notification....
                        $.toast({
                            heading: 'Error',
                            text: 'Something went wrong!',
                            icon: 'error',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#fb9678',
                            textColor: 'white'
                        });
                        //..
                    }
                }
            });
        }
        else{
            //Notification....
            $.toast({
                heading: 'Error',
                text: 'Finish Date must be greater and equal Start Date',
                icon: 'error',
                position: 'top-right',
                loader: false,
                bgColor: '#fb9678',
                textColor: 'white'
            });
            //..
        }
        
    });
    //end:: Create New Trip

    //Delete Backloading Trip
    $('body').on('click', '.delete_backloading_trip', function () {
        var trip_id = $(this).data('tripid');;
        var _token = $('input[name="_token"]').val();
        swal({
            title: "Are you sure?",
            text: "You sure you want to delete this reservation?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#00c292",
            confirmButtonText: "Yes, Confirm!",
            cancelButtonText: "No, Cancel!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: "{{ route('admin.backloading.destroy') }}",
                    method: 'POST',
                    data: { '_token': _token, 'trip_id': trip_id},
                    dataType: "json",
                    beforeSend: function() {
                        $(".preloader").show();
                    },
                    complete: function() {
                        $(".preloader").hide();
                    },
                    success: function(result) {
                        if (result.error == 0) {
                            // $('#storage_tab_btn').click();
                            //Notification....
                            $.toast({
                                heading: 'Success',
                                text: result.message,
                                icon: 'success',
                                position: 'top-right',
                                loader: false,
                                bgColor: '#00c292',
                                textColor: 'white'
                            });
                            //..
                            window.location.reload();
                        } else {
                            //Notification....
                            $.toast({
                                heading: 'Error',
                                text: 'Something went wrong!',
                                icon: 'error',
                                position: 'top-right',
                                loader: false,
                                bgColor: '#fb9678',
                                textColor: 'white'
                            });
                            //..
                        }
                    }
                });
            }
        });
    });
    //end:: delete Backloading Trip

    //START Create New Trip
    $("body").off('keyup', '#search').on('keyup', '#search', function(e) {
        e.preventDefault();
        var search = $('#search').val();
        var created_date_start = $('#created_date_start').val();
        var created_date_end = $('#created_date_end').val();
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: "{{ route('admin.backloading.search') }}",
            method: 'GET',
            data: {
                '_token': _token,
                'search': search,
                'created_date_start': created_date_start,
                'created_date_end': created_date_end
            },
            dataType: "json",
            beforeSend: function() {
                $(".preloader").show();
            },
            complete: function() {
                $(".preloader").hide();
            },
            success: function(result) {
                if(result.error == 0)
                    {
                        $('.trip-table').html(result.html);
                    }
                    else
                    {
                       //Notification....
                        $.toast({
                            heading: 'Error',
                            text: 'Something went wrong',
                            icon: 'error',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#fb9678',
                            textColor: 'white'
                        });
                        //..
                    }
            }
        });
        
    });
    //end:: Create New Trip
});
</script>
@endpush