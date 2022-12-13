
<!DOCTYPE html>
<html>
<head>
    <title> Tenant Registration </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="description" content="">
    <meta name="keywords" content="admin, bootstrap,admin template, bootstrap admin, simple, awesome">
    <meta name="author" content="">
    <!-- Custom CSS -->

    <link href="{{ asset('tenant_registration_steps/icheck/skins/all.css') }}" rel="stylesheet">
    <link href="{{ asset('tenant_registration_steps/style.min.css') }}" rel="stylesheet">

    <link href="{{ asset('tenant_registration_steps/register-steps/steps.css') }}" rel="stylesheet">
    <link href="{{ asset('tenant_registration_steps/register3.css') }}" rel="stylesheet">
    <link href="{{ asset('tenant_registration_steps/dropify/dist/css/dropify.min.css') }}" rel="stylesheet">

    <link href="{{ asset('tenant_registration_steps/form-icheck.css') }}" rel="stylesheet">

    <script src="{{ asset('tenant_registration_steps/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('tenant_registration_steps/jquery.validate.min.js') }}" type="text/javascript"></script>
    <style type="text/css">
        label.error {
            color: red;
            float: left;
            /* font-weight: 600; */
            /* padding: 0 2px; */
            width: 100%;
            text-align: left;
            margin-bottom: 18px;
            margin-top: -15px;
        }
        .icheck-list li{
            border: 1px solid lightgray;
            padding: 10px;
            margin-bottom: 10px;
        }
        .icheck-list {
            width: 100%;
            text-align: left;
        }
        .field-icon {
            float: right;
            margin-right: 8px;
            margin-top: -51px;
            position: relative;
            z-index: 2;
            cursor: pointer;
        }
        .authError {border:1px dotted red !important;}
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
<!-- BODY -->
<body>
<!-- ============================================================== -->
<!-- Preloader - style you can find in spinners.css -->
<!-- ============================================================== -->
<div class="preloader">
    <div class="loader">
        <div class="loader__figure"></div>
        <p class="loader__label">Tenant Registration</p>
    </div>
</div>
<!-- ============================================================== -->
<!-- Main wrapper - style you can find in pages.scss -->
<!-- ============================================================== -->
<section id="wrapper" class="step-register" style="overflow: visible;">
    @yield('content')
</section>
<!-- ============================================================== -->
<!-- End Wrapper -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- All Jquery -->
<!-- ============================================================== -->
<!-- Bootstrap tether Core JavaScript -->
<script src="{{ asset('tenant_registration_steps/popper/popper.min.js') }}"></script>
<script src="{{ asset('tenant_registration_steps/bootstrap/dist/js/bootstrap.min.js') }}"></script>

<script src="{{ asset('tenant_registration_steps/register-steps/jquery.easing.min.js') }}"></script>
<script src="{{ asset('tenant_registration_steps/register-steps/register-init.js') }}"></script>

<script>
    /*function validatePassword() {
        var validator = $("#msform").validate({
            rules: {
                first_name: "required",
                password: "required",
                confirmpassword: {
                    equalTo: "#password"
                }
            },
            messages: {
               first_name: "Enter First Name",
               password: " Enter Password",
               confirmpassword: " Enter Confirm Password Same as Password"
            }
        });
        if (validator.form()) {
            return true;
            alert('Sucess');
        } else {
         e.preventDefault();
        }
    }*/

</script>
<script src="{{ asset('tenant_registration_steps/dropify/dist/js/dropify.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // Basic
        $('.dropify').dropify();
        // Used events
        var drEvent = $('#input-file-events').dropify();

        drEvent.on('dropify.beforeClear', function(event, element) {
            return confirm("Do you really want to delete \"" + element.file.name + "\" ?");
        });

        drEvent.on('dropify.afterClear', function(event, element) {
            alert('File deleted');
        });

        drEvent.on('dropify.errors', function(event, element) {
            console.log('Has Errors');
        });

        var drDestroy = $('#input-file-to-destroy').dropify();
        drDestroy = drDestroy.data('dropify')
        $('#toggleDropify').on('click', function(e) {
            e.preventDefault();
            if (drDestroy.isDropified()) {
                drDestroy.destroy();
            } else {
                drDestroy.init();
            }
        })
    });
</script>


<script type="text/javascript">

    $( document ).ready(function() {
        $(".toggle-password").click(function() {

            $(this).toggleClass("fa-eye fa-eye-slash");
            var input = $($(this).attr("toggle"));
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
    });
</script>
<!--Custom JavaScript -->
<script type="text/javascript">
    $(function() {
        $(".preloader").fadeOut();
    });
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    });
    // ==============================================================
    // Login and Recover Password
    // ==============================================================
    /* $('#to-recover').on("click", function() {
         $("#login_form").slideUp();
         $("#recoverform").fadeIn();
     });
     $('#sign-in-btn').on("click", function() {
         $("#login_form").slideDown();
         $("#recoverform").slideUp();

     });*/

    $(function() {
        // $(".uniform_on").uniform();

        // $(".chzn-select").chosen();
    });
</script>

<script src="{{ asset('tenant_registration_steps/icheck/icheck.min.js') }}"></script>
<script src="{{ asset('tenant_registration_steps/icheck/icheck.init.js') }}"></script>



<!--  <link href="http://ec2-52-65-201-83.ap-southeast-2.compute.amazonaws.com/paasfort//assets/system_design/css/chosen.min.css" rel="stylesheet" media="screen">

 <script src="http://ec2-52-65-201-83.ap-southeast-2.compute.amazonaws.com/paasfort//assets/system_design/scripts/chosen.jquery.min.js"></script> -->

</body>

</html>