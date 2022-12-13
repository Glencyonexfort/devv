<table class="payment">
        <thead>
            <tr>
                <th><span >Shift Name</span></th>
                <th>Shift Start Time (display only)</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @if(count($cleaning_shifts))
            @foreach($cleaning_shifts as $data)                            
                <tr id="row_line_{{ $data->id }}">
                    <td>
                        {{ $data->shift_name }}
                    </td>
                    <td>
                        {{ $data->shift_display_start_time }}                        
                    </td>
                    <td>
                        <div class="list-icons float-right">
                            <div class="dropdown">
                                <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown"><i class="icon-menu"></i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="#" class="dropdown-item edit_cleaningshift_btn" data-toggle="modal" data-target="#call" data-id="{{ $data->id }}"><i class="icon-pencil"></i> Edit</a>
                                        <a href="#" class="delete_cleaningshift_btn dropdown-item" data-id="{{ $data->id }}" style="color: #ff0000"><i class="icon-trash"></i> Delete</a>        
                                    </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr id="edit_line_{{ $data->id }}" class="hidden">
                    <td>
                        <input type="text" id="cleaning_shift_name_{{ $data->id }}" class="form-control" value="{{ $data->shift_name }}" placeholder="2-PM"/>
                    </td>
                    <td>
                        <input type="text" id="cleaning_shift_display_start_time_{{ $data->id }}" class="form-control" value="{{ $data->shift_display_start_time }}" placeholder="2pm-3pm"/>                    
                    </td>
                    <td>
                        <div class="d-flex justify-content-start align-items-center m-t-10">
                            <button type="button" class="btn btn-light cancel_cleaningshift_btn" data-id="{{ $data->id }}"> Cancel</button>
                            <button type="button" class="btn btn-success ml-2 update_cleaningshift_btn" data-id="{{ $data->id }}"> Update</button>
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
                    <input type="text" id="cleaning_shift_name" class="form-control" placeholder="2-PM"/>
                </td>
                <td>
                    <input type="text" id="cleaning_shift_display_start_time" class="form-control" placeholder="2pm-3pm"/>                    
                </td>
                <td>
                    <div class="d-flex justify-content-start align-items-center m-t-10">
                        <button type="button" class="btn btn-light" id="cancel_cleaningshift_btn"> Cancel</button>
                        <button type="button" class="btn btn-success ml-2 save_cleaningshift_btn"> Save</button>
                    </div>
                </td>
            </tr>            
        </tbody>
    </table> 
    <input type="hidden" id="job_type_id" value="{{ $job_type_id }}"/>   
    <div class="float-left">
        <button id="add_cleaningshift_btn" type="button" class="btn plus_btn"><i class="icon-plus3"></i></button>
    </div> 