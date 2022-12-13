{!! Form::open(['action' => 'QuoteController@store', 'id'=>'formStep4','class'=>'','method'=>'GET']) !!}
{{ Form::hidden('step', $step) }}
{{ Form::hidden('tenant_id', $tenant_id) }}
{{ Form::hidden('company_id', $company_id) }}
{{ Form::hidden('session_data', $session_data) }}
<div class="tab-pane active" role="tabpanel" id="step4">
    <div class="title_box">
        <h1>When do you like to move?</h1>
    </div>
    <div class="row">
        <div class="col-xs-4">
            <div class="form-group form-label">
                <label>Moving Date</label>
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group form-label">
                <ul class="checklist">
                    <li><input type="radio" name="pickup_date_type" class="pickup_date_type" value="pickup_date" checked> PickUp Date</li>
                    <li><input type="radio" name="pickup_date_type" class="pickup_date_type" value="not_sure"> Not Sure</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row" id="pickup_date_box">
        <div class="col-xs-4">
            <div class="form-group form-label">
                <label>Removal Date</label>
            </div>
        </div>
        <div class="col-xs-7">
            <div class="form-group">
                <input type="text" name="pickup_date" id="pickup_date" class="form-control datepicker" required>
            </div>
        </div>
    </div>
    <div class="title_box">
        <h1>Contact Details</h1>
    </div>
    <div class="row margin-top-10">
        <div class="col-xs-4">
            <div class="form-group form-label">
                <label>Your Name</label>
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
        </div>
    </div>
    <div class="row margin-top-10">
        <div class="col-xs-4">
            <div class="form-group form-label">
                <label>Email</label>
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group form-label">
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
        </div>
    </div>
    <div class="row margin-top-10">
        <div class="col-xs-4">
            <div class="form-group form-label">
                <label>Mobile</label>
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group form-label">
                <input type="text" name="phone" id="phone" class="form-control" required>
            </div>
        </div>
    </div>
    <div class="row margin-top-10">
        <div class="col-xs-4">
            <div class="form-group form-label">
                <label>Notes</label>
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group form-label">
                <textarea name="other_instructions" class="form-control"></textarea>
            </div>
        </div>
    </div>
    <ul class="list-inline text-right hr-top margin-top-30">
        <li class="pull-left"><a href="{{url('/quote/'.$tenant_id.'/'.$company_id.'?step=3&session_data='. urlencode($session_data))}}" class="btn btn-default btn-back-step"><i class="fa fa-arrow-left"></i> Go back to previous</a></li>
        <li class="pull-right"><button id="final_step_submit_btn" type="submit" class="btn btn-primary btn-next-step">Send Me Quote</button></li>
    </ul>
</div>
{!! Form::close() !!}
<script>
    $(document).ready(function() {
        $('input[type=radio][name=pickup_date_type]').change(function() {
            $this = $(this);
            if ($this.val() == 'not_sure') {
                $('#pickup_date_box').hide();
                $('#pickup_date').prop('required', false);
            } else {
                $('#pickup_date_box').show();
                $('#pickup_date').prop('required', true);
            }
        });
        // $('body').on('click', '#final_step_submit_btn', function(e) {
        //     $(this).prop('disabled', true);
        // });
        $("body").on("submit", "#formStep4", function() {
            $(this).submit(function() {
                return false;
            });
            return true;
        });
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy'
        }).on('changeDate', function(ev) {
            $(this).datepicker('hide');
        });;
    });
</script>