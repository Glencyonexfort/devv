@if(count($job_legs) > 0)
                    @foreach($job_legs as $leg)                    
                    <div class="card-body job_left_panel_body">
                        <div class="list-icons pull-right">
                            {{-- <a href="#" class="list-icons-item mr-2" title="Mark Completed"><i class="icon-checkmark3"></i></a> --}}
                            <div class="dropdown">
                                <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown">
                                <img src="{{ asset('newassets/img/icon-edit-1.png') }}">            
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a data-legid="{{ $leg->id }}" class="show_update_operation_form_btn dropdown-item cursor-pointer" title="Edit"><i class="icon-pencil5"></i>Edit</a>
                                    <a data-legid="{{ $leg->id }}" class="operation-remove-btn dropdown-item mr-2 cursor-pointer txt-red" title="Delete"><i class="icon-bin"></i>Delete</a>
                                </div>
                            </div>
                        </div>
                        
                        <p class="job-label-txt float-right" style="color: #d9424d;margin-right: 18px;">
                            {{ date($global->date_format,strtotime($leg->leg_date)) }}
                        </p>  
                            <p class="job-label-txt font-bold" style="font-size: 16px;">
                                Leg {{ $leg->leg_number }}
                            </p>                            
                              
                            <p>
                                <table class="left_panel_table" style="width: 75%!important">
                                    <tbody>
                                    <tr>
                                        <td>{{ \App\User::where('id', '=', $leg->driver_id)->pluck('name')->first() }}</td>
                                        <td style="text-align: left;padding-left: 10px;border-left:1px solid #ccc!important">
                                            {{ \App\Vehicles::where('id', '=', $leg->vehicle_id)->pluck('vehicle_name')->first() }}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </p>
                            <p colspan="2" class="mb0">{{ $leg->pickup_address }}</p>
                            <p colspan="2" class="mb0">{{ $leg->drop_off_address }}</p>
                            <p class="mb0" style="font-weight: normal!important">{{ $leg->notes }}</p>
                            
                    </div>

                    <div id="update_oppertation_form_grid_{{ $leg->id }}" class="card-body light-blue-bg p10 hidden">
                            <form id="update_oppertation_form_{{ $leg->id }}" class="custom-form" action="#">
                                @csrf
                                <div class="form-group">
                                    <label>Leg Date</label>
                                    <div class="input-group">
                                        <span class="input-group-prepend"><span class="input-group-text"><i class="icon-calendar22"></i></span></span>
                                        <input name="leg_date" type="text" class="form-control daterange-single" value="{{ date($global->date_format, strtotime($leg->leg_date)) }}">
                                    </div>
                                </div>
            
                                <div class="form-group">
                                    <label>Pickup Address</label>
                                    <textarea name="pickup_address" class="form-control">{{ $leg->pickup_address }}</textarea>
                                </div>
            
                                <div class="form-group">
                                    <label>Drop off Address</label>
                                    <textarea name="drop_off_address" class="form-control">{{ $leg->drop_off_address }}</textarea>
                                </div>
                        
                                <div class="form-group">
                                    <label>Driver</label>
                                    <select name="driver_id" class="form-control">
                                        @foreach($drivers as $data)
                                            <option value="{{ $data->id }}"
                                                @if($data->id == $leg->driver_id)
                                                selected=""
                                                @endif
                                                >{{ $data->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
            
                                <div class="form-group">
                                    <label>Vehicle</label>
                                    <select name="vehicle_id" class="form-control">
                                        @foreach($vehicles as $data)
                                            <option value="{{ $data->id }}"
                                                @if($data->id == $leg->vehicle_id)
                                                selected=""
                                                @endif
                                                >{{ $data->vehicle_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
            
                                <div class="form-group">
                                    <label>Job Type</label>
                                    <select name="job_type" class="form-control">
                                        @foreach($job_type as $data)
                                            <option value="{{ $data->options }}"
                                                @if($data->options == $leg->job_type)
                                                selected=""
                                                @endif
                                                >{{ $data->options }}</option>
                                        @endforeach
                                    </select>
                                </div>
            
                                <div class="form-group">
                                    <label>Leg Status</label>
                                    <select name="leg_status" class="form-control">
                                        @foreach($leg_status as $data)
                                            <option value="{{ $data->options }}"
                                                @if($data->options == $leg->leg_status)
                                                selected=""
                                                @endif
                                                >{{ $data->options }}</option>
                                        @endforeach
                                    </select>
                                </div>
            
                                <div class="form-group">
                                    <label>Dispatch Notes</label>
                                    <textarea name="notes" class="form-control">{{ $leg->notes }}</textarea>
                                </div>
            
                                <div class="d-flex justify-content-start align-items-center m-t-10">
                                    <button type="button" class="btn btn-light show_update_operation_form_btn" data-legid="{{ $leg->id }}">Cancel</button>
                                    <button type="button" class="btn bg-blue ml-3 update_operation_btn" data-legid="{{ $leg->id }}">Update</button>
                                </div>
                        
                            </form>
                        </div>
                @endforeach    
            @else
                <div class="job_left_panel_body">
                    <p class="muted">No Operations</p>
                </div>
            @endif