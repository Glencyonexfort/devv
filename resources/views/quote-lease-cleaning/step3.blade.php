{!! Form::open(['action' => 'QuoteLeaseCleaningController@store', 'id'=>'formStep1','class'=>'','method'=>'GET']) !!}
{{ Form::hidden('step', $step) }}
{{ Form::hidden('tenant_id', $tenant_id) }}
{{ Form::hidden('company_id', $company_id) }}
{{ Form::hidden('discount', $discount) }}
{{ Form::hidden('city_id', $city_id) }}
{{ Form::hidden('session_data', $session_data) }}
<div class="tab-pane active" role="tabpanel" id="step3">
    <div class="extraContainer margin-top-10">
        @foreach($question_list as $qid => $rs)
        <div class="row">
            <div class="col-xs-12">
                <div class="form-group form-group-sm">
                    <label>{{$rs['question']->list_name}}</label></div>
                <div class="form-group">
                    <select name="question[{{ intval($qid) }}]" class="form-control">
                        @foreach($rs['list'] as $optn)
                        @php
                        $value = 0;
                        if(isset($step3_ary['question'][intval($qid)])):
                        $value = $step3_ary['question'][intval($qid)];
                        endif;
                        @endphp
                        <option value="{{$optn->id}}" @if($optn->id == ($value ?? ''))
                            selected
                            @endif
                            >{{$optn->list_option}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <ul class="list-inline text-center hr-top">
        <li class="pull-left"><a href="{{url('/quote-lease-cleaning/'.$tenant_id.'/'.$company_id.'/'.$city_id.'/'.$discount.'?step=2&session_data='. urlencode($session_data))}}" class="btn btn-default btn-next-step">Back</a></li>
        <li class="pull-right"><button type="submit" class="btn btn-primary btn-next-step">Next <i class="fa fa-angle-right"></i></button></li>
    </ul>
</div>
{!! Form::close() !!}