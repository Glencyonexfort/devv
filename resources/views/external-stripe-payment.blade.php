<!DOCTYPE html>
<html>
<head>
    <title>Payments</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <link href="//stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Poppins" />
    <link rel="icon" href="{{ asset('favicon/favicon.png') }}" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
        html, body {
          height: 100%;
          margin: 0;
          font-family: "Poppins" , sans-serif !important;
          font-weight: 400;
          font-size: 14px;
        }
        .c_blue{
            color: #3681ae;
        }
        .font12{
            font-size: 12px;
            padding: 10px 15px;
        }
        .font14{
            font-size: 14px;
            padding: 0px 15px 15px;            
        }
        .field{
            padding: 8px 0px 8px 20px;
            border-radius: 4px;
            width: 310px;
            border: 1px solid #ebebeb;
            color: #b3b3b3;
        }
        .inputfield{
            padding: 8px 0px 8px 20px;
            border-radius: 4px;
            width: 290px;
            border: 1px solid #ebebeb;
            color: #111;
        }
        .bb{
            border-bottom: 1px solid #ebebeb;
        }
        .title-heading{
            font-family: "Poppins" , sans-serif !important;
            font-weight: bold;
            font-size: 26px;
            color: #111;
        }
        .title-subheading{
            font-family: "Poppins" , sans-serif !important;
            color: #111;
            font-size: 12px;
        }
        .title-subheading2{
            font-family: "Poppins" , sans-serif !important;
            font-size: 26px;
            text-align: center;
            font-weight: bold;
            padding-top: 2rem;           
        }
        .full-height {
          height: 100%;
        }
        .btn-default{
            color: #6cd4ca;
        background: none!important;
        border:none!important;
        border-radius: 0px!important;
        padding: 3px 11px!important;
        height: 38px!important;
        width: 38px!important;
        font-size: 20px!important;
    }
    .btn-default:hover{
        color: #6cd4ca;
        background: none!important;
        border:none!important;
        border-radius: 0px!important;
        padding: 3px 11px!important;
        height: 38px!important;
        width: 38px!important;
        font-size: 20px!important;
    }
    #inventory_success_div{
        height: 440px;
        width: 980px;
        display: table-cell;
        padding: 2rem;
        border:1px solid #ebebeb;
        border-radius: 6px;
        padding-top: 6px;
        background-color: #fff;
    }
    .inventory_qty{
        display: block;text-align: center;padding: 18px;border: 1px solid #ebebeb;width: 98%!important;
    }

        .page-top{
            color:#111;
            background: linear-gradient(to right, #397496, #55d9d1), linear-gradient(to right, #397496, #55d9d1);
            background-size: 100% 2px;
            background-position: bottom 0 left 0,bottom 2px left 0;
            background-repeat: no-repeat;
            background-color:#fff; 
            padding: 10px;
            border: none!important;
        }   
        .page-top-right{
            float: right;
            margin-top: 16px;
            font-size: 22px;
            font-weight: bold;
        }
        .content-wrap{
            padding: 2px 22px;
        }
        .submit-btn{
            background-color: #6cd4ca;
            color: #fff;
            font-size: 14px!important;
            font-weight: normal;
            padding: 8px 38px;
            border-radius:2rem; 
        }
        .submit-btn:hover{
            color: #fff;
            opacity: 0.9;
        }
        footer{
            background-color: #397496;
            padding-top: 18px;
            padding-bottom: 22px;
            color: white;
        }
        .inv-title{            
            border: none!important; 
            padding: 20px 15px!important;           
        }  
        .inv-div{
            background-color: #fff!important;
            padding: 34px;
            border: 1px solid #ebebeb;
            border-bottom-left-radius: 6px;
            border-bottom-right-radius: 6px;
            margin-bottom: 4rem;
        }
        .noborder{
            border: none!important;
            -webkit-box-shadow: none!important;
            box-shadow: none!important;
        }
        .font14{
            font-size: 14px!important;
        }
        .pay-tab{
            padding: 20px;
            color: #111;
            font-size: 14px;
        }
        .pay-tab.active2{
            border-bottom: 4px solid #6cd4ca; 
        }
        .ctabs{
            margin-top: 18px;padding-bottom: 23px;border-bottom:1px solid #ebebeb;margin-left: 0px;margin-right: 0px;
        }
        .required{
            color:#da251e;
        }
        .card-img{
            height: 25px
        }
        #paymentForm{
            border: 1px solid #eee;padding: 20px;
        }
        .field_label{
            font-size: 12px;font-weight: normal;cursor: pointer;
        }
        #paymentDetails{
            border: 1px solid #eee;padding: 20px;color: #fff;background: #6cd4ca;text-align: center;display: none;
        }
        .success_box{
            margin-bottom: 3rem;padding-top: 3.6rem;font-weight: 600;
        }
        @media only screen and (min-width: 767px) {
            .container {
                margin-left: 10px;
            }
        }
        </style>
</head>
<?php
    $deposit_required = $processing_fee + $deposit_required;
    $deposit_required = str_replace(',', '', number_format($deposit_required, 2));
?>
<body style="background-color:#fafbfe">
    <nav class="navbar navbar-default navbar-top page-top" style="margin-bottom: 30px;">
        <div class="container">
        <div>
            
            @if($company_logo_exists)
            <img class="top-logo" style="height: 62px;padding:7px" src="{{ request()->getSchemeAndHttpHost().'/user-uploads/company-logo/'.$company->logo }}"/>
            @else
            <h1 style="font-size:24px;margin-top: 10px;margin-bottom: 12px;">{{ $company->company_name }}</h1>            
            @endif
        </div>
        </div>
    </nav>
<div class="container">
    <div class="row">
        <div class="col-sm-12 col-sm-offset-0   col-md-10 col-md-offset-1   col-lg-9 col-lg-offset-1" style="margin-bottom: 2rem;margin-left:12.33333333%">
            <div id="inventory_success_div">                
                    <div class="col-sm-12">
                        <div class="row">
                        <h2 class="title-heading">Thank you for choosing {{ $company->company_name }}</h2>
                        <h2 class="title-subheading">Please enter you payment details below:</h2>
                </div>
                    </div>
                <div class="row" style="margin-bottom: 15px;">
                    <div class="col-sm-6" style="padding-right: 3.1rem;">
                        <h3 style="font-size: 16px;font-weight: 600;padding-bottom: 23px;border-bottom: 1px solid #ebebeb;">Job Number: 
                            <span style="font-size: 16px;float: right;color: #6cd4ca">{{ $job->job_number }}</span></h3>                        
                    </div>
                    <div class="col-sm-6">
                        <div class="row ctabs">
                        <span id="transaction_tab" class="pay-tab active2">Transaction Details</span>
                        <span id="confirmation_tab" class="pay-tab" style="margin-left: 2rem;">Confirmation</span>
                    </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div style="border: 1px solid #eee;padding: 20px;">
                            <table>
                                <tbody>
                                    <tr>
                                        <td class="font12">Name</td>                                    
                                    </tr>
                                    <tr>
                                        <td class="font14 c_blue bb">{{ $lead->name }}</td>                                                                        
                                    </tr>
                                    <tr>
                                        <td class="font12">Mobile Number</td>
                                    </tr>
                                    <tr>
                                        <td class="font14 c_blue bb">
                                            @if($mobile=='')
                                            ...
                                            @else
                                            {{ $mobile }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font12">Email:</td>                                    
                                    </tr>
                                    <tr>
                                        <td class="font14 c_blue bb">
                                            @if($email=='')
                                            ...
                                            @else
                                            {{ $email }}
                                            @endif
                                        </td>
                                    </tr>
                                    @if($sys_job_type=="Moving")
                                    <tr>
                                        <td class="font12">Pickup Address: <span class="required"> *</span></td>                                    
                                    </tr>
                                    <tr>
                                        <td class="font14 c_blue bb">
                                            @if($job)
                                                <input type="text" id="pickup_address" class="geo-address form-control" value="{{ $job->pickup_address }}" placeholder="Pickup Address"/>
                                            @else
                                                <input type="text" id="pickup_address" class="geo-address form-control" value="" placeholder="Pickup Address"/>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font12">Drop off Address: <span class="required"> *</span></td>                                    
                                    </tr>
                                    <tr>
                                        <td class="font14 c_blue">
                                            @if($job)
                                                <input type="text" id="drop_off_address" class="geo-address form-control" value="{{ $job->drop_off_address }}" placeholder="Drop off Address"/>
                                            @else
                                                <input type="text" id="drop_off_address" class="geo-address form-control" value="" placeholder="Drop off Address"/>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                        </table>
                    </div>
                    </div>
                    <div class="col-sm-6">
                        <div id="error_box" class="alert alert-danger" style="display: none">Transaction has been failed</div>
                        <div id="warning_box" class="alert alert-warning" style="display: none"></div>                        
                        <div id="paymentForm">
                            <div style="padding: 10px 15px;padding-left: 30px;">
                            <div class="form-group row" style="margin-bottom: 20px;">
                              <label for="staticEmail" class="col-form-label field_label">Payment Amount <span class="required">*</span></label><br/>
                                  <input type="number" class="form-control" disabled step=".01" value="{{ $deposit_required }}" />
                            </div>
                            <div class="form-group row" style="margin-bottom: 20px;">
                              <label for="inputPassword" class="col-form-label field_label">Email address for confirmation of this payment: <span class="required">*</span></label><br/>
                              <input type="text" class="email-field form-control" value="{{ $email }}" disabled/>
                            </div>
                            @if($stripeCustomerId=='Y')
                            <div class="form-group row" style="margin-bottom: 20px;">
                                <input type="radio" name="card_detail" class="card_detail" value="old" id="previous_card_detail"/>
                                <label class="col-form-label field_label" for="previous_card_detail"> User previous card details</label>

                                <input type="radio" name="card_detail" class="card_detail" value="new" id="new_card_detail" checked style="margin-left: 2rem;"/>
                                <label class="col-form-label field_label" for="new_card_detail"> User new card</label>
                            </div>
                            @endif
                            <div class="form-group row" style="margin-bottom: 20px;">
                                <input type="checkbox" id="term_and_condition_check"/> 
                                <label class="col-form-label field_label"> I agree to the <a data-toggle="modal" data-target="#termConditions_popup" style="cursor: pointer">terms and conditions</a></label>
                              </div>
                            <div class="text-center">
                            <button id="payButton" type="button" class="btn submit-btn">Pay Now</button>
                            <a id="payWithCardDetailButton" class="btn submit-btn" style="display: none">Pay Now</a>
                            <input type="hidden" id="payProcess" value="0"/>
                            </div>
                        </div>
                    </div>
                        <div id="paymentDetails">
                        <div class="success_box">
                                <img src="{{ request()->getSchemeAndHttpHost() }}/img/msg-icon.png"/>
                                <p style="font-size:20px;font-weight: bold">Thanks for the payment</p>
                                <p style="font-size: 12px;font-weight: normal">A receipt has sent to your email.</p>
                        </div>                                                
                        </div>
                        <div style="margin-top: 20px">
                            <img class="card-img" src="{{ request()->getSchemeAndHttpHost() }}/img/visacard.jpg"/>
                            <img class="card-img" src="{{ request()->getSchemeAndHttpHost() }}/img/mastercard.jpg"/>
                            <img class="card-img" src="{{ request()->getSchemeAndHttpHost() }}/img/amexcard.jpg"/>
                            <span class="pull-right">
                                <span style="color:#b3b3b3;padding-right:10px">Powered by</span>
                                <img class="card-img" src="{{ request()->getSchemeAndHttpHost() }}/img/stripe.png"/>
                            </span>
                        </div>
                    </div>
                    
                    <div class="pull-right">
                        
                    </div>
            </div>
               
            </div>      
        </div>
    </div>
</div>
<!-- Modal -->
<div id="termConditions_popup" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
  
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          
          <div class="modal-title">
            @if($company_logo_exists)
            <img class="top-logo" style="height: 40px;" src="{{ request()->getSchemeAndHttpHost().'/user-uploads/company-logo/'.$company->logo }}"/>
            @else
            <h4>{{ $company->company_name }}</h4>            
            @endif
          </div>
        </div>
        <div class="modal-body">
          <p><?php echo htmlspecialchars_decode(stripslashes($company->payment_terms)); ?></p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
      </div>      
    </div>
  </div>
<footer>
    <div class="container">
        {{-- <span style="float: left"><a style="color: white;" href="javascript:">Terms &amp; Conditions</a></span> --}}
        <span style="float: right;">© {{ date('Y') }} {{ $company->company_name }}</span>
    </div>
</footer>
<script src="https://maps.googleapis.com/maps/api/js?key={{ $google_api_key }}&libraries=places"></script>
<script type="text/javascript">
    $(document).ready(function() {
      var autocompletesWraps = ['facility_address', 'source_address'];
      createGeoListeners(autocompletesWraps);

    //START:: Payment with Store card detail
    $('body').on('click', '.card_detail', function(e) {
        if($(this).val()=="old"){
            $('#payButton').hide();
            $('#payWithCardDetailButton').show();
        }else{
            $('#payWithCardDetailButton').hide();
            $('#payButton').show();
        }

    });
    $('body').on('click', '#payWithCardDetailButton', function(e) {
        e.preventDefault();
        //check if terms and condition checked
        if ($('#term_and_condition_check').is(':checked')) {
                }else{
                    $("#warning_box").html('Please agree to the terms and conditions').show().delay(5000).fadeOut('slow');
                    return false;
                }
                if($('#pickup_address').val() == ''){
                    $("#warning_box").html('Please enter pickup address').show().delay(5000).fadeOut('slow');
                    return false;
                }
                if($('#drop_off_address').val() == ''){
                    $("#warning_box").html('Please enter drop off address').show().delay(5000).fadeOut('slow');
                    return false;
                }
                //--->
            var tkn = "{{ csrf_token() }}";
            $('#paymentDetails').hide();
            $('#payProcess').val(1);    
            var pickup_address = $("#pickup_address").val();
            var drop_off_address = $("#drop_off_address").val();
                $.ajax({
                    url: '/paymentCharge',
                    type: 'POST',
                    data: {'_token':tkn,'stripeCustomerId':'Y', 
                    'deposit_required': "{{ $deposit_required }}", 
                    'processing_fee': "{{ $processing_fee }}",
                    'invoice_id': "{{ $invoice_id }}", 
                    'booking_fee': "{{ $booking_fee }}", 
                    'job_id': "{{ $job->job_id }}", 
                    'sys_job_type': "{{ $sys_job_type }}", 
                    'jobNumber': "{{ $job->job_number }}",
                    'tenant_id': "{{ $job->tenant_id }}",
                    'pickup_address': pickup_address,
                    'drop_off_address': drop_off_address
                },
                    dataType: "json",
                    beforeSend: function(){
                        $(this).prop('disabled', true);
                        $(this).html('Please wait...');
                    },
                    success: function(data){
                        $('#payProcess').val(0);                        
                        if(data.status == 1){
                            if(data.is_redirect==1){
                                window.open(data.inventory_form_url,'_self');
                            }
                            $('#buynow').hide();
                            $('#transaction_tab').removeClass('active2');
                            $('#confirmation_tab').addClass('active2');
                            $('#paymentForm').toggle();
                            $('#paymentDetails').toggle();
                        } else {
                            $(this).prop('disabled', false);
                            $(this).html('Pay Now');
                            $("#error_box").html(data.msg).show().delay(5000).fadeOut('slow');
                        }
                    },
                    error: function(data) {
                        $('#payProcess').val(0);
                        $(this).prop('disabled', false);
                        $(this).html('Pay Now');
                        $("#error_box").show().delay(5000).fadeOut('slow');
                    }
                });        
    });
    //END:: Payment with Store card detail
    });
    $(document).on('change', '#pickup_address', function() {
        setTimeout(function() {
            var newval = $('#pickup_address').val().replace(', Australia', '');
            $('#pickup_address').val(newval);
        }, 10);
    });
    $(document).on('change', '#drop_off_address', function() {
        setTimeout(function() {
            var newval = $('#drop_off_address').val().replace(', Australia', '');
            $('#drop_off_address').val(newval);
        }, 10);
    });
    function createGeoListeners(autocompletesWraps) {
            var options = {types: ['geocode'],componentRestrictions: {country: "au"}};
            var inputs = $('.geo-address');
            var autocompletes = [];
            for (var i = 0; i < inputs.length; i++) {
                var autocomplete = new google.maps.places.Autocomplete(inputs[i], options);
                autocomplete.inputId = inputs[i].id;
                autocomplete.parentDiv = autocompletesWraps[i];
                autocomplete.addListener('place_changed', fillInAddressFields);
                inputs[i].addEventListener("focus", function() {
                    geoLocate(autocomplete);
                }, false);
                autocompletes.push(autocomplete);
            }
        }
        function fillInAddressFields() {
            $('.googleerror').removeClass('is-valid is-invalid');
            var place = this.getPlace();
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                var val = place.address_components[i].long_name;
                //console.log("address Type " + addressType + " val " + val + " pd " + this.parentDiv);
                $('#'+this.parentDiv).find("."+addressType).val(val);
                $('#'+this.parentDiv).find("."+addressType).attr('disabled', false);
            }
        }
        function geoLocate(autocomplete) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var geolocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    var circle = new google.maps.Circle({
                        center: geolocation,
                        radius: position.coords.accuracy
                    });
                    autocomplete.setBounds(circle.getBounds());
                });
            }
        }
        function gm_authFailure() { 
            $('.gm-err-autocomplete').addClass('is-invalid');
            swal("Error","There is a problem with the Google Maps or Places API","error");
        };

    // $(document).on('keyup', '.geo-address', function() {
    //     setTimeout(function() {
    //         var newval = $(this).val().replace(', Australia', '');
    //         $(this).val(newval);
    //     }, 10);
    // });
</script>
<script type="text/jscript" src="https://checkout.stripe.com/checkout.js"></script>
{{-- <script src="https://checkout.stripe.com/checkout.js" 
      class="stripe-button" 
      data-key="" 
      data-image="" 
      data-name="{{ $organisation->company_name }}" 
      data-description=""
      data-amount="{{ $deposit_required*100 }}">
    </script> --}}
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
                $('#paymentDetails').hide();
                $('#payProcess').val(1);    
                var pickup_address = $("#pickup_address").val();
                var drop_off_address = $("#drop_off_address").val();
                $.ajax({
                    url: '/paymentCharge',
                    type: 'POST',
                    data: {'_token':tkn,stripeToken: token.id, stripeEmail: token.email, 
                    'deposit_required': "{{ $deposit_required }}", 
                    'processing_fee': "{{ $processing_fee }}",
                    'invoice_id': "{{ $invoice_id }}", 
                    'booking_fee': "{{ $booking_fee }}", 
                    'job_id': "{{ $job->job_id }}", 
                    'sys_job_type': "{{ $sys_job_type }}", 
                    'jobNumber': "{{ $job->job_number }}",
                    'tenant_id': "{{ $job->tenant_id }}",
                    'pickup_address': pickup_address,
                    'drop_off_address': drop_off_address
                },
                    dataType: "json",
                    beforeSend: function(){
                        $('#payButton').prop('disabled', true);
                        $('#payButton').html('Please wait...');
                    },
                    success: function(data){
                        $('#payProcess').val(0);                        
                        if(data.status == 1){
                            if(data.is_redirect==1){
                                window.open(data.inventory_form_url,'_self');
                            }
                            $('#buynow').hide();
                            $('#transaction_tab').removeClass('active2');
                            $('#confirmation_tab').addClass('active2');
                            $('#paymentForm').toggle();
                            $('#paymentDetails').toggle();
                        } else {
                            $('#payButton').prop('disabled', false);
                            $('#payButton').html('Pay Now');
                            $("#error_box").html(data.msg).show().delay(5000).fadeOut('slow');
                        }
                    },
                    error: function(data) {
                        $('#payProcess').val(0);
                        $('#payButton').prop('disabled', false);
                        $('#payButton').html('Pay Now');
                        $("#error_box").show().delay(5000).fadeOut('slow');
                    }
                });
            }
        });
        
            var stripe_closed = function(){
                var processing = $('#payProcess').val();
                if (processing == 0){
                    $('#payButton').prop('disabled', false);
                    $('#payButton').html('Pay Now');
                }
            };
        
        var eventTggr = document.getElementById('payButton');
        if(eventTggr){
            eventTggr.addEventListener('click', function(e) {
                //check if terms and condition checked
                if ($('#term_and_condition_check').is(':checked')) {
                }else{
                    $("#warning_box").html('Please agree to the terms and conditions').show().delay(5000).fadeOut('slow');
                    return false;
                }
                if($('#pickup_address').val() == ''){
                    $("#warning_box").html('Please enter pickup address').show().delay(5000).fadeOut('slow');
                    return false;
                }
                if($('#drop_off_address').val() == ''){
                    $("#warning_box").html('Please enter drop off address').show().delay(5000).fadeOut('slow');
                    return false;
                }
                //--->

                $('#payButton').prop('disabled', true);
                $('#payButton').html('Please wait...');
                
                // Open Checkout with further options:
                handler.open({
                    name: '{{ $organisation->company_name }}',
                    description: 'Job Confirmation Payment',
                    email: '{{ $email }}',
                    amount: "{{ $deposit_required*100 }}",

                    closed:	stripe_closed
                });
                e.preventDefault();
            });
        }

        // $("#stripe-pay").click(function(e){
    //     handler.open({
    //                     name: "{{ $organisation->company_name }}",
    //                     //  description: '2 widgets',
    //                     amount: "{{ $deposit_required*100 }}",
    //                 });

    //                 // Close Checkout on page navigation
    //                 $(window).on('popstate', function () {
    //                     handler.close();
    //                 });
    // });

    </script>
    <script type="text/javascript">
        window._mfq = window._mfq || [];
        (function() {
        var mf = document.createElement("script");
        mf.type = "text/javascript"; mf.defer = true;
        mf.src = "//cdn.mouseflow.com/projects/3ba9387b-26cd-4cbd-b530-381fb72e7879.js";
        document.getElementsByTagName("head")[0].appendChild(mf);
        })();
        </script>
</body>
</html>