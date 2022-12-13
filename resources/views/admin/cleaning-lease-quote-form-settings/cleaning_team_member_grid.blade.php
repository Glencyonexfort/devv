<?php
    $teams = \App\JobsCleaningTeams::where(['tenant_id'=> auth()->user()->tenant_id,'job_type_id'=>$job_type_id])->get();
    $persons = \App\PplPeople::where(['tenant_id'=> auth()->user()->tenant_id])->get();
?>
<table>
        <thead>
            <tr>
                <th>Team Name</th>
                <th>Team Member</th>
                <th style="width: 26%!important;"></th>
            </tr>
        </thead>
        <tbody>
            @if(count($cleaning_team_members))
            @foreach($cleaning_team_members as $data)
                <tr id="row_line_{{ $data->id }}">
                    <td>
                        {{ $data->team_name }}
                    </td>
                    <td>
                        {{ $data->person_name }}                       
                    </td>
                    <td>
                        <div class="list-icons float-right">
                            <div class="dropdown">
                                <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown"><i class="icon-menu"></i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="#" class="dropdown-item edit_cleaningteam_btn" data-toggle="modal" data-target="#call" data-id="{{ $data->id }}"><i class="icon-pencil"></i> Edit</a>
                                        <a href="#" class="delete_cleaningteam_btn dropdown-item" data-id="{{ $data->id }}" style="color: #ff0000"><i class="icon-trash"></i> Delete</a>        
                                    </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr id="edit_line_{{ $data->id }}" class="hidden">
                    <td>
                        <div class="form-group">
                            <select id="team_id_{{ $data->id }}" class="form-control">
                                @foreach($teams as $team)
                                <option value="{{ $team->id }}"
                                    @if($team->id == $data->team_id)
                                    selected=""
                                    @endif
                                    >{{ $team->team_name }}</option>
                                @endforeach
                            </select>                            
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <select id="person_id_{{ $data->id }}" class="form-control">
                                @foreach($persons as $person)
                                <option value="{{ $person->id }}"
                                    @if($person->id == $data->person_id)
                                    selected=""
                                    @endif
                                    >{{ $person->first_name.' '.$person->last_name  }}</option>
                                @endforeach
                            </select>                            
                        </div>
                    </td>
                    <td>
                        <div class="d-flex justify-content-start align-items-center m-t-10">
                            <button type="button" class="btn btn-light cancel_cleaningteam_btn" data-id="{{ $data->id }}"> Cancel</button>
                            <button type="button" class="btn btn-success ml-2 update_cleaningteam_btn" data-id="{{ $data->id }}"> Update</button>
                        </div>
                    </td>
                </tr>
            @endforeach
            @else
                <tr>
                    <td colspan="3">
                        No record found!
                    </td>
                </tr>
            @endif
            {{-- New line item --}}
            <tr id="new_line" class="hidden">
                <td>
                    <div class="form-group">
                        <select id="team_id" class="form-control">
                            @foreach($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->team_name }}</option>
                            @endforeach
                        </select>                            
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <select id="person_id" class="form-control">
                            @foreach($persons as $person)
                            <option value="{{ $person->id }}">{{ $person->first_name.' '.$person->last_name  }}</option>
                            @endforeach
                        </select>                            
                    </div>
                </td>
                <td>
                    <div class="d-flex justify-content-start align-items-center m-t-10">
                        <button type="button" class="btn btn-light" id="cancel_cleaningteam_btn"> Cancel</button>
                        <button type="button" class="btn btn-success ml-2 save_cleaningteam_btn"> Save</button>
                    </div>
                </td>
            </tr>            
        </tbody>
    </table> 
    <input type="hidden" id="job_type_id" value="{{ $job_type_id }}"/>   
    <div class="float-left">
        <button id="add_cleaningteam_btn" type="button" class="btn plus_btn"><i class="icon-plus3"></i></button>
    </div> 