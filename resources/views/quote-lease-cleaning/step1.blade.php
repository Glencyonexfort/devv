<div class="tab-pane active" role="tabpanel" id="step1">
    <div class="pagetitle">
        <h1>Make a booking in few clicks.</h1>
    </div>
    {!! Form::open(['action' => 'QuoteLeaseCleaningController@store', 'id' => 'formStep1', 'class' => '', 'method' =>
    'GET']) !!}
    {{ Form::hidden('step', $step) }}
    {{ Form::hidden('tenant_id', $tenant_id) }}
    {{ Form::hidden('company_id', $company_id) }}
    {{ Form::hidden('discount', $discount) }}
    {{ Form::hidden('city_id', $city_id) }}
    {{ Form::hidden('session_data', $session_data) }}
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group form-group-sm">
                <label>Cleaning Address</label>
            </div>
            <div class="form-group">
                <input type="text" name="cleaning_address" id="cleaning_address"
                    value="{{ $step1_ary['cleaning_address'] ?? '' }}" class="form-control" required>
            </div>
        </div>
        <!-- <div class="col-xs-12">
            <div class="form-group">
                <label>Suburb</label>
                <input type="text" name="suburb" id="suburb" class="form-control" value="{{ $step1_ary['suburb'] ?? '' }}" required>
            </div>
        </div> -->
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-12">
                    <div class="form-group form-group-sm">
                        <label>Single Storey or Double?</label>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="form-group">
                        <input type="radio" name="story" id="story" checked value="N" required> Single
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="form-group">
                        <input type="radio" name="story" id="story" @if ('Y' == ($step1_ary['story'] ?? '')) checked @endif value="Y"
                        required> Double
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-12">
                    <div class="form-group form-group-sm">
                        <label>Do you require carpet steam cleaning?</label>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="form-group">
                        <input type="radio" name="carpeted" id="carpeted_yes" checked value="Y" required> Yes
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="form-group">
                        <input type="radio" name="carpeted" id="carpeted_no" @if ('N' == ($step1_ary['carpeted'] ?? '')) checked @endif
                        value="N" required> No
                    </div>
                </div>
            </div>
        </div>
        @php
        $bedrooms = 1;
        $max_bedrooms = intval($cleaning_form_setup->max_bedrooms);
        if(isset($step1_ary['bedrooms'])):
        $bedrooms = intval($step1_ary['bedrooms']);
        endif;

        $bathrooms = 1;
        $max_bathrooms = intval($cleaning_form_setup->max_bathrooms);
        if(isset($step1_ary['bathrooms'])):
        $bathrooms = intval($step1_ary['bathrooms']);
        endif;
        @endphp
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-12">
                    <div class="form-group form-group-sm">
                        <label>How many rooms on your property?</label>
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default btn-number" @if ($bedrooms == 1) disabled="disabled" @endif
                                    data-type="minus" data-field="bedrooms">
                                    <span class="glyphicon glyphicon-minus"></span>
                                </button>
                            </span>
                            <input type="text" name="bedrooms" class="form-control input-number"
                                value="{{ $bedrooms }} Bedrooms" data-slug="Bedrooms" data-val="{{ $bedrooms }}" min="1"
                                max="{{ $max_bedrooms }}">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default btn-number" data-type="plus"
                                    data-field="bedrooms">
                                    <span class="glyphicon glyphicon-plus"></span>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default btn-number" @if ($bathrooms == 1) disabled="disabled" @endif
                                    data-type="minus" data-field="bathrooms">
                                    <span class="glyphicon glyphicon-minus"></span>
                                </button>
                            </span>
                            <input type="text" name="bathrooms" class="form-control input-number"
                                value="{{ $bathrooms }} Bathrooms" data-slug="Bathrooms" data-val="{{ $bathrooms }}"
                                min="1" max="{{ $max_bathrooms }}">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default btn-number" data-type="plus"
                                    data-field="bathrooms">
                                    <span class="glyphicon glyphicon-plus"></span>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group form-group-sm">
                        <label>When do you want the cleaning?</label>
                    </div>
                    <div class="form-group">
                        <div class="input-group inputaddon">
                            <input type="text" name="date" id="date" class="form-control datepicker"
                                value="{{ $step1_ary['date'] ?? '' }}" autocomplete="off" required>
                            <span class="input-group-addon"><i class="fa fa-calendar-o"></i></span>
                        </div>

                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="form-group form-group-sm">
                        <label>Choose an avialable start time</label>
                    </div>
                    <div class="form-group">
                        <div class="input-group inputaddon">
                            <select name="start_time" id="start_time" class="form-control" required></select>
                            <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <ul class="list-inline text-right hr-top margin-top-30_">
        <li><button type="submit" class="btn btn-primary btn-next-step">NEXT</button></li>
    </ul>
    {!! Form::close() !!}
</div>
<script>
    $(document).ready(function() {
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            startDate: 'today'
        }).on('changeDate', function(ev) {
            $(this).datepicker('hide');
            getStartTime();
        });

        getStartTime();
    });

    function getStartTime() {

        var date_selected = $('#date').val();
        $.get("{{ url('/quote-lease-cleaning/ajaxStartTime') }}", {
            tenant_id: "{{ $tenant_id }}",
            date: date_selected
        }, function(data) {
            $('#start_time').html(data.options);
        });
    }

    $('.btn-number').click(function(e) {
        e.preventDefault();

        fieldName = $(this).attr('data-field');
        type = $(this).attr('data-type');
        var input = $("input[name='" + fieldName + "']");
        var currentVal = parseInt(input.data('val'));
        var slug = input.data('slug');
        if (!isNaN(currentVal)) {
            if (type == 'minus') {

                if (currentVal > input.attr('min')) {
                    input.data('val', currentVal - 1);
                    input.val((currentVal - 1) + ' ' + slug).change();
                }
                if (parseInt(input.val()) == input.attr('min')) {
                    $(this).attr('disabled', true);
                }

            } else if (type == 'plus') {

                if (currentVal < input.attr('max')) {
                    input.data('val', currentVal + 1);
                    input.val((currentVal + 1) + ' ' + slug).change();
                }
                if (parseInt(input.val()) == input.attr('max')) {
                    $(this).attr('disabled', true);
                }

            }
        } else {
            input.data('val', 1);
            input.val((1) + ' ' + slug);
        }
    });
    $('.input-number').focusin(function() {
        $(this).data('oldValue', $(this).val());
    });
    $('.input-number').change(function() {

        minValue = parseInt($(this).attr('min'));
        maxValue = parseInt($(this).attr('max'));
        valueCurrent = parseInt($(this).data('val'));

        name = $(this).attr('name');
        if (valueCurrent >= minValue) {
            $(".btn-number[data-type='minus'][data-field='" + name + "']").removeAttr('disabled')
        } else {
            alert('Sorry, the minimum value was reached');
            $(this).data('val', $(this).data('oldValue'));
        }
        if (valueCurrent <= maxValue) {
            $(".btn-number[data-type='plus'][data-field='" + name + "']").removeAttr('disabled')
        } else {
            alert('Sorry, the maximum value was reached');
            $(this).data('val', $(this).data('oldValue'));
        }


    });
    $(".input-number").keydown(function(e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
            // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) ||
            // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
            // let it happen, don't do anything
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

</script>
<!-- cleaning_form_setup -->
<script
    src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key={{ $tenant_api_details->account_key ?? '' }}&region=au">
</script>
<!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB2SMtaVBlqC5v72gqS716BX8R5oXklaFc&v=3.exp&libraries=places"></script> -->
<script type="text/javascript">
    function initialize() {
        var sw_lat = '{{ $cleaning_form_setup->servicing_city_geocode_sw_lat }}';
        var sw_lng = '{{ $cleaning_form_setup->servicing_city_geocode_sw_lng }}';
        var ne_lat = '{{ $cleaning_form_setup->servicing_city_geocode_ne_lat }}';
        var ne_lng = '{{ $cleaning_form_setup->servicing_city_geocode_ne_lng }}';

        var defaultBounds = new google.maps.LatLngBounds(
            new google.maps.LatLng(sw_lat, sw_lng),
            new google.maps.LatLng(ne_lat, ne_lng));

        var options = {
            types: ['address'],
            bounds: defaultBounds,
            //radius: '40000',
            strictbounds: true,
            // radius: 1,
            // location: '-27.4697707,153.0251235',
            // location: {
            //     "lat": 32.0739787,
            //     "lng": 72.6860696
            // },
            // componentRestrictions: {
            //     country: "pk"
            // }
        };
        var input = document.getElementById('cleaning_address');
        var autocomplete = new google.maps.places.Autocomplete(input, options);
        autocomplete.setOptions({
            strictBounds: true
        });

        // google.maps.event.addListener(autocomplete, 'place_changed', function() {
        //     var place = autocomplete.getPlace();
        //     var lat = place.geometry.location.lat();
        //     var lng = place.geometry.location.lng();
        //     console.log(place);
        //     document.getElementById('city2').value = place.name;
        //     document.getElementById('cityLat').value = lat;
        //     document.getElementById('cityLng').value = lng;
        // });
    }

    google.maps.event.addDomListener(window, 'load', initialize);

</script>
