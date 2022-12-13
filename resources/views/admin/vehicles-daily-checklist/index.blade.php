@extends('layouts.app')

@section('group_grid')
@include('admin.vehicles-daily-checklist.group_grid')
@endsection

@section('chooseGroupChecklist_grid')
@include('admin.vehicles-daily-checklist.chooseGroupChecklist_grid')
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
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            

                            <legend class="font-size-lg font-weight-bold">Checklist Group</legend>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="group-table">
                                    <thead>
                                        <tr>
                                            <th>@lang('app.menu.group')</th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody id="group_grid">
                                        @yield('group_grid')
                                    </tbody>

                                </table>

                            </div>

                            <button type="button" class="btn alpha-primary text-primary-800 btn-icon add_new_group_row"><span class="cursor-pointer"><img src="{{ asset('newassets/img/icon-add.png') }}"></span></button>


                            <legend class="font-size-lg font-weight-bold m-t-30"><mark>Checklist</mark></legend>

                            
                            <div class="form-body">
                                <div class="form-group row">
                                    <label class="col-form-label col-lg-3">@lang('app.menu.chooseGroup')</label>                                    
                                    <div class="col-lg-4">
                                        <div class="input-group" id="chooseGroupChecklist_grid">
                                            @yield('chooseGroupChecklist_grid')                                     
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-12 text-left m-t-10 m-b-20">
                                        <!-- <hr> -->
                                        <button type="submit" id="load-group-checklist" class="btn btn-success m-r-10"><i class="fa fa-check"></i> @lang('app.menu.loadChecklist')</button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="group-checklist-table">
                                    <thead>
                                        <tr>
                                            <th>@lang('app.menu.group')</th>
                                            <th>@lang('app.menu.checklist')</th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody id="group_checklist_grid">
                                       
                                    </tbody>

                                </table>

                            </div>

                            <button type="button" class="btn alpha-primary text-primary-800 btn-icon add_new_group_checklist_row"><span class="cursor-pointer"><img src="{{ asset('newassets/img/icon-add.png') }}"></span></button>

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

    $("#load-group-checklist").click(function() {
       var group_id = $('#choose_group_checklist').val();

       if(group_id == null || group_id == ''){
        $('#group-checklist-table tbody').html('');
       }

       $.ajax({
            url: "{{ route('admin.vehiclesDailyChecklist.groupChecklist.load') }}",
            method: 'post',
            data: { "_token": "{{ csrf_token() }}", "group_id":group_id

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
                    $('#group-checklist-table tbody').html(result.groupChecklist_html);

                } else {
                    //Notification....
                    $.toast({
                        heading: 'Error',
                        text: 'Please choose list type to proceed.',
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

    $(".add_new_group_checklist_row").click(function() {
       var rowCount = $('#group-checklist-table tr').length;
       $.ajax({
            url: "{{ route('admin.vehiclesDailyChecklist.groupChecklist.get') }}",
            method: 'post',
            data: { "_token": "{{ csrf_token() }}"

            },
            dataType: "json",
            success: function(result) {    
                $('#group-checklist-table').append('<tr id="tr_group_checklist'+rowCount+'"><td><select name="checklist_id" class="form-control" id="group_id_'+rowCount+'">'+result+'</select></td><td><input type="text" name="checklist_option" id="checklist_'+rowCount+'" class="form-control"></td><td><button type="submit" id="create_group_checklist_btn_'+rowCount+'"  data-createGroupChecklistid="'+rowCount+'" class="btn btn-success m-r-10 btn-sm create_group_checklist_btn" style="padding:6px 6px;">Save</button><button id="delete_group_checklist_row_'+rowCount+'" type="button" onclick="deleteGroupChecklistAddedRow('+rowCount+')" class="btn btn-light btn-sm" style="padding:6px 6px;">Cancel</button></td></tr>');
            }
        });

       

    });

    $('body').on('click', '.group-checklist-edit-btn', function(e) {

        e.preventDefault();
        var groupChecklistId = $(this).data('groupchecklistid');
        $("#update_group_checklist_form_grid_" + groupChecklistId).removeClass('hidden');
        $("#display_group_checklist_form_grid_" + groupChecklistId).addClass('hidden');
    });

    $('body').on('click', '.groupChecklist-cancelUpdate-btn', function(e) {

        e.preventDefault();
        var groupChecklistId = $(this).data('groupchecklistid');
        $("#update_group_checklist_form_grid_" + groupChecklistId).addClass('hidden');
        $("#display_group_checklist_form_grid_" + groupChecklistId).removeClass('hidden');
    });


    $('body').on('click', '.create_group_checklist_btn', function(e) {

        var createrowid = $(this).data('creategroupchecklistid');

        var group_id   = $('#group_id_'+createrowid).val();
        var checklist    = $('#checklist_'+createrowid).val();

        $.ajax({
            url: "{{ route('admin.vehiclesDailyChecklist.groupChecklist.store') }}",
            method: 'post',
            data: { "_token": "{{ csrf_token() }}", "group_id":group_id, "checklist":checklist

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
                    $('#group-checklist-table tbody').html(result.groupChecklist_html);
                    $("#choose_group_checklist").val(result.group_id).change();
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
                        text: result.message,
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


     $('body').on('click', '.update_groupChecklist_btn', function(e) {
        e.preventDefault();
        var groupChecklistId = $(this).data('groupchecklistid');

        var group_id   = $('#group_id_'+groupChecklistId).val();
        var checklist    = $('#checklist_'+groupChecklistId).val();

        var selected_group_id    = $('#choose_group_checklist').val();

        //alert(listoptionid);
        $.ajax({
            url: "{{ route('admin.vehiclesDailyChecklist.groupChecklist.update') }}",
            method: 'post',
            data: { "_token": "{{ csrf_token() }}", "group_id":group_id, "checklist":checklist, "update_id": groupChecklistId, "selected_group_id":selected_group_id

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
                    $('#group-checklist-table tbody').html(result.groupChecklist_html);
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
                        text: result.message,
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

    $(".add_new_group_row").click(function() {
       var rowCount = $('#group-table tbody tr').length;
       $('#group-table').append('<tr id="tr_group'+rowCount+'"><td><input type="text" name="checklist_group" id="checklist_group_'+rowCount+'add" class="form-control"></td><td><button type="submit" id="create_group_btn_'+rowCount+'"  data-createrowid="'+rowCount+'" class="btn btn-success m-r-10 btn-sm create_group_btn" style="padding:6px 6px;">Save</button><button id="delete_row_'+rowCount+'" type="button" onclick="deleteAddedRow('+rowCount+')" class="btn btn-light btn-sm" style="padding:6px 6px;">Cancel</button></td></tr>');
    });

    $('body').on('click', '.create_group_btn', function(e) {

        var createrowid = $(this).data('createrowid');
        var checklist_group   = $('#checklist_group_'+createrowid+'add').val();
        
        $.ajax({
            url: "{{ route('admin.vehiclesDailyChecklist.group.store') }}",
            method: 'post',
            data: { "_token": "{{ csrf_token() }}", "checklist_group": checklist_group

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
                    $('#group-table tbody').html(result.group_html);
                    $('#chooseGroupChecklist_grid').html(result.chooseGroupList_html);
                    $('#group-checklist-table tbody').html('');
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
                        text: result.message,
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


    $('body').on('click', '.group-checklist-remove-btn', function() {
        var delete_id = $(this).data('groupchecklistid');
        var selected_group_id    = $('#choose_group_checklist').val();
        swal({
            title: "Are you sure?",
            text: "You want to delete this Group Checklist?",
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
                    url: "{{ route('admin.vehiclesDailychecklist.groupChecklist.destroy') }}",
                    method: 'post',
                    data: { "_token": "{{ csrf_token() }}", "id":delete_id, "selected_group_id":selected_group_id },
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
                            
                            $('#group-checklist-table tbody').html(result.groupChecklist_html);
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

    $('body').on('click', '.update_group_btn', function(e) {
        e.preventDefault();
        var group_id = $(this).data('localmovesid');

        var checklist_group   = $('#checklist_group_'+group_id).val();

        // alert(checklist_group);
        $.ajax({
            url: "{{ route('admin.vehiclesDailyChecklist.group.update') }}",
            method: 'post',
            data: { "_token": "{{ csrf_token() }}", "group_id":group_id, "checklist_group":checklist_group

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
                    $('#group-table tbody').html(result.group_html);
                    $('#chooseGroupChecklist_grid').html(result.chooseGroupList_html);
                    $('#group-checklist-table tbody').html('');
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
                        text: result.message,
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
  

    $('body').on('click', '.group-remove-btn', function() {
        var delete_id = $(this).data('localmovesid');
        swal({
            title: "Are you sure?",
            text: "You want to delete this Group?",
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
                    url: "{{ route('admin.vehiclesDailychecklist.group.destroy') }}",
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
                                title: "Info",
                                text: result.message,
                                type: "info",
                                button: "OK",
                            });
                        } else if (result.error == 0) {
                            
                            $('#group-table tbody').html(result.group_html);
                            $('#chooseGroupChecklist_grid').html(result.chooseGroupList_html);
                            $('#group-checklist-table tbody').html('');
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

    $('body').on('click', '.group-edit-btn', function(e) {

        e.preventDefault();
        var localmovesid = $(this).data('localmovesid');
        $("#update_group_form_grid_" + localmovesid).removeClass('hidden');
        $("#display_group_form_grid_" + localmovesid).addClass('hidden');
    });

    $('body').on('click', '.group-cancelUpdate-btn', function(e) {

        e.preventDefault();
        var localmovesid = $(this).data('localmovesid');
        $("#update_group_form_grid_" + localmovesid).addClass('hidden');
        $("#display_group_form_grid_" + localmovesid).removeClass('hidden');
    });

    function deleteAddedRow(row_id)
    {
        $('#tr_group'+row_id).remove();
    }

    function deleteGroupChecklistAddedRow(row_id)
    {
        $('#tr_group_checklist'+row_id).remove();
    }

</script>

@endpush