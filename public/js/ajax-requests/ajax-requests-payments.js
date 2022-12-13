$(document).ready(function() {
    //START:: PAYMENT Section
    $("body").on('click','#add_payment_line', function(e){
        $("#payment_line_div_new").clone().insertAfter("tr.payment_line_div:last").css("display", "table-row");
        var row_id = $("#payment_line_div_new").data('row') + 1;
        $("#payment_line_div_new").data('row',row_id);
        $("tr.payment_line_div:last").attr('id','payment_line_div_new_'+row_id);
        $("tr.payment_line_div:last").attr('data-row',row_id);

        //edit line
        $("button.edit_payment_btn:last").attr('data-row',row_id);
        //--
        $("select.payment_method_new:last").attr('data-row',row_id);

        $("button.cancel_payment_btn:last").attr('data-row',row_id);
        $("button.save_payment_btn:last").attr('data-row',row_id);

        $("select.payment_method_new:last").attr('id','payment_method_new_'+row_id);
        $("form.payment_form_new:last").attr('id','payment_form_new_'+row_id);
        $("textarea.payment_description_new:last").attr('id','payment_description_new_'+row_id);

        $("div.payment_total_new:last").attr('id','payment_total_new_'+row_id);
        $("input.payment_total_new_field:last").attr('id','payment_total_new_field_'+row_id);
        $("input.payment_paidon_date:last").attr('id','payment_paidon_date_'+row_id);
        //hide add new line button
        $("#add_payment_line").toggle();
        $('.daterange-single').daterangepicker({ 
            singleDatePicker: true,
            locale: {
                format: 'DD/MM/YYYY'
            }
        });
    });
    //Edit PAYMENT line item
    $("body").off('change','.payment_method_edit').on('change','.payment_method_edit', function(e){
        var row_id = $(this).data('row');
        $("#payment_description_edit_"+row_id).val(desc);
    });
    $("body").off('click','.cancel_update_payment_btn').on('click','.cancel_update_payment_btn', function(e){
        var id = $(this).data('id');
        $("#payment_line_div_edit_"+id).css("display", "none");
        $("#payment_line_div_view_"+id).css("display", "table-row");
        
    });
    $("body").off('click','.edit_payment_btn').on('click','.edit_payment_btn', function(e){
        var id = $(this).data('id');
        $("#payment_line_div_edit_"+id).css("display", "table-row");
        $("#payment_line_div_view_"+id).css("display", "none");
        $('.daterange-single').daterangepicker({ 
            singleDatePicker: true,
            locale: {
                format: 'DD/MM/YYYY'
            }
        });
        
    });
    $('body').on('click', '.update_payment_btn', function (e) {
        e.preventDefault();
        var row_id = $(this).data('id');
        var invoice_id = $('input[name="invoice_id"]').val();
        var _token = $('input[name="_token"]').val();
        var gateway = $('#payment_method_edit_'+row_id).find(":selected").val();
        var description = $('#payment_description_edit_'+row_id).val();
        var amount = $("#payment_total_edit_field_"+row_id).val();
        var paid_on = $("#payment_paidon_date_edit_"+row_id).val();

        $.ajax({
            url: "/admin/moving/ajaxUpdatePayment",
            method: 'post',
            data: {'id':row_id,'_token':_token,'invoice_id':invoice_id,'gateway':gateway,'description':description,'amount':amount, 'paid_on':paid_on},
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    $('#payment_table_grid').html(result.html);
                    $('#totalInvoiceAmount').html(result.amount);
                    $('#totalPaidAmount').html(result.paid);
                    $('#totalBalanceAmount').html(result.balance);
                    $('#payment_status').html(result.payment_status);
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
    //Add new PAYMENT Line item
    $('body').on('click', '.save_payment_btn', function (e) {
    e.preventDefault();
    var row_id = $(this).data('row');
    var job_id = $('input[name="job_id"]').val();
    var invoice_id = $('input[name="invoice_id"]').val();
    var _token = $('input[name="_token"]').val();
    var gateway = $('#payment_method_new_'+row_id).find(":selected").val();
    var description = $('#payment_description_new_'+row_id).val();
    var amount = $("#payment_total_new_field_"+row_id).val();
    var paid_on = $("#payment_paidon_date_"+row_id).val();

    $.ajax({
        url: "/admin/moving/ajaxSavePayment",
        method: 'post',
        data: {'_token':_token,'job_id':job_id,'invoice_id':invoice_id,'gateway':gateway,
        'description':description,'amount':amount,'paid_on':paid_on},
        dataType: "json",
        beforeSend: function () {
            $.blockUI();
        },
        complete: function () {
            $.unblockUI();
        },
        success: function (result) {
            if (result.error == 0) {
                $('#payment_table_grid').html(result.html);
                $('#totalInvoiceAmount').html(result.amount);
                $('#totalPaidAmount').html(result.paid);
                $('#totalBalanceAmount').html(result.balance);
                $('#payment_status').html(result.payment_status);
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
    $("body").off('click', '.cancel_payment_btn').on('click', '.cancel_payment_btn', function(e) {
        var row_id = $(this).data('row');
        $("#payment_line_div_new_" + row_id).remove();
        $("#add_payment_line").toggle();

    });


            //Delete Payment
    $('body').on('click', '.delete_payment_btn', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var job_id = $('input[name="job_id"]').val();
        var _token = $('input[name="_token"]').val();
        var invoice_id = $('input[name="invoice_id"]').val();
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted payment item!",
            icon: "warning",
            buttons: true
        }).then((isConfirm) => {
            if (isConfirm) {
                $.ajax({
                    url: "/admin/moving/ajaxDestroyPaymentItem",
                    method: 'post',
                    data: { '_token': _token, 'job_id': job_id,'invoice_id':invoice_id, 'id': id },
                    dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    $('#payment_table_grid').html(result.html);
                    $('#totalInvoiceAmount').html(result.amount);
                    $('#totalPaidAmount').html(result.paid);
                    $('#totalBalanceAmount').html(result.balance);
                    $('#payment_status').html(result.payment_status);
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
    //end:: delete Payment
    //END:: PAYMENT Section

}); // ready function end