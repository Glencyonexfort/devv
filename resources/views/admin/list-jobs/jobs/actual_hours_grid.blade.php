<article>                    
    <table class="actual_hour">
        <thead>
            <tr>
                <th><span >Leg #</span></th>
                <th><span >Leg Date</span></th>
                <th><span >Job Start Time</span></th>
                <th><span >Job End Time</span></th>
                <th><span style="margin-right: 22px;">Total Hours</span></th>
            </tr>
        </thead>
        <tbody>
            @inject('job_moving_leg', 'App\JobsMovingLegs')
            @if(count($job_legs))
            @foreach($job_legs as $item)
                <tr id="hours_line_div_view_{{ $item->id }}" class="hours_line_div">
                    <td>
                        <span>{{ $item->leg_number }}</span>
                    </td>
                    <td>
                        <span>{{ date($global->date_format,strtotime($item->leg_date)) }}</span>
                    </td>
                    <td>
                        <span>
                            {{ ($item->actual_start_time!=NULL)?date('h:i A',strtotime($item->actual_start_time)):'-' }}
                        </span><br/>
                        <p class="weight500">
                            {{ $item->actual_start_location }}
                        </p>
                    </td>
                    <td>
                        <span>
                            {{ ($item->actual_finish_time!=NULL)?date('h:i A',strtotime($item->actual_finish_time)):'-' }}
                        </span><br/>
                        <p class="weight500">
                            {{ $item->actual_finish_location }}
                        </p>
                    </td>
                    <td>                        
                        <span data-prefix style="margin-right: 22px;">
                            {{ $item->calculateTimeDuration() }}
                            <div class="list-icons float-right">
                                <div class="dropdown">
                                    <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown"><i class="icon-menu"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="#" class="dropdown-item edit_hours_btn" data-toggle="modal" data-target="#call" data-id="{{ $item->id }}"><i class="icon-pencil"></i> Edit</a>                                            
                                        </div>
                                </div>
                            </div> 
                        </span>
                    </td>
                </tr>
                <tr id="hours_line_div_edit_{{ $item->id }}" class="bgblu hidden" data-row="0">
                    <td>
                        <span>
                            <span>{{ $item->leg_number }}</span>
                        </span>
                    </td>
                    <td>
                        <span>
                            <span>{{ date($global->date_format,strtotime($item->leg_date)) }}</span>
                        </span>
                    </td>
                    <td>
                        <span>
                            <div class="form-group">
                                <input id="hours_actual_start_time_edit_{{ $item->id }}" type="time" class="form-control pickatime" value="{{ $item->actual_start_time }}" placeholder="Start">
                            </div>
                        </span>
                    </td>
                    <td>
                        <span>
                            <div class="form-group">
                                <input id="hours_actual_finish_time_edit_{{ $item->id }}" type="time" class="form-control pickatime" value="{{ $item->actual_finish_time }}" placeholder="Start">
                            </div>
                        </span>
                    </td>
                    <td>                        
                        <div class="d-flex justify-content-start align-items-center m-t-10">
                            <button type="button" class="btn btn-light cancel_update_hours_btn" data-id="{{ $item->id }}"> Cancel</button>
                            <button type="button" class="btn btn-success ml-2 update_hours_btn" data-id="{{ $item->id }}"> Update</button>
                        </div>
                    </td>
                </tr>
            @endforeach
            @else
            <tr>
                <td colspan="5">No record available !</td>
            </tr>
            @endif            

</tbody>                
</table>
</article>  