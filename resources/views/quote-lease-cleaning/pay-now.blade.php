@if($status==1)
<div class="tab-pane active" role="tabpanel" id="step5">
    <h1 class="main_title hr-bottom ">Thanks for your payment</h1>
    <p class="font-bold margin-top-30 text-red">Your booking is confirmed!</p>
    <p class="margin-top-30">Thanks for your payment! Your booking is confirmed. Your reference number is <strong>{{ ($new_job_number ?? 'xxxxx') }}</strong>. You will receive the booking confirmation and the invoice in your email in a few minutes.</p>
    <p class="margin-top-30">Please make sure to check the SPAM/JUNK folders of your email in case you don't receive the quote.</p>
</div>
@else
<div class="tab-pane active" role="tabpanel" id="step5">
    <h1 class="main_title hr-bottom ">Unsuccessful</h1>
    <p class="font-bold margin-top-30 text-red">{{ $error_msg }}</p>
</div>
@endif