$(document).ready(function() {
    //START:: Opperation Section
    $("body").on('click','.add_oppTrip_line', function(e){
        var leg_id = $(this).data('leg');
        $("#oppTrip_line_div_new_"+leg_id).css("display", "table-row");
        $(this).toggle();
        return false;
    });
    //Edit Leg line item
    $("body").off('click','.cancel_update_oppTrip_btn').on('click','.cancel_update_oppTrip_btn', function(e){
        var id = $(this).data('id');
        $("#oppTrip_line_div_edit_"+id).css("display", "none");
        $("#oppTrip_line_div_view_"+id).css("display", "table-row");
        
    });
    $("body").off('click','.edit_oppTrip_btn').on('click','.edit_oppTrip_btn', function(e){
        var id = $(this).data('id');
        $("#oppTrip_line_div_view_"+id).css("display", "none");
        $("#oppTrip_line_div_edit_"+id).css("display", "table-row");  
        return false;     
        
    });
    $('body').on('click', '.update_oppTrip_btn', function (e) {
        e.preventDefault();

        var row_id = $(this).data('id');
        var job_id = $('input[name="job_id"]').val();
        var _token = $('input[name="_token"]').val();
        var pickup = $('#oppTrip_pickup_address_edit_'+row_id).val();
        var dropoff = $('#oppTrip_drop_off_address_edit_'+row_id).val();
        var notes = $("#oppTrip_notes_edit_"+row_id).val();

    $.ajax({
        url: "/admin/moving/ajaxUpdateJobOperationTrip",
        method: 'post',
        data: {'_token':_token,'job_id':job_id,'id':row_id,'pickup':pickup,'dropoff':dropoff,'notes':notes},
        dataType: "json",
        beforeSend: function () {
            $.blockUI();
        },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    $('#operations_trip_table_grid').html(result.trips);

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
    $('body').on('click', '.save_oppTrip_btn', function (e) {
    e.preventDefault();

    var leg_id = $(this).data('leg');
    var job_id = $('input[name="job_id"]').val();
    var _token = $('input[name="_token"]').val();
    var pickup = $('#oppTrip_pickup_address_new_'+leg_id).val();
    var dropoff = $('#oppTrip_drop_off_address_new_'+leg_id).val();
    var notes = $("#oppTrip_notes_new_"+leg_id).val();

    $.ajax({
        url: "/admin/moving/ajaxSaveJobOperationTrip",
        method: 'post',
        data: {'_token':_token,'job_id':job_id,'leg_id':leg_id,'pickup':pickup,'dropoff':dropoff,'notes':notes},
        dataType: "json",
        beforeSend: function () {
            $.blockUI();
        },
        complete: function () {
            $.unblockUI();
        },
        success: function (result) {
            if (result.error == 0) {
                $('#operations_trip_table_grid').html(result.trips);
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
    $("body").off('click', '.cancel_oppTrip_btn').on('click', '.cancel_oppTrip_btn', function(e) {
        var leg_id = $(this).data('leg');
        $("#oppTrip_line_div_new_" + leg_id).hide();
        $("#add_oppTrip_line_"+leg_id).toggle();
        return false;

    });


            //Delete oppTrip
    $('body').on('click', '.delete_oppTrip_btn', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var job_id = $('input[name="job_id"]').val();
        var _token = $('input[name="_token"]').val();

        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted trip item!",
            icon: "warning",
            buttons: true
        }).then((isConfirm) => {
            if (isConfirm) {
                $.ajax({
                    url: "/admin/moving/ajaxDestroyJobOperationTrip",
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
                    $('#operations_trip_table_grid').html(result.trips);
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

}); // ready function end