@extends('layouts.app')

@section('page-title')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="{{ $pageIcon }} mr-2"></i> <span class="font-weight-semibold">{{ $pageTitle }}</span></h4>
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
                    <h6 class="card-title">@lang('app.menu.connectStripe')</h6>
                </div>
                <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <img src="{{asset('img/powered_by_stripe.png')}}" class="img img-responsive img-powered-by-stripe" alt="powered-by-stripe">
                            </div>
                            @if(isset($stripe->account_key) && !empty($stripe->account_key))
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <br/>
                            <div class="alert alert-success alert-styled-left alert-arrow-left alert-dismissible">
                                <span class="font-weight-semibold">Connected!</span> Your Stripe account is connected to Onexfort
                            </div>
                            </div>
                            @else
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <p style="padding-top:10px;">
                                    For processing payments on Onexfort using Stripe, you should create your own Stripe account and connect your Stripe <br>account to Onexfort.
                                </p>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <p style="padding-top:10px;">
                                    1- All payments processed will be directly deposited into your bank account by Stripe.
                                </p>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <p style="padding-top:10px;">
                                    2- Onexfort does not store any credit card details of your customers.
                                </p>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <hr>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <p style="padding-top:10px;">
                                    If you don't have a Stripe account, please use the following link to create one.<br>
                                    <a href="https://dashboard.stripe.com/register" target="_blank" style="color:blue;">https://dashboard.stripe.com/register</a>
                                </p>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <p style="padding-top:10px;">
                                    If you already have a Stripe account, click on the Connect button below to connect your Stripe account to Onexfort:
                                </p>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <a href="{{ route('admin.connect-stripe.create') }}">
                                    <img src="{{asset('img/connect-with-stripe.png')}}" class="img img-responsive img-connect-with-stripe" alt="connect-with-stripe">
                                </a>
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
@endpush