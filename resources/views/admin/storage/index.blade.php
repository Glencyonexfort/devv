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
            <h4><span class="font-weight-semibold" style="font-size:23.6px;margin-left:-11px !important;font-family: 'Poppins',sans-serif;">Storage Units List</span></h4>
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
                    <form action="" id="filter-form" style="width: 100%">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="txt14 w400">Storage Type</label>
                                    <div class="multiselect-native-select">
                                        <select class="form-control multiselect" multiple="multiple" name="storage_type" id="storage_type">
                                            @foreach($storage_types as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endforeach
                                       
                                    </select>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="txt14 w400">Storage Unit</label>
                                    {{-- <div class="multiselect-native-select"> --}}
                                        <select class="form-control" name="storage_unit" id="storage_unit">        
                                            <option></option>                               
                                    </select>
                                    {{-- </div> --}}
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
                                    <label class="txt14 w400">Allocation Status</label>
                                    <div class="multiselect-native-select">
                                        <select class="form-control multiselect" multiple="multiple" name="allocation_status" id="allocation_status">
                                            @foreach($allocation_status as $status)
                                            <option value="{{ $status }}">{{ $status }}</option>
                                            @endforeach
                                       
                                    </select>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                <label class="txt14 w400">Allocation Date</label>
                                <div class="input-daterange input-group" id="created-date-range">
                                    <input type="text" class="form-control" id="from_date" placeholder="From Date" value="" />
                                    <span class="input-group-prepend">
                                        <span class="input-group-text prepend-txt">@lang('app.to')</span>
                                    </span>
                                    <input type="text" class="form-control" id="to_date" placeholder="To Date" value="" />
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
                        <div class="col-sm-3 pull-left"><h4 style="margin-top:8px;">Storage Units: <span id="totalCount">0</span></h4></div>
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
                            <th>Unit #</th>
                            <th>Allocation Status</th>
                            <th>Job Type</th>
                            <th>Job #</th>
                            <th>Customer</th>
                            <th>From Date</th>
                            <th>To Date</th>
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
            jQuery('#created-date-range, #removal-date-range').datepicker({
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
                $('#allocation_status').val('').selectpicker('refresh');
                $('#storage_type').val('').selectpicker('refresh');
                $('#storage_unit').val('').selectpicker('refresh');
                loadTable();
            });
            $('body').on('change', '#storage_type', function(e) {
                e.preventDefault();
                var token = "{{ csrf_token() }}";
                $.ajax({
                    url: "/admin/storage/get-storage-units",
                    method: 'post',
                    data: {'_token':token,'storage_type':$('#storage_type').val()},
                    dataType: "json",
                    beforeSend: function() {
                        $(".preloader").show();
                    },
                    complete: function() {
                        $(".preloader").hide();
                    },
                    success: function(result) {
                        if (result.error == 0) {
                            var len = result.data.length;
                             console.log(result.data);
                             console.log(len);
                            $("#storage_unit").empty();
                            for( var i = 0; i<len; i++){
                                var id = result.data[i]['id'];
                                var name = result.data[i]['name'];                        
                                var serial = result.data[i]['serial_number'];
                                $("#storage_unit").append("<option value='"+id+"'>"+serial+"-"+name+"</option>");
                                //$('#storage_unit').selectpicker('refresh');
                            }
                        }else if(result.error == 1){
                            $("#storage_unit").empty();
                            $.toast({
                                heading: 'Warning',
                                text: result.message,
                                icon: 'warning',
                                position: 'top-right',
                                loader: false,
                                bgColor: '#ea8d2e',
                                textColor: 'white'
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
        });
        function loadTable() {
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            var allocation_status = $('#allocation_status').val();
            var storage_type = $('#storage_type').val();
            var storage_unit = $('#storage_unit').val();

            if (from_date == '') {
                from_date = null;
            }
            if (to_date == '') {
                to_date = null;
            }
            
            var url = '{!! route("admin.storage.data") !!}?from_date=' + from_date + '&to_date=' + to_date + '&allocation_status=' + allocation_status + '&storage_type=' + storage_type + '&storage_unit=' + storage_unit;
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
                ajax: '{!! route("admin.storage.data") !!}?from_date=' + from_date + '&to_date=' + to_date + '&allocation_status=' + allocation_status + '&storage_type=' + storage_type + '&storage_unit=' + storage_unit,
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
                        data: 'unit_no',
                        name: 'unit_no',
                        width: '10%'
                    },
                    {
                        data: 'allocation_status',
                        name: 'allocation_status',
                        width: '20%'
                    },
                    {
                        data: 'job_type',
                        name: 'job_type',
                        width: '15%'
                    },
                    {
                        data: 'job_no',
                        name: 'job_no',
                        width: '10%'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name',
                        width: '15%'
                    },
                    {
                        data: 'from_date',
                        name: 'from_date',
                        width: '10%'
                    },
                    {
                        data: 'to_date',
                        name: 'to_date',
                        width: '10%'
                    }                    
                ]
            });
        }
    </script>
@endpush    
