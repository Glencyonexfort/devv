@extends('layouts.app')
@section('page-title')
@push('head-script')
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css"> -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/tagify-master/dist/tagify.css') }}">
@endpush
<style type="text/css">
    #listing-table_length {
        float: left;
    }

    #listing-table_filter {
        float: right;
    }

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
                    <a href="{{ route('admin.vehicle-unavailability') }}" class="btn btn-link btn-float vehicle-tab-active" title="List View"><i class="icon-list"></i></a>
                    <a href="{{ route('admin.vehicle-unavailability-calender',['id'=>0]) }}" class="btn btn-link btn-float" title="Calender View"><i class="icon-calendar"></i></a>
                </div>
            </div>
        </div>
        
        <div class="card" style="border:0px;">
            <div class="card-header header-elements-inline" style="border:0px;">
                <h6 class="card-title">Search Filters</h6>
                <div class="header-elements">
                    <div class="list-icons">
                        <a class="list-icons-item" data-action="collapse"></a>
                    </div>
                </div>
            </div>

            <div class="card-body pb-0" style="display: block!important;">
                <div class="row" style="background: #fbfbfb;padding: 10px;margin-bottom: 15px;margin-right:0px;" id="div-filters">
    <form action="" id="filter-form" style="width: 100%">
        
        <div class="row">            
            <div class="col-md-4">
                <div class="form-group">
                <label class="txt14 w400">Date Range</label>
                <div class="input-daterange input-group" id="created-date-range">
                    <input type="text" class="form-control" id="from_date" placeholder="From Date" value="{{ $from_date }}" />
                    <span class="input-group-prepend">
                        <span class="input-group-text prepend-txt">@lang('app.to')</span>
                    </span>
                    <input type="text" class="form-control" id="to_date" placeholder="To Date" value="{{ $to_date }}" />
                </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="txt14 w400">Vehicles</label>
                    <div class="multiselect-native-select">
                        <select class="form-control multiselect" multiple="multiple" name="vehicle" id="vehicle">
                            @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_name }}</option>
                            @endforeach
                       
                    </select>
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
    </div>
@endsection

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <div class="row">
                <div class="col-sm-12">
                    <div class="badge-dark pull-left col-sm-12">
                        <div class="col-sm-3 pull-left"><h4 style="margin-top:8px;">Vehicle Unavailability: <span id="totalCount">0</span></h4></div>
                    </div>
                </div>
                <div class="col-sm-6 text-right hidden-xs">
                    
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-12">
                <div class="form-group pull-right">
                    <a href="#" data-toggle="modal" data-target="#add_new_unavailability_popup" class="btn btn-outline btn-success btn-sm">Add Unavailability <i class="fa fa-plus" aria-hidden="true"></i></a>
                </div>
            </div>
            </div>
            <div class="table-responsive">
                    <table class="table table-bordered table-hover toggle-circle default footable-loaded footable" id="listing-table" style="">
                        <thead>
                        <tr>
                            <th>Vehicle</th>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>From Time#</th>
                            <th>To Time</th>
                            <th>Reason</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                    </table>
        </div>
        </div>
    </div>
</div>
<!-- Create Vehicle Unavailability Popup -->
<div id="add_new_unavailability_popup" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header" style="border: 1px solid #f0f0f0;padding-bottom:10px">
                <span style="font-size:18px;font-weight: 400;">Add Vehicle Unavailability</span>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
        
            <form id="create_new_unavailability_form" action="{{ route('admin.crm-leads.ajaxStoreLead') }}" method="post">
                @csrf
            <div class="modal-body">
                    <div class="form-body">                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-size:14px">Vehicle</label>
                                    <select name="vehicle_id" class="form-control">
                                        @foreach($vehicles as $data)
                                            <option value="{{ $data->id }}">{{ $data->vehicle_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label style="font-size:14px">From Date</label>
                                        <div class="input-group">
                                            <span class="input-group-prepend">
                                                <span class="input-group-text"><i class="icon-calendar52"></i></span>
                                            </span>
                                        <input type="text" name="from_date" class="form-control daterange-single" autocomplete="nope">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label style="font-size:14px">From Time</label>
                                        <div class="input-group">
                                            <span class="input-group-prepend">
                                                <span class="input-group-text"><i class="icon-alarm"></i></span>
                                            </span>
                                            <input name="from_time" type="time" class="form-control pickatime-editable" value="{{ date('h:m:s') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <div class="col-md-12">
                                <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label style="font-size:14px">To Date</label>
                                        <div class="input-group">
                                            <span class="input-group-prepend">
                                                <span class="input-group-text"><i class="icon-calendar52"></i></span>
                                            </span>
                                        <input type="text" name="to_date" class="form-control daterange-single" autocomplete="nope">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label style="font-size:14px">To Time</label>
                                        <div class="input-group">
                                            <span class="input-group-prepend"><span class="input-group-text"><i class="icon-alarm"></i></span>
                                            </span>
                                            <input name="to_time" type="time" class="form-control pickatime-editable" value="{{ date('h:m:s') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="font-size:14px">Reason</label>
                                    <textarea name="reason" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>                    
                    <div id="new-vehicle-validation-errors" class="col-md-6 alert alert-danger hidden"></div>
            </div>
            <div class="modal-footer" style="background-color: #f5f5f5!important;padding: 10px 20px!important;">
                <button type="button" class="btn btn-link" data-dismiss="modal" id="close-create-vehicle-unavailability">Cancel</button>
                <button id="create-vehicle-unavailability" type="button" class="btn btn-success">Create</button>
                {{-- <input id="create_leatn" type="submit" value="Create Lead" class="btn btn-success"/> --}}
            
        </div>
            </form>
    </div>
</div>
</div>
<!-- /Create Vehicle Unavailability Popup -->

<!-- /Edit Vehicle Unavailability Popup -->
<div id="edit_unavailability_popup" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header" style="border: 1px solid #f0f0f0;padding-bottom:10px">
                <span style="font-size:18px;font-weight: 400;">Add Vehicle Unavailability</span>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div id="edit_unavailability_grid"></div>
        </div>
    </div>
</div>
<!-- /Edit Vehicle Unavailability Popup -->
@endsection

@push('footer-script')
<script src="{{ asset('plugins/tagify-master/dist/tagify.js') }}"></script> <!-- Theme JS files -->
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('newassets/global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    
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
    <script src="{{ asset('newassets/global_assets/js/demo_pages/form_multiselect.js') }}"></script>
    <script src="{{ asset('newassets/global_assets/js/demo_pages/picker_date.js') }}"></script>
    <script>
        var table;
        $(function() {
            jQuery('#created-date-range, #removal-date-range').datepicker({
                toggleActive: true,
                format: '{{ $global->date_picker_format }}',
                language: '{{ $global->locale }}',
                autoclose: true
            });
            loadTable();
            $('body').on('click', '.sa-params', function(){
                            var id = $(this).data('row-id');
                            swal({
                            title: "Are you sure?",
                                    text: "You will not be able to recover the deleted record!",
                                    type: "warning",
                                    showCancelButton: true,
                                    confirmButtonColor: "#DD6B55",
                                    confirmButtonText: "Yes, delete it!",
                                    cancelButtonText: "No, cancel please!",
                                    closeOnConfirm: true,
                                    closeOnCancel: true
                            }, function(isConfirm){
                            if (isConfirm) {

                            var url = "{{ route('admin.vehicle-unavailability.destroy',':id') }}";
                            url = url.replace(':id', id);
                            var token = "{{ csrf_token() }}";
                            $.easyAjax({
                            type: 'get',
                                    url: url,
                                    //data: {'_token': token, '_method': 'DELETE'},
                                    success: function (response) {
                                    if (response.status == "success") {
                                    $.unblockUI();
//                                    swal("Deleted!", response.message, "success");
                                    table._fnDraw();
                                    }
                                    }
                            });
                            }
                            });
                            });
    $('body').on('click', '#create-vehicle-unavailability', function() {
        var url = "{{ route('admin.vehicle-unavailability.store') }}";
        $.ajax({
            url: url,
            method: 'post',
            data: $("#create_new_unavailability_form").serialize(),
            dataType: "json",
            beforeSend: function() {
                $.blockUI();
            },
            complete: function() {
                $.unblockUI();
            },
            success: function(result) {

                if (result.error == 0) {
                    table._fnDraw();
                    $('#close-create-vehicle-unavailability').click();
                    $('div.modal-backdrop').remove();
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
            },
            error: function( xhr, ajaxOptions, thrownError ) {
                console.log(xhr.responseText);
                var response = JSON.parse(xhr.responseText);
                var errorString = '<ul>';
                $.each( response.errors, function( key, value) {
                    errorString += '<li>' + value + '</li>';
                });
                errorString += '</ul>';
                $('#new-vehicle-validation-errors').html(errorString).show().delay(3000).hide('slow');
        }
        });
    });
    $('body').on('click', '#update-vehicle-unavailability', function() {
        var url = "{{ route('admin.vehicle-unavailability.update') }}";
        $.ajax({
            url: url,
            method: 'post',
            data: $("#update_unavailability_form").serialize(),
            dataType: "json",
            beforeSend: function() {
                $.blockUI();
            },
            complete: function() {
                $.unblockUI();
            },
            success: function(result) {

                if (result.error == 0) {
                    table._fnDraw();
                    $('#close-update-vehicle-unavailability').click();
                    $('div.modal-backdrop').remove();
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
            },
            error: function( xhr, ajaxOptions, thrownError ) {
                console.log(xhr.responseText);
                var response = JSON.parse(xhr.responseText);
                var errorString = '<ul>';
                $.each( response.errors, function( key, value) {
                    errorString += '<li>' + value + '</li>';
                });
                errorString += '</ul>';
                $('#edit-vehicle-validation-errors').html(errorString).show().delay(3000).hide('slow');
        }
        });
    });
            $('#apply-filters').click(function() {
                loadTable();
            });
            $('#reset-filters').click(function() {
                $('#filter-form')[0].reset();
                $('#allocation_status').val('').selectpicker('refresh');
                $('#storage_type').val('').selectpicker('refresh');
                $('#storage_unit').val('').selectpicker('refresh');
                loadTable();
            });
        });
        function loadTable() {
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            var vehicle = $('#vehicle').val();

            if (from_date == '') {
                from_date = null;
            }
            if (to_date == '') {
                to_date = null;
            }
            
            var url = '{!! route("admin.vehicle-unavailability.data") !!}?from_date=' + from_date + '&to_date=' + to_date + '&vehicle=' + vehicle;
            //console.log(url);
            table = $('#listing-table').dataTable({
                "pageLength": 50,
                destroy: true,
                //                                    responsive: true,
                processing: true,
                order: [], //Initial no order.
                aaSorting: [],
                serverSide: true,
                scrollX: true,
                ajax: '{!! route("admin.vehicle-unavailability.data") !!}?from_date=' + from_date + '&to_date=' + to_date + '&vehicle=' + vehicle,
                language: {
                    "url": "<?php echo __("app.datatable") ?>"
                },
                "fnDrawCallback": function(oSettings) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });

                    var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                    };

                    var count = $('#listing-table').DataTable().page.info().recordsTotal;
                    $('#totalCount').html(count);

                    var sum = $('#listing-table').DataTable().column(1).data()
                    .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                    }, 0 );
                    sum = sum.toFixed(2);
                    var nf = new Intl.NumberFormat();
                    //$('#totalVal').html(nf.format(sum));
                },
                columns: [
                    {
                        data: 'vehicle_name',
                        name: 'vehicle_name',
                        width: '20%'
                    },
                    {
                        data: 'from_date',
                        name: 'from_date',
                        width: '13%'
                    },
                    {
                        data: 'to_date',
                        name: 'to_date',
                        width: '13%'
                    },
                    {
                        data: 'from_time',
                        name: 'from_time',
                        width: '12%'
                    },
                    {
                        data: 'to_time',
                        name: 'to_time',
                        width: '12%'
                    },
                    {
                        data: 'reason',
                        name: 'reason',
                        width: '18%'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        width: '12%'
                    }                    
                ]
            });
        }
        
        $('body').on('click', '.edit-vehicle-unavailability', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var url = "{{ route('admin.vehicle-unavailability.edit',':id') }}";
        url = url.replace(':id', id);
        $.ajax({
            url: url,
            method: 'get',
            //data: $("#movingto_form").serialize(),
            dataType: "json",
            beforeSend: function() {
                $.blockUI();
            },
            complete: function() {
                $.unblockUI();
            },
            success: function(result) {

                if (result.error == 0) {
                    $('#edit_unavailability_grid').html(result.html);   
                    $('.daterange-single').daterangepicker({ 
                        singleDatePicker: true,
                        locale: {
                            format: 'DD/MM/YYYY'
                        }
                    });      
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
    });
    </script>
@endpush