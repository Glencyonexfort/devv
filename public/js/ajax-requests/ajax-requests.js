$(document).ready(function () {

    //START:: Create New Opportunity
    $('body').on('click', '#create_opp_btn', function (e) {
        e.preventDefault();
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxStoreOpportunity",
            method: 'post',
            data: $("#add_new_opp_form").serialize(),
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    // var lead_id = result.id;
                    // $('#add_new_opp_form').trigger("reset");
                    // $("#add_new_opp_form_grid").toggle(200);
                    // $('#opportunity_grid').html(result.opp_html);
                    // $("#OppCount").html(result.opp_count);
                    // $('.daterange-single').daterangepicker({ 
                    //     singleDatePicker: true,
                    //     locale: {
                    //         format: 'DD/MM/YYYY'
                    //     }
                    // });
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
                    location.reload();
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
    //END:: Create New Opportunity

    //START:: Edit Opportunity
    $('body').on('click', '.opportunity-edit-btn', function (e) {
        e.preventDefault();
        var oppid = $(this).data('oppid');
        $("#update_opp_form_grid_" + oppid).toggle(200);
    });
    $('body').on('click', '.update_opportunity_btn', function (e) {
        e.preventDefault();
        var oppid = $(this).data('oppid');
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxUpdateOpportunity",
            method: 'post',
            data: $("#update_opp_form_" + oppid).serialize(),
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {

                if (result.error == 0) {
                    // var lead_id = result.id;
                    // $("#update_opp_form_grid_" + oppid).toggle(200);
                    // $('#opportunity_grid').html(result.opp_html);
                    // $("#OppCount").html(result.opp_count);
                    // $('.daterange-single').daterangepicker({ 
                    //     singleDatePicker: true,
                    //     locale: {
                    //         format: 'DD/MM/YYYY'
                    //     }
                    // });
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
                    location.reload();
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
    //END:: Edit Opportunity

    //START:: Estimate Section

    //START:: Deposit Required
    $('body').on('click', '.deposit_edit_btn', function (e) {
        $("#deposit_label").hide();
        $("#deposit_value").show();
    });
    $('body').on('click', '#cancel_deposit_btn', function (e) {
        $("#deposit_value").hide();
        $("#deposit_label").show();
    });
    $('body').on('click', '#save_deposit_btn', function (e) {
        e.preventDefault();
        var deposit = 'Y';
        var deposit_required = $("#deposit_value_field").val();
        var quote_id = $('#estimateID').val();
        var crm_opportunity_id = $('#estimate_opp_id').find(":selected").val();
        var sys_job_type = $('#estimate_opp_id').find(":selected").data('type');
        var _token = $('input[name="_token"]').val();
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxSaveDepositRequired",
            method: 'post',
            data: {
                '_token': _token,
                'quote_id': quote_id,
                'deposit': deposit,
                'deposit_required': deposit_required,
                'crm_opportunity_id': crm_opportunity_id,
                'sys_job_type': sys_job_type
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
                    $('#estimate_table_grid').html(result.html);
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
    $('body').on('click', '#no_deposit_btn', function (e) {
        e.preventDefault();
        var deposit = 'N';
        var quote_id = $('#estimateID').val();
        var crm_opportunity_id = $('#estimate_opp_id').find(":selected").val();
        var sys_job_type = $('#estimate_opp_id').find(":selected").data('type');
        var _token = $('input[name="_token"]').val();
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxSaveDepositRequired",
            method: 'post',
            data: {
                '_token': _token,
                'quote_id': quote_id,
                'deposit': deposit,
                'crm_opportunity_id': crm_opportunity_id,
                'sys_job_type': sys_job_type
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
                    $('#estimate_table_grid').html(result.html);
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
    //END:: Deposit Required

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
        var quote_id = $('#estimateID').val();
        var crm_opportunity_id = $('#estimate_opp_id').find(":selected").val();
        var sys_job_type = $('#estimate_opp_id').find(":selected").data('type');
        var _token = $('input[name="_token"]').val();
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxSaveEstimateDiscount",
            method: 'post',
            data: {
                '_token': _token,
                'quote_id': quote_id,
                'discount': discount,
                'discount_type': discount_type,
                'crm_opportunity_id': crm_opportunity_id,
                'sys_job_type': sys_job_type
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
                    $('#estimate_table_grid').html(result.html);
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

    $("body").on('click', '#add_estimate_line', function (e) {
        $("#estimate_line_div_new").clone().insertAfter("tr.estimate_line_div:last").css("display", "table-row");
        var row_id = $("#estimate_line_div_new").data('row') + 1;
        $("#estimate_line_div_new").data('row', row_id);
        $("tr.estimate_line_div:last").attr('id', 'estimate_line_div_new_' + row_id);
        $("tr.estimate_line_div:last").attr('data-row', row_id);

        //edit line
        $("button.edit_estimate_btn:last").attr('data-row', row_id);
        $('input.prod_product_new:last').attr('data-row', row_id);
        //--
        $("select.prod_product_new:last").attr('data-row', row_id);
        $("select.prod_tax_new:last").attr('data-row', row_id);
        $("input.prod_price_new:last").attr('data-row', row_id);
        $("input.prod_qty_new:last").attr('data-row', row_id);
        $("input.prod_total_new_field:last").attr('data-row', row_id);
        $("button.cancel_estimate_btn:last").attr('data-row', row_id);
        $("button.save_estimate_btn:last").attr('data-row', row_id);

        $("select.prod_product_new:last").attr('id', 'prod_product_new_' + row_id);
        $("input.prod_product_new:last").attr('id', 'prod_product_new_' + row_id);
        $("form.estimate_form_new:last").attr('id', 'estimate_form_new_' + row_id);
        $("textarea.prod_description_new:last").attr('id', 'prod_description_new_' + row_id);
        $("input.prod_price_new:last").attr('id', 'prod_price_new_' + row_id);
        $("input.prod_qty_new:last").attr('id', 'prod_qty_new_' + row_id);
        $("select.prod_tax_new:last").attr('id', 'prod_tax_new_' + row_id);

        // $("div.prod_total_new:last").attr('id', 'prod_total_new_' + row_id);
        $("input.prod_total_new_field:last").attr('id', 'prod_total_new_field_' + row_id);
        $("input.prod_type_new_field:last").attr('id', 'prod_type_new_field_' + row_id);
        //hide add new line button
        $("#add_estimate_line").toggle();
    });
    $("body").off('change', '.prod_product_new').on('change', '.prod_product_new', function (e) {
        var row_id = $(this).data('row');
        var value = $(this).val();
        var desc = $('#products [value="' + value + '"]').data('desc');
        var price = $('#products [value="' + value + '"]').data('price');
        var tax = $('#products [value="' + value + '"]').data('tax');
        var type = $('#products [value="' + value + '"]').data('type');
        var pid = $('#products [value="' + value + '"]').data('pid');
        $(this).attr('data-pid', pid);
        $(this).attr('data-type', type);
        $(this).attr('data-tax', tax);
        $(this).attr('data-price', price);
        var lead_id = $('input[name="lead_id"]').val();
        var job_type = $("#op_job_type").val();
        var _token = $('input[name="_token"]').val();
        var job_id = $("#op_job_id").val();

        if (desc) {
            $.ajax({
                url: "/admin/crm/crm-leads/ajaxSetProductDescParameter",
                method: 'post',
                data: {
                    '_token': _token,
                    'lead_id': lead_id,
                    'description': desc,
                    'job_type': job_type,
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
                        $(".prod_description_new").val(result.desc);
                    }
                }
            });
        }

        // if(price)
        // {
        $("#prod_price_new_" + row_id).val(price);
        $("#prod_qty_new_" + row_id).val(1);
        if (isNaN(price)) {
            price = 0;
        }
        var total = parseFloat((price) * 1).toFixed(2);
        // $("#prod_total_new_" + row_id).html(total);
        $("#prod_type_new_field_" + row_id).val(type);
        $("#prod_total_new_field_" + row_id).val(total);
        $("#prod_tax_new_" + row_id + " option[value='" + tax + "']").attr('selected', 'selected').change();
        //}
    });
    $("body").off('change keyup paste', '.prod_price_new,.prod_tax_new,.prod_qty_new')
        .on('change keyup paste', '.prod_price_new,.prod_tax_new,.prod_qty_new', function (e) {

            var row_id = $(this).data('row');
            var tax = parseFloat($("#prod_tax_new_" + row_id).find(':selected').data('rate'));
            var qty = parseFloat($("#prod_qty_new_" + row_id).val());
            var price = parseFloat($("#prod_price_new_" + row_id).val());
            if (isNaN(price)) {
                price = 0;
            }
            if (isNaN(qty)) {
                qty = 0;
            }
            var total = parseFloat((price * qty) * (1 + tax / 100)).toFixed(2);
            // $("#prod_total_new_" + row_id).html(total);
            $("#prod_total_new_field_" + row_id).val(total);
        });
    $("body").off('change', '.prod_product_edit').on('change', '.prod_product_edit', function (e) {
        var row_id = $(this).data('row');
        var desc = $(this).find(':selected').data('desc');
        var price = $(this).find(':selected').data('price');
        var tax = $(this).find(':selected').data('tax');
        var type = $(this).find(':selected').data('type');
        if (desc) {
            $("#prod_description_edit_" + row_id).val(desc);
            $("#prod_price_edit_" + row_id).val(price);
            $("#prod_qty_edit_" + row_id).val(1);
            if (isNaN(price)) {
                price = 0;
            }
            var total = parseFloat((price) * 1).toFixed(2);
            // $("#prod_total_edit_" + row_id).html(total);
            $("#prod_type_edit_field_" + row_id).val(type);
            $("#prod_total_edit_field_" + row_id).val(total);
            $("#prod_tax_edit_" + row_id + " option[value='" + tax + "']").attr('selected', 'selected').change();
        }
    });
    //Edit estimate line item
    $("body").off('change keyup paste', '.prod_price_edit,.prod_tax_edit,.prod_qty_edit')
        .on('change keyup paste', '.prod_price_edit,.prod_tax_edit,.prod_qty_edit', function (e) {

            var row_id = $(this).data('row');
            var tax = parseFloat($("#prod_tax_edit_" + row_id).find(':selected').data('rate'));
            var qty = parseFloat($("#prod_qty_edit_" + row_id).val());
            var price = parseFloat($("#prod_price_edit_" + row_id).val());
            if (isNaN(price)) {
                price = 0;
            }
            if (isNaN(qty)) {
                qty = 0;
            }
            var total = parseFloat((price * qty) * (1 + tax / 100)).toFixed(2);
            // $("#prod_total_edit_" + row_id).html(total);
            $("#prod_total_edit_field_" + row_id).val(total);
        });
    //start::If user update line total
    //for edit line
    $("body").off('change keyup paste', '.prod_total_edit')
        .on('change keyup paste', '.prod_total_edit', function (e) {

            var row_id = $(this).data('row');
            var tax = parseFloat($("#prod_tax_edit_" + row_id).find(':selected').data('rate'));
            var qty = parseFloat($("#prod_qty_edit_" + row_id).val());
            var total = parseFloat($(this).val());
            if (isNaN(total)) {
                total = 0;
            }
            var unit_price = parseFloat((total / qty) / (1 + tax / 100)).toFixed(2);
            $("#prod_price_edit_" + row_id).val(unit_price);
        });
    //for new line
    $("body").off('change keyup paste', '.prod_total_new_field')
        .on('change keyup paste', '.prod_total_new_field', function (e) {

            var row_id = $(this).data('row');
            var tax = parseFloat($("#prod_tax_new_" + row_id).find(':selected').data('rate'));
            var qty = parseFloat($("#prod_qty_new_" + row_id).val());
            var total = parseFloat($(this).val());
            if (isNaN(total)) {
                total = 0;
            }

            var unit_price = parseFloat((total / qty) / (1 + tax / 100)).toFixed(2);

            $("#prod_price_new_" + row_id).val(unit_price);
        });
    //end::If user update line total

    $("body").off('click', '.cancel_update_estimate_btn').on('click', '.cancel_update_estimate_btn', function (e) {
        var id = $(this).data('id');
        $("#estimate_line_div_edit_" + id).css("display", "none");
        $("#estimate_line_div_view_" + id).css("display", "table-row");

    });
    $("body").off('click', '.edit_estimate_btn').on('click', '.edit_estimate_btn', function (e) {
        var id = $(this).data('id');
        $("#estimate_line_div_edit_" + id).css("display", "table-row");
        $("#estimate_line_div_view_" + id).css("display", "none");

    });
    $('body').on('click', '.update_estimate_btn', function (e) {
        e.preventDefault();
        var row_id = $(this).data('id');
        var estimateID = $('#estimateID').val();
        var _token = $('input[name="_token"]').val();
        var product_id = $('#prod_product_edit_' + row_id).find(":selected").data('pid');
        var name = $('#prod_product_edit_' + row_id).val();
        var crm_opportunity_id = $('#estimate_opp_id').find(":selected").val();
        var description = $('#prod_description_edit_' + row_id).val();
        var tax_id = $("#prod_tax_edit_" + row_id).find(':selected').val();
        var unit_price = $("#prod_price_edit_" + row_id).val();
        var quantity = $("#prod_qty_edit_" + row_id).val();
        var amount = $("#prod_total_edit_field_" + row_id).val();
        var type = $("#prod_type_edit_field_" + row_id).val();
        var sys_job_type = $('#estimate_opp_id').find(":selected").data('type');

        $.ajax({
            url: "/admin/crm/crm-leads/ajaxUpdateEstimate",
            method: 'post',
            data: {
                'id': row_id,
                '_token': _token,
                'name': name,
                'quote_id': estimateID,
                'product_id': product_id,
                'description': description,
                'tax_id': tax_id,
                'unit_price': unit_price,
                'quantity': quantity,
                'amount': amount,
                'type': type,
                'crm_opportunity_id': crm_opportunity_id,
                'sys_job_type': sys_job_type
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
                    $('#estimate_table_grid').html(result.html);
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
    //Add new Estimate Line item
    $("body").off('click', '.save_estimate_btn').on('click', '.save_estimate_btn', function (e) {
        // $('body').on('click', '.save_estimate_btn', function(e) {
        e.preventDefault();
        var row_id = $(this).data('row');
        var estimateID = $('#estimateID').val();
        var lead_id = $('input[name="lead_id"]').val();
        var _token = $('input[name="_token"]').val();
        var name = $('#prod_product_new_' + row_id).val();
        var product_id = $('#prod_product_new_' + row_id).data('pid');
        var crm_opportunity_id = $('#estimate_opp_id').find(":selected").val();
        var sys_job_type = $('#estimate_opp_id').find(":selected").data('type');
        var description = $('#prod_description_new_' + row_id).val();
        var tax_id = $("#prod_tax_new_" + row_id).find(':selected').val();
        var unit_price = $("#prod_price_new_" + row_id).val();
        var quantity = $("#prod_qty_new_" + row_id).val();
        var amount = $("#prod_total_new_field_" + row_id).val();
        var type = $("#prod_type_new_field_" + row_id).val();

        $.ajax({
            url: "/admin/crm/crm-leads/ajaxSaveEstimate",
            method: 'post',
            data: {
                '_token': _token,
                'lead_id': lead_id,
                'quote_id': estimateID,
                'product_id': product_id,
                'name': name,
                'description': description,
                'tax_id': tax_id,
                'unit_price': unit_price,
                'quantity': quantity,
                'amount': amount,
                'crm_opportunity_id': crm_opportunity_id,
                'sys_job_type': sys_job_type,
                'type': type
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
                    $('#estimate_table_grid').html(result.html);
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

    $("body").off('click', '.cancel_estimate_btn').on('click', '.cancel_estimate_btn', function (e) {
        var row_id = $(this).data('row');
        $("#estimate_line_div_new_" + row_id).remove();
        $("#add_estimate_line").toggle();

    });

    $('body').on('change', '#estimate_opp_id', function (e) {
        e.preventDefault();
        var crm_opportunity_id = $('#estimate_opp_id').find(":selected").val();
        var lead_id = $('input[name="lead_id"]').val();
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: "/admin/crm/crm-leads/ajaxLoadQuoteItem",
            method: 'post',
            data: { '_token': _token, 'crm_opportunity_id': crm_opportunity_id },
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    window.location = "/admin/crm/view-opportunity/" + lead_id + "/" + crm_opportunity_id + "#estimate_tab";
                    
                    /*$('#estimate_table_grid').html(result.html);

                    // Load Removal Tab
                    var seleced_opp = $('#removal_opp_id').find(':selected').val();
                    if(seleced_opp != crm_opportunity_id){
                        $("#removal_opp_id").val(crm_opportunity_id).trigger('change');
                    }

                    //Set current opportunity 
                    $(".opportunity_grid").removeClass("current_opportunity light-blue-bg");
                    $("#opportunity_grid_" + crm_opportunity_id).addClass("current_opportunity light-blue-bg");
                    //end*/
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

    $('body').on('change', '#removal_opp_id,#cleaning_opp_id', function (e) {
        e.preventDefault();
        var crm_opportunity_id = $(this).find(":selected").val();
        var lead_id = $('input[name="lead_id"]').val();
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: "/admin/crm/crm-leads/ajaxLoadJobDetail",
            method: 'post',
            data: { '_token': _token, 'crm_opportunity_id': crm_opportunity_id },
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    if (result.type == 'Moving') {
                        window.location = "/admin/crm/view-opportunity/" + lead_id + "/" + crm_opportunity_id + "#removal_tab";
                        $('#removal_table_grid').html(result.html);
                    } else if (result.type == 'Cleaning') {
                        $('#cleaning_table_grid').html(result.html);
                    }
                    /*
                    // Load Estimate Tab
                    var seleced_opp = $('#estimate_opp_id').find(':selected').val();
                    if(seleced_opp != crm_opportunity_id){
                        $("#estimate_opp_id").val(crm_opportunity_id).trigger('change');
                    }

                    //Set current opportunity 
                    $("#job_id").val($("#op_job_id").val());
                    $(".opportunity_grid").removeClass("current_opportunity light-blue-bg");
                    $("#opportunity_grid_" + crm_opportunity_id).addClass("current_opportunity light-blue-bg");
                    //end*/
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


    $('body').on('click', '.delete_estimate_btn', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var estimateID = $('#estimateID').val();
        var crm_opportunity_id = $('#estimate_opp_id').find(":selected").val();
        var lead_id = $('input[name="lead_id"]').val();
        var _token = $('input[name="_token"]').val();
        var sys_job_type = $('#estimate_opp_id').find(":selected").data('type');

        $.ajax({
            url: "/admin/crm/crm-leads/ajaxDestroyQuoteItem",
            method: 'post',
            data: { '_token': _token, 'quote_id': estimateID, 'lead_id': lead_id, 'id': id, 'crm_opportunity_id': crm_opportunity_id, 'sys_job_type': sys_job_type },
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    $('#estimate_table_grid').html(result.html);
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
    $('body').on('click', '.leadEstimateGenerateQuote', function (e) {
        e.preventDefault();
        var crm_opportunity_id = $('#estimate_opp_id').find(":selected").val();
        $.ajax({
            url: "/admin/crm/crm-leads/generateQuote/" + crm_opportunity_id,
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
                        text: result.message,
                        icon: 'error',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#fb9678',
                        textColor: 'white'
                    });
                    //..
                }
            },
        });
    });
    $('body').on('click', '.leadEstimateDownloadQuote', function (e) {
        e.preventDefault();
        var crm_opportunity_id = $('#estimate_opp_id').find(":selected").val();
        $.ajax({
            url: "/admin/crm/crm-leads/downloadQuote/" + crm_opportunity_id,
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
                        text: "Quote not yet genearated. Click on the 'Generate Quote' button to generate the quote.",
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
    //Generate Insurance quote
    $('body').on('click', '.leadEstimateGenerateInsurance', function (e) {
        e.preventDefault();
        var crm_opportunity_id = $('#estimate_opp_id').find(":selected").val();
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
                    $('.leadEstimateDownloadInsurance').removeAttr("disabled");
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
    $('body').on('click', '.leadEstimateDownloadInsurance', function (e) {
        e.preventDefault();
        var crm_opportunity_id = $('#estimate_opp_id').find(":selected").val();
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
    //END:: Estimate Section

    //START:: Removals Section
    $('body').on('click', '.show_update_booking_detail_btn', function (e) {
        e.preventDefault();
        $('#update_booking_detail_view').toggle();
        $('#update_booking_detail_form').toggle();
    });

    $('body').on('click', '.show_update_property_detail_btn', function (e) {
        e.preventDefault();
        $('#update_property_detail_view').toggle();
        $('#update_property_detail_form').toggle();
    });
    $('body').on('click', '.show_update_movingfrom_btn', function (e) {
        e.preventDefault();
        $('#update_movingfrom_view').toggle();
        $('#update_movingfrom_form').toggle();
    });

    $('body').on('click', '.show_update_movingto_btn', function (e) {
        e.preventDefault();
        $('#update_movingto_view').toggle();
        $('#update_movingto_form').toggle();
    });

    $('body').on('click', '#update_booking_detail_btn', function (e) {
        e.preventDefault();
        var op_job_type = $("#op_job_type").val();
        if (op_job_type == "Moving") {
            var id = $("#removal_opp_id").find(':selected').val();
        } else if (op_job_type == "Cleaning") {
            var id = $("#cleaning_opp_id").find(':selected').val();
        }
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxUpdateRemovalBookingDetail",
            method: 'post',
            data: $("#booking_detail_form").serialize() + '&opp_id=' + id,
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {

                if (result.error == 0) {
                    $('#removal_booking_detail_grid').html(result.res_html);
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

    $('body').on('click', '#update_property_detail_btn', function (e) {
        e.preventDefault();
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxUpdateRemovalPropertyDetail",
            method: 'post',
            data: $("#property_detail_form").serialize(),
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {

                if (result.error == 0) {
                    $('#removal_property_detail_grid').html(result.res_html);
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

    $("body").off('click', '.update_movingfrom_btn').on('click', '#update_movingfrom_btn', function (e) {
        e.preventDefault();
        var op_job_type = $("#op_job_type").val();
        if (op_job_type == "Moving") {
            var id = $("#removal_opp_id").find(':selected').val();
        } else if (op_job_type == "Cleaning") {
            var id = $("#cleaning_opp_id").find(':selected').val();
        }
        $("#removal_opp_id_hidden_field").val(id);
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxUpdateRemovalMovingFrom",
            method: 'post',
            data: $("#movingfrom_form").serialize(),
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {

                if (result.error == 0) {
                    $('#moving_from_grid').html(result.res_html);
                    $('#update_movingfrom_form').toggle();
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

    $('body').on('click', '#update_movingto_btn', function (e) {
        e.preventDefault();
        var op_job_type = $("#op_job_type").val();
        if (op_job_type == "Moving") {
            var id = $("#removal_opp_id").find(':selected').val();
        } else if (op_job_type == "Cleaning") {
            var id = $("#cleaning_opp_id").find(':selected').val();
        }
        $("#removal_opp_id").val(id);
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxUpdateRemovalMovingTo",
            method: 'post',
            data: $("#movingto_form").serialize(),
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {

                if (result.error == 0) {
                    $('#moving_to_grid').html(result.res_html);
                    $('#update_movingto_form').toggle();
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
    //END:: Removal Section

    //END:: Cleaning Section

    $('body').on('click', '.show_update_booking_detail_cleaning_btn', function (e) {
        e.preventDefault();
        $('#update_booking_detail_cleaning_view').toggle();
        $('#update_booking_detail_cleaning_form').toggle();
    });

    $('body').on('click', '.show_update_end_of_lease_btn', function (e) {
        e.preventDefault();
        $('#update_end_of_lease_view').toggle();
        $('#update_end_of_lease_form').toggle();
    });

    $('body').on('click', '.show_update_questions_btn', function (e) {
        e.preventDefault();
        $('#update_questions_view').toggle();
        $('#update_questions_form').toggle();
    });

    $('body').on('click', '#update_booking_detail_cleaning_btn', function (e) {
        e.preventDefault();
        var op_job_type = $("#op_job_type").val();
        if (op_job_type == "Moving") {
            var id = $("#removal_opp_id").find(':selected').val();
        } else if (op_job_type == "Cleaning") {
            var id = $("#cleaning_opp_id").find(':selected').val();
        }
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxUpdateRemovalBookingDetail",
            method: 'post',
            data: $("#booking_detail_cleaning_form").serialize() + '&opp_id=' + id,
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {

                if (result.error == 0) {
                    $('#cleaning_booking_detail_grid').html(result.res_html);
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

    $('body').on('click', '#update_end_of_lease_btn', function (e) {
        e.preventDefault();
        var oppid = $(this).data('oppid');
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxUpdateCleaningEndOfLease",
            method: 'post',
            data: $("#end_of_lease_form").serialize(),
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {

                if (result.error == 0) {
                    $('#end_of_lease_detail_grid').html(result.res_html);
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

    $('body').on('click', '#update_questions_btn', function (e) {
        e.preventDefault();
        var oppid = $(this).data('oppid');
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxUpdateCleaningQuestion",
            method: 'post',
            data: $("#questions_form").serialize(),
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {

                if (result.error == 0) {
                    $('#questions_grid').html(result.res_html);
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

    $('body').on('click', '.removal-confirm-booking', function () {
        var op_job_type = $("#op_job_type").val();
        if (op_job_type == "Moving") {
            var id = $("#removal_opp_id").find(':selected').val();
        } else if (op_job_type == "Cleaning") {
            var id = $("#cleaning_opp_id").find(':selected').val();
        }
        var leadid = $(this).data('leadid');
        var token = $(this).data('token');
        var job_id = $("#op_job_id").val();
        swal({
            title: "Are you sure?",
            text: "You want to confirm this opportunity as a booking?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#00c292",
            confirmButtonText: "Yes, Confirm!",
            cancelButtonText: "No, Cancel!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: "/admin/crm/crm-leads/ajaxRemovalsConfirmBooking",
                    method: 'post',
                    data: { '_token': token, 'opp_id': id, 'lead_id': leadid },
                    dataType: "json",
                    beforeSend: function () {
                        $.blockUI();
                    },
                    complete: function () {
                        $.unblockUI();
                    },
                    success: function (result) {
                        if (result.error == 2) {
                            swal({
                                title: "Info",
                                text: result.message,
                                type: "info",
                                button: "OK",
                            });
                        } else if (result.error == 0) {
                            $('.media .icon-pen').hide();
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
                            //Redirect too Job Page
                            window.location = "/admin/moving/view-job/" + job_id;
                        }
                    }
                });
            }
        });
    });
    //END:: Cleaning Section

}); // ready function end