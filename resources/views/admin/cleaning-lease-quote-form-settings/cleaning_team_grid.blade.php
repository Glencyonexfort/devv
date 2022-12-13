<table>
        <thead>
            <tr>
                <th>Team Name</th>
                <th>Team Priority</th>
                <th>Team Colour</th>
                <th>Team Rating</th>
                <th style="width:14%">Active</th>
                <th style="width: 26%!important;"></th>
            </tr>
        </thead>
        <tbody>
            @if(count($cleaning_teams))
            @foreach($cleaning_teams as $data)
                <tr id="row_line_{{ $data->id }}">
                    <td>
                        {{ $data->team_name }}
                    </td>
                    <td>
                        {{ $data->team_priority }}                        
                    </td>
                    <td>
                        <div style="line-height: 0px;">
                            <p style="width: 60px;height: 16px;background-color: {{ $data->team_colour }}"></p>
                            {{ $data->team_colour }}
                        </div>
                    </td>
                    <td>
                        {{ $data->team_rating }}                        
                    </td>
                    <td>
                        {{ $data->active }}                        
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
                        <input type="text" id="team_name_{{ $data->id }}" class="form-control" value="{{ $data->team_name }}"/>
                    </td>
                    <td>
                        <input type="number" id="team_priority_{{ $data->id }}" class="form-control number3" value="{{ $data->team_priority }}"/>                    
                    </td>
                    <td>
                        <input type="color" id="team_colour_{{ $data->id }}" class="form-control" value="{{ $data->team_colour }}"/>                    
                    </td>
                    <td>
                        <input type="number" id="team_rating_{{ $data->id }}" class="form-control number3" value="{{ $data->team_rating }}"/>                    
                    </td>
                    <td>
                        <select id="team_active_{{ $data->id }}" class="form-control">
                            <option value="Y" 
                            @if($data->active=='Y') selected=""
                            @endif
                            >Y</option>
                            <option value="N" 
                            @if($data->active=='N') selected=""
                            @endif
                            >N</option>
                        </select>
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
                    <td colspan="6">
                        No record found!
                    </td>
                </tr>
            @endif
            {{-- New line item --}}
            <tr id="new_line" class="hidden">
                <td>
                    <input type="text" id="team_name" class="form-control"/>
                </td>
                <td>
                    <input type="number" id="team_priority" class="form-control number3"/>
                </td>
                <td>
                    <input type="color" id="team_colour" class="form-control" />                    
                </td>
                <td>
                    <input type="number" id="team_rating" class="form-control number3"/>                    
                </td>
                <td>
                    <select id="team_active" class="form-control">
                        <option value="Y" selected>Y</option>
                        <option value="N">N</option>
                    </select>
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