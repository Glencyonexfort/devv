{!! Form::open(['id'=>'updatePipelineStatuses','class'=>'ajax-form','method'=>'POST']) !!}
{{ Form::hidden('pipeline_status_id', $row->id) }}
<div class="modal-header" style="border: 1px solid #f0f0f0;padding-bottom:10px">
    <span style="font-size:18px;font-weight: 400;">@lang('modules.pipeline_statuses.editPipelineStatus')</span>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body">
    <div class="form-body">
        <div class="row">
            <div class="col-md-6 ">
                <div class="form-group">
                    <label style="font-size:14px">@lang('modules.pipeline_statuses.pipeline_status_name')</label>
                    <input type="text" name="pipeline_status" id="pipeline_status" value="{{ $row->pipeline_status }}" class="form-control" autocomplete="nope" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 ">
                <div class="form-group">
                    <label style="font-size:14px">@lang('modules.pipeline_statuses.pipeline'):</label>
                    <select name="pipeline_id" data-placeholder="" class="form-control" required>
                        @foreach($op_pipelines as $pl)
                        <option value="{{ $pl->id }}"
                        @if($pl->id == $row->pipeline_id)
                            selected=""
                        @endif    
                        >{{ $pl->pipeline }}</option>
                        @endforeach
                    </select>
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