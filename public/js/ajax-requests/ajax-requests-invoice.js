// const { result } = require("lodash");

$(document).ready(function () {
    //START:: Invoice Section
    $("body").on('click', '#add_invoice_charge_line', function (e) {
        $("#invoice_charge_line_div_new").clone().insertAfter("tr.invoice_charge_line_div:last").css("display", "table-row");
        var row_id = $("#invoice_charge_line_div_new").data('row') + 1;
        $("#invoice_charge_line_div_new").data('row', row_id);
        $("tr.invoice_charge_line_div:last").attr('id', 'invoice_charge_line_div_new_' + row_id);
        $("tr.invoice_charge_line_div:last").attr('data-row', row_id);

        //edit line
        $("button.edit_invoice_charge_btn:last").attr('data-row', row_id);
        //--
        $("select.invoice_charge_new:last").attr('data-row', row_id);
        $("select.invoice_charge_tax_new:last").attr('data-row', row_id);
        $("input.invoice_charge_price_new:last").attr('data-row', row_id);
        $("input.invoice_charge_qty_new:last").attr('data-row', row_id);
        $("button.cancel_invoice_charge_btn:last").attr('data-row', row_id);
        $("button.save_invoice_charge_btn:last").attr('data-row', row_id);

        $("select.invoice_charge_new:last").attr('id', 'invoice_charge_new_' + row_id);
        //$("form.invoice_form_new:last").attr('id', 'invoice_form_new_' + row_id);
        $("input.invoice_charge_price_new:last").attr('id', 'invoice_charge_price_new_' + row_id);
        $("input.invoice_charge_qty_new:last").attr('id', 'invoice_charge_qty_new_' + row_id);
        $("select.invoice_charge_tax_new:last").attr('id', 'invoice_charge_tax_new_' + row_id);

        $("div.invoice_charge_total_new:last").attr('id', 'invoice_charge_total_new_' + row_id);
        $("input.invoice_charge_total_new_field:last").attr('id', 'invoice_charge_total_new_field_' + row_id);
        $("input.invoice_charge_type_new:last").attr('id', 'invoice_charge_type_new_' + row_id);
        //hide add new line button
        $("#add_invoice_charge_line").toggle();
    });

    $("body").off('change', '.invoice_charge_new').on('change', '.invoice_charge_new', function (e) {
        var row_id = $(this).data('row');
        var price = $(this).find(':selected').data('price');
        var type = $(this).find(':selected').data('type');
        $("#invoice_charge_type_new_" + row_id).val(type);
        $("#invoice_charge_qty_new_" + row_id).val(1);

        var invoice_id = $('input[name="invoice_id"]').val();
        var _token = $('input[name="_token"]').val();

        //start:: calculating charge price        
        $.ajax({
            url: "/admin/moving/ajaxCalculateChargePrice",
            method: 'post',
            data: {
                '_token': _token,
                'invoice_id': invoice_id,
                'price': price,
            },
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (charges) {
                $("#invoice_charge_price_new_" + row_id).val(charges);
                var total = parseFloat((charges) * 1).toFixed(2);
                $("#invoice_charge_total_new_" + row_id).html(total);
                $("#invoice_charge_total_new_field_" + row_id).val(total);
            }
        });
        //end:: calculating charge price        

    });

    $("body").off('change keyup paste', '.invoice_charge_price_new,.invoice_charge_tax_new,.invoice_charge_qty_new')
        .on('change keyup paste', '.invoice_charge_price_new,.invoice_charge_tax_new,.invoice_charge_qty_new', function (e) {

            var row_id = $(this).data('row');
            var tax = parseFloat($("#invoice_charge_tax_new_" + row_id).find(':selected').data('rate'));
            var qty = parseFloat($("#invoice_charge_qty_new_" + row_id).val());
            var price = parseFloat($("#invoice_charge_price_new_" + row_id).val());
            console.log(tax);
            if (isNaN(tax)) {
                tax = 0;
            }
            if (isNaN(price)) {
                price = 0;
            }
            if (isNaN(qty)) {
                qty = 0;
            }
            var total = parseFloat((price * qty) * (1 + tax / 100)).toFixed(2);
            $("#invoice_charge_total_new_" + row_id).html(total);
            $("#invoice_charge_total_new_field_" + row_id).val(total);
        });

    //Edit invoice line item
    $("body").off('change', '.invoice_charge_edit').on('change', '.invoice_charge_edit', function (e) {
        var row_id = $(this).data('row');
        var price = $(this).find(':selected').data('price');
        var type = $(this).find(':selected').data('type');
        $("#invoice_charge_type_edit_" + row_id).val(type);
        $("#invoice_charge_qty_edit_" + row_id).val(1);


        var invoice_id = $('input[name="invoice_id"]').val();
        var _token = $('input[name="_token"]').val();
        //start:: calculating charge price        
        $.ajax({
            url: "/admin/moving/ajaxCalculateChargePrice",
            method: 'post',
            data: {
                '_token': _token,
                'invoice_id': invoice_id,
                'price': price,
            },
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (charges) {
                $("#invoice_charge_price_edit_" + row_id).val(charges);
                var total = parseFloat((charges) * 1).toFixed(2);
                $("#invoice_charge_total_edit_" + row_id).html(total);
                $("#invoice_charge_total_edit_field_" + row_id).val(total);
            }
        });
        //end:: calculating charge price
    });

    $("body").off('change keyup paste', '.invoice_charge_price_edit,.invoice_charge_tax_edit,.invoice_charge_qty_edit')
        .on('change keyup paste', '.invoice_charge_price_edit,.invoice_charge_tax_edit,.invoice_charge_qty_edit', function (e) {

            var row_id = $(this).data('row');
            var tax = parseFloat($("#invoice_charge_tax_edit_" + row_id).find(':selected').data('rate'));
            var qty = parseFloat($("#invoice_charge_qty_edit_" + row_id).val());
            var price = parseFloat($("#invoice_charge_price_edit_" + row_id).val());
            if (isNaN(price)) {
                price = 0;
            }
            if (isNaN(qty)) {
                qty = 0;
            }
            var total = parseFloat((price * qty) * (1 + tax / 100)).toFixed(2);
            $("#invoice_charge_total_edit_" + row_id).html(total);
            $("#invoice_charge_total_edit_field_" + row_id).val(total);
        });

    $("body").off('click', '.cancel_update_invoice_charge_btn').on('click', '.cancel_update_invoice_charge_btn', function (e) {
        var id = $(this).data('id');
        $("#invoice_charge_line_div_edit_" + id).css("display", "none");
        $("#invoice_charge_line_div_view_" + id).css("display", "table-row");

    });

    $("body").off('click', '.edit_invoice_charge_btn').on('click', '.edit_invoice_charge_btn', function (e) {
        var id = $(this).data('id');
        $("#invoice_charge_line_div_edit_" + id).css("display", "table-row");
        $("#invoice_charge_line_div_view_" + id).css("display", "none");

    });

    $('body').on('click', '.update_invoice_charge_btn', function (e) {
        e.preventDefault();
        var row_id = $(this).data('id');
        var invoice_id = $('input[name="invoice_id"]').val();
        var _token = $('input[name="_token"]').val();
        var name = $('#invoice_charge_edit_' + row_id).find(":selected").val();
        var product_id = $('#invoice_charge_edit_' + row_id).find(":selected").data('pid');
        var tax_id = $("#invoice_charge_tax_edit_" + row_id).find(':selected').val();
        var unit_price = $("#invoice_charge_price_edit_" + row_id).val();
        var type = $("#invoice_charge_type_edit_" + row_id).val();
        var amount = $("#invoice_charge_total_edit_field_" + row_id).val();
        var job_id = $('input[name="job_id"]').val();

        $.ajax({
            url: "/admin/moving/ajaxUpdateInvoice",
            method: 'post',
            data: {
                'id': row_id,
                'job_id': job_id,
                '_token': _token,
                'invoice_id': invoice_id,
                'product_id': product_id,
                'name': name,
                'tax_id': tax_id,
                'unit_price': unit_price,
                'quantity': 1,
                'type': type,
                'amount': amount
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
                    $('#invoice_table_grid').html(result.html);
                    $('#totalInvoiceAmount').html(result.amount);
                    $('#totalPaidAmount').html(result.paid);
                    $('#totalBalanceAmount').html(result.balance);
                    $('#grand_total_balance').html(result.balance);
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

    //Add new invoice Line item
    $('body').on('click', '.save_invoice_charge_btn', function (e) {
        e.preventDefault();
        var row_id = $(this).data('row');
        var job_id = $('input[name="job_id"]').val();
        var invoice_id = $('input[name="invoice_id"]').val();
        var _token = $('input[name="_token"]').val();
        var name = $('#invoice_charge_new_' + row_id).find(":selected").val();
        var product_id = $('#invoice_charge_new_' + row_id).find(":selected").data('pid');
        var tax_id = $("#invoice_charge_tax_new_" + row_id).find(':selected').val();
        var unit_price = $("#invoice_charge_price_new_" + row_id).val();
        var type = $("#invoice_charge_type_new_" + row_id).val();
        var amount = $("#invoice_charge_total_new_field_" + row_id).val();

        $.ajax({
            url: "/admin/moving/ajaxSaveInvoice",
            method: 'post',
            data: {
                '_token': _token,
                'job_id': job_id,
                'invoice_id': invoice_id,
                'product_id': product_id,
                'description': "",
                'name': name,
                'tax_id': tax_id,
                'unit_price': unit_price,
                'quantity': 1,
                'type': type,
                'amount': amount
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
                    $('#invoice_table_grid').html(result.html);
                    $('#totalInvoiceAmount').html(result.amount);
                    $('#totalPaidAmount').html(result.paid);
                    $('#totalBalanceAmount').html(result.balance);
                    $('#grand_total_balance').html(result.balance);
                    $('#grand_total_balance').html(result.invoice_id);

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

    $('body').on('click', '.delete_invoice_charge_btn', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var job_id = $('input[name="job_id"]').val();
        var _token = $('input[name="_token"]').val();
        var invoice_id = $('input[name="invoice_id"]').val();
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted invoice item!",
            icon: "warning",
            buttons: true
        }).then((isConfirm) => {
            if (isConfirm) {
                $.ajax({
                    url: "/admin/moving/ajaxDestroyInvoiceItem",
                    method: 'post',
                    data: { '_token': _token, 'job_id': job_id, 'invoice_id': invoice_id, 'id': id },
                    dataType: "json",
                    beforeSend: function () {
                        $.blockUI();
                    },
                    complete: function () {
                        $.unblockUI();
                    },
                    success: function (result) {
                        if (result.error == 0) {
                            $('#invoice_table_grid').html(result.html);
                            $('#totalInvoiceAmount').html(result.amount);
                            $('#totalPaidAmount').html(result.paid);
                            $('#totalBalanceAmount').html(result.balance);
                            $('#grand_total_balance').html(result.balance);
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
            }
        });
    });

    $("body").off('click', '.cancel_invoice_charge_btn').on('click', '.cancel_invoice_charge_btn', function (e) {
        var row_id = $(this).data('row');
        $("#invoice_charge_line_div_new_" + row_id).remove();
        $("#add_invoice_charge_line").toggle();

    });

    $("body").off('click', '#recalculate').on('click', '#recalculate', function (e) {
        var invoice_id = $('input[name="invoice_id"]').val();
        var job_id = $('input[name="job_id"]').val();
        var _token = $('input[name="_token"]').val();

        //start:: calculating charge price        
        $.ajax({
            url: "/admin/moving/ajaxReCalculateChargePrice",
            method: 'post',
            data: {
                '_token': _token,
                'invoice_id': invoice_id,
                'job_id': job_id
            },
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    $('#invoice_table_grid').html(result.html);

                    $.toast({
                        heading: 'Success',
                        text: result.message,
                        icon: 'success',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#00c292',
                        textColor: 'white'
                    });
                }else {
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
        //end:: calculating charge price
    });

}); // ready function end