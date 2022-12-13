@extends('layouts.app')

<style type="text/css">
    #listing-table_length {
        float: left;
    }

    #listing-table_filter {
        margin: 0 0 1.25rem 8.25rem;
    }

    .dataTables_filter>label:after {
        top: 67% !important;
    }

    .dataTable thead .sorting:before {
        display: none !important;
    }

    .dropdown-menu>li>a {
        display: block !important;
    }
</style>
@section('page-title')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="{{ $pageIcon }} mr-2"></i> <span class="font-weight-semibold">{{ $pageTitle }}</span></h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
</div>
@endsection

@push('head-script')
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('newassets/global_assets/js/plugins/forms/styling/switchery.min.js') }}">
<link rel="stylesheet" href="{{ asset('newassets/global_assets/js/demo_pages/form_checkboxes_radios.js') }}">

@endpush

@section('content')

<div class="content">
    <div class="d-md-flex align-items-md-start">
        @include('sections.removal_setting_menu')
    <div class="card" style="width: 100%;">
    <div class="col-md-12">
        <div class="white-box">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <a href="{{ route('admin.job-templates.create') }}" class="btn btn-outline btn-success btn-sm">@lang('modules.jobTemplates.addNew') <i class="fa fa-plus" aria-hidden="true"></i></a>
                    </div>
                </div>
                <div class="col-sm-6 text-right hidden-xs">
                    <div class="form-group">
                        <a href="javascript:;" onclick="exportData()" class="btn btn-info btn-sm"><i class="ti-export" aria-hidden="true"></i> @lang('app.exportExcel')</a>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover toggle-circle default footable-loaded footable" id="listing-table" style="font-size:13px;width:100%!important;">
                    <thead>
                        <tr>
                            <th>@lang('modules.jobTemplates.job_template_name')</th>
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
<!-- .row -->

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
                            $('body').on('click', '.sa-params', function(){
                            var id = $(this).data('row-id');
                            swal({
                            title: "Are you sure?",
                                    text: "You will not be able to recover the deleted email template!",
                                    type: "warning",
                                    showCancelButton: true,
                                    confirmButtonColor: "#DD6B55",
                                    confirmButtonText: "Yes, delete it!",
                                    cancelButtonText: "No, cancel please!",
                                    closeOnConfirm: true,
                                    closeOnCancel: true
                            }, function(isConfirm){
                            if (isConfirm) {

                            var url = "{{ route('admin.job-templates.destroy',':id') }}";
                            url = url.replace(':id', id);
                            var token = "{{ csrf_token() }}";
                            $.easyAjax({
                            type: 'POST',
                                    url: url,
                                    data: {'_token': token, '_method': 'DELETE'},
                                    success: function (response) {
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
                            function loadTable (){

                            table = $('#listing-table').dataTable({
                            destroy: true,
                                    responsive: true,
                                    processing: true,
                                    serverSide: true,
                                    ajax: '{!! route('admin.job-templates.data') !!}',
                                    "order": [[ 0, "desc" ]],
                                    language: {
                                    "url": "<?php echo __("app.datatable") ?>"
                                    },
                                    "fnDrawCallback": function(oSettings) {
                                    $("body").tooltip({
                                    selector: '[data-toggle="tooltip"]'
                                    });
                                    },
                                    columns: [
                                    { data: 'job_template_name', name: 'job_template_name' },
                                    { data: 'action', name: 'action', width: '25%' }
                                    ]
                            });
                            }

                            function exportData(){
                            var url = '{{ route('admin.job-templates.export') }}';
                            window.location.href = url;
                            }

</script>
@endpush