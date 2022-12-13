@extends('layouts.app')

@section('page-title')
 <div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline" style="border-top:1px solid #ccc; border-bottom: 1px solid #ccc;">
        <div class="page-pipelines d-flex">
            <h4><span class="font-weight-semibold" style="font-size:23.6px;margin-left:-11px !important;font-family: 'Poppins',sans-serif;">Employees</span></h4>
        </div>
    </div>
</div>
@endsection

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <div class="row">
                <div class="col-sm-6">
                            <div class="form-group">
                                <a href="{{ route('admin.list-employees.create') }}" class="btn btn-outline btn-success btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> @lang('modules.peopleOperations.addEmployee')</a>
                            </div>
                        </div>
            </div>

            <div class="table-responsive">
                <table class="table table-responsive" id="listing-table">
                    <thead>
                        <tr>
                            <th>@lang('modules.peopleOperations.employee_number')</th>
                            <th>@lang('modules.peopleOperations.first_name')</th>
                            <th>@lang('modules.peopleOperations.last_name')</th>
                            <th>@lang('modules.peopleOperations.mobile')</th>
                            <th>@lang('modules.peopleOperations.system_user')</th>
                            <th>@lang('modules.peopleOperations.email')</th>
                            <th>@lang('modules.peopleOperations.system_role')</th>
                            <th>@lang('app.action')</th>
                        </tr>
                    </thead>

                    
                </table>
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
        $('body').on('click', '.sa-params', function() {
            var id = $(this).data('row-id');
            swal({
                title: "Warning",
                text: "Are you sure you want to delete this employee?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm) {
                if (isConfirm) {

                    var url = "{{ route('admin.list-employees.destroy',':id') }}";
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
            ajax: "{!! route('admin.list-employees.data') !!}",
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
            columns: [
                //{ data: 'id', name: 'id' },
                {
                    data: 'employee_number',
                    name: 'employee_number'
                },
                {
                    data: 'first_name',
                    name: 'first_name'
                },
                {
                    data: 'last_name',
                    name: 'last_name'
                },
                {
                    data: 'mobile',
                    name: 'mobile'
                },
                {
                    data: 'is_system_user',
                    name: 'is_system_user'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'display_name',
                    name: 'display_name'
                },

                {
                    data: 'action',
                    name: 'action',
                    width: '10%'
                }
            ]
        });
    }
</script>
@endpush
