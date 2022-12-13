@extends('layouts.app')

@section('page-title')
<div class="page-header page-header-light view_blade_page_header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex view_blade_page_padding">
            <h4>
                <img class="view_blade_page_img_header" src="../../../../../newassets/img/statistics (1)@2x.png">
                <span class="view_blade_page_span_header">List Jobs </span>
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
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css"> -->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
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

    .dataTables_filter>label:after {
        display: none !important;
    }

    .row {
        width: 100% !important;
    }
</style>
@section('content')
<!-- <div class="white-box"> -->
<div class="card card-collapsed" style="border:0px;">
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
            ?>
            <form action="" id="filter-form" style="width: 100%">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="txt14 w400">@lang('modules.listJobs.job_status')</label>
                            <div class="multiselect-native-select">
                                <select class="form-control multiselect" multiple="multiple" name="job_status" id="job_status">
                                    @foreach($job_status as $rs)
                                    <option value="{{ $rs->options }}">{{ ucwords($rs->options) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="txt14 w400">@lang('modules.listJobs.payment_status')</label>
                            <div class="multiselect-native-select">
                                <select class="form-control multiselect" multiple="multiple" name="payment_status" id="payment_status">
                                    @foreach($payment_status_array as $rs=>$val)
                                    <option value="{{ $rs }}">{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" style="margin-top: 36px!important;">
                            <label></label>
                            <input type="checkbox" name="hide_deleted_archived" value="1" id="hide_deleted_archived"> <label class="txt14 w400">@lang('modules.listJobs.hide_deleted_archived')</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="txt14 w400">@lang('modules.listJobs.created_date')</label>
                            <div class="input-daterange input-group" id="created-date-range">
                                <input type="text" class="form-control" id="created_date_start" placeholder="@lang('app.startDate')" value="" />
                                <span class="input-group-prepend">
                                    <span class="input-group-text prepend-txt">@lang('app.to')</span>
                                </span>
                                <input type="text" class="form-control" id="created_date_end" placeholder="@lang('app.endDate')" value="" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="txt14 w400">@lang('modules.listJobs.job_date')</label>
                            <div class="input-daterange input-group" id="removal-date-range">
                                <input type="text" class="form-control" id="job_date_start" placeholder="@lang('modules.listJobs.job_date')" value="" />
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
    <div class="table-responsive">
        <table class="table table-bordered table-hover toggle-circle default footable-loaded footable general-table" id="listing-table">
            <thead>
                <tr>
                    <th width="8%">Job#</th>
                    <th>Customer Name</th>
                    <th width="9%">Job Date</th>
                    <th width="12%">Email</th>
                    <th width="10%">Mobile</th>
                    <th width="20%">Job Address</th>
                    <th width="10%">Job Status</th>
                    <th width="10%">Payment Status</th>
                    <th width="9%">Balance Payment</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@endsection

@push('footer-script')

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
<script>
    var table;
    $(function() {
        jQuery('#created-date-range, #removal-date-range').datepicker({
            toggleActive: true,
            format: '{{ $global->date_picker_format }}',
            language: '{{ $global->locale }}',
            autoclose: true,
        });
        loadTable();
        $('body').on('click', '.sa-params', function() {
            var id = $(this).data('row-id');
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover the deleted email template!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm) {
                if (isConfirm) {
                    var url = "{{ route('admin.job-templates.destroy',':id') }}";
                    url = url.replace(':id', id);
                    var token = "{{ csrf_token() }}";
                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {
                            '_token': token,
                            '_method': 'DELETE'
                        },
                        success: function(response) {
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
        $('#apply-filters').click(function() {
            loadTable();
        });
        $('#reset-filters').click(function() {
            $('#filter-form')[0].reset();
            $('#job_status').val('').selectpicker('refresh');
            $('#payment_status').val('').selectpicker('refresh');
            $('#subcontractor').val('').selectpicker('refresh');
            loadTable();
        });
    });

    function loadTable() {
        var created_date_start = $('#created_date_start').val();
        var created_date_end = $('#created_date_end').val();
        var job_date_start = $('#job_date_start').val();
        var job_status = $('#job_status').val();
        var payment_status = $('#payment_status').val();
        var subcontractor = $('#subcontractor').val();
        var hide_deleted_archived = '0';
        if (created_date_start == '') {
            created_date_start = null;
        }
        if (created_date_end == '') {
            created_date_end = null;
        }
        if (job_date_start == '') {
            job_date_start = null;
        }
        if ($("#hide_deleted_archived").is(':checked')) {
            hide_deleted_archived = '1';
        }
        table = $('#listing-table').dataTable({
            "pageLength": 50,
            destroy: true,
            autoWidth: false,
            // responsive: true,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: '{!! route("admin.list-jobs-cleaning.data") !!}?created_date_start=' + created_date_start + '&created_date_end=' + created_date_end + '&job_date_start=' + job_date_start + '&job_status=' + job_status + '&payment_status=' + payment_status + '&subcontractor=' + subcontractor + '&hide_deleted_archived=' + hide_deleted_archived,
            "order": [
                [0, "desc"]
            ],
            language: {
                "url": "<?php echo __("app.datatable") ?>"
            },
            "fnDrawCallback": function(oSettings) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            columns: [{
                    data: 'job_number',
                    name: 'job_number'
                },
                {
                    data: 'customer_name',
                    name: 'customer_name'
                },
                {
                    data: 'job_date',
                    name: 'job_date',
                },
                {
                    data: 'email',
                    name: 'email',
                },
                {
                    data: 'mobile',
                    name: 'mobile',
                },
                {
                    data: 'address',
                    name: 'address',
                    width: '15%'
                },
                {
                    data: 'job_status',
                    name: 'job_status',
                },
                {
                    data: 'payment_status',
                    name: 'payment_status',
                },
                {
                    data: 'balance_payment',
                    name: 'balance_payment',
                }
                //  { data: 'action', name: 'action', width: '10%' }
            ]
        });
    }
</script>
@endpush