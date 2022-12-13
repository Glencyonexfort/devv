@extends('layouts.app')

@section('groupname_grid')
@include('admin.vehicle-groups.groupname_grid')
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

@section('content')

<div class="content">
    <div class="d-md-flex align-items-md-start">
            @include('sections.removal_setting_menu')
        <div style="flex:auto">
            <div class="card">
                <div class="card-header bg-white header-elements-inline">
                    <h6 class="card-title">@lang('modules.vehicleGroups.boxTitle')</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            

                            <div class="table-responsive">
                                <table class="table table-bordered" id="vehicleGroup-table">
                                    <thead>
                                        <tr>
                                            <th>@lang('modules.vehicleGroups.groupName')</th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody id="groupname_grid">
                                        @yield('groupname_grid')
                                    </tbody>

                                </table>

                            </div>

                            <button type="button" class="btn alpha-primary text-primary-800 btn-icon add_new_truck_size_row"><span class="cursor-pointer"><img src="{{ asset('newassets/img/icon-add.png') }}"></span></button>
                           
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

    $(".add_new_truck_size_row").click(function() {
       var rowCount = $('#vehicleGroup-table tr').length;
       $('#vehicleGroup-table').append('<tr id="tr_vehicleGroup'+rowCount+'"><td><input type="text" name="group_name" id="add_group_name_'+rowCount+'" class="form-control"></td><td><button type="submit" id="create_vehicleGroup_btn_'+rowCount+'"  data-createrowid="'+rowCount+'" class="btn btn-success m-r-10 btn-sm create_vehicleGroup_btn" style="padding:6px 6px;">Save</button><button id="delete_row_'+rowCount+'" type="button" onclick="deleteAddedRow('+rowCount+')" class="btn btn-light btn-sm" style="padding:6px 6px;">Cancel</button></td></tr>');
    });
    
    $('body').on('click', '.vehicleGroup-edit-btn', function(e) {

        e.preventDefault();
        var vehiclegroupid = $(this).data('vehiclegroupid');
        $("#update_vehicleGroup_form_grid_" + vehiclegroupid).removeClass('hidden');
        $("#display_vehicleGroup_form_grid_" + vehiclegroupid).addClass('hidden');
    });

    $('body').on('click', '.vehicleGroup-cancelUpdate-btn', function(e) {

        e.preventDefault();
        var vehiclegroupid = $(this).data('vehiclegroupid');
        $("#update_vehicleGroup_form_grid_" + vehiclegroupid).addClass('hidden');
        $("#display_vehicleGroup_form_grid_" + vehiclegroupid).removeClass('hidden');
    });


    $('body').on('click', '.create_vehicleGroup_btn', function(e) {

        var createrowid = $(this).data('createrowid');

        var group_name  = $('#add_group_name_'+createrowid).val();

        $.ajax({
            url: "/admin/moving-settings/ajaxCreateVehicleGroup",
            method: 'post',
            data: { "_token": "{{ csrf_token() }}", "group_name":group_name

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
                    $('#vehicleGroup-table tbody').html(result.vehicleGroups_html);
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
                }else if (result.error == 1) {
                    $.toast({
                        heading: 'Warning',
                        text: result.message,
                        icon: 'warning',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#fb9678',
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

    $('body').on('click', '.update_truckSize_btn', function(e) {
        e.preventDefault();
        var vehiclegroupid = $(this).data('vehiclegroupid');

        var group_name          = $('#group_name_'+vehiclegroupid).val();

        //alert(group_name);
        $.ajax({
            url: "/admin/moving-settings/ajaxUpdateVehicleGroup",
            method: 'post',
            data: { "_token": "{{ csrf_token() }}", "vehiclegroupid":vehiclegroupid, "group_name":group_name

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
                    //console.log(result.vehicleGroups_html);
                    $('#vehicleGroup-table tbody').html(result.vehicleGroups_html);
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
                }else if (result.error == 1) {
                    $.toast({
                        heading: 'Warning',
                        text: result.message,
                        icon: 'warning',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#fb9678',
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


    $('body').on('click', '.vehicleGroup-remove-btn', function() {
        var delete_id = $(this).data('vehiclegroupid');
        swal({
            title: "Are you sure?",
            text: "You want to delete this Vehicle Group?",
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
                    url: "/admin/moving-settings/ajaxDestroyVehicleGroup",
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
                            
                            $('#vehicleGroup-table tbody').html(result.vehicleGroups_html);
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
        $('#tr_vehicleGroup'+row_id).remove();
    }

</script>

@endpush