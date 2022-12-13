<!DOCTYPE html>

<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
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

    <title>@lang('app.adminPanel') | {{ $pageTitle }}</title>
    <!-- Bootstrap Core CSS -->
    <!-- <link href="{{ asset('bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet"> -->
    <link rel='stylesheet prefetch'
          href='https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/0.8.2/css/flag-icon.min.css'>
    
    <link rel='stylesheet prefetch'
          href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/css/bootstrap-select.min.css'>
          <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <!-- This is Sidebar menu CSS -->
    <link href="{{ asset('plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">

    <link href="{{ asset('plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/bower_components/sweetalert/sweetalert.css') }}" rel="stylesheet">

    <!-- This is a Animation CSS -->
    <link href="{{ asset('css/animate.css') }}" rel="stylesheet">

    <!-- Limitless head star -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="{{ asset('newassets/global_assets/css/icons/icomoon/styles.min.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('newassets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('newassets/css/bootstrap_limitless.min.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('newassets/css/layout.min.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('newassets/css/components.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('newassets/css/colors.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('newassets/css/custom.css?v=1') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('newassets/css/lead-style.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('newassets/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('newassets/css/newasset_custom.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('newassets/css/pipeline_custom.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300&display=swap" rel="stylesheet">

	<script src="{{ asset('newassets/global_assets/js/main/jquery.min.js') }}"></script>
    <script src="{{ asset('newassets/global_assets/js/plugins/ui/moment/moment.min.js') }}"></script>
	<script src="{{ asset('newassets/global_assets/js/main/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('newassets/global_assets/js/plugins/loaders/blockui.min.js') }}"></script>
    
	<script src="{{ asset('newassets/global_assets/js/plugins/visualization/d3/d3.min.js') }}"></script>
	<script src="{{ asset('newassets/global_assets/js/plugins/visualization/d3/d3_tooltip.js') }}"></script>
	<script src="{{ asset('newassets/global_assets/js/plugins/forms/styling/switchery.min.js') }}"></script>
    
	<script src="{{ asset('newassets/js/app.js') }}"></script>
	<script src="{{ asset('newassets/js/custom.js') }}"></script>
	<script src="{{ asset('newassets/global_assets/js/demo_pages/dashboard.js') }}"></script>
	<script src="{{ asset('newassets/global_assets/js/demo_charts/pages/dashboard/light/streamgraph.js') }}"></script>
	<script src="{{ asset('newassets/global_assets/js/demo_charts/pages/dashboard/light/sparklines.js') }}"></script>
	<script src="{{ asset('newassets/global_assets/js/demo_charts/pages/dashboard/light/lines.js') }}"></script>	
	<script src="{{ asset('newassets/global_assets/js/demo_charts/pages/dashboard/light/areas.js') }}"></script>
	<script src="{{ asset('newassets/global_assets/js/demo_charts/pages/dashboard/light/donuts.js') }}"></script>
	<script src="{{ asset('newassets/global_assets/js/demo_charts/pages/dashboard/light/bars.js') }}"></script>
	<script src="{{ asset('newassets/global_assets/js/demo_charts/pages/dashboard/light/progress.js') }}"></script>
	<script src="{{ asset('newassets/global_assets/js/demo_charts/pages/dashboard/light/heatmaps.js') }}"></script>
	<script src="{{ asset('newassets/global_assets/js/demo_charts/pages/dashboard/light/pies.js') }}"></script>
    <script src="{{ asset('newassets/global_assets/js/demo_charts/pages/dashboard/light/bullets.js') }}"></script>
    

@stack('head-script')

<!-- This is a Custom CSS -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <!-- color CSS you can use different color css from css/colors folder -->
    <!-- We have chosen the skin-blue (default.css) for this starter
       page. However, you can choose any other skin from folder css / colors .
       -->
    <!-- <link href="{{ asset('css/colors/default.css') }}" id="theme" rel="stylesheet"> -->
    <link href="{{ asset('plugins/froiden-helper/helper.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/magnific-popup.css') }}">
    <!-- <link href="{{ asset('css/custom.css') }}" rel="stylesheet"> -->

    @if(file_exists(public_path().'/css/admin-custom.css'))
        <!-- <link href="{{ asset('css/admin-custom.css') }}" rel="stylesheet"> -->
    @endif


<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    {{-- @if($pushSetting->status == 'active')
        <link rel="manifest" href="{{ asset('manifest.json') }}" />
        <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async='async'></script>
        <script>
            var OneSignal = window.OneSignal || [];
            OneSignal.push(function() {
                OneSignal.init({
                    appId: "{{ $pushSetting->onesignal_app_id }}",
                    autoRegister: false,
                    notifyButton: {
                        enable: false,
                    },
                    promptOptions: {
                        /* actionMessage limited to 90 characters */
                        actionMessage: "We'd like to show you notifications for the latest news and updates.",
                        /* acceptButtonText limited to 15 characters */
                        acceptButtonText: "ALLOW",
                        /* cancelButtonText limited to 15 characters */
                        cancelButtonText: "NO THANKS"
                    }
                });
                OneSignal.on('subscriptionChange', function (isSubscribed) {
                    console.log("The user's subscription state is now:", isSubscribed);
                });

                if (Notification.permission === "granted") {
                    // Automatically subscribe user if deleted cookies and browser shows "Allow"
                    OneSignal.getUserId()
                        .then(function(userId) {
                            if (!userId) {
                                OneSignal.registerForPushNotifications();
                            }
                            else{
                                let db_onesignal_id = '{{ $user->onesignal_player_id }}';
                                if(db_onesignal_id == null || db_onesignal_id !== userId){ //update onesignal ID if it is new
                                    updateOnesignalPlayerId(userId);
                                }
                            }
                        })
                } else {
                    OneSignal.isPushNotificationsEnabled(function(isEnabled) {
                        if (isEnabled){
                            console.log("Push notifications are enabled! - 2    ");
                            // console.log("unsubscribe");
                            // OneSignal.setSubscription(false);
                        }
                        else{
                            console.log("Push notifications are not enabled yet. - 2");
                            // OneSignal.showHttpPrompt();
                            // OneSignal.registerForPushNotifications({
                            //         modalPrompt: true
                            // });
                        }
                        OneSignal.getUserId(function(userId) {
                            console.log("OneSignal User ID:", userId);
                            // (Output) OneSignal User ID: 270a35cd-4dda-4b3f-b04e-41d7463a2316
                            let db_onesignal_id = '{{ $user->onesignal_player_id }}';
                            console.log('database id : '+db_onesignal_id);

                            if(db_onesignal_id == null || db_onesignal_id !== userId){ //update onesignal ID if it is new
                                updateOnesignalPlayerId(userId);
                            }
                        });
                        OneSignal.showHttpPrompt();
                    });

                }
            });
        </script>
    @endif --}}

    @if($global->active_theme == 'custom')
        <!--{{--Custom theme styles--}}-->
        <style>
            
            .navbar-header {
                background: {{ $adminTheme->header_color }};
            }
            .sidebar .notify  {
                margin: 0 !important;
            }
            .sidebar .notify .heartbit {
                border: 5px solid {{ $adminTheme->header_color }} !important;
                top: -23px !important;
                right: -15px !important;
            }
            .sidebar .notify .point {
                background-color: {{ $adminTheme->header_color }} !important;
                top: -13px !important;
            }
            .navbar-top-links > li > a {
                color: {{ $adminTheme->link_color }};
            }
            /*Right panel*/
            .right-sidebar .rpanel-title {
                background: {{ $adminTheme->header_color }};
            }
            /*Bread Crumb*/
            .bg-title .breadcrumb .active {
                color: {{ $adminTheme->header_color }};
            }
            /*Sidebar*/
            .sidebar {
                background: {{ $adminTheme->sidebar_color }};
                box-shadow: 1px 0px 20px rgba(0, 0, 0, 0.08);
            }
            .sidebar .label-custom {
                background: {{ $adminTheme->header_color }};
            }
            #side-menu li a {
                color: {{ $adminTheme->sidebar_text_color }} !important;
                border-left: 0 solid {{ $adminTheme->sidebar_color }};
            }
            #side-menu > li > a:hover,
            #side-menu > li > a:focus {
                background: rgba(0, 0, 0, 0.07);
            }
            #side-menu > li > a.active {
                border-left: 3px solid {{ $adminTheme->header_color }};
                color: {{ $adminTheme->link_color }};
            }
            #side-menu > li > a.active i {
                color: {{ $adminTheme->link_color }};
            }
            #side-menu ul > li > a:hover {
                color: {{ $adminTheme->link_color }};
            }
            #side-menu ul > li > a.active {
                color: {{ $adminTheme->link_color }};
            }
            .sidebar #side-menu .user-pro .nav-second-level a:hover {
                color: {{ $adminTheme->header_color }};
            }
            .nav-small-cap {
                color: {{ $adminTheme->sidebar_text_color }};
            }
            .content-wrapper .sidebar .nav-second-level li {
                background: #444859;
            }
            @media (min-width: 768px) {
                .content-wrapper #side-menu ul,
                .content-wrapper .sidebar #side-menu > li:hover,
                .content-wrapper .sidebar .nav-second-level > li > a {
                    background: #444859;
                }
            }
            /*themecolor*/
            .bg-theme {
                background-color: {{ $adminTheme->header_color }} !important;
            }
            .bg-theme-dark {
                background-color: {{ $adminTheme->sidebar_color }} !important;
            }
            /*Chat widget*/
            .chat-list .odd .chat-text {
                background: {{ $adminTheme->header_color }};
            }
            /*Button*/
            .btn-custom {
                background: {{ $adminTheme->header_color }};
                border: 1px solid {{ $adminTheme->header_color }};
                color: {{ $adminTheme->link_color }};
            }
            .btn-custom:hover {
                background: {{ $adminTheme->header_color }};
                border: 1px solid {{ $adminTheme->header_color }};
            }
            /*Custom tab*/
            .customtab li.active a,
            .customtab li.active a:hover,
            .customtab li.active a:focus {
                border-bottom: 2px solid {{ $adminTheme->header_color }};
                color: {{ $adminTheme->header_color }};
            }
            .tabs-vertical li.active a,
            .tabs-vertical li.active a:hover,
            .tabs-vertical li.active a:focus {
                background: {{ $adminTheme->header_color }};
                border-right: 2px solid {{ $adminTheme->header_color }};
            }
            /*Nav-pills*/
            .nav-pills > li.active > a,
            .nav-pills > li.active > a:focus,
            .nav-pills > li.active > a:hover {
                background: {{ $adminTheme->header_color }};
                color: {{ $adminTheme->link_color }};
            }
            .admin-panel-name{
                background: {{ $adminTheme->header_color }};
            }
            /*fullcalendar css*/
            .fc th.fc-widget-header{
                background: {{ $adminTheme->sidebar_color }};
            }
            .fc-button{
                background: {{ $adminTheme->header_color }};
                color: {{ $adminTheme->link_color }};
                margin-left: 2px !important;
            }
            .fc-unthemed .fc-today{
                color: #757575 !important;
            }
            .user-pro{
                background-color: {{ $adminTheme->sidebar_color }};
            }
            .top-left-part{
                background: {{ $adminTheme->sidebar_color }};
            }
            .notify .heartbit{
                border: 5px solid {{ $adminTheme->sidebar_color }};
            }
            .notify .point{
                background-color: {{ $adminTheme->sidebar_color }};
            }
        </style>

        <style>
            {!! $adminTheme->user_css !!}
        </style>
        {{--Custom theme styles end--}}
    @endif

    <style>
    .pac-container {
                z-index: 100000;
            }
        .sidebar .notify  {
            margin: 0 !important;
        }
        .sidebar .notify .heartbit {
            top: -23px !important;
            right: -15px !important;
        }
        .sidebar .notify .point {
            top: -13px !important;
        }
        .top-notifications .message-center .user-img{
            margin: 0 0 0 0 !important;
        }
        .fc-license-message{
            display: none!important;
        }
    </style>

<?php
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
    <!-- Top Navigation -->
    <div class="navbar navbar-expand-md navbar-light custom_height">

		<!-- Header with logos -->
		<div class="navbar-header navbar-dark d-none d-md-flex align-items-md-center">
			<div class="navbar-brand navbar-brand-md">
				<a href="{{ route('admin.inbox') }}" class="d-inline-block">
                    <!-- <img src="{{ $global->logo_url }}" alt=""> -->
                    <img src="{{ asset('logo-light.png') }}" alt="Onexfort" style="width: 240px;padding-top: 16px;height: auto;"/> 
				</a>
			</div>
			
			<div class="navbar-brand navbar-brand-xs">
				<a href="{{ route('admin.inbox') }}" class="d-inline-block">
					<img src="{{ asset('logo-sm.png') }}" alt="Onexfort" style="width: 50px;padding-top: 8px;height: auto;">
				</a>
			</div>
		</div>
		<!-- /header with logos -->
	

		<!-- Mobile controls -->
		<div class="d-flex flex-1 d-md-none">
			<div class="navbar-brand mr-auto">
				<a href="index.html" class="d-inline-block">
					<img src="{{ asset('logo-dark.png') }}" alt="">
				</a>
			</div>	
            <div class="form-group" style="width: 400px; width: 100%;padding-right: 5%;margin-top: 0.8rem;">
                <div class="input-group">
                    <span class="input-group-prepend">
                        <span class="input-group-text" style="border-top-left-radius: 30px;border-bottom-left-radius: 30px;background-color:#fff">
                            <i id="m_top_search_icon_other" class="icon-truck" style="display: none"></i>
                            <i id="m_top_search_icon_customer" class="icon-users2"></i>
                        </span>
                    </span>
                    <input type="hidden" id="m_top_search_type" value="customer"/>
                    <input id="m_top_search_bar" type="text" class="form-control top_search_bar" data-view="m" value="" autocomplete="off" placeholder="Search"/>
                    <div class="input-group-prepend">
                        <button type="button" class="btn btn-light btn-icon dropdown-toggle" data-toggle="dropdown" style="border-top-right-radius: 30px;border-bottom-right-radius: 30px;background-color:#fff"></button>
                        <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(155px, 40px, 0px);">
                            <a href="#" class="dropdown-item top_search_type" data-value="customer" data-view="m">Customer</a>
                            <a href="#" class="dropdown-item top_search_type" data-value="other" data-view="m">Job/Opportunity Number</a>
                        </div>
                    </div>
                </div>
                <div id="m_top_search_list" class="card" style="border: none;">
                </div>									
            </div>

			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-mobile">
				<i class="icon-tree5"></i>
			</button>

			<button class="navbar-toggler sidebar-mobile-main-toggle" type="button">
				<i class="icon-paragraph-justify3"></i>
			</button>
		</div>
		<!-- /mobile controls -->


		<!-- Navbar content -->
		<div class="collapse navbar-collapse app_blade_header" id="navbar-mobile" >
			<ul class="navbar-nav">
				<li class="nav-item">
					<a href="#" class="navbar-nav-link sidebar-control sidebar-main-toggle d-none d-md-block">
						<i class="icon-paragraph-justify3"></i>
					</a>
                </li>

              
            </ul>

			<span class="ml-md-2 mr-md-auto">&nbsp;</span>
            <div class="form-group" style="width: 400px; width: 380px;padding-right: 5%;margin-top: 2rem;">
                <div class="input-group">
                    <span class="input-group-prepend">
                        <span class="input-group-text" style="border-top-left-radius: 30px;border-bottom-left-radius: 30px;background-color:#fff">
                            <i id="w_top_search_icon_other" class="icon-truck" style="display: none"></i>
                            <i id="w_top_search_icon_customer" class="icon-users2"></i>
                        </span>
                    </span>
                    <input type="hidden" id="w_top_search_type" value="customer"/>
                    <input type="text" id="w_top_search_bar" class="form-control top_search_bar" data-view="w" value="" autocomplete="off" placeholder="Search"/>
                    <div class="input-group-prepend">
                        <button type="button" class="btn btn-light btn-icon dropdown-toggle" data-toggle="dropdown" style="border-top-right-radius: 30px;border-bottom-right-radius: 30px;background-color:#fff"></button>
                        <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(155px, 40px, 0px);">
                            <a href="#" class="dropdown-item top_search_type" data-value="customer" data-view="w">Customer</a>
                            <a href="#" class="dropdown-item top_search_type" data-value="other" data-view="w">Job/Opportunity Number</a>
                        </div>
                    </div>
                </div>
                <div id="w_top_search_list" class="card" style="border: none;">
                </div>									
            </div>
			<ul class="navbar-nav">               
				<li class="nav-item dropdown">
					<a href="#" class="navbar-nav-link dropdown-toggle caret-0" data-toggle="dropdown">
                        <img class="view_blade_3_bell" src="{{ asset('newassets/img/Icon awesome-bell@2x.png') }}">
                        <span class="d-md-none ml-2">@lang('app.newNotifications')</span>
                        @if(count($user->unreadNotifications) > 0)
						<span class="badge badge-mark border-pink-400 ml-auto ml-md-0"></span>
                        @endif
					</a>
					
					<div class="dropdown-menu dropdown-menu-right dropdown-content wmin-md-350">
						<div class="dropdown-content-header">
							<span class="font-weight-semibold">@lang('app.newNotifications')</span>
						</div>
                        @if(count($user->unreadNotifications) > 0)
						<div class="dropdown-content-body dropdown-scrollable">
							<ul class="media-list">                                
                                @foreach ($user->unreadNotifications as $notification)
								<li>
                                @include('notifications.member.'.snake_case(class_basename($notification->type)))
								</li>
                                @endforeach
							</ul>
						</div>
                        @endif

                        @if(count($user->unreadNotifications) > 0)
						<div class="dropdown-content-footer bg-light">
							<a href="javascript:;" class="text-grey mr-auto" id="mark-notification-read">@lang('app.markRead')</a>
						</div>
                        @endif
					</div>
                </li>
                <li class="dropdown">
                    <a href="" title="Logout" 
                    >
                    </a>
                </li>

				<li class="nav-item">
                    <a href="{{ route('logout') }}" class="logout-link navbar-nav-link d-flex align-items-center" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
					<span class="app_blade_shutdown_span" style="font-weight:500 !important;"><img class="app_blade_shutdown" src="{{ asset('newassets/img/shutdown@2x.png') }}"> @lang('app.logout')</span>
					</a>
				</li>
			</ul>
		</div>
		<!-- /navbar content -->
		
	</div>
    <!-- End Top Navigation -->

    
	<div class="page-content">
    <!-- Left navbar-header -->
    @include('sections.left_sidebar')
<!-- Left navbar-header end -->
    <!-- Page Content -->
    

    <div id="page-wrapper" style="width:100%; overflow-x: hidden!important;" 
    class="{{ (request()->is('admin/opportunity/pipeline')) ? 'pipeline-display' : '' }}">
        @yield('page-title')
        <div class="container-fluid">
        <!-- .row -->
            @yield('content')

            @include('sections.right_sidebar')
        </div>
        <!-- /.container-fluid -->    
        <footer class="footer text-center"> {{ \Carbon\Carbon::now()->year }} &copy; {{ $companyName }} </footer>    
    </div>    
    </div>    
    <!-- /#page-wrapper -->

@if(request()->is('admin/moving/edit-job/*'))
    <!--{{--Footer sticky notes--}}-->
    <div id="footer-sticky-notes" class="row hidden-xs hidden-sm">
        <div class="col-md-12" id="sticky-note-header">
            <div class="col-xs-10" style="line-height: 30px">
                @lang('app.menu.jobLogs')
                <a href="javascript:;" onclick="showCreateNoteModal({{$job->job_id}})" class="btn btn-success btn-outline btn-xs m-l-10">
                    <i class="fa fa-plus"></i> @lang("modules.sticky.addNote")
                </a>
            </div>
            <div class="col-xs-2">
                <a style="display: none;" href="javascript:;" class="btn btn-default btn-circle pull-right" id="open-sticky-bar"><i class="fa fa-chevron-up"></i></a>
                <a class="btn btn-default btn-circle pull-right" href="javascript:;" id="close-sticky-bar"><i class="fa fa-chevron-down"></i></a>
            </div>

        </div>

        <div id="sticky-note-list">

            @foreach($job_logs as $note)
                <div class="col-md-12 sticky-note" id="stickyBox_{{$note->id}}">
                    <div class="well @if($note->sys_log_types->colour == 'red')
                            bg-danger
@elseif($note->sys_log_types->colour == 'green')
                            bg-success
@elseif($note->sys_log_types->colour == 'yellow')
                            bg-warning
@elseif($note->sys_log_types->colour == 'blue')
                            bg-info
@elseif($note->sys_log_types->colour == 'purple')
                            bg-purple
@endif">
                        <p class="font-12 title-sec">
                            <img
                                    src="{{asset('img/icons/'.$note->sys_log_types->log_type_icon)}}"
                                    style="width:20px;"> <span>{!! strtoupper($note->sys_log_types->log_type) !!}</span>
                            <br>
                            @if(($note->log_type_id == 3) && $note->email_to != NULL)
                                @lang("modules.sticky.to"): {!! $note->email_to !!}
                                <br>
                            @elseif(($note->log_type_id == 5 || $note->log_type_id == 4) && $note->email_from != NULL)
                                @lang("modules.sticky.from"): {!! $note->email_from !!}
                                <br>
                            @elseif(($note->log_type_id == 8))
                                @if($note->sms_from != NULL)
                                    @lang("modules.sticky.from"): {!! $note->sms_from !!}
                                @endif
                                @if($note->sms_to != NULL)
                                    <br>
                                    @lang("modules.sticky.to"): {!! $note->sms_to !!}
                                @endif
                                <br>
                            
                            @endif
                            @if($note->email_subject != NULL)
                                @lang("modules.sticky.subject"): {!! $note->email_subject !!}
                            @endif
                        </p>
                        <p class="font-12 body-sec">
                            @if($note->log_type_id == 3 || $note->log_type_id == 4 || $note->log_type_id == 5)
                                <a  href="javascript:;"
                                    onclick="showEditNoteModal({{$note->id}}, this)" 
                                    style="color:#ffffff;">
                                    <b>
                                        <u>Body</u>
                                    </b>
                                </a>
                            @else
                                {{$note->log_details}}
                            @endif
                        </p>
                        <hr>
                        <?php
                        $name = $note->users['name'];
                        if($note->log_type_id == 5){
                            $job_moving = App\JobsMoving::where('job_id', $note->job_id)->first();
                            if($job_moving){
                                $customer = \App\Customers::find($job_moving->customer_id);
                                if($customer){
                                    $name = $customer->first_name.' '.$customer->last_name;
                                }
                            }
                        }
                        ?>
                        <div class="row" style="font-size:11px;">
                            <div class="col-xs-6">
                                @lang("modules.sticky.by"): <i class="fa fa-user"></i> {{ $name }}
                            </div>
                            <div class="col-xs-6">
                                {{ date('h:i A d/m/Y', strtotime($note->log_date)) }}
                            </div>
                            {{--<div class="col-xs-3">--}}
                            {{--<a href="javascript:;"  onclick="showEditNoteModal({{$note->id}})"><i class="ti-pencil-alt text-white"></i></a>--}}
                            {{--<a href="javascript:;" class="m-l-5" onclick="deleteSticky({{$note->id}})" ><i class="ti-close text-white"></i></a>--}}
                            {{--</div>--}}
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
    {{--sticky note end--}}
@endif
<input type="hidden" id="app_auth_status" value="200"/>
{{--Timer Modal--}}
<div class="modal fade bs-modal-md in" id="projectTimerModal" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
            </div>
            <div class="modal-body">
                Loading...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                <button type="button" class="btn blue">Save changes</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
{{--Timer Modal Ends--}}

{{--sticky note modal--}}
<div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            Loading ...
        </div>
    </div>
</div>
{{--sticky note modal ends--}}
{{--Timer Modal--}}
<div class="modal fade bs-modal-md in" id="projectTimerModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
            </div>
            <div class="modal-body">
                Loading...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                <button type="button" class="btn blue">Save changes</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
{{--Timer Modal Ends--}}
<!-- Create New Lead Popup -->
<div id="add_new_lead_popup" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="border: 1px solid #f0f0f0;padding-bottom:10px">
                <span style="font-size:18px;font-weight: 400;">New Opportunity</span>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Right content -->
                <div class="col-12 col-lg-12">   
                    <ul class="nav nav-tabs view_blade_5_navs_tabbs_box_shadow nobackground">
                        <li class="nav-item noborder"><a href="#residential_tab" class="nav-link active view_blade_5_navs_tabbs_nav_item_link" data-toggle="tab">Residential</a></li>
                        <li class="nav-item noborder"><a href="#commercial_tab" class="nav-link view_blade_5_navs_tabbs_nav_item_link" data-toggle="tab">Commercial</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="residential_tab">
                            <form id="create_residential_opportunity" method="post">
                                @csrf
                                <div class="form-body">                        
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label style="font-size:14px">@lang('modules.lead.lead_name')</label>
                                                <input type="text" name="lead_name" id="residential_name_field" class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label style="font-size:14px">Mobile</label>
                                                <input type="text" name="mobile" id="residential_mobile_field" class="form-control"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label style="font-size:14px">Email</label>
                                                <input type="text" name="email" class="form-control"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Pickup Address</label>
                                                <span style="margin-left: 30px;">
                                                    <input type="checkbox" id="r_pickup_suburb_popup" name="residential_suburb"/>
                                                    <label>Suburb only</label>
                                                </span>
                                                <input id="r_pickup_address_popup" type="text" name="" class="form-control new_opp_address_popup" autocomplete="off"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Drop off Address</label>
                                                <input id="r_drop_off_address_popup" type="text" name="" class="form-control new_opp_address_popup" autocomplete="off"/>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <input id="r_pickup_address" name="pickup_address" type="hidden"/>
                                    <input id="r_drop_off_address" name="drop_off_address" type="hidden"/>
                                    <input id="r_pickup_suburb" name="pickup_suburb" type="hidden"/>
                                    <input id="r_pickup_post_code" name="pickup_post_code" type="hidden"/>
                                    <input id="r_delivery_suburb" name="delivery_suburb" type="hidden"/>
                                    <input id="r_drop_off_post_code" name="drop_off_post_code" type="hidden"/>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Estimated Job Date</label>
                                                <div class="input-group">
                                                    <span class="input-group-prepend">
                                                        <span class="input-group-text"><i class="icon-calendar52"></i></span>
                                                    </span>
                                                    <input name="est_job_date" type="text" class="form-control daterange-single" value="{{ date('d/m/Y') }}"/>
                                                </div>
                                            </div>
                                        </div>
                                        @php
                                            $google_api_key = DB::table('tenant_api_details')->where(['tenant_id' => Auth::user()->tenant_id, 'provider' => 'GoogleMaps'])->pluck('account_key')->first();
                                        @endphp
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Lead info</label>
                                                <select name="lead_info" class="form-control">
                                                    @foreach($list_options as $data)
                                                        <option value="{{ $data->options }}">{{ $data->options }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label style="font-size:14px">Company</label>
                                                <select name="company_id" class="form-control">
                                                    @foreach($companies_list as $data)
                                                        <option value="{{ $data->id }}">{{ $data->company_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label style="font-size:14px">Opportunity Type</label>
                                                <select name="op_type" class="form-control op_job_type_field">
                                                    @foreach($op_type as $data)
                                                        <option value="{{ $data->options }}">{{ $data->options }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <input type="hidden" name="type" value="Residential"/>
                                    </div>
                                </div>
                                <div id="new-lead-validation-errors" class="col-md-6">
                                </div>
                                <div id="leads_gridview">
                                </div>
                                <div class="modal-footer mt-3" style="background-color: #f5f5f5!important;padding: 10px 20px!important;">
                                    <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                                    <button id="create_residential_btn" type="button" class="btn btn-success">Create Opportunity</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="commercial_tab">
                            <form id="create_commercial_opportunity" method="post">
                                @csrf
                                <div class="form-body">
                                    @php
                                        $customers = \App\CRMLeads::where('tenant_id', Auth::user()->tenant_id)->where('lead_type', 'Commercial')->get();
                                    @endphp                        
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Customer</label>
                                                <select name="customer_id" class="form-control">
                                                    @foreach($customers as $customer)
                                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label style="font-size:14px">Job Contact Name</label>
                                                <input type="text" name="pickup_contact_name" class="form-control"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label style="font-size:14px">Email (Job Contact)</label>
                                                <input type="text" name="pickup_email" class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label style="font-size:14px">Mobile (Job Contact)</label>
                                                <input type="text" name="pickup_mobile" class="form-control"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Pickup Address</label>
                                                <span style="margin-left: 30px;">
                                                    <input type="checkbox" id="c_pickup_suburb_popup" name="commercial_suburb"/>
                                                    <label>Suburb only</label>
                                                </span>
                                                <input id="c_pickup_address_popup" type="text" name="" class="form-control new_opp_address_popup"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Drop off Address</label>
                                                <input id="c_drop_off_address_popup" type="text" name="" class="form-control new_opp_address_popup"/>
                                            </div>
                                        </div>
                                    </div>
                                    <input id="c_pickup_address" name="pickup_address" type="hidden"/>
                                    <input id="c_drop_off_address" name="drop_off_address" type="hidden"/>
                                    <input id="c_pickup_suburb" name="pickup_suburb" type="hidden"/>
                                    <input id="c_pickup_post_code" name="pickup_post_code" type="hidden"/>
                                    <input id="c_delivery_suburb" name="delivery_suburb" type="hidden"/>
                                    <input id="c_drop_off_post_code" name="drop_off_post_code" type="hidden"/>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Estimated Job Date</label>
                                                <div class="input-group">
                                                    <span class="input-group-prepend">
                                                        <span class="input-group-text"><i class="icon-calendar52"></i></span>
                                                    </span>
                                                    <input name="est_job_date" type="text" class="form-control daterange-single" value="{{ date('d/m/Y') }}"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label style="font-size:14px">Company</label>
                                                <select name="company_id" class="form-control">
                                                    @foreach($companies_list as $data)
                                                        <option value="{{ $data->id }}">{{ $data->company_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label style="font-size:14px">Opportunity Type</label>
                                                <select name="op_type" class="form-control op_job_type_field">
                                                    @foreach($op_type as $data)
                                                        <option value="{{ $data->options }}">{{ $data->options }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <input type="hidden" name="type" value="Commercial"/>
                                    </div>
                                </div>
                                <div id="new-lead-validation-errors" class="col-md-6">
                                </div>
                                <div id="leads_gridview">
                                </div>
                                <div class="modal-footer mt-3" style="background-color: #f5f5f5!important;padding: 10px 20px!important;">
                                    <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                                    <button id="create_commercial_btn" type="button" class="btn btn-success">Create Opportunity</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- /Create New Lead Popup -->

<!-- jQuery -->
<script src="{{ asset('plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="{{ asset('bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src='//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/js/bootstrap-select.min.js'></script>

<!-- Sidebar menu plugin JavaScript -->
<script src="{{ asset('plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js') }}"></script>
<!--Slimscroll JavaScript For custom scroll-->
<script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
<!--Wave Effects -->
<script src="{{ asset('js/waves.js') }}"></script>
<!-- Custom Theme JavaScript -->
<script src="{{ asset('plugins/bower_components/sweetalert/sweetalert.min.js') }}"></script>
<script src="{{ asset('js/custom.min.js') }}"></script>
<script src="{{ asset('js/jasny-bootstrap.js') }}"></script>
<script src="{{ asset('plugins/froiden-helper/helper.js') }}"></script>
<script src="{{ asset('plugins/bower_components/toast-master/js/jquery.toast.js') }}"></script>
<script src="https://js.chargebee.com/v2/chargebee.js" data-cb-site="onexfort" ></script>


{{--sticky note script--}}
<script src="{{ asset('js/cbpFWTabs.js') }}"></script>
<script src="{{ asset('plugins/bower_components/icheck/icheck.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/icheck/icheck.init.js') }}"></script>
<script src="{{ asset('js/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('js/jquery.magnific-popup-init.js') }}"></script>
<script src="{{ asset('newassets/global_assets/js/plugins/pickers/daterangepicker.js') }}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ $google_api_key }}&libraries=places"></script>
    <script type="text/javascript">
    function initialize() {
        if($('#r_pickup_suburb_popup').is(":checked") || $('#c_pickup_suburb_popup').is(":checked"))
        {
            var options = {
                types: ['(cities)'],
                componentRestrictions: {
                    country: "au"
                }
            };
        }
        else
        {
            var options = {
                fields: ["address_components", "geometry"],
                types: ['address'],
                componentRestrictions: {
                    country: "au"
                }
            };
        }
            
            var allDepotInputs = document.getElementsByClassName('new_opp_address_popup');
            var autocompletes = [];
            for (var i = 0; i < allDepotInputs.length; i++) {
                //console.log(allDepotInputs[i]);
                var autocomplete = new google.maps.places.Autocomplete(allDepotInputs[i], options);
                autocomplete.inputId = allDepotInputs[i].id;
                autocomplete.addListener('place_changed', fillInFieldsNewOpp);
                autocompletes.push(autocomplete);
            }        
    
        }

        function fillInFieldsNewOpp() {
            var elemId = this.inputId;
            var place = this.getPlace();
            var address = '';
            var full_address = '';
            var suburb = '';
            var postal_code='';
            //Set Postcode field
             console.log(place.address_components);
            $.each(place.address_components, function( key, value ) {
                if(value.types[0]=="subpremise"){
                    address=address+' '+value.long_name+'/';
                    full_address=full_address+' '+value.long_name+'/';
                }
                if(value.types[0]=="street_number"){
                    address=address+value.long_name;
                    full_address=full_address+value.long_name;
                }
                if(value.types[0]=="route"){
                    address=address+' '+value.long_name;
                    full_address=full_address+' '+value.long_name;
                }
                if(value.types[0]=="locality"){
                    suburb=value.long_name;
                    full_address=full_address+' '+value.long_name;
                }
                if(value.types[0]=="administrative_area_level_1"){
                    suburb=suburb+' '+value.short_name;
                    full_address=full_address+' '+value.short_name;
                }
                if(value.types[0]=="postal_code"){
                    postal_code=value.long_name;
                }
            });
            if(elemId=="r_pickup_address_popup" || elemId=="r_drop_off_address_popup"){
                if($('#r_pickup_suburb_popup').is(":checked")){
                     $("#"+elemId).val(suburb);
                     $("#r_pickup_address").val('');
                     $("#r_drop_off_address").val('');
                }else{
                    $("#"+elemId).val(full_address);
                    if(elemId=="r_pickup_address_popup"){
                        $("#r_pickup_address").val(address);
                    }else{
                        $("#r_drop_off_address").val(address);
                    }
                }
                if(elemId=="r_pickup_address_popup"){
                        $("#r_pickup_post_code").val(postal_code);
                }else{
                        $("#r_drop_off_post_code").val(postal_code);
                }
                if(elemId=="r_pickup_address_popup"){
                    $("#r_pickup_suburb").val(suburb);
                }else{
                    $("#r_delivery_suburb").val(suburb);
                }
            }else if(elemId=="c_pickup_address_popup" || elemId=="c_drop_off_address_popup"){
                if($('#c_pickup_suburb_popup').is(":checked")){
                    $("#"+elemId).val(suburb);
                    $("#c_pickup_address").val('');
                     $("#c_drop_off_address").val('');
                }else{
                    $("#"+elemId).val(full_address);
                    if(elemId=="c_pickup_address_popup"){
                        $("#c_pickup_address").val(address);
                    }else{
                        $("#c_drop_off_address").val(address);
                    }
                    
                }
                if(elemId=="c_pickup_address_popup"){
                        $("#c_pickup_post_code").val(postal_code);
                }else{
                        $("#c_drop_off_post_code").val(postal_code);
                }
                if(elemId=="c_pickup_address_popup"){
                    $("#c_pickup_suburb").val(suburb);
                }else{
                    $("#c_delivery_suburb").val(suburb);
                }
            }
        }        
    
        google.maps.event.addDomListener(window, 'load', initialize);  
        document.addEventListener('DOMNodeInserted', function(event) {
            // console.log(event);
    
        });

        $('body').on('click', '#r_pickup_suburb_popup', initialize);
        $('body').on('click', '#c_pickup_suburb_popup', initialize);
    
    </script>

<script>
    $('body').on('click', '.timer-modal', function(){
        var url = '{{ route('admin.all-time-logs.show-active-timer')}}';
        $('#modelHeading').html('Active Timer');
        $.ajaxModal('#projectTimerModal',url);
    });
    function addOrEditStickyNote(id)
    {
        var url = '';
        var method = 'POST';
        if(id === undefined || id == "" || id == null) {
            url =  '{{ route('admin.sticky-note.store') }}'
        } else{
            url = "{{ route('admin.sticky-note.update',':id') }}";
            url = url.replace(':id', id);
            var stickyID = $('#stickyID').val();
            method = 'PUT'
        }
        var job_id = $('#job_id').val();
        var noteText = $('#notetext').val();
        var stickyColor = $('#stickyColor').val();
        $.easyAjax({
            url: url,
            container: '#responsive-modal',
            type: method,
            data:{'job_id':job_id,'notetext':noteText,'stickyColor':stickyColor,'_token':'{{ csrf_token() }}'},
            success: function (response) {
                $("#responsive-modal").modal('hide');
                window.location.reload();
            }
        })
    }
    // FOR SHOWING FEEDBACK DETAIL IN MODEL
    function showCreateNoteModal(id){
        var url = '{{ route('admin.sticky-note.createStickyNote',':id') }}';
        url = url.replace(':id', id);
        $("#responsive-modal").removeData('bs.modal').modal({
            remote: url,
            show: true
        });
        $('#responsive-modal').on('hidden.bs.modal', function () {
            $(this).find('.modal-body').html('Loading...');
            $(this).data('bs.modal', null);
        });
        return false;
    }
    // FOR SHOWING FEEDBACK DETAIL IN MODEL
    function showEditNoteModal(id, element){
        var url = '{{ route('admin.list-jobs.job-logs-body',':id') }}';
        url  = url.replace(':id',id);
        $("#responsive-modal").removeData('bs.modal').modal({
            remote: url,
            show: true
        });
        $('#responsive-modal').on('hidden.bs.modal', function () {
            $(this).find('.modal-body').html('Loading...');
            $(this).data('bs.modal', null);
        });
        //$(element).parent().siblings('.title-sec').find('img').attr("src","{{asset('img/icons/email_opened.png')}}");
        //$(element).parent().siblings('.title-sec').find('span').text("Email Opened");
        //$(element).parent().parent().removeClass("bg-danger");
        //$(element).parent().parent().addClass("bg-warning");
        return false;
    }
    function selectColor(id){
        $('.icolors li.active ').removeClass('active');
        $('#'+id).addClass('active');
        $('#stickyColor').val(id);
    }
    function deleteSticky(id){
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted Sticky Note!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {
                var url = "{{ route('admin.sticky-note.destroy',':id') }}";
                url = url.replace(':id', id);
                var token = "{{ csrf_token() }}";
                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        $('#stickyBox_'+id).hide('slow');
                        $("#responsive-modal").modal('hide');
                        getNoteData();
                    }
                });
            }
        });
    }
    //getting all chat data according to user
    function getNoteData(){
        var url = "{{ route('admin.sticky-note.index') }}";
        $.easyAjax({
            type: 'GET',
            url: url,
            messagePosition: '',
            data:  {},
            container: ".noteBox",
            error: function (response) {
                //set notes in box
                $('#sticky-note-list').html(response.responseText);
            }
        });
    }
</script>


<script>
    $('#mark-notification-read').click(function () {
        var token = '{{ csrf_token() }}';
        $.easyAjax({
            type: 'POST',
            url: '{{ route("mark-notification-read") }}',
            data: {'_token': token},
            success: function (data) {
                if (data.status == 'success') {
                    $('.top-notifications').remove();
                    $('#top-notification-count').html('0');
                    $('#top-notification-dropdown .notify').remove();
                }
            }
        });
    });
    $('.show-all-notifications').click(function () {
        var url = '{{ route('show-all-member-notifications')}}';
        $('#modelHeading').html('View Unread Notifications');
        $.ajaxModal('#projectTimerModal', url);
    });
    $('.submit-search').click(function () {
        $(this).parent().submit();
    });
    // $(function () {
    //     $('.selectpicker').selectpicker();
    // });
    $('.language-switcher').change(function () {
        var lang = $(this).val();
        $.easyAjax({
            url: '{{ route("admin.settings.change-language") }}',
            data: {'lang': lang},
            success: function (data) {
                if (data.status == 'success') {
                    window.location.reload();
                }
            }
        });
    });
    //    sticky notes script
    var stickyNoteOpen = $('#open-sticky-bar');
    var stickyNoteClose = $('#close-sticky-bar');
    var stickyNotes = $('#footer-sticky-notes');
    var viewportHeight = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
    var stickyNoteHeaderHeight = stickyNotes.height();
    $('#sticky-note-list').css('max-height', viewportHeight-150);
    stickyNoteOpen.click(function () {
        $('#sticky-note-list').toggle(function () {
            $(this).animate({
                height: (viewportHeight-150)
            })
        });
        stickyNoteClose.toggle();
        stickyNoteOpen.toggle();
    })
    stickyNoteClose.click(function () {
        $('#sticky-note-list').toggle(function () {
            $(this).animate({
                height: 0
            })
        });
        stickyNoteOpen.toggle();
        stickyNoteClose.toggle();
    })
    $('body').on('click', '.right-side-toggle', function () {
        $(".right-sidebar").slideDown(50).removeClass("shw-rside");
    })
    function updateOnesignalPlayerId(userId) {
        $.easyAjax({
            url: '{{ route("member.profile.updateOneSignalId") }}',
            type: 'POST',
            data:{'userId':userId, '_token':'{{ csrf_token() }}'},
            success: function (response) {
            }
        })
    }

    $('body').on('click', '#create_new_opp_btn', function(e) {
        e.preventDefault();
        var status;
        $.ajax({
            url: "/check-auth-login",
            method: 'get',
            async: false,
            dataType: "json",
            beforeSend: function() {
                // $("#preloader").show();
            },
            complete: function() {
                // $("#preloader").hide();
            },
            success: function(result) {
                $("#app_auth_status").val(result.status);
            },
        });
        if($("#app_auth_status").val()!=200){
            window.location.href = '/login';
            return false;
        }
    });

    //START:: Create New Residential Lead
    $('body').on('click', '#create_commercial_btn, #create_residential_btn', function(e) {
        $("#preloader").show();
    });
    $('body').on('click', '#create_residential_btn', function(e) {
        e.preventDefault();
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxStoreLead",
            method: 'post',
            data: $("#create_residential_opportunity").serialize(),
            dataType: "json",
            beforeSend: function() {
                $("#preloader").show();
            },
            complete: function() {
                $("#preloader").hide();
            },
            success: function(result, textStatus, xhr) {
                if (result.error == 0) {
                    var lead_id = result.id;
                    var opportunity_id = result.opportunity_id;

                    //Hide Model
                    $('#add_new_lead_popup').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                    //...
                    window.location = "/admin/crm/view-opportunity/" + lead_id + "/" + opportunity_id;

                    // Notification....
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
            },
            error: function(xhr) { 
                $('#new-lead-validation-errors').html('');
                $.each(xhr.responseJSON.errors, function(key,value) {
                    $('#new-lead-validation-errors').append('<div class="alert alert-danger" style="padding: 2px 4px;margin-bottom: 6px;">'+value+'</div');
                });

                setTimeout(function() {
                    $("#new-lead-validation-errors").hide('blind', {}, 200)
                }, 10000);
            }
        });
    });
    //END:: Create New Residential Lead
    //START:: Create New Commercial Lead
    $('body').on('click', '#create_commercial_btn', function(e) {
        e.preventDefault();
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxStoreLead",
            method: 'post',
            data: $("#create_commercial_opportunity").serialize(),
            dataType: "json",
            beforeSend: function() {
                $("#preloader").show();
            },
            complete: function() {
                $("#preloader").hide();
            },
            success: function(result) {
                //console.log(result.message);
                if (result.error == 0) {
                    var lead_id = result.id;
                    var opportunity_id = result.opportunity_id;

                    //Hide Model
                    $('#add_new_lead_popup').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                    //...
                    window.location = "/admin/crm/view-opportunity/" + lead_id + "/" + opportunity_id;
                    // Notification....
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
            },
            error: function(xhr) {
                if(xhr.status == 422)
                {
                    $.each(xhr.responseJSON.errors, function(key,value) {
                        //Notification....
                            $.toast({
                            heading: 'Error',
                            text: value,
                            icon: 'error',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#fb9678',
                            textColor: 'white'
                        });
                        //..
                    });
                }
                else{
                    //Notification....
                        $.toast({
                        heading: 'Error',
                        text: 'Customer contact is missing',
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
    //END:: Create New Commercial Lead
    $('.daterange-single').daterangepicker({ 
       singleDatePicker: true,
        locale: {
            format: 'DD/MM/YYYY'
        }
    }); 
    //START:: Find similar leads
    $('body').on('keyup', '#residential_name_field', function(e) {
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxFindLeads",
            method: 'post',
            data: $("#create_residential_opportunity").serialize(),
            dataType: "json",

            success: function(result) {
                $("#leads_gridview").html(result.html);
            }
        });
    });
    //END:: Find similar leads
    //START:: Find similar Mobile Leads
    $('body').on('keyup', '#residential_mobile_field', function(e) {
        $.ajax({
            url: "/admin/crm/crm-leads/ajaxFindLeadsByNumber",
            method: 'post',
            data: $("#create_residential_opportunity").serialize(),
            dataType: "json",

            success: function(result) {
                // $("#div1").remove();
                $("#leads_gridview").html(result.html);
            }
        });
    });
    //END:: Find similar Mobile leads

    //START:: Top Search Bar
    $(document).click(function(e) {
        var target = e.target;
        if (!$(target).is('#top_search_result_list') && !$(target).parents().is('#top_search_result_list')) {
            $('#top_search_result_list').hide();
            $('#top_search_bar').val("");
        }
    });

    $(document).ready(function(){
        var cache = {};
        $('body').on('click', '.top_search_type', function(e) {
            var type = $(this).data("value");
            var view = $(this).data("view");
            $("#"+view+"_top_search_type").val(type);
            if(type=="customer"){
                $("#"+view+"_top_search_icon_other").hide();
                $("#"+view+"_top_search_icon_customer").show();
            }else{
                $("#"+view+"_top_search_icon_customer").hide();
                $("#"+view+"_top_search_icon_other").show();
            }
        });

        $('.top_search_bar').keyup(function(){ 
            console.log(cache);
            var query = $(this).val();
            var view = $(this).data("view");
            var type = $("#"+view+"_top_search_type").val();
            var cache_search = query+'_'+type;
            if(query != '')
            {
                if ( cache_search in cache ) {
					$("#"+view+"_top_search_list").html(cache[ cache_search ]);
                    return;
				}
                var _token = $('input[name="_token"]').val();
                $.ajax({
                url:"{{ route('admin.ajax-main-search') }}",
                method:"POST",
                data:{query:query, _token:_token,type:type},
                beforeSend: function() {
                    $('.preloader').show();
                },
                complete: function() {
                    $('.preloader').hide();
                },
                success:function(data){
                $("#"+view+"_top_search_list").fadeIn();  
                    cache[ cache_search] = data;
                    $("#"+view+"_top_search_list").html(data);
                }
                });
            }
        });

        $(document).on('click', 'li', function(){  
            $("#"+view+"_top_search_bar").val($(this).text());  
            $("#"+view+"_top_search_list").fadeOut();  
        });  

        });
    //END:: Top Search Bar
</script>
@stack('footer-script')

</body>
</html>