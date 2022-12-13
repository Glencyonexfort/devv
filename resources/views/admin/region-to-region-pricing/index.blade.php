@extends('layouts.app')

@section('region_pricing_grid')
@include('admin.region-to-region-pricing.region_pricing_grid')
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
                        <div class="col-sm-4 col-xs-6 pull-right">
                            <div class="form-group">
                                <input type="text" id="searchText" onkeyup="searchPilgrims()" placeholder="Search.." class="form-control" style="margin-bottom:15px;">
                            </div>
                        </div>
                        <div class="col-sm-12 col-xs-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="regionToRegionPricings-table">
                                    <thead>
                                        <tr>
                                            <th>@lang('modules.regionToRegionPricing.from_region')</th>
                                            <th>@lang('modules.regionToRegionPricing.to_region')</th>
                                            <th>@lang('modules.regionToRegionPricing.from_m3')</th>
                                            <th>@lang('modules.regionToRegionPricing.to_m3')</th>
                                            <th>@lang('modules.regionToRegionPricing.flat_price')</th>
                                            <th>@lang('modules.regionToRegionPricing.price_per_m3')</th>
                                            <th>@lang('modules.regionToRegionPricing.min_price')</th>
                                            <th width="20%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="region_pricing_grid">
                                        @yield('region_pricing_grid')
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
        var rowCount = $('#regionToRegionPricings-table tr').length;
        $('#regionToRegionPricings-table').append('<tr id="tr_regionpricing' + rowCount + '"><td> <select name="from_region_id" id="from_region_id_' + rowCount + '" class="form-control"> @foreach($jobs_pricing_regions as $data)<option value="{{$data->id}}">{{$data->region_name}}</option> @endforeach </select></td><td> <select name="to_region_id" id="to_region_id_' + rowCount + '" class="form-control"> @foreach($jobs_pricing_regions as $data)<option value="{{$data->id}}">{{$data->region_name}}</option> @endforeach </select></td><td><input type="text" name="cbm_min" value="" id="cbm_min_' + rowCount + '" class="form-control" /></td><td><input type="text" name="cbm_max" value="" id="cbm_max_' + rowCount + '" class="form-control" /></td><td><input type="text" name="price_flat" value="" id="price_flat_' + rowCount + '" class="form-control" /></td><td><input type="text" name="price_per_cbm" value="" id="price_per_cbm_' + rowCount + '" class="form-control" /></td><td><input type="text" name="min_price" value="" id="min_price_' + rowCount + '" class="form-control" /></td><td> <button type="submit" id="create_regionToRegionPricings_btn_' + rowCount + '" data-createrowid="' + rowCount + '" class="btn btn-success m-r-10 btn-sm create_regionToRegionPricings_btn" style="padding: 6px 6px;">Save</button> <button id="delete_regionToRegionPricings_row_' + rowCount + '" type="button" onclick="deleteAddedRow(' + rowCount + ')" class="btn btn-light btn-sm" style="padding: 6px 6px;">Cancel</button></td></tr>');
        initialize();
    });


    $('body').on('click', '.regionToRegionPricings-edit-btn', function(e) {

        e.preventDefault();
        var pricingregionid = $(this).data('pricingregionid');
        $("#update_regionToRegionPricings_form_grid_" + pricingregionid).removeClass('hidden');
        $("#display_regionToRegionPricings_form_grid_" + pricingregionid).addClass('hidden');
    });

    $('body').on('click', '.regionToRegionPricings-cancelUpdate-btn', function(e) {

        e.preventDefault();
        var pricingregionid = $(this).data('pricingregionid');
        $("#update_regionToRegionPricings_form_grid_" + pricingregionid).addClass('hidden');
        $("#display_regionToRegionPricings_form_grid_" + pricingregionid).removeClass('hidden');
    });

    $('body').on('click', '.create_regionToRegionPricings_btn', function(e) {

        var createrowid = $(this).data('createrowid');

        var from_region_id = $('#from_region_id_' + createrowid).val();
        var to_region_id = $('#to_region_id_' + createrowid).val();
        var cbm_min = $('#cbm_min_' + createrowid).val();
        var cbm_max = $('#cbm_max_' + createrowid).val();
        var price_flat = $('#price_flat_' + createrowid).val();
        var price_per_cbm = $('#price_per_cbm_' + createrowid).val();
        var min_price = $('#min_price_' + createrowid).val();

        $.ajax({
            url: "/admin/moving-settings/ajaxCreateRegionToRegionPricing",
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                "from_region_id": from_region_id,
                "to_region_id": to_region_id,
                "cbm_min": cbm_min,
                "cbm_max": cbm_max,
                "price_flat": price_flat,
                "price_per_cbm": price_per_cbm,
                "min_price": min_price
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
                    // $('#regionToRegionPricings-table tbody').html(result.regionpricing_html);
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

    $('body').on('click', '.update_regionToRegionPricings_btn', function(e) {
        e.preventDefault();
        var createrowid = $(this).data('pricingregionid');

        var from_region_id = $('#from_region_id_' + createrowid).val();
        var to_region_id = $('#to_region_id_' + createrowid).val();
        var cbm_min = $('#cbm_min_' + createrowid).val();
        var cbm_max = $('#cbm_max_' + createrowid).val();
        var price_flat = $('#price_flat_' + createrowid).val();
        var price_per_cbm = $('#price_per_cbm_' + createrowid).val();
        var min_price = $('#min_price_' + createrowid).val();

        //alert(min_cbm);
        $.ajax({
            url: "/admin/moving-settings/ajaxUpdateRegionToRegionPricing",
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                "pricing_region_id": createrowid,
                "from_region_id": from_region_id,
                "to_region_id": to_region_id,
                "cbm_min": cbm_min,
                "cbm_max": cbm_max,
                "price_flat": price_flat,
                "price_per_cbm": price_per_cbm,
                "min_price": min_price
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
                    // $('#regionToRegionPricings-table tbody').html(result.regionpricing_html);
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

    $('body').on('click', '.regionToRegionPricings-remove-btn', function() {
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
                    url: "/admin/moving-settings/ajaxDestroyRegionToRegionPricing",
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
                            // $('#regionToRegionPricings-table tbody').html(result.regionpricing_html);
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
<script>
    function searchPilgrims() {
        // Declare variables
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchText");
        filter = input.value.toUpperCase();
        table = document.getElementById("regionToRegionPricings-table");
        tr = table.getElementsByTagName("tr");
        // Loop through all table rows, and hide those who don't match the search query
        for (i = 1; i < tr.length; i++) {
            var tds = [];
            tds[0] = tr[i].getElementsByTagName("td")[0];
            tds[1] = tr[i].getElementsByTagName("td")[1];
            tds[2] = tr[i].getElementsByTagName("td")[2];
            tds[3] = tr[i].getElementsByTagName("td")[3];
            tds[4] = tr[i].getElementsByTagName("td")[4];
            tds[5] = tr[i].getElementsByTagName("td")[5];
            tds[5] = tr[i].getElementsByTagName("td")[6];
            // console.log(tds);
            var found = false;
            tds.forEach(function myFunction(item, index) {
                // document.getElementById("demo").innerHTML += index + ":" + item + "<br>";
                td = tds[index];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        // tr[i].style.display = "";
                        found = true;
                    } else {
                        // tr[i].style.display = "none";
                    }
                }
            });
            if (found) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
</script>
@endpush