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
<link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">
@endpush

@section('content')

<div class="content">
    <div class="d-md-flex align-items-md-start">
        @include('sections.payment_setting_menu')
        <div style="flex:auto">
            <div class="card">
                <div class="card-header bg-white header-elements-inline">
                    <h6 class="card-title">@lang('app.menu.onlinePayment')</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12 ">
                            {!! Form::open(['id'=>'updateSettings','class'=>'ajax-form','method'=>'PUT']) !!}
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 class="box-title text-success">Paypal</h3>
                                        <hr class="m-t-0 m-b-20">
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Paypal Client Id</label>
                                            <input type="text" name="paypal_client_id" id="paypal_client_id" class="form-control" value="{{ $credentials->paypal_client_id }}">
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Paypal Secret</label>
                                            <input type="text" name="paypal_secret" id="paypal_secret" class="form-control" value="{{ $credentials->paypal_secret }}">
                                        </div>
                                    </div>
                                    <!--/span-->

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">@lang('modules.payments.paypalStatus')</label>
                                            <div class="switchery-demo">
                                                <input type="checkbox" name="paypal_status" @if($credentials->paypal_status == 'active') checked @endif class="js-switch " data-color="#00c292" data-secondary-color="#f96262" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 m-t-20">
                                        <h3 class="box-title text-warning">Stripe</h3>
                                        <hr class="m-t-0 m-b-20">
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Stripe Publishable Key</label>
                                            <input type="text" name="stripe_client_id" id="stripe_client_id" class="form-control" value="{{ $credentials->stripe_client_id }}">
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Stripe Secret</label>
                                            <input type="text" name="stripe_secret" id="stripe_secret" class="form-control" value="{{ $credentials->stripe_secret }}">
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Stripe Webhook Secret</label>
                                            <input type="text" name="stripe_webhook_secret" id="stripe_webhook_secret" class="form-control" value="{{ $credentials->stripe_webhook_secret }}">
                                        </div>
                                    </div>
                                    <!--/span-->

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">@lang('modules.payments.stripeStatus')</label>
                                            <div class="switchery-demo">
                                                <input type="checkbox" name="stripe_status" @if($credentials->stripe_status == 'active') checked @endif class="js-switch " data-color="#00c292" data-secondary-color="#f96262" />
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <!--/row-->

                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <hr>
                                        <button type="submit" id="save-form-2" class="btn btn-lg btn-success"><i class="fa fa-check"> </i>@lang('app.save')</button>
                                        <button type="reset" class="btn btn-lg btn-default">@lang('app.reset')</button>
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{--Ajax Modal--}}
<div class="modal fade bs-modal-md in" id="leadStatusModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
{{--Ajax Modal Ends--}}

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/switchery/dist/switchery.min.js') }}"></script>
<script>
    // Switchery
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    $('.js-switch').each(function() {
        new Switchery($(this)[0], $(this).data());

    });
    $('#save-form-2').click(function() {
        $.easyAjax({
            url: "{{ route('admin.payment-gateway-credential.update', [$credentials->id])}}",
            container: '#updateSettings',
            type: "POST",
            redirect: true,
            data: $('#updateSettings').serialize()
        })
    });
</script>
@endpush