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
<?php if (isset($sys_api_settings) && $sys_api_settings->user != '') { ?>
    <!-- Stripe JavaScript library -->
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
    <script type="text/javascript">
        //set your publishable key
        Stripe.setPublishableKey('<?php echo $sys_api_settings->user; ?>');

        //callback to handle the response from stripe
        function stripeResponseHandler(status, response) {
            if (response.error) {
                //enable the submit button
                $('#payBtn').removeAttr("disabled");
                //display the errors on the form
                // $('#payment-errors').attr('hidden', 'false');
                $('#payment-errors').show();
                $('#payment-errors').addClass('alert alert-danger');
                $("#payment-errors").html(response.error.message);
                setTimeout(function() {
                    $('#payment-errors').fadeOut('fast');
                }, 3000); // <-- time in milliseconds
                //return false;
            } else {
                $('#payment-errors').hide();
                var form$ = $("#add_stripe_payment");
                //get token id
                var token = response['id'];
                //insert the token into the form
                form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");

                $("#payBtn").prop("type", "submit");
                $("#payBtn").trigger("click");
                $('#add_stripe_payment').submit();
                $('#payBtn').attr("disabled", "disabled");
                // 

            }
        }

        $(document).ready(function() {
            //on form submit
            $("#payBtn").one("click", function(event) {
                //$("#payBtn").click(function(event) {
                //disable the submit button to prevent repeated clicks

                //$('#reset-btn').attr("disabled", "disabled");

                //create single-use token to charge the user
                var tokenCreation = Stripe.createToken({
                    number: $('#card_num').val(),
                    cvc: $('#card-cvc').val(),
                    exp_month: $('#card-expiry-month').val(),
                    exp_year: $('#card-expiry-year').val()
                }, stripeResponseHandler);
                console.log(tokenCreation);


                //return false;
                //submit from callback
                //

            });
        });
    </script>
<?php } ?>
@endsection

@section('content')

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
                            {!! Form::open(['id'=>'UpdatesmsCredits','class'=>'ajax-form','method'=>'PUT']) !!}
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <div class="form-group">
                                            <label>@lang('modules.smsCredits.available_credits'):</label>


                                            <strong>&nbsp;&nbsp;&nbsp;
                                                @if($tenant_details)

                                                {{ $tenant_details->sms_credit ? $tenant_details->sms_credit : "0" }}

                                                @endif()
                                            </strong>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('modules.smsCredits.credits')</label>
                                            <input type="number" min="1" step="1" class="form-control" id="sms_credit" name="sms_credit">
                                            <note>1 credit = 1 sms at 7 cents per sms</note>

                                        </div>
                                    </div>
                                    <!--/span-->
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <hr>
                                        <button type="button" class="btn btn-lg btn-primary" onclick="add_payment_popup({{$tenant_details ? $tenant_details->tenant_id : auth()->user()->tenant_id}});" data-toggle="modal" data-target="#stripePaymentModal" data-whatever=""><i class="fa fa-plus">&nbsp;@lang('modules.smsCredits.buy')</i></button>
                                    </div>
                                </div>
                                <!-- <button type="submit" id="payBtn" class="btn btn-success"><i
                                                    class="fa fa-check"></i>
                                            @lang('modules.smsCredits.buy')
                                        </button> -->
                                
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Stripe Payment Modal -->
<div id="stripePaymentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('modules.smsCredits.addStripePayment')</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <?php //if(isset($sys_api_settings) && $sys_api_settings->in_use!='0'){ 
            ?>
            <div id="payment-errors"></div>
            <?php
            //$attributes = array('id' => 'add_stripe_payment');
            // echo form_open("admin/add_stripe_payment", $attributes) 
            ?>

            {!! Form::open(['id'=>'UpdatesmsCredits','class'=>'ajax-form','method'=>'PUT']) !!}

            <div class="modal-body">
                <div class="row">
                    <div class="col-8">
                        <div class="form-group">
                            <input type="number" name="card_num" id="card_num" class="form-control" placeholder="Card Number" autocomplete="off" required> <!-- value="4242424242424242"  -->
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <input type="text" name="cvc" id="card-cvc" maxlength="3" class="form-control" autocomplete="off" placeholder="CVC" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <div class="form-group">
                            <input type="text" name="exp_month" maxlength="2" class="form-control" id="card-expiry-month" placeholder="MM" required>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <input type="text" name="exp_year" class="form-control" maxlength="4" id="card-expiry-year" placeholder="YYYY" required>
                            <?php //echo set_value('exp_year'); 
                            ?>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group row">
                            <label class="col-form-label"><?php //echo $this->lang->line('amount');
                                                            ?></label>
                            <input type="number" class="form-control" placeholder="Amount" id="stripe_amount" step=0.01 min="0" name="stripe_amount" required />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <input type="hidden" id="update_rec_id" name="update_rec_id">
                            <input type="hidden" id="invoice_id" name="invoice_id">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="payBtn" name="stripe_submit_btn" class="btn waves-effect waves-light btn-outline-danger"> <i class="fa fa-check"></i> @lang('modules.smsCredits.addPayment')</button>
                <input type="hidden" value="@lang('app.save') name=" submit" />
                <button class="btn btn-secondary" id="reset-btn" type="reset">Reset</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div> <!-- end of modal-body -->
    <?php //} else { 
    ?>
    <!-- <div class="modal-body"> 
       Stripe is not enabled for processing payment. You can sign up for a connected Stripe account at <a href="https://stripe.com" target="_blank" >https://stripe.com</a>
       </div> -->
    <?php //} 
    ?>
</div>

</div>
</div>
<!-- end of stripe-payment-body -->
@endsection

@push('footer-script')
<script type="text/javascript">
    function add_payment_popup(tenant_id) {
        $('#tenant_id').val(tenant_id);
        //$('#paymentModal').modal('show');
    }
</script>
<?php if (isset($sys_api_settings) && $sys_api_settings->user != '') { ?>
    <!-- Stripe JavaScript library -->
    <script src="https://js.stripe.com/v3/"></script>
    <script type="text/javascript">
        //set your publishable key
        Stripe.setPublishableKey('{{$sys_api_settings->user}}');

        //callback to handle the response from stripe
        function stripeResponseHandler(status, response) {
            if (response.error) {
                //enable the submit button
                $('#payBtn').removeAttr("disabled");
                //display the errors on the form
                // $('#payment-errors').attr('hidden', 'false');
                $('#payment-errors').show();
                $('#payment-errors').addClass('alert alert-danger');
                $("#payment-errors").html(response.error.message);
                setTimeout(function() {
                    $('#payment-errors').fadeOut('fast');
                }, 5000);
            } else {
                $('#payment-errors').hide();
                var form$ = $("#add_stripe_payment");
                //get token id
                var token = response['id'];
                //insert the token into the form
                form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");

                $("#payBtn").prop("type", "submit");
                $("#payBtn").trigger("click");
                $('#add_stripe_payment').submit();
                $('#payBtn').attr("disabled", "disabled");
                // 

            }
        }

        /*function submit_stripeForm()
        {
            //$('#payBtn').attr("disabled", "disabled");
            var tokenCreation = Stripe.createToken({
                    number: $('#card_num').val(),
                    cvc: $('#card-cvc').val(),
                    exp_month: $('#card-expiry-month').val(),
                    exp_year: $('#card-expiry-year').val()
                }, stripeResponseHandler);
        }*/
        $(document).ready(function() {
            //on form submit
            $("#payBtn").one("click", function(event) {
                //$("#payBtn").click(function(event) {
                //disable the submit button to prevent repeated clicks

                //$('#reset-btn').attr("disabled", "disabled");

                //create single-use token to charge the user
                var tokenCreation = Stripe.createToken({
                    number: $('#card_num').val(),
                    cvc: $('#card-cvc').val(),
                    exp_month: $('#card-expiry-month').val(),
                    exp_year: $('#card-expiry-year').val()
                }, stripeResponseHandler);


                //return false;
                //submit from callback
                //

            });
        });
    </script>
<?php } ?>
<script>
    $('#save-form-2').click(function() {
        $.easyAjax({
            url: "{{route('admin.sms-credits.update', [auth()->user()->tenant_id])}}",
            container: '#UpdatesmsCredits',
            type: "POST",
            redirect: true,
            data: $('#UpdatesmsCredits').serialize(),
            success: function(data) {
                if (data.status == 'success') {
                    window.location.reload();
                }
            }
        })
    });
</script>
@endpush