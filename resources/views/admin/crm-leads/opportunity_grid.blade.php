{{-- @section('opportunity_grid') --}}
<style>
    .current_opportunity{
        border-left: 4px solid #03a9f4;
    }
</style>
<?php
$is_first_opp=0;
?>
@foreach($opportunities as $opportunity)
<?php
    $is_first_opp=0;
    if ($opportunity->id == $current_opportunity_id) {
        $is_first_opp = 1;
    }
    $current_opp = ($is_first_opp==1)?"current_opportunity light-blue-bg":"";
    $est_job_date = date(isset($global->date_format)?$global->date_format:'d/m/Y',strtotime($opportunity->est_job_date));
    if($opportunity->op_type=='Moving'){
        $op_job = App\JobsMoving::where(['crm_opportunity_id'=>$opportunity->id])->first();
    }elseif($opportunity->op_type=='Cleaning'){
        $op_job = App\JobsCleaning::where(['crm_opportunity_id'=>$opportunity->id])->first();
    }
?>
    <div id="opportunity_grid_{{ $opportunity->id }}" class="header-elements-inline opportunity_grid {{ $current_opp }}" data-id="{{ $opportunity->id }}">
        <div class="page-title" style="width: 75%;">
            <h5 class="d-flex">
                {{-- <button class=" btn btn-icon btn-sm oppurtunity_grid_1_button">{{ $opportunity->confidence.'%' }}</button> --}}
                <span class="font-weight-semibold lh16 oppurtunity_grid_1_span" style="width: 90px;width: 100%;line-height: 20px;font-weight: normal!important;">{{ $opportunity->op_type.' - '.$opportunity->job_number }} 
                    <span class="oppurtunity_grid_1_span" style="opacity: 0.8;float:right;text-transform: uppercase">{{$opportunity->op_status}}</span>
                    <br />
                    @foreach($companies_list as $data)
                            @if($data->id == $op_job->company_id)
                                {{ $data->company_name }}
                            @endif
                    @endforeach                    
                    <small class="d-block text-muted mg-0 oppurtunity-date-width">{{ $est_job_date }}</small>
                </span>                
            </h5>
        </div>

        <div class="header-elements broderline">
            <div class="list-icons">
                {{-- <a href="#" class="list-icons-item mr-2" title="Mark Completed"><i class="icon-checkmark3"></i></a> --}}
                <div class="dropdown">
                    <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown">
                    <img class="oppurtunity_grid_2_img" src="{{ asset('newassets/img/icon-edit-1.png') }}">
                    
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a data-oppid="{{ $opportunity->id }}" data-leadid="{{ $lead_id }}"  class="opportunity-edit-btn dropdown-item" title="Edit"><i class="icon-pencil5"></i>Edit</a>
                        <a data-oppid="{{ $opportunity->id }}" data-leadid="{{ $lead_id }}"  class="opportunity-remove-btn dropdown-item txt-red" title="Delete"><i class="icon-bin"></i> Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="update_opp_form_grid_{{ $opportunity->id }}" class="card-body light-blue-bg p10 hidden">
        
        <form id="update_opp_form_{{ $opportunity->id }}" class="custom-form" action="#">
            @csrf
            {{ Form::hidden('lead_id', $opportunity->lead_id) }}
            {{ Form::hidden('opp_id', $opportunity->id) }}
            <div class="form-group">
                <label>Company</label>
                <select name="company_id" class="form-control">
                    @foreach($companies_list as $data)
                        <option value="{{ $data->id }}"
                            @if($data->id == $op_job->company_id)
                            selected=""
                            @endif
                            >{{ $data->company_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Job Type</label>
                <select name="op_type" class="form-control" style="pointer-events: none;background: #f0f0f0;color: #999;">
                    @foreach($op_type as $data)
                        <option value="{{ $data->options }}"
                            @if($data->options == $opportunity->op_type)
                            selected=""
                            @endif
                            >{{ $data->options }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="op_status" class="form-control">
                    @foreach($op_status as $data)
                        <option value="{{ $data->pipeline_status }}"
                            @if($data->pipeline_status == $opportunity->op_status)
                            selected=""
                            @endif
                            >{{ $data->pipeline_status }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Estimated Job Date</label>
                <div class="input-group">
                    <span class="input-group-prepend">
                        <span class="input-group-text"><i class="icon-calendar22"></i></span>
                    </span>
                    <input name="est_job_date" type="text" class="form-control daterange-single" value="{{ $est_job_date  }}">
                </div>
            </div>
            <?php
                 $moving_field = ($opportunity->op_type!='Moving')?'hidden':'';
                 $cleaning_field = ($opportunity->op_type!='Cleaning')?'hidden':'';
            ?>
            <div id="Moving_job_fields_{{ $opportunity->id }}" class="all_job_fields {{ $moving_field }}">
                <div class="form-group">
                    <label>Estimated Start Time</label>
                    <div class="input-group">
                        <span class="input-group-prepend">
                            <span class="input-group-text"><i class="icon-watch"></i></span>
                        </span>                                
                        <input name="job_start_time" type="time" class="form-control oppLeg_leg_start_time_new pickatime" placeholder="Estimated Start Time" value="{{ $op_job->job_start_time }}">
                    </div>
                </div>
            </div>

            <div id="Cleaning_job_fields_{{ $opportunity->id }}" class="all_job_fields {{ $cleaning_field }}">
                <div class="form-group">
                    <label>Estimated Time Range</label>
                    <select name="preferred_time_range" class="form-control">
                        @if(count($cleaning_shifts)){
                            @foreach($cleaning_shifts as $data)
                                <option value="{{ $data->id }}"
                                    @if($data->id == $op_job->preferred_time_range)
                                    selected=""
                                    @endif
                                    >{{ $data->shift_display_start_time }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            {{-- <div class="form-group slidecontainer">
                <label>Confidence</label><output id="confidenceOutputId_{{$opportunity->id}}" class="txt-green" style="float: right;margin-top: 10px;font-weight: 500">{{ $opportunity->confidence }}</output>
                <input id="confidenceInputId_{{$opportunity->id}}" name="confidence" type="range" value="{{ $opportunity->confidence }}" class="slider" min="0" max="100" oninput="confidenceOutputId_{{$opportunity->id}}.value = confidenceInputId_{{$opportunity->id}}.value">
            </div> --}}

            {{-- <div class="form-group">
                <label>Value</label>
                <input name="value" type="text" class="form-control" value="{{ $opportunity->value }}">
            </div>

            <div class="form-group">
                <label>Frequency</label>
                <select name="op_frequency" class="form-control">
                    @foreach($frequency as $data)
                        <option value="{{ $data->list_option }}"
                            @if($data->list_option == $opportunity->op_frequency)
                            selected=""
                            @endif
                            >{{ $data->list_option }}</option>
                    @endforeach
                </select>
            </div> --}}

            <div class="form-group">
                <label>Contact</label>
                <select name="contact_id" class="form-control">
                    @foreach($contacts as $data)
                        <option value="{{ $data->id }}"
                            @if($data->id == $opportunity->contact_id)
                            selected=""
                            @endif
                            >{{ $data->name }}</option>
                    @endforeach
                </select>
            </div>
            <?php
                if(isset($opportunity->user_id) && $opportunity->user_id!=0){
                    $user_id = $opportunity->user_id;
                }else{
                    $user_id = auth()->user()->id;
                }
            ?>
                <div class="form-group">
                    <label>Users</label>
                    <select name="user_id" class="form-control">
                        @foreach($without_worker_users as $data)
                            <option value="{{ $data->user_id }}" 
                                @if($data->user_id == $user_id) selected="" @endif >{{ $data->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

            <div class="form-group">
                <label>Lead info</label>
                <select name="lead_info" class="form-control">
                    @foreach($lead_info as $data)
                        <option value="{{ $data->list_option }}"
                            @if($data->list_option == $op_job->lead_info)
                            selected=""
                            @endif
                            >{{ $data->list_option }}</option>
                    @endforeach
                </select>
            </div>


            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" rows="3" cols="3" class="form-control">{{ $opportunity->notes }}</textarea>
            </div>
            <div class="d-flex justify-content-start align-items-center m-t-10">
                <button class="btn btn-light opportunity-edit-btn" data-oppid="{{ $opportunity->id }}">Cancel</button>
                <button type="button" class="btn bg-blue ml-3 update_opportunity_btn" data-oppid="{{ $opportunity->id }}">Update</button>
            </div>
        </form>
    </div>
    @endforeach
{{-- @endsection --}}