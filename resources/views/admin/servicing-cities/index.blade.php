@extends('layouts.app')

@section('partial_grid')
@include('admin.servicing-cities.partial_grid')
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
<script src="https://maps.googleapis.com/maps/api/js?key={{$tenant_api_details->account_key ?? ''}}&v=3.exp&libraries=places"></script>
<link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">

@endpush

@section('content')

<div class="content">
    <div class="d-md-flex align-items-md-start">
        @include('sections.admin_setting_menu')
        <div style="flex:auto">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="servicingCity-table">
                                    <thead>
                                        <tr>
                                            <th>@lang('modules.servicingCities.servicing_city')</th>
                                            <th width="20%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="partial_grid">
                                        @yield('partial_grid')
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn alpha-primary text-primary-800 btn-icon add_new_servicing_city_row"><span class="cursor-pointer"><img src="{{ asset('newassets/img/icon-add.png') }}"></span></button>
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

    $(".add_new_servicing_city_row").click(function() {
        var rowCount = $('#servicingCity-table tr').length;
        $('#servicingCity-table').append('<tr id="tr_servicingcity' + rowCount + '"><td><input type="text" name="servicing_city" onchange="removeCountryNameAddCase(' + rowCount + ')" id="servicing_city_' + rowCount + '" class="form-control servicing_city" /></td><td> <button type="submit" id="create_servicingCity_btn_' + rowCount + '" data-createrowid="' + rowCount + '" class="btn btn-success m-r-10 btn-sm create_servicingCity_btn" style="padding: 6px 6px;">Save</button> <button id="delete_servicingCity_row_' + rowCount + '" type="button" onclick="deleteAddedRow(' + rowCount + ')" class="btn btn-light btn-sm" style="padding: 6px 6px;">Cancel</button></td></tr> ');
        initialize();
    });


    $('body').on('click', '.servicingCity-edit-btn', function(e) {

        e.preventDefault();
        var rowid = $(this).data('rowid');
        $("#update_servicingCity_form_grid_" + rowid).removeClass('hidden');
        $("#display_servicingCity_form_grid_" + rowid).addClass('hidden');
    });

    $('body').on('click', '.servicingCity-cancelUpdate-btn', function(e) {

        e.preventDefault();
        var rowid = $(this).data('rowid');
        $("#update_servicingCity_form_grid_" + rowid).addClass('hidden');
        $("#display_servicingCity_form_grid_" + rowid).removeClass('hidden');
    });

    $('body').on('click', '.create_servicingCity_btn', function(e) {

        var createrowid = $(this).data('createrowid');
        var servicing_city = $('#servicing_city_' + createrowid).val();


        $.ajax({
            url: "/admin/settings/ajaxCreateServicingCities",
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                "servicing_city": servicing_city
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
                    // $('#servicingCity-table tbody').html(result.response_html);
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

    $('body').on('click', '.update_servicingCity_btn', function(e) {
        e.preventDefault();
        var rowid = $(this).data('rowid');
        var servicing_city = $('#servicing_city_' + rowid).val();

        //alert(min_cbm);
        $.ajax({
            url: "/admin/settings/ajaxUpdateServicingCities",
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                "row_id": rowid,
                "servicing_city": servicing_city

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
                    // console.log(result.response_html);
                    //var lead_id = result.id;
                    window.location.reload();
                    // $('#servicingCity-table tbody').html(result.response_html);
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

    $('body').on('click', '.servicingCity-remove-btn', function() {
        var delete_id = $(this).data('rowid');
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
                    url: "/admin/settings/ajaxDestroyServicingCities",
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
                            // $('#servicingCity-table tbody').html(result.response_html);
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
        $('#tr_servicingcity' + row_id).remove();
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
        var allDepotInputs = document.getElementsByClassName('servicing_city');

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
            if ($('#servicing_city_' + row_id).val() != '') {
                var newval = $('#servicing_city_' + row_id).val().replace(', Australia', '');
                $('#servicing_city_' + row_id).val(newval);
            }
        }, 5);
    }

    function removeCountryNameEditCase(row_id) {
        setTimeout(function() {
            if ($('#servicing_city_' + row_id).val() != '') {
                var newval = $('#servicing_city_' + row_id).val().replace(', Australia', '');
                $('#servicing_city_' + row_id).val(newval);
            }
        }, 5);
    }
</script>


@endpush