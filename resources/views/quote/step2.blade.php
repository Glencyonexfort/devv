{!! Form::open(['action' => 'QuoteController@store', 'id'=>'formStep1','class'=>'','method'=>'GET']) !!}
{{ Form::hidden('step', $step) }}
{{ Form::hidden('tenant_id', $tenant_id) }}
{{ Form::hidden('company_id', $company_id) }}
{{ Form::hidden('session_data', $session_data) }}
{{ Form::hidden('move_from_type', ($step2_ary['moving_from_type'] ?? ''), array('id' => 'move_from_type')) }}
{{ Form::hidden('move_to_type', ($step2_ary['moving_to_type'] ?? ''), array('id' => 'move_to_type')) }}
<div class="tab-pane active" role="tabpanel" id="step2">
    <div class="title_box">
        <h1>Select building type</h1>
        <p>Choose establishment classification</p>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group bold-label">
                <label>FROM:</label>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group img-container moveFromType @if(isset($step2_ary['moving_from_type']) && $step2_ary['moving_from_type'] == 'House') active @endif" data-id="House">
                <div class="home-icon"></div>
                <p>House</p>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group img-container moveFromType @if(isset($step2_ary['moving_from_type']) && $step2_ary['moving_from_type'] == 'Flat') active @endif" data-id="Flat">
                <div class="building-icon"></div>
                <p>Flat</p>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group img-container moveFromType @if(isset($step2_ary['moving_from_type']) && $step2_ary['moving_from_type'] == 'Storage Facility') active @endif" data-id="Storage Facility">
                <div class="storage-icon"></div>
                <p>Storage Facility</p>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group bold-label">
                <label>TO:</label>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group img-container moveToType @if(isset($step2_ary['moving_to_type']) && $step2_ary['moving_to_type'] == 'House') active @endif" data-id="House">
                <div class="home-icon"></div>
                <p>House</p>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group img-container moveToType  @if(isset($step2_ary['moving_to_type']) && $step2_ary['moving_to_type'] == 'Flat') active @endif" data-id="Flat">
                <div class="building-icon"></div>
                <p>Flat</p>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group img-container moveToType  @if(isset($step2_ary['moving_to_type']) && $step2_ary['moving_to_type'] == 'Storage Facility') active @endif" data-id="Storage Facility">
                <div class="storage-icon"></div>
                <p>Storage Facility</p>
            </div>
        </div>
    </div>
    <ul class="list-inline text-center hr-top margin-top-30">
        <li class="pull-left"><a href="{{url('/quote/'.$tenant_id.'/'.$company_id.'?step=1&session_data='. urlencode($session_data))}}" class="btn btn-default btn-back-step"><i class="fa fa-arrow-left"></i> Go back to previous</a></li>
        <li class="pull-right"><button type="submit" class="btn btn-primary btn-next-step  step2_nextBtn" style="display: none;">Continue</button></li>
    </ul>
</div>
{!! Form::close() !!}
<script>
    function showHideNext() {
        $('.step2_nextBtn').hide();
        if ($('#move_from_type').val() != '' && $('#move_to_type').val() != '') {
            $('.step2_nextBtn').show();
        }
    }
    $(document).ready(function() {
        $(document).on('click', '.moveFromType', function() {
            $this = $(this);
            $('.moveFromType').removeClass('active');
            $this.addClass('active');
            $('#move_from_type').val($this.data('id'));
            showHideNext();
        });
        $(document).on('click', '.moveToType', function() {
            $this = $(this);
            $('.moveToType').removeClass('active');
            $this.addClass('active');
            $('#move_to_type').val($this.data('id'));
            showHideNext();
        });
        showHideNext();
    });
</script>