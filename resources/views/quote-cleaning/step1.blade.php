<div class="tab-pane active" role="tabpanel" id="step1">
    <p class="font-bold">Make a booking in few clicks.</p>
    {!! Form::open(['action' => 'QuoteCleaningController@store', 'id'=>'formStep1','class'=>'','method'=>'GET']) !!}
    {{ Form::hidden('step', $step) }}
    {{ Form::hidden('tenant_id', $tenant_id) }}
    {{ Form::hidden('company_id', $company_id) }}
    {{ Form::hidden('session_data', $session_data) }}
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group">
                <label>Choose Your Service</label>
                <select name="products" id="products" class="form-control">
                    @foreach($products as $optn)
                    <option value="{{$optn->id}}" data-type="{{$optn->price_type}}" @if($optn->id == ($step1_ary['products'] ?? ''))
                        selected
                        @endif
                        >{{$optn->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @php
        $min_cleaners = intval($cleaning_form_setup->min_cleaners);
        $max_cleaners = intval($cleaning_form_setup->max_cleaners);
        @endphp
        <div class="col-xs-6 hourlyOptionBox">
            <div class="form-group">
                <label>Cleaners</label>
                <select name="cleaners" id="cleaners" class="form-control">
                    @for($i=$min_cleaners; $i<=$max_cleaners; $i++) <option value="{{$i}}" @if($i==($step1_ary['cleaners'] ?? '' )) selected @endif>{{$i}} Cleaner</option>
                        @endfor
                </select>
            </div>
        </div>
        @php
        $min_hours = intval($cleaning_form_setup->min_hours);
        $max_hours = intval($cleaning_form_setup->max_hours);
        @endphp
        <div class="col-xs-6 hourlyOptionBox">
            <div class="form-group">
                <label>Hours</label>
                <select name="hours" id="hours" class="form-control">
                    @for($i=$min_hours; $i<=$max_hours; $i++) <option value="{{$i}}" @if($i==($step1_ary['hours'] ?? '' )) selected @endif>{{$i}} Hours</option>
                        @endfor
                </select>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-12">
                    <div class="form-group" style="margin-bottom: 0em;">
                        <label>When do you want the cleaning?</label>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="form-group">
                        <input type="text" name="date" id="date" class="form-control datepicker" value="{{($step1_ary['date'] ?? '')}}" autocomplete="off" required>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="form-group">
                        <select name="time" id="time" class="form-control">
                            @foreach($time_list as $optn)
                            <option value="{{$optn->list_option}}" @if($optn->list_option == ($step1_ary['time'] ?? ''))
                                selected
                                @endif
                                >{{$optn->list_option}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <label>How often?</label>
                <select name="how_often" id="how_often" class="form-control">
                    @foreach($often_list as $optn)
                    <option value="{{$optn->list_option}}" @if($optn->list_option == ($step1_ary['how_often'] ?? ''))
                        selected
                        @endif
                        >{{$optn->list_option}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <ul class="list-inline text-center hr-top">
        <li><button type="submit" class="btn btn-primary btn-next-step">NEXT <i class="fa fa-angle-right"></i></button></li>
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
        });

        $(document).on('change', '#products', function() {
            showHourlyOptions();
        });
        showHourlyOptions();
    });

    function showHourlyOptions() {
        var type = $('#products').find(':selected').data('type');
        if (type == 'Hourly') {
            $('.hourlyOptionBox').show();
        } else {
            $('.hourlyOptionBox').hide();
        }
    }
</script>