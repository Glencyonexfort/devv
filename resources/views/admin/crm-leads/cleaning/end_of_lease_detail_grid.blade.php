<?php
    $job_type = \App\JobsCleaningType::where('id',$jobs_cleaning->job_type_id)->first();    
    $job_shift = \App\JobsCleaningShifts::where('id',$jobs_cleaning->preferred_time_range)->first();
    $cleaning_job_type = ($job_type)?$job_type->job_type_name:'';
    $cleaning_job_shift = ($job_shift)?$job_shift->shift_display_start_time:'';
?>
<div class="card view_blade_4_card">
    <span class="view_blade_4_card_span">
        <div class="card-header header-elements-inline view_blade_4_card_header">
            <h6 class="card-title card-title-mg view_blade_4_card_task">{{ $cleaning_job_type }}</h6>
        </div>
    </span>
    {{-- VIEW --}}
    <div id="update_end_of_lease_view">
        <div style="border-left:3px solid #89dd88;min-height: 14rem;" class="card-body job_left_panel_body1">
            @if($jobs_cleaning->opportunity == 'Y')
                <div class="d-flex justify-content-start align-items-center float-right">
                    <button class="show_update_end_of_lease_btn btn btn-icon"><i class="icon-pencil"></i></button>
                </div>
            @endif
            <div class="job-label-txt">
                <table class="left_panel_table" style="width: auto!important;">
                    <tbody>
                    <tr>
                        <td>
                            Preferred Time:
                        </td>
                        <td class="textalign-left">
                            <span>{{ $cleaning_job_shift}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Cleaning Address:
                        </td>
                        <td class="textalign-left">
                            <span>{{ $jobs_cleaning->address}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Bedrooms:
                        </td>
                        <td class="textalign-left">
                            <span>{{ $jobs_cleaning->bedrooms.' Bedrooms'}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Living Areas:
                        </td>
                        <td class="textalign-left">
                            <span>{{ $jobs_cleaning->bathrooms.' Bathrooms'}}</span>                                   
                        </td>
                    </tr>
                </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- FORM --}}
    <div id="update_end_of_lease_form" class="card-body p10 hidden body_margin">
        <form id="end_of_lease_form" class="custom-form" action="#">
            @csrf
            {{ Form::hidden('lead_id', $removal_opportunities->lead_id) }}
            {{ Form::hidden('opp_id', $removal_opportunities->id) }}
            <div class="form-group">
                <label>Preferred Time</label>
                        <select name="preferred_time_range" class="form-control">
                            @foreach($cleaning_shifts as $data)
                            <option value="{{ $data->id }}" 
                                @if($data->id == $jobs_cleaning->preferred_time_range)
                                selected=""
                                @endif
                                >{{ $data->shift_display_start_time }}</option>
                            @endforeach
                        </select>
            </div>
            <div class="form-group">
                <label>Cleaning Address</label>
                <textarea name="address" class="form-control">{{ $jobs_cleaning->address}}</textarea>
            </div>  
            <div class="form-group">
                <label>Bedrooms</label>
                <input class="form-control" name="bedrooms" value="{{ $jobs_cleaning->bedrooms }}" type="number"/>
            </div> 
            <div class="form-group">
                <label>Bathrooms</label>
                <input class="form-control" name="bathrooms" value="{{ $jobs_cleaning->bathrooms }}" type="number"/>
            </div>
            <div class="d-flex justify-content-start align-items-center m-t-10">
                <button type="reset" class="btn btn-light show_update_end_of_lease_btn">Cancel</button>
                <button type="button" id="update_end_of_lease_btn" class="btn bg-blue ml-3">Update</button>
            </div>

        </form>
    </div>
</div>
