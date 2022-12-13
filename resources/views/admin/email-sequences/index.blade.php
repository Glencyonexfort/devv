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
    <!-- <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item"> <i class="icon-home2 mr-2"></i> @lang('app.menu.home')</a>
                <a class="breadcrumb-item">@lang('app.menu.crmSettings')</a>
                <span class="breadcrumb-item active">@lang('app.menu.email_sequences')</span>
            </div>
        </div>
    </div> -->
</div>
<!-- /page header and Breadcrumb-->
@endsection

@push('head-script')
@endpush

@section('content')
<div class="content">
    <div class="d-md-flex align-items-md-start">
        @include('sections.admin_moving_setting_menu')
        <div class="card" style="width: 100%;">
            <div class="card-header bg-white header-elements-inline">
                <h6 class="card-title">&nbsp;</h6>
                <div>
                    <!-- <a href="javascript:;" onclick="exportData()" class="btn btn-info btn-sm pull-right m-l-5 hide"><i class="ti-export" aria-hidden="true"></i> @lang('app.exportExcel')</a> -->
                    <a href="{{ route('admin.email-sequences.create') }}" class="btn btn-success btn-sm pull-right hide">@lang('modules.emailSequences.addNew') <i class="fa fa-plus" aria-hidden="true"></i></a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-responsive" id="listing-table" style="font-size:13px;width:100%!important;">
                            <thead>
                                <tr>
                                    <th>@lang('modules.emailSequences.sequence_name')</th>
                                    <th>@lang('modules.emailSequences.description')</th>
                                    <th>@lang('app.status')</th>
                                    <th>@lang('app.action')</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
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
    $(function() {
        loadTable();
        $('body').on('click', '.sa-params', function() {
            var id = $(this).data('row-id');
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover the deleted email sequence record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm) {
                if (isConfirm) {
                    var url = "{{ route('admin.email-sequences.destroy',':id') }}";
                    url = url.replace(':id', id);
                    var token = "{{ csrf_token() }}";
                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {
                            '_token': token,
                            '_method': 'DELETE'
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                //                                    swal("Deleted!", response.message, "success");
                                table._fnDraw();
                            }
                        }
                    });
                }
            });
        });
    });

    function loadTable() {

        table = $('#listing-table').dataTable({
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: "{!! route('admin.email-sequences.data') !!}",
            "order": [
                [0, "desc"]
            ],
            language: {
                "url": "<?php echo __("app.datatable") ?>"
            },
            "fnDrawCallback": function(oSettings) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            columns: [{
                    data: 'sequence_name',
                    name: 'sequence_name'
                },
                {
                    data: 'sequence_description',
                    name: 'sequence_description'
                },
                {
                    data: 'active',
                    name: 'active'
                },
                {
                    data: 'action',
                    name: 'action',
                    width: '10%'
                }
            ]
        });
    }

    function exportData() {
        var url = "{{ route('admin.email-sequences.export') }}";
        window.location.href = url;
    }
</script>
@endpush