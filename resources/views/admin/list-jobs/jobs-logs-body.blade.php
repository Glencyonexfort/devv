<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">
        <img
                src="{{asset('img/icons/'.$job_log->sys_log_types->log_type_icon)}}"
                style="width:20px;"> {!! strtoupper($job_log->sys_log_types->log_type) !!}
    </h4>
</div>
<div class="modal-body">
    <div class="form-group">
        {!! nl2br($job_log->log_details)  !!}
        <div class="form-control-focus"> </div>
    </div>

</div>