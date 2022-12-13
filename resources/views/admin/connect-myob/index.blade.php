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
                    <h6 class="card-title">@lang('app.menu.connectMyob')</h6>
                    @if($tenant_api_details)
                    <a href="{{ route('admin.connect-myob.disconnect') }}" class="btn btn-outline-danger">Disconnect</a>
                    @endif
                </div>
                <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center">
                                <img src="{{asset('img/connectmyob.png')}}" class="img img-responsive img-powered-by-stripe" alt="Connect MYOB">
                            </div>
                            @if($tenant_api_details)
                                <?php
                                    if(!isset($tenant_api_details->smtp_secret) || !isset($tenant_api_details->account_key)){
                                        $display = 'display:none';
                                    }else{
                                        $display='';
                                    }
                                ?>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">   
                                @if(!isset($tenant_api_details->url))
                                <form id="myob_company_detail" action="#">
                                    @csrf
                                <div style="border-bottom:1px solid rgba(0,0,0,.125);margin: 28px 0px;">
                                    <h6 class="card-title">MYOB Business</h6>
                                </div>    

                                <div class="form-group row">
                                    <label class="col-form-label col-lg-3">Business Name </label>
                                    <select name="url" id="url" class="form-control col-lg-5">
                                        <option>Select business</option>
                                        @if($myob_companies)
                                        @foreach($myob_companies as $company)
                                            <option value="{{ $company->Uri }}"
                                            @if($company->Uri==$tenant_api_details->url)
                                            selected=""
                                            @endif
                                            >{{ $company->Name }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    <i class="fa fa-check check_icon" style="font-size: 24px;padding: 6px 24px;color: #4caf50;{{ $display }}"></i>
                                </div>

                                <div class="row">                                    
                                    <div class="col-lg-12">  
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <hr>
                                    <div class="col-lg-3">                                        
                                        <button id="save_company_detail" class="btn btn-lg btn-success m-l-10">Save & Next <i class="fa fa-angle-double-right"></i></button>                                        
                                    </div>
                                </div>
                                </form>
                                @else
                                    <form id="myob_config" action="#">
                                        @csrf
                                    <div style="border-bottom:1px solid rgba(0,0,0,.125);margin: 28px 0px;">
                                        <h6 class="card-title">Account Configuration</h6>
                                    </div>    

                                    <div class="form-group row">
                                        <label class="col-form-label col-lg-3">Sales Account </label>
                                        <select name="account_id" id="account_id" class="form-control col-lg-5">
                                            <option>Select account</option>
                                            @if($accounts)
                                            @foreach($accounts->Items as $account)
                                                @if($account->Classification!='Income')@continue;@endif
                                                <option value="{{ $account->UID }}"
                                                @if($account->UID==$tenant_api_details->account_key)
                                                selected=""
                                                @endif
                                                >{{ $account->DisplayID.' - '.$account->Name }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                        <i class="fa fa-check check_icon" style="font-size: 24px;padding: 6px 24px;color: #4caf50;{{ $display }}"></i>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-lg-3">Payments Account </label>
                                        <select name="payment_account_id" id="payment_account_id" class="form-control col-lg-5">
                                            <option>Select account</option>
                                            @if($accounts)
                                            @foreach($accounts->Items as $account)
                                                @if($account->Type!='Bank')@continue;@endif
                                                    <option value="{{ $account->UID }}"
                                                    @if($account->UID==$tenant_api_details->smtp_secret)
                                                    selected=""
                                                    @endif
                                                    >{{ $account->DisplayID.' - '.$account->Name }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                        <i class="fa fa-check check_icon" style="font-size: 24px;padding: 6px 24px;color: #4caf50; {{ $display }}"></i>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-lg-3">Tax Code </label>
                                        <select name="smtp_user" id="smtp_user" class="form-control col-lg-5">
                                            <option>Select tax</option>
                                            @if($taxcodes)
                                            @foreach($taxcodes->Items as $tax)
                                                    <option value="{{ $tax->UID }}"
                                                    @if($tax->UID==$tenant_api_details->smtp_user)
                                                    selected=""
                                                    @endif
                                                    >{{ $tax->Code.' - '.$tax->Type.' ('.$tax->Rate.')' }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                        <i class="fa fa-check check_icon" style="font-size: 24px;padding: 6px 24px;color: #4caf50; {{ $display }}"></i>
                                    </div>

                                    <div class="row">                                    
                                        <div class="col-lg-12">  
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <hr>
                                        <div class="col-lg-3">                                        
                                            <button id="save_config" class="btn btn-lg btn-success m-l-10"><i class="fa fa-check"></i>  @lang('app.update')</button>                                        
                                        </div>
                                        {{-- <div class="col-lg-9">
                                            <p>For product wise sale account setup <a href="{{ route('admin.products.index') }}">click here</a></p>
                                        </div> --}}
                                    </div>
                                    </form>
                                @endif
                            </div>
                            @else
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <p style="padding-top:10px;">
                                    Connect MYOB and Onexfort will automatically add invoices, payments & customers to your MYOB account.<br/>
                                    When you click connect, a MYOB window will open. There are following quick steps to do there
                                </p>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <p style="padding-top:10px;">
                                    1- Login to your MYOB account
                                </p>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <p style="padding-top:10px;">
                                    2- Select the organization you want to sync with Onexfort
                                </p>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <p style="padding-top:10px;">
                                    3- Authorize the connection to Onexfort
                                </p>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <p style="padding-top:10px;">
                                    4- MYOB accounts configuration
                                </p>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <hr>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                @if($xero_connected)
                                    <div class="alert alert-info">XERO is already connected!</div>
                                @else
                                <a href="{{ $auth_url }}" class="btn btn-lg btn-success">Connect</a>
                                @endif
                                {{-- <span data-xero-sso data-href="{{ route('admin.connect-myob.authorize') }}" data-label="Connect with MYOB"></span>
                                <script src="https://edge.xero.com/platform/sso/xero-sso.js" async defer></script> --}}
                            </div>
                            @endif
                        </div>
                </div>
            </div>
            @if(count($invoices))
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div style="border-bottom:1px solid rgba(0,0,0,.125);margin: 28px 0px;">
                                <h6 class="card-title">In Queue Invoices</h6>
                            </div> 
                            <table>
                                <thead>
                                    @include('admin.connect-myob.inqueue-invoice')
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script>
    $('body').on('click', '#save_company_detail', function(e) {
        e.preventDefault();

        var job_id = $(this).data("jobid");
        $.ajax({
            url: "/admin/settings/connect-myob/storeCompanyDetail",
            method: 'POST',
            data: $("#myob_company_detail").serialize(),
            dataType: "json",
            beforeSend: function() {
                $.blockUI();
            },
            complete: function() {
                $.unblockUI();
            },
            success: function(result) {
                if (result.error == 0) {
                    $('.check_icon').show(500);
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
                    $(".preloader").show();
                    location.reload();
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
    $('body').on('click', '#save_config', function(e) {
        e.preventDefault();

        var job_id = $(this).data("jobid");
        $.ajax({
            url: "/admin/settings/connect-myob/storeConfig",
            method: 'POST',
            data: $("#myob_config").serialize(),
            dataType: "json",
            beforeSend: function() {
                $.blockUI();
            },
            complete: function() {
                $.unblockUI();
            },
            success: function(result) {
                if (result.error == 0) {
                    $('.check_icon').show(500);
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
</script>
@endpush