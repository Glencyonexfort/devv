$(document).ready(function () {
    //START:: material_return Section
    $("body").on('click', '#add_material_return_line', function (e) {
        $("#material_return_line_div_new").clone().insertAfter("tr.material_return_line_div:last").css("display", "table-row");
        var row_id = $("#material_return_line_div_new").data('row') + 1;
        $("#material_return_line_div_new").data('row', row_id);
        $("tr.material_return_line_div:last").attr('id', 'material_return_line_div_new_' + row_id);
        $("tr.material_return_line_div:last").attr('data-row', row_id);

        //edit line
        $("button.edit_material_return_btn:last").attr('data-row', row_id);
        //--
        $("select.material_return_new:last").attr('data-row', row_id);
        $("textarea.material_return_description_new:last").attr('id', 'material_return_description_new_' + row_id);
        $("input.material_return_qty_new:last").attr('data-row', row_id);
        $("button.cancel_material_return_btn:last").attr('data-row', row_id);
        $("button.save_material_return_btn:last").attr('data-row', row_id);
        $("select.material_return_new:last").attr('id', 'material_return_new_' + row_id);
        $("input.material_return_qty_new:last").attr('id', 'material_return_qty_new_' + row_id);
        $("input.material_return_type_new:last").attr('id', 'material_return_type_new_' + row_id);
        //hide add new line button
        $("#add_material_return_line").toggle();
        n =  new Date();
        y = n.getFullYear();
        m = n.getMonth() + 1;
        d = n.getDate();
        $('.new_date').val('');
        $('.return_date').html(m + "/" + d + "/" + y);
    });

    //Edit material_return line item

    $("body").off('change', '.material_return_new').on('change', '.material_return_new', function(e) {
        var row_id = $(this).data('row');
        var desc = $(this).find(':selected').data('desc');
        var type = $(this).find(':selected').data('type');        
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
                $("#material_return_description_new_" + row_id).val(result.desc);
            }
        });
        $("#material_return_qty_new_" + row_id).val(1);
        //---end--->        
    });

    $("body").off('change', '.material_return_product_edit').on('change', '.material_return_product_edit', function(e) {
        var row_id = $(this).data('row');
        var desc = $(this).find(':selected').data('desc');
        var type = $(this).find(':selected').data('type');        
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
                $("#material_return_description_edit_" + row_id).val(result.desc);
            }
        });
        //---end--->        
    });


    $("body").off('click', '.cancel_update_material_return_btn').on('click', '.cancel_update_material_return_btn', function (e) {
        var id = $(this).data('id');
        $("#material_return_line_div_edit_" + id).css("display", "none");
        $("#material_return_line_div_view_" + id).css("display", "table-row");

    });

    $("body").off('click', '.edit_material_return_btn').on('click', '.edit_material_return_btn', function (e) {
        var id = $(this).data('id');
        $("#material_return_line_div_edit_" + id).css("display", "table-row");
        $("#material_return_line_div_view_" + id).css("display", "none");
    });

    $('body').on('click', '.update_material_return_btn', function (e) {
        e.preventDefault();
        var row_id = $(this).data('id');
        var job_id = $('input[name="job_id"]').val();
        var _token = $('input[name="_token"]').val();
        var product_id = $('#material_return_product_edit_' + row_id).find(":selected").data('pid');
        var quantity = $('#material_return_qty_edit_' + row_id).val();
        var type = $('#material_return_product_edit_' + row_id).find(":selected").data('product_type');
        var lead_id = $('#lead_id').val();
        $.ajax({
            url: "/admin/moving/ajaxUpdateMaterialReturn",
            method: 'PUT',
            data: {
                'id': row_id,
                '_token': _token,
                'product_id': product_id,
                'job_id': job_id,
                'quantity': quantity,
                'type': type,
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
                if (result.error == 0) {
                    $('#material_return_table_grid').html(result.html);
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

    //Add new material_return Line item
    $('body').on('click', '.save_material_return_btn', function (e) {
        e.preventDefault();
        var row_id = $(this).data('row');
        var job_id = $('input[name="job_id"]').val();
        var _token = $('input[name="_token"]').val();
        var product_id = $('#material_return_new_' + row_id).find(":selected").data('pid');
        var quantity = $('#material_return_qty_new_' + row_id).val();
        var type = $('#material_return_new_' + row_id).find(":selected").data('product_type');
        var lead_id = $('#lead_id').val();
        $.ajax({
            url: "/admin/moving/ajaxSaveMaterialReturn",
            method: 'post',
            data: {
                '_token': _token,
                'job_id': job_id,
                'product_id': product_id,
                'quantity': quantity,
                'type': type,
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
                if (result.error == 0) {
                    $('#material_return_table_grid').html(result.html);

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

    $('body').on('click', '.delete_material_return_btn', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var job_id = $('input[name="job_id"]').val();
        var _token = $('input[name="_token"]').val();
        var lead_id = $('#lead_id').val();
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted Material Return item!",
            icon: "warning",
            buttons: true
        }).then((isConfirm) => {
            if (isConfirm) {
                $.ajax({
                    url: "/admin/moving/ajaxDestroyMaterialReturn",
                    method: 'post',
                    data: { 
                        '_token': _token, 
                        'job_id': job_id, 
                        'id': id,
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
                        if (result.error == 0) {
                            $('#material_return_table_grid').html(result.html);
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

    $("body").off('click', '.cancel_material_return_btn').on('click', '.cancel_material_return_btn', function (e) {
        var row_id = $(this).data('row');
        $("#material_return_line_div_new_" + row_id).remove();
        $("#add_material_return_line").toggle();

    });

}); // ready function end