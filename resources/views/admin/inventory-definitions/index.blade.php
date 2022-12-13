@extends('layouts.app')

@section('inventorydefinition_grid')
@include('admin.inventory-definitions.inventorydefinition_grid')
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
                    <h6 class="card-title">@lang('modules.inventoryDefinitions.boxTitle')</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            

                            <div class="table-responsive">
                                <table class="table table-bordered" id="inventoryDefinition-table">
                                    <thead>
                                        <tr>
                                            <th>@lang('modules.inventoryDefinitions.group')</th>
                                            <th>@lang('modules.inventoryDefinitions.itemName')</th>
                                            <th>@lang('modules.inventoryDefinitions.cbmM3')</th>
                                            <th>@lang('modules.inventoryDefinitions.specialItem')</th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody id="inventorydefinition_grid">
                                        @yield('inventorydefinition_grid')
                                    </tbody>

                                </table>

                            </div>

                            <button type="button" class="btn alpha-primary text-primary-800 btn-icon add_new_inventory_definition_row"><span class="cursor-pointer"><img src="{{ asset('newassets/img/icon-add.png') }}"></span></button>
                           
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

    $(".add_new_inventory_definition_row").click(function() {
       var rowCount = $('#inventoryDefinition-table tr').length;
       $('#inventoryDefinition-table').append('<tr id="tr_inventorydefinition'+rowCount+'"><td><select name="group_id" id="add_group_id_'+rowCount+'" class="form-control"> @foreach($inventoryGroups as $data) <option value="{{$data->group_id}}">{{$data->group_name}}</option> @endforeach </select></td><td><input type="text" name="item_name" id="add_item_name_'+rowCount+'" class="form-control"></td><td><input type="text" name="cbm" id="add_cbm_'+rowCount+'" class="form-control"></td><td><select name="special_item" id="add_special_item_'+rowCount+'" class="form-control"><option value="Yes">Yes</option><option value="No" selected="">No</option></select></td><td><button type="submit" id="create_inventoryDefinition_btn_'+rowCount+'"  data-createrowid="'+rowCount+'" class="btn btn-success m-r-10 btn-sm create_inventoryDefinition_btn" style="padding:6px 6px;">Save</button><button id="delete_row_'+rowCount+'" type="button" onclick="deleteAddedRow('+rowCount+')" class="btn btn-light btn-sm" style="padding:6px 6px;">Cancel</button></td></tr>');
    });
    
    $('body').on('click', '.inventoryDefinition-edit-btn', function(e) {

        e.preventDefault();
        var localmovesid = $(this).data('localmovesid');
        $("#update_inventoryDefinition_form_grid_" + localmovesid).removeClass('hidden');
        $("#display_inventoryDefinition_form_grid_" + localmovesid).addClass('hidden');
    });

    $('body').on('click', '.inventoryDefinition-cancelUpdate-btn', function(e) {

        e.preventDefault();
        var localmovesid = $(this).data('localmovesid');
        $("#update_inventoryDefinition_form_grid_" + localmovesid).addClass('hidden');
        $("#display_inventoryDefinition_form_grid_" + localmovesid).removeClass('hidden');
    });


    $('body').on('click', '.create_inventoryDefinition_btn', function(e) {

        var createrowid = $(this).data('createrowid');

        var group_id        = $('#add_group_id_'+createrowid).val();
        var item_name       = $('#add_item_name_'+createrowid).val();
        var cbm             = $('#add_cbm_'+createrowid).val();
        var special_item    = $('#add_special_item_'+createrowid).val();

        $.ajax({
            url: "/admin/moving-settings/ajaxCreateInventoryDefinition",
            method: 'post',
            data: { "_token": "{{ csrf_token() }}", "group_id":group_id, "item_name":item_name, "cbm":cbm, "special_item":special_item

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
                    //console.log(result.inventoryDefinitions_html);
                    $('#inventoryDefinition-table tbody').html(result.inventoryDefinitions_html);
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

    $('body').on('click', '.update_inventoryDefinition_btn', function(e) {
        e.preventDefault();
        var updateid = $(this).data('localmovesid');

        var group_id        = $('#group_id_'+updateid).val();
        var item_name       = $('#item_name_'+updateid).val();
        var cbm             = $('#cbm_'+updateid).val();
        var special_item    = $('#special_item_'+updateid).val();

        //alert(group_name);
        $.ajax({
            url: "/admin/moving-settings/ajaxUpdateInventoryDefinition",
            method: 'post',
            data: { "_token": "{{ csrf_token() }}", "updateid":updateid, "group_id":group_id, "item_name":item_name, "cbm":cbm, "special_item":special_item

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
                    //console.log(result.inventoryDefinitions_html);
                    //var lead_id = result.id;
                    $('#inventoryDefinition-table tbody').html(result.inventoryDefinitions_html);
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


    $('body').on('click', '.inventoryDefinition-remove-btn', function() {
        var delete_id = $(this).data('localmovesid');
        swal({
            title: "Are you sure?",
            text: "You want to delete this Inventory Definition?",
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
                    url: "/admin/moving-settings/ajaxDestroyInventorDefinition",
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
                            
                            $('#inventoryDefinition-table tbody').html(result.inventoryDefinitions_html);
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
        $('#tr_inventorydefinition'+row_id).remove();
    }

</script>

@endpush