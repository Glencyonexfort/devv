<!doctype html>
<html>

<head>
    <meta name="viewport" content="width=1024" />
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <style type="text/css">
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
        }

        .wizard>div.wizard-inner {
            position: relative;
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
            color: #dc2832;
            font-weight: bold;
        }

        .font-bold {
            font-weight: bold;
        }

        .hr-bottom {
            border-bottom: 1px solid #e7e7e7;
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

        .btn-next-step {
            text-transform: uppercase;
            background: #1a6de2;
            color: #fff;
            padding: 8px 20px;
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

        .form-control {
            font-size: 12px;
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

        .panel-summary {}

        .panel-summary .panel-heading {
            background: #fff;
            text-align: center;
            color: #1a6de2;
            font-size: 18px;
        }

        .panel-summary .panel-body {
            font-size: 14px;
        }

        .panel-summary .panel-body p {
            font-size: 18px;
            color: #333;
            vertical-align: middle;
        }

        .panel-summary .panel-body .fa {
            font-size: 22px;
            color: #888;
            margin-right: 15px;
        }

        .panel-summary .panel-footer {
            background: #ffffff;
            font-size: 14px;
            line-height: 20px;
        }

        .panel-summary .panel-footer strong {
            font-size: 20px;
            color: #4cae4c;
            float: right;
        }
    </style>
</head>

<body>
    <div class="container">
        <section>
            @if($step != '6' && $step != '7')
            <h1 class="main_title hr-bottom ">Book Now</h1>
            @endif
            <div class="wizard">
                @if($step != '0')
                @if($step != '5' && $step != '6' && $step != '7')
                <div class="wizard-inner">
                    <div class="connecting-line"></div>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="{{ $step >= '1'?'active':'' }}"><a href="#"><span class="round-tab">1</span></a></li>
                        <li role="presentation" class="{{ $step >= '2'?'active':'' }}"><a href="#"><span class="round-tab">2 </span></a></li>
                        <li role="presentation" class="{{ $step >= '3'?'active':'' }}"><a href="#"><span class="round-tab">3</span></a></li>
                        <li role="presentation" class="{{ $step >= '4'?'active':'' }}"><a href="#"><span class="round-tab">4</span></a></li>
                    </ul>
                </div>
                @endif
                <div class="tab-content">
                    @if($step == '2')
                    @include('quote-cleaning.step2')
                    @elseif($step == '3')
                    @include('quote-cleaning.step3')
                    @elseif($step == '4')
                    @include('quote-cleaning.step4')
                    @elseif($step == '5')
                    @include('quote-cleaning.summary')
                    @elseif($step == '6')
                    @include('quote-cleaning.pay-later')
                    @elseif($step == '7')
                    @include('quote-cleaning.pay-now')
                    @else
                    @include('quote-cleaning.step1')
                    @endif
                    <div class="clearfix"></div>
                </div>
                @else
                <div class="big_error">Something went wrong, <br>try again later.</div>
                @endif
            </div>
        </section>
    </div>
</body>

</html>