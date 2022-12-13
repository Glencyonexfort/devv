@extends('layouts.front.app')
@section('content')
<div class="register-box">
    <div class="">
        <a href="javascript:void(0)" class="text-center m-b-40">
            <img
                    src="{{url('/user-uploads/app-logo/ZBsBf7tHSYR1eKIMhUDwsIhf3B57GAiKW8uP7C25.png')}}"
                    alt="Home" />
        </a>
        <!-- multistep form -->
        <!-- <form class="form-horizontal form-material" name="register_form" id="msform" method="post" enctype="multipart/form-data" action="http://ec2-52-65-201-83.ap-southeast-2.compute.amazonaws.com/paasfort/en/registration/create_customer"> -->
        <form
                action="{{url('/registration')}}"
                method="post"
                accept-charset="utf-8"
                class="form-horizontal form-material"
                id="msform"
                enctype="multipart/form-data">
            @csrf
            <!-- progressbar -->
            {{--<ul id="eliteregister">--}}
                {{--<li class="active">Business Intro</li>--}}
                {{--<li>Business Details</li>--}}
                {{--<li>Signup Details</li>--}}
            {{--</ul>--}}
            <!-- fieldsets -->
            {{--<fieldset>--}}
                {{--<h2 class="fs-title">Tell us about your business</h2>--}}
                {{--<h3 class="fs-subtitle">We'll use this information to personalise your quotes and invoices.</h3>--}}

                {{--<label class="pull-left">Business Name</label>--}}
                {{--<input--}}
                        {{--type="text"--}}
                        {{--required--}}
                        {{--name="business_name" id="business_name" placeholder="Business Name" value="Glue Stick.com" />--}}

                {{--<label class="pull-left">Business Address 1</label>--}}
                {{--<input type="text" name="business_address1" id="business_address1" placeholder="Business Address 1"  value="" />--}}

                {{--<label class="pull-left">Business Address 2</label>--}}
                {{--<input type="text" name="business_address2" id="business_address2" placeholder="Business Address 2"  value="" />--}}

                {{--<div class="col-lg-12 pull-left p-0">--}}

                    {{--<div class="col-lg-7 pull-left p-0">--}}
                        {{--<label class="pull-left">City</label>--}}
                        {{--<input type="text" class="pull-left" name="city" id="city" placeholder="City Name"  value="" />--}}
                    {{--</div>--}}

                    {{--<div class="col-lg-5 pull-left p-r-0">--}}
                        {{--<label class="pull-left">Postcode</label>--}}
                        {{--<input type="text" name="postcode" id="postcode" placeholder="Postcode"  value="" />--}}
                    {{--</div>--}}


                    {{--<div class="col-lg-6 pull-left p-0">--}}
                        {{--<label class="pull-left">State</label>--}}
                        {{--<input type="text" class="pull-left" name="state" id="state" placeholder="State"  value="" />--}}
                    {{--</div>--}}

                    {{--<div class="col-lg-6 pull-left p-r-0">--}}
                        {{--<label class="pull-left">Country</label>--}}
                        {{--<select name="country" class="chzn-select" id="country">--}}
                            {{--<option value="">Select Country</option>--}}
                            {{--<option  value="241">Afghanistan</option>--}}
                            {{--<option  value="243">Albania</option>--}}
                            {{--<option  value="244">Algeria</option>--}}
                            {{--<option  value="245">American Samoa</option>--}}
                            {{--<option  value="246">Andorra</option>--}}
                            {{--<option  value="1">Angola</option>--}}
                            {{--<option  value="2">Anguilla</option>--}}
                            {{--<option  value="3">Antarctica</option>--}}
                            {{--<option  value="4">Antigua and Barbuda</option>--}}
                            {{--<option  value="5">Argentina</option>--}}
                            {{--<option  value="6">Armenia</option>--}}
                            {{--<option  value="7">Aruba</option>--}}
                            {{--<option  value="8">Australia</option>--}}
                            {{--<option  value="9">Austria</option>--}}
                            {{--<option  value="10">Azerbaijan</option>--}}
                            {{--<option  value="11">Bahamas</option>--}}
                            {{--<option  value="12">Bahrain</option>--}}
                            {{--<option  value="13">Bangladesh</option>--}}
                            {{--<option  value="14">Barbados</option>--}}
                            {{--<option  value="15">Belarus</option>--}}
                            {{--<option  value="16">Belgium</option>--}}
                            {{--<option  value="17">Belize</option>--}}
                            {{--<option  value="18">Benin</option>--}}
                            {{--<option  value="19">Bermuda</option>--}}
                            {{--<option  value="20">Bhutan</option>--}}
                            {{--<option  value="21">Bolivia, Plurinational State of</option>--}}
                            {{--<option  value="22">Bosnia and Herzegovina</option>--}}
                            {{--<option  value="23">Botswana</option>--}}
                            {{--<option  value="24">Bouvet Island</option>--}}
                            {{--<option  value="25">Brazil</option>--}}
                            {{--<option  value="26">British Indian Ocean Territory</option>--}}
                            {{--<option  value="27">Brunei Darussalam</option>--}}
                            {{--<option  value="28">Bulgaria</option>--}}
                            {{--<option  value="29">Burkina Faso</option>--}}
                            {{--<option  value="30">Burundi</option>--}}
                            {{--<option  value="31">Cambodia</option>--}}
                            {{--<option  value="32">Cameroon</option>--}}
                            {{--<option  value="33">Canada</option>--}}
                            {{--<option  value="34">Cape Verde</option>--}}
                            {{--<option  value="35">Cayman Islands</option>--}}
                            {{--<option  value="36">Central African Republic</option>--}}
                            {{--<option  value="37">Chad</option>--}}
                            {{--<option  value="38">Chile</option>--}}
                            {{--<option  value="39">China</option>--}}
                            {{--<option  value="40">Christmas Island</option>--}}
                            {{--<option  value="41">Cocos (Keeling) Islands</option>--}}
                            {{--<option  value="42">Colombia</option>--}}
                            {{--<option  value="43">Comoros</option>--}}
                            {{--<option  value="44">Congo</option>--}}
                            {{--<option  value="45">Congo, the Democratic Republic of the</option>--}}
                            {{--<option  value="46">Cook Islands</option>--}}
                            {{--<option  value="47">Costa Rica</option>--}}
                            {{--<option  value="48">Cote d'Ivoire</option>--}}
                            {{--<option  value="49">Croatia</option>--}}
                            {{--<option  value="50">Cuba</option>--}}
                            {{--<option  value="51">Cyprus</option>--}}
                            {{--<option  value="52">Czech Republic</option>--}}
                            {{--<option  value="53">Denmark</option>--}}
                            {{--<option  value="54">Djibouti</option>--}}
                            {{--<option  value="55">Dominica</option>--}}
                            {{--<option  value="56">Dominican Republic</option>--}}
                            {{--<option  value="57">Ecuador</option>--}}
                            {{--<option  value="58">Egypt</option>--}}
                            {{--<option  value="59">El Salvador</option>--}}
                            {{--<option  value="60">Equatorial Guinea</option>--}}
                            {{--<option  value="61">Eritrea</option>--}}
                            {{--<option  value="62">Estonia</option>--}}
                            {{--<option  value="63">Ethiopia</option>--}}
                            {{--<option  value="64">Falkland Islands (Malvinas)</option>--}}
                            {{--<option  value="65">Faroe Islands</option>--}}
                            {{--<option  value="66">Fiji</option>--}}
                            {{--<option  value="67">Finland</option>--}}
                            {{--<option  value="68">France</option>--}}
                            {{--<option  value="69">French Guiana</option>--}}
                            {{--<option  value="70">French Polynesia</option>--}}
                            {{--<option  value="71">French Southern Territories</option>--}}
                            {{--<option  value="72">Gabon</option>--}}
                            {{--<option  value="73">Gambia</option>--}}
                            {{--<option  value="74">Georgia</option>--}}
                            {{--<option  value="75">Germany</option>--}}
                            {{--<option  value="76">Ghana</option>--}}
                            {{--<option  value="77">Gibraltar</option>--}}
                            {{--<option  value="78">Greece</option>--}}
                            {{--<option  value="79">Greenland</option>--}}
                            {{--<option  value="80">Grenada</option>--}}
                            {{--<option  value="81">Guadeloupe</option>--}}
                            {{--<option  value="82">Guam</option>--}}
                            {{--<option  value="83">Guatemala</option>--}}
                            {{--<option  value="84">Guernsey</option>--}}
                            {{--<option  value="85">Guinea</option>--}}
                            {{--<option  value="86">Guinea-Bissau</option>--}}
                            {{--<option  value="87">Guyana</option>--}}
                            {{--<option  value="88">Haiti</option>--}}
                            {{--<option  value="89">Heard Island and McDonald Islands</option>--}}
                            {{--<option  value="90">Holy See (Vatican City State)</option>--}}
                            {{--<option  value="91">Honduras</option>--}}
                            {{--<option  value="92">Hong Kong</option>--}}
                            {{--<option  value="93">Hungary</option>--}}
                            {{--<option  value="94">Iceland</option>--}}
                            {{--<option  value="95">India</option>--}}
                            {{--<option  value="96">Indonesia</option>--}}
                            {{--<option  value="97">Iran, Islamic Republic of</option>--}}
                            {{--<option  value="98">Iraq</option>--}}
                            {{--<option  value="99">Ireland</option>--}}
                            {{--<option  value="100">Isle of Man</option>--}}
                            {{--<option  value="101">Israel</option>--}}
                            {{--<option  value="102">Italy</option>--}}
                            {{--<option  value="103">Jamaica</option>--}}
                            {{--<option  value="104">Japan</option>--}}
                            {{--<option  value="105">Jersey</option>--}}
                            {{--<option  value="106">Jordan</option>--}}
                            {{--<option  value="107">Kazakhstan</option>--}}
                            {{--<option  value="108">Kenya</option>--}}
                            {{--<option  value="109">Kiribati</option>--}}
                            {{--<option  value="110">Korea, Democratic People's Republic of</option>--}}
                            {{--<option  value="111">Korea, Republic of</option>--}}
                            {{--<option  value="112">Kuwait</option>--}}
                            {{--<option  value="113">Kyrgyzstan</option>--}}
                            {{--<option  value="114">Lao People's Democratic Republic</option>--}}
                            {{--<option  value="115">Latvia</option>--}}
                            {{--<option  value="116">Lebanon</option>--}}
                            {{--<option  value="117">Lesotho</option>--}}
                            {{--<option  value="118">Liberia</option>--}}
                            {{--<option  value="119">Libyan Arab Jamahiriya</option>--}}
                            {{--<option  value="120">Liechtenstein</option>--}}
                            {{--<option  value="121">Lithuania</option>--}}
                            {{--<option  value="122">Luxembourg</option>--}}
                            {{--<option  value="123">Macao</option>--}}
                            {{--<option  value="124">Macedonia, the former Yugoslav Republic of</option>--}}
                            {{--<option  value="125">Madagascar</option>--}}
                            {{--<option  value="126">Malawi</option>--}}
                            {{--<option  value="127">Malaysia</option>--}}
                            {{--<option  value="128">Maldives</option>--}}
                            {{--<option  value="129">Mali</option>--}}
                            {{--<option  value="130">Malta</option>--}}
                            {{--<option  value="131">Marshall Islands</option>--}}
                            {{--<option  value="132">Martinique</option>--}}
                            {{--<option  value="133">Mauritania</option>--}}
                            {{--<option  value="134">Mauritius</option>--}}
                            {{--<option  value="135">Mayotte</option>--}}
                            {{--<option  value="136">Mexico</option>--}}
                            {{--<option  value="137">Micronesia, Federated States of</option>--}}
                            {{--<option  value="138">Moldova, Republic of</option>--}}
                            {{--<option  value="139">Monaco</option>--}}
                            {{--<option  value="140">Mongolia</option>--}}
                            {{--<option  value="141">Montenegro</option>--}}
                            {{--<option  value="142">Montserrat</option>--}}
                            {{--<option  value="143">Morocco</option>--}}
                            {{--<option  value="144">Mozambique</option>--}}
                            {{--<option  value="145">Myanmar</option>--}}
                            {{--<option  value="146">Namibia</option>--}}
                            {{--<option  value="147">Nauru</option>--}}
                            {{--<option  value="148">Nepal</option>--}}
                            {{--<option  value="149">Netherlands</option>--}}
                            {{--<option  value="150">Netherlands Antilles</option>--}}
                            {{--<option  value="151">New Caledonia</option>--}}
                            {{--<option  value="152">New Zealand</option>--}}
                            {{--<option  value="153">Nicaragua</option>--}}
                            {{--<option  value="154">Niger</option>--}}
                            {{--<option  value="155">Nigeria</option>--}}
                            {{--<option  value="156">Niue</option>--}}
                            {{--<option  value="157">Norfolk Island</option>--}}
                            {{--<option  value="158">Northern Mariana Islands</option>--}}
                            {{--<option  value="159">Norway</option>--}}
                            {{--<option  value="242">Oland Islands</option>--}}
                            {{--<option  value="160">Oman</option>--}}
                            {{--<option  value="161">Pakistan</option>--}}
                            {{--<option  value="162">Palau</option>--}}
                            {{--<option  value="163">Palestinian Territory, Occupied</option>--}}
                            {{--<option  value="164">Panama</option>--}}
                            {{--<option  value="165">Papua New Guinea</option>--}}
                            {{--<option  value="166">Paraguay</option>--}}
                            {{--<option  value="167">Peru</option>--}}
                            {{--<option  value="168">Philippines</option>--}}
                            {{--<option  value="169">Pitcairn</option>--}}
                            {{--<option  value="170">Poland</option>--}}
                            {{--<option  value="171">Portugal</option>--}}
                            {{--<option  value="172">Puerto Rico</option>--}}
                            {{--<option  value="173">Qatar</option>--}}
                            {{--<option  value="174">R?©union</option>--}}
                            {{--<option  value="175">Romania</option>--}}
                            {{--<option  value="176">Russian Federation</option>--}}
                            {{--<option  value="177">Rwanda</option>--}}
                            {{--<option  value="178">Saint Barth?©lemy</option>--}}
                            {{--<option  value="179">Saint Helena</option>--}}
                            {{--<option  value="180">Saint Kitts and Nevis</option>--}}
                            {{--<option  value="181">Saint Lucia</option>--}}
                            {{--<option  value="182">Saint Martin (French part)</option>--}}
                            {{--<option  value="183">Saint Pierre and Miquelon</option>--}}
                            {{--<option  value="184">Saint Vincent and the Grenadines</option>--}}
                            {{--<option  value="185">Samoa</option>--}}
                            {{--<option  value="186">San Marino</option>--}}
                            {{--<option  value="187">Sao Tome and Principe</option>--}}
                            {{--<option  value="188">Saudi Arabia</option>--}}
                            {{--<option  value="189">Senegal</option>--}}
                            {{--<option  value="190">Serbia</option>--}}
                            {{--<option  value="191">Seychelles</option>--}}
                            {{--<option  value="192">Sierra Leone</option>--}}
                            {{--<option  value="193">Singapore</option>--}}
                            {{--<option  value="194">Slovakia</option>--}}
                            {{--<option  value="195">Slovenia</option>--}}
                            {{--<option  value="196">Solomon Islands</option>--}}
                            {{--<option  value="197">Somalia</option>--}}
                            {{--<option  value="198">South Africa</option>--}}
                            {{--<option  value="199">South Georgia and the South Sandwich Islands</option>--}}
                            {{--<option  value="200">Spain</option>--}}
                            {{--<option  value="201">Sri Lanka</option>--}}
                            {{--<option  value="202">Sudan</option>--}}
                            {{--<option  value="203">Suriname</option>--}}
                            {{--<option  value="204">Svalbard and Jan Mayen</option>--}}
                            {{--<option  value="205">Swaziland</option>--}}
                            {{--<option  value="206">Sweden</option>--}}
                            {{--<option  value="207">Switzerland</option>--}}
                            {{--<option  value="208">Syrian Arab Republic</option>--}}
                            {{--<option  value="209">Taiwan, Province of China</option>--}}
                            {{--<option  value="210">Tajikistan</option>--}}
                            {{--<option  value="211">Tanzania, United Republic of</option>--}}
                            {{--<option  value="212">Thailand</option>--}}
                            {{--<option  value="213">Timor-Leste</option>--}}
                            {{--<option  value="214">Togo</option>--}}
                            {{--<option  value="215">Tokelau</option>--}}
                            {{--<option  value="216">Tonga</option>--}}
                            {{--<option  value="217">Trinidad and Tobago</option>--}}
                            {{--<option  value="218">Tunisia</option>--}}
                            {{--<option  value="219">Turkey</option>--}}
                            {{--<option  value="220">Turkmenistan</option>--}}
                            {{--<option  value="221">Turks and Caicos Islands</option>--}}
                            {{--<option  value="222">Tuvalu</option>--}}
                            {{--<option  value="223">Uganda</option>--}}
                            {{--<option  value="224">Ukraine</option>--}}
                            {{--<option  value="225">United Arab Emirates</option>--}}
                            {{--<option  value="226">United Kingdom</option>--}}
                            {{--<option  value="227">United States</option>--}}
                            {{--<option  value="228">United States Minor Outlying Islands</option>--}}
                            {{--<option  value="229">Uruguay</option>--}}
                            {{--<option  value="230">Uzbekistan</option>--}}
                            {{--<option  value="231">Vanuatu</option>--}}
                            {{--<option  value="232">Venezuela, Bolivarian Republic of</option>--}}
                            {{--<option  value="233">Viet Nam</option>--}}
                            {{--<option  value="234">Virgin Islands, British</option>--}}
                            {{--<option  value="235">Virgin Islands, U.S.</option>--}}
                            {{--<option  value="236">Wallis and Futuna</option>--}}
                            {{--<option  value="237">Western Sahara</option>--}}
                            {{--<option  value="238">Yemen</option>--}}
                            {{--<option  value="239">Zambia</option>--}}
                            {{--<option  value="240">Zimbabwe</option>--}}
                        {{--</select>--}}

                        {{--<!-- <input type="text" name="country" id="country" placeholder="country"  value="" /> -->--}}
                    {{--</div>--}}
                {{--</div>--}}

                {{--<label class="pull-left">Business Logo</label>--}}
                {{--<input type="file" id="file-input" name="business_logo" class="dropify" data-height="100" data-max-file-size="500K" />--}}
                {{--<input type="button" name="next" class="next action-button" value="Next" />--}}
            {{--</fieldset>--}}
            {{--<fieldset>--}}
                {{--<h2 class="fs-title">What does your business do?</h2>--}}
                {{--<h3 class="fs-subtitle">This will help us customise the account to work best for your industry and size.</h3>--}}
                {{--<label class="pull-left">Industry</label>--}}
                {{--<select name="business_industry">--}}
                    {{--<!-- <option value="">Select Industry</option> -->--}}
                    {{--<option value="9">Business Support Services</option>--}}
                    {{--<option value="1">Commercial Cleaning Services</option>--}}
                    {{--<option value="2">Dining & Catering Services</option>--}}
                    {{--<option value="3">Equipment Repair services</option>--}}
                    {{--<option value="10">Goods and Passenger Land Transportation</option>--}}
                    {{--<option value="5">Industrial Auctioneers</option>--}}
                    {{--<option value="4">Industrial Maintenance Services</option>--}}
                    {{--<option value="12">Moving Services</option>--}}
                    {{--<option value="6">Storage & Warehousing</option>--}}
                    {{--<option value="7">Transaction Services</option>--}}
                    {{--<option value="8">Uniform Rental Services</option>--}}
                    {{--<option value="11">Vehicle Rental</option>--}}

                {{--</select>--}}

                {{--<label class="pull-left">Business Phone Number</label>--}}
                {{--<input type="text" required name="business_phone" placeholder="Business Phone Number" value="+61349049502" />--}}

                {{--<label class="pull-left">Business Email Address</label>--}}
                {{--<input type="text" required name="business_email" placeholder="Business Email Address" value="anvar.zackaria@gmail.com" />--}}

                {{--<label class="pull-left">Number of Employees</label>--}}
                {{--<input type="number" required name="business_size" id="business_size" placeholder="Number of Employees" min="1">--}}
                {{--<!-- <ul class="icheck-list">--}}
                   {{--<li>--}}
                      {{--<input type="radio" class="check" id="flat-radio-1" name="business_size" data-radio="iradio_flat-red" value="sole_trader" checked>--}}
                      {{--<label for="flat-radio-1">Sole Trader/Proprieter</label>--}}
                   {{--</li>--}}
                   {{--<li>--}}
                      {{--<input type="radio" class="check" value="2_3_staff" id="flat-radio-2" name="business_size"  data-radio="iradio_flat-red">--}}
                      {{--<label for="flat-radio-2">2 - 3 Staff</label>--}}
                   {{--</li>--}}

                   {{--<li>--}}
                      {{--<input type="radio" class="check" value="4_10_staff" name="business_size" data-radio="iradio_flat-red">--}}
                      {{--<label for="flat-radio-2">4 - 10 Staff</label>--}}
                   {{--</li>--}}

                   {{--<li>--}}
                      {{--<input type="radio" class="check" value="10_plus_staff" name="business_size" data-radio="iradio_flat-red">--}}
                      {{--<label for="flat-radio-2">10+ Staff</label>--}}
                   {{--</li>--}}
                {{--</ul> -->--}}


                {{--<input type="button" name="previous" class="previous action-button" value="Previous" />--}}
                {{--<input type="button" name="next" class="next action-button" value="Next" />--}}
            {{--</fieldset>--}}
            <fieldset>
                <h2 class="fs-title">Setup up your details</h2>
                <h3 class="fs-subtitle">This will be used on your quotes, invoices and email signature.</h3>

                <label class="pull-left">Your First Name</label>
                <input
                        type="text"
                        required
                        name="first_name"
                        id="first_name"
                        placeholder="First Name"
                        value="{{old('first_name')}}" />

                <label class="pull-left">Your Last Name</label>
                <input
                        type="text"
                        required
                        name="last_name"
                        id="last_name"
                        placeholder="Last Name"
                        value="{{old('last_name')}}" />

                <label class="pull-left">Your Email</label>
                <input
                        type="email"
                        required
                        name="email"
                        id="email"
                        placeholder="Email"
                        value="{{old('email')}}" />

                <label class="pull-left">Your Business Name</label>
                <input
                        type="text"
                        required
                        name="business_name"
                        id="business_name"
                        placeholder="Business Name"
                        value="{{old('business_name')}}" />

                <label class="pull-left">Your Phone Number</label>
                <input
                        type="text"
                        required
                        name="business_phone"
                        placeholder="Business Phone Number"
                        value="{{old('business_phone')}}" />

                <label class="pull-left">Your Address</label>
                <input
                        type="text"
                        name="business_address"
                        id="business_address"
                        placeholder="Business Address"
                        value="{{old('business_address')}}" />

                <label class="pull-left">Your Address</label>
                <input
                        type="text"
                        name="plan_id"
                        id="plan_id"
                        placeholder="Plan Id"
                        value="{{old('plan_id')}}" />

                <label class="pull-left">City</label>
                <input
                        type="text"
                        class="pull-left"
                        name="city"
                        id="city"
                        placeholder="City Name"
                        value="{{old('city')}}" />

                <label class="pull-left">State</label>
                <input
                        type="text"
                        class="pull-left"
                        name="state"
                        id="state"
                        placeholder="State"
                        value="{{old('state')}}" />

                <label class="pull-left">Country</label>
                <select name="country" class="chzn-select" id="country">
                    <option value="">Select Country</option>
                    @foreach($countries as $country)
                    <option
                            value="{{$country->id}}"{{old('country') == $country->id ? ' selected' : ''}}>{{$country->country_name}}</option>
                    @endforeach
                </select>

                <label class="pull-left">Enter Password</label>
                <input
                        type="password"
                        name="password"
                        value=""
                        id="password"
                        class="form-control"
                        placeholder="New password"
                        required="required"  />
                <span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span>

                <label class="pull-left">Confirm Password</label>
                <input
                        type="password"
                        name="confirmpassword"
                        value=""
                        id="confirmpassword"
                        class="form-control"
                        placeholder="Confirm New Password"
                        required="required"  />

                {{--<label class="pull-left">Web Address</label>--}}
                {{--<input type="text" name="web_address" placeholder="Website Address" />--}}

                {{--<label class="pull-left">ABN</label>--}}
                {{--<input type="text" name="abn" placeholder="00 000 000 000" />--}}


                {{--<ul class="icheck-list">--}}
                    {{--<li>--}}
                        {{--<input type="checkbox" name="gst_registered" value="1" class="check" id="gst_registered" checked data-checkbox="icheckbox_flat-red">--}}
                        {{--<label for="flat-checkbox-2">Are you GST registered?</label>--}}
                    {{--</li>--}}
                {{--</ul>--}}

                <!-- <div style="width: 100%" class="pull-left">
                 <input type="checkbox" value="1" class="check pull-left" checked name="gst_registered" style="width: 10%; height: 20px;">
                 <label class="pull-left">Are you GST registered?</label>
                </div> -->

                {{--<input type="hidden" name="csrf_test_name" value="d3a9a737c4e050f41c37a56799171386">--}}

                {{--<input type="hidden" name="plan_id" value="cbdemo_scale">--}}
                {{--<input type="hidden" name="plan_name" value="Premium">--}}
                {{--<input type="hidden" name="subscription_id" value="1t0Aw4DRi9nOWo1f1e">--}}
                {{--<input type="hidden" name="subscription_status" value="In Trial">--}}

                {{--<input type="button" name="previous" class="previous action-button" value="Previous" />--}}
                <input type="submit" name="submit" class="submit action-button" value="Submit" />
                <!-- onClick="validatePassword();" -->
            </fieldset>
        </form>                <div class="clear"></div>
    </div>
</div>
@endsection