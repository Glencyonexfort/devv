@extends('layouts.app')

@section('page-title')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="{{ $pageIcon }} mr-2" style="font-size: 2rem;"></i> <span class="font-weight-semibold">{{ $pageTitle }}</span></h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
    <!-- <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> @lang('app.menu.home')</a>
                <span class="breadcrumb-item active">{{ $pageTitle }}</span>
            </div>
        </div>
    </div> -->
</div>
@endsection

@section('content')

<div class="content">
    @if(Session::has('message'))
    <div class="alert {{ Session::get('alert-class', 'alert-info') }} alert-dismissible">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
    @endif
    <div class="d-md-flex align-items-md-start">
        @include('sections.admin_setting_menu')
        <div style="flex:auto">
            <div class="card">
                <div class="card-header bg-white header-elements-inline">
                    <h6 class="card-title">@lang('app.menu.coverFreight')</h6>
                    @if($tenant_api_details)
                    <a id="disconnect_btn" href="javascript:;" class="btn btn-outline-danger">Disconnect</a>
                    @endif
                </div>
                <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center">
                                <img src="{{asset('img/connectcover.jpg')}}" class="img img-responsive img-powered-by-stripe" alt="Connect CoverFreight">
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <p style="padding-top:10px;">
                                    CoverFreight provides Comprehensive Moving Insurance for household goods and personal effects. The insurance provides cover for accidental damage to household and personal goods in transit.
                                </p>
                            </div>
                            @if(!$tenant_api_details)
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <p style="padding-top:10px;">
                                    To connect with CoverFreight, please contact CoverFreight at <a href="mailto:info@coverfreight.com.au">info@coverfreight.com.au</a> or call (07) 3613 7901. Or email us at <a href="mailto:support@onexfort.com">support@onexfort.com</a>
                                </p>
                            </div>
                            @endif
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script>
    $('body').on('click', '#disconnect_btn', function(e) {
        e.preventDefault();
        var token = "{{ csrf_token() }}";
        swal({
            title: "Are you sure?",
            text: "Are you sure you want to disconnect from CoverFreight?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#f44336",
            confirmButtonText: "Yes, Disconnect!",
            cancelButtonText: "No, Cancel!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: "/admin/settings/coverfreight/disconnect",
                    method: 'post',
                    data: { '_token': token },
                    dataType: "json",
                    beforeSend: function() {
                        $('.preloader').show();
                    },
                    complete: function() {
                        $('.preloader').hide();
                    },
                    success: function(result) {
                        if (result.status == 1) {
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
                        //Reload Page
                        window.location = "/admin/settings/coverfreight";
                    }
                });
            }
        });
    });
</script>
@endpush