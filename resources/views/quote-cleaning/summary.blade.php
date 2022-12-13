<div class="tab-pane active" role="tabpanel" id="step5">
    <div class="panel panel-default panel-summary">
        <div class="panel-heading">Booking Summary</div>
        <div class="panel-body">
            <p><i class="fa fa-home"></i> {{$product->name}}</p>
            <p><i class="fa fa-calendar"></i> {{$date}} @ {{$time}}</p>
            <p><i class="fa fa-refresh"></i> {{$how_often}}</p>
        </div>
        <div class="panel-footer">
            Total <strong>{{$organisation_settings->currency_symbol}}{{$total_cost}}</strong>
        </div>
    </div>
    <ul class="list-inline text-center hr-top">
        <li><a href="{{url('/quote-cleaning/payLater?tenant_id='.$tenant_id.'&company_id='.$company_id.'&session_data='.urlencode($session_data))}}" class="btn btn-default btn-next-step">Book Now - Pay Later</a></li>
    </ul>
    <ul class="list-inline text-center hr-top">
        <li><a href="{{url('/quote-cleaning/payNow?tenant_id='.$tenant_id.'&company_id='.$company_id.'&session_data='. urlencode($session_data))}}" class="btn btn-success btn-next-step btn-next-success"> Book and Pay now</a></li>
    </ul>    
    <ul class="list-inline text-center hr-top">
        <li><a href="{{url('/quote-cleaning/'.$tenant_id.'/'.$company_id.'?step=4&session_data='. urlencode($session_data))}}" class="btn btn-default btn-next-step"><i class="fa fa-angle-left"></i> Back</a></li>
    </ul>
</div>