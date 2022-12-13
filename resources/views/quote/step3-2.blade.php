{!! Form::open(['action' => 'QuoteController@store', 'id'=>'formStep1','class'=>'','method'=>'GET']) !!}
{{ Form::hidden('step', $step) }}
{{ Form::hidden('tenant_id', $tenant_id) }}
{{ Form::hidden('company_id', $company_id) }}
{{ Form::hidden('session_data', $session_data) }}
{{ Form::hidden('move_from_type', ($step2_ary['moving_from_type'] ?? '')) }}
<div class="tab-pane active" role="tabpanel" id="step3.2">
    <div class="title_box">
        <h1>What's the size of your move?</h1>
        <p>Choose establishment classification</p>
    </div>
    <div class="row">
        <div class="col-xs-4">
            <div class="form-group form-label">
                <label>Floor</label>
                <input type="number" name="floor" id="floor" value="{{ ($step3_ary['floor'] ?? '0') }}" min="0" step="1" class="form-control">
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group form-label">
                <label>&nbsp;</label>
                <ul class="checklist">
                    <li><input type="checkbox" name="stairs" value="1" id="stairs" @if(isset($step3_ary['stairs']) && $step3_ary['stairs']=='1' ) checked @endif> Stairs</li>
                    <li><input type="checkbox" name="lift" value="1" id="lift" @if(isset($step3_ary['lift']) && $step3_ary['lift']=='1' ) checked @endif> Lift</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <div class="form-group form-label">
                <label>Furnishing</label>
                <select name="furnishing" id="furnishing" class="form-control">
                    @foreach($furnishing as $optn)
                    <option value="{{$optn->options}}" @if($optn->options == ($step3_ary['furnishing'] ?? ''))
                        selected
                        @endif
                        >{{$optn->options}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group form-label">
                <label>Bedrooms</label>
                <select name="bedroom" id="bedroom" class="form-control" required>
                    @foreach($bedroom as $optn)
                    <option value="{{ ($optn->options == 'None' ? '' : $optn->options)}}" @if($optn->options == ($step3_ary['bedroom'] ?? ''))
                        selected
                        @endif
                        >{{$optn->options}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <div class="form-group form-label">
                <label>Living Rooms</label>
                <select name="living_room" id="living_room" class="form-control" required>
                    @foreach($living_room as $optn)
                    <option value="{{ ($optn->options == 'None' ? '' : $optn->options)}}" @if($optn->options == ($step3_ary['living_room'] ?? ''))
                        selected
                        @endif
                        >{{$optn->options}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group form-label">
                <label>Speciality Items</label>
            </div>
            <div class="form-group">
                <ul class="checklist special">
                    @foreach($special_item as $optn)
                    <li><input type="checkbox" name="special_item[]" value="{{$optn->options}}" @if(isset($step3_ary['special_item']) && is_array($step3_ary['special_item'])) @if(in_array($optn->options, $step3_ary['special_item']))
                        checked
                        @endif
                        @endif
                        > {{$optn->options}}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group form-label">
                <label>Other Rooms</label>
            </div>
            <div class="form-group">
                <ul class="checklist">
                    @foreach($other_room as $optn)
                    <li><input type="checkbox" name="other_room[]" value="{{$optn->options}}" @if(isset($step3_ary['other_room']) && is_array($step3_ary['other_room'])) @if(in_array($optn->options, $step3_ary['other_room']))
                        checked
                        @endif
                        @endif
                        > {{$optn->options}}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <ul class="list-inline text-center hr-top">
        <li class="pull-left"><a href="{{url('/quote/'.$tenant_id.'/'.$company_id.'?step=2&session_data='. urlencode($session_data))}}" class="btn btn-default btn-back-step"><i class="fa fa-arrow-left"></i> Go back to previous</a></li>
        <li class="pull-right"><button type="submit" class="btn btn-primary btn-next-step">Continue</button></li>
    </ul>
</div>
{!! Form::close() !!}