{!! Form::open(['action' => 'QuoteController@store', 'id'=>'formStep1','class'=>'','method'=>'GET']) !!}
{{ Form::hidden('step', $step) }}
{{ Form::hidden('tenant_id', $tenant_id) }}
{{ Form::hidden('company_id', $company_id) }}
{{ Form::hidden('session_data', $session_data) }}
{{ Form::hidden('move_from_type', ($step2_ary['moving_from_type'] ?? '')) }}
{{ Form::hidden('storage_cbm', '', array('id' => 'storage_cbm')) }}
<div class="tab-pane active" role="tabpanel" id="step3.3">
    <div class="title_box">
        <h1>How much is there in your storage?</h1>
        <p>Choose establishment classification</p>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group">
                <ul class="checkoptions">
                    <li data-cbm="15" @if(isset($step3_ary['storage_cbm']) && $step3_ary['storage_cbm']=='15' ) class="active" @endif>
                        <p>Less than 20m3 <br><span>Equivalent 1-2 Bedrooms home</span></p>
                    </li>
                    <li data-cbm="25" @if(isset($step3_ary['storage_cbm']) && $step3_ary['storage_cbm']=='25' ) class="active" @endif>
                        <p>20 to 30m3 <br><span>Equivalent 2-3 Bedrooms home</span></p>
                    </li>
                    <li data-cbm="40" @if(isset($step3_ary['storage_cbm']) && $step3_ary['storage_cbm']=='40' ) class="active" @endif>
                        <p>30 to 50m3 <br><span>Equivalent 3-4 Bedrooms home</span></p>
                    </li>
                    <li data-cbm="55" @if(isset($step3_ary['storage_cbm']) && $step3_ary['storage_cbm']=='55' ) class="active" @endif>
                        <p>More than 20m3 <br><span>Equivalent to a large home</span></p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <ul class="list-inline text-center hr-top margin-top-30">
        <li class="pull-left"><a href="{{url('/quote/'.$tenant_id.'/'.$company_id.'?step=2&session_data='. urlencode($session_data))}}" class="btn btn-default btn-back-step"><i class="fa fa-arrow-left"></i> Go back to previous</a></li>
        <li class="pull-right"><button type="submit" class="btn btn-primary btn-next-step step4_nextBtn" @if(!isset($step3_ary['storage_cbm']) || empty($step3_ary['storage_cbm'])) style="display: none;" @endif>Continue</button></li>
    </ul>
</div>
{!! Form::close() !!}
<script>
    $(document).ready(function() {
        $(document).on('click', '.checkoptions li', function() {
            $this = $(this);
            $('.checkoptions li').removeClass('active');
            $this.addClass('active');
            $('#storage_cbm').val($this.data('cbm'));
            $('.step4_nextBtn').show();
        });
    });
</script>