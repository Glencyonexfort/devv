<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="icon" sizes="192x192" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="icon" sizes="32x32" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="icon" sizes="96x96" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="icon" sizes="16x16" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="manifest" href="{{ asset('favicon/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('favicon/favicon.ico') }}">
    <meta name="theme-color" content="#ffffff">
    <title>
        @if (Auth::check())
        {{ $setting->company_name }}
        @else
        Onexfort | The Connected Business Software
        @endif
    </title>
    <!-- Bootstrap Core CSS -->
    <link href="{{ asset('bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">    
    
    <!-- animation CSS -->
    <link href="{{ asset('css/animate.css') }}" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <!-- color CSS -->
    <link href="{{ asset('css/colors/blue.css') }}" id="theme" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin-custom.css') }}" rel="stylesheet">
    <link href="{{ asset('newassets/css/bootstrap_limitless.min.css') }}" rel="stylesheet"/>
    <script src="{{ asset('newassets/global_assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>

    @if(!is_null($setting->login_background))
        <style>
            .login-register {
                background: url("{{ $setting->login_background_url }}") center center/cover no-repeat !important;
            }
        </style>
    @endif


<?php
//dd(env('APP_ENV'));
if(env('APP_ENV')=='codecanyon'){
if (isset($_SERVER['HTTPS']) &&
    ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
    $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
  $protocol = 'https://';
}
else {
  $protocol = 'http://';
}
$notssl = 'http://';
if($protocol==$notssl){
    $url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";?>
    <script> 
    window.location.href ='<?php echo $url?>';
    </script> 
 <?php } } ?>
</head>
<body>
<!-- Preloader -->
<div class="preloader">
    <div class="cssload-speeding-wheel"></div>
</div>
<section id="wrapper" class="login-register">
    <div class="login-box" style="margin:8% auto 0 15%;">

        <!--This is dark logo icon-->
        <a href="javascript:void(0)"
           style="margin-top:10px;padding-top:25px; @if($setting->active_theme == 'custom') background: #292929; @endif"

           class="text-center db"><img src="{{ asset('logo-dark.png') }}" style="max-width: 200px" alt="Onexfort"/></a>

        <div class="white-box">
            @yield('content')
        </div>
    </div>
</section>

<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
<!-- Menu Plugin JavaScript -->
<script src="{{ asset('plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js') }}"></script>

<!--slimscroll JavaScript -->
<script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
<!--Wave Effects -->
<script src="{{ asset('js/waves.js') }}"></script>
<!-- Custom Theme JavaScript -->
<script src="{{ asset('js/custom.min.js') }}"></script>
<!--Style Switcher -->
<script src='https://www.google.com/recaptcha/api.js'></script>
</body>
</html>
