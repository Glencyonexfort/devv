@extends('layouts.app')

@section('roles_permission_grid')
@include('admin.role-permission.roles_permission_grid')
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

@section('content')

<div class="content">
    <div class="d-md-flex align-items-md-start">
            @include('sections.people_setting_menu')
        <div style="flex:auto">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            

                            <div class="table-responsive">
                                <table class="table table-bordered" id="roles-table">
                                    <thead>
                                        <tr>
                                            <th style="width:30%">Role Name</th>
                                            <th style="width:50%">Description</th>
                                            <th style="width:20%">Action</th>
                                        </tr>
                                    </thead>

                                    <tbody id="roles_permission_grid">
                                        @yield('roles_permission_grid')
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
       var rowCount = $('#roles-table tr').length;
       $('#roles-table').append('<tr id="tr_roles'+rowCount+'"><td><input type="text" name="display_name" id="add_display_name_'+rowCount+'" class="form-control"></td><td><input type="text" name="description" id="add_description_'+rowCount+'" class="form-control"></td><td><button type="submit" id="create_roles_btn_'+rowCount+'"  data-createrowid="'+rowCount+'" class="btn btn-success m-r-10 btn-sm create_roles_btn" style="padding:6px 6px;">Save</button><button id="delete_row_'+rowCount+'" type="button" onclick="deleteAddedRow('+rowCount+')" class="btn btn-light btn-sm" style="padding:6px 6px;">Cancel</button></td></tr>');
    });
    
    $('body').on('click', '.roles-edit-btn', function(e) {

        e.preventDefault();
        var id = $(this).data('id');
        $("#update_roles_form_grid_" + id).removeClass('hidden');
        $("#display_roles_form_grid_" + id).addClass('hidden');
    });

    $('body').on('click', '.roles-cancelUpdate-btn', function(e) {

        e.preventDefault();
        var id = $(this).data('id');
        $("#update_roles_form_grid_" + id).addClass('hidden');
        $("#display_roles_form_grid_" + id).removeClass('hidden');
    });


    $('body').on('click', '.create_roles_btn', function(e) {

        var createrowid = $(this).data('createrowid');

        var display_name  = $('#add_display_name_'+createrowid).val();
        var description  = $('#add_description_'+createrowid).val();

        $.ajax({
            url: "/admin/peopleoperations/ajax-create-role",
            method: 'post',
            data: { "_token": "{{ csrf_token() }}", "display_name":display_name,"description":description

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
                    $('#roles-table tbody').html(result.html);
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

    $('body').on('click', '.update_role_btn', function(e) {
        var id = $(this).data('id');

        var display_name  = $('#edit_display_name_'+id).val();
        var description  = $('#edit_description_'+id).val();

        $.ajax({
            url: "/admin/peopleoperations/ajax-update-role",
            method: 'post',
            data: { "_token": "{{ csrf_token() }}", "display_name":display_name,"description":description,"id":id

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
                    $('#roles-table tbody').html(result.html);
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
            },
            error: function( xhr, ajaxOptions, thrownError ) {
                var response = JSON.parse(xhr.responseText);
                var errorString = '';
                $.each( response.errors, function( key, value) {
                    errorString += '\n' + value;
                });
                swal({
                    title: "Error",
                    text: errorString,
                    type: "error",
                    button: "OK",
                });
        }
        });
});



    $('body').on('click', '.roles-remove-btn', function() {
        var delete_id = $(this).data('id');
        swal({
            title: "Are you sure?",
            text: "You want to delete this Role?",
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
                    url: "/admin/peopleoperations/ajax-destroy-role",
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

                        if (result.error == 1) {
                            swal({
                                title: "Warning",
                                text: result.message,
                                type: "warning",
                                button: "OK",
                            });
                        } else if (result.error == 0) {
                            
                            $('#roles-table tbody').html(result.html);
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
        $('#tr_roles'+row_id).remove();
    }

</script>

@endpush