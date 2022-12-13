$(document).ready(function() {
    //START:: Opperation Section
    $("body").on('click','#add_oppLeg_line', function(e){
        $("#oppLeg_line_div_new").clone().insertAfter("tr.oppLeg_line_div:last").css("display", "table-row");
        var row_id = $("#oppLeg_line_div_new").data('row') + 1;
        $("#oppLeg_line_div_new").data('row',row_id);
        $("tr.oppLeg_line_div:last").attr('id','oppLeg_line_div_new_'+row_id);
        $("tr.oppLeg_line_div:last").attr('data-row',row_id);

        //edit line
        $("button.edit_oppLeg_btn:last").attr('data-row',row_id);
        //--
        $("input.oppLeg_pickup_address_new:last").attr('id', 'oppLeg_pickup_address_new_' + row_id);
        $("input.oppLeg_drop_off_address_new:last").attr('id', 'oppLeg_drop_off_address_new_' + row_id);
        $("input.oppLeg_leg_date_new:last").attr('id', 'oppLeg_leg_date_new_' + row_id);
        $("input.oppLeg_leg_start_time_new:last").attr('id', 'oppLeg_leg_start_time_new_' + row_id);
        $("input.oppLeg_leg_finish_time_new:last").attr('id', 'oppLeg_leg_finish_time_new_' + row_id);
        $("select.oppLeg_driver_id_new:last").attr('id', 'oppLeg_driver_id_new_' + row_id);        
        $("input.oppLeg_has_multiple_trips_new:last").attr('id', 'oppLeg_has_multiple_trips_new_' + row_id);
        $("textarea.oppLeg_notes_new:last").attr('id', 'oppLeg_notes_new_' + row_id);
        $("select.oppLeg_offsider_ids_new:last").attr('id', 'oppLeg_offsider_ids_new_' + row_id);
        $("select.oppLeg_vehicle_id_new:last").attr('id', 'oppLeg_vehicle_id_new_' + row_id);

        $("button.cancel_oppLeg_btn:last").attr('data-row',row_id);
        $("button.save_oppLeg_btn:last").attr('data-row',row_id);

        //$("form.oppLeg_form_new:last").attr('id','oppLeg_form_new_'+row_id);

        //hide add new line button
        $("#add_oppLeg_line").toggle();
        $('.daterange-single').daterangepicker({ 
            singleDatePicker: true,
            locale: {
                format: 'DD/MM/YYYY'
            }
        });
        var autocompletesWraps = ['facility_address', 'source_address'];
        createGeoListeners(autocompletesWraps);
        return false;
    });
    //Edit Leg line item
    $("body").off('click','.cancel_update_oppLeg_btn').on('click','.cancel_update_oppLeg_btn', function(e){
        var id = $(this).data('id');
        $("#oppLeg_line_div_edit_"+id).css("display", "none");
        $("#oppLeg_line_div_view_"+id).css("display", "table-row");
        
    });
    $("body").off('click','.edit_oppLeg_btn').on('click','.edit_oppLeg_btn', function(e){
        var id = $(this).data('id');
        $("#oppLeg_line_div_edit_"+id).css("display", "table-row");
        $("#oppLeg_line_div_view_"+id).css("display", "none");
        return false;
        
    });
    $('body').on('click', '.update_oppLeg_btn', function (e) {
        e.preventDefault();
        var has_multiple_trips=0;
        var row_id = $(this).data('id');
        var job_id = $('input[name="job_id"]').val();
        var _token = $('input[name="_token"]').val();
        var pickup = $('#oppLeg_pickup_address_edit_'+row_id).val();
        var dropoff = $('#oppLeg_drop_off_address_edit_'+row_id).val();
        var leg_date = $("#oppLeg_leg_date_edit_"+row_id).val();
        var est_start_time = $("#oppLeg_leg_start_time_edit_"+row_id).val();
        var est_finish_time = $("#oppLeg_leg_finish_time_edit_"+row_id).val();
        var driver_id = $('#oppLeg_driver_id_edit_'+row_id).find(":selected").val();
        var vehicle_id = $('#oppLeg_vehicle_id_edit_'+row_id).find(":selected").val();
        var offsider_ids = $('#oppLeg_offsider_ids_edit_'+row_id).val();
        var leg_status = $('#oppLeg_leg_status_edit_'+row_id).val();
        var notes = $("#oppLeg_notes_edit_"+row_id).val();
        if ($("#oppLeg_has_multiple_trips_edit_"+row_id).is(":checked"))
        {
            var has_multiple_trips=1;
        }

        $.ajax({
            url: "/admin/moving/ajaxUpdateJobOperation",
            method: 'post',
            data: {'_token':_token,'id':row_id,'job_id':job_id,'pickup':pickup,'dropoff':dropoff,'leg_status':leg_status,
                    'leg_date':leg_date,'est_start_time':est_start_time,'est_finish_time':est_finish_time,
                    'driver_id':driver_id,'vehicle_id':vehicle_id,'offsider_ids':offsider_ids,'has_multiple_trips':has_multiple_trips,'notes':notes},
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    $('#operations_leg_table_grid').html(result.legs);
                    $('#operations_trip_table_grid').html(result.trips);
                    $('#actual_hours_table_grid').html(result.actual_hours);
                    $('.daterange-single').daterangepicker({ 
                        singleDatePicker: true,
                        locale: {
                            format: 'DD/MM/YYYY'
                        }
                    });
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
    //--
    //Add new Leg Line item
    $('body').on('click', '.save_oppLeg_btn', function (e) {
    e.preventDefault();
    var has_multiple_trips=0;
    var row_id = $(this).data('row');
    var job_id = $('input[name="job_id"]').val();
    var _token = $('input[name="_token"]').val();
    var pickup = $('#oppLeg_pickup_address_new_'+row_id).val();
    var dropoff = $('#oppLeg_drop_off_address_new_'+row_id).val();
    var leg_date = $("#oppLeg_leg_date_new_"+row_id).val();
    var est_start_time = $("#oppLeg_leg_start_time_new_"+row_id).val();
    var est_finish_time = $("#oppLeg_leg_finish_time_new_"+row_id).val();
    var driver_id = $('#oppLeg_driver_id_new_'+row_id).find(":selected").val();
    var vehicle_id = $('#oppLeg_vehicle_id_new_'+row_id).find(":selected").val();
    var offsider_ids = $('#oppLeg_offsider_ids_new_'+row_id).val();
    var leg_status = $('#oppLeg_leg_status_new_'+row_id).val();
    // var offsider_ids=[];
    // $('#oppLeg_offsider_ids_new_'+row_id+' :selected').each(function(){
    //     offsider_ids[$(this).val()]=$(this).val();
    // });
    var notes = $("#oppLeg_notes_new_"+row_id).val();

    if ($("#oppLeg_has_multiple_trips_new_"+row_id).is(":checked"))
    {
        var has_multiple_trips=1;
    }
    $.ajax({
        url: "/admin/moving/ajaxSaveJobOperation",
        method: 'post',
        data: {'_token':_token,'job_id':job_id,'pickup':pickup,'dropoff':dropoff,'leg_status':leg_status,
        'leg_date':leg_date,'est_start_time':est_start_time,'est_finish_time':est_finish_time,
        'driver_id':driver_id,'vehicle_id':vehicle_id,'offsider_ids':offsider_ids,'has_multiple_trips':has_multiple_trips,'notes':notes},
        dataType: "json",
        beforeSend: function () {
            $.blockUI();
        },
        complete: function () {
            $.unblockUI();
        },
        success: function (result) {
            if (result.error == 0) {
                $('#operations_leg_table_grid').html(result.legs);
                $('#operations_trip_table_grid').html(result.trips);
                $('#actual_hours_table_grid').html(result.actual_hours);
                $('.daterange-single').daterangepicker({ 
                    singleDatePicker: true,
                    locale: {
                        format: 'DD/MM/YYYY'
                    }
                });
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
    $("body").off('click', '.cancel_oppLeg_btn').on('click', '.cancel_oppLeg_btn', function(e) {
        var row_id = $(this).data('row');
        $("#oppLeg_line_div_new_" + row_id).remove();
        $("#add_oppLeg_line").toggle();

    });


            //Delete oppLeg
    $('body').on('click', '.delete_oppLeg_btn', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var job_id = $('input[name="job_id"]').val();
        var _token = $('input[name="_token"]').val();
        swal({
            title: "Are you sure?",
            text: "Related trips will be deleted with Leg!",
            icon: "warning",
            buttons: true
        }).then((isConfirm) => {
            if (isConfirm) {
                $.ajax({
                    url: "/admin/moving/ajaxDestroyJobOperation",
                    method: 'post',
                    data: { '_token': _token, 'job_id': job_id, 'id': id },
                    dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    $('#operations_leg_table_grid').html(result.legs);
                    $('#operations_trip_table_grid').html(result.trips);
                    $('#actual_hours_table_grid').html(result.actual_hours);
                    //Notification....
                    $.toast({
                        heading: 'Deleted',
                        text: result.message,
                        icon: 'success',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#00c292',
                        textColor: 'white'
                    });
                    //..
                }
            }
        });
            }
        });
    });
    //end:: delete Leg
    //END:: Opperation Leg Section

    //START:: Actual hours
    $("body").off('click','.edit_hours_btn').on('click','.edit_hours_btn', function(e){
        var id = $(this).data('id');
        $("#hours_line_div_edit_"+id).css("display", "table-row");
        $("#hours_line_div_view_"+id).css("display", "none");
        return false;
        
    });
    $("body").off('click', '.cancel_update_hours_btn').on('click', '.cancel_update_hours_btn', function(e) {
        var id = $(this).data('id');
        $("#hours_line_div_edit_"+id).css("display", "none");
        $("#hours_line_div_view_"+id).css("display", "table-row");

    });
    //Update actual hours
    $('body').on('click', '.update_hours_btn', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var job_id = $('input[name="job_id"]').val();
        var _token = $('input[name="_token"]').val();
        var actual_start_time = $('#hours_actual_start_time_edit_'+id).val();
        var actual_finish_time = $('#hours_actual_finish_time_edit_'+id).val();                
        $.ajax({
            url: "/admin/moving/ajaxUpdateActualhours",
            method: 'post',
            data: {'_token':_token,'id':id,'job_id':job_id,'actual_start_time':actual_start_time,'actual_finish_time':actual_finish_time},
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    $('#actual_hours_table_grid').html(result.html);
                    $('.daterange-single').daterangepicker({ 
                        singleDatePicker: true,
                        locale: {
                            format: 'DD/MM/YYYY'
                        }
                    });
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
    $('body').on('click', '#update_regenerate_invoice', function (e) {
        e.preventDefault();
        var job_id = $(this).data('jobid');
        var invoice_id = $(this).data('invoiceid');
        var _token = $('input[name="_token"]').val();
                    
        $.ajax({
            url: "/admin/moving/ajaxUpdateRegenrateInvoice",
            method: 'post',
            data: {'_token':_token,'job_id':job_id,'invoice_id':invoice_id},
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {                    
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
                    //..
                    //Regenrate Invoice PDF
                    $.ajax({
                        url: "/admin/moving/list-jobs/generateInvoice/" + job_id+"/Moving",
                        method: 'GET',
                        dataType: "json",
                        success: function (result) {
                            //----
                        }
                    });
                    
                    // location.reload();
                    window.location = "/admin/moving/view-job/"+job_id+"#invoice_tab";
                } else {
                    //Notification....
                    $.toast({
                        heading: 'Warning',
                        text: result.message,
                        icon: 'error',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#fcbd2e',
                        textColor: 'white'
                    });
                    //..
                }
            }
        });
    });
    //END:: Actual hours

    //START:: Notify Driver
    $('body').on('click', '.notify_driver_btn', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var job_id = $('input[name="job_id"]').val();
        var _token = $('input[name="_token"]').val();
        swal({
            title: "Are you sure?",
            text: "Driver will be notified!",
            icon: "warning",
            buttons: true
        }).then((isConfirm) => {
            if (isConfirm) {
                $.ajax({
                    url: "/admin/moving/ajaxNotifyDriver",
                    method: 'post',
                    data: { '_token': _token, 'job_id': job_id, 'id': id },
                    dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    $('#operations_leg_table_grid').html(result.legs);
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
                    //..

                    //START::Push Notification
                    $.ajax({
                            url: "/admin/moving/sendPushNotification",
                            method: 'post',
                            data: { '_token': _token, 'ppl_id': result.ppl_id, 'job_number': result.job_number },
                            dataType: "json",
                    
                        success: function (result) {
                            //--
                        }
                    });
                    //END::Push Notification
                }
            }
        });
        }
        });
    });
    //end:: Notify Driver

    //START:: Reassign Driver
    $('body').on('click', '.save_reassign_driver_btn', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        
        var job_id = $('input[name="job_id"]').val();
        var driver_id = $('#reassign_driver_id_'+id).val();
        var _token = $('input[name="_token"]').val();
        swal({
            title: "Are you sure?",
            text: "Driver will be reassigned!",
            icon: "warning",
            buttons: true
        }).then((isConfirm) => {
            if (isConfirm) {
                $.ajax({
                    url: "/admin/moving/ajaxReassignDriver",
                    method: 'post',
                    data: { '_token': _token, 'job_id': job_id, 'id': id, 'driver_id':driver_id },
                    dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    $('#operations_leg_table_grid').html(result.legs);                    
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
                    //..
                }
            }
        });
            }
        });
    });
    //end:: Reassign Driver

}); // ready function end