@extends('layouts.app')

@section('page-title')
    <div class="row bg-title" style="margin-bottom:5px;">
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
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css"> -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
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

    .dataTables_filter>label:after {
        display: none !important;
    }

    .row {
        width: 100% !important;
    }
    td>label{
        color:white !important;
        text-align: center;
    }
    .dataTables_length {
        float: left !important;
    }
    .dataTables_filter {
        float: right !important;
    }
</style>
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">

            <div class="card card-collapsed" style="border:0px;margin-bottom:6px;">
                <div class="card-header header-elements-inline" style="border:0px;">
                    <h6 class="card-title">Search Filters</h6>
                    <div class="header-elements">
                        <div class="list-icons">
                            <a class="list-icons-item" data-action="collapse"></a>
                        </div>
                    </div>
                </div>

                    <div class="card-body pb-0">
        <div class="row" style="display:''; background: #fbfbfb;padding: 10px;margin-bottom: 15px;" id="div-filters">
            <?php
            $payment_status_array = ['all' => 'All', 'unpaid' => 'Unpaid', 'paid' => 'Paid', 'partial' => 'Partial'];
            $statusToSelect = ['Completed'];
            ?>
            <form action="" id="filter-form" style="width: 100%">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="txt14 w400">@lang('modules.listJobs.job_status')</label>
                            <div class="multiselect-native-select">
                                <select class="form-control multiselect" multiple="multiple" name="job_status" id="job_status">
                                @foreach($job_status as $rs)
                                <option value="{{ $rs->options }}" @if(in_array($rs->options, $statusToSelect)) selected="" @endif>{{ ucwords($rs->options) }}</option>
                                @endforeach
                            </select>
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-md-4">

                        <div class="form-group">
                            <label class="txt14 w400">Invoice Status</label>
                            <select class="form-control" name="status" id="status" data-style="form-control">
                                @foreach($payment_status_array as $rs=>$val)
                                <option value="{{ $rs }}" @if($rs=="unpaid") selected="" @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">

                            <label class="txt14 w400">@lang('modules.listJobs.created_date')</label>
                            <div class="input-daterange input-group" id="date-range">
                                <input type="text" class="form-control" id="start-date" placeholder="@lang('app.startDate')" value="" />
                                <span class="input-group-prepend">
                                    <span class="input-group-text prepend-txt">@lang('app.to')</span>
                                </span>
                                <input type="text" class="form-control" id="end-date" placeholder="@lang('app.endDate')" value="" />
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



            <div class="white-box">
                <div class="row">
                    <!-- <div class="col-sm-6">
                        <div class="form-group">
                            <a href="{{ route('admin.all-invoices.create') }}" 
                               class="btn btn-outline btn-success btn-sm hidden">@lang('modules.invoices.addInvoice') <i class="fa fa-plus" aria-hidden="true"></i>
                            </a>
                            <a href="javascript:;" id="toggle-filter" class="btn btn-outline btn-danger btn-sm toggle-filter"><i
                                        class="fa fa-sliders"></i> @lang('app.filterResults')</a>
                        </div>
                    </div> -->
                    <div class="col-sm-6 text-right hidden">
                        <div class="form-group">
                            <a href="javascript:;" onclick="exportData()" class="btn btn-info btn-sm"><i class="ti-export" aria-hidden="true"></i> @lang('app.exportExcel')</a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover toggle-circle default footable-loaded footable general-table" id="invoice-table">
                        <thead>
                        <tr>
                            {{-- <th>@lang('app.id')</th> --}}
                            <th>@lang('app.invoice') #</th>
                            <th>@lang('app.systemJobType')</th>
                            <th>@lang('app.jobNo')</th>
                            <th>@lang('app.customer')</th>
                            {{-- <th>@lang('modules.invoices.invoiceDate')</th> --}}
                            <th>@lang('modules.invoices.dueDate')</th>
                            <th>Issue Date</th>
                            <th>@lang('modules.invoices.total')</th>
                            <th>@lang('modules.invoices.paymentRecieved')</th>
                            <th>@lang('app.status')</th>
                            {{-- <th>@lang('app.action')</th>  --}}
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- .row -->

@endsection

@push('footer-script')
    <script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
    <script src="{{ asset('newassets/global_assets/js/plugins/forms/selects/bootstrap_multiselect.js') }}"></script>
    <script src="{{ asset('newassets/global_assets/js/demo_pages/form_multiselect.js') }}"></script>

    <script>
        $(".select2").select2({
            formatNoMatches: function () {
                return "{{ __('messages.noRecordFound') }}";
            }
        });
        var table;
        $(function() {
            loadTable();
            jQuery('#date-range').datepicker({
                toggleActive: true,
                format: '{{ $global->date_picker_format }}',
                language: '{{ $global->locale }}',
                autoclose: true
            });
            $('body').on('click', '.sa-params', function(){
                var id = $(this).data('invoice-id');
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover the deleted invoice!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel please!",
                    closeOnConfirm: true,
                    closeOnCancel: true
                }, function(isConfirm){
                    if (isConfirm) {
                        var url = "{{ route('admin.all-invoices.destroy',':id') }}";
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
                                    table._fnDraw();
                                }
                            }
                        });
                    }
                });
            });
        });
        function loadTable(){
            var startDate = $('#start-date').val();
            if (startDate == '') {
                startDate = null;
            }
            var endDate = $('#end-date').val();
            if (endDate == '') {
                endDate = null;
            }
            var status = $('#status').val();
            var job_status = $('#job_status').val();
            table = $('#invoice-table').dataTable({
                pageLength: 100,
                responsive: true,
                destroy: true,
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: '{!! route('admin.all-invoices.data') !!}?startDate=' + startDate + '&endDate=' + endDate + '&status=' + status + '&job_status=' + job_status,
                "order": [[ 0, "desc" ]],
                language: {
                    "url": "<?php echo __("app.datatable") ?>"
                },
                "fnDrawCallback": function( oSettings ) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                columns: [
//                    { data: 'id', name: 'id' },
                    { data: 'invoice_number', name: 'invoice_number' },
                    { data: 'sys_job_type', name: 'sys_job_type' },
                    { data: 'job_id', name: 'job_id' },
                    { data: 'name', name: 'name' },
                    { data: 'due_date', name: 'due_date' },
                    { data: 'issue_date', name: 'issue_date' },
                    { data: 'total', name: 'total' },
                    { data: 'payment_recieved', name: 'payment_recieved' },
                    { data: 'status', name: 'status' },
                    //{ data: 'action', name: 'action', width: '12%' }
                ]
            });
        }
        $('.toggle-filter').click(function () {
            $('#ticket-filters').toggle('slide');
        })
        $('#apply-filters').click(function () {
            loadTable();
        });
        $('#reset-filters').click(function () {
            $('#filter-form')[0].reset();
            $('#projectID').val('all');
            $('#clientID').val('all');
            $('#status').val('all');
            $('#projectID').select2();
            $('#clientID').select2();
            loadTable();
        })
        function exportData(){
            var startDate = $('#start-date').val();
            if (startDate == '') {
                startDate = null;
            }
            var endDate = $('#end-date').val();
            if (endDate == '') {
                endDate = null;
            }
            var status = $('#status').val();
            var projectID = $('#projectID').val();
            var url = '{{ route('admin.all-invoices.export', [':startDate', ':endDate', ':status', ':projectID']) }}';
            url = url.replace(':startDate', startDate);
            url = url.replace(':endDate', endDate);
            url = url.replace(':status', status);
            url = url.replace(':projectID', projectID);
            window.location.href = url;
        }
    </script>
@endpush