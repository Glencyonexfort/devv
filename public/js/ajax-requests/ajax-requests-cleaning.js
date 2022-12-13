$(document).ready(function() {
//START:: Cleaning Operations
$('body').on('click', '.edit_team_btn', function(e) {
    e.preventDefault();
    $("#team_line_view").css("display", "none");
    $("#team_line_edit").css("display", "table-row");
});

$('body').on('click', '.cancel_edit_team_btn', function(e) {
    e.preventDefault();
    $("#team_line_edit").css("display", "none");
    $("#team_line_view").css("display", "table-row");
});

$('body').on('click', '#update_team_btn', function(e) {
    e.preventDefault();
    var _token = $('input[name="_token"]').val();
    var job_id = $('input[name="job_id"]').val();
    var team_roaster_id = $('#cleaning_team_roaster_id').val();
    var job_date = $('#cleaning_team_date').val();
    var job_shift_id = $('#cleaning_team_shift').children("option:selected").val();
    var team_id = $('#job_cleaning_team').children("option:selected").val();
    
    $.ajax({
        url: '/admin/cleaning/ajaxUpdateTeamRoaster',
        method: 'post',
        data: {'_token':_token,'job_id':job_id,'team_roaster_id':team_roaster_id,'job_date':job_date,'job_shift_id':job_shift_id,'team_id':team_id},
        dataType: "json",
        beforeSend: function() {
            $.blockUI();
        },
        complete: function() {
            $.unblockUI();
        },
        success: function(result) {
            if (result.error == 0) {
                $('#team_table_grid').html(result.html);
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

$('body').on('click', '#add_new_team_btn', function(e) {
    e.preventDefault();
    var _token = $('input[name="_token"]').val();
    var job_id = $('input[name="job_id"]').val();
    var job_date = $('#cleaning_team_date_new').val();
    var job_shift_id = $('#cleaning_team_shift_new').children("option:selected").val();
    var team_id = $('#job_cleaning_team_new').children("option:selected").val();
    
    $.ajax({
        url: '/admin/cleaning/ajaxUpdateTeamRoaster',
        method: 'post',
        data: {'_token':_token,'job_id':job_id,'team_roaster_id':0,'job_date':job_date,'job_shift_id':job_shift_id,'team_id':team_id},
        dataType: "json",
        beforeSend: function() {
            $.blockUI();
        },
        complete: function() {
            $.unblockUI();
        },
        success: function(result) {
            if (result.error == 0) {
                $('#team_table_grid').html(result.html);
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

$('body').on('click', '.edit_additional_btn', function(e) {
    e.preventDefault();
    var id = $(this).data('id');
    console.log("#additional_line_div_view_"+id);
    $("#additional_line_div_view_"+id).css("display", "none");
    $("#additional_line_div_edit_"+id).css("display", "table-row");
});

$('body').on('click', '.cancel_edit_additional_btn', function(e) {
    e.preventDefault();
    var id = $(this).data('id');
    $("#additional_line_div_edit_"+id).css("display", "none");
    $("#additional_line_div_view_"+id).css("display", "table-row");
});

$('body').on('click', '.update_additional_btn', function(e) {
    e.preventDefault();
    var id = $(this).data('id');
    var job_id = $('input[name="job_id"]').val();
    var _token = $('input[name="_token"]').val();
    var reply = $('#additional_reply_'+id).children("option:selected").val();
    
    $.ajax({
        url: '/admin/cleaning/ajaxUpdateAdditionalInfo',
        method: 'post',
        data: {'_token':_token,'id':id,'job_id':job_id,'reply':reply},
        dataType: "json",
        beforeSend: function() {
            $.blockUI();
        },
        complete: function() {
            $.unblockUI();
        },
        success: function(result) {
            if (result.error == 0) {
                $('#additional_table_grid').html(result.html);
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

//START:: Notify Driver
$('body').on('click', '.notify_team_lead_btn', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    var job_id = $('input[name="job_id"]').val();
    var _token = $('input[name="_token"]').val();
    swal({
        title: "Are you sure?",
        text: "Team Lead will be notified!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#2cb12b",
        confirmButtonText: "Yes, proceed!",
        cancelButtonText: "No, cancel please!",
        closeOnConfirm: true,
        closeOnCancel: true
    }, function (isConfirm) {
        if (isConfirm) {
            $.ajax({
                url: "/admin/cleaning/ajaxNotifyTeamLead",
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
                $('#team_table_grid').html(result.html);
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

                //START::Push Notification
                $.ajax({
                    url: "/admin/cleaning/sendPushNotification",
                    method: 'post',
                    data: { '_token': _token, 'team_id': result.team_id, 'job_number': result.job_number, 'job_id': result.job_id },
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
$('body').on('click', '.save_reassign_team_btn', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    
    var job_id = $('input[name="job_id"]').val();
    var team_id = $('#reassign_team_id_'+id).val();
    var _token = $('input[name="_token"]').val();
    swal({
        title: "Are you sure?",
        text: "Team will be reassigned!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#2cb12b",
        confirmButtonText: "Yes, proceed!",
        cancelButtonText: "No, cancel please!",
        closeOnConfirm: true,
        closeOnCancel: true
    }, function (isConfirm) {
        if (isConfirm) {
            $.ajax({
                url: "/admin/cleaning/ajaxReassignTeam",
                method: 'post',
                data: { '_token': _token, 'job_id': job_id, 'id': id, 'team_id':team_id },
                dataType: "json",
        beforeSend: function () {
            $.blockUI();
        },
        complete: function () {
            $.unblockUI();
        },
        success: function (result) {
            if (result.error == 0) {
                $('#team_table_grid').html(result.html);
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
            }
        }
    });
        }
    });
});
//end:: Reassign Driver

//END:: Cleaning Operations
});