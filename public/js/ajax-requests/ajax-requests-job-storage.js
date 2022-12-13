$(document).ready(function() {    
    //START:: Storage Reservation Section    

    $('body').on('click', '#storage_tab_btn', function(e) {
        e.preventDefault();
        var job_id = $('input[name="job_id"]').val();
        var _token = $('input[name="_token"]').val();
        $.ajax({
            url: "/admin/moving/storageTabContent",
            method: 'post',
            data: { '_token': _token,job_id:job_id},
            dataType: "json",
            beforeSend: function() {
                $(".preloader").show();
            },
            complete: function() {
                $(".preloader").hide();
            },
            success: function(result) {
                if (result.error == 0) {
                    $('#storage_reservation').html(result.storage_reservation_html);
                    $('#storage_invoice').html(result.storage_invoice_html);
                    $('#storage_payment').html(result.storage_payment_html);
                    $('.daterange-single').daterangepicker({ 
                        singleDatePicker: true,
                        locale: {
                            format: 'DD/MM/YYYY'
                        }
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

    $('body').on('click', '#search-reservation-filters', function(e) {
        e.preventDefault();
        $.ajax({
            url: "/admin/storage/find-available-storage-units",
            method: 'post',
            data: $("#storage_reservation_form").serialize(),
            dataType: "json",
            beforeSend: function() {
                $(".preloader").show();
            },
            complete: function() {
                $(".preloader").hide();
            },
            success: function(result) {
                if (result.error == 0) {
                    var len = result.data.length;
                    console.log(result.data);
                    console.log(len);
                    $("#storage_line_div_new").css("display", "table-row");
                    $("#storage_line_div_norecord").hide();
                    $("#storage_unit_new").empty();
                    for( var i = 0; i<len; i++){
                        var id = result.data[i]['id'];
                        var name = result.data[i]['name'];                        
                        var serial = result.data[i]['serial_number'];
                        $("#storage_unit_new").append("<option value='"+id+"'>"+serial+"-"+name+"</option>");
                    }
                    $("#storage_unit_start_date_new").val($("#storage_unit_start_date").val());     
                    $("#storage_unit_end_date_new").val($("#storage_unit_end_date").val());
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
                }else if(result.error == 1){
                    $("#storage_unit_new").empty();
                    $("#storage_line_div_new").css("display", "none");
                    $.toast({
                        heading: 'Warning',
                        text: result.message,
                        icon: 'warning',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#ea8d2e',
                        textColor: 'white'
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

    $('body').on('click', '.save_storage_btn', function(e) {
        e.preventDefault();
        var lead_id = $('input[name="lead_id"]').val();
        var job_id = $('input[name="job_id"]').val();
        var _token = $('input[name="_token"]').val();
        var unit_id = $('#storage_unit_new').find(":selected").val();
        var crm_opportunity_id = $('#estimate_opp_id').find(":selected").val();
        var from_date = $('#storage_unit_start_date_new').val();
        var to_date = $("#storage_unit_end_date_new").val();
        var allocation_status = "Occupied";        

        $.ajax({
            url: "/admin/storage/ajaxSaveStorageReservation",
            method: 'post',
            data: {
                '_token': _token,
                'lead_id': 0,
                'job_id':job_id,
                'unit_id': unit_id,
                'from_date': from_date,
                'to_date': to_date,
                'allocation_status': allocation_status,
                'crm_opportunity_id': crm_opportunity_id
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
                    $('#storage_tab_btn').click();
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
                } else if (result.error == 1){
                    swal({
                        title: "Warning",
                        text: result.message,
                        type: "warning",
                        button: "OK",
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
    $("body").off('click', '.cancel_storage_btn').on('click', '.cancel_storage_btn', function(e) {
        $("#storage_line_div_new").toggle();

    });

    $('body').on('click', '.delete_storage_reservation_btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');        
        var _token = $('input[name="_token"]').val();
        swal({
            title: "Are you sure?",
            text: "Are you sure? You will not be able to recover this deleted reservation!",
            icon: "warning",
            buttons: true
        }).then((isConfirm) => {
            if (isConfirm) {
                $.ajax({
                    url: "/admin/storage/ajaxDestroyStorageReservation",
                    method: 'post',
                    data: { '_token': _token, 'id': id,},
                    dataType: "json",
                    beforeSend: function() {
                        $(".preloader").show();
                    },
                    complete: function() {
                        $(".preloader").hide();
                    },
                    success: function(result) {
                        if (result.error == 0) {
                            $('#storage_tab_btn').click();
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
    
    //END:: Storage Reservation Section

    //START:: Invoice Section
    $("body").on('click', '#add_s_invoice_line', function(e) {
        $("#s_invoice_line_div_new").clone().insertAfter("tr.s_invoice_line_div:last").css("display", "table-row");
        var row_id = $("#s_invoice_line_div_new").data('row') + 1;
        $("#s_invoice_line_div_new").data('row', row_id);
        $("tr.s_invoice_line_div:last").attr('id', 's_invoice_line_div_new_' + row_id);
        $("tr.s_invoice_line_div:last").attr('data-row', row_id);

        //edit line
        $("button.edit_s_invoice_btn:last").attr('data-row', row_id);
        //--
        $("select.s_invoice_product_new:last").attr('data-row', row_id);
        $("select.s_invoice_tax_new:last").attr('data-row', row_id);
        $("input.s_invoice_price_new:last").attr('data-row', row_id);
        $("input.s_invoice_qty_new:last").attr('data-row', row_id);
        $("input.s_invoice_total_new_field:last").attr('data-row', row_id);
        $("button.cancel_s_invoice_btn:last").attr('data-row', row_id);
        $("button.save_s_invoice_btn:last").attr('data-row', row_id);

        $("select.s_invoice_product_new:last").attr('id', 's_invoice_product_new_' + row_id);
        $("form.s_invoice_form_new:last").attr('id', 's_invoice_form_new_' + row_id);
        $("textarea.s_invoice_description_new:last").attr('id', 's_invoice_description_new_' + row_id);
        $("input.s_invoice_price_new:last").attr('id', 's_invoice_price_new_' + row_id);
        $("input.s_invoice_qty_new:last").attr('id', 's_invoice_qty_new_' + row_id);
        $("select.s_invoice_tax_new:last").attr('id', 's_invoice_tax_new_' + row_id);

        // $("div.s_invoice_total_new:last").attr('id', 's_invoice_total_new_' + row_id);
        $("input.s_invoice_total_new_field:last").attr('id', 's_invoice_total_new_field_' + row_id);
        $("input.s_invoice_item_type_new:last").attr('id', 's_invoice_item_type_new_' + row_id);
        //hide add new line button
        $("#add_s_invoice_line").toggle();
    });
    
    $("body").off('change', '.s_invoice_product_new').on('change', '.s_invoice_product_new', function(e) {
        var row_id = $(this).data('row');
        var desc = $(this).find(':selected').data('desc');
        var price = $(this).find(':selected').data('price');
        var type = $(this).find(':selected').data('type');
        var tax = $(this).find(':selected').data('tax');
        
        //----start set dynamic description parameter
        var job_id = $('input[name="job_id"]').val();
        var _token = $('input[name="_token"]').val();
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxSetProductDescParameter",
            method: 'post',
            data: {
                '_token': _token,
                'lead_id': 0,
                'description': desc,
                'job_id':job_id
            },
            dataType: "json",
            beforeSend: function() {
                $.blockUI();
            },
            complete: function() {
                $.unblockUI();
            },
            success: function(result) {
                $("#s_invoice_description_new_" + row_id).val(result.desc);
            }
        });
        //---end--->        
        $("#s_invoice_price_new_" + row_id).val(price);
        $("#s_invoice_item_type_new_" + row_id).val(type);
        $("#s_invoice_qty_new_" + row_id).val(1);
        if (isNaN(price)) {
            price = 0;
        }
        var total = parseFloat((price) * 1).toFixed(2);
        // $("#s_invoice_total_new_" + row_id).html(total);
        $("#s_invoice_total_new_field_" + row_id).val(total);
        $("#s_invoice_tax_new_"+row_id+" option[value='"+tax+"']").attr('selected','selected').change();
    });
    
    $("body").off('change keyup paste', '.s_invoice_price_new,.s_invoice_tax_new,.s_invoice_qty_new')
        .on('change keyup paste', '.s_invoice_price_new,.s_invoice_tax_new,.s_invoice_qty_new', function(e) {

            var row_id = $(this).data('row');
            var tax = parseFloat($("#s_invoice_tax_new_" + row_id).find(':selected').data('rate'));
            var qty = parseFloat($("#s_invoice_qty_new_" + row_id).val());
            var price = parseFloat($("#s_invoice_price_new_" + row_id).val());
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
            var total = parseFloat((price*qty)*(1 + tax/100)).toFixed(2);
            // $("#s_invoice_total_new_" + row_id).html(total);
            $("#s_invoice_total_new_field_" + row_id).val(total);
        });
    
        //Edit invoice line item
    $("body").off('change', '.s_invoice_product_edit').on('change', '.s_invoice_product_edit', function(e) {
        var row_id = $(this).data('row');
        var desc = $(this).find(':selected').data('desc');
        var price = $(this).find(':selected').data('price');
        var type = $(this).find(':selected').data('type');
        var tax = $(this).find(':selected').data('tax');
        $("#s_invoice_description_edit_" + row_id).val(desc);
        $("#s_invoice_price_edit_" + row_id).val(price);
        $("#s_invoice_item_type_edit_" + row_id).val(type);
        $("#s_invoice_qty_edit_" + row_id).val(1);
        if (isNaN(price)) {
            price = 0;
        }
        var total = parseFloat((price) * 1).toFixed(2);
        // $("#s_invoice_total_edit_" + row_id).html(total);
        $("#s_invoice_total_edit_field_" + row_id).val(total);
        $("#s_invoice_tax_edit_"+row_id+" option[value='"+tax+"']").attr('selected','selected').change();
    });
    
    $("body").off('change keyup paste', '.s_invoice_price_edit,.s_invoice_tax_edit,.s_invoice_qty_edit')
        .on('change keyup paste', '.s_invoice_price_edit,.s_invoice_tax_edit,.s_invoice_qty_edit', function(e) {

            var row_id = $(this).data('row');
            var tax = parseFloat($("#s_invoice_tax_edit_" + row_id).find(':selected').data('rate'));
            var qty = parseFloat($("#s_invoice_qty_edit_" + row_id).val());
            var price = parseFloat($("#s_invoice_price_edit_" + row_id).val());
            if (isNaN(price)) {
                price = 0;
            }
            if (isNaN(qty)) {
                qty = 0;
            }
            var total = parseFloat((price*qty)*(1 + tax/100)).toFixed(2);
            // $("#s_invoice_total_edit_" + row_id).html(total);
            $("#s_invoice_total_edit_field_" + row_id).val(total);
        });
    //start::If user update line total
    //for edit line
    $("body").off('change keyup paste', '.s_invoice_total_edit')
        .on('change keyup paste', '.s_invoice_total_edit', function(e) {

            var row_id = $(this).data('row');
            var tax = parseFloat($("#s_invoice_tax_edit_" + row_id).find(':selected').data('rate'));
            var qty = parseFloat($("#s_invoice_qty_edit_" + row_id).val());
            var total = parseFloat($(this).val());
            if (isNaN(total)) {
                total = 0;
            }
            var unit_price = parseFloat((total/qty)/(1 + tax/100)).toFixed(2);
            $("#s_invoice_price_edit_" + row_id).val(unit_price);
    });
    //for new line
    $("body").off('change keyup paste', '.s_invoice_total_new_field')
        .on('change keyup paste', '.s_invoice_total_new_field', function(e) {
            var row_id = $(this).data('row');
            var tax = parseFloat($("#s_invoice_tax_new_"+row_id).find(':selected').data('rate'));
            var qty = parseFloat($("#s_invoice_qty_new_"+row_id).val());
            var total = parseFloat($(this).val());
            if (isNaN(total)) {
                total = 0;
            }
            var unit_price = parseFloat((total/qty)/(1 + tax/100)).toFixed(2);
            $("#s_invoice_price_new_"+row_id).val(unit_price);
    });
    //end::If user update line total    
    
    $("body").off('click', '.cancel_update_s_invoice_btn').on('click', '.cancel_update_s_invoice_btn', function(e) {
        var id = $(this).data('id');
        $("#s_invoice_line_div_edit_" + id).css("display", "none");
        $("#s_invoice_line_div_view_" + id).css("display", "table-row");

    });
    
    $("body").off('click', '.edit_s_invoice_btn').on('click', '.edit_s_invoice_btn', function(e) {
        var id = $(this).data('id');
        $("#s_invoice_line_div_edit_" + id).css("display", "table-row");
        $("#s_invoice_line_div_view_" + id).css("display", "none");

    });
    
    $('body').on('click', '.update_s_invoice_btn', function(e) {
        e.preventDefault();
        var row_id = $(this).data('id');
        var invoice_id = $('input[name="storage_invoice_id"]').val();
        var _token = $('input[name="_token"]').val();
        var name = $('#s_invoice_product_edit_' + row_id).find(":selected").val();
        var product_id = $('#s_invoice_product_edit_' + row_id).find(":selected").data('pid');
        var description = $('#s_invoice_description_edit_' + row_id).val();
        var tax_id = $("#s_invoice_tax_edit_" + row_id).find(':selected').val();
        var unit_price = $("#s_invoice_price_edit_" + row_id).val();
        var quantity = $("#s_invoice_qty_edit_" + row_id).val();
        var type = $("#s_invoice_item_type_edit_" + row_id).val();
        var amount = $("#s_invoice_total_edit_field_" + row_id).val();
        var sys_job_type = "Moving_Storage";

        $.ajax({
            url: "/admin/moving/ajaxUpdateInvoice",
            method: 'post',
            data: {
                'id': row_id,
                '_token': _token,
                'invoice_id': invoice_id,
                'product_id': product_id,
                'name': name,
                'description': description,
                'tax_id': tax_id,
                'unit_price': unit_price,
                'quantity': quantity,
                'type': type,
                'sys_job_type':sys_job_type,
                'amount': amount
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
                    $('#storage_invoice').html(result.storage_invoice_html);
                    $('#storage_payment').html(result.storage_payment_html);
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
    //Add new invoice Line item
    $('body').on('click', '.save_s_invoice_btn', function(e) {
        e.preventDefault();
        var row_id = $(this).data('row');
        var job_id = $('input[name="job_id"]').val();
        var invoice_id = $('input[name="storage_invoice_id"]').val();
        var _token = $('input[name="_token"]').val();
        var name = $('#s_invoice_product_new_' + row_id).find(":selected").val();
        var product_id = $('#s_invoice_product_new_' + row_id).find(":selected").data('pid');
        var description = $('#s_invoice_description_new_' + row_id).val();
        var tax_id = $("#s_invoice_tax_new_" + row_id).find(':selected').val();
        var unit_price = $("#s_invoice_price_new_" + row_id).val();
        var quantity = $("#s_invoice_qty_new_" + row_id).val();
        var type = $("#s_invoice_item_type_new_" + row_id).val();
        var amount = $("#s_invoice_total_new_field_" + row_id).val();
        var sys_job_type = "Moving_Storage";

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
                'sys_job_type':sys_job_type,
                'amount': amount
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
                    $('#storage_invoice').html(result.storage_invoice_html);
                    $('#storage_payment').html(result.storage_payment_html);
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
    
    $("body").off('click', '.cancel_s_invoice_btn').on('click', '.cancel_s_invoice_btn', function(e) {
        var row_id = $(this).data('row');
        $("#s_invoice_line_div_new_" + row_id).remove();
        $("#add_s_invoice_line").toggle();

    });

    $('body').on('click', '.delete_s_invoice_btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var job_id = $('input[name="job_id"]').val();
        var _token = $('input[name="_token"]').val();
        var invoice_id = $('input[name="storage_invoice_id"]').val();
        var sys_job_type = "Moving_Storage";
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
                    data: { '_token': _token, 'job_id': job_id, 'invoice_id': invoice_id,'sys_job_type':sys_job_type, 'id': id },
                    dataType: "json",
                    beforeSend: function() {
                        $.blockUI();
                    },
                    complete: function() {
                        $.unblockUI();
                    },
                    success: function(result) {
                        if (result.error == 0) {
                            $('#storage_invoice').html(result.storage_invoice_html);
                            $('#storage_payment').html(result.storage_payment_html);
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
            }
        });
    });

    //START:Discount 
    $('body').on('click', '.s_discount_type_option', function(e) {
        var type = $(this).data('val');
        $("#s_discount_type_label").html(" - "+type);
        $("#s_discount_type_field").val(type);
        $("#s_discount_label").hide();
        $("#s_discount_value").show();
    });

    $('body').on('click', '#cancel_s_discount_btn', function(e) {
        $("#discount_value").hide();
        $("#discount_label").show();
    });        

    $('body').on('click', '#save_s_discount_btn', function(e) {
        e.preventDefault();
        var discount = $("#s_discount_value_field").val();         
        var discount_type = $("#s_discount_type_field").val();
        var invoice_id = $('input[name="storage_invoice_id"]').val();
        var _token = $('input[name="_token"]').val();
        var sys_job_type = "Moving_Storage";
        $.ajax({
            url: "/admin/moving/ajaxSaveInvoiceDiscount",
            method: 'post',
            data: {
                '_token': _token,
                'invoice_id': invoice_id,
                'discount': discount,
                'sys_job_type':sys_job_type,
                'discount_type': discount_type
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
                    $('#storage_invoice').html(result.storage_invoice_html);
                    $('#storage_payment').html(result.storage_payment_html);
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

    //END:: Discount

    //END:: Invoice Section

    //START:: PAYMENT Section
    $("body").on('click','#add_s_payment_line', function(e){
        $("#s_payment_line_div_new").clone().insertAfter("tr.s_payment_line_div:last").css("display", "table-row");
        var row_id = $("#s_payment_line_div_new").data('row') + 1;
        $("#s_payment_line_div_new").data('row',row_id);
        $("tr.s_payment_line_div:last").attr('id','s_payment_line_div_new_'+row_id);
        $("tr.s_payment_line_div:last").attr('data-row',row_id);

        //edit line
        $("button.edit_s_payment_btn:last").attr('data-row',row_id);
        //--
        $("select.s_payment_method_new:last").attr('data-row',row_id);

        $("button.cancel_s_payment_btn:last").attr('data-row',row_id);
        $("button.save_s_payment_btn:last").attr('data-row',row_id);

        $("select.s_payment_method_new:last").attr('id','s_payment_method_new_'+row_id);
        $("form.s_payment_form_new:last").attr('id','s_payment_form_new_'+row_id);
        $("textarea.s_payment_description_new:last").attr('id','s_payment_description_new_'+row_id);

        $("div.s_payment_total_new:last").attr('id','s_payment_total_new_'+row_id);
        $("input.s_payment_total_new_field:last").attr('id','s_payment_total_new_field_'+row_id);
        //hide add new line button
        $("#add_s_payment_line").toggle();
    });
    //Edit PAYMENT line item
    $("body").off('change','.s_payment_method_edit').on('change','.s_payment_method_edit', function(e){
        var row_id = $(this).data('row');
        $("#s_payment_description_edit_"+row_id).val(desc);
    });
    $("body").off('click','.cancel_update_s_payment_btn').on('click','.cancel_update_s_payment_btn', function(e){
        var id = $(this).data('id');
        $("#s_payment_line_div_edit_"+id).css("display", "none");
        $("#s_payment_line_div_view_"+id).css("display", "table-row");
        
    });
    $("body").off('click','.edit_s_payment_btn').on('click','.edit_s_payment_btn', function(e){
        var id = $(this).data('id');
        $("#s_payment_line_div_edit_"+id).css("display", "table-row");
        $("#s_payment_line_div_view_"+id).css("display", "none");
        
    });
    $('body').on('click', '.update_s_payment_btn', function (e) {
        e.preventDefault();
        var row_id = $(this).data('id');
        var invoice_id = $('input[name="storage_invoice_id"]').val();
        var _token = $('input[name="_token"]').val();
        var gateway = $('#s_payment_method_edit_'+row_id).find(":selected").val();
        var description = $('#s_payment_description_edit_'+row_id).val();
        var amount = $("#s_payment_total_edit_field_"+row_id).val();
        var sys_job_type = "Moving_Storage";

        $.ajax({
            url: "/admin/moving/ajaxUpdatePayment",
            method: 'post',
            data: {'id':row_id,'_token':_token,'invoice_id':invoice_id,'gateway':gateway,
            'description':description,'amount':amount, 'sys_job_type':sys_job_type},
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    $('#storage_payment').html(result.storage_payment_html);
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
    //Add new PAYMENT Line item
    $('body').on('click', '.save_s_payment_btn', function (e) {
    e.preventDefault();
    var row_id = $(this).data('row');
    var job_id = $('input[name="job_id"]').val();
    var invoice_id = $('input[name="storage_invoice_id"]').val();
    var _token = $('input[name="_token"]').val();
    var gateway = $('#s_payment_method_new_'+row_id).find(":selected").val();
    var description = $('#s_payment_description_new_'+row_id).val();
    var amount = $("#s_payment_total_new_field_"+row_id).val();
    var sys_job_type = "Moving_Storage";

    $.ajax({
        url: "/admin/moving/ajaxSavePayment",
        method: 'post',
        data: {'_token':_token,'job_id':job_id,'invoice_id':invoice_id,'gateway':gateway,
        'description':description,'amount':amount, 'sys_job_type':sys_job_type},
        dataType: "json",
        beforeSend: function () {
            $.blockUI();
        },
        complete: function () {
            $.unblockUI();
        },
        success: function (result) {
            if (result.error == 0) {
                $('#storage_payment').html(result.storage_payment_html);
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
    $("body").off('click', '.cancel_s_payment_btn').on('click', '.cancel_s_payment_btn', function(e) {
        var row_id = $(this).data('row');
        $("#s_payment_line_div_new_" + row_id).remove();
        $("#add_s_payment_line").toggle();

    });


            //Delete Payment
    $('body').on('click', '.delete_s_payment_btn', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var job_id = $('input[name="job_id"]').val();
        var _token = $('input[name="_token"]').val();
        var invoice_id = $('input[name="storage_invoice_id"]').val();
        var sys_job_type = "Moving_Storage";
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
                    data: { '_token': _token, 'job_id': job_id,'invoice_id':invoice_id, 'id': id, 'sys_job_type':sys_job_type },
                    dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    $('#storage_payment').html(result.storage_payment_html);
                    $('.daterange-single').daterangepicker({ 
                        singleDatePicker: true,
                        locale: {
                            format: 'DD/MM/YYYY'
                        }
                    });
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