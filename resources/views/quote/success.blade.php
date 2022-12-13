@if($jobs_moving_auto_quoting->quote_form_redirect_after_submit == 'Y' && !empty($jobs_moving_auto_quoting->quote_form_redirect_url))
<script>
    window.top.location.href = '{{$jobs_moving_auto_quoting->quote_form_redirect_url}}';
</script>
@else
<div class="tab-pane active" role="tabpanel" id="step5">
    <div class="title_box">
        <h1 class="text-red">Get a Quote</h1>
    </div>
    <div class="redline"></div>
    <p class="margin-top-30 text-style">Your quote is being processed!</p>
    <p class="margin-top-30 text-style">Thank you for submitting the request for a quote! Your reference number is <strong>{{$new_job_number}}</strong>. You will receive the quote in your email in a few minutes.</p>
    <p class="margin-top-30 text-style">Please make sure to check the SPAM/JUNK folders of your email in case you don't receive the quote.</p>
</div>
@endif