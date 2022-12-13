<!doctype html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <style type="text/css">
        @font-face {
            font-family: OpenSans;
            src: url('{{asset("quote-assets/fonts/Open_Sans/OpenSans-Regular.ttf")}}');
        }
        @font-face {
            font-family: OpenSansBold;
            src: url('{{asset("quote-assets/fonts/Open_Sans/OpenSans-SemiBold.ttf")}}');
        }

        body {
            font-family: OpenSans;
        }

        .wizard {
            font-family: OpenSans;
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
            /* color: #555555;
            cursor: default; */
        }

        /* .wizard li {
            width: 30px;
            height: 30px;
            line-height: 27px;
            display: inline-block;
            background: #fff;
            border: 1px solid #c1c1c1;
            z-index: 2;
            left: 0;
            text-align: center;
            font-size: 14px;
            color: #c1c1c1;
            border-right: 0px;
            font-weight: 500;
        }

        .wizard li:last-child {
            border-right: 1px solid #c1c1c1;
        } */

        /* span.round-tab i {
            color: #555555;
        }

        .wizard li.active {
            width: 90px;
            background: #dc2832;
            border: 1px solid #dc2832;
            border-radius: 1px;
            color: #fff;
            text-align: left;
            padding-left: 10px;
        } */

        /* .wizard li.active::after {
            content: '';
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 15px 0 15px 20px;
            border-color: transparent transparent transparent #007bff;
        } */



        /* .wizard li.active span.round-tab i {
            color: #5bc0de;
        }

        span.round-tab:hover {
            color: #333;
            border: 2px solid #333;
        } */

        .wizard .nav-tabs>li {
            /* width: 25%; */
        }

        .wizard .nav-tabs>li a {
            width: 30px;
            height: 30px;
            /* margin: 10px auto;
            border-radius: 100%; */
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

        .nav-tabs>li.active>a,
        .nav-tabs>li.active>a:hover,
        .nav-tabs>li.active>a:focus {
            color: #555;
            cursor: default;
            /* background-color: #fff; */
            border: 0px solid #ddd;
            /* border-bottom-color: transparent; */
        }

        .nav-tabs>li>a {
            margin-right: 0px;
            line-height: 1.42857143;
            border: 0px solid transparent;
            border-radius: 0px;
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

        .img-container {
            border: 2px solid #e7e7e7;
            padding: 15px 5px;
            text-align: center;
            border-radius: 5px;
        }

        .img-container img {
            width: 45%;
        }

        .img-container p {
            text-align: center;
            font-size: 10px;
            padding-top: 10px;
            font-weight: bold;
            margin: 0;
            color: #c1c1c1;
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
            width: auto;
            height: auto;
            border: none;
            font-size: 10px;
            width: 47%;
            margin: 0px;
            line-height: 12px;
            padding: 0px;
            text-align: left;
            color: #787878;
            margin-bottom: 10px;
            vertical-align: top;
        }

        .checklist li:last-child {
            border-right: 0px;
        }


        ul.special li {
            display: block;
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
            width: auto;
            height: auto;
            text-align: left;
            color: #414141;
        }

        ul.checkoptions li:hover {
            border: 1px solid #dc2832;
        }

        ul.checkoptions li:hover::before {
            color: #dc2832;
        }

        ul.checkoptions li.active {
            border: 1px solid #dc2832;
            background: transparent;
            color: #414141;
            width: auto;
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
            color: #dc2832 !important;
        }

        .moveFromType:hover {
            cursor: pointer;
            /* border: 2px solid #dc2832; */
        }

        .moveFromType.active {
            cursor: pointer;
            border: 2px solid #dc2832;
        }

        .moveToType:hover {
            /* cursor: pointer;
            border: 2px solid #dc2832; */
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

        .get_qutoe_box {
            background-image: url('{{ asset("quote-assets/quote.png") }}');
            background-position: left top;
            background-repeat: no-repeat;
            background-size: contain;
            padding-left: 55px;
            margin: 15px 0px;
        }

        .get_qutoe_box h1 {
            padding: 0px;
            margin: 0px;
            font-size: 22px;
            color: #484848;
            margin-bottom: 5px;
        }

        .get_qutoe_box p {
            padding: 0px;
            margin: 0px;
            font-size: 14px;
            color: #787878;
        }

        .title_box {
            margin: 15px 0px;
            font-family: OpenSansBold;
        }

        .title_box h1 {
            padding: 0px;
            margin: 0px;
            font-size: 22px;
            color: #484848;
            margin-bottom: 10px;
        }

        .title_box p {
            padding: 0px;
            margin: 0px;
            font-size: 14px;
            color: #787878;
        }


        .form-label label {
            font-family: OpenSans;
            color: #414141;
            font-size: 14px;
            font-weight: normal;
        }

        .bold-label {
            font-family: OpenSansBold;
            color: #414141;
            font-size: 14px;
            font-weight: normal;
        }

        .travel-line {
            background-image: url('{{ asset("quote-assets/vline.png") }}');
            height: 60px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: top center;
            margin-bottom: 10px;
        }

        .location-text,
        .location-text:focus,
        .location-text:active,
        .location-text:visited {
            border: 0px;
            box-shadow: none;
            border-bottom: 1px solid #dc2832aa;
            border-radius: 0px;
            font-family: OpenSans;
            font-size: 14px;
        }

        .location-icon {
            background-image: url('{{ asset("quote-assets/search.png") }}');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center right;
            background-origin: content-box;
        }

        .list-inline li {
            border: none !important;
            width: auto;
            height: auto;
            padding: 0;
            margin: 0;
        }


        .btn-next-step,
        .btn-next-step:hover,
        .btn-next-step:active,
        .btn-next-step:visited,
        .btn-next-step:focus {
            text-transform: capitalize;
            background: #1a6de2;
            color: #fff;
            padding: 15px 30px;
            -webkit-box-shadow: 0px 0px 10px 0px rgba(26, 109, 226, 1);
            -moz-box-shadow: 0px 0px 10px 0px rgba(26, 109, 226, 1);
            box-shadow: 0px 0px 10px 0px rgba(26, 109, 226, 1);
            border: none;
            font-size: 12px;
        }

        .btn-back-step,
        .btn-back-step:hover,
        .btn-back-step:active,
        .btn-back-step:visited,
        .btn-back-step:focus {
            color: #1a6de2;
            border: none;
            font-size: 12px;
            line-height: 30px;
            background: none;
            box-shadow: none;
        }

        .btn-check-icon {
            height: 12px;
            margin-left: 20px;
        }


        .margin-top-40 {
            margin-top: 40px;
        }

        .margin-top-10 {
            margin-top: 10px;
        }

        .home-icon {
            background-image: url('{{ asset("quote-assets/home.png") }}');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center center;
            background-origin: content-box;
            height: 35px;
        }

        .building-icon {
            background-image: url('{{ asset("quote-assets/building.png") }}');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center center;
            background-origin: content-box;
            height: 35px;
        }

        .storage-icon {
            background-image: url('{{ asset("quote-assets/storage.png") }}');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center center;
            background-origin: content-box;
            height: 35px;
        }

        .img-container.active .home-icon {
            background-image: url('{{ asset("quote-assets/home-hover.png") }}');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center center;
            background-origin: content-box;
            height: 35px;
        }

        .img-container.active .building-icon {
            background-image: url('{{ asset("quote-assets/building-hover.png") }}');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center center;
            background-origin: content-box;
            height: 35px;
        }

        .img-container.active .storage-icon {
            background-image: url('{{ asset("quote-assets/storage-hover.png") }}');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center center;
            background-origin: content-box;
            height: 35px;
        }

        .img-container.active p {
            color: #dc2832;
        }

        .redline {
            border-bottom: 1px solid #EBEBEB;
            height: 20px;
        }

        .text-style {
            line-height: 30px;
            color: #414141;
        }

        #preloadedImages {
            width: 0px;
            height: 0px;
            display: inline;
            background-image: url('{{ asset("quote-assets/building-hover.png") }}');
            background-image: url('{{ asset("quote-assets/storage-hover.png") }}');
            background-image: url('{{ asset("quote-assets/home-hover.png") }}');
            background-image: url('{{ asset("quote-assets/building.png") }}');
            background-image: url('{{ asset("quote-assets/storage.png") }}');
            background-image: url('{{ asset("quote-assets/home.png") }}');
            background-image: url('{{ asset("quote-assets/search.png") }}');
            background-image: url('{{ asset("quote-assets/quote.png") }}');
            background-image: url('{{ asset("quote-assets/vline.png") }}');
            background-image: url();

        }
        

    @media only screen and (max-width: 550px) {
        .get_qutoe_box {
            padding-left: 70px;
        }

        .img-container {
            min-height: 90px;
        }

        .btn-next-step,
        .btn-next-step:hover,
        .btn-next-step:active,
        .btn-next-step:visited,
        .btn-next-step:focus {
            padding: 10px 10px;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <section>
            <div class="wizard">
                @if($step != '0')
                @if($step != '5')
                <div class="wizard-inner">
                    @if($step == '4')
                    <img src="{{ asset('quote-assets/step4.png') }}" style="height: 40px;">
                    @elseif($step == '3')
                    <img src="{{ asset('quote-assets/step3.png') }}" style="height: 40px;">
                    @elseif($step == '2')
                    <img src="{{ asset('quote-assets/step2.png') }}" style="height: 40px;">
                    @else
                    <img src="{{ asset('quote-assets/step1.png') }}" style="height: 40px;">
                    @endif
                    <!-- <ul class="nav nav-tabs">
                        <li class="{{ $step >= '1'?'active':'' }}">1</li>
                        <li class="{{ $step >= '2'?'active':'' }}">2</li>
                        <li class="{{ $step >= '3'?'active':'' }}">3</li>
                        <li class="{{ $step >= '4'?'active':'' }}">4</li>
                    </ul> -->
                </div>
                @endif
                <div class="tab-content">
                    @if($step == '2')
                    @include('quote.step2')
                    @elseif($step == '3')
                    @if(($step2_ary['moving_from_type'] ?? '') == 'Storage Facility')
                    @include('quote.step3-3')
                    @elseif(($step2_ary['moving_from_type'] ?? '') == 'Flat')
                    @include('quote.step3-2')
                    @else
                    @include('quote.step3-1')
                    @endif
                    @elseif($step == '4')
                    @include('quote.step4')
                    @elseif($step == '5')
                    @include('quote.success')
                    @else
                    @include('quote.step1')
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