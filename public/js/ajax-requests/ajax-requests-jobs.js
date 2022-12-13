$(document).ready(function () {
    //START:: Invoice Section
    $("body").on('click', '#add_invoice_line', function (e) {
        $("#invoice_line_div_new").clone().insertAfter("tr.invoice_line_div:last").css("display", "table-row");
        var row_id = $("#invoice_line_div_new").data('row') + 1;
        $("#invoice_line_div_new").data('row', row_id);
        $("tr.invoice_line_div:last").attr('id', 'invoice_line_div_new_' + row_id);
        $("tr.invoice_line_div:last").attr('data-row', row_id);

        //edit line
        $("button.edit_invoice_btn:last").attr('data-row', row_id);
        $('input.invoice_product_new:last').attr('data-row', row_id);
        //--
        $("select.invoice_product_new:last").attr('data-row', row_id);
        $("select.invoice_tax_new:last").attr('data-row', row_id);
        $("input.invoice_price_new:last").attr('data-row', row_id);
        $("input.invoice_qty_new:last").attr('data-row', row_id);
        $("input.invoice_total_new_field:last").attr('data-row', row_id);
        $("button.cancel_invoice_btn:last").attr('data-row', row_id);
        $("button.save_invoice_btn:last").attr('data-row', row_id);

        $("select.invoice_product_new:last").attr('id', 'invoice_product_new_' + row_id);
        $("input.invoice_product_new:last").attr('id', 'invoice_product_new_' + row_id);
        $("form.invoice_form_new:last").attr('id', 'invoice_form_new_' + row_id);
        $("textarea.invoice_description_new:last").attr('id', 'invoice_description_new_' + row_id);
        $("input.invoice_price_new:last").attr('id', 'invoice_price_new_' + row_id);
        $("input.invoice_qty_new:last").attr('id', 'invoice_qty_new_' + row_id);
        $("select.invoice_tax_new:last").attr('id', 'invoice_tax_new_' + row_id);

        // $("div.invoice_total_new:last").attr('id', 'invoice_total_new_' + row_id);
        $("input.invoice_total_new_field:last").attr('id', 'invoice_total_new_field_' + row_id);
        $("input.invoice_item_type_new:last").attr('id', 'invoice_item_type_new_' + row_id);
        //hide add new line button
        $("#add_invoice_line").toggle();
    });

    $("body").off('change', '.invoice_product_new').on('change', '.invoice_product_new', function (e) {
        var row_id = $(this).data('row');
        var value = $(this).val();
        var desc = $('#job_products [value="' + value + '"]').data('desc');
        var price = $('#job_products [value="' + value + '"]').data('price');
        var tax = $('#job_products [value="' + value + '"]').data('tax');
        var type = $('#job_products [value="' + value + '"]').data('type');
        var pid = $('#job_products [value="' + value + '"]').data('pid');
        $(this).attr('data-type', type);
        $(this).attr('data-tax', tax);
        $(this).attr('data-price', price);
        $(this).attr('data-pid', pid);

        //----start set dynamic description parameter
        var job_id = $('input[name="job_id"]').val();
        var _token = $('input[name="_token"]').val();
        if (desc) {
            $.ajax({
                url: "/admin/crm/crm-leads/ajaxSetProductDescParameter",
                method: 'post',
                data: {
                    '_token': _token,
                    'lead_id': 0,
                    'description': desc,
                    'job_id': job_id
                },
                dataType: "json",
                beforeSend: function () {
                    $.blockUI();
                },
                complete: function () {
                    $.unblockUI();
                },
                success: function (result) {
                    if (result.desc != null) {
                        $("#invoice_description_new_" + row_id).val(result.desc);
                    }
                }
            });
        }
        //---end--->        
        // if(price)
        // {
        $("#invoice_price_new_" + row_id).val(price);
        $("#invoice_item_type_new_" + row_id).val(type);
        $("#invoice_qty_new_" + row_id).val(1);
        if (isNaN(price)) {
            price = 0;
        }
        var total = parseFloat((price) * 1).toFixed(2);
        // $("#invoice_total_new_" + row_id).html(total);
        $("#invoice_total_new_field_" + row_id).val(total);
        $("#invoice_tax_new_" + row_id + " option[value='" + tax + "']").attr('selected', 'selected').change();
        //}
    });

    $("body").off('change keyup paste', '.invoice_price_new,.invoice_tax_new,.invoice_qty_new')
        .on('change keyup paste', '.invoice_price_new,.invoice_tax_new,.invoice_qty_new', function (e) {

            var row_id = $(this).data('row');
            var tax = parseFloat($("#invoice_tax_new_" + row_id).find(':selected').data('rate'));
            var qty = parseFloat($("#invoice_qty_new_" + row_id).val());
            var price = parseFloat($("#invoice_price_new_" + row_id).val());
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
            // $("#invoice_total_new_" + row_id).html(total);
            $("#invoice_total_new_field_" + row_id).val(total);
        });

    //Edit invoice line item
    $("body").off('change', '.invoice_product_edit').on('change', '.invoice_product_edit', function (e) {
        var row_id = $(this).data('row');
        var desc = $(this).find(':selected').data('desc');
        var price = $(this).find(':selected').data('price');
        var type = $(this).find(':selected').data('type');
        var tax = $(this).find(':selected').data('tax');
        if (desc) {
            $("#invoice_description_edit_" + row_id).val(desc);
            $("#invoice_price_edit_" + row_id).val(price);
            $("#invoice_item_type_edit_" + row_id).val(type);
            $("#invoice_qty_edit_" + row_id).val(1);
            if (isNaN(price)) {
                price = 0;
            }
            var total = parseFloat((price) * 1).toFixed(2);
            // $("#invoice_total_edit_" + row_id).html(total);
            $("#invoice_total_edit_field_" + row_id).val(total);
            $("#invoice_tax_edit_" + row_id + " option[value='" + tax + "']").attr('selected', 'selected').change();
        }
    });

    $("body").off('change keyup paste', '.invoice_price_edit,.invoice_tax_edit,.invoice_qty_edit')
        .on('change keyup paste', '.invoice_price_edit,.invoice_tax_edit,.invoice_qty_edit', function (e) {

            var row_id = $(this).data('row');
            var tax = parseFloat($("#invoice_tax_edit_" + row_id).find(':selected').data('rate'));
            var qty = parseFloat($("#invoice_qty_edit_" + row_id).val());
            var price = parseFloat($("#invoice_price_edit_" + row_id).val());
            if (isNaN(price)) {
                price = 0;
            }
            if (isNaN(qty)) {
                qty = 0;
            }
            var total = parseFloat((price * qty) * (1 + tax / 100)).toFixed(2);
            // $("#invoice_total_edit_" + row_id).html(total);
            $("#invoice_total_edit_field_" + row_id).val(total);
        });
    //start::If user update line total
    //for edit line
    $("body").off('change keyup paste', '.invoice_total_edit')
        .on('change keyup paste', '.invoice_total_edit', function (e) {

            var row_id = $(this).data('row');
            var tax = parseFloat($("#invoice_tax_edit_" + row_id).find(':selected').data('rate'));
            var qty = parseFloat($("#invoice_qty_edit_" + row_id).val());
            var total = parseFloat($(this).val());
            if (isNaN(total)) {
                total = 0;
            }
            var unit_price = parseFloat((total / qty) / (1 + tax / 100)).toFixed(2);
            $("#invoice_price_edit_" + row_id).val(unit_price);
        });
    //for new line
    $("body").off('change keyup paste', '.invoice_total_new_field')
        .on('change keyup paste', '.invoice_total_new_field', function (e) {
            var row_id = $(this).data('row');
            var tax = parseFloat($("#invoice_tax_new_" + row_id).find(':selected').data('rate'));
            var qty = parseFloat($("#invoice_qty_new_" + row_id).val());
            var total = parseFloat($(this).val());
            if (isNaN(total)) {
                total = 0;
            }
            var unit_price = parseFloat((total / qty) / (1 + tax / 100)).toFixed(2);
            $("#invoice_price_new_" + row_id).val(unit_price);
        });
    //end::If user update line total    

    $("body").off('click', '.cancel_update_invoice_btn').on('click', '.cancel_update_invoice_btn', function (e) {
        var id = $(this).data('id');
        $("#invoice_line_div_edit_" + id).css("display", "none");
        $("#invoice_line_div_view_" + id).css("display", "table-row");

    });

    $("body").off('click', '.edit_invoice_btn').on('click', '.edit_invoice_btn', function (e) {
        var id = $(this).data('id');
        $("#invoice_line_div_edit_" + id).css("display", "table-row");
        $("#invoice_line_div_view_" + id).css("display", "none");

    });

    $('body').on('click', '.update_invoice_btn', function (e) {
        e.preventDefault();
        var row_id = $(this).data('id');
        var invoice_id = $('input[name="invoice_id"]').val();
        var job_id = $('input[name="job_id"]').val();
        var _token = $('input[name="_token"]').val();
        var name = $('#invoice_product_edit_' + row_id).val();
        var product_id = $('#invoice_product_edit_' + row_id).data('pid');
        var description = $('#invoice_description_edit_' + row_id).val();
        var tax_id = $("#invoice_tax_edit_" + row_id).find(':selected').val();
        var unit_price = $("#invoice_price_edit_" + row_id).val();
        var quantity = $("#invoice_qty_edit_" + row_id).val();
        var type = $("#invoice_item_type_edit_" + row_id).val();
        var amount = $("#invoice_total_edit_field_" + row_id).val();

        $.ajax({
            url: "/admin/moving/ajaxUpdateInvoice",
            method: 'post',
            data: {
                'id': row_id,
                '_token': _token,
                'invoice_id': invoice_id,
                'job_id': job_id,
                'product_id': product_id,
                'name': name,
                'description': description,
                'tax_id': tax_id,
                'unit_price': unit_price,
                'quantity': quantity,
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
    //Add new invoice Line item
    $('body').on('click', '.save_invoice_btn', function (e) {
        e.preventDefault();
        var row_id = $(this).data('row');
        var job_id = $('input[name="job_id"]').val();
        var invoice_id = $('input[name="invoice_id"]').val();
        var _token = $('input[name="_token"]').val();
        var name = $('#invoice_product_new_' + row_id).val();
        var product_id = $('#invoice_product_new_' + row_id).data('pid');
        var description = $('#invoice_description_new_' + row_id).val();
        var tax_id = $("#invoice_tax_new_" + row_id).find(':selected').val();
        var unit_price = $("#invoice_price_new_" + row_id).val();
        var quantity = $("#invoice_qty_new_" + row_id).val();
        var type = $("#invoice_item_type_new_" + row_id).val();
        var amount = $("#invoice_total_new_field_" + row_id).val();

        $.ajax({
            url: "/admin/moving/ajaxSaveInvoice",
            method: 'post',
            data: {
                '_token': _token,
                'job_id': job_id,
                'invoice_id': invoice_id,
                'product_id': product_id,
                'name': name,
                'description': description,
                'tax_id': tax_id,
                'unit_price': unit_price,
                'quantity': quantity,
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
                    $('#grand_total_balance').html(result.balance);
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

    $("body").off('click', '.cancel_invoice_btn').on('click', '.cancel_invoice_btn', function (e) {
        var row_id = $(this).data('row');
        $("#invoice_line_div_new_" + row_id).remove();
        $("#add_invoice_line").toggle();

    });

    $('body').on('click', '.delete_invoice_btn', function (e) {
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
            }
        });
    });

    $('body').on('click', '.listJobInvoiceGenerate', function (e) {
        e.preventDefault();

        var job_id = $(this).data("jobid");
        var type = $(this).data("type");
        $.ajax({
            url: "/admin/moving/list-jobs/generateInvoice/" + job_id + "/" + type,
            method: 'GET',
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    $('#listJobInvoiceDownload').removeAttr("disabled");
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
    $('body').on('click', '.listJobInvoiceDownload', function (e) {
        e.preventDefault();
        var job_id = $(this).data("jobid");
        $.ajax({
            url: "/admin/moving/list-jobs/downloadInvoice/" + job_id,
            method: 'GET',
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    window.open(result.url);
                    //Notification....
                    // $.toast({
                    //     heading: 'Success',
                    //     text: result.message,
                    //     icon: 'success',
                    //     position: 'top-right',
                    //     loader: false,
                    //     bgColor: '#00c292',
                    //     textColor: 'white'
                    // });
                    // //..
                } else {
                    //Notification....
                    $.toast({
                        heading: 'Error',
                        text: "Invoice not yet genearated. Click on the 'Generate Invoice' button to generate the invoice.",
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

    // Generate Work Order Invoice

    $('body').on('click', '.listJobWorkOrderGenerate', function (e) {
        e.preventDefault();

        var job_id = $(this).data("jobid");
        var type = $(this).data("type");
        $.ajax({
            url: "/admin/moving/list-jobs/generateWorkOrder/" + job_id + "/" + type,
            method: 'GET',
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    $('#listWorkOrderInvoiceDownload').removeAttr("disabled");
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
    $('body').on('click', '.listJobWorkOrderDownload', function (e) {
        e.preventDefault();
        var job_id = $(this).data("jobid");
        $.ajax({
            url: "/admin/moving/list-jobs/downloadWordOrder/" + job_id,
            method: 'GET',
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    window.open(result.url);
                    //Notification....
                    // $.toast({
                    //     heading: 'Success',
                    //     text: result.message,
                    //     icon: 'success',
                    //     position: 'top-right',
                    //     loader: false,
                    //     bgColor: '#00c292',
                    //     textColor: 'white'
                    // });
                    // //..
                } else {
                    //Notification....
                    $.toast({
                        heading: 'Error',
                        text: "Work order not yet genearated. Click on the 'Generate Work Order' button to generate.",
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
    //END Generate Work Order Invoice

    // Generate POd Invoice
    $('body').on('click', '.listJobPODGenerate', function (e) {
        e.preventDefault();
        var job_id = $(this).data("jobid");
        var type = $(this).data("type");
        $.ajax({
            url: "/admin/moving/list-jobs/generatePod/" + job_id + "/" + type,
            method: 'GET',
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    $('#listJobPODDownload').removeAttr("disabled");
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
                if (result.error == 2) {
                    //Notification....
                    swal({
                        title: "Warning",
                        text: result.message,
                        type: "warning",
                        button: "OK",
                    });
                    //..
                }
            }
        });
    });
    $('body').on('click', '.listJobPODDownload', function (e) {
        e.preventDefault();
        var job_id = $(this).data("jobid");
        $.ajax({
            url: "/admin/moving/list-jobs/downloadPod/" + job_id,
            method: 'GET',
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    window.open(result.url);
                    //Notification....
                    // $.toast({
                    //     heading: 'Success',
                    //     text: result.message,
                    //     icon: 'success',
                    //     position: 'top-right',
                    //     loader: false,
                    //     bgColor: '#00c292',
                    //     textColor: 'white'
                    // });
                    // //..
                } else {
                    //Notification....
                    $.toast({
                        heading: 'Error',
                        text: "Work order not yet genearated. Click on the 'Generate Work Order' button to generate.",
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
    //END Genearet POD Invoice

    // START:: Generate Inventory PDF
    $('body').on('click', '.inventoryPdfGenerate', function (e) {
        e.preventDefault();
        var job_id = $(this).data("jobid");
        var type = $(this).data("type");

        $.ajax({
            url: "/admin/moving/list-jobs/generateInventoryPdf/" + job_id + "/" + type,
            method: 'GET',
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    $('#inventoryPdfDownload').removeAttr("disabled");
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
                if (result.error == 2) {

                }
            }
        });
    });
    $('body').on('click', '.inventoryPdfDownload', function (e) {
        e.preventDefault();
        var job_id = $(this).data("jobid");
        $.ajax({
            url: "/admin/moving/list-jobs/downloadInventoryPdf/" + job_id,
            method: 'GET',
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0)
                    window.open(result.url);
                else if (result.error == 2)
                    //Notification....
                    $.toast({
                        heading: 'Error',
                        text: result.message,
                        icon: 'error',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#FF0000',
                        textColor: 'white'
                    });
                // //..
                else
                    //Notification....
                    $.toast({
                        heading: 'Error',
                        text: "Inventory List not yet genearated. Click on the 'Generate Inventory List' button to generate.",
                        icon: 'error',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#fb9678',
                        textColor: 'white'
                    });
                //..
            }
        });
    });
    //END Generate Inventory PDF

    //START:Discount 
    $('body').on('click', '.discount_type_option', function (e) {
        var type = $(this).data('val');
        $("#discount_type_label").html(" - " + type);
        $("#discount_type_field").val(type);
        $("#discount_label").hide();
        $("#discount_value").show();
    });

    $('body').on('click', '#cancel_discount_btn', function (e) {
        $("#discount_value").hide();
        $("#discount_label").show();
    });

    $('body').on('click', '#save_discount_btn', function (e) {
        e.preventDefault();
        var discount = $("#discount_value_field").val();
        var discount_type = $("#discount_type_field").val();
        var lead_id = $("#lead_id").val();
        var invoice_id = $('input[name="invoice_id"]').val();
        var _token = $('input[name="_token"]').val();
        $.ajax({
            url: "/admin/moving/ajaxSaveInvoiceDiscount",
            method: 'post',
            data: {
                '_token': _token,
                'invoice_id': invoice_id,
                'discount': discount,
                'discount_type': discount_type,
                'lead_id': lead_id
            },
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                console.log(result);
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
    //END:: Discount

    //start:: Calculate Truck CBM Value
    $(document).off('change', '#local_move_truck').on('change', '#local_move_truck', function (e) {
        var $this = $(this);
        var template_id = $this.val();
        var url = "{{ route('admin.crm-leads.calculateTruckCbmValue') }}";
        var token = "{{ csrf_token() }}";
        $.easyAjax({
            type: 'POST',
            url: url,
            data: {
                '_token': token,
                'max_cbm': $(this).val()
            },
            beforeSend: function () {
                $('.preloader').show();
            },
            complete: function () {
                $('.preloader').hide();
            },
            success: function (response) {
                console.log(response);
                if (response.error == 0) {
                    $("#total_cbm_field").val(response.cbm);
                    $("#goods_value_field").val(response.value);
                } else {
                    //Notification....
                    $.toast({
                        heading: 'Error',
                        text: response.message,
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
    //end:: Calculate Truck CBM Value

    //start:: Calculate Insurance Value by Truck CBM
    $(document).off('change', '#local_move_truck').on('change', '#local_move_truck', function (e) {
        var max_cbm = $(this).val();
        var goods_value_per_cbm = $("#goods_value_per_cbm").val();
        var goods_value_field = max_cbm * goods_value_per_cbm;
        $("#total_cbm_field").val(parseFloat(max_cbm).toFixed(2));
        $("#goods_value_field").val(parseFloat(goods_value_field).toFixed(2));
    });
    //end:: Calculate Insurance Value by Truck CBM

    //start:: Calculate Insurance Value by Manual CBM
    $(document).off('keyup', '#total_cbm_field').on('keyup', '#total_cbm_field', function (e) {
        var total_cbm_field = $(this).val();
        var goods_value_per_cbm = $("#goods_value_per_cbm").val();
        var goods_value_field = total_cbm_field * goods_value_per_cbm;
        $("#goods_value_field").val(parseFloat(goods_value_field).toFixed(2));
    });
    //end:: Calculate Insurance Value by Manual CBM

    // start:: Storage Invoice Generate / Download
    $('body').on('click', '#generateStorageInvoice', function (e) {
        e.preventDefault();

        var invoiceid = $(this).data("invoiceid");
        var type = $(this).data("type");
        $.ajax({
            url: "/admin/moving/list-jobs/generateStorageInvoice/" + invoiceid + "/" + type,
            method: 'GET',
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    $('#downloadStorageInvoice').removeAttr("disabled");
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
    $('body').on('click', '#downloadStorageInvoice', function (e) {
        e.preventDefault();
        var invoiceid = $(this).data("invoiceid");
        $.ajax({
            url: "/admin/moving/list-jobs/downloadStorageInvoice/" + invoiceid,
            method: 'GET',
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    window.open(result.url);
                    //Notification....
                    // $.toast({
                    //     heading: 'Success',
                    //     text: result.message,
                    //     icon: 'success',
                    //     position: 'top-right',
                    //     loader: false,
                    //     bgColor: '#00c292',
                    //     textColor: 'white'
                    // });
                    // //..
                } else {
                    //Notification....
                    $.toast({
                        heading: 'Error',
                        text: "Invoice not yet genearated. Click on the 'Generate Invoice' button to generate the invoice.",
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
    // end:: Storage Invoice Generate / Download
}); // ready function end