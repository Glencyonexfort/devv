{!! Form::open(['id'=>'updateStatuses','class'=>'ajax-form','method'=>'PUT']) !!}
{{ Form::hidden('lead_status_id', $row->id) }}
<div class="modal-header" style="border: 1px solid #f0f0f0;padding-bottom:10px">
    <span style="font-size:18px;font-weight: 400;">@lang('modules.statuses.editLeadStatus')</span>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body">
    <div class="form-body">
        <div class="row">
            <div class="col-md-6 ">
                <div class="form-group">
                    <label style="font-size:14px">@lang('modules.statuses.lead_status_name')</label>
                    <input type="text" name="lead_status" id="lead_status" value="{{ $row->lead_status }}" class="form-control" autocomplete="nope" required>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer" style="background-color: #f5f5f5!important;padding: 10px 20px!important;">
    <button type="button" class="btn btn-link" data-dismiss="modal">@lang('app.cancel')</button>
    <button type="submit" class="btn btn-success">@lang('app.update')</button>
</div>
{!! Form::close() !!}