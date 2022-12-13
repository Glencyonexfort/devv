$(function() {

//jQuery time
var current_fs, next_fs, previous_fs; //fieldsets
var left, opacity, scale; //fieldset properties which we will animate
var animating; //flag to prevent quick multi-click glitches

$(".next").click(function(){

	var validator = $("#msform").validate({
		//errorClass: "authError",
            rules: {
                first_name: "required",
                last_name: "required",
                business_name: "required",
                business_email: {
			          required: true,
			          email: true,
			         remote: {
			          url: "/checkemail",
			          type: "get",            
      				}
        		},
                business_address1: "required",
                city: "required",
                mobile: "required",
                postcode: "required",
                state: "required",
                country: "required",
                business_size: "required",
                password: "required",
                gst_registered: "required",
                confirmpassword: {
                    equalTo: "#password"
                }
            },
            messages: {
               first_name: "Enter First Name",
               last_name: "Enter Last Name",
               business_name: "Enter Business Name",
               business_email: {
			          	required: "Enter Email Address",
			            business_email: "Please enter a valid email address.",
			            remote: "Email address is already taken."
        		},
               business_address1: "Enter Business Address",
               mobile: "Enter Mobile Number",
               city: " Enter City",
               state: " Enter State Name",
               country: " Select Country",
               postcode: " Enter Postcode",
               password: " Enter Password",
               business_size: " Enter Business Size greater than 0",
               gst_registered: " *",
               confirmpassword: " Enter Confirm Password Same as Password"
            },
			submitHandler: function(form) { // <- pass 'form' argument in
				$("#registration_submit_btn").attr("disabled", true);
				$('#registration_submit_btn').removeClass('action-button');
				$('#registration_submit_btn').addClass('disabled-button');	
				form.submit(); // <- use 'form' argument here.
			}
        });
        // if (validator.form()) {
		// 	$('#registration_submit_btn').removeClass('disabled-button');
		// 	$('#registration_submit_btn').addClass('action-button');
        //     //return true;            
        // } else {
		// 	$('#registration_submit_btn').removeClass('action-button');
		// 	$('#registration_submit_btn').addClass('disabled-button');			
		// 	//return false;         
        // }

	if(!validator.form() || animating) return false;
	animating = true;
	
	current_fs = $(this).parent();
	next_fs = $(this).parent().next();
	
	//activate next step on progressbar using the index of next_fs
	$("#eliteregister li").eq($("fieldset").index(next_fs)).addClass("active");
	
	//show the next fieldset
	next_fs.show(); 
	//hide the current fieldset with style
	current_fs.animate({opacity: 0}, {
		step: function(now, mx) {
			//as the opacity of current_fs reduces to 0 - stored in "now"
			//1. scale current_fs down to 80%
			scale = 1 - (1 - now) * 0.2;
			//2. bring next_fs from the right(50%)
			left = (now * 50)+"%";
			//3. increase opacity of next_fs to 1 as it moves in
			opacity = 1 - now;
			current_fs.css({'transform': 'scale('+scale+')'});
			next_fs.css({'left': left, 'opacity': opacity});
		}, 
		duration: 800, 
		complete: function(){
			current_fs.hide();
			animating = false;
		}, 
		//this comes from the custom easing plugin
		easing: 'easeInOutBack'
	});
});

$(".previous").click(function(){
	if(animating) return false;
	animating = true;
	
	current_fs = $(this).parent();
	previous_fs = $(this).parent().prev();
	
	//de-activate current step on progressbar
	$("#eliteregister li").eq($("fieldset").index(current_fs)).removeClass("active");
	
	//show the previous fieldset
	previous_fs.show(); 
	//hide the current fieldset with style
	current_fs.animate({opacity: 0}, {
		step: function(now, mx) {
			//as the opacity of current_fs reduces to 0 - stored in "now"
			//1. scale previous_fs from 80% to 100%
			scale = 0.8 + (1 - now) * 0.2;
			//2. take current_fs to the right(50%) - from 0%
			left = ((1-now) * 50)+"%";
			//3. increase opacity of previous_fs to 1 as it moves in
			opacity = 1 - now;
			current_fs.css({'left': left});
			previous_fs.css({'transform': 'scale('+scale+')', 'opacity': opacity});
		}, 
		duration: 800, 
		complete: function(){
			current_fs.hide();
			animating = false;
		}, 
		//this comes from the custom easing plugin
		easing: 'easeInOutBack'
	});
});

$('body').on('click', '.verify_email_btn', function(e) {
	e.preventDefault();
	var _token = $('input[name="_token"]').val();
	var email = $('#business_email').val();
	var user_name = $('#first_name').val() +' '+$('#last_name').val();
	$.ajax({
		url: "/registration-generate-opt",
		method: 'post',
		data: {
			'_token': _token,
            'email': email,
            'user_name': user_name
		},
		dataType: "json",
		beforeSend: function() {
			$(".preloader").show();
		},
		complete: function() {
			$(".preloader").hide();
		},
		success: function(result) {

			if (result.error == 0) {
				$("#business_detail_box").hide();
				$("#otp_verify_box").show();
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
		}
	});
});

$('body').on('click', '#registration_submit_btn', function(e) {
	e.preventDefault();
	var _token = $('input[name="_token"]').val();
	var email = $('#business_email').val();
	var otp_code = $('#otp_code').val();
	$.ajax({
		url: "/verify-opt",
		method: 'post',
		data: {
			'_token': _token,
            'email': email,
            'otp_code': otp_code
		},
		dataType: "json",
		beforeSend: function() {
			$(".preloader").show();
		},
		complete: function() {
			$(".preloader").hide();
		},
		success: function(result) {
			if (result.error == 0) {
				$("#opt_field_box").hide();
				$("#success_box").show();
				$("#registration_submit_form").click();
			} else {
				$("#opt_errors").html(result.message).show().delay(5000).hide("slow");
			}
		}
	});
});

$(".submit").click(function(){
	//return false;
});

});