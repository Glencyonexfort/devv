@extends('layouts.app')

@section('product_categories_grid')
    @include('admin.product-categories.product_categories_grid')
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
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB2SMtaVBlqC5v72gqS716BX8R5oXklaFc&v=3.exp&libraries=places">
    </script>
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">

@endpush

@section('content')

    <div class="content">
        <div class="d-md-flex align-items-md-start">
            @include('sections.admin_finance_setting_menu')
            <div style="flex:auto">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="productCategories-table">
                                        <thead>
                                            <tr>
                                                <th>@lang('modules.ProductCategories.category_name')</th>
                                                <th class="text-center" width="20%">@lang('modules.ProductCategories.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody id="product_categories_grid">
                                            @yield('product_categories_grid')
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
            var rowCount = $('#productCategories-table tr').length;
            $('#productCategories-table').append('<tr id="tr_regionpricing' + rowCount +
                '"><td><input type="text" name="category_name" id="new_category_name_' + rowCount +
                '" class="form-control" /></td><td> <button type="submit" id="create_productCategories_btn_' +
                rowCount + '" data-createrowid="' + rowCount +
                '" class="btn btn-success m-r-10 btn-sm create_productCategories_btn" style="padding: 6px 6px;">Save</button> <button id="delete_productCategories_row_' +
                rowCount + '" type="button" onclick="deleteAddedRow(' + rowCount +
                ')" class="btn btn-light btn-sm" style="padding: 6px 6px;">Cancel</button></td></tr> ');
            initialize();
        });


        $('body').on('click', '.productCategories-edit-btn', function(e) {

            e.preventDefault();
            var row_id = $(this).data('row_id');
            $("#update_productCategories_form_grid_" + row_id).removeClass('hidden');
            $("#display_productCategories_form_grid_" + row_id).addClass('hidden');
        });

        $('body').on('click', '.productCategories-cancelUpdate-btn', function(e) {

            e.preventDefault();
            var row_id = $(this).data('row_id');
            $("#update_productCategories_form_grid_" + row_id).addClass('hidden');
            $("#display_productCategories_form_grid_" + row_id).removeClass('hidden');
        });

        $('body').on('click', '.create_productCategories_btn', function(e) {

            var createrowid = $(this).data('createrowid');

            var category_name = $('#new_category_name_' + createrowid).val();

            $.ajax({
                url: "/admin/finance/ajaxCreateProductCategories",
                method: 'post',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "category_name": category_name
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
                        // $('#productCategories-table tbody').html(result.response_html);
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

        $('body').on('click', '.update_productCategories_btn', function(e) {
            e.preventDefault();
            var row_id = $(this).data('row_id');

            var category_name = $('#category_name_' + row_id).val();

            //alert(min_cbm);
            $.ajax({
                url: "/admin/finance/ajaxUpdateProductCategories",
                method: 'post',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "product_categories_id": row_id,
                    "category_name": category_name

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
                        // $('#productCategories-table tbody').html(result.response_html);
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

        $('body').on('click', '.productCategories-remove-btn', function() {
            var delete_id = $(this).data('row_id');
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
                        url: "/admin/finance/ajaxDestroyProductCategories",
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
                                // $('#productCategories-table tbody').html(result.response_html);
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
