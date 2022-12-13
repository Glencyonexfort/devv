<?php
//getting lists
    $cleaning_teams = \App\JobsCleaningTeams::where(['tenant_id'=>auth()->user()->tenant_id,'active'=>'Y'])->get();
    $cleaning_shifts = \App\JobsCleaningShifts::where(['tenant_id'=>auth()->user()->tenant_id])->get();
    if($team_roaster){
        $job_team = \App\JobsCleaningTeams::where(['id'=>$team_roaster->team_id,'tenant_id'=>auth()->user()->tenant_id,'active'=>'Y'])->first();        
        $job_shift = \App\JobsCleaningShifts::where('id',$team_roaster->job_shift_id)->first();
        $cleaning_job_shift = ($job_shift)?$job_shift->shift_display_start_time:'';
    }else{
        $job_team=[];
        $job_shift=[];
        $cleaning_job_shift='';
    }
?>
<article>
    <table>
        <thead>
            <tr>
                <th>Job Date</th>
                <th><span >Start Time</span></th>
                <th><span >Team</th>
            </tr>
        </thead>
        <tbody>
            @if($team_roaster)
                <input type="hidden" id="cleaning_team_roaster_id" value="{{ $team_roaster->id }}"/>
                <tr id="team_line_view">
                    <td>
                        {{ date($global->date_format, strtotime($team_roaster->job_date)) }}
                    </td>
                    <td>
                        {{ $cleaning_job_shift }}
                    </td>
                    <td>                        
                        <span data-prefix style="margin-right: 22px;">
                            {{ $job_team->team_name }}

                            <div class="list-icons float-right">
                                <div class="dropdown">
                                    <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown"><i class="icon-menu"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="#" class="dropdown-item edit_team_btn" data-toggle="modal" data-target="#call"><i class="icon-pencil"></i> Edit</a>                                            
                                        </div>
                                </div>
                            </div> 

                            <?php
                            if($team_roaster->roster_status==NULL || empty($team_roaster->roster_status)){
                                $notify_btn_status = "";
                            }else{
                                $notify_btn_status = "readonly_field";
                            }

                            if($team_roaster->roster_status==NULL || $team_roaster->roster_status=="Awaiting Confirmation" || $team_roaster->roster_status=="Confirmed"){
                                $reassign_btn_status = "";
                            }else{
                                $reassign_btn_status = "readonly_field";
                            }
                        ?>
                        <br/><button type="button" class="btn btn-light leg_sm_btn notify_team_lead_btn {{ $notify_btn_status }}" title="Notify Team Lead" data-id="{{ $team_roaster->id }}">Notify Team Lead</button>
                            @if($team_roaster->roster_status=="Awaiting Confirmation")
                                <span class="leg_status_txt text-red">{{ $team_roaster->roster_status }}</span>
                            @elseif($notify_btn_status!="")
                                <span class="leg_status_txt text-green">{{ $team_roaster->roster_status }}</span>
                            @endif 
                        <br/><button type="button" class="btn btn-light leg_sm_btn {{ $reassign_btn_status }}" data-toggle="modal" data-target="#reassign_team_popup_{{ $team_roaster->id }}" title="Reassign Team" data-id="{{ $team_roaster->id }}">Reassign Team</button>                            
                        </span>
                    </td>
                    <div id="reassign_team_popup_{{ $team_roaster->id }}" class="modal fade" tabindex="-1">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header" style="border: 1px solid #f0f0f0;padding-bottom:10px">
                                    <span style="font-size:18px;font-weight: 400;">Reassign Team</span>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                        <div class="form-body">                        
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <select id="reassign_team_id_{{ $team_roaster->id }}" name="team_id" class="form-control">
                                                            @foreach($cleaning_teams as $data)
                                                                <option value="{{ $data->id }}"
                                                                    @if($data->id == $team_roaster->team_id)
                                                                    selected=""
                                                                    @endif
                                                                    >{{ $data->team_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer" style="padding: 0px">
                                                <button type="button" class="btn btn-primary save_reassign_team_btn" data-id={{ $team_roaster->id }} data-dismiss="modal">Save</button>            
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                </tr>
                <tr id="team_line_edit" class="bgblu hidden" data-row="0">
                    <td>
                        <span>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-prepend"><span class="input-group-text"><i class="icon-calendar22"></i></span></span>
                                    <input id="cleaning_team_date" type="text" class="form-control daterange-single" value="{{ date($global->date_format, strtotime($team_roaster->job_date)) }}">
                                </div>
                            </div>
                        </span>
                    </td>
                    <td>
                        <span>
                            <div class="form-group">
                                <select id="cleaning_team_shift" class="form-control">
                                    @foreach($cleaning_shifts as $data)
                                        <option value="{{ $data->id }}"
                                            @if($data->id == $team_roaster->job_shift_id)
                                            selected=""
                                            @endif
                                            >{{ $data->shift_display_start_time }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </span>
                    </td>
                    <td>
                            <div class="form-group">
                                <select id="job_cleaning_team" class="form-control
                                @if($team_roaster->roster_status!=NULL || !empty($team_roaster->roster_status))
                                    readonly_field 
                                    @endif">
                                    @foreach($cleaning_teams as $data)
                                        <option value="{{ $data->id }}"
                                            @if($data->id == $team_roaster->team_id)
                                            selected=""
                                            @endif
                                            >{{ $data->team_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-flex justify-content-start align-items-center m-t-10">
                                <button type="button" class="btn btn-light cancel_edit_team_btn"> Cancel</button>
                                <button id="update_team_btn" type="button" class="btn btn-success ml-2"> Update</button>
                            </div>
                    </td>
                </tr>
            @else
            <tr id="team_line_new" class="bgblu" data-row="0">
                <td>
                    <span>
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-prepend"><span class="input-group-text"><i class="icon-calendar22"></i></span></span>
                                <input id="cleaning_team_date_new" type="text" class="form-control daterange-single" value="{{ date('d/m/Y') }}">
                            </div>
                        </div>
                    </span>
                </td>
                <td>
                    <span>
                        <div class="form-group">
                            <select id="cleaning_team_shift_new" class="form-control">
                                @foreach($cleaning_shifts as $data)
                                    <option value="{{ $data->id }}">{{ $data->shift_display_start_time }}</option>
                                @endforeach
                            </select>
                        </div>
                    </span>
                </td>
                <td>
                        <div class="form-group">
                            <select id="job_cleaning_team_new" class="form-control">
                                @foreach($cleaning_teams as $data)
                                    <option value="{{ $data->id }}">{{ $data->team_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex justify-content-start align-items-center m-t-10">
                            <button type="button" class="btn btn-light cancel_edit_team_btn"> Cancel</button>
                            <button id="add_new_team_btn" type="button" class="btn btn-success ml-2"> Save</button>
                        </div>
                </td>
            </tr>
            @endif
    </tbody>                
    </table>
</article>  