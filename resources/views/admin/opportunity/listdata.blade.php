@extends('layouts.app')

@section('page-title')


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
    .dataTables_filter>label:after{
        display: none!important;
    }
    .row{
        width: 100%!important;
    }
</style>
 <div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline" style="border-top:1px solid #ccc; border-bottom: 1px solid #ccc;">
        <div class="page-pipelines d-flex">
            <h4><span class="font-weight-semibold" style="font-size:23.6px;margin-left:-11px !important;font-family: 'Poppins',sans-serif;">Opportunities</span></h4>
        </div>
    </div>


    <!-- Latest posts -->
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
                                <div class="row" style="display:''; background: #fbfbfb;padding: 10px;margin-bottom: 15px;margin-right:0px;" id="div-filters">
                    <?php
                        $sorting_order_array = ['created_at'=>'Created Date', 'op_status'=>'Status', 'lead_id'=>'Lead', 'value'=>'Value'];
                    ?>
                    <form action="" id="filter-form" style="width: 100%">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="txt14 w400">@lang('modules.opportunities.opportunityStatus')</label>
                                    <div class="multiselect-native-select">
                                        <select class="form-control multiselect" multiple="multiple" name="opportunity_status" id="opportunity_status">
                                            @foreach($statuses as $status)
                                            <option @if(in_array($status->pipeline_status, $statusToSelect)) selected="" @endif value="{{ $status->pipeline_status }}">{{ $status->pipeline_status }}</option>
                                            @endforeach
                                       
                                    </select>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="txt14 w400">@lang('modules.opportunities.users')</label>
                                    <div class="multiselect-native-select">
                                        <select class="form-control multiselect" multiple="multiple" name="user_id" id="user_id">
                                        @foreach($users as $val)
                                        <option  value="{{ $val->user_id }}">{{ $val->name }}</option>
                                        @endforeach
                                    </select>
                                    </div>
                                </div>
                            </div>    
                            <div class="col-md-4">
                                <div class="form-group" style="margin-top: 36px!important;">
                                    <label></label>
                                    <!-- <input type="checkbox" name="sort_descending" value="1" id="sort_descending"> <label class="txt14 w400">@lang('modules.listJobs.sort_descending')</label> -->
                                </div>
                            </div>                        
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                <label class="txt14 w400">@lang('modules.opportunities.created_date')</label>
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
                                <label class="txt14 w400">@lang('modules.opportunities.job_date')</label>
                                <div class="input-daterange input-group" id="job-date-range">
                                    <input type="text" class="form-control" id="job_date_start" placeholder="@lang('app.startDate')" value="" />
                                    <span class="input-group-prepend">
                                        <span class="input-group-text prepend-txt">@lang('app.to')</span>
                                    </span>
                                    <input type="text" class="form-control" id="job_date_end" placeholder="@lang('app.endDate')" value="" />
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                <label class="txt14 w400 col-lg-12">@lang('modules.opportunities.sortingOrder')</label>
                                <div>
                                    <div class="col-lg-6 pull-left">
                                        <select class="form-control" name="sorting_order" id="sorting_order">
                                        @foreach($sorting_order_array as $rs=>$val)
                                        <option value="{{ $rs }}">{{ $val }}</option>
                                        @endforeach
                                    </select>
                                    </div>
                                    <div class="col-lg-6 pull-left">
                                        <span class="pull-left m-t-10"><input style="width:24px;height:20px;cursor:pointer;" type="checkbox" name="sort_descending" value="1" id="sort_descending" class="pull-left" checked=""> &nbsp;Descending
                                        </span>
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
                        <!-- /latest posts -->
</div>
@endsection

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <div class="row">
                <div class="col-sm-12">
                    <div class="badge-dark pull-left col-sm-12">
                        <div class="col-sm-3 pull-left"><h4 style="margin-top:8px;">Opportunities: <span id="totalCount">0</span></h4></div>
                        <!-- <div class="col-sm-3 pull-left"><h4 style="margin-top:8px;">Total Value: {{ $global->currency_symbol }}<span id="totalVal">0.00</span></h4></div> -->
                    </div>
                </div>
                <div class="col-sm-6 text-right hidden-xs">
                    <div class="form-group">

                    </div>
                </div>
            </div>

            <div class="table-responsive">
                    <table class="table table-bordered table-hover toggle-circle default footable-loaded footable" id="listing-table" style="">
                        <thead>
                        <tr>
                            <th>@lang('modules.opportunities.opportunity_number')</th>
                            <th>@lang('modules.opportunities.lead')</th>
                            <th>@lang('modules.opportunities.mobile')</th>
                            <th>@lang('modules.opportunities.lead_info')</th>
                            <th>@lang('modules.opportunities.company')</th>
                            <th>@lang('modules.opportunities.type')</th>
                            <th>@lang('modules.opportunities.job_date')</th>
                            <th>Created Date</th>
                            <th>@lang('modules.opportunities.status')</th>
                            <th>@lang('modules.opportunities.user')</th>
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
            jQuery('#created-date-range, #removal-date-range, #job-date-range').datepicker({
                toggleActive: true,
                format: '{{ $global->date_picker_format }}',
                language: '{{ $global->locale }}',
                autoclose: true
            });
            loadTable();
            
            $('#apply-filters').click(function() {
                loadTable();
            });
            $('#reset-filters').click(function() {
                $('#filter-form')[0].reset();
                $('#opportunity_status').val('').selectpicker('refresh');
                $('#user_id').val('').selectpicker('refresh');
                loadTable();
            });
        });
        function loadTable() {
            var created_date_start = $('#created_date_start').val();
            // alert(created_date_start);
            var created_date_end = $('#created_date_end').val();
            var job_date_start = $('#job_date_start').val();
            var job_date_end = $('#job_date_end').val();
            var opportunity_status = $('#opportunity_status').val();
            var user_id = $('#user_id').val();
            var sorting_order = $('#sorting_order').val();

            var sort_descending = '0';
            if (created_date_start == '') {
                created_date_start = null;
            }
            if (created_date_end == '') {
                created_date_end = null;
            }

            if (job_date_start == '') {
                job_date_start = null;
            }
            if (job_date_end == '') {
                job_date_end = null;
            }
            
            if ($("#sort_descending").is(':checked')) {
                sort_descending = '1';
            }
            var url = '{!! route("admin.opportunity.data") !!}?job_date_start=' + job_date_start + '&job_date_end=' + job_date_end + '&created_date_start=' + created_date_start + '&created_date_end=' + created_date_end + '&opportunity_status=' + opportunity_status + '&user_id=' + user_id + '&sorting_order=' + sorting_order + '&sort_descending=' + sort_descending;
            console.log(url);
            table = $('#listing-table').dataTable({
                "pageLength": 50,
                destroy: true,
                //                                    responsive: true,
                processing: true,
                order: [], //Initial no order.
                aaSorting: [],
                serverSide: true,
                scrollX: true,
                ajax: '{!! route("admin.opportunity.data") !!}?job_date_start=' + job_date_start + '&job_date_end=' + job_date_end + '&created_date_start=' + created_date_start + '&created_date_end=' + created_date_end + '&opportunity_status=' + opportunity_status + '&user_id=' + user_id + '&sorting_order=' + sorting_order + '&sort_descending=' + sort_descending,
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
                columnDefs : [{targets:6, type: "ddMmYyyy"}],
                columns: [
                    {
                        data: 'opportunity_number',
                        name: 'opportunity_number',
                        width: '10%'
                    },
                    {
                        data: 'lead',
                        name: 'lead',
                        width: '16%'
                    },
                    {
                        data: 'mobile',
                        name: 'mobile',
                        width: '12%'
                    },
                    {
                        data: 'lead_info',
                        name: 'lead_info',
                        width: '12%'
                    },
                    {
                        data: 'company',
                        name: 'company',
                        width: '12%'
                    },
                    {
                        data: 'type',
                        name: 'type',
                        width: '10%'
                    },
                    {
                        data: 'job_date',
                        name: 'job_date',
                        width: '12%'
                    },
                    {
                        data: 'created_date',
                        name: 'created_date',
                        width: '13%'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        width: '12%'
                    },
                    {
                        data: 'user',
                        name: 'user',
                        width: '12%'
                    },
                ]
            });
        }
    </script>

<script>
    function allowDrop(ev) {
        ev.preventDefault();
    }
    function drag(ev,id) {
        ev.dataTransfer.setData("text", id);
    }

    function drop(ev) {
        ev.preventDefault();
        var data = ev.dataTransfer.getData("text");
        console.log(data);
        ev.target.appendChild(document.getElementById('dragable_col_'+data));
        
    }
    
    
</script>
@endpush    
