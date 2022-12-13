@extends('layouts.app')

@section('page-title')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="{{ $pageIcon }}"></i> <span class="font-weight-semibold">{{ $pageTitle }}</span></h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
    <!-- <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> @lang('app.menu.home')</a>
                <a href="{{ route('admin.settings.index') }}" class="breadcrumb-item">@lang('app.menu.settings')</a>
                <span class="breadcrumb-item active">{{ $pageTitle }}</span>
            </div>
        </div>
    </div> -->
</div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<style>
    .panel-black .panel-heading a,
    .panel-inverse .panel-heading a {
        color: unset !important;
    }
</style>
@endpush

@section('content')
<div class="content">
    <div class="d-md-flex align-items-md-start">
        @include('sections.payment_setting_menu')
        <div style="flex:auto">
            <div class="card">
                <div class="card-header bg-white header-elements-inline">
                    <h6 class="card-title">@lang('app.menu.offlinePaymentMethod')</h6>
                    <div>
                        <a href="javascript:;" id="addMethod" class="btn btn-success btn-sm pull-right "><i class="fa fa-plus" aria-hidden="true"></i> @lang('modules.offlinePayment.addMethod')</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="box-title m-b-0">@lang('modules.offlinePayment.title')</h3>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>@lang('app.menu.method')</th>
                                            <th>@lang('app.description')</th>
                                            <th>@lang('app.status')</th>
                                            <th width="20%">@lang('app.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($offlineMethods as $key=>$method)
                                        <tr>
                                            <td>{{ ($key+1) }}</td>
                                            <td>{{ ucwords($method->name) }}</td>
                                            <td>{!! ucwords($method->description) !!} </td>
                                            <td>@if($method->status == 'yes') <label class="label label-success">@lang('modules.offlinePayment.active')</label> @else <label class="label label-danger">@lang('modules.offlinePayment.inActive')</label> @endif </td>
                                            <td>
                                                <a href="javascript:;" data-type-id="{{ $method->id }}" class="btn btn-sm btn-info btn-rounded edit-type m-t-5"><i class="fa fa-edit"></i> @lang('app.edit')</a>
                                                <a href="javascript:;" data-type-id="{{ $method->id }}" class="btn btn-sm btn-danger btn-rounded delete-type m-t-5"><i class="fa fa-times"></i> @lang('app.remove')</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td>
                                                @lang('messages.noMethodsAdded')
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


{{--Ajax Modal--}}
<div class="modal fade bs-modal-md show in" id="leadStatusModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
            </div>
            <div class="modal-body">
                Loading...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                <button type="button" class="btn blue">Save changes</button>
            </div>
        </div>
    </div>
</div>
{{--Ajax Modal Ends--}}

@endsection

@push('footer-script')

<script>
    //    save project members
    $('#save-type').click(function() {
        $.easyAjax({
            url: "{{route('admin.offline-payment-setting.store')}}",
            container: '#createMethods',
            type: "POST",
            data: $('#createMethods').serialize(),
            success: function(response) {
                if (response.status == "success") {
                    $.unblockUI();
                    window.location.reload();
                }
            }
        })
    });


    $('body').on('click', '.delete-type', function() {
        var id = $(this).data('type-id');
        swal({
            title: "Are you sure?",
            text: "This will remove the method from the list.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm) {
            if (isConfirm) {

                var url = "{{ route('admin.offline-payment-setting.destroy',':id') }}";
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
                            window.location.reload();
                        }
                    }
                });
            }
        });
    });


    $('.edit-type').click(function() {
        var typeId = $(this).data('type-id');
        var url = '{{ route("admin.offline-payment-setting.edit", ":id")}}';
        url = url.replace(':id', typeId);

        $('#modelHeading').html("{{  __('app.edit')." ".__('modules.offlinePayment.title') }}");
        $.ajaxModal('#leadStatusModal', url);
    })
    $('#addMethod').click(function() {
        var url = '{{ route("admin.offline-payment-setting.create")}}';
        $('#modelHeading').html("{{  __('app.edit')." ".__('modules.offlinePayment.title') }}");
        $.ajaxModal('#leadStatusModal', url);
    })
</script>


@endpush