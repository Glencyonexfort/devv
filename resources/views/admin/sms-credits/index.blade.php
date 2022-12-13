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
<style>
    .txt-orange{
        color: #f96261!important;
    }

</style>
<div class="content">
    <div class="d-md-flex align-items-md-start">
        @include('sections.admin_setting_menu')
        <div style="flex:auto">
            <div class="card">
                <div class="card-header bg-white header-elements-inline">
                    <h6 class="card-title">@lang('app.menu.smsCredits')</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            {!! Form::open(['id'=>'UpdatesmsCredits1','class'=>'ajax-form','method'=>'PUT']) !!}
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.smsCredits.credits')</label>
                                            <input type="number" min="1" step="1" class="form-control" id="sms_credit" name="sms_credit">
                                            <input type="hidden" id="per_unit_cost" value="{{ $sys_api_settings->per_unit_cost }}"/>
                                            <input type="hidden" id="processing_fee_1" value="{{ $sys_api_settings->variable1 }}"/>
                                            <input type="hidden" id="processing_fee_2" value="{{ $sys_api_settings->variable2 }}"/>
                                            <note>1 credit = 1 sms at <b>{{ $sys_api_settings->per_unit_cost*100 }}</b> cents per sms</note>

                                        </div>
                                        <div id="warning_box" class="alert alert-danger border-0 alert-dismissible" style="display: none"></div> 
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                            <span href="#" class="btn bg-transparent border-success text-success rounded-pill border-2 btn-icon mr-3">
                                                <i class="icon-coins"></i>
                                            </span>
                                            <div>
                                                <div class="font-weight-semibold" style="font-size: 20px;line-height: 20px;">
                                                    @if($tenant_details)
                                                        {{ $tenant_details->sms_credit ? $tenant_details->sms_credit : "0" }}
                                                    @else
                                                        {{ "0" }}
                                                    @endif
                                                </div>
                                                <span>@lang('modules.smsCredits.available_credits')</span>
                                            </div>
                                        </div>
                                    </div>
                                    <!--/span-->
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="hidden" id="buy_btn" value="1"/>
                                        <button type="button" class="btn btn-success m-r-10" id="payButton"><i class="fa fa-plus"></i> @lang('modules.smsCredits.buy')</button>
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    {{-- Auto SMS Top Up --}}
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <hr>
                            {!! Form::open(['id'=>'AutoTopup','class'=>'ajax-form','method'=>'PUT']) !!}
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <div class="form-group">
                                            <label class="control-label">Auto top up</label>
                                            <br/>
                                            <span style="display: inline-flex;">
                                                <strong class="text-muted">Off</strong> 
                                                <div class="switchery-demo" style="margin: 0 10px;">
                                                    <input type="checkbox" id="auto_topup" name="auto_topup" value="Y" class="js-switch " data-color="#f96262" 
                                                    @if($sms_auto_top_up == 'Y') checked @endif/>
                                                </div>  
                                                 <strong id="auto_topup_on" class="text-muted">On</strong>                                          
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                    $auto_topup_div = ($sms_auto_top_up=='N')?'hidden':'';
                                ?>
                                <div id="auto_topup_div" class="{{ $auto_topup_div }}">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>Payment Method</label>
                                                <input type="text" name="stripe_customer_id" class="form-control" id="stripe_customer_id" value="Stripe Customer Id: {{ ($stripe_customer_id=='N')?'':$stripe_customer_id }}" readonly/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-6">
                                            <div class="row">
                                            <label class="col-6 col-form-label">When my balance falls below </label>
                                            <div class="col-6">
                                                <div class="form-group form-group-feedback form-group-feedback-right">
                                                    <input type="number" min="0" id="sms_balance_lower_limit" class="form-control form-control-lg" value="{{ $sms_balance_lower_limit }}">
                                                    <div class="form-control-feedback form-control-feedback-lg">
                                                        credits
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-6">
                                            <div class="row">
                                            <label class="col-6 col-form-label">Then top up my balance by </label>
                                            <div class="col-6">
                                                <div class="form-group form-group-feedback form-group-feedback-right">
                                                    <input type="number" min="0" id="sms_balance_top_up_qty" class="form-control form-control-lg" value="{{ $sms_balance_top_up_qty }}">
                                                    <div class="form-control-feedback form-control-feedback-lg">
                                                        credits
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                            <div id="warning_box2" class="alert alert-danger border-0 alert-dismissible" style="display: none"></div> 
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-success m-r-10" id="topup_setting_btn" >Top up & Save</button>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    </div>
                </div>
        </div>

        @if(count($sms_purchase_history))
            <div class="card">
                <div class="card-body" style="height: 600px;overflow-y: scroll;">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <h6 class="card-title">SMS Purchases</h6>
                            <table>
                                <thead>
                                    @include('admin.sms-credits.sms_purchase_history')
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
<script type="text/jscript" src="https://checkout.stripe.com/checkout.js"></script>
    <script>
        
        var handler = StripeCheckout.configure({
            key: "{{ env('STRIPE_PUBLIC') }}",
            image: '{{ request()->getSchemeAndHttpHost() }}/stripe-onex-logo.jpg',
            locale: 'auto',
            currency:'aud',
            allowRememberMe: false,
            token: function(token) {
                // You can access the token ID with `token.id`.
                // Get the token ID to your server-side code for use.
                var tkn = "{{ csrf_token() }}";
                var auto_topup = ($("#auto_topup").is(':checked'))?'Y':'N';
                var sms_balance_lower_limit = $('#sms_balance_lower_limit').val();
                var sms_balance_top_up_qty = $('#sms_balance_top_up_qty').val();
                var per_unit_cost = parseFloat($('#per_unit_cost').val());
                var processing_fee_1 = parseFloat($('#processing_fee_1').val());
                var processing_fee_2 = parseFloat($('#processing_fee_2').val());
                if($("#buy_btn").val()==1){
                    //if buy credits btn clicked
                    var credits = $('#sms_credit').val();
                    var amount = (((credits*per_unit_cost) * (1+processing_fee_1/100)) + processing_fee_2)*100;
                }else{
                    //if Auto Top up btn clicked
                    var credits = $('#sms_balance_top_up_qty').val();
                    var amount = (((credits*per_unit_cost) * (1+processing_fee_1/100)) + processing_fee_2)*100;
                }

                $.ajax({
                    url: '/admin/settings/buy-sms-credits',
                    type: 'POST',
                    data: {
                        '_token':tkn,stripeToken: token.id, stripeEmail: token.email,'amount':amount,
                        'sms_credit':credits,'auto_topup':auto_topup,'sms_balance_lower_limit':sms_balance_lower_limit,'sms_balance_top_up_qty':sms_balance_top_up_qty,
                        'stripe_customer_id':'{{ $stripe_customer_id }}'
                    },
                    dataType: "json",
                    beforeSend: function(){
                        $('#payButton').prop('disabled', true);
                        $('#payButton').html('Please wait...');
                        $('#topup_setting_btn').prop('disabled', true);
                        $('#topup_setting_btn').html('Please wait...');
                        $(".preloader").show();
                    },
                    complete: function() {
                        $(".preloader").hide();
                    },
                    success: function(data){                       
                        if(data.status == 1){
                            $.toast({
                                heading: 'Success',
                                text: data.msg,
                                icon: 'success',
                                position: 'top-right',
                                loader: false,
                                bgColor: '#00c292',
                                textColor: 'white'
                            });
                            setTimeout(function(){location.reload()}, 3000);
                        } else {
                            $('#payButton').prop('disabled', false);
                            $('#payButton').html('<i class="fa fa-plus"></i> Buy');
                            $('#topup_setting_btn').prop('disabled', false);
                            $('#topup_setting_btn').html('Top up & Save');
                            $("#warning_box").html(data.msg).show().delay(2000).fadeOut('slow');
                        }
                    },
                    error: function(data) {
                        $('#payButton').prop('disabled', false);
                        $('#payButton').html('<i class="fa fa-plus"></i> Buy');
                        $('#topup_setting_btn').prop('disabled', false);
                        $('#topup_setting_btn').html('Top up & Save');
                        $("#warning_box").html('Something went wrong, internal server error!').show().delay(2000).fadeOut('slow');
                    }
                });
            }
        });
        
            var stripe_closed = function(){
                $('#payButton').prop('disabled', false);
                $('#payButton').html('<i class="fa fa-plus"></i> Buy');
                $('#topup_setting_btn').prop('disabled', false);
                $('#topup_setting_btn').html('Top up & Save');
            };
        
        var eventTggr = document.getElementById('payButton');
        if(eventTggr){
            eventTggr.addEventListener('click', function(e) {
                if($('#sms_credit').val() == 0 || $('#sms_credit').val() == ''){
                    $("#warning_box").html('Please enter credits').show(200).delay(4000).fadeOut('slow');
                    return false;
                }
                if($('#sms_credit').val() < 10){
                    $("#warning_box").html('Please enter minimum 10 credits').show(200).delay(4000).fadeOut('slow');
                    return false;
                }

                var credits = $('#sms_credit').val();
                var processing_fee_1 = parseFloat($('#processing_fee_1').val());
                var processing_fee_2 = parseFloat($('#processing_fee_2').val());
                var per_unit_cost = parseFloat($('#per_unit_cost').val());
                var buy_btn = $('#buy_btn').val(1);
                var pay_amount = (((credits*per_unit_cost) * (1+processing_fee_1/100)) + processing_fee_2)*100;
                $('#payButton').prop('disabled', true);
                $('#payButton').html('Please wait...');
                
                // Open Checkout with further options:
                handler.open({
                    name: '{{ $organisation_settings->company_name }}',
                    description: 'SMS Top up',
                    email: '{{ $organisation_settings->company_email }}',
                    amount: pay_amount,
                    closed:	stripe_closed
                });
                e.preventDefault();
            });
        }

        var eventTggrTopup = document.getElementById('topup_setting_btn');
        if(eventTggrTopup){
            eventTggrTopup.addEventListener('click', function(e) {
                console.log($('#sms_balance_lower_limit').val());
                if($('#sms_balance_lower_limit').val() == 0 || $('#sms_balance_lower_limit').val() == ''){
                    $("#warning_box2").html('Please enter sms balance lower limit').show(200).delay(4000).fadeOut('slow');
                    return false;
                }
                if($('#sms_balance_top_up_qty').val() == 0 || $('#sms_balance_top_up_qty').val() == ''){
                    $("#warning_box2").html('Please enter top up credits').show(200).delay(4000).fadeOut('slow');
                    return false;
                }
                if($('#sms_balance_top_up_qty').val() < 10){
                    $("#warning_box2").html('Please enter minimum 10 credits').show(200).delay(4000).fadeOut('slow');
                    return false;
                }
                var buy_btn = $('#buy_btn').val(0);
                var sms_balance_top_up_qty = $('#sms_balance_top_up_qty').val();
                var per_unit_cost = parseFloat($('#per_unit_cost').val());
                var processing_fee_1 = parseFloat($('#processing_fee_1').val());
                var processing_fee_2 = parseFloat($('#processing_fee_2').val());
                var pay_amount_topup = (((sms_balance_top_up_qty*per_unit_cost) * (1+processing_fee_1/100)) + processing_fee_2)*100;
                $('#topup_setting_btn').prop('disabled', true);
                $('#topup_setting_btn').html('Please wait...');
                
                // Open Checkout with further options:
                handler.open({
                    name: '{{ $organisation_settings->company_name }}',
                    description: 'SMS Top up',
                    email: '{{ $organisation_settings->company_email }}',
                    amount: pay_amount_topup,
                    closed:	stripe_closed
                });
                e.preventDefault();
            });
        }
    </script>
<script>
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
            $('.js-switch').each(function() {
                new Switchery($(this)[0], $(this).data());
    });

    $(".js-switch").change(function() {
        var $this = $(this);
        if ($this.prop('id') == 'auto_topup') {
            if($(this).is(':checked')){
                $("#auto_topup_on").addClass("txt-orange");
                $('#auto_topup_div').show(200);
            }else{
                $("#auto_topup_on").removeClass("txt-orange");
                $('#auto_topup_div').hide();
            }
            
        }
    });
</script>
@endpush