<!doctype html>
<html>
<head>
    <meta name="viewport" content="width=1024" />
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

    <link rel="stylesheet"
        href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <style type="text/css">
        @font-face {
            font-family: Popins;
            src: url('{{ asset('quote-assets/fonts/popins/Poppins-Regular.ttf') }}');
        }

        @font-face {
            font-family: PopinsBold;
            src: url('{{ asset('quote-assets/fonts/popins/Poppins-Bold.ttf') }}');
        }

        * {
            outline: none !important;
        }

        body {
            outline: none !important;
            font-family: Popins;
        }

        .wizard {
            font-family: Popins;
        }


        .wizard {
            width: 100%;
            height: auto !important;
            margin: 0px auto;
            background: #fff;
        }


        .wizard .nav-tabs {
            position: relative;
            margin: 10px auto;
            margin-bottom: 0;
            /* border-bottom-color: #e0e0e0; */
            border-bottom: 0px;
            height: 30px;
        }

        .wizard>div.wizard-inner {
            position: relative;
            padding-top: 20px;
        }


        .connecting-line {
            height: 2px;
            background: #dc2832;
            position: absolute;
            width: 75%;
            margin: 0 auto;
            left: 0;
            right: 0;
            top: 50%;
            z-index: 1;
        }

        .wizard .nav-tabs>li.active>a,
        .wizard .nav-tabs>li.active>a:hover,
        .wizard .nav-tabs>li.active>a:focus {
            color: #555555;
            cursor: default;
            border: 0;
            border-bottom-color: transparent;
        }

        span.round-tab {
            width: 30px;
            height: 30px;
            line-height: 27px;
            display: inline-block;
            border-radius: 100px;
            background: #fff;
            border: 2px solid #dc2832;
            z-index: 2;
            position: absolute;
            left: 0;
            text-align: center;
            font-size: 14px;
            color: #dc2832;
        }

        span.round-tab i {
            color: #555555;
        }

        .wizard li.active span.round-tab {
            background: #dc2832;
            border: 2px solid #dc2832;
            color: #ffff;
        }

        .wizard li.active span.round-tab i {
            color: #5bc0de;
        }

        span.round-tab:hover {
            color: #333;
            border: 2px solid #333;
        }

        .wizard .nav-tabs>li {
            width: 25%;
        }

        .wizard .nav-tabs>li a {
            width: 30px;
            height: 30px;
            margin: 10px auto;
            border-radius: 100%;
            padding: 0;
        }

        .wizard .nav-tabs>li a:hover {
            background: transparent;
        }

        .wizard .tab-pane {
            position: relative;
            padding-top: 10px;
        }

        .wizard h3 {
            margin-top: 0;
        }

        .main_title {
            font-size: 18px;
            color: #1B685F;
            font-weight: bold;
            text-align: center;
        }

        .font-bold {
            font-size: 14px;
            color: #1B685F;
            font-weight: bold;
        }

        .hr-bottom {
            /* border-bottom: 1px solid #1B685F; */
            padding-bottom: 15px;
        }

        .hr-top {
            /* border-top: 1px solid #e7e7e7; */
            padding-top: 10px;
        }

        .nav-tabs li a:hover {
            cursor: default;
            color: #dc2832;
        }

        .round-tab:hover {
            cursor: default;
            color: #dc2832;
        }

        /* .btn-next-step {
            text-transform: uppercase;
            background: #1a6de2;
            color: #fff;
            padding: 8px 20px;
        } */

        .btn-next-step,
        .btn-next-step:hover,
        .btn-next-step:active,
        .btn-next-step:visited,
        .btn-next-step:focus {
            text-transform: capitalize;
            background: #FF798D;
            color: #fff;
            padding: 15px 50px;
            /* -webkit-box-shadow: 0px 0px 10px 0px rgba(26, 109, 226, 1);
            -moz-box-shadow: 0px 0px 10px 0px rgba(26, 109, 226, 1);
            box-shadow: 0px 0px 10px 0px rgba(26, 109, 226, 1); */
            border: none;
            font-size: 12px;
            border-radius: 25px;
            outline: none;
        }


        .btn-next-success {
            background: #4cae4c;
            color: #fff;
        }

        .img-container {
            border: 2px solid #e7e7e7;
            padding: 5px;
            text-align: center;
        }

        .img-container img {
            width: 50%;
        }

        .img-container p {
            text-align: center;
            font-size: 10px;
            padding-top: 10px;
            font-weight: bold;
        }

        label {
            margin-bottom: 0px;
            font-weight: 700;
            font-size: 11px;
        }

        .form-group {
            margin-bottom: 5px;
        }

        .form-group-sm {
            margin-bottom: 5px;
        }

        .form-control {
            border: 1px solid #1B685F;
            border-radius: 7px;
            font-size: 12px;
        }

        .input-number {
            font-size: 12px;
            border: none;
            color: #1B685F;
            font-weight: bold;
        }

        .input-group-btn .btn-number {
            border-radius: 50% !important;
            margin: 0px !important;
            padding: 4px 7px;
            font-size: 10px;
            border-color: #ffffff;
            color: #ffffff;
            background: #1B685F;
        }

        .input-group-btn .btn-number:hover {
            background: none !important;
            border-color: #1B685F;
            color: #1B685F;
            background: #ffffff;
        }

        .input-group .form-control {
            box-shadow: none !important;
        }

        .inputaddon .form-control {
            border-right: none;
        }

        .inputaddon .input-group-addon {
            background: none;
            border: 1px solid #1B685F;
            border-left: none;
        }

        ul.checklist {
            list-style: none;
            padding: 0px;
            margin: 0px;
        }

        ul.checklist li {
            display: inline-block;
            margin-right: 15px;
        }

        ul.checkoptions {
            list-style: none;
            padding: 0px;
            margin: 0px;
        }

        ul.checkoptions li {
            display: block;
            margin-right: 15px;
            border: 1px solid #c7c7c7;
            margin-bottom: 10px;
            border-radius: 5px;
            padding: 5px;
            cursor: pointer;
        }

        ul.checkoptions li:hover {
            border: 1px solid #dc2832;
        }

        ul.checkoptions li:hover::before {
            color: #dc2832;
        }

        ul.checkoptions li.active {
            border: 1px solid #dc2832;
        }

        ul.checkoptions li.active::before {
            color: #dc2832;
        }

        ul.checkoptions li:before {
            font: normal normal normal 14px/1 FontAwesome;
            -webkit-font-smoothing: antialiased;
            content: "\f10c";
            width: 40px;
            display: inline-block;
            text-align: center;
            vertical-align: top;
            font-size: 20px;
            margin-top: 10px;
            color: #b7b7b7;
        }

        ul.checkoptions p {
            display: inline-block;
            margin: 0;
            padding: 0;
            font-weight: 700;
        }

        ul.checkoptions p span {
            font-weight: normal;
        }

        .margin-top-30 {
            margin-top: 30px;
        }

        .margin-top-40 {
            margin-top: 40px;
        }

        .margin-top-10 {
            margin-top: 10px;
        }

        .text-red {
            color: #dc2832;
        }

        .moveFromType:hover {
            cursor: pointer;
            border: 2px solid #dc2832;
        }

        .moveFromType.active {
            cursor: pointer;
            border: 2px solid #dc2832;
        }

        .moveToType:hover {
            cursor: pointer;
            border: 2px solid #dc2832;
        }

        .moveToType.active {
            cursor: pointer;
            border: 2px solid #dc2832;
        }

        .big_error {
            font-size: 22px;
            text-align: center;
            color: #dc2832;
        }

        .btn-number {
            padding: 6px 6px;
        }

        .input-number {
            text-align: center;
        }

        .extraBox {
            border-bottom: 1px solid #b9b9b9;
            padding-top: 5px;
        }

        .extraBox:last-child {
            border-bottom: 0px;
        }

        .input-group-btn>.btn {
            outline: none;
        }

        .input-group-btn>.btn:active {
            outline: none;
        }

        .input-group-btn>.btn:visited {
            outline: none;
        }

        .input-group-btn>.btn:hover {
            outline: none;
        }

        .panel-summary {
            border: none;
            box-shadow: none;
        }

        .panel-summary .panel-heading {
            background: #fff;
            text-align: center;
            color: #1B685F;
            font-size: 22px;
            border: none;
            font-weight: bold;
            margin-bottom: 40px;
        }

        .panel-summary .panel-body {
            font-size: 14px;
        }

        .panel-summary .panel-body p {
            font-size: 12px;
            color: #1B685F;
            vertical-align: middle;
        }

        .panel-summary .panel-body .fa {
            font-size: 14px;
            color: #1B685F;
            margin-right: 15px;
        }

        .panel-summary .panel-footer {
            background: #ffffff;
            font-size: 16px;
            line-height: 20px;
            border: none;
            font-weight: bold;
        }

        .panel-summary .panel-footer strong {
            font-size: 20px;
            color: #FF798D;
            float: right
        }


        .pagetitle h1 {
            color: #1B685F;
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0px;
        }

        .pagetitle p {
            color: #1B685F;
            font-size: 11px;
        }

        .extraBox .form-group {
            margin: 0px;
            padding: 0px;
        }

        .extraBox .extra_title {
            margin: 0px;
            font-size: 12px;
            padding: 10px;
        }

        .btn-send-quote,
        .btn-send-quote:hover,
        .btn-send-quote:active,
        .btn-send-quote:visited,
        .btn-send-quote:focus {
            background: #394763 !important;
        }

        #preloadedImages {
            width: 0px;
            height: 0px;
            display: inline;
            background-image: url('{{ asset("quote-assets/lease_step1.png") }}');
            background-image: url('{{ asset("quote-assets/lease_step2.png") }}');
            background-image: url('{{ asset("quote-assets/lease_step3.png") }}');
            background-image: url('{{ asset("quote-assets/lease_step4.png") }}');
            background-image: url('{{ asset("quote-assets/quote.png") }}');
            background-image: url();
        }

        @media only screen and (max-width: 460px) {

        .btn-next-step,
        .btn-next-step:hover,
        .btn-next-step:active,
        .btn-next-step:visited,
        .btn-next-step:focus {
            padding: 10px 20px;
        }

        .extraBox {
            padding-top: 0px;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <section>
            @if ($step != '6' && $step != '7')
                {{-- <h1 class="main_title hr-bottom ">Book Now</h1>
                --}}
            @endif
            <div class="wizard">
                @if ($step != '0')
                    @if ($step != '5' && $step != '6' && $step != '7')
                        @if ($step != '5')
                            <div class="wizard-inner">
                                @if ($step == '4')
                                    <img src="{{ asset('quote-assets/lease_step4.png') }}" style="height: 40px;">
                                @elseif($step == '3')
                                    <img src="{{ asset('quote-assets/lease_step3.png') }}" style="height: 40px;">
                                @elseif($step == '2')
                                    <img src="{{ asset('quote-assets/lease_step2.png') }}" style="height: 40px;">
                                @else
                                    <img src="{{ asset('quote-assets/lease_step1.png') }}" style="height: 40px;">
                                @endif
                            </div>
                        @endif
                    @endif
                    <div class="tab-content">
                        @if ($step == '2')
                            @include('quote-lease-cleaning.step2')
                        @elseif($step == '3')
                            @include('quote-lease-cleaning.step3')
                        @elseif($step == '4')
                            @include('quote-lease-cleaning.step4')
                        @elseif($step == '5')
                            @include('quote-lease-cleaning.summary')
                        @elseif($step == '6')
                            @include('quote-lease-cleaning.pay-later')
                        @elseif($step == '7')
                            @include('quote-lease-cleaning.pay-now')
                        @else
                            @include('quote-lease-cleaning.step1')
                        @endif
                        <div class="clearfix"></div>
                    </div>
                @else
                    <div class="big_error">Something went wrong, <br>try again later.</div>
                @endif
            </div>
        </section>
    </div>
    <div class="preloadedImages"></div>
</body>

</html>
