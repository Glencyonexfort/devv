@extends('layouts.app')

@section('page-title')
<!-- Page header and Breadcrumb -->
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="{{ $pageIcon }}"></i> <span class="font-weight-semibold"> {{ $pageTitle }}</span></h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
</div>
<!-- /page header and Breadcrumb-->
@endsection

@push('head-script')
<style type="text/css">
    #listing-table thead {
        visibility: hidden !important;
    }

    #listing-table2 thead {
        visibility: hidden !important;
    }
</style>
@endpush

@section('content')
<div class="content">
    <div class="d-md-flex align-items-md-start">
        @include('sections.admin_moving_setting_menu')
        <div class="card" style="width: 100%;">
            <div class="card-header bg-white header-elements-inline">
                <h6 class="card-title">
                    Lead Statuses
                    <p class="font-12 font-weight-normal">Lead Statuses represent a Lead's current relation to your company.</p>
                </h6>
                <div>
                    <a data-toggle="modal" data-target="#add_new_lead_status" href="#" class="btn btn-success btn-sm pull-right hide"><i class="fa fa-plus" aria-hidden="true"></i> @lang('modules.statuses.add')</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-borderless table-responsive" id="listing-table" style="font-size:13px;width:100%!important;">
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-header bg-white header-elements-inline">
                <h6 class="card-title">
                    Opportunity Pipeline Statuses
                    <p class="font-12 font-weight-normal">Opportunity Statuses describe each stage of deal in the sales process.</p>
                </h6>
                <div>
                    <a data-toggle="modal" data-target="#add_new_pipeline_status" href="#" class="btn btn-success btn-sm pull-right hide"><i class="fa fa-plus" aria-hidden="true"></i> @lang('modules.pipeline_statuses.add')</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-borderless table-responsive" id="listing-table2" style="font-size:13px;width:100%!important;">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="add_new_lead_status" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            @include('admin.statuses.lead.create')
        </div>
    </div>
</div>
<div id="edit_lead_status" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
<div id="delete_lead_status" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
<div id="add_new_pipeline_status" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            @include('admin.statuses.pipeline.create')
        </div>
    </div>
</div>
<div id="edit_pipeline_status" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
<div id="delete_pipeline_status" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
<script>
    $(window).on('load', function() {
        $("#listing-table_wrapper").removeClass("form-inline");
    });
</script>
@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('newassets/global_assets/js/plugins/tables/datatables/extensions/row_reorder.min.js') }}"></script>
<!-- <script src="{{ asset('newassets/global_assets/js/demo_pages/datatables_extension_row_reorder.js') }}"></script> -->

<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
@if($global->locale == 'en')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.{{ $global->locale }}-AU.min.js"></script>
@elseif($global->locale == 'pt-br')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.pt-BR.min.js"></script>
@else
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.{{ $global->locale }}.min.js"></script>
@endif
<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('js/datatable_custom_pagination.js') }}"></script>
<script>
    var table;
    var table2;
    $(function() {

        loadTable();
        loadTable2();

        $(document).on('click', '.leadStatuses-edit-btn', function(e) {
            e.preventDefault();
            $('#edit_lead_status .modal-content').html('');
            var id = $(this).data('row-id');
            $.ajax({
                url: "/admin/moving-settings/statuses/edit/" + id,
                method: 'get',
                data: {},
                dataType: "json",
                success: function(result) {

                    if (result.error == 0) {
                        // $('#edit_lead_status').modal('show');
                        $('#edit_lead_status .modal-content').html(result.html);
                    } else {
                        $.toast({
                            heading: 'Error',
                            text: 'Something went wrong!',
                            icon: 'error',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#fb9678',
                            textColor: 'white'
                        });
                    }
                }
            });
        });

        $(document).on('click', '.leadStatuses-delete-btn', function(e) {
            e.preventDefault();
            $('#delete_lead_status .modal-content').html('');
            var id = $(this).data('row-id');
            $.ajax({
                url: "/admin/moving-settings/statuses/delete/" + id,
                method: 'get',
                data: {},
                dataType: "json",
                success: function(result) {

                    if (result.error == 0) {
                        $('#delete_lead_status .modal-content').html(result.html);
                    } else {
                        $.toast({
                            heading: 'Error',
                            text: 'Something went wrong!',
                            icon: 'error',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#fb9678',
                            textColor: 'white'
                        });
                    }
                }
            });
        });

        $(document).on('click', '.leadStatuses-remove-btn', function() {
            var delete_id = $(this).data('row-id');
            swal({
                title: "Are you sure?",
                text: "You want to delete this Lead status?",
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
                        url: "/admin/moving-settings/ajaxDestroyLeadStatus",
                        method: 'post',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "id": delete_id
                        },
                        dataType: "json",
                        beforeSend: function() {
                            $.blockUI();
                        },
                        complete: function() {
                            $.unblockUI();
                        },
                        success: function(result) {

                            if (result.error == 2) {
                                swal({
                                    title: "Info",
                                    text: result.message,
                                    type: "info",
                                    button: "OK",
                                });
                            } else if (result.error == 0) {

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
                                loadTable();
                            }
                        }
                    });
                }
            });
        });

        $(document).on('submit', '#createStatuses', function(e) {
            e.preventDefault();

            $.easyAjax({
                url: "{{route('admin.statuses.store')}}",
                container: '#createStatuses',
                type: "POST",
                redirect: true,
                data: $('#createStatuses').serialize(),
                beforeSend: function() {
                    $.blockUI();
                },
                complete: function() {
                    $.unblockUI();
                },
                success: function(result) {

                    if (result.error == 0) {
                        $('#add_new_lead_status').modal('hide');
                        $(".modal-backdrop").remove();
                        loadTable();
                        $.toast({
                            heading: 'Success',
                            text: result.message,
                            icon: 'success',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#00c292',
                            textColor: 'white'
                        });
                    } else {
                        $.toast({
                            heading: 'Error',
                            text: 'Something went wrong!',
                            icon: 'error',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#fb9678',
                            textColor: 'white'
                        });
                    }
                }

            })

        });

        $(document).on('submit', '#updateStatuses', function(e) {
            e.preventDefault();
            var row_id = $('#lead_status_id').val();
            $.easyAjax({
                url: "{{route('admin.statuses.update', [1])}}",
                container: '#createStatuses',
                type: "POST",
                redirect: true,
                data: $('#updateStatuses').serialize(),
                beforeSend: function() {
                    $.blockUI();
                },
                complete: function() {
                    $.unblockUI();
                },
                success: function(result) {

                    if (result.error == 0) {
                        $('#edit_lead_status').modal('hide');
                        $(".modal-backdrop").remove();
                        loadTable();
                        $.toast({
                            heading: 'Success',
                            text: result.message,
                            icon: 'success',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#00c292',
                            textColor: 'white'
                        });
                    } else {
                        $.toast({
                            heading: 'Error',
                            text: 'Something went wrong!',
                            icon: 'error',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#fb9678',
                            textColor: 'white'
                        });
                    }
                }
            })

        });

        $(document).on('submit', '#deleteStatuses', function(e) {
            e.preventDefault();
            var row_id = $('#lead_status_id').val();
            $.easyAjax({
                url: "{{route('admin.statuses.ajaxDeleteLeadStatuses')}}",
                container: '#createStatuses',
                type: "POST",
                redirect: true,
                data: $('#deleteStatuses').serialize(),
                beforeSend: function() {
                    $.blockUI();
                },
                complete: function() {
                    $.unblockUI();
                },
                success: function(result) {

                    if (result.error == 0) {
                        $('#delete_lead_status').modal('hide');
                        $(".modal-backdrop").remove();
                        loadTable();
                        $.toast({
                            heading: 'Success',
                            text: result.message,
                            icon: 'success',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#00c292',
                            textColor: 'white'
                        });
                    } else {
                        $.toast({
                            heading: 'Error',
                            text: 'Something went wrong!',
                            icon: 'error',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#fb9678',
                            textColor: 'white'
                        });
                    }
                }
            })

        });

        // pipeline

        $(document).on('click', '.pipelineStatuses-edit-btn', function(e) {
            e.preventDefault();
            $('#edit_pipeline_status .modal-content').html('');
            var id = $(this).data('row-id');
            $.ajax({
                url: "/admin/moving-settings/statuses/editPipeline/" + id,
                method: 'get',
                data: {},
                dataType: "json",
                success: function(result) {

                    if (result.error == 0) {
                        // $('#edit_pipeline_status').modal('show');
                        $('#edit_pipeline_status .modal-content').html(result.html);
                    } else {
                        $.toast({
                            heading: 'Error',
                            text: 'Something went wrong!',
                            icon: 'error',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#fb9678',
                            textColor: 'white'
                        });
                    }
                }
            });
        });

        $(document).on('click', '.pipelineStatuses-delete-btn', function(e) {
            e.preventDefault();
            $('#delete_pipeline_status .modal-content').html('');
            var id = $(this).data('row-id');
            $.ajax({
                url: "/admin/moving-settings/statuses/deletePipeline/" + id,
                method: 'get',
                data: {},
                dataType: "json",
                success: function(result) {

                    if (result.error == 0) {
                        $('#delete_pipeline_status .modal-content').html(result.html);
                    } else {
                        $.toast({
                            heading: 'Error',
                            text: 'Something went wrong!',
                            icon: 'error',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#fb9678',
                            textColor: 'white'
                        });
                    }
                }
            });
        });

        $(document).on('click', '.pipelineStatuses-remove-btn', function() {
            var delete_id = $(this).data('row-id');
            swal({
                title: "Are you sure?",
                text: "You want to delete this Pipeline status?",
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
                        url: "/admin/moving-settings/ajaxDestroyPipelineStatus",
                        method: 'post',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "id": delete_id
                        },
                        dataType: "json",
                        beforeSend: function() {
                            $.blockUI();
                        },
                        complete: function() {
                            $.unblockUI();
                        },
                        success: function(result) {

                            if (result.error == 2) {
                                swal({
                                    title: "Info",
                                    text: result.message,
                                    type: "info",
                                    button: "OK",
                                });
                            } else if (result.error == 0) {

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
                                loadTable2();
                            }
                        }
                    });
                }
            });
        });

        $(document).on('submit', '#createPipelineStatuses', function(e) {
            e.preventDefault();

            $.easyAjax({
                url: "{{route('admin.statuses.storePipeline')}}",
                container: '#createPipelineStatuses',
                type: "POST",
                redirect: true,
                data: $('#createPipelineStatuses').serialize(),
                beforeSend: function() {
                    $.blockUI();
                },
                complete: function() {
                    $.unblockUI();
                },
                success: function(result) {

                    if (result.error == 0) {
                        $('#add_new_pipeline_status').modal('hide');
                        $(".modal-backdrop").remove();
                        loadTable2();
                        $.toast({
                            heading: 'Success',
                            text: result.message,
                            icon: 'success',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#00c292',
                            textColor: 'white'
                        });
                    } else {
                        $.toast({
                            heading: 'Error',
                            text: 'Something went wrong!',
                            icon: 'error',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#fb9678',
                            textColor: 'white'
                        });
                    }
                }

            })

        });

        $(document).on('submit', '#updatePipelineStatuses', function(e) {
            e.preventDefault();
            var row_id = $('#pipeline_status_id').val();
            $.easyAjax({
                url: "{{route('admin.statuses.updatePipeline')}}",
                container: '#createPipelineStatuses',
                type: "POST",
                redirect: true,
                data: $('#updatePipelineStatuses').serialize(),
                beforeSend: function() {
                    $.blockUI();
                },
                complete: function() {
                    $.unblockUI();
                },
                success: function(result) {

                    if (result.error == 0) {
                        $('#edit_pipeline_status').modal('hide');
                        $(".modal-backdrop").remove();
                        loadTable2();
                        $.toast({
                            heading: 'Success',
                            text: result.message,
                            icon: 'success',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#00c292',
                            textColor: 'white'
                        });
                    } else {
                        $.toast({
                            heading: 'Error',
                            text: 'Something went wrong!',
                            icon: 'error',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#fb9678',
                            textColor: 'white'
                        });
                    }
                }
            })

        });

        $(document).on('submit', '#deletePipelineStatuses', function(e) {
            e.preventDefault();
            var row_id = $('#pipeline_status_id').val();
            $.easyAjax({
                url: "{{route('admin.statuses.ajaxDeletePipelineStatuses')}}",
                container: '#createPipelineStatuses',
                type: "POST",
                redirect: true,
                data: $('#deletePipelineStatuses').serialize(),
                beforeSend: function() {
                    $.blockUI();
                },
                complete: function() {
                    $.unblockUI();
                },
                success: function(result) {

                    if (result.error == 0) {
                        $('#delete_pipeline_status').modal('hide');
                        $(".modal-backdrop").remove();
                        loadTable2();
                        $.toast({
                            heading: 'Success',
                            text: result.message,
                            icon: 'success',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#00c292',
                            textColor: 'white'
                        });
                    } else {
                        $.toast({
                            heading: 'Error',
                            text: 'Something went wrong!',
                            icon: 'error',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#fb9678',
                            textColor: 'white'
                        });
                    }
                }
            })

        });

    });

    function loadTable() {

        table = $('#listing-table').DataTable({
            ajax: "{!! route('admin.statuses.data') !!}",
            "order": [
                [0, "asc"]
            ],
            language: {
                "url": "<?php echo __("app.datatable") ?>"
            },
            paging: false,
            deferRender: true,
            ordering: false,
            sDom: 't',
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            rowReorder: true,
            // rowReorder: {
            //     selector: 'tr',
            //     update: true
            // },
            // columnDefs: [{
            //     reorderable: true,
            //     targets: [2]
            // }],
            "fnDrawCallback": function(oSettings) {},
            columns: [{
                    data: 'sort_order',
                    name: 'sort_order'
                },
                {
                    data: 'lead_status',
                    name: 'lead_status'
                },
                {
                    data: 'action',
                    name: 'action',
                    width: '10%'
                }
            ],
            createdRow: function(row, data, dataIndex) {
                // console.log(data);
                $(row).attr('data-sort_order', data.sort_order);
                $(row).attr('data-sort_id', data.id);
            }
        });


        table.on('row-reorder', function(e, diff, edit) {
            // console.log(diff);
            // console.log(edit);

            // Find indexes of rows which have `Yes` in the second column
            // table.rows().every(function(rowIdx, tableLoop, rowLoop) {
            //     var data = this.data();
            //     console.log(data.sort_order);
            // });
            var params = [];
            for (var i = 0, ien = diff.length; i < ien; i++) {
                var rowData0 = table.row(diff[i].oldPosition).data().id;
                var rowData2 = table.row(diff[i].newPosition).data().sort_order;
                params[rowData0] = rowData2;
                // params.push({
                //     rowData0: rowData2
                // });
            }
            // console.log(params);
            reorderLeadStatuses(params);
        });
    }

    function reorderLeadStatuses(params) {
        $.ajax({
            url: "/admin/moving-settings/ajaxUpdateLeadStatusReorder",
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                "params": params
            },
            dataType: "json",
            beforeSend: function() {
                // $.blockUI();
            },
            complete: function() {
                // $.unblockUI();
            },
            success: function(result) {

                if (result.error == 0) {
                    // window.location.reload();
                    // $.toast({
                    //     heading: 'Success',
                    //     text: result.message,
                    //     icon: 'success',
                    //     position: 'top-right',
                    //     loader: false,
                    //     bgColor: '#00c292',
                    //     textColor: 'white'
                    // });
                } else {
                    //Notification....
                    // $.toast({
                    //     heading: 'Error',
                    //     text: 'Something went wrong!',
                    //     icon: 'error',
                    //     position: 'top-right',
                    //     loader: false,
                    //     bgColor: '#fb9678',
                    //     textColor: 'white'
                    // });
                }
            }
        });
    }

    function loadTable2() {

        table2 = $('#listing-table2').DataTable({
            ajax: "{!! route('admin.statuses.dataPipeline') !!}",
            "order": [
                [0, "asc"]
            ],
            language: {
                "url": "<?php echo __("app.datatable") ?>"
            },
            paging: false,
            deferRender: true,
            ordering: false,
            sDom: 't',
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            rowReorder: true,
            "fnDrawCallback": function(oSettings) {},
            columns: [{
                    data: 'sort_order',
                    name: 'sort_order'
                },
                {
                    data: 'pipeline_status',
                    name: 'pipeline_status'
                },
                {
                    data: 'pipeline_id',
                    name: 'pipeline_id'
                },
                {
                    data: 'action',
                    name: 'action',
                    width: '10%'
                }
            ],
            createdRow: function(row, data, dataIndex) {
                // console.log(data);
                $(row).attr('data-sort_order', data.sort_order);
                $(row).attr('data-sort_id', data.id);
            }
        });


        table2.on('row-reorder', function(e, diff, edit) {
            // console.log(diff);
            // console.log(edit);

            // Find indexes of rows which have `Yes` in the second column
            // table2.rows().every(function(rowIdx, table2Loop, rowLoop) {
            //     var data = this.data();
            //     console.log(data.sort_order);
            // });
            var params = [];
            for (var i = 0, ien = diff.length; i < ien; i++) {
                var rowData0 = table2.row(diff[i].oldPosition).data().id;
                var rowData2 = table2.row(diff[i].newPosition).data().sort_order;
                params[rowData0] = rowData2;
                // params.push({
                //     rowData0: rowData2
                // });
            }
            reorderPipelineStatuses(params);
        });
    }

    function reorderPipelineStatuses(params) {
        $.ajax({
            url: "/admin/moving-settings/ajaxUpdatePipelineStatusReorder",
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                "params": params
            },
            dataType: "json",
            beforeSend: function() {
                // $.blockUI();
            },
            complete: function() {
                // $.unblockUI();
            },
            success: function(result) {

                if (result.error == 0) {
                    // window.location.reload();
                    // $.toast({
                    //     heading: 'Success',
                    //     text: result.message,
                    //     icon: 'success',
                    //     position: 'top-right',
                    //     loader: false,
                    //     bgColor: '#00c292',
                    //     textColor: 'white'
                    // });
                } else {
                    //Notification....
                    // $.toast({
                    //     heading: 'Error',
                    //     text: 'Something went wrong!',
                    //     icon: 'error',
                    //     position: 'top-right',
                    //     loader: false,
                    //     bgColor: '#fb9678',
                    //     textColor: 'white'
                    // });
                }
            }
        });
    }

    // $('#save-form').click(function() {
    // });
</script>
@endpush