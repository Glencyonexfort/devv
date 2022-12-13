{!! Form::open(['action' => 'QuoteLeaseCleaningController@store', 'id'=>'formStep1','class'=>'','method'=>'GET']) !!}
{{ Form::hidden('step', $step) }}
{{ Form::hidden('tenant_id', $tenant_id) }}
{{ Form::hidden('company_id', $company_id) }}
{{ Form::hidden('discount', $discount) }}
{{ Form::hidden('city_id', $city_id) }}
{{ Form::hidden('session_data', $session_data) }}
<div class="tab-pane active" role="tabpanel" id="step2">
    <div class="pagetitle">
        <h1>Select Extras</h1>
    </div>
    <div class="extraContainer">
        @foreach($extras_list as $optn)
        <div class="extraBox">
            <div class="row">
                @php
                $value = 0;
                if(isset($step2_ary['extras'][$optn->id])):
                $value = intval($step2_ary['extras'][$optn->id]);
                endif;
                @endphp
                <div class="col-xs-7">
                    <div class="form-group extra_title">
                        {{$optn->name}}
                    </div>
                </div>
                <div class="col-xs-5">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default btn-number" @if($value==0) disabled="disabled" @endif data-type="minus" data-field="extras[{{$optn->id}}]">
                                    <span class="glyphicon glyphicon-minus"></span>
                                </button>
                            </span>
                            <input type="text" name="extras[{{$optn->id}}]" class="form-control input-number" value="{{$value}}" min="0" max="99">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="extras[{{$optn->id}}]">
                                    <span class="glyphicon glyphicon-plus"></span>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <ul class="list-inline text-center hr-top margin-top-10">
        <li class="pull-left"><a href="{{url('/quote-lease-cleaning/'.$tenant_id.'/'.$company_id.'/'.$city_id.'/'.$discount.'?step=1&session_data='. urlencode($session_data))}}" class="btn btn-default btn-next-step">Back</a></li>
        <li class="pull-right"><button type="submit" class="btn btn-primary btn-next-step  step2_nextBtn">Next <i class="fa fa-angle-right"></i></button></li>
    </ul>
</div>
{!! Form::close() !!}
<script>
    //plugin bootstrap minus and plus
    //http://jsfiddle.net/laelitenetwork/puJ6G/
    $('.btn-number').click(function(e) {
        e.preventDefault();

        fieldName = $(this).attr('data-field');
        type = $(this).attr('data-type');
        var input = $("input[name='" + fieldName + "']");
        var currentVal = parseInt(input.val());
        if (!isNaN(currentVal)) {
            if (type == 'minus') {

                if (currentVal > input.attr('min')) {
                    input.val(currentVal - 1).change();
                }
                if (parseInt(input.val()) == input.attr('min')) {
                    $(this).attr('disabled', true);
                }

            } else if (type == 'plus') {

                if (currentVal < input.attr('max')) {
                    input.val(currentVal + 1).change();
                }
                if (parseInt(input.val()) == input.attr('max')) {
                    $(this).attr('disabled', true);
                }

            }
        } else {
            input.val(0);
        }
    });
    $('.input-number').focusin(function() {
        $(this).data('oldValue', $(this).val());
    });
    $('.input-number').change(function() {

        minValue = parseInt($(this).attr('min'));
        maxValue = parseInt($(this).attr('max'));
        valueCurrent = parseInt($(this).val());

        name = $(this).attr('name');
        if (valueCurrent >= minValue) {
            $(".btn-number[data-type='minus'][data-field='" + name + "']").removeAttr('disabled')
        } else {
            alert('Sorry, the minimum value was reached');
            $(this).val($(this).data('oldValue'));
        }
        if (valueCurrent <= maxValue) {
            $(".btn-number[data-type='plus'][data-field='" + name + "']").removeAttr('disabled')
        } else {
            alert('Sorry, the maximum value was reached');
            $(this).val($(this).data('oldValue'));
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