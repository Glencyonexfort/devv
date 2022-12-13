@extends('layouts.app')

@section('listtype_grid')
@include('admin.list-type-and-options.listtype_grid')
@endsection

@section('chooseListType_grid')
@include('admin.list-type-and-options.chooseListType_grid')
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
            @include('sections.admin_setting_menu')
        <div style="flex:auto">
            <div class="card">
                <!-- <div class="card-header bg-white header-elements-inline">
                    <h6 class="card-title">@lang('modules.hourlySettings.boxTitle')</h6>
                </div> -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            

                            <legend class="font-size-lg font-weight-bold"><mark>List Types:</mark></legend>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="listType-table">
                                    <thead>
                                        <tr>
                                            <th>@lang('modules.listTypeOptions.listType')</th>
                                            <!-- <th></th> -->
                                        </tr>
                                    </thead>

                                    <tbody id="listtype_grid">
                                        @yield('listtype_grid')
                                    </tbody>

                                </table>

                            </div>

                            <!-- <button type="button" class="btn alpha-primary text-primary-800 btn-icon add_new_list_type_row"><span class="cursor-pointer"><img src="{{ asset('newassets/img/icon-add.png') }}"></span></button> -->


                            <legend class="font-size-lg font-weight-bold m-t-30"><mark>List Options:</mark></legend>

                            
                            <div class="form-body">
                                <div class="form-group row">
                                    <label class="col-form-label col-lg-3">@lang('modules.listTypeOptions.chooseListType')</label>                                    
                                    <div class="col-lg-4">
                                        <div class="input-group" id="chooseListType_grid">
                                            @yield('chooseListType_grid')                                     
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-12 text-left m-t-10 m-b-20">
                                        <!-- <hr> -->
                                        <button type="submit" id="load-list-options" class="btn btn-success m-r-10"><i class="fa fa-check"></i> @lang('modules.listTypeOptions.loadListOptions')</button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="listOptions-table">
                                    <thead>
                                        <tr>
                                            <th>@lang('modules.listTypeOptions.listType')</th>
                                            <th>@lang('modules.listTypeOptions.listOptions')</th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody id="listOptions_grid">
                                       
                                    </tbody>

                                </table>

                            </div>

                            <button type="button" class="btn alpha-primary text-primary-800 btn-icon add_new_list_option_row"><span class="cursor-pointer"><img src="{{ asset('newassets/img/icon-add.png') }}"></span></button>

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

    $("#load-list-options").click(function() {
       var list_type_val = $('#choose_list_type').val();

       if(list_type_val==null || list_type_val==''){
        $('#listOptions-table tbody').html('');
       }

       $.ajax({
            url: "/admin/settings/ajaxLoadListOptions",
            method: 'post',
            data: { "_token": "{{ csrf_token() }}", "list_type_val":list_type_val

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
                    $('#listOptions-table tbody').html(result.listOptions_html);

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

    $(".add_new_list_option_row").click(function() {
       var rowCount = $('#listOptions-table tr').length;

       $.ajax({
            url: "/admin/settings/ajaxGetListTypes",
            method: 'post',
            data: { "_token": "{{ csrf_token() }}"

            },
            dataType: "json",
            success: function(result) {
              
                $('#listOptions-table').append('<tr id="tr_listOption'+rowCount+'"><td><select name="list_id" class="form-control" id="list_id_'+rowCount+'">'+result+'</select></td><td><input type="text" name="list_option" id="list_option_'+rowCount+'" class="form-control"></td><td><button type="submit" id="create_listOption_btn_'+rowCount+'"  data-createListOptionid="'+rowCount+'" class="btn btn-success m-r-10 btn-sm create_listOption_btn" style="padding:6px 6px;">Save</button><button id="delete_listOption_row_'+rowCount+'" type="button" onclick="deleteListOptionAddedRow('+rowCount+')" class="btn btn-light btn-sm" style="padding:6px 6px;">Cancel</button></td></tr>');
            }
        });

       

    });


    $('body').on('click', '.listOption-edit-btn', function(e) {

        e.preventDefault();
        var listOptionId = $(this).data('listoptionid');
        $("#update_listOption_form_grid_" + listOptionId).removeClass('hidden');
        $("#display_listOption_form_grid_" + listOptionId).addClass('hidden');
    });

    $('body').on('click', '.listOption-cancelUpdate-btn', function(e) {

        e.preventDefault();
        var listOptionId = $(this).data('listoptionid');
        $("#update_listOption_form_grid_" + listOptionId).addClass('hidden');
        $("#display_listOption_form_grid_" + listOptionId).removeClass('hidden');
    });


    $('body').on('click', '.create_listOption_btn', function(e) {

        var createrowid = $(this).data('createlistoptionid');

        var list_type_id   = $('#list_id_'+createrowid).val();
        var list_option    = $('#list_option_'+createrowid).val();
        //alert(list_type_id);

        $.ajax({
            url: "/admin/settings/ajaxCreateListOption",
            method: 'post',
            data: { "_token": "{{ csrf_token() }}", "list_type_id":list_type_id, "list_option":list_option

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
                    $('#listOptions-table tbody').html(result.listOptions_html);
                    $("#choose_list_type").val(result.select_list_type).change();
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


     $('body').on('click', '.update_listOption_btn', function(e) {
        e.preventDefault();
        var listoptionid = $(this).data('listoptionid');

        var list_type_id   = $('#list_id_'+listoptionid).val();
        var list_option    = $('#list_option_'+listoptionid).val();

        var selected_list_id    = $('#choose_list_type').val();

        //alert(listoptionid);
        $.ajax({
            url: "/admin/settings/ajaxUpdateListOption",
            method: 'post',
            data: { "_token": "{{ csrf_token() }}", "list_type_id":list_type_id, "list_option":list_option, "update_id": listoptionid, "selected_list_id":selected_list_id

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
                    $('#listOptions-table tbody').html(result.listOptions_html);
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

    $(".add_new_list_type_row").click(function() {
       var rowCount = $('#listType-table tr').length;
       $('#listType-table').append('<tr id="tr_listType'+rowCount+'"><td><input type="text" name="list_name" id="list_name_'+rowCount+'" class="form-control"></td><td><button type="submit" id="create_listType_btn_'+rowCount+'"  data-createrowid="'+rowCount+'" class="btn btn-success m-r-10 btn-sm create_listType_btn" style="padding:6px 6px;">Save</button><button id="delete_row_'+rowCount+'" type="button" onclick="deleteAddedRow('+rowCount+')" class="btn btn-light btn-sm" style="padding:6px 6px;">Cancel</button></td></tr>');
    });

    $('body').on('click', '.create_listType_btn', function(e) {

        var createrowid = $(this).data('createrowid');

        var list_name   = $('#list_name_'+createrowid).val();
        //alert(list_name);

        $.ajax({
            url: "/admin/settings/ajaxCreateListType",
            method: 'post',
            data: { "_token": "{{ csrf_token() }}", "list_name":list_name

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
                    $('#listType-table tbody').html(result.listType_html);
                    $('#chooseListType_grid').html(result.chooseListType_html);
                    $('#listOptions-table tbody').html('');
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


    $('body').on('click', '.listOption-remove-btn', function() {
        var delete_id = $(this).data('listoptionid');
        var selected_list_id    = $('#choose_list_type').val();
        swal({
            title: "Are you sure?",
            text: "You want to delete this List Option?",
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
                    url: "/admin/settings/ajaxDestroyListOption",
                    method: 'post',
                    data: { "_token": "{{ csrf_token() }}", "id":delete_id, "selected_list_id":selected_list_id },
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
                            
                            $('#listOptions-table tbody').html(result.listOptions_html);
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

    $('body').on('click', '.update_listType_btn', function(e) {
        e.preventDefault();
        var list_type_id = $(this).data('localmovesid');

        var list_name   = $('#list_name_'+list_type_id).val();

        //alert(min_cbm);
        $.ajax({
            url: "/admin/settings/ajaxUpdateListType",
            method: 'post',
            data: { "_token": "{{ csrf_token() }}", "list_type_id":list_type_id, "list_name":list_name

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
                    $('#listType-table tbody').html(result.listType_html);
                    $('#chooseListType_grid').html(result.chooseListType_html);
                    $('#listOptions-table tbody').html('');
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
  

    $('body').on('click', '.listType-remove-btn', function() {
        var delete_id = $(this).data('localmovesid');
        swal({
            title: "Are you sure?",
            text: "You want to delete this List Type?",
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
                    url: "/admin/settings/ajaxDestroyListType",
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
                            
                            $('#listType-table tbody').html(result.listType_html);
                            $('#chooseListType_grid').html(result.chooseListType_html);
                            $('#listOptions-table tbody').html('');
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

    $('body').on('click', '.listType-edit-btn', function(e) {

        e.preventDefault();
        var localmovesid = $(this).data('localmovesid');
        $("#update_listType_form_grid_" + localmovesid).removeClass('hidden');
        $("#display_listType_form_grid_" + localmovesid).addClass('hidden');
    });

    $('body').on('click', '.listType-cancelUpdate-btn', function(e) {

        e.preventDefault();
        var localmovesid = $(this).data('localmovesid');
        $("#update_listType_form_grid_" + localmovesid).addClass('hidden');
        $("#display_listType_form_grid_" + localmovesid).removeClass('hidden');
    });





    function deleteAddedRow(row_id)
    {
        $('#tr_listType'+row_id).remove();
    }

    function deleteListOptionAddedRow(row_id)
    {
        $('#tr_listOption'+row_id).remove();
    }

</script>

@endpush