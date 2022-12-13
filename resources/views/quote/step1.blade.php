<div class="tab-pane active" role="tabpanel" id="step1">
    <div class="get_qutoe_box">
        <h1>Get a Quote</h1>
        <p>A no-obligation quote in a few clicks.</p>
    </div>
    {!! Form::open(['action' => 'QuoteController@store', 'id' => 'formStep1', 'class' => '', 'method' => 'GET']) !!}
    {{ Form::hidden('step', $step) }}
    {{ Form::hidden('tenant_id', $tenant_id) }}
    {{ Form::hidden('company_id', $company_id) }}
    {{ Form::hidden('session_data', $session_data) }}
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group form-label">
                <label>You are moving</label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-2">
            <div class="form-group form-label text-center">
                <label style="padding: 7px;">FROM:</label>
            </div>
        </div>
        <div class="col-xs-10">
            <div class="form-group">
                <input type="text" name="moving_from" id="moving_from" value="{{ $step1_ary['moving_from'] ?? '' }}"
                    placeholder="Enter Suburb" class="form-control location-text location-icon" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-2">
            <div class="travel-line"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-2">
            <div class="form-group form-label text-center">
                <label style="padding: 7px;">TO:</label>
            </div>
        </div>
        <div class="col-xs-10">
            <div class="form-group">
                <input type="text" name="moving_to" id="moving_to" value="{{ $step1_ary['moving_to'] ?? '' }}"
                    placeholder="Enter Suburb" class="form-control location-text" required>
            </div>
        </div>
    </div>
    <ul class="list-inline text-right hr-top margin-top-30">
        <li><button type="submit" class="btn btn-primary btn-next-step" disabled>Start Moving <img
                    src="{{ asset('quote-assets/check.png') }}" class="btn-check-icon"></button></li>
    </ul>
    {!! Form::close() !!}
</div>
<script
    src="https://maps.googleapis.com/maps/api/js?key={{ $tenant_api_details->account_key ?? '' }}&v=3.exp&libraries=places">
</script>
<script type="text/javascript">
    var suburb1 = "{{ isset($step1_ary['moving_from']) && !empty($step1_ary['moving_from']) ? 'true' : 'false' }}";
    var suburb2 = "{{ isset($step1_ary['moving_to']) && !empty($step1_ary['moving_to']) ? 'true' : 'false' }}";
    var autocomplete, autocomplete2;

    function validateSuburb() {
        $('.btn-next-step').prop('disabled', true);
        if (suburb1 == 'true' && suburb2 == 'true') {
            $('.btn-next-step').prop('disabled', false);
        }
    }

    function initialize() {
        var options = {
            types: ['(cities)'],
            componentRestrictions: {
                country: "au"
            }
        };
        var input = document.getElementById('moving_from');
        var autocomplete = new google.maps.places.Autocomplete(input, options);

        var input2 = document.getElementById('moving_to');
        var autocomplete2 = new google.maps.places.Autocomplete(input2, options);

        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            suburb1 = "true";
            validateSuburb();
        });

        google.maps.event.addListener(autocomplete2, 'place_changed', function() {
            suburb2 = "true";
            validateSuburb();
        });

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

    $(document).on('change', '#moving_from', function() {
        suburb1 = "false";
        validateSuburb();
        setTimeout(function() {
            var newval = $('#moving_from').val().replace(', Australia', '');
            $('#moving_from').val(newval);
        }, 10);
    });
    $(document).on('change', '#moving_to', function() {
        suburb2 = "false";
        validateSuburb();
        setTimeout(function() {
            var newval = $('#moving_to').val().replace(', Australia', '');
            $('#moving_to').val(newval);
        }, 10);
    });

    $(document).ready(function() {
        validateSuburb();
    });

</script>
