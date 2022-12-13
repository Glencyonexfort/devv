@extends('layouts.app')

@section('tax_grid')
    @include('admin.taxes.tax_grid')
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
                                    <table class="table table-bordered" id="tax-table">
                                        <thead>
                                            <tr>
                                                <th>Tax</th>
                                                <th>Rate Percent</th>
                                                <th class="text-center" width="20%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tax_grid">
                                            @yield('tax_grid')
                                        </tbody>
                                    </table>
                                </div>
                                <button id="add_new_tax_row" type="button" class="btn alpha-primary text-primary-800 btn-icon"><span class="cursor-pointer"><img src="{{ asset('newassets/img/icon-add.png') }}"></span></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-script')
    <script>
    $(document).ready(function() {
        $('body').on('click', '#add_new_tax_row', function(e) {
            e.preventDefault();
            $(this).hide();
            $("#new_tax_form_grid").css("display", "table-row");
        });    
        $('body').on('click', '#cancel_new_tax_row', function(e) {        
            e.preventDefault();
            $("#new_tax_form_grid").hide();
            $("#add_new_tax_row").show();
        });
        $('body').on('click', '.cancel_update_tax_row', function(e) {
            var id = $(this).data('row_id');
            e.preventDefault();
            $("#update_tax_form_grid_"+id).hide();
            $("#display_tax_form_grid_"+id).css("display", "table-row");
        });
        $('body').on('click', '.edit_tax_row', function(e) {
            var id = $(this).data('row_id');
            e.preventDefault();
            $("#display_tax_form_grid_"+id).hide();
            $("#update_tax_form_grid_"+id).css("display", "table-row");        
        });
    });
        

        $('body').on('click', '#save_new_tax', function(e) {
            var tax_name = $('#tax_name_new').val();
            var rate_percent = $('#rate_percent_new').val();
            $.ajax({
                url: "/admin/finance/ajaxCreateTax",
                method: 'post',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "tax_name": tax_name,
                    "rate_percent": rate_percent,
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
                        //window.location.reload();
                        $('#tax_grid').html(result.html);
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

        $('body').on('click', '.update_tax_btn', function(e) {
            e.preventDefault();
            var id = $(this).data('row_id');
            var tax_name = $('#tax_name_'+id).val();
            var rate_percent = $('#rate_percent_'+id).val();

            //alert(min_cbm);
            $.ajax({
                url: "/admin/finance/ajaxUpdateTax",
                method: 'post',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": id,
                    "tax_name": tax_name,
                    "rate_percent": rate_percent
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
                        $('#tax_grid').html(result.html);
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

        $('body').on('click', '.remove_tax_btn', function() {
            var id = $(this).data('row_id');
            swal({
                title: "Are you sure?",
                text: "You want to delete this Tax?",
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
                        url: "/admin/finance/ajaxDestroyTax",
                        method: 'post',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "id": id
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
                                //window.location.reload();
                                $('#tax_grid').html(result.html);
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
