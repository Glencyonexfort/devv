$(document).ready(function () {
    //START:: Trip Assign Jobs Section
    $("body").on('click', '.trip-update-btn', function (e) {
        $('#update_side_trip_form_grid').removeClass('hidden');
    });

    $("body").on('click', '.cancel-trip-update-btn', function (e) {
        $('#update_side_trip_form_grid').addClass('hidden');
    });

    $("body").on('click', '.update_trip_btn', function (e) {
        $.ajax({
            url: "/admin/moving-settings/update-trip",
            method: 'POST',
            data: $('#update_trip_form').serialize(),
            dataType: "json",
            beforeSend: function() {
                $(".preloader").show();
            },
            complete: function() {
                $(".preloader").hide();
            },
            success: function(result) {
                if (result.error == 0) {
                    $('#trip_side_grid').html(result.html);
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

    $("body").off('click', '.trip-unassign-job').on('click', '.trip-unassign-job', function (e) {
        var trip_id = $(this).data('trip_id');
        var job_id = $(this).data('job_id');
        var leg_id = $(this).data('leg_id');
        var _token = $('input[name="_token"]').val();

        swal({
            title: "Are you sure?",
            text: "You Want to Unassign This Job!",
            icon: "warning",
            buttons: true
        }).then((isConfirm) => {
            if (isConfirm) {
                $.ajax({
                    url: "/admin/moving-settings/trip-unassign-job",
                    method: 'POST',
                    data: {
                        '_token': _token,
                        'trip_id': trip_id,
                        'job_id': job_id,
                        'leg_id': leg_id
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
                            loadTableJobs();
                            loadTableAssignJobs();
                            $('#barData > span').text(result.barData['sum']+'%');
                            $('div#barData').width(''+result.barData['sum']+'%');
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

    $("body").off('click', '.trip-assign-job').on('click', '.trip-assign-job', function (e) {
        var trip_id = $(this).data('trip_id');
        var job_id = $(this).data('job_id');
        var leg_id = $(this).data('leg_id');
        var _token = $('input[name="_token"]').val();

        swal({
            title: "Are you sure?",
            text: "You want to Assign This Job",
            icon: "warning",
            buttons: true
        }).then((isConfirm) => {
            if (isConfirm) {
                $.ajax({
                    url: "/admin/moving-settings/trip-assign-job",
                    method: 'POST',
                    data: {
                        '_token': _token,
                        'trip_id': trip_id,
                        'job_id': job_id,
                        'leg_id': leg_id
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
                            loadTableJobs();
                            loadTableAssignJobs();
                            $('#barData > span').text(result.barData['sum']+'%');
                            $('div#barData').width(''+result.barData['sum']+'%');
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

    $("body").off('click', '#apply-job-filters').on('click', '#apply-job-filters', function (e) {          
        e.preventDefault();
        var trip_id = $('#trip_id').val();
        var created_date_start = $('#created_date_start_job').val();
        var created_date_end = $('#created_date_end_job').val();

        if($('#created_date_start_job').val())
        {
            table = $('#listing-table-assign-jobs').dataTable({
                "pageLength": 5,
                searching: false,
                paging: true,
                info: true,
                bLengthChange: false,
                destroy: true,
                //                                    responsive: true,
                processing: true,
                order: [], //Initial no order.
                aaSorting: [],
                serverSide: true,
                scrollX: true,
                ajax: '/admin/moving-settings/get-search-jobs?trip_id=' + trip_id + '&created_date_start=' + created_date_start + '&created_date_end=' + created_date_end,
                // language: {
                //     "url": "<?php echo __("app.datatable") ?>"
                // },
                "fnDrawCallback": function(oSettings) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
    
                    var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                    };
    
                    var count = $('#listing-table-assign-jobs').DataTable().page.info().recordsTotal;
                    $('#totalCount').html(count);
    
                    var sum = $('#listing-table-assign-jobs').DataTable().column(1).data()
                    .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                    }, 0 );
                    sum = sum.toFixed(2);
                    var nf = new Intl.NumberFormat();
                    //$('#totalVal').html(nf.format(sum));
                },
                columns: [
                    {
                        data: 'job_number',
                        name: 'job_number',
                        width: '5%'
                    },
                    {
                        data: 'leg_number',
                        name: 'leg_number',
                        width: '5%'
                    },
                    {
                        data: 'name',
                        name: 'name',
                        width: '15%'
                    },
                    {
                        data: 'cbm',
                        name: 'cbm',
                        width: '5%'
                    },
                    {
                        data: 'job_date',
                        name: 'job_date',
                        width: '15%'
                    },
                    {
                        data: 'pickup_suburb',
                        name: 'pickup_suburb',
                        width: '15%'
                    },
                    {
                        data: 'drop_off_suburb',
                        name: 'drop_off_suburb',
                        width: '15%'
                    },
                    {
                        data: 'job_status',
                        name: 'job_status',
                        width: '10%'
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status',
                        width: '10%'
                    },
                    { 
                        data: 'action',
                        name: 'action', 
                        width: '5%' 
                    },
                ]
            });
        }
    });
        
    $('#reset-job-filters').click(function() {
        $('#filter-form')[0].reset();
        $('#user_id').val('').selectpicker('refresh');
        $("#tableData tr td").detach();
        location.reload(true);
        // loadTable();
    });

    $('body').on('click', '#generate_waybill', function(e) {
        e.preventDefault();
        var trip_id = $(this).data("trip_id");

        $.ajax({
            url: "/admin/moving-settings/generate-waybill/" + trip_id,
            method: 'GET',
            dataType: "json",
            beforeSend: function() {
                $.blockUI();
            },
            complete: function() {
                $.unblockUI();
            },
            success: function(result) {
                if (result.error == 0) {
                    $('#download_waybill').removeAttr("disabled");
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

    $('body').on('click', '#download_waybill', function(e) {
        e.preventDefault();
        var trip_id = $(this).data("trip_id");
        $.ajax({
            url: "/admin/moving-settings/download-waybill/" + trip_id,
            method: 'GET',
            dataType: "json",
            beforeSend: function() {
                $.blockUI();
            },
            complete: function() {
                $.unblockUI();
            },
            success: function(result) {
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

}); // ready function end