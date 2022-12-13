{!! Form::open(['id'=>'deleteStatuses','class'=>'ajax-form','method'=>'POST']) !!}
{{ Form::hidden('lead_status_id', $row->id) }}
<div class="modal-header" style="border: 1px solid #f0f0f0;padding-bottom:10px">
    <span style="font-size:18px;font-weight: 400;">@lang('modules.statuses.deleteLeadStatus')</span>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body">
    <div class="form-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="alert alert-danger">
                        <strong>Delete the "{{ $row->lead_status }}" Status?</strong><br>
                        <p>{{ $existing_leads }} Leads will need to be moved to a different Status.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 ">
                <div class="form-group">
                    <label style="font-size:14px">@lang('modules.statuses.choose_a_replacement'):</label>
                    <select name="new_lead_status" data-placeholder="" class="form-control" required>
                        <option value="">@lang('modules.statuses.select_a_status')</option>
                        @foreach($lead_statuses as $ls)
                        <option value="{{ $ls->lead_status }}">{{ $ls->lead_status }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer" style="background-color: #f5f5f5!important;padding: 10px 20px!important;">
    <button type="button" class="btn btn-link" data-dismiss="modal">@lang('app.cancel')</button>
    <button type="submit" class="btn btn-danger">@lang('app.delete')</button>
</div>
{!! Form::close() !!}