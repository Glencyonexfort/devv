$(document).ready(function () {
    //START:: Create New Task
    $('body').on('click', '#create_task_btn', function (e) {
        e.preventDefault();
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxStoreTask",
            method: 'post',
            data: $("#add_new_task_form").serialize(),
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {

                if (result.error == 0) {
                    var lead_id = result.id;
                    $('#add_new_task_form').trigger("reset");
                    $("#add_new_task_form_grid").toggle(200);
                    $('#tasks_grid').html(result.task_html);
                    $("#tasksCount").html(result.task_count);
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
    //END:: Create New Task
    //START:: Edit Task
    $('body').on('click', '.task-edit-btn', function (e) {
        e.preventDefault();
        var taskid = $(this).data('taskid');
        $("#update_task_form_grid_" + taskid).toggle(200);
    });
    $('body').on('click', '.update_task_btn', function (e) {
        e.preventDefault();
        var taskid = $(this).data('taskid');
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxUpdateTask",
            method: 'post',
            data: $("#update_task_form_" + taskid).serialize(),
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {

                if (result.error == 0) {
                    var lead_id = result.id;
                    $("#update_task_form_grid_" + taskid).toggle(200);
                    $('#tasks_grid').html(result.task_html);
                    $("#tasksCount").html(result.task_count);
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
    //END:: Edit Task
    //START:: Activity - Add Notes
    $('body').on('click', '.add_notes_btn', function (e) {
        e.preventDefault();
        $('#add_notes_box').toggle(200);
    });

    $('body').on('click', '#store_notes_btn', function (e) {
        e.preventDefault();
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxStoreNote",
            method: 'post',
            data: $("#activity_notes_form").serialize() + '&job_id=' + $("#op_job_id").val(),
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    $(".summernote").summernote('code', '');
                    var lead_id = result.id;
                    $('#activity_notes_form').trigger("reset");
                    $("#add_notes_box").toggle(200);
                    $('#activity_notes_grid').html(result.html);
                    // $('html, body').animate({
                    //     scrollTop: $("#activity_notes_grid").offset().top - 200
                    // }, 1000);
                    //Notification....
                    $('.summernote').summernote({
                        height: 200,
                        toolbar: [
                            ['font', ['bold', 'underline']],
                            ['fontname', ['fontname']],
                            ['fontsize', ['fontsize']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol']],
                            ['insert', ['link', 'picture']],
                            ['view', ['codeview', 'fullscreen']],
                        ],
                    });
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
    $('body').on('click', '.edit_act_note_btn', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        $('#act_note_view_div_' + id).toggle();
        $('#act_note_form_div_' + id).toggle();
    });

    $('body').on('click', '.update_act_note_btn', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxUpdateNote",
            method: 'post',
            data: $("#act_note_form_" + id).serialize(),
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    $(".summernote").summernote('code', '');
                    var lead_id = result.id;
                    $('#act_note_form_' + id).trigger("reset");
                    $("#act_note_form_div_" + id).toggle();
                    $('#act_note_view_div_' + id).toggle();
                    $('#activity_notes_grid').html(result.html);
                    $('.summernote').summernote({
                        code:'',
                        height: 200,
                        toolbar: [
                            ['font', ['bold', 'underline']],
                            ['fontname', ['fontname']],
                            ['fontsize', ['fontsize']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol']],
                            ['insert', ['link', 'picture']],
                            ['view', ['codeview', 'fullscreen']],
                        ],
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
    //END:: Activity - Add Notes

    //START:: Activity - Add SMS
    $('body').on('click', '.add_sms_btn', function (e) {
        e.preventDefault();
        $('#add_sms_box').toggle(200);
    });
    $('body').on('click', '#send_sms_btn', function (e) {
        if ($('#act_sms_send_to').val() == 0) {
            swal({
                title: "Error",
                text: 'Please select sent to contact!',
                type: "error",
                button: "OK",
            });
        } else if ($('#sms_message').val().length == 0) {
            swal({
                title: "Error",
                text: 'Please enter message!',
                type: "error",
                button: "OK",
            });
        }
        e.preventDefault();
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxSendSms",
            method: 'post',
            data: $("#activity_sms_form").serialize() + '&job_id=' + $("#op_job_id").val(),
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    var lead_id = result.id;
                    $('#activity_sms_form').trigger("reset");
                    $("#add_sms_box").toggle(200);
                    $('#activity_notes_grid').html(result.html);
                    $('html, body').animate({
                        scrollTop: $("#activity_notes_grid").offset().top - 200
                    }, 1000);
                    //Notification....
                    $('.summernote').summernote({
                        height: 200,
                        toolbar: [
                            ['font', ['bold', 'underline']],
                            ['fontname', ['fontname']],
                            ['fontsize', ['fontsize']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol']],
                            ['insert', ['link', 'picture']],
                            ['view', ['codeview', 'fullscreen']],
                        ],
                    });
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
                    if (result.error == 1) {
                        swal({
                            title: "Error",
                            text: result.message,
                            type: "error",
                            button: "OK",
                        });
                    } else {
                        swal({
                            title: "Error",
                            text: 'Something went wrong!',
                            type: "error",
                            button: "OK",
                        });
                    }
                }
            }
        });
    });
    //END:: Activity - Add SMS

    //START:: Activity - Add Email

    $('body').on('change', '#act_email_from', function (e) {
        e.preventDefault();
        var from_name = $(this).find(':selected').data('name');
        $("#act_email_from_name").val(from_name);
    });

    $('body').on('click', '.add_email_btn', function (e) {
        e.preventDefault();
        $('#add_email_box').toggle(200);
    });
    $('body').on('click', '#send_email_btn', function (e) {
        if ($('#act_email_to').val() == 0) {
            swal({
                title: "Error",
                text: 'Please enter sent to email!',
                type: "error",
                button: "OK",
            });
        }
        e.preventDefault();
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxSendEmail",
            method: 'post',
            data: $("#activity_email_form").serialize(),
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    var lead_id = result.id;
                    $('#activity_email_form')[0].reset();
                    $('#email_attachment_div').empty();
                    $('#email_body').val('');
                    $("#add_email_box").toggle(200);
                    $('#activity_notes_grid').html(result.html);
                    $('html, body').animate({
                        scrollTop: $("#activity_notes_grid").offset().top - 200
                    }, 1000);
                    //Notification....
                    $('.summernote').summernote({
                        height: 200,
                        toolbar: [
                            ['font', ['bold', 'underline']],
                            ['fontname', ['fontname']],
                            ['fontsize', ['fontsize']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol']],
                            ['insert', ['link', 'picture']],
                            ['view', ['codeview', 'fullscreen']],
                        ],
                    });
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
                } else if (result.error == 2) {
                    //Notification....
                    $.toast({
                        heading: 'Cancel',
                        text: result.message,
                        icon: 'danger',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#00c294',
                        textColor: 'white'
                    });
                    //..
                } else {
                    if (result.error == 1) {
                        swal({
                            title: "Error",
                            text: result.message,
                            type: "error",
                            button: "OK",
                        });
                    } else {
                        swal({
                            title: "Error",
                            text: 'Something went wrong!',
                            type: "error",
                            button: "OK",
                        });
                    }
                }
            }
        });
    });
    //START:: Find similar Contacts
    $(".search_email").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: "/admin/crm/crm-leads/ajaxFindContactEmail",
                method: 'post',
                data: { 'key': request.term, 'lead_id': $('#activity_email_form :input[name="lead_id"]').val(), '_token': $('#activity_email_form :input[name="_token"]').val() },
                dataType: "json",
                success: function (data) {
                    response(data);
                },
            });
        },
        select: function (event, ui) {
            var input = $(this).data('input');
            // Set selection
            $('#act_email_' + input).val(ui.item.label); // display the selected text
            $('#act_email_' + input).val(ui.item.value); // save selected id to input
            return false;
        }
    });
    //END:: Activity - Add Email

    //START:: Job Detail
    $('body').on('click', '.show_update_job_detail_btn', function (e) {
        e.preventDefault();
        $('#update_jo_detail_view').toggle();
        $('#update_jo_detail_form').toggle();
    });

    $('body').on('click', '#update_job_detail_btn', function (e) {
        e.preventDefault();
        var job_id = $('input[name="job_id"]').val();
        var job_type = $('#job_detail_form :input[name=job_type]').val();
        if (job_type == "Cleaning") {
            var url = "/admin/cleaning/ajaxUpdateJobDetail";
        } else {
            var url = "/admin/moving/ajaxUpdateJobDetail";
        }
        $.ajax({
            url: url,
            method: 'post',
            data: $("#job_detail_form").serialize() + '&job_id=' + job_id,
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    $('#job_detail_grid').html(result.html);
                    $('#update_jo_detail_form').toggle();
                    $('#update_jo_detail_view').toggle();
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
                    $('.summernote').summernote({
                        height: 200,
                        toolbar: [
                            ['font', ['bold', 'underline']],
                            ['fontname', ['fontname']],
                            ['fontsize', ['fontsize']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol']],
                            ['insert', ['link', 'picture']],
                            ['view', ['codeview', 'fullscreen']],
                        ],
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
    //END:: Job Detail

    $('body').on('click', '.show_update_pickup_btn', function (e) {
        e.preventDefault();
        $('#update_pickup_view').toggle();
        $('#update_pickup_form').toggle();
    });

    $('body').on('click', '#update_job_pickup_btn', function (e) {
        e.preventDefault();
        var job_id = $('input[name="job_id"]').val();
        $.ajax({
            url: "/admin/moving/ajaxUpdateJobPickup",
            method: 'post',
            data: $("#job_pickup_form").serialize() + '&job_id=' + job_id,
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    $('#job_pickup_grid').html(result.html);
                    $('#update_pickup_form').toggle();
                    $('#update_pickup_view').toggle();
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
                    $('.summernote').summernote({
                        height: 200,
                        toolbar: [
                            ['font', ['bold', 'underline']],
                            ['fontname', ['fontname']],
                            ['fontsize', ['fontsize']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol']],
                            ['insert', ['link', 'picture']],
                            ['view', ['codeview', 'fullscreen']],
                        ],
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

    $('body').on('click', '.show_update_dropoff_btn', function (e) {
        e.preventDefault();
        $('#update_dropoff_view').toggle();
        $('#update_dropoff_form').toggle();
    });

    $('body').on('click', '#update_job_dropoff_btn', function (e) {
        e.preventDefault();
        var job_id = $('input[name="job_id"]').val();
        $.ajax({
            url: "/admin/moving/ajaxUpdateJobDropoff",
            method: 'post',
            data: $("#job_dropoff_form").serialize() + '&job_id=' + job_id,
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    $('#job_dropoff_grid').html(result.html);
                    $('#update_dropoff_form').toggle();
                    $('#update_dropoff_view').toggle();
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

    //START:: Create New Contact
    $('.add_new_contact_btn').click(function () {
        $("#add_new_contact_form_grid").toggle(200);
    });
    $("#add_new_contact_form").on('keyup', 'div.contact_detail_div:last input', function (e) {
        var count = $(this).val().length;
        if (count == 1) {
            $("div.contact_detail_div:last").clone().insertAfter("div.contact_detail_div:last");
            $('div.contact_detail_div:last input').val('');
        }
    });
    $(".update_contact_form").on('keyup', 'div.contact_detail_div_edit:last input', function (e) {
        var id = $(this).data('id');
        var count = $(this).val().length;
        if (count == 1) {
            $("div#contact_detail_div_edit_" + id + ":last").clone().insertAfter("div#contact_detail_div_edit_" + id + ":last");
            $("div#contact_detail_div_edit_" + id + ":last input").val('');
        }
    });
    $('body').on('click', '#create_contact_btn', function (e) {
        e.preventDefault();
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxStoreContact",
            method: 'post',
            data: $("#add_new_contact_form").serialize(),
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {

                if (result.error == 0) {
                    var lead_id = result.id;
                    $('#add_new_contact_form').trigger("reset");
                    $("#add_new_contact_form_grid").toggle(200);
                    $('#contacts_grid').html(result.contact_html);
                    $("#contactsCount").html(result.contact_count);
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
    $('body').on('click', '.contact-update-btn', function (e) {
        e.preventDefault();
        var contactid = $(this).data('contactid');
        $('#update_contact_form_grid_' + contactid).toggle(200);
    });
    $('body').on('click', '.update_contact_btn', function (e) {
        var contactid = $(this).data('contactid');
        e.preventDefault();
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxUpdateContact",
            method: 'post',
            data: $("#update_contact_form_" + contactid).serialize(),
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    var lead_id = result.id;
                    $('#update_contact_form_' + contactid).trigger("reset");
                    $("#update_contact_form_grid_" + contactid).toggle(200);
                    $('#contacts_grid').html(result.contact_html);
                    $("#contactsCount").html(result.contact_count);
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
    $('body').on('click', '.contact-expand-btn', function (e) {
        e.preventDefault();
        var contactid = $(this).data('contactid');
        $('#expand_contact_form_grid_' + contactid).toggle(200);
    });
    //END:: Create New Contact

    //START:: Add Attachment
    $('body').on('click', '.add_attachment_btn', function (e) {
        e.preventDefault();
        //Hide Model
        $('#add_attachment_popup').modal('hide');
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
        //...
    });
    $('body').on('click', '#upload_attachment_btn', function (e) {
        e.preventDefault();
        var _token = $('input[name="_token"]').val();
        var note_id = $(this).data('key');
        var id = $('#new_attachment_form :input[name="lead_id"]').val();
        var type = $('#new_attachment_form #activity_attachment_type').val();
        var is_reply = $('#ac_email_type_value').val();
        var formData = new FormData();
        formData.append('attachment', $('#activity_attachment')[0].files[0]);
        formData.append('id', id);
        formData.append('type', type);
        formData.append('is_reply', is_reply);
        formData.append('note_id', note_id);
        formData.append('_token', _token);
        $.ajax({
            url: "/admin/crm/crm-leads/uploadActivityAttachment",
            processData: false,
            contentType: false,
            method: 'post',
            data: formData,
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    if (is_reply == 1) {
                        $('#email_reply_attachment_div_' + note_id).html(result.html);
                    } else {
                        $('#' + type + '_attachment_div').html(result.html);
                    }
                    $('#activity_attachment').val('');
                    //Hide Model
                    $('#add_attachment_popup').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                    //...
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
    $('body').on('click', '.remove_attachment_btn', function (e) {
        e.preventDefault();
        var _token = $('input[name="_token"]').val();
        var key = $(this).data('key');
        var type = $(this).data('type');
        var is_reply = $('#ac_email_type_value').val();
        var note = $('#note_id').val();

        var note_id = $(this).data('noteid');
        var formData = new FormData();
        formData.append('key', key);
        formData.append('type', type);
        formData.append('is_reply', is_reply);
        formData.append('_token', _token);
        $.ajax({
            url: "/admin/crm/crm-leads/removeActivityAttachment",
            processData: false,
            contentType: false,
            method: 'post',
            data: formData,
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    if (note_id == 0) {
                        $('#' + type + '_attachment_div').html(result.html);
                    } else {
                        $('#email_reply_attachment_div_' + note_id).html(result.html);
                    }
                    //..
                    if (result.is_reply == 1) {
                        $('#email_reply_attachment_div_' + note).html(result.html);
                    }
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
    //END:: Add Attachment
    $('body').on('click', '#add_email_cc', function (e) {
        $("#add_email_cc_box").toggle(200);
    });
    $('body').on('click', '#add_email_bcc', function (e) {
        $("#add_email_bcc_box").toggle(200);
    });

    //START:: Email Reply 
    $('body').on('click', '.email_reply_btn', function (e) {
        e.preventDefault();
        var key = $(this).data('key');
        var action = $(this).data('action');
        $('#email_view_' + key).toggle();

        if (action == "forward") {
            $("#forward_email_form_" + key).toggle();
        } else {
            $('#reply_email_form_' + key).toggle();
            $('#ac_email_type_value').val(1);
        }
    });
    $('body').on('click', '.cancel_email_reply', function (e) {
        e.preventDefault();
        var key = $(this).data('key');
        var action = $(this).data('action');
        $('#email_view_' + key).toggle();

        if (action == "forward") {
            $("#forward_email_form_" + key).toggle();
        } else {
            $('#reply_email_form_' + key).toggle();
            $('#ac_email_type_value').val(0);
        }
    });
    $('body').on('click', '.add_email_reply_cc', function (e) {
        e.preventDefault();
        var key = $(this).data('key');
        $("#add_email_reply_cc_box_" + key).toggle(200);
    });
    $('body').on('click', '.add_email_reply_bcc', function (e) {
        e.preventDefault();
        var key = $(this).data('key');
        $("#add_email_reply_bcc_box_" + key).toggle(200);
    });

    $('body').on('click', '.add_email_forward_cc', function (e) {
        e.preventDefault();
        var key = $(this).data('key');
        $("#add_email_forward_cc_box_" + key).toggle(200);
    });
    $('body').on('click', '.add_email_forward_bcc', function (e) {
        e.preventDefault();
        var key = $(this).data('key');
        $("#add_email_forward_bcc_box_" + key).toggle(200);
    });


    $('body').on('click', '.send_email_reply_btn', function (e) {
        e.preventDefault();
        var key = $(this).data('key');
        e.preventDefault();
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxSendEmail",
            method: 'post',
            data: $("#activity_email_reply_form_" + key).serialize() + '&job_id=' + $("#op_job_id").val(),
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    var lead_id = result.id;
                    $("#reply_email_form_" + key).toggle();
                    $('#activity_notes_grid').html(result.html);
                    $('html, body').animate({
                        scrollTop: $("#activity_notes_grid").offset().top - 200
                    }, 1000);
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
                    $('.summernote').summernote({
                        height: 200,
                        toolbar: [
                            ['font', ['bold', 'underline']],
                            ['fontname', ['fontname']],
                            ['fontsize', ['fontsize']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol']],
                            ['insert', ['link', 'picture']],
                            ['view', ['codeview', 'fullscreen']],
                        ],
                    });
                } else {
                    if (result.error == 1) {
                        swal({
                            title: "Error",
                            text: result.message,
                            type: "error",
                            button: "OK",
                        });
                    } else {
                        swal({
                            title: "Error",
                            text: 'Something went wrong!',
                            type: "error",
                            button: "OK",
                        });
                    }
                }
            }
        });
    });

    $('body').on('click', '.send_email_forward_btn', function (e) {
        e.preventDefault();
        var key = $(this).data('key');
        e.preventDefault();
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxSendEmail",
            method: 'post',
            data: $("#activity_email_forward_form_" + key).serialize() + '&job_id=' + $("#op_job_id").val(),
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    var lead_id = result.id;
                    //$('#activity_email_form').trigger("reset");
                    $("#forward_email_form_" + key).toggle();
                    $('#activity_notes_grid').html(result.html);
                    $('html, body').animate({
                        scrollTop: $("#activity_notes_grid").offset().top - 200
                    }, 1000);
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
                    $('.summernote').summernote({
                        height: 200,
                        toolbar: [
                            ['font', ['bold', 'underline']],
                            ['fontname', ['fontname']],
                            ['fontsize', ['fontsize']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol']],
                            ['insert', ['link', 'picture']],
                            ['view', ['codeview', 'fullscreen']],
                        ],
                    });
                } else {
                    if (result.error == 1) {
                        swal({
                            title: "Error",
                            text: result.message,
                            type: "error",
                            button: "OK",
                        });
                    } else {
                        swal({
                            title: "Error",
                            text: 'Something went wrong!',
                            type: "error",
                            button: "OK",
                        });
                    }
                }
            }
        });
    });

    //END:: Email Reply

    $('body').on('click', '.activities', function () {
        var type = $(this).data('type');
        var lead_id = $(this).data('lead_id');
        var _token = $('input[name="_token"]').val();
        $.ajax({
            url: "/admin/crm/crm-leads/getActivitiesForCustom",
            method: 'GET',
            data: {
                '_token': _token,
                'type': type,
                'lead_id': lead_id,
                'job_id': $("#op_job_id").val()
            },
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    // $('#activity_notes_grid').remove();
                    $('#activity_notes_grid').html(result.html);
                    $('#btn-activity').html(result.btn_text);

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
    //start::Inventory Tab - Manually Update CBM or Value
    $('body').on('click', '#cbm_edit_btn', function (e) {
        e.preventDefault();
        $('#cbm_edit_view').toggle();
        $('#cbm_edit_form').css("display", "flex");
    });
    $('body').on('click', '#cancel_cbm_btn', function (e) {
        e.preventDefault();
        $('#cbm_edit_form').toggle();
        $('#cbm_edit_view').show();
    });
    $("body").off('click', '#update_cbm_btn').on('click', '#update_cbm_btn', function (e) {
        e.preventDefault();
        var _token = $('input[name="_token"]').val();
        $.ajax({
            url: "/admin/moving/ajaxUpdateCbmManually",
            method: 'post',
            data: {
                '_token': _token,
                'job_id': $('#job_id').val(),
                'total_cbm': $('#total_cbm_field').val(),
                'goods_value': $('#goods_value_field').val(),
                'insurance_based_on': $('#insurance_based_on_field').val()
            },
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    $('#inventory_top_grid').html(result.html);
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
    //end::Inventory Tab - Update CBM or Value 

    //Generate Insurance quote
    $('body').on('click', '#jobGenerateInsurance', function (e) {
        e.preventDefault();
        var crm_opportunity_id = $('#crm_opportunity_id').val();
        $.ajax({
            url: "/admin/crm/crm-leads/generate-insurance-quote/" + crm_opportunity_id,
            method: 'GET',
            dataType: "json",
            beforeSend: function () {
                $('.preloader').show();
            },
            complete: function () {
                $('.preloader').hide();
            },
            success: function (result) {
                if (result.status == 0) {
                    swal({
                        title: "Error!",
                        text: result.message,
                        type: "error",
                        button: "OK",
                    });
                } else {
                    $('#jobDownloadInsurance').removeAttr("disabled");
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
            },
        });
    });
    $('body').on('click', '#jobDownloadInsurance', function (e) {
        e.preventDefault();
        var crm_opportunity_id = $('#crm_opportunity_id').val();
        $.ajax({
            url: "/admin/crm/crm-leads/download-insurance-quote/" + crm_opportunity_id,
            method: 'GET',
            dataType: "json",
            beforeSend: function () {
                $('.preloader').show();
            },
            complete: function () {
                $('.preloader').hide();
            },
            success: function (result) {
                if (result.error == 0) {
                    window.open(result.url);
                } else {
                    //Notification....
                    $.toast({
                        heading: 'Error',
                        text: "Quote not yet genearated. Click on the 'Generate Insurance Quote' button to generate the quote.",
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
    //::end generate insurance quote
});


function addAttachmentPopup(type) {
    $('#activity_attachment_type').val(type);
}

function addEmailReplyAttachmentPopup(key) {
    $('#activity_attachment_type').val('email');
    $("#ac_email_type_value").val(1);
    $("#upload_attachment_btn").attr('data-key', key);
}