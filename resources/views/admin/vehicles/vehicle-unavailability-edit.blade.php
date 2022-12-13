<form id="update_unavailability_form" action="{{ route('admin.crm-leads.ajaxStoreLead') }}" method="post">
            @csrf
            {{ Form::hidden('id', $vehicle_unavailability->id) }}
        <div class="modal-body">
                <div class="form-body">                        
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:14px">Vehicle</label>
                                <select name="vehicle_id" class="form-control" disabled>
                                    @foreach($vehicles as $data)
                                        <option value="{{ $data->id }}"
                                            @if($vehicle_unavailability->vehicle_id == $data->id)
                                        selected=""
                                        @endif >{{ $data->vehicle_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-size:14px">From Date</label>
                                    <div class="input-group">
                                        <span class="input-group-prepend">
                                            <span class="input-group-text"><i class="icon-calendar52"></i></span>
                                        </span>
                                    <input type="text" name="from_date" class="form-control daterange-single" autocomplete="nope" value="{{ $vehicle_unavailability->from_date }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-size:14px">From Time</label>
                                    <div class="input-group">
                                        <span class="input-group-prepend"><span class="input-group-text"><i class="icon-alarm"></i></span></span>
                                        <input name="from_time" type="time" class="form-control pickatime-editable" value="{{ $vehicle_unavailability->from_time }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        <div class="col-md-12">
                            <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-size:14px">To Date</label>
                                    <div class="input-group">
                                        <span class="input-group-prepend">
                                            <span class="input-group-text"><i class="icon-calendar52"></i></span>
                                        </span>
                                        <input type="time" name="to_date" class="form-control daterange-single" autocomplete="nope" value="{{ $vehicle_unavailability->to_date }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-size:14px">To Time</label>
                                    <div class="input-group">
                                        <span class="input-group-prepend"><span class="input-group-text"><i class="icon-alarm"></i></span></span>
                                        <input name="to_time" type="time" class="form-control pickatime-editable" value="{{ $vehicle_unavailability->to_time }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label style="font-size:14px">Reason</label>
                                <textarea name="reason" class="form-control">{{ $vehicle_unavailability->reason }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>                    
                <div id="edit-vehicle-validation-errors" class="col-md-6 alert alert-danger hidden"></div>
        </div>
        <div class="modal-footer" style="background-color: #f5f5f5!important;padding: 10px 20px!important;">
            <button type="button" class="btn btn-link" data-dismiss="modal" id="close-update-vehicle-unavailability">Cancel</button>
            <button id="update-vehicle-unavailability" type="button" class="btn btn-success">Update</button>
            {{-- <input id="create_leatn" type="submit" value="Create Lead" class="btn btn-success"/> --}}
        
    </div>
</form>