@extends('layouts.front.app')
@section('content')
{{-- {!! NoCaptcha::renderJs() !!} --}}
    <div class="register-box">
        <div class="">
            <a href="javascript:void(0)" class="text-center m-b-40">
            </a>
            @if(Session::has('message'))
                <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
            @endif
            <!-- multistep form -->
            <form
                    action="{{url('/registration')}}"
                    method="post"
                    accept-charset="utf-8"
                    class="form-horizontal form-material"
                    id="msform"
                    enctype="multipart/form-data">
                @csrf
                <!-- progressbar -->
                <img src="{{ asset('onexfort-logo-light.png') }}" alt="Onexfort"/>
                <ul id="eliteregister">
                    <li class="active">Business Intro</li>
                    <li>Business Details</li>
                    <li>Signup Details</li>
                </ul>
                <!-- fieldsets -->
                <fieldset>
                    <h2 class="fs-title">Tell us about your business</h2>
                    <h3 class="fs-subtitle">We'll use this information to personalise your quotes and invoices.</h3>

                    <label class="pull-left">Business Name</label>
                    <input
                            type="text"
                            required
                            name="business_name"
                            id="business_name"
                            placeholder="Business Name"
                            value="{{ app('request')->input('cust_business') ? app('request')->input('cust_business') : old('business_name') }}" />

                    <label class="pull-left">Business Address 1</label>
                    <input
                            type="text"
                            required
                            name="business_address1"
                            id="business_address1"
                            placeholder="Business Address 1"
                            value="{{old('business_address1')}}" />

                    <label class="pull-left">Business Address 2</label>
                    <input
                            type="text"
                            name="business_address2"
                            id="business_address2"
                            placeholder="Business Address 2"
                            value="{{old('business_address2')}}" />

                    <div class="col-lg-12 pull-left p-0">
                        <div class="col-lg-7 pull-left p-0">
                            <label class="pull-left">City</label>
                            <input
                                    type="text"
                                    required
                                    class="pull-left"
                                    name="city"
                                    id="city"
                                    placeholder="City Name"
                                    value="{{old('city')}}" />
                        </div>

                        <div class="col-lg-5 pull-left p-r-0">
                            <label class="pull-left">Postcode</label>
                            <input
                                    type="text"
                                    required
                                    name="postcode"
                                    id="postcode"
                                    placeholder="Postcode"
                                    value="{{old('postcode')}}" />
                        </div>


                        <div class="col-lg-6 pull-left p-0">
                            <label class="pull-left">State</label>
                            <input
                                    type="text"
                                    required
                                    class="pull-left"
                                    name="state"
                                    id="state"
                                    placeholder="State"
                                    value="{{old('state')}}" />
                        </div>

                        <div class="col-lg-6 pull-left p-r-0">
                            <label class="pull-left">Country</label>
                            <select name="country" class="chzn-select" id="country">
                                @foreach($countries as $country)
                                    <option
                                            value="{{$country->country_id}}" {{old('country') == $country->country_id ? ' selected' : ''}}>{{$country->country_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <label class="pull-left">Business Logo</label>
                    <input
                            type="file"
                            id="file-input"
                            name="business_logo"
                            class="dropify"
                            data-height="100"
                            data-max-file-size="500K" />
                    <input type="button" name="next" class="next action-button" value="Next" />
                </fieldset>
                <fieldset>
                    <h2 class="fs-title">What does your business do?</h2>
                    <h3 class="fs-subtitle">This will help us customise the account to work best for your industry and size.</h3>
                    <label class="pull-left">Module</label>
                    <select name="sys_module_id">
                        <!-- <option value="">Select Industry</option> -->
                        <?php //foreach ($business_industries as $value) { ?>
                        <!-- <option value="<?php //echo $value->business_category_id; ?>"><?php //echo $value->business_category_name; ?></option> -->
                        <?php //} ?>
                        <?php foreach ($sys_modules as $value) { ?>
                        <option value="<?php echo $value->id; ?>"><?php echo $value->module_name; ?></option>
                        <?php } ?>
                    </select>

                    <label class="pull-left">Business Phone Number</label>
                    <input
                            type="text"
                            required
                            name="business_phone"
                            placeholder="Business Phone Number"
                            value="{{old('business_phone')}}" />

                    <label class="pull-left">Number of Employees</label>
                    <input
                            type="number"
                            required
                            name="business_size"
                            id="business_size"
                            placeholder="Number of Employees"
                            min="1">
                    <!-- <ul class="icheck-list">
                       <li>
                          <input type="radio" class="check" id="flat-radio-1" name="business_size" data-radio="iradio_flat-red" value="sole_trader" checked>
                          <label for="flat-radio-1">Sole Trader/Proprieter</label>
                       </li>
                       <li>
                          <input type="radio" class="check" value="2_3_staff" id="flat-radio-2" name="business_size"  data-radio="iradio_flat-red">
                          <label for="flat-radio-2">2 - 3 Staff</label>
                       </li>

                       <li>
                          <input type="radio" class="check" value="4_10_staff" name="business_size" data-radio="iradio_flat-red">
                          <label for="flat-radio-2">4 - 10 Staff</label>
                       </li>

                       <li>
                          <input type="radio" class="check" value="10_plus_staff" name="business_size" data-radio="iradio_flat-red">
                          <label for="flat-radio-2">10+ Staff</label>
                       </li>
                    </ul> -->


                    <input type="button" name="previous" class="previous action-button" value="Previous" />
                    <input type="button" name="next" class="next action-button" value="Next" />
                </fieldset>
                <fieldset id="business_detail_box">
                    <h2 class="fs-title">Setup up your business details</h2>
                    <h3 class="fs-subtitle">This will be used on your quotes, invoices and email signature.</h3>

                    <label class="pull-left">Your First Name</label>
                    <input
                            type="text"
                            required
                            name="first_name"
                            id="first_name"
                            placeholder="First Name"
                            value="{{ app('request')->input('firstname') ? app('request')->input('firstname') : old('first_name') }}" />

                    <label class="pull-left">Your Last Name</label>
                    <input
                            type="text"
                            required
                            name="last_name"
                            id="last_name"
                            placeholder="Last Name"
                            value="{{ app('request')->input('lastname') ? app('request')->input('lastname') : old('last_name') }}" />

                    <label class="pull-left">Business Email Address</label>
                    <input
                            type="email"
                            required
                            name="business_email"
                        id="business_email"
                            placeholder="Business Email Address"
                            value="{{ app('request')->input('email') ? app('request')->input('email') : old('business_email') }}" />

                    <label class="pull-left">Enter Password</label>
                    <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control"
                            placeholder="New password"
                            required="required"  />
                    <span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span>

                    <label class="pull-left">Confirm Password</label>
                    <input
                            type="password"
                            name="confirmpassword"
                            id="confirmpassword"
                            class="form-control"
                            placeholder="Confirm New Password"
                            required="required"  />

                    <label class="pull-left">Your Mobile Number</label>
                    <input
                            type="text"
                            required
                            name="mobile"
                            id="mobile"
                            placeholder="Mobile Number"
                            value="{{ app('request')->input('mobile') ? app('request')->input('mobile') : old('mobile') }}" />


                    <label class="pull-left">Web Address</label>
                    <input
                            type="text"
                            name="web_address"
                            placeholder="Website Address" />

                    <label class="pull-left">ABN</label>
                    <input
                            type="text"
                            name="abn"
                            placeholder="00 000 000 000"
                            value="{{old('abn')}}" />


                    <ul class="icheck-list">
                        <li>
                            <input
                                    type="checkbox"
                                    name="gst_registered"
                                    value="1"
                                    class="check"
                                    id="gst_registered"
                                    checked
                                    data-checkbox="icheckbox_flat-red">
                            <label for="flat-checkbox-2">Are you GST registered?</label>
                        </li>
                    </ul>

                    <input type="hidden" name="plan_id" value="<?php echo $plan_id;?>">
                    <input type="hidden" name="subscription_id" value="<?php echo $subscription_id;?>">

                    {{-- {!! NoCaptcha::display() !!} --}}

                    <input type="button" name="previous" class="previous action-button" value="Previous" style="margin-top: 5rem;"/>
                    <input name="submit" class="next action-button verify_email_btn" value="Verify Email"/>
                    <!-- onClick="validatePassword();" -->
                </fieldset>
                <fieldset id="otp_verify_box">
                        <div id="opt_field_box">
                        <h2 class="fs-title" style="font-size: 16px;font-weight: 600;">One-time Passcode</h2>
                        <h3 class="fs-subtitle">You will be receiving an email that contains the one-time passcode to continue with your registration process. Please make sure to check the SPAM/Junk folders of your email. This code will expire in 5 minutes. If you don't receive the email, click on the <a class="verify_email_btn" href="javascript:void(0)">Send Again</a>.</h3>                        
                          <div class="alert alert-warning" id="opt_errors" style="display: none"></div>
                                <input type="text" name="otp_code" id="otp_code" placeholder="Enter OTP" required/>   
                                <input id="registration_submit_btn" name="submit" class="submit action-button" value="Submit" style="text-align: center;"/>
                                <input id="registration_submit_form" type="submit" value="Submit" style="display:none"/>
                        </div>
                        <div id="success_box" style="display: none">
                                <h2 class="fs-title" style="font-size: 16px;font-weight: 600;">Please Wait....</h2>
                                <h3>Registration process will take few minutes to complete.</h3>
                        </div>
                </fieldset>
            </form>
            <div class="clear"></div>
        </div>
    </div>
@endsection