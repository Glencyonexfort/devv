<style>
    .overlay-this {
    background: #e9e9e9;  
    display: none;        
    position: absolute;   
    top: 0;                 
    right: 0;               
    bottom: 0;
    left: 0;
    opacity: 0.5;
    z-index: 1;
}
</style>
<div class="overlay-this"></div>
<div class="tab-pane active" role="tabpanel" id="step5">
    <div class="panel panel-default panel-summary">
        <div class="panel-heading">Booking Summary</div>
        <div class="panel-body">
            <p><i class="fa fa-home"></i> End of Lease Cleaning</p>
            <p><i class="fa fa-bed"></i> {{$bed}} bed, {{$bath}} bath</p>
            <p><i class="fa fa-calendar"></i> {{$date}} @ {{$time}}</p>
        </div>
        {{-- <div class="panel-footer">
            Total <strong>{{$organisation_settings->currency_symbol}}{{$total_cost}}</strong>
        </div> --}}
    </div>
    @if($cleaning_form_setup->pay_now_button == 'Y')
    <ul class="list-inline text-center hr-top">
        <li><a id="payButton" href="" class="btn btn-success btn-next-step btn-next-success"> Book and Pay now</a></li>
    </ul>
    @endif    
    <ul class="list-inline text-center hr-top">
        <li class="pull-left"><a href="{{ url('/quote-lease-cleaning/' . $tenant_id . '/' . $company_id . '/' . $city_id .'/'.$discount. '?step=4&session_data=' . urlencode($session_data)) }}" class="btn btn-default btn-next-step">BACK</a></li>
        @if($cleaning_form_setup->pay_now_button != 'Y')
        <li class="pull-right"><a href="{{url('/quote-lease-cleaning/payLater?tenant_id='.$tenant_id.'&company_id='.$company_id.'&city_id='.$city_id.'&discount='.$discount.'&session_data='. urlencode($session_data))}}" class="btn btn-default btn-next-step btn-send-quote">SEND ME QUOTE</a></li>
        @endif
    </ul>
    
</div>
{{-- {{url('/quote-lease-cleaning/payNow?tenant_id='.$tenant_id.'&company_id='.$company_id)}} --}}
<script type="text/jscript" src="https://checkout.stripe.com/checkout.js"></script>
<script>
        
    var handler = StripeCheckout.configure({
        key: "{{ env('STRIPE_PUBLIC') }}",
        image: '{{ request()->getSchemeAndHttpHost() }}/stripe-onex-logo.jpg',
        locale: 'auto',
        allowRememberMe: false,
        token: function(token) {
            // You can access the token ID with `token.id`.
            // Get the token ID to your server-side code for use.
            var tkn = "{{ csrf_token() }}";
            $('#payProcess').val(1);      
            $('.overlay-this').show();
            window.location = '/quote-lease-cleaning/payNow?stripeToken='+token.id+'&tenant_id='+{{ $tenant_id }}+'&company_id='+{{ $company_id }}+'&city_id='+{{ $city_id }}+'&total_cost='+{{ $total_cost }}+'&session_data='+{{ $session_data }}+'&discount='+{{ $discount }};    
        }
    });
    
        var stripe_closed = function(){
            var processing = $('#payProcess').val();
            if (processing == 0){
                $('.overlay-this').hide();
                $('#payButton').prop('disabled', false);
                $('#payButton').html('Book and Pay now');
            }
        };
    
    var eventTggr = document.getElementById('payButton');
    if(eventTggr){
        eventTggr.addEventListener('click', function(e) {
            $('#payButton').prop('disabled', true);
            $('#payButton').html('Please wait...');
            $('.overlay-this').show();
            
            // Open Checkout with further options:
            handler.open({
                name: "{{ $organisation_settings->company_name }}",
                description: 'Job Confirmation Payment',
                amount: "{{ $total_cost*100 }}",
                email: "{{ $step4_ary['email'] }}",
                closed:	stripe_closed
            });
            e.preventDefault();
        });
    }

</script>