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
                    <div class="col-md-4">
                        <div class="form-group">
                        <label class="txt14 w400 col-lg-12">@lang('app.report.user')</label>
                        <div>
                            <div class="col-lg-12 pull-left">
                                <select class="form-control" name="user_id" id="sorting_order">
                                    <option value="">@lang('app.report.noneSelected')</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->user_id }}">{{ $user->name }}</option>
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
            <div class="table-responsive">
                <table class="w3-table w3-striped w3-border table-bordered table-hover toggle-circle" id="tableData">
                    <tr>
                        <th>User</th>
                        <th>Quotes Created</th>
                        <th>Jobs Confirmed</th>
                        <th>Quotes Lost</th>
                        <th>Emails Sent</th>
                        <th>Total Sales</th>
                    </tr>
                    @if ($reports)
                        @foreach ($reports as $report)
                            <tr>
                                <td>{{ $report['name'] }}</td>
                                <td>{{ $report['QuotesCreated'] }}</td>
                                <td>{{ $report['jobsConfirmed'] }}</td>
                                <td>{{ $report['QuotesLost'] }}</td>
                                <td>{{ $report['emailSend'] }}</td>
                                <td>{{ $report['totalSales'] }}</td>
                            </tr>
                        @endforeach
                    @else
                            <tr>
                                <td colspan="6">No record found!</td>
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
                        var url = "{{ route('admin.sales-report.getdata') }}";
                            $.ajax({
                                type: "POST",
                                url: url,
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
                                    
                                        var trHTML = '';
                                        trHTML +=    '<tr>'+
                                                '<td>' + response.user + '</td>'+
                                                '<td>' + response.quotesCreated + '</td>'+
                                                '<td>' + response.jobsConfirmed + '</td>'+
                                                '<td>' + response.quotesLost + '</td>'+
                                                '<td>' + response.emailSend +'</td>'+
                                                '<td>$' + response.totalSales + '</td>'+
                                            '</tr>';
                                
                                        $('#tableData').append(trHTML);
                                    }
                                    else
                                    {
                                        $("#tableData tr td").detach();

                                        $.each(response.reports, function(index, value){
                                            var trHTML = '';
                                            trHTML +=    '<tr>'+
                                                    '<td>' + value.name + '</td>'+
                                                    '<td>' + value.QuotesCreated + '</td>'+
                                                    '<td>' + value.jobsConfirmed + '</td>'+
                                                    '<td>' + value.QuotesLost + '</td>'+
                                                    '<td>' + value.emailSend +'</td>'+
                                                    '<td>' + value.totalSales + '</td>'+
                                                '</tr>';
                                    
                                            $('#tableData').append(trHTML);
                                        });
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
    });
</script>
@endpush