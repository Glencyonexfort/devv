@extends('layouts.app')

@section('pricing_region_grid')
@include('admin.pricing-region.pricing_region_grid')
@endsection

@section('page-title')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="{{ $pageIcon }} mr-2"></i> <span class="font-weight-semibold">{{ $pageTitle }}</span></h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
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
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="regionPricing-table">
                                    <thead>
                                        <tr>
                                            <th>@lang('modules.pricingRegion.state')</th>
                                            <th>@lang('modules.pricingRegion.region_name')</th>
                                            <th>@lang('modules.pricingRegion.region_subrub_name')</th>
                                            <th width="20%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="pricing_region_grid">
                                        @yield('pricing_region_grid')
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn alpha-primary text-primary-800 btn-icon add_new_region_pricing_row"><span class="cursor-pointer"><img src="{{ asset('newassets/img/icon-add.png') }}"></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/switchery/dist/switchery.min.js') }}"></script>
<script>
    // var autocomplete = [];

    // var autocompleteOptions = {
    //     types: ['(cities)'],
    //     componentRestrictions: {
    //         country: "au"
    //     }
    // };

    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    $('.js-switch').each(function() {
        new Switchery($(this)[0], $(this).data());
    });

    $(".add_new_region_pricing_row").click(function() {
        var rowCount = $('#regionPricing-table tr').length;
        $('#regionPricing-table').append('<tr id="tr_regionpricing' + rowCount + '"><td> <select name="state_id" id="state_id_' + rowCount + '" class="form-control"> @foreach($sys_country_states as $data)<option value="{{$data->state_id}}">{{$data->state_code}}</option> @endforeach </select></td><td><input type="text" name="region_name" id="region_name_' + rowCount + '" class="form-control" /></td><td><input type="text" name="region_suburb_name" onchange="removeCountryNameAddCase(' + rowCount + ')" id="region_suburb_name_' + rowCount + '" class="form-control region_suburb_name" /></td><td> <button type="submit" id="create_regionPricing_btn_' + rowCount + '" data-createrowid="' + rowCount + '" class="btn btn-success m-r-10 btn-sm create_regionPricing_btn" style="padding: 6px 6px;">Save</button> <button id="delete_regionPricing_row_' + rowCount + '" type="button" onclick="deleteAddedRow(' + rowCount + ')" class="btn btn-light btn-sm" style="padding: 6px 6px;">Cancel</button></td></tr> ');
        initialize();
    });


    $('body').on('click', '.regionPricing-edit-btn', function(e) {

        e.preventDefault();
        var pricingregionid = $(this).data('pricingregionid');
        $("#update_regionPricing_form_grid_" + pricingregionid).removeClass('hidden');
        $("#display_regionPricing_form_grid_" + pricingregionid).addClass('hidden');
    });

    $('body').on('click', '.regionPricing-cancelUpdate-btn', function(e) {

        e.preventDefault();
        var pricingregionid = $(this).data('pricingregionid');
        $("#update_regionPricing_form_grid_" + pricingregionid).addClass('hidden');
        $("#display_regionPricing_form_grid_" + pricingregionid).removeClass('hidden');
    });

    $('body').on('click', '.create_regionPricing_btn', function(e) {

        var createrowid = $(this).data('createrowid');

        var state_id = $('#state_id_' + createrowid).val();
        var region_name = $('#region_name_' + createrowid).val();
        var region_suburb_name = $('#region_suburb_name_' + createrowid).val();


        $.ajax({
            url: "/admin/moving-settings/ajaxCreateRegionPricing",
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                "state_id": state_id,
                "region_name": region_name,
                "region_suburb_name": region_suburb_name
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
                    // $('#regionPricing-table tbody').html(result.regionpricing_html);
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

    $('body').on('click', '.update_regionPricing_btn', function(e) {
        e.preventDefault();
        var pricingregionid = $(this).data('pricingregionid');

        var state_id = $('#state_id_' + pricingregionid).val();
        var region_name = $('#region_name_' + pricingregionid).val();
        var region_suburb_name = $('#region_suburb_name_' + pricingregionid).val();

        //alert(min_cbm);
        $.ajax({
            url: "/admin/moving-settings/ajaxUpdateRegionPricing",
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                "pricing_region_id": pricingregionid,
                "state_id": state_id,
                "region_name": region_name,
                "region_suburb_name": region_suburb_name

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
                    // console.log(result.regionpricing_html);
                    //var lead_id = result.id;
                    window.location.reload();
                    // $('#regionPricing-table tbody').html(result.regionpricing_html);
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

    $('body').on('click', '.regionPricing-remove-btn', function() {
        var delete_id = $(this).data('pricingregionid');
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
                    url: "/admin/moving-settings/ajaxDestroyRegionPricing",
                    method: 'post',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "id": delete_id
                    },
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
                            // $('#regionPricing-table tbody').html(result.regionpricing_html);
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


    function deleteAddedRow(row_id) {
        $('#tr_regionpricing' + row_id).remove();
    }
</script>
<script type="text/javascript">
    function initialize() {
        var options = {
            types: ['(cities)'],
            componentRestrictions: {
                country: "au"
            }
        };
        var allDepotInputs = document.getElementsByClassName('region_suburb_name');

        for (var i = 0; i < allDepotInputs.length; i++) {
            //console.log(allDepotInputs[i]);
            var autocomplete = new google.maps.places.Autocomplete(allDepotInputs[i], options);
            autocomplete.inputId = allDepotInputs[i].id;
        }

        //var autocomplete = new google.maps.places.Autocomplete(input, options);

    }

    google.maps.event.addDomListener(window, 'load', initialize);

    document.addEventListener('DOMNodeInserted', function(event) {
        // console.log(event);

    });

    function removeCountryNameAddCase(row_id) {
        setTimeout(function() {
            if ($('#region_suburb_name_' + row_id).val() != '') {
                var newval = $('#region_suburb_name_' + row_id).val().replace(', Australia', '');
                $('#region_suburb_name_' + row_id).val(newval);
            }
        }, 5);
    }

    function removeCountryNameEditCase(row_id) {
        setTimeout(function() {
            if ($('#region_suburb_name_' + row_id).val() != '') {
                var newval = $('#region_suburb_name_' + row_id).val().replace(', Australia', '');
                $('#region_suburb_name_' + row_id).val(newval);
            }
        }, 5);
    }
</script>


@endpush