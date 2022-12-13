@extends('layouts.app')
@section('page-title')

@push('head-script')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
@endpush
<style>
    .attachment-img {
    width: 24px;
    margin-right: 3px;
    }
    .files input {
        outline: 2px dashed #92b0b3;
        outline-offset: -10px;
        -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
        transition: outline-offset .15s ease-in-out, background-color .15s linear;
        padding: 120px 0px 85px 35%;
        text-align: center !important;
        margin: 0;
        width: 100% !important;
    }
    .files input:focus{     outline: 2px dashed #92b0b3;  outline-offset: -10px;
        -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
        transition: outline-offset .15s ease-in-out, background-color .15s linear; border:1px solid #92b0b3;
    }
    .files{ position:relative}
    .files:after {  pointer-events: none;
        pointer-events: none;
        position: absolute;
        top: 35px;
        left: 240px;
        width: 100%;
        content: "\f0ee";
        font-family: FontAwesome;
        display: block;
        margin: 0 auto;
        background-size: 100%;
        background-repeat: no-repeat;
        font-size: 80px;
        opacity: 0.4;
    }
    .color input{ background-color:#f1f1f1;}
    .files:before {
        position: absolute;
        bottom: 0px;
        font-size: 22px;
        left: 0;  pointer-events: none;
        width: 100%;
        right: 0;
        height: 57px;
        content: " or drag it here. ";
        display: block;
        margin: 0 auto;
        color: #999;
        font-weight: 600;
        text-align: center;
    }
    .attachment-link{
        font-size: 16px;
        color: #f44336!important;
        font-weight: 400;
    }
</style>
<div class="page-header page-header-light view_blade_page_header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex view_blade_page_padding">
            <h4>
            <img class="view_blade_page_img_header" src="../../../../../newassets/img/statistics (1)@2x.png">
                <span class="view_blade_page_span_header">Backloading - Assign Jobs </span>
        </div>
    </div>
    <!-- <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> @lang('app.menu.home')</a>
                <a href="content_page_header.html" class="breadcrumb-item">&nbsp;</a>
                <span class="breadcrumb-item active">{{ $pageTitle }}</span>
            </div>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div> -->
</div>
@endsection

@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/tagify-master/dist/tagify.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
    
@endpush

@section('content')
<div class="row">
    <div class="col-12 col-lg-3 px-2">        
        <div class="text-left lead_title">
            <div class="card card-body border-top-primary" style="margin-bottom: 0px;">
                <div class="progress rounded-pill" style="height: 20px;">
                    <div class="progress-bar bg-teal" id="barData" style="width: {{ $barData['sum'] }}%">
                        <span>{{ $barData['sum'] }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card view_blade_4_card">
                <span class="view_blade_4_card_span">
                    <div class="card-header header-elements-inline view_blade_4_card_header">
                        <h6 class="card-title card-title-mg view_blade_4_card_task">EDIT TRIP</h6>
                        {{-- <div class="header-elements">
                            <div class="list-icons">
                                <span class="cursor-pointer add_new_contact_btn">
                                    <img src="{{ asset('newassets/img/icon-add.png') }}">
                                </span>
                            </div>
                        </div> --}}
                    </div>
                </span>
                <div id="trip_side_grid" class="card-body p10">
                    @include('admin.backloading.trip_side_grid')
                </div>
            </div>
        </div>

    <!-- Right content -->
    <div class="col-12 col-lg-9 px-2 pt-2">   
        <ul class="nav nav-tabs view_blade_5_navs_tabbs_box_shadow nobackground">
            <li class="nav-item noborder"><a href="#assignedjobs" class="nav-link active view_blade_5_navs_tabbs_nav_item_link" data-toggle="tab">Assigned Jobs</a></li>
            <li class="nav-item noborder"><a href="#detail_tab" class="nav-link view_blade_5_navs_tabbs_nav_item_link" data-toggle="tab"></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="assigned_jobs">
                <div class="row">
                    <div class="table-responsive col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <span class="font-weight-bold" style="padding: 10px; text: bold;">Jobs</span>
                            </div>
                            @php
                                // dd($trip);
                            @endphp
                            <div class="col-md-6" style="text-align: right">
                                <button type="button" id="generate_waybill" class="btn btn-sm btn-light" data-trip_id="{{ $trip->id }}" ><i class="icon-clipboard3"></i> Generate Waybill</button>
                                <button type="button" id="download_waybill" class="btn btn-sm btn-light ml-2" data-trip_id="{{ $trip->id }}" @if (empty($trip) || $trip->waybill_file_name == null) disabled @endif><i class="icon-file-pdf"></i> Download</button>
                            </div>
                        </div>
                        <table class="table table-bordered table-hover toggle-circle default footable-loaded footable" id="listing-table-jobs" style="">
                            <thead>
                                <tr>
                                    <th>Job #</th>
                                    <th>Leg #</th>
                                    <th>Name</th>
                                    <th>CBM</th>
                                    <th>Job Date</th>
                                    <th>Pickup Suburb</th>
                                    <th>Drop Off Suburb</th>
                                    <th>Job Status</th>
                                    <th>Payment Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="table-responsive col-md-12">
                        <span class="font-weight-bold" style="text: bold;"><b>Assign Jobs</b></span>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="">
                                    <div class="row" style="background: #fbfbfb;padding: 10px 0px;margin-bottom: 15px; margin: 10px 0px;" id="div-filters">
                                    <?php
                                        $sorting_order_array = ['created_at'=>'Created Date', 'id'=>'Lead', 'lead_status'=>'Status'];
                                    ?>
                                    <form action="" id="job-filter-form" style="width: 100%">
                                        @csrf
                                        <input type="hidden" id="trip_id" value="{{ $trip->id }}">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                <label class="txt14 w400">@lang('app.report.dateRange')</label>
                                                <div class="input-daterange input-group" id="created-date-range">
                                                    <input type="text" class="form-control" name="created_date_start" id="created_date_start_job" placeholder="@lang('app.startDate')" value="{{ $from_date }}" />
                                                    <span class="input-group-prepend">
                                                        <span class="input-group-text prepend-txt">@lang('app.to')</span>
                                                    </span>
                                                    <input type="text" class="form-control" name="created_date_end" id="created_date_end_job" placeholder="@lang('app.endDate')" value="{{ $to_date }}" />
                                                </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6" style="margin-top: 28px!important;">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" id="apply-job-filters" class="btn btn-success wide-btn"><i class="fa fa-check"></i> @lang('app.apply')</button>
                                                <button type="button" id="reset-job-filters" class="btn bg-slate-700 wide-btn"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                                            </div>
                                        </div>
                                    </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="table table-bordered table-hover toggle-circle default footable-loaded footable" id="listing-table-assign-jobs" style="">
                            <thead>
                                <tr>
                                    <th>Job #</th>
                                    <th>Leg #</th>
                                    <th>Name</th>
                                    <th>CBM</th>
                                    <th>Job Date</th>
                                    <th>Pickup Suburb</th>
                                    <th>Drop Off Suburb</th>
                                    <th>Job Status</th>
                                    <th>Payment Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <!-- /timeline -->

            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('footer-script')
        

    <script src="{{ asset('plugins/tagify-master/dist/tagify.js') }}"></script> <!-- Theme JS files -->
    <script src="{{ asset('newassets/global_assets/js/plugins/forms/selects/select2.min.js') }}"></script>
    <script src="{{ asset('newassets/global_assets/js/plugins/editors/summernote/summernote.min.js') }}"></script>
	<script src="{{ asset('newassets/global_assets/js/plugins//forms/styling/uniform.min.js') }}"></script>    
    <script src="{{ asset('newassets/global_assets/js/demo_pages/picker_date.js') }}"></script>   
    <script src="{{ asset('js/ajax-requests/ajax-requests-trip-assign-jobs.js') }}"></script>    
    <script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>

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
    <script src="{{ asset('plugins/bower_components/sweetalert/sweetalert2.min.js') }}"></script>
    <script>
        $(function() {
            jQuery('#created-date-range').datepicker({
                toggleActive: true,
                format: '{{ $global->date_picker_format }}',
                language: '{{ $global->locale }}',
                autoclose: true
            });
        loadTableJobs();
        loadTableAssignJobs(); 
        
    }); 

    function loadTableJobs()
    {
        table = $('#listing-table-jobs').dataTable({
            "pageLength": 5,
            searching: false,
            paging: true,
            info: true,
            bLengthChange: false,
            destroy: true,
            //                                    responsive: true,
            processing: true,
            order: [], //Initial no order.
            aaSorting: [],
            serverSide: true,
            scrollX: true,
            ajax: '{!! route('admin.backloading.getTripJobs') !!}?trip_id=' + {{ $trip->id }},
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

                var count = $('#listing-table-jobs').DataTable().page.info().recordsTotal;
                $('#totalCount').html(count);

                var sum = $('#listing-table-jobs').DataTable().column(1).data()
                .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                }, 0 );
                sum = sum.toFixed(2);
                var nf = new Intl.NumberFormat();
                //$('#totalVal').html(nf.format(sum));
            },
            columns: [
                {
                    data: 'job_number',
                    name: 'job_number',
                    width: '5%'
                },
                {
                    data: 'leg_number',
                    name: 'leg_number',
                    width: '5%'
                },
                {
                    data: 'name',
                    name: 'name',
                    width: '15%'
                },
                {
                    data: 'cbm',
                    name: 'cbm',
                    width: '5%'
                },
                {
                    data: 'job_date',
                    name: 'job_date',
                    width: '15%'
                },
                {
                    data: 'pickup_suburb',
                    name: 'pickup_suburb',
                    width: '15%'
                },
                {
                    data: 'drop_off_suburb',
                    name: 'drop_off_suburb',
                    width: '15%'
                },
                {
                    data: 'job_status',
                    name: 'job_status',
                    width: '10%'
                },
                {
                    data: 'payment_status',
                    name: 'payment_status',
                    width: '10%'
                },
                { 
                    data: 'action',
                    name: 'action', 
                    width: '5%' 
                },
            ]
        });
    }

    function loadTableAssignJobs()
    {
        var trip_id = $('#trip_id').val();
        var created_date_start = $('#created_date_start_job').val();
        var created_date_end = $('#created_date_end_job').val();

        table = $('#listing-table-assign-jobs').dataTable({
            "pageLength": 5,
            searching: false,
            paging: true,
            info: true,
            bLengthChange: false,
            destroy: true,
            //                                    responsive: true,
            processing: true,
            order: [], //Initial no order.
            aaSorting: [],
            serverSide: true,
            scrollX: true,
            ajax: '/admin/moving-settings/get-search-jobs?trip_id=' + trip_id + '&created_date_start=' + created_date_start + '&created_date_end=' + created_date_end,
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

                var count = $('#listing-table-assign-jobs').DataTable().page.info().recordsTotal;
                $('#totalCount').html(count);

                var sum = $('#listing-table-assign-jobs').DataTable().column(1).data()
                .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                }, 0 );
                sum = sum.toFixed(2);
                var nf = new Intl.NumberFormat();
                //$('#totalVal').html(nf.format(sum));
            },
            columns: [
                {
                    data: 'job_number',
                    name: 'job_number',
                    width: '5%'
                },
                {
                    data: 'leg_number',
                    name: 'leg_number',
                    width: '5%'
                },
                {
                    data: 'name',
                    name: 'name',
                    width: '15%'
                },
                {
                    data: 'cbm',
                    name: 'cbm',
                    width: '5%'
                },
                {
                    data: 'job_date',
                    name: 'job_date',
                    width: '15%'
                },
                {
                    data: 'pickup_suburb',
                    name: 'pickup_suburb',
                    width: '15%'
                },
                {
                    data: 'drop_off_suburb',
                    name: 'drop_off_suburb',
                    width: '15%'
                },
                {
                    data: 'job_status',
                    name: 'job_status',
                    width: '10%'
                },
                {
                    data: 'payment_status',
                    name: 'payment_status',
                    width: '10%'
                },
                { 
                    data: 'action',
                    name: 'action', 
                    width: '5%' 
                },
            ]
        });
    }
</script>
@endpush