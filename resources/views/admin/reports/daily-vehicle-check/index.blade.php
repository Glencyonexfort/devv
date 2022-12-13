@extends('layouts.app')

@section('page-title')
    <style type="text/css">
        .row{
            width: 100%!important;
        }

        .border-none {
        border-collapse: collapse;
        border: none;
        }

        .border-none tr td:first-child {
        border-left: none;
        border-right: none;
        border-bottom: none;
        border-top: none;
        }

        .border-none tr td:last-child {
        border-right: none;
        border-left: none;
        border-bottom: none;
        border-top: none;
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
                            <label class="txt14 w400 col-lg-12">@lang('app.report.vehicle')</label>
                            <div>
                                <div class="col-lg-12 pull-left">
                                    <select class="form-control" name="vehicle_id" id="sorting_order">
                                        <option value="">@lang('app.report.noneSelected')</option>
                                        @foreach ($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_name }}-{{ $vehicle->license_plate_number }}</option>
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
                            <th>Date</th>
                            <th>Vehicle</th>
                            <th>Driver</th>
                            <th>Report</th>
                        </tr>
                        <tr>
                            <td colspan="4">No record found!</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- START Report Popup -->
    <div id="report-popup" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="padding: 30px;">
                    <div class="row">
                        <div class="col-md-4">
                            @if($companies->logo)
                                <div id="logo">
                                    <img style="height: 60px; width: 200px;" src="{{ request()->getSchemeAndHttpHost().'/user-uploads/company-logo/'.$companies->logo }}">
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8" style="margin-top: 10px; padding-left: 60px; display: flex;">
                            <p><b style="font-size: 25px;">Daily Vehicle Check</b></p>
                            <button type="button" class="close" data-dismiss="modal">×</button>
                        </div>
                    </div>
                    <br>
                </div>
                <div class="modal-body ml-2 p-3">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="border-none">
                                <tr>
                                    <td><b>Date:</b></td>
                                    <td id="date">9/4/2021</td>
                                </tr>
                                <tr>
                                    <td><b>Driver:</b></td>
                                    <td id="driver">Tom Driver</td>
                                </tr>
                                <tr>
                                    <td><b>Start Odometer:</b></td>
                                    <td id="odometer">567343</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="border-none">
                                <tr>
                                    <td><b>Time:</b></td>
                                    <td id="time">9/4/2021</td>
                                </tr>
                                <tr>
                                    <td><b>Vehicle:</b></td>
                                    <td id="vehicle">Tom Driver</td>
                                </tr>
                                <tr>
                                    <td><b>Fuel Percent:</b></td>
                                    <td id="fuel">567343</td>
                                </tr>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-md-12 ml-2">
                                <table class="border-none">
                                    <thead style="background: none; border-top: none;">
                                        <tr>
                                            <td><b>Notes:</b></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tbody>
                                            <tr>
                                                <td id="notes">No notes here today!</td>
                                            </tr>
                                        </tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr>
                    {{-- @if (count($tables))
                        @foreach ($tables as $table)
                            <article>       
                                <h3 class="col-lg-10" style="font-size: 20px;font-family: 'Poppins', sans-serif;">{{ $table->checklist_group }}</h3>          
                                <table class="inventory">
                                    <thead>
                                        <tr>
                                            <th style="width: 70%;"><span >Check</span></th>
                                            <th style="width: 30%;"><span>Status</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>                
                                </table>
                            </article>
                            <br>
                            <br>
                        @endforeach
                    @endif --}}
                    <div id="viewTables"></div>
                    <div class="tables"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- /END Report Popup -->

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

            $('#apply-filters').click(function(e){
                            e.preventDefault();
                            if($('#created_date_start').val())
                            {
                                var url = "{{ route('admin.crm-checklist-data') }}";
                                $.ajax({
                                    type: "GET",
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
                                                    '<td>' + response.date + '</td>'+
                                                    '<td>' + response.vehicle + '</td>'+
                                                    '<td>' + response.driver + '</td>'+
                                                    '<td>' + response.report + '</td>'+
                                                '</tr>';
                                    
                                            $('#tableData').append(trHTML);
                                        }
                                        else
                                        {
                                            $("#tableData tr td").detach();

                                            $.each(response.reports, function(index, value){
                                                var trHTML = '';
                                                trHTML +=    '<tr>'+
                                                        '<td>' + value.date + '</td>'+
                                                        '<td>' + value.vehicle + '</td>'+
                                                        '<td>' + value.driver + '</td>'+
                                                        '<td><a class="showReport" data-dailycheckid="' + value.id + '" href="javascript:;">' + value.report + '</a></td>'+
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
                
            $('#reset-filters').click(function(){
                $('#filter-form')[0].reset();
                $('#user_id').val('').selectpicker('refresh');
                $("#tableData tr td").detach();
                location.reload(true);
                // loadTable();
            });

            $(document).on('click', '.showReport', function (e) {
                e.preventDefault();
                var daily_driver_vehicle_check_id = $(this).data('dailycheckid');
                $.ajax({
                    type: "GET",
                    url: "{{ route('admin.crm-daily-check-popup-data') }}",
                    data: {
                        'daily_driver_vehicle_check_id': daily_driver_vehicle_check_id
                    },
                    beforeSend: function() {
                        $(".preloader").show();
                    },
                    complete: function() {
                        $(".preloader").hide();
                    },
                    success: function (response) {
                        console.log(response);
                        $('#report-popup').modal('show');
                        $("#report-popup-inner").addClass("popup-shadow");
                        $('#report-popup').modal('show');
                        $('#report-popup').css("opacity","1");
                        $('#report-popup').css('top', "30px");
                        // $('body').removeClass('modal-open');
                        // $('.modal-backdrop').remove();
                        $('#date').text(response.data.date);
                        $('#time').text(response.data.time);
                        $('#driver').text(response.data.driver);
                        $('#vehicle').text(response.data.vehicle);
                        $('#odometer').text(response.data.odometer);
                        $('#fuel').text(response.data.fuel);
                        if(response.data.notes != null)
                        {
                            $('#notes').text(response.data.notes);
                        }
                        else
                        {
                            $('#notes').text("No notes here today!");
                        }

                        $('#viewTables').empty();
                        $.each(response.viewTables, function(index, value){
                            $('#viewTables').append(value);
                        });
                        

                    },
                    error: function(error){
                        alert("Something Went Wrong");
                    }
                });
            })

        });
    </script>
@endpush