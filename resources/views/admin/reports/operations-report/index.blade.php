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
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <!-- search -->
    <div class="card" style="border:0px;">
        <div class="card-header header-elements-inline" style="border:0px;">
            <h6 class="card-title">Search Filters</h6>
            <div class="header-elements">
                <div class="list-icons">
                    <a class="list-icons-item" data-action="collapse"></a>
                </div>
            </div>
        </div>


        <div class="card-body pb-0">
            <div class="row" style="display:''; background: #fbfbfb;padding: 10px;margin-bottom: 15px;margin-right:0px;" id="div-filters">
            <?php
                $sorting_order_array = ['created_at'=>'Created Date', 'id'=>'Lead', 'lead_status'=>'Status'];
            ?>
            <form action="" id="filter-form" style="width: 100%">
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
                    <div class="col-md-4">
                        <div class="form-group">
                        <label class="txt14 w400 col-lg-12">@lang('app.report.user')</label>
                        <div>
                            <div class="col-lg-12 pull-left">
                                <select class="form-control" name="user_id" id="sorting_order">
                                    <option value="">@lang('app.report.noneSelected')</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="form-group col-md-4" style="margin-top: 28px!important;">
                        <label class="control-label">&nbsp;</label>
                        <button type="submit" id="apply-filters" class="btn btn-success wide-btn"><i class="fa fa-check"></i> @lang('app.apply')</button>
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
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css"> -->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
@endpush

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">

                    </div>
                </div>
                <div class="col-sm-6 text-right hidden-xs">
                    <div class="form-group">

                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="w3-table w3-striped w3-border table-bordered table-hover toggle-circle" id="tableData">
                    <tr>
                        <th>User</th>
                        <th>Number of Jobs</th>
                        <th>Hours Worked</th>
                    </tr>
                    @if ($reports)
                        @foreach ($reports as $report)
                            <tr>
                                <td>{{ $report['name'] }}</td>
                                <td>{{ $report['NumberOfJobs'] }}</td>
                                <td>{{ $report['Hours'] }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3">No record found!</td>
                        </tr>
                    @endif
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
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('newassets/global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
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
                autoclose: true
            });

        loadTable();

        $('#apply-filters').click(function() {
                $(document).ready(function() {
                    $('#filter-form').on('submit', function(e) {
                        e.preventDefault();
                        if($('#created_date_start').val())
                        {
                            $.ajax({
                                type: "GET",
                                url: "{{ route('admin.crm-operations-get-data') }}",
                                data: $('#filter-form').serialize(),
                                beforeSend: function() {
                                    $(".preloader").show();
                                },
                                complete: function() {
                                    $(".preloader").hide();
                                },
                                success: function (response) {
                                    if(response.success == 1)
                                    {
                                        $("#tableData tr td").detach();
                                        if(response.NumberOfJobs != null)
                                        {
                                            var trHTML = '';
                                            trHTML +=    '<tr>'+
                                                    '<td>' + response.user + '</td>'+
                                                    '<td>' + response.NumberOfJobs + '</td>'+
                                                    '<td>' + response.Hours + '</td>'+
                                                '</tr>';
                                    
                                            $('#tableData').append(trHTML); 
                                        }
                                        else
                                        {
                                            $("#tableData tr td").detach();
                                            var trHTML = '';
                                            trHTML +=    '<tr>'+
                                                    '<td colspan="3">No record found!</td>'+
                                                '</tr>';
                                    
                                            $('#tableData').append(trHTML);
                                        }
                                    }
                                    else if(response.success == 2)
                                    {
                                        $("#tableData tr td").detach();
                                        $.each(response.reports, function(index, value){
                                            var trHTML = '';
                                            trHTML +=    '<tr>'+
                                                    '<td>' + value.name + '</td>'+
                                                    '<td>' + value.NumberOfJobs + '</td>'+
                                                    '<td>' + value.Hours + '</td>'+
                                                '</tr>';
                                            $('#tableData').append(trHTML);
                                        });
                                    }else{
                                        $("#tableData tr td").detach();
                                        var trHTML = '';
                                            trHTML +=    '<tr>'+
                                                    '<td colspan="3">No record found!</td>'+
                                                '</tr>';
                                    
                                            $('#tableData').append(trHTML);
                                    }
                                },
                                error: function(error){
                                    alert("Something Went Wrong");
                                }
                            });
                        }
                    });
                });
            });
            $('#reset-filters').click(function() {
                $('#filter-form')[0].reset();
                $('#lead_status').val('').selectpicker('refresh');
                $('#user_id').val('').selectpicker('refresh');
                $("#tableData tr td").detach();
                location.reload(true);
            });

        $('body').on('click', '.sa-params', function() {
            var id = $(this).data('id');
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover the deleted company!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm) {
                if (isConfirm) {

                    var url = "{{ route('admin.crm-leads.destroy',':id') }}";
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
                                table._fnDraw();
                            }
                        }
                    });
                }
            });
        });
    });

    function loadTable() {

            var created_date_start = $('#created_date_start').val();
            var created_date_end = $('#created_date_end').val();
            var lead_status = $('#lead_status').val();
            var sorting_order = $('#sorting_order').val();

            var sort_descending = '0';
            if (created_date_start == '') {
                created_date_start = null;
            }
            if (created_date_end == '') {
                created_date_end = null;
            }
            
            if ($("#sort_descending").is(':checked')) {
                sort_descending = '1';
            }

        var url = '{!! route("admin.crm-leads.data") !!}?created_date_start=' + created_date_start + '&created_date_end=' + created_date_end + '&lead_status=' + lead_status + '&sorting_order=' + sorting_order + '&sort_descending=' + sort_descending;
            console.log(url);

        table = $('#listing-table').dataTable({
            dom: 'Bfrtip',
            destroy: true,
            responsive: true,
            order: [], //Initial no order.
            processing: true,
            serverSide: true,
            ajax: '{!! route("admin.crm-leads.data") !!}?created_date_start=' + created_date_start + '&created_date_end=' + created_date_end + '&lead_status=' + lead_status + '&sorting_order=' + sorting_order + '&sort_descending=' + sort_descending,

            /*ajax: "{!! route('admin.crm-leads.data') !!}",
            "order": [
                [0, "desc"]
            ],*/
            language: {
                "url": "<?php echo __("app.datatable") ?>"
            },
            "fnDrawCallback": function(oSettings) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            columns: [
                //                { data: 'id', name: 'id' },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'opportunities',
                    name: 'opportunities'
                },
                {
                    data: 'lead_status',
                    name: 'lead_status'
                },
            ]
        });

    }
</script>
@endpush