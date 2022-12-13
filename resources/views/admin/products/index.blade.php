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
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css"> -->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
@endpush

@section('content')
<style>
    #products-table_filter > label {
        margin-left: 88px;
    }

    #products-table_length > label {
        margin-right: 190px;
    }

    #products-table_paginate {
        margin-left: 325px;
    }
</style>
<div class="content">
    <div class="d-md-flex align-items-md-start">
        @include('sections.admin_finance_setting_menu')
        <div class="card" style="width: 100%;">
            <div class="card-header bg-white header-elements-inline">
                <h6 class="card-title">&nbsp;</h6>
                <div>
                    {{-- <a href="javascript:;" onclick="exportData()" class="btn btn-info btn-sm pull-right m-l-5 hide"><i class="ti-export" aria-hidden="true"></i> @lang('app.exportExcel')</a> --}}
                    <a href="{{ route('admin.products.create') }}" class="btn btn-success btn-sm pull-right hide">@lang('app.addNew') @lang('app.menu.products') <i class="fa fa-plus" aria-hidden="true"></i></a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- <div class="col-md-3">
                        <div class="white-box bg-inverse">
                            <h3 class="box-title text-white">@lang('app.total') @lang('app.menu.products')</h3>
                            <ul class="list-inline two-part">
                                <li><i class="icon-basket text-white"></i></li>
                                <li class="text-right"><span id="totalWorkingDays" class="counter text-white">{{ $totalProducts }}</span></li>
                            </ul>
                        </div>
                    </div> -->
                    <div class="col-md-12">
                        <table class="table table-bordered table-hover toggle-circle default footable-loaded footable" id="products-table">
                            <thead>
                                <tr>
                                    {{-- <th>@lang('app.id')</th> --}}
                                    <th>@lang('app.name')</th>
                                    <th>Category</th>
                                    <th>Product Type</th>
                                    <th>@lang('app.price') (@lang('app.inclusiveAllTaxes'))</th>
                                    <th>Customer Type</th>
                                    <th>Stockable</th>
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
@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script>
    $(function() {
        var table = $('#products-table').dataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            stateSave: true,
            ajax: "{!! route('admin.products.data') !!}",
            language: {
                "url": "<?php echo __("app.datatable") ?>"
            },
            "fnDrawCallback": function(oSettings) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            columns: [
                // {
                //     data: 'id',
                //     name: 'id'
                // },
                {
                    data: 'name',
                    name: 'name',
                    width: '11%'
                },
                {
                    data: 'category',
                    name: 'category',
                    width: '10%'
                },
                {
                    data: 'productType',
                    name: 'productType',
                    width: '10%'
                },
                {
                    data: 'price',
                    name: 'price',
                    width: '10%'
                },
                {
                    data: 'customerType',
                    name: 'customerType',
                    width: '12%'
                },
                {
                    data: 'stockable',
                    name: 'stockable',
                    width: '10%'
                },
                {
                    data: 'action',
                    name: 'action',
                    width: '8%'
                }
            ]
        });


        $('body').on('click', '.sa-params', function() {
            var id = $(this).data('user-id');
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover the deleted product!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm) {
                if (isConfirm) {

                    var url = "{{ route('admin.products.destroy',':id') }}";
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
                                table._fnDraw();
                            }
                        }
                    });
                }
            });
        });



    });

    function exportData() {
        var url = "{{ route('admin.products.export') }}";
        window.location.href = url;
    }
</script>
@endpush