@extends('layouts.app')

@section('trucksize_grid')
@include('admin.hourly-settings.trucksize_grid')
@endsection

@section('regionaldepot_grid')
@include('admin.hourly-settings.regionaldepot_grid')
@endsection

@section('page-title')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="{{ $pageIcon }} mr-2"></i> <span class="font-weight-semibold">{{ $pageTitle }}</span></h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
    <!-- <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> @lang('app.menu.home')</a>
                <span class="breadcrumb-item active">{{ $pageTitle }}</span>
            </div>
        </div>
    </div> -->
</div>
@endsection

@push('head-script')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB2SMtaVBlqC5v72gqS716BX8R5oXklaFc&v=3.exp&libraries=places"></script>
<link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">
    
@endpush

@section('content')

<div class="content">
    <div class="d-md-flex align-items-md-start">
            @include('sections.removal_setting_menu')
        <div style="flex:auto">
            <div class="card">
                <div class="card-header bg-white header-elements-inline">
                    <h6 class="card-title">@lang('modules.hourlySettings.boxTitle')</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            {!! Form::open(['id'=>'updateHourlyPricingSettings','class'=>'ajax-form','method'=>'POST']) !!}
                            <div class="form-body">


                                <div class="form-group row">
                                    <label class="col-lg-3" style="margin-top: 7px;">@lang('modules.hourlySettings.useHourlyPricingforLocalMove')</label>
                                    <div class="col-lg-7">
                                       <div class="form-check form-check-switchery" style="margin-top:0px;">
                                                <label class="form-check-label">
                                                    <input value="Y" name="use_hourly_pricing_local_moves" id="use_hourly_pricing_local_moves" type="checkbox" @if(isset($pricingAdditional) && $pricingAdditional->use_hourly_pricing_local_moves  == 'Y') checked @endif class="js-switch class="js-switch form-check-input-switchery-danger" data-fouc data-color="#f96262">
                                                </label>
                                            </div>

                                    </div>
                                </div>


                                <div class="form-group row">
                                    <label class="col-form-label col-lg-3">@lang('modules.hourlySettings.excessMinutesTierOne')</label>
                                    <div class="col-form-label col-lg-2"> From 0 to </div>
                                    <div class="col-lg-7">
                                        <div class="input-group">
                                            
                                            <input type="text" class="form-control" name="local_move_excess_minutes_tier1" id="local_move_excess_minutes_tier1" @if(isset($pricingAdditional) && $pricingAdditional->use_hourly_pricing_local_moves  == 'Y') enabled @else disabled @endif value="{{ $pricingAdditional ? $pricingAdditional->local_move_excess_minutes_tier1 : '' }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-lg-3">@lang('modules.hourlySettings.excessMinutesTierTwo')</label>
                                    <div class="col-form-label col-lg-2"> From {{ $pricingAdditional ? $pricingAdditional->local_move_excess_minutes_tier1 : '0' }} to </div>
                                    <div class="col-lg-7">
                                        <div class="input-group">
                                            
                                            <input type="text" class="form-control" name="local_move_excess_minutes_tier2" id="local_move_excess_minutes_tier2" value="{{ $pricingAdditional ? $pricingAdditional->local_move_excess_minutes_tier2 : '' }}"
                                              @if(isset($pricingAdditional) && $pricingAdditional->use_hourly_pricing_local_moves  == 'Y') enabled @else disabled @endif>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-lg-5">@lang('modules.hourlySettings.MinPriceAsPercentofTheMaxQuotePrice')</label>
                                    
                                    <div class="col-lg-7">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="hourly_pricing_min_pricing_percent" id="hourly_pricing_min_pricing_percent" value="{{ $pricingAdditional ? ($pricingAdditional->hourly_pricing_min_pricing_percent)*100 : '' }}" @if(isset($pricingAdditional) && $pricingAdditional->use_hourly_pricing_local_moves  == 'Y') enabled @else disabled @endif
                                             >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-lg-5">Minimum hours for invoice</label>
                                    
                                    <div class="col-lg-7">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="hourly_pricing_min_hours" id="hourly_pricing_min_hours" value="{{ $pricingAdditional ? ($pricingAdditional->hourly_pricing_min_hours) : '' }}">
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <h5>Include in the price calculation:</h5>
                            <div class="row col-lg-12">
                                <div class="form-group row col-lg-6">
                                    <label class="col-form-label col-lg-8">@lang('modules.hourlySettings.TimeFromDepotToPickupSuburb')</label>
                                    
                                    <div class="col-lg-3">
                                       <div class="form-check form-check-switchery" style="margin-top:0px;">
                                                <label class="form-check-label">
                                                    <input value="Y" name="hourly_pricing_include_depot_pickup" id="hourly_pricing_include_depot_pickup" type="checkbox" @if(isset($pricingAdditional) && $pricingAdditional->hourly_pricing_include_depot_pickup  == 'Y') checked @endif class="js-switch class="js-switch form-check-input-switchery-danger" data-fouc data-color="#f96262">
                                                </label>
                                            </div>

                                    </div>
                                </div>


                                <div class="form-group row col-lg-6">
                                    <label class="col-form-label col-lg-8">@lang('modules.hourlySettings.TimeFromDropoffSuburbToDepot')</label>
                                    
                                    <div class="col-lg-3">
                                       <div class="form-check form-check-switchery" style="margin-top:0px;">
                                                <label class="form-check-label">
                                                    <input value="Y" name="hourly_pricing_include_drop_off_depot" id="hourly_pricing_include_drop_off_depot" type="checkbox" @if(isset($pricingAdditional) && $pricingAdditional->hourly_pricing_include_drop_off_depot  == 'Y') checked @endif class="js-switch class="js-switch form-check-input-switchery-danger" data-fouc data-color="#f96262">
                                                </label>
                                            </div>

                                    </div>
                                </div>


                                <div class="form-group row col-lg-6 m-t-10">
                                    <label class="col-form-label col-lg-8">@lang('modules.hourlySettings.LoadingTime')</label>
                                    
                                    <div class="col-lg-3">
                                       <div class="form-check form-check-switchery" style="margin-top:0px;">
                                                <label class="form-check-label">
                                                    <input value="Y" name="hourly_pricing_include_loading_time" id="hourly_pricing_include_loading_time" type="checkbox" @if(isset($pricingAdditional) && $pricingAdditional->hourly_pricing_include_loading_time  == 'Y') checked @endif class="js-switch class="js-switch form-check-input-switchery-danger" data-fouc data-color="#f96262">
                                                </label>
                                            </div>

                                    </div>
                                </div>


                                <div class="form-group row col-lg-6 m-t-10">
                                    <label class="col-form-label col-lg-8">@lang('modules.hourlySettings.UnloadingTime')</label>
                                    
                                    <div class="col-lg-3">
                                       <div class="form-check form-check-switchery" style="margin-top:0px;">
                                                <label class="form-check-label">
                                                    <input value="Y" name="hourly_pricing_include_unloading_time" id="hourly_pricing_include_unloading_time" type="checkbox" @if(isset($pricingAdditional) && $pricingAdditional->hourly_pricing_include_unloading_time  == 'Y') checked @endif class="js-switch class="js-switch form-check-input-switchery-danger" data-fouc data-color="#f96262">
                                                </label>
                                            </div>

                                    </div>
                                </div>


                                <div class="form-group row col-lg-6 m-t-10">
                                    <label class="col-form-label col-lg-8">@lang('modules.hourlySettings.TimeFromPickupSuburbToDropoffSuburb')</label>
                                    
                                    <div class="col-lg-3">
                                       <div class="form-check form-check-switchery" style="margin-top:0px;">
                                                <label class="form-check-label">
                                                    <input value="Y" name="hourly_pricing_include_pickup_drop_off" id="hourly_pricing_include_pickup_drop_off" type="checkbox" @if(isset($pricingAdditional) && $pricingAdditional->hourly_pricing_include_pickup_drop_off  == 'Y') checked @endif class="js-switch class="js-switch form-check-input-switchery-danger" data-fouc data-color="#f96262">
                                                </label>
                                            </div>

                                    </div>
                                </div>

                            </div>

                                <hr>

                            <div class="row col-lg-12">

                                <div class="form-group row col-lg-12">
                                    <label class="col-form-label col-lg-4">@lang('modules.hourlySettings.UseBookingFeeInsteadofDeposit')</label>
                                    
                                    <div class="col-lg-3">
                                       <div class="form-check form-check-switchery" style="margin-top:0px;">
                                                <label class="form-check-label">
                                                    <input value="Y" name="hourly_pricing_has_booking_fee" id="hourly_pricing_has_booking_fee" type="checkbox" @if(isset($pricingAdditional) && $pricingAdditional->hourly_pricing_has_booking_fee  == 'Y') checked @endif class="js-switch class="js-switch form-check-input-switchery-danger" data-fouc data-color="#f96262">
                                                </label>
                                            </div>

                                    </div>
                                </div>

                                <div class="form-group row col-lg-12">
                                    <label class="col-form-label col-lg-4">@lang('modules.hourlySettings.BookingFee'):</label>

                                    <div class="form-group form-group-feedback form-group-feedback-left">
                                            
                                            <input type="number" min="0.00" step="0.01" class="form-control" id="hourly_pricing_booking_fee" name="hourly_pricing_booking_fee" value="{{ $pricingAdditional ? $pricingAdditional->hourly_pricing_booking_fee : '0.00' }}" @if(isset($pricingAdditional) && $pricingAdditional->hourly_pricing_has_booking_fee  == 'Y') enabled @else disabled @endif >
                                            <div class="form-control-feedback">
                                                <span>{{ $global->currency_symbol }}</span>
                                            </div>
                                        </div>
                                </div>

                            </div>


                                <div class="row" id="deposit-fixed-amount-section" @if(isset($pricingAdditional) && $pricingAdditional->hourly_pricing_has_booking_fee  == 'Y') style="display:none;" @endif>

                                    
                                    <div class="col-md-6" style="margin:15px 0px 15px 0px;">

                                        <div class="form-group">
                                            <label>@lang('modules.PricingSettings.isDepositFixedAmount')</label>
                                            
                                            <div class="form-check form-check-switchery">
                                                <label class="form-check-label">
                                                    <input value="Y" name="is_deposit_for_hourly_pricing_fixed_amt" id="is_deposit_for_hourly_pricing_fixed_amt" type="checkbox"
                                                    @if(isset($pricingAdditional) && $pricingAdditional->is_deposit_for_hourly_pricing_fixed_amt  == 'Y') checked @endif class="js-switch class="js-switch form-check-input-switchery-danger" data-fouc data-color="#f96262">
                                                </label>
                                            </div>

                                        </div>
                                        
                                    </div>

                                    <div class="col-md-6">
                                    </div>

                                    <div class="col-md-6">

                                        <label>@lang('modules.PricingSettings.depositAmount')</label>
                                        <div class="form-group form-group-feedback form-group-feedback-left">
                                            
                                            <input type="number" min="0.00" step="0.01" class="form-control" id="deposit_amount_hourly_pricing" name="deposit_amount_hourly_pricing" value="{{ $pricingAdditional ? $pricingAdditional->deposit_amount_hourly_pricing : '0.00' }}"
                                             @if(isset($pricingAdditional) && $pricingAdditional->is_deposit_for_hourly_pricing_fixed_amt  == 'Y') enabled @else disabled @endif >
                                            <div class="form-control-feedback">
                                                <span>{{ $global->currency_symbol }}</span>
                                            </div>
                                        </div>
                                        
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.PricingSettings.depositPercent')</label>
                                            <input type="number" min="0.00" step="0.01" class="form-control form-control-sm" id="deposit_percent_hourly_pricing" name="deposit_percent_hourly_pricing" value="{{ $pricingAdditional ? ($pricingAdditional->deposit_percent_hourly_pricing)*100 : '0.00' }}"
                                             @if(isset($pricingAdditional) && $pricingAdditional->is_deposit_for_hourly_pricing_fixed_amt  == 'Y') disabled @else enabled @endif>
                                        </div>
                                    </div>
                                   
                                    

                                    
                                </div>

                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-12 text-left" style="margin-bottom: 20px;">
                                        <hr>
                                        <button type="submit" id="save-form-2" class="btn btn-success m-r-10"><i class="fa fa-check"></i> @lang('app.save')</button>
                                        <!-- <button type="reset" class="btn btn-default">@lang('app.reset')</button> -->
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}

                            <legend class="font-size-lg font-weight-bold"><mark>Truck Size based Rates:</mark></legend>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="truckSize-table">
                                    <thead>
                                        <tr>
                                            <th>@lang('modules.hourlySettings.minVolume')</th>
                                            <th>@lang('modules.hourlySettings.maxVolume')</th>
                                            <th>@lang('modules.hourlySettings.truckSize')</th>
                                            <th>@lang('modules.hourlySettings.loadingTime')</th>
                                            <th>@lang('modules.hourlySettings.unLoadingTime')</th>
                                            <th>@lang('modules.hourlySettings.hourlyRate')</th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody id="trucksize_grid">
                                        @yield('trucksize_grid')
                                    </tbody>

                                </table>

                            </div>

                            <button type="button" class="btn alpha-primary text-primary-800 btn-icon add_new_truck_size_row"><span class="cursor-pointer"><img src="{{ asset('newassets/img/icon-add.png') }}"></span></button>


                            <legend class="font-size-lg font-weight-bold m-t-30"><mark>Regional Depots:</mark></legend>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="regionalDepots-table">
                                    <thead>
                                        <tr>
                                            <th>@lang('modules.hourlySettings.region')</th>
                                            <th>@lang('modules.hourlySettings.depotSuburb')</th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody id="regionaldepot_grid">
                                       @yield('regionaldepot_grid')
                                    </tbody>

                                </table>

                            </div>

                            <button type="button" class="btn alpha-primary text-primary-800 btn-icon add_new_depot_location_row"><span class="cursor-pointer"><img src="{{ asset('newassets/img/icon-add.png') }}"></span></button>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php 
if(isset($pricingAdditional)){
    $url = '/admin/moving-settings/saveHourlySettingsData/'.$pricingAdditional->id;
} else {
    $url = '/admin/moving-settings/createHourlySettingsData';
}
//dd($url); ?>
</div>
@endsection

@push('footer-script')
    
<script src="{{ asset('plugins/bower_components/switchery/dist/switchery.min.js') }}"></script>


<script>
    var autocomplete = [];

var autocompleteOptions = {
            types: ['(cities)'],
            componentRestrictions: {
                country: "au"
            }
        };

    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    $('.js-switch').each(function() {
        new Switchery($(this)[0], $(this).data());
    });

    $(".add_new_depot_location_row").click(function() {
       var rowCount = $('#regionalDepots-table tr').length;
       $('#regionalDepots-table').append('<tr id="tr_depotlocation'+rowCount+'"><td><select name="region_id" id="region_id_'+rowCount+'" class="form-control">@foreach($pricingRegions as $data)<option value="{{ $data->id }}">{{ $data->region_name }}</option>@endforeach</select></td><td><input type="text" name="depot_suburb" id="add_depot_suburb_'+rowCount+'" onchange="removeCountryNameAddCase('+rowCount+')" class="form-control depot_suburb"></td><td><button type="submit" id="create_depotLocation_btn_'+rowCount+'"  data-createDepotrowid="'+rowCount+'" class="btn btn-success m-r-10 btn-sm create_depotLocation_btn" style="padding:6px 6px;">Save</button><button id="delete_depotLocation_row_'+rowCount+'" type="button" onclick="deleteDepotLocationAddedRow('+rowCount+')" class="btn btn-light btn-sm" style="padding:6px 6px;">Cancel</button></td></tr>');

        var newInput = [];
        var newEl = document.getElementById('add_depot_suburb_' + rowCount);
        newInput.push(newEl);
        setupAutocomplete(autocomplete, newInput, 0);
    });

    $("#hourly_pricing_has_booking_fee").change(function() {
        if(this.checked) {
            $("#hourly_pricing_booking_fee").prop("disabled", false);

            $("#deposit-fixed-amount-section").hide("1000");
        } else {
            $("#hourly_pricing_booking_fee").prop("disabled", true);
            $("#deposit-fixed-amount-section").show("1000");
        }
    });

    $("#is_deposit_for_hourly_pricing_fixed_amt").change(function() {
        if(this.checked) {
            $("#deposit_amount_hourly_pricing").prop("disabled", false);
            $("#deposit_percent_hourly_pricing").prop("disabled", true);
        } else {
            $("#deposit_amount_hourly_pricing").prop("disabled", true);
            $("#deposit_percent_hourly_pricing").prop("disabled", false);
        }
    });

    $(".add_new_truck_size_row").click(function() {
       var rowCount = $('#truckSize-table tr').length;
       $('#truckSize-table').append('<tr id="tr_trucksize'+rowCount+'"><td><input type="text" name="min_cbm" id="min_cbm_'+rowCount+'" class="form-control"></td><td><input type="text" name="max_cbm" id="max_cbm_'+rowCount+'" class="form-control"></td><td><input type="text" name="truck_size_in_ton" id="truck_size_in_ton_'+rowCount+'" class="form-control"></td><td><input type="text" name="loading_mins" id="loading_mins_'+rowCount+'" class="form-control"></td><td><input type="text" name="unloading_mins" id="unloading_mins_'+rowCount+'" class="form-control"></td><td><input type="text" name="hourly_rate" id="hourly_rate_'+rowCount+'" class="form-control"></td><td><button type="submit" onclick="create_added_row('+rowCount+')" id="create_truckSize_btn_'+rowCount+'"  data-createrowid="'+rowCount+'" class="btn btn-success m-r-10 btn-sm create_truckSize_btn" style="padding:6px 6px;">Save</button><button id="delete_row_'+rowCount+'" type="button" onclick="deleteAddedRow('+rowCount+')" class="btn btn-light btn-sm" style="padding:6px 6px;">Cancel</button></td></tr>');
    });
    $("#use_hourly_pricing_local_moves").change(function() {
        if(this.checked) {
            $("#local_move_excess_minutes_tier1").prop("disabled", false);
            $("#local_move_excess_minutes_tier2").prop("disabled", false);
            $("#hourly_pricing_min_pricing_percent").prop("disabled", false);
            //$("#save-form-2").prop("disabled", false);
        } else {
            $("#local_move_excess_minutes_tier1").prop("disabled", true);
            $("#local_move_excess_minutes_tier2").prop("disabled", true);
            $("#hourly_pricing_min_pricing_percent").prop("disabled", true);
            //$("#save-form-2").prop("disabled", true);
        }
    });




    $('#save-form-2').click(function() {
        $.easyAjax({
            url: "{{ $url }}",
            container: '#updateHourlyPricingSettings',
            type: "POST",
            redirect: true,
            data: $('#updateHourlyPricingSettings').serialize(),
            success: function(data) {
                if (data.status == 'success') {
                    window.location.reload();
                }
            }
        })
    });

    $('body').on('click', '.depotLocation-edit-btn', function(e) {
        var depotlocationid = $(this).data('depotlocationid');
        $("#update_depotLocation_form_grid_" + depotlocationid).removeClass('hidden');
        $("#display_depotLocation_form_grid_" + depotlocationid).addClass('hidden');
    });

    $('body').on('click', '.depotLocation-cancelUpdate-btn', function(e) {

        var depotlocationid = $(this).data('depotlocationid');
        $("#update_depotLocation_form_grid_" + depotlocationid).addClass('hidden');
        $("#display_depotLocation_form_grid_" + depotlocationid).removeClass('hidden');
    });


    $('body').on('click', '.create_depotLocation_btn', function(e) {

        var createdepotrowid = $(this).data('createdepotrowid');
        var region_id           = $('#region_id_'+createdepotrowid).val();
        var depot_suburb        = $('#add_depot_suburb_'+createdepotrowid).val();

        $.ajax({
            url: "/admin/moving-settings/ajaxCreatedepotLocation",
            method: 'post',
            data: { "_token": "{{ csrf_token() }}", "region_id":region_id, "depot_suburb":depot_suburb
            },
            dataType: "json",
            beforeSend: function() {
                $.blockUI();
            },
            complete: function() {
                $.unblockUI();
            },
            success: function(result) {
                if (result.error == 0) {
                    window.location.reload();
                    //$('#regionalDepots-table tbody').html(result.depotlocation_html);
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

    $('body').on('click', '.update_depotLocation_btn', function(e) {
        e.preventDefault();
        var depotlocationid = $(this).data('depotlocationid');

        var region_id           = $('#region_id_'+depotlocationid).val();
        var depot_suburb        = $('#depot_suburb_'+depotlocationid).val();

        //alert(region_id);
        $.ajax({
            url: "/admin/moving-settings/ajaxUpdatedepotLocation",
            method: 'post',
            data: { "_token": "{{ csrf_token() }}", "depot_location_id":depotlocationid, "region_id":region_id, "depot_suburb":depot_suburb
            },
            dataType: "json",
            beforeSend: function() {
                $.blockUI();
            },
            complete: function() {
                $.unblockUI();
            },
            success: function(result) {
                if (result.error == 0) {
                    window.location.reload();
                    //$('#regionalDepots-table tbody').html(result.depotlocation_html);
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

    $('body').on('click', '.depotLocation-remove-btn', function() {
        var delete_id = $(this).data('depotlocationid');
        swal({
            title: "Are you sure?",
            text: "You want to delete this Regional Depot?",
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
                    url: "/admin/moving-settings/ajaxDestroyDepotLocation",
                    method: 'post',
                    data: { "_token": "{{ csrf_token() }}", "id":delete_id },
                    dataType: "json",
                    beforeSend: function() {
                        $.blockUI();
                    },
                    complete: function() {
                        $.unblockUI();
                    },
                    success: function(result) {

                        if (result.error == 2) {
                            swal({
                                title: "Info",
                                text: result.message,
                                type: "info",
                                button: "OK",
                            });
                        } else if (result.error == 0) {
                            window.location.reload();
                            //$('#regionalDepots-table tbody').html(result.depotlocation_html);
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
                        }
                    }
                });
            }
        });
    });



    $('body').on('click', '.truckSize-edit-btn', function(e) {

        e.preventDefault();
        var localmovesid = $(this).data('localmovesid');
        $("#update_truckSize_form_grid_" + localmovesid).removeClass('hidden');
        $("#display_truckSize_form_grid_" + localmovesid).addClass('hidden');
    });

    $('body').on('click', '.truckSize-cancelUpdate-btn', function(e) {

        e.preventDefault();
        var localmovesid = $(this).data('localmovesid');
        $("#update_truckSize_form_grid_" + localmovesid).addClass('hidden');
        $("#display_truckSize_form_grid_" + localmovesid).removeClass('hidden');
    });


    $('body').on('click', '.create_truckSize_btn', function(e) {

        var createrowid = $(this).data('createrowid');

        var min_cbm             = $('#min_cbm_'+createrowid).val();
        var max_cbm             = $('#max_cbm_'+createrowid).val();
        var truck_size_in_ton   = $('#truck_size_in_ton_'+createrowid).val();
        var loading_mins        = $('#loading_mins_'+createrowid).val();
        var unloading_mins      = $('#unloading_mins_'+createrowid).val();
        var hourly_rate         = $('#hourly_rate_'+createrowid).val();

        $.ajax({
            url: "/admin/moving-settings/ajaxCreateTruckSize",
            method: 'post',
            data: { "_token": "{{ csrf_token() }}", "min_cbm":min_cbm, "max_cbm":max_cbm, "truck_size_in_ton":truck_size_in_ton, "loading_mins":loading_mins, "unloading_mins":unloading_mins, "hourly_rate":hourly_rate

            },
            dataType: "json",
            beforeSend: function() {
                $.blockUI();
            },
            complete: function() {
                $.unblockUI();
            },
            success: function(result) {
              
                if (result.error == 0) {
                    $('#truckSize-table tbody').html(result.trucksize_html);
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

    $('body').on('click', '.update_truckSize_btn', function(e) {
        e.preventDefault();
        var localmovesid = $(this).data('localmovesid');

        var min_cbm             = $('#min_cbm_'+localmovesid).val();
        var max_cbm             = $('#max_cbm_'+localmovesid).val();
        var truck_size_in_ton   = $('#truck_size_in_ton_'+localmovesid).val();
        var loading_mins        = $('#loading_mins_'+localmovesid).val();
        var unloading_mins      = $('#unloading_mins_'+localmovesid).val();
        var hourly_rate         = $('#hourly_rate_'+localmovesid).val();

        //alert(min_cbm);
        $.ajax({
            url: "/admin/moving-settings/ajaxUpdateTruckSize",
            method: 'post',
            data: { "_token": "{{ csrf_token() }}", "local_moves_id":localmovesid, "min_cbm":min_cbm, "max_cbm":max_cbm, "truck_size_in_ton":truck_size_in_ton, "loading_mins":loading_mins, "unloading_mins":unloading_mins, "hourly_rate":hourly_rate

            },
            dataType: "json",
            beforeSend: function() {
                $.blockUI();
            },
            complete: function() {
                $.unblockUI();
            },
            success: function(result) {

                if (result.error == 0) {
                    //console.log(result.trucksize_html);
                    //var lead_id = result.id;
                    $('#truckSize-table tbody').html(result.trucksize_html);
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


    $('body').on('click', '.truckSize-remove-btn', function() {
        var delete_id = $(this).data('localmovesid');
        swal({
            title: "Are you sure?",
            text: "You want to delete this Truck size based rate?",
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
                    url: "/admin/moving-settings/ajaxDestroyTruckSize",
                    method: 'post',
                    data: { "_token": "{{ csrf_token() }}", "id":delete_id },
                    dataType: "json",
                    beforeSend: function() {
                        $.blockUI();
                    },
                    complete: function() {
                        $.unblockUI();
                    },
                    success: function(result) {

                        if (result.error == 2) {
                            swal({
                                title: "Info",
                                text: result.message,
                                type: "info",
                                button: "OK",
                            });
                        } else if (result.error == 0) {
                            
                            $('#truckSize-table tbody').html(result.trucksize_html);
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
                        }
                    }
                });
            }
        });
    });


    function deleteAddedRow(row_id)
    {
        $('#tr_trucksize'+row_id).remove();
    }

    function deleteDepotLocationAddedRow(row_id)
    {
        $('#tr_depotlocation'+row_id).remove();
    }

</script>

<script type="text/javascript">
    function setupAutocomplete(autocomplete, inputs, i) {
    console.log('setupAutocomplete...');

        // autocomplete[i] = new google.maps.places.Autocomplete(inputs[i], autocompleteOptions);
        autocomplete.push(new google.maps.places.Autocomplete(inputs[i], autocompleteOptions));
        var idx = autocomplete.length - 1;

        var target = $(event.target);
        if (target.hasClass('pac-item')) {
            // console.log(target.html());
            target.html(target.html().replace(/, Australia<\/span>/, "</span>"));
        }
        //autocomplete[i].bindTo('bounds', map);
        //autocomplete[idx].bindTo('bounds', map);

        //google.maps.event.addListener(autocomplete[i], 'place_changed', function() {
        
    }
    function initialize() {
        var options = {
            types: ['(cities)'],
            componentRestrictions: {
                country: "au"
            }
        };
        var allDepotInputs = document.getElementsByClassName('depot_suburb');

        for (var i = 0; i < allDepotInputs.length; i++) {
            //console.log(allDepotInputs[i]);
            var autocomplete = new google.maps.places.Autocomplete(allDepotInputs[i], options);
            autocomplete.inputId = allDepotInputs[i].id;
        }
    
        //var autocomplete = new google.maps.places.Autocomplete(input, options);

    }

    document.addEventListener('DOMNodeInserted', function(event) {
        // console.log(event);
        
    });

    function removeCountryNameAddCase(row_id)
    {
      setTimeout(function() {
            var newval = $('#add_depot_suburb_'+row_id).val().replace(', Australia', '');
            $('#add_depot_suburb_'+row_id).val(newval);
        }, 5);
    }

    //google.maps.event.addDomListener(window, 'load', initialize);

    /*$(document).on('change', '.depot_suburb', function() {
        setTimeout(function() {
            var newval = $('.depot_suburb').val().replace(', Australia', '');
            $('.depot_suburb').val(newval);
        }, 10);
    });*/
</script> 
@endpush