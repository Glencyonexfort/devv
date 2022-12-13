           
    @if($job_legs)
    @foreach($job_legs as $leg)
    <?php
    // If multiple trips not allow
        if($leg->has_multiple_trips==0){
            continue;
        }
        $job_leg_trips = \App\JobsMovingLegTrips::where('jobs_moving_leg_id', '=', $leg->id)->get();
    ?>
    <article style="border: 1px solid #d2d2d2;border-bottom: solid 2rem #f6f5f5;"> 
    <div class="opperation_leg_trip_box">
        <span>{{ 'Leg # '.$leg->leg_number }}</span>
        <span>{{ $leg->pickup_address }}</span>
        <span>{{ $leg->drop_off_address }}</span>
        <span>{{ date($global->date_format,strtotime($leg->leg_date)) }}</span>
    </div>         
    <table class="operation_leg_trip">
        <thead>
            <tr>
                <th>Trips</th>
                <th><span >Pickup Address</span></th>
                <th><span >Drop off Address</span></th>
                <th><span style="margin-right: 22px;">Trip Notes</span></th>
            </tr>
        </thead>
        <tbody>
            @if($job_leg_trips)
            @foreach($job_leg_trips as $item)
                <tr id="oppTrip_line_div_view_{{ $item->id }}" class="oppTrip_line_div">
                    <td>
                        {{ $item->trip_number }}
                    </td>
                    <td>
                        <span>{{ $item->pickup_address }}</span><br/>                        
                    </td>
                    <td>
                        <span>{{ $item->drop_off_address }}</span>
                    </td>
                    <td>
                        <span style="margin-right: 22px;">
                            {{$item->trip_notes }}
                            <div class="list-icons float-right">
                                <div class="dropdown">
                                    <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown"><i class="icon-menu"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="#" class="dropdown-item edit_oppTrip_btn" data-id="{{ $item->id }}" data-row="{{ $leg->id }}"><i class="icon-pencil"></i> Edit</a>
                                            <a href="#" class="delete_oppTrip_btn dropdown-item" data-id="{{ $item->id }}" style="color: #ff0000"><i class="icon-trash"></i> Delete</a>        
                                        </div>
                                </div>
                            </div> 
                        </span>
                    </td>
                </tr>

                <tr id="oppTrip_line_div_edit_{{ $item->id }}" class="bgblu hidden" data-row="0">
                    <td>
                        {{ $item->trip_number }}
                    </td>
                    <td>
                        <span>
                            <div class="form-group">                                
                                <input id="oppTrip_pickup_address_edit_{{ $item->id }}" class="form-control mt-1 geo-address" name="pickup_address" value="{{ $item->pickup_address }}" placeholder="If no pickup, leave blank"/>
                            </div>
                            
                        </span>
                    </td>
                    <td>
                        <span>
                            <div class="form-group">                                
                                <input id="oppTrip_drop_off_address_edit_{{ $item->id }}" class="form-control mt-1 geo-address" name="drop_off_address" value="{{ $item->drop_off_address }}" placeholder="If no drop-off, leave blank"/>
                            </div>
                        </span>
                    </td>
                    <td>
                        <div class="form-group">
                            <textarea id="oppTrip_notes_edit_{{ $item->id }}" name="notes" class="form-control">{{ $item->trip_notes }}</textarea>
                        </div>
                        <div class="d-flex justify-content-start align-items-center m-t-10">
                            <button type="button" class="btn btn-light cancel_update_oppTrip_btn" data-id="{{ $item->id }}"> Cancel</button>
                            <button type="button" class="btn btn-success ml-2 update_oppTrip_btn" data-id="{{ $item->id }}"> Update</button>
                        </div>
                    </td>
                </tr>
            @endforeach
            @endif


            <tr id="oppTrip_line_div_new_{{ $leg->id }}" class="bgblu oppTrip_line_div_{{ $leg->id }} hidden" data-row="0">
                <td>
                    
                </td>
                <td>
                    <span>
                        <div class="form-group">                                
                            <input id="oppTrip_pickup_address_new_{{ $leg->id }}" class="form-control mt-1 geo-address" name="pickup_address" value="" placeholder="If no pickup, leave blank"/>
                        </div>                        
                    </span>
                </td>
                <td>
                    <span>
                        <div class="form-group">                                
                            <input id="oppTrip_drop_off_address_new_{{ $leg->id }}" class="form-control mt-1 geo-address" name="drop_off_address" value="" placeholder="If no drop-off, leave blank"/>
                        </div>
                    </span>
                </td>
                <td>
                    <div class="form-group">
                        <textarea id="oppTrip_notes_new_{{ $leg->id }}" class="form-control"></textarea>
                    </div>
                    <div class="d-flex justify-content-start align-items-center m-t-10">
                        <button type="button" class="btn btn-light cancel_oppTrip_btn" data-leg="{{ $leg->id }}"> Cancel</button>
                        <button type="button" class="btn btn-success ml-2 save_oppTrip_btn" data-leg="{{ $leg->id }}"> Save</button>
                    </div>
                </td>
            </tr>

    </tbody>                
    </table>

<div class="float-left">
    <button id="add_oppTrip_line_{{ $leg->id }}" type="button" class="btn btn-light add_oppTrip_line" data-leg="{{ $leg->id }}"><i class="icon-plus3"></i></button>
</div>
</article>  
@endforeach
@endif