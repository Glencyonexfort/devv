$(document).ready(function() {    
    //START:: Storage Reservation Section    

    $('body').on('click', '#storage_tab_btn', function(e) {
        e.preventDefault();
        var crm_opportunity_id = $('#estimate_opp_id').find(":selected").val();
        var _token = $('input[name="_token"]').val();
        $.ajax({
            url: "/admin/crm/crm-leads/storageTabContent",
            method: 'post',
            data: { '_token': _token, 'crm_opportunity_id': crm_opportunity_id},
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
                    $('#storage_estimate').html(result.storage_estimate_html);
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
                    // console.log(result.data);
                    // console.log(len);
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
        var _token = $('input[name="_token"]').val();
        var unit_id = $('#storage_unit_new').find(":selected").val();
        var crm_opportunity_id = $('#estimate_opp_id').find(":selected").val();
        var from_date = $('#storage_unit_start_date_new').val();
        var to_date = $("#storage_unit_end_date_new").val();
        var allocation_status = "Reserved";

        $.ajax({
            url: "/admin/storage/ajaxSaveStorageReservation",
            method: 'post',
            data: {
                '_token': _token,
                'lead_id': lead_id,
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
            text: "You sure you want to delete this reservation?",
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

    //START:: Storage Estimate Section    

    $("body").on('click', '#add_s_estimate_line', function(e) {
        $("#s_estimate_line_div_new").clone().insertAfter("tr.s_estimate_line_div:last").css("display", "table-row");
        var row_id = $("#s_estimate_line_div_new").data('row') + 1;
        $("#s_estimate_line_div_new").data('row', row_id);
        $("tr.s_estimate_line_div:last").attr('id', 's_estimate_line_div_new_' + row_id);
        $("tr.s_estimate_line_div:last").attr('data-row', row_id);

        //edit line
        $("button.edit_s_estimate_btn:last").attr('data-row', row_id);
        //--
        $("select.s_product_new:last").attr('data-row', row_id);
        $("select.s_prod_tax_new:last").attr('data-row', row_id);
        $("input.s_prod_price_new:last").attr('data-row', row_id);
        $("input.s_prod_qty_new:last").attr('data-row', row_id);
        $("input.s_prod_total_new_field:last").attr('data-row', row_id);
        $("button.cancel_s_estimate_btn:last").attr('data-row', row_id);
        $("button.save_s_estimate_btn:last").attr('data-row', row_id);

        $("select.s_product_new:last").attr('id', 's_product_new_' + row_id);
        $("form.s_estimate_form_new:last").attr('id', 's_estimate_form_new_' + row_id);
        $("textarea.s_prod_description_new:last").attr('id', 's_prod_description_new_' + row_id);
        $("input.s_prod_price_new:last").attr('id', 's_prod_price_new_' + row_id);
        $("input.s_prod_qty_new:last").attr('id', 's_prod_qty_new_' + row_id);
        $("select.s_prod_tax_new:last").attr('id', 's_prod_tax_new_' + row_id);

        // $("div.prod_total_new:last").attr('id', 'prod_total_new_' + row_id);
        $("input.s_prod_total_new_field:last").attr('id', 's_prod_total_new_field_' + row_id);
        $("input.s_prod_type_new_field:last").attr('id', 's_prod_type_new_field_' + row_id);
        //hide add new line button
        $("#add_s_estimate_line").toggle();
    });
    $("body").off('change', '.s_product_new').on('change', '.s_product_new', function(e) {
        var row_id = $(this).data('row');
        var desc = $(this).find(':selected').data('desc');
        var price = $(this).find(':selected').data('price');
        var tax = $(this).find(':selected').data('tax');
        var type = $(this).find(':selected').data('type');
        var lead_id = $('input[name="lead_id"]').val();
        var job_type = $("#op_job_type").val();
        var _token = $('input[name="_token"]').val();
        var job_id = $("#op_job_id").val();

        $.ajax({
            url: "/admin/crm/crm-leads/ajaxSetProductDescParameter",
            method: 'post',
            data: {
                '_token': _token,
                'lead_id': lead_id,
                'description': desc,
                'job_type': job_type,
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
                $("#s_prod_description_new_" + row_id).val(result.desc);
            }
        });
        
        $("#s_prod_price_new_" + row_id).val(price);
        $("#s_prod_qty_new_" + row_id).val(1);         
        if (isNaN(price)) {
            price = 0;
        }
        var total = parseFloat((price) * 1).toFixed(2);
        // $("#prod_total_new_" + row_id).html(total);
        $("#s_prod_type_new_field_" + row_id).val(type);
        $("#s_prod_total_new_field_" + row_id).val(total);
        $("#s_prod_tax_new_"+row_id+" option[value='"+tax+"']").attr('selected','selected').change();
    });
    $("body").off('change keyup paste', '.s_prod_price_new,.s_prod_tax_new,.s_prod_qty_new')
        .on('change keyup paste', '.s_prod_price_new,.s_prod_tax_new,.s_prod_qty_new', function(e) {

            var row_id = $(this).data('row');
            var tax = parseFloat($("#s_prod_tax_new_" + row_id).find(':selected').data('rate'));
            var qty = parseFloat($("#s_prod_qty_new_" + row_id).val());
            var price = parseFloat($("#s_prod_price_new_" + row_id).val());
            if (isNaN(price)) {
                price = 0;
            }
            if (isNaN(qty)) {
                qty = 0;
            }
            var total = parseFloat((price*qty)*(1 + tax/100)).toFixed(2);
            // $("#prod_total_new_" + row_id).html(total);
            $("#s_prod_total_new_field_" + row_id).val(total);
        });
    $("body").off('change', '.s_prod_product_edit').on('change', '.s_prod_product_edit', function(e) {
        var row_id = $(this).data('row');
        var desc = $(this).find(':selected').data('desc');
        var price = $(this).find(':selected').data('price');
        var tax = $(this).find(':selected').data('tax');
        var type = $(this).find(':selected').data('type');
        $("#s_prod_description_edit_" + row_id).val(desc);
        $("#s_prod_price_edit_" + row_id).val(price);
        $("#s_prod_qty_edit_" + row_id).val(1);
        if (isNaN(price)) {
            price = 0;
        }
        var total = parseFloat((price) * 1).toFixed(2);
        // $("#prod_total_edit_" + row_id).html(total);
        $("#s_prod_type_edit_field_" + row_id).val(type);
        $("#s_prod_total_edit_field_" + row_id).val(total);
        $("#s_prod_tax_edit_"+row_id+" option[value='"+tax+"']").attr('selected','selected').change();
    });
    //Edit estimate line item
    $("body").off('change keyup paste', '.s_prod_price_edit,.s_prod_tax_edit,.s_prod_qty_edit')
        .on('change keyup paste', '.s_prod_price_edit,.s_prod_tax_edit,.s_prod_qty_edit', function(e) {

            var row_id = $(this).data('row');
            var tax = parseFloat($("#s_prod_tax_edit_" + row_id).find(':selected').data('rate'));
            var qty = parseFloat($("#s_prod_qty_edit_" + row_id).val());
            var price = parseFloat($("#s_prod_price_edit_" + row_id).val());
            if (isNaN(price)) {
                price = 0;
            }
            if (isNaN(qty)) {
                qty = 0;
            }
            var total = parseFloat((price*qty)*(1 + tax/100)).toFixed(2);
            // $("#prod_total_edit_" + row_id).html(total);
            $("#s_prod_total_edit_field_" + row_id).val(total);
    });
    //start::If user update line total
    //for edit line
    $("body").off('change keyup paste', '.s_prod_total_edit')
        .on('change keyup paste', '.s_prod_total_edit', function(e) {

            var row_id = $(this).data('row');
            var tax = parseFloat($("#s_prod_tax_edit_" + row_id).find(':selected').data('rate'));
            var qty = parseFloat($("#s_prod_qty_edit_" + row_id).val());
            var total = parseFloat($(this).val());
            if (isNaN(total)) {
                total = 0;
            }
            var unit_price = parseFloat((total/qty)/(1 + tax/100)).toFixed(2);
            $("#s_prod_price_edit_" + row_id).val(unit_price);
    });
    //for new line
    $("body").off('change keyup paste', '.s_prod_total_new_field')
        .on('change keyup paste', '.s_prod_total_new_field', function(e) {

            var row_id = $(this).data('row');
            var tax = parseFloat($("#s_prod_tax_new_"+row_id).find(':selected').data('rate'));
            var qty = parseFloat($("#s_prod_qty_new_"+row_id).val());
            var total = parseFloat($(this).val());
            if (isNaN(total)) {
                total = 0;
            }
            
            var unit_price = parseFloat((total/qty)/(1 + tax/100)).toFixed(2);

            $("#s_prod_price_new_"+row_id).val(unit_price);
    });
    //end::If user update line total

    $("body").off('click', '.cancel_update_s_estimate_btn').on('click', '.cancel_update_s_estimate_btn', function(e) {
        var id = $(this).data('id');
        $("#s_estimate_line_div_edit_" + id).css("display", "none");
        $("#s_estimate_line_div_view_" + id).css("display", "table-row");

    });
    $("body").off('click', '.edit_s_estimate_btn').on('click', '.edit_s_estimate_btn', function(e) {
        var id = $(this).data('id');
        $("#s_estimate_line_div_edit_" + id).css("display", "table-row");
        $("#s_estimate_line_div_view_" + id).css("display", "none");

    });
    $('body').on('click', '.update_s_estimate_btn', function(e) {
        e.preventDefault();
        var row_id = $(this).data('id');
        var estimateID = $('#Storage_estimateID').val();
        var _token = $('input[name="_token"]').val();
        var product_id = $('#s_product_edit_' + row_id).find(":selected").data('pid');
        var name = $('#s_product_edit_' + row_id).find(":selected").val();
        var crm_opportunity_id = $('#estimate_opp_id').find(":selected").val();
        var description = $('#s_prod_description_edit_' + row_id).val();
        var tax_id = $("#s_prod_tax_edit_" + row_id).find(':selected').val();
        var unit_price = $("#s_prod_price_edit_" + row_id).val();
        var quantity = $("#s_prod_qty_edit_" + row_id).val();
        var amount = $("#s_prod_total_edit_field_" + row_id).val();
        var type = $("#s_prod_type_edit_field_" + row_id).val();

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
                    $('#storage_estimate').html(result.html);
                    //Notification....
                    $.toast({
                        heading: 'Updated',
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
    $('body').on('click', '.save_s_estimate_btn', function(e) {
        e.preventDefault();
        var row_id = $(this).data('row');
        var estimateID = $('#Storage_estimateID').val();
        var lead_id = $('input[name="lead_id"]').val();
        var _token = $('input[name="_token"]').val();
        var name = $('#s_product_new_' + row_id).find(":selected").val();
        var product_id = $('#s_product_new_' + row_id).find(":selected").data('pid');
        var crm_opportunity_id = $('#estimate_opp_id').find(":selected").val();
        var sys_job_type = "Moving_Storage";
        var description = $('#s_prod_description_new_' + row_id).val();
        var tax_id = $("#s_prod_tax_new_" + row_id).find(':selected').val();
        var unit_price = $("#s_prod_price_new_" + row_id).val();
        var quantity = $("#s_prod_qty_new_" + row_id).val();
        var amount = $("#s_prod_total_new_field_" + row_id).val();
        var type = $("#s_prod_type_new_field_" + row_id).val();

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
            beforeSend: function() {
                $.blockUI();
            },
            complete: function() {
                $.unblockUI();
            },
            success: function(result) {
                if (result.error == 0) {
                    $('#storage_estimate').html(result.html);
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

    $("body").off('click', '.cancel_s_estimate_btn').on('click', '.cancel_s_estimate_btn', function(e) {
        var row_id = $(this).data('row');
        $("#s_estimate_line_div_new_" + row_id).remove();
        $("#add_s_estimate_line").toggle();

    });    

    //START:Discount 
    $('body').on('click', '.storage_discount_type_option', function(e) {
        var type = $(this).data('val');
        $("#storage_discount_type_label").html(" - "+type);
        $("#storage_discount_type_field").val(type);
        $("#storage_discount_label").hide();
        $("#storage_discount_value").show();
    });

    $('body').on('click', '#cancel_storage_discount_btn', function(e) {
        $("#storage_discount_value").hide();
        $("#storage_discount_label").show();
    });        

    $('body').on('click', '#save_storage_discount_btn', function(e) {
        e.preventDefault();
        var discount = $("#storage_discount_value_field").val();         
        var discount_type = $("#storage_discount_type_field").val();
        var quote_id = $('#Storage_estimateID').val();
        var _token = $('input[name="_token"]').val();
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxSaveEstimateDiscount",
            method: 'post',
            data: {
                '_token': _token,
                'quote_id': quote_id,
                'discount': discount,
                'discount_type': discount_type
            },
            dataType: "json",
            beforeSend: function() {
                $(".preloader").show();
            },
            complete: function() {
                $(".preloader").hide();
            },
            success: function(result) {
                if (result.error == 0) {
                    $('#storage_estimate').html(result.html);
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

    $('body').on('click', '.delete_s_estimate_btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var estimateID = $('#Storage_estimateID').val();
        var crm_opportunity_id = $('#estimate_opp_id').find(":selected").val();
        var lead_id = $('input[name="lead_id"]').val();
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: "/admin/crm/crm-leads/ajaxDestroyQuoteItem",
            method: 'post',
            data: { '_token': _token,'quote_id': estimateID, 'lead_id': lead_id, 'id': id, 'crm_opportunity_id': crm_opportunity_id },
            dataType: "json",
            beforeSend: function() {
                $.blockUI();
            },
            complete: function() {
                $.unblockUI();
            },
            success: function(result) {
                if (result.error == 0) {
                    $('#storage_estimate').html(result.html);
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

    //END:: Storage Estimate Section

}); // ready function end