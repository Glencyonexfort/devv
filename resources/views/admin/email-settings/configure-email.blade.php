@extends('layouts.app')

@section('page-title')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="{{ $pageIcon }} mr-2"></i> <span class="font-weight-semibold">{{ $pageTitle }}</span></h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-envelope"></i></a>
        </div>
    </div>
</div>
@endsection

@section('content')

<div class="content">
    <div class="d-md-flex align-items-md-start">
        @include('sections.admin_setting_menu')
        <div style="flex:auto">
            <div class="card">
                <div class="card-header bg-white header-elements-inline">
                    <h6 class="card-title">@lang('app.menu.configureEmail') Settings</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            {!! Form::open(['id'=>'UpdateConfigureEmail','class'=>'ajax-form','method'=>'PUT']) !!}
                            <div class="row" style="font-size: .96rem;font-weight: 500;">
                                    <div class="col-sm-4">1. Email Server Created</div>

                                    <div class="col-sm-4">
                                        @if($tenant_api_detail)
                                        <i class="icon-checkmark-circle font30 icongreen"></i>
                                        @else
                                        <i class="icon-cancel-circle2 font30 icongrey"></i>
                                        @endif
                                    </div>
                            </div>
                            <br/>
                            <div class="row mb-2" style="font-size: .96rem;font-weight: 500;">
                                <div class="col-sm-4">2. Domain</div>

                                <div class="col-sm-4">
                                    @if($account_key!="")
                                    <i class="icon-checkmark-circle font30 icongreen"></i>
                                    @else
                                        <i class="icon-cancel-circle2 font30 icongrey"></i>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-1" style="font-size: .96rem;font-weight: 500;">
                                <div class="col-sm-4">
                                    <input class="form-control domain_field" type="text" id="tenant_domain" placeholder="Your website domain" value="{{ $account_key }}"/>
                                </div>

                                <div class="col-sm-4">
                                    <button id="configure_domain" class="btn btn-light btn-lg weight500" @if($account_key!="")disabled @endif>Configure Domain</button>
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-sm-12 mb-2" style="font-size: 14px;font-weight: bold;">3. DNS Settings</div>

                                <div class="col-sm-12">
                                    <p style="font-weight: 500;">
                                        Head over to DNS provider and add the following records to verify your domain for sending emails through Onexfort.
                                    </p>
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-sm-12">
                                        <table class="table email-config-table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 20%;"></th>
                                                    <th style="width: 20%;">Hostname</th>
                                                    <th style="width: 10%;">Type</th>
                                                    <th style="width: 40%;">Add this value</th>
                                                    <th style="width: 10%;"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex mb-2" style="line-height: 16px;">
                                                            @if($domain_detail!=false && $domain_detail->DKIMVerified==1)
                                                                <i class="icon-checkmark-circle font30 icongreen mr-1"></i>
                                                            @else
                                                                <i class="icon-cancel-circle2 font30 icongrey mr-1"></i>
                                                            @endif 
                                                            <div>
                                                                <div class="font-weight-semibold font16" style="color:#333;">DKIM</div>
                                                                    @if($domain_detail!=false && $domain_detail->DKIMVerified==1)
                                                                        <span class="icongreen">Active</span>
                                                                    @else
                                                                        <span class="text-muted">Inactive</span>
                                                                    @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($domain_detail!=false && $domain_detail->DKIMVerified==1)
                                                            {{ $domain_detail->DKIMHost }}
                                                        @elseif($domain_detail!=false)
                                                            {{ $domain_detail->DKIMPendingHost }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($domain_detail!=false)
                                                            TXT
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($domain_detail!=false && $domain_detail->DKIMVerified==1)
                                                            {{ str_replace(' ','',$domain_detail->DKIMTextValue) }}
                                                        @elseif($domain_detail!=false)
                                                            {{ str_replace(' ','',$domain_detail->DKIMPendingTextValue) }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($domain_detail!=false && $domain_detail->DKIMVerified==false)
                                                            <button id="verify_dkim" class="btn btn-primary btn-sm" data-val="{{ $domain_detail->ID }}">Verify</button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex mb-2" style="line-height: 16px;">
                                                            @if($domain_detail!=false && $domain_detail->ReturnPathDomainVerified==true)
                                                            <i class="icon-checkmark-circle font30 icongreen mr-1"></i>
                                                            @else
                                                                <i class="icon-cancel-circle2 font30 icongrey mr-1"></i>
                                                            @endif                                                             
                                                            <div>
                                                                <div class="font-weight-semibold font16" style="color:#333;">Return-Path</div>
                                                                
                                                                    @if($domain_detail!=false && $domain_detail->ReturnPathDomainVerified==true)
                                                                        <span class="icongreen">Active</span>
                                                                    @else
                                                                        <span class="text-muted">Inactive</span>
                                                                    @endif 
                                                                
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($domain_detail!=false && $domain_detail->ReturnPathDomainVerified==1)
                                                            {{ $domain_detail->ReturnPathDomain }}
                                                        @elseif($domain_detail!=false)
                                                            {{ "pm-bounces" }}
                                                        @endif
                                                    </td>ReturnPathDomain
                                                    <td>
                                                        @if($domain_detail!=false)
                                                            CNAME
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($domain_detail!=false)
                                                            {{ $domain_detail->ReturnPathDomainCNAMEValue }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($domain_detail!=false && $domain_detail->ReturnPathDomainVerified==false)
                                                            <button id="verify_return_path" class="btn btn-primary btn-sm" data-val="{{ $domain_detail->ID }}">Verify</button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-sm-12">
                                    <p style="font-weight: 500;">
                                        Help Article: <a href="https://postmarkapp.com/support/article/1090-resources-for-adding-dkim-and-return-path-records-to-dns-for-common-hosts-and-dns-providers" target="_blank" style="text-decoration: underline">
                                            Resources for adding DNS records for common hosts and DNS providers
                                        </a>
                                    </p>
                                </div>
                            </div>
                            <div class="row mb-2" style="font-size: .96rem;font-weight: 500;">
                                <div class="col-sm-12 mb-1" style="font-size: 14px;font-weight: bold;">4. Default Email For Communication</div>
                            </div>

                            <div class="row" style="font-size: .96rem;font-weight: 500;">
                                <div class="col-sm-4">
                                    <input class="form-control domain_field" type="text" id="default_email" placeholder="email@onexfort.com" value="{{ $default_email }}"/>
                                </div>

                                <div class="col-sm-4">
                                    <button id="update_default_email" class="btn btn-light btn-lg weight500">Update</button>
                                </div>
                            </div>
                            <br/>
                            <div class="row mb-2">
                                <div class="col-sm-12 mb-2" style="font-size: 14px;font-weight: bold;">5. Email Forwarding</div>

                                <div class="col-sm-12">
                                    <p style="font-weight: 500;">
                                        Head over to your Email service provider and set up to forward a copy of the email received as follows:
                                    </p>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3" style="font-size: 14px;">{{$default_email}}</div>
                                <div class="col-sm-3" style="font-size: 14px;font-weight: bold;">forward a copy to</div>

                                <div class="col-sm-6">
                                    <p>
                                        @if($tenant_api_detail)
                                            {{ $tenant_api_detail->incoming_email }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-12">
                                    <p style="font-weight: 500;">
                                        Help Article: <a href="https://postmarkapp.com/support/article/785-configuring-a-custom-email-address-forward-with-gmail" target="_blank" style="text-decoration: underline">
                                            Configuring email forwarding in Gmail/Google Apps
                                        </a>
                                    </p>
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
</div>
</div>
@endsection

@push('footer-script')
<script>
    $(document).ready(function() {    


$('body').on('click', '#configure_domain', function(e) {
    e.preventDefault();
    var domain = $("#tenant_domain").val();
    var token = "{{ csrf_token() }}";
    $.ajax({
        url: "/admin/settings/configure-email/configureDomain",
        method: 'post',
        data: {'_token': token, 'domain':domain},
        dataType: "json",
        beforeSend: function() {
            $.blockUI();
        },
        complete: function() {
            $.unblockUI();
        },
        success: function(result) {

            if (result.error == 0) {
                location.reload();
            } else {
                //Notification....
                $.toast({
                    heading: 'Error',
                    text: result.message,
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

$('body').on('click', '#update_default_email', function(e) {
    e.preventDefault();
    var email = $("#default_email").val();
    var token = "{{ csrf_token() }}";
    $.ajax({
        url: "/admin/settings/configure-email/updateEmail",
        method: 'post',
        data: {'_token': token, 'email':email},
        dataType: "json",
        beforeSend: function() {
            $.blockUI();
        },
        complete: function() {
            $.unblockUI();
        },
        success: function(result) {

            if (result.error == 0) {
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
                    location.reload();
            } else {
                //Notification....
                $.toast({
                    heading: 'Error',
                    text: result.message,
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

$('body').on('click', '#verify_dkim', function(e) {
    e.preventDefault();
    var domain_id = $(this).data('val');
    var token = "{{ csrf_token() }}";
    $.ajax({
        url: "/admin/settings/configure-email/verifyPostMarkDKIM",
        method: 'post',
        data: {'_token': token, 'domain_id':domain_id},
        dataType: "json",
        beforeSend: function() {
            $.blockUI();
        },
        complete: function() {
            $.unblockUI();
        },
        success: function(result) {

            if (result.error == 0) {
                    location.reload();
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

$('body').on('click', '#verify_return_path', function(e) {
    e.preventDefault();
    var domain_id = $(this).data('val');
    var token = "{{ csrf_token() }}";
    $.ajax({
        url: "/admin/settings/configure-email/verifyPostMarkReturnPath",
        method: 'post',
        data: {'_token': token, 'domain_id':domain_id},
        dataType: "json",
        beforeSend: function() {
            $.blockUI();
        },
        complete: function() {
            $.unblockUI();
        },
        success: function(result) {

            if (result.error == 0) {
                    location.reload();
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

});
</script>
@endpush