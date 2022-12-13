{!! Form::open(['action' => 'QuoteCleaningController@store', 'id'=>'formStep1','class'=>'','method'=>'GET']) !!}
{{ Form::hidden('step', $step) }}
{{ Form::hidden('tenant_id', $tenant_id) }}
{{ Form::hidden('company_id', $company_id) }}
{{ Form::hidden('session_data', $session_data) }}
<div class="tab-pane active" role="tabpanel" id="step4">
    <p class="font-bold">Contact Details</p>
    <p>The booking summary and payment amount will be shown in the next step.</p>
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group">
                <label>Your Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{($step4_ary['name'] ?? '')}}" required>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{($step4_ary['email'] ?? '')}}" required>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <label>Mobile</label>
                <input type="text" name="phone" id="phone" class="form-control" value="{{($step4_ary['phone'] ?? '')}}" required>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <label>Cleaning Address</label>
                <input type="text" name="cleaning_address" id="cleaning_address" value="{{($step4_ary['cleaning_address'] ?? '')}}" class="form-control" required>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <label>Please give any other details here</label>
                <textarea type="text" rows="3" name="other_details" id="other_details" class="form-control">{{($step4_ary['other_details'] ?? '')}}</textarea>
            </div>
        </div>
    </div>
    <ul class="list-inline text-center hr-top">
        <li><a href="{{url('/quote-cleaning/'.$tenant_id.'/'.$company_id.'?step=3&session_data='. urlencode($session_data))}}" class="btn btn-default btn-next-step"><i class="fa fa-angle-left"></i> Back</a></li>
        <li><button type="submit" class="btn btn-primary btn-next-step">Next <i class="fa fa-angle-right"></i></button></li>
    </ul>
</div>
{!! Form::close() !!}
<script src="https://maps.googleapis.com/maps/api/js?key={{$tenant_api_details->account_key ?? ''}}&v=3.exp&libraries=places"></script>
<script type="text/javascript">
    function initialize() {
        var options = {
            // types: ['(locality)'],
            componentRestrictions: {
                country: "au"
            }
        };
        var input = document.getElementById('cleaning_address');
        var autocomplete = new google.maps.places.Autocomplete(input, options);
    }

    document.addEventListener('DOMNodeInserted', function(event) {
        // console.log(event);
        var target = $(event.target);
        if (target.hasClass('pac-item')) {
            // console.log(target.html());
            target.html(target.html().replace(/, Australia<\/span>/, "</span>"));
        }
    });

    google.maps.event.addDomListener(window, 'load', initialize);

    $(document).on('change', '#cleaning_address', function() {
        setTimeout(function() {
            var newval = $('#cleaning_address').val().replace(', Australia', '');
            $('#cleaning_address').val(newval);
        }, 10);
    });
</script>