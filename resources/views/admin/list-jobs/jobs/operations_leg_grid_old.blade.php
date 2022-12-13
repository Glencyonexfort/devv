<article>
    <table class="operation_leg">
        <thead>
            <tr>
                <th>#</th>
                <th><span >Address</span></th>
                <th><span >Leg Date/<br/>Estimated Time</span></th>
                {{-- <th><span >Leg Status/<br/>Job Type</span></th> --}}
                <th><span >Driver/<br/>Offsiders</span></th>
                <th><span >Vehicle</span></th>
                <th><span >Multiple<br/>Trips?</span></th>
                <th><span style="margin-right: 22px;">Dispatch Notes</span></th>
            </tr>
        </thead>
        <tbody>
            @if(count($job_legs))
                @foreach($job_legs as $item)
                    <tr id="oppLeg_line_div_view_{{ $item->id }}" class="oppLeg_line_div">
                        <td>
                            {{ $item->leg_number }}
                        </td>
                        <td>
                            <span>{{ $item->pickup_address }}</span><br/>
                            <span>{{ $item->drop_off_address }}</span>

                        </td>
                        <td>
                            <span>
                                {{ date($global->date_format,strtotime($item->leg_date)) }}
                                <br/>
                                <hr style="margin: 5px 0px;"/>
                                {{ date('H:i',strtotime($item->est_start_time)).' - '.date('H:i',strtotime($item->est_finish_time)) }}                         
                            </span>
                        </td>
                        <td>
                            <span>
                                @foreach($drivers as $d)
                                @if($d->id==$item->driver_id)
                                    {{ $d->name }}
                                    @break
                                @endif
                                @endforeach
                            </span>
                            @if($item->offsider_ids)
                            <br/><hr style="margin: 5px 0px;"/>
                            <span>
                                <?php
                                    $offsiders = explode(',',$item->offsider_ids);
                                ?>                            
                                    @foreach($offsiders as $sider)
                                        @foreach($people as $p)
                                            @if($p->id==$sider)
                                                {{ $p->name }},
                                                @break
                                            @endif
                                        @endforeach
                                    @endforeach
                            </span>
                            @endif

                            <?php
                                if($item->leg_status==NULL || empty($item->leg_status)){
                                    $notify_btn_status = "";
                                }else{
                                    $notify_btn_status = "readonly_field";
                                }

                                if($item->leg_status==NULL || $item->leg_status=="Awaiting Confirmation" || $item->leg_status=="Confirmed"){
                                    $reassign_btn_status = "";
                                }else{
                                    $reassign_btn_status = "readonly_field";
                                }
                            ?>
                            <br/><button type="button" class="btn btn-light leg_sm_btn notify_driver_btn {{ $notify_btn_status }}" title="Notify Driver" data-id="{{ $item->id }}">Notify Driver</button>
                                @if($item->leg_status=="Awaiting Confirmation")
                                    <span class="leg_status_txt text-red">{{ $item->leg_status }}</span>
                                @elseif($notify_btn_status!="")
                                    <span class="leg_status_txt text-green">{{ $item->leg_status }}</span>
                                @endif 
                            <br/><button type="button" class="btn btn-light leg_sm_btn {{ $reassign_btn_status }}" data-toggle="modal" data-target="#reassign_driver_popup_{{ $item->id }}" title="(Re)Assign Driver" data-id="{{ $item->id }}">(Re)Assign Driver</button>
                        </td>
                        <div id="reassign_driver_popup_{{ $item->id }}" class="modal fade" tabindex="-1">
                            <div class="modal-dialog modal-sm">
                                <div class="modal-content">
                                    <div class="modal-header" style="border: 1px solid #f0f0f0;padding-bottom:10px">
                                        <span style="font-size:18px;font-weight: 400;">(Re)Assign Driver</span>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                            <div class="form-body">                        
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <select id="reassign_driver_id_{{ $item->id }}" name="driver_id" class="form-control">
                                                                @foreach($drivers as $data)
                                                                    <option value="{{ $data->id }}"
                                                                        @if($data->id == $item->driver_id)
                                                                        selected=""
                                                                        @endif
                                                                        >{{ $data->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer" style="padding: 0px">
                                                    <button type="button" class="btn btn-primary save_reassign_driver_btn" data-id={{ $item->id }} data-dismiss="modal">Save</button>            
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <td>
                            <span>
                                @foreach($vehicles as $d)
                                @if($d->id==$item->vehicle_id)
                                    {{ $d->vehicle_name }}
                                    @break
                                @endif
                                @endforeach
                            </span>
                        </td>
                        <td>
                            <span>
                                @if($item->has_multiple_trips==1)
                                    {{ "Yes" }}
                                @else
                                    {{ "No" }}
                                @endif
                            </span>
                        </td>
                        <td>
                            <span style="margin-right: 22px;">
                                {{$item->notes }}
                                <div class="list-icons float-right">
                                    <div class="dropdown">
                                        <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown"><i class="icon-menu"></i></a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a href="#" class="dropdown-item edit_oppLeg_btn" data-toggle="modal" data-target="#call" data-id="{{ $item->id }}"><i class="icon-pencil"></i> Edit</a>
                                                <a href="#" class="delete_oppLeg_btn dropdown-item" data-id="{{ $item->id }}" style="color: #ff0000"><i class="icon-trash"></i> Delete</a>        
                                            </div>
                                    </div>
                                </div> 
                            </span>
                        </td>     
                    </tr>
                    <tr id="oppLeg_line_div_edit_{{ $item->id }}" class="bgblu hidden" data-row="0">
                        <td>
                            {{ $item->leg_number }}
                        </td>
                        <td>
                            <span>
                                <div class="form-group">                                
                                    <input id="oppLeg_pickup_address_edit_{{ $item->id }}" class="form-control mt-1 geo-address" name="pickup_address" value="{{ $item->pickup_address }}" placeholder="Pickup Address"/>
                                </div>
                                <div class="form-group">                                
                                    <input id="oppLeg_drop_off_address_edit_{{ $item->id }}" class="form-control mt-1 geo-address" name="drop_off_address" value="{{ $item->drop_off_address }}" placeholder="Drop-off Address"/>
                                </div>
                            </span>
                            <hr/>
                            <div class="form-group">
                                <label>Status: </label>
                                <select id="oppLeg_leg_status_edit_{{ $item->id }}" name="leg_status" class="form-control">
                                    <option value=""></option>
                                    @foreach($leg_status as $data)
                                        <option value="{{ $data->list_option }}"
                                            @if($data->list_option == $item->leg_status)
                                            selected=""
                                            @endif
                                            >{{ $data->list_option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                        <td>
                            <span>
                                <div class="form-group">
                                    <div class="input-group">
                                        {{-- <span class="input-group-prepend"><span class="input-group-text"><i class="icon-calendar22"></i></span></span> --}}
                                        <input id="oppLeg_leg_date_edit_{{ $item->id }}" name="leg_date" type="text" class="form-control daterange-single" value="{{ date($global->date_format, strtotime($item->leg_date)) }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-12" style="padding: 0px!important;">
                                        <input id="oppLeg_leg_start_time_edit_{{ $item->id }}" type="time" class="form-control pickatime" value="{{ $item->est_start_time }}" placeholder="Start">
                                        </div>
                                        <div class="col-12" style="padding: 0px!important;">
                                        <input id="oppLeg_leg_finish_time_edit_{{ $item->id }}" type="time" class="form-control pickatime" value="{{ $item->est_finish_time }}" placeholder="Finish">
                                        </div>
                                    </div>
                            </div>
                            </span>
                        </td>
                        {{-- <td>
                            <span>
                                <div class="form-group">
                                    <select id="oppLeg_leg_status_edit_{{ $item->id }}" name="leg_status" class="form-control">
                                        @foreach($leg_status as $data)
                                            <option value="{{ $data->list_option }}"
                                                @if($data->list_option == $item->leg_status)
                                                selected=""
                                                @endif
                                                >{{ $data->list_option }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select id="oppLeg_job_type_edit_{{ $item->id }}" name="job_type" class="form-control">
                                        @foreach($job_type as $data)
                                            <option value="{{ $data->list_option }}"
                                                @if($data->list_option == $item->job_type)
                                                selected=""
                                                @endif
                                                >{{ $data->list_option }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </span>
                        </td> --}}
                        <td>
                            <span>
                                <div class="form-group">
                                    <select id="oppLeg_driver_id_edit_{{ $item->id }}" name="driver_id" class="form-control
                                        @if($item->leg_status!=NULL || !empty($item->leg_status))
                                        readonly_field 
                                        @endif">
                                        <option></option>
                                        @foreach($drivers as $data)
                                            <option value="{{ $data->id }}"
                                                @if($data->id == $item->driver_id)
                                                selected=""
                                                @endif
                                                >{{ $data->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                    <button type="button" data-toggle="modal" data-target="#offsiders_popup_{{ $item->id }}" class="btn btn-light leg_sm_btn" title="Select Offsiders">Offsiders</button>
                            </span>
                        </td>
                        <div id="offsiders_popup_{{ $item->id }}" class="modal fade" tabindex="-1">
                            <div class="modal-dialog modal-sm">
                                <div class="modal-content">
                                    <div class="modal-header" style="border: 1px solid #f0f0f0;padding-bottom:10px">
                                        <span style="font-size:18px;font-weight: 400;">Select Offsiders</span>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                            <div class="form-body">                        
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <?php
                                                                $offsider_ids2 = array();
                                                                if(isset($item->offsider_ids)){
                                                                    $offsider_ids2 = @explode(',', $item->offsider_ids);
                                                                }
                                                            ?>
                                                            <select id="oppLeg_offsider_ids_edit_{{ $item->id }}" multiple="multiple" class="form-control form-control-sm select" name="offsider_ids">
                                                                @foreach($people as $data)
                                                                    <option value="{{ $data->id }}"
                                                                        @if(in_array($data->id, $offsider_ids2)) selected @endif
                                                                        >{{ $data->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer" style="padding: 0px">
                                                <button type="button" class="btn btn-link offsiders_popup_edit_clear" style="margin-top: -10px;" data-id="{{ $item->id }}"> Clear All</button>
                                                    <button type="button" class="btn btn-primary" data-dismiss="modal">Save</button>            
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <td>
                            <span>
                                <div class="form-group">
                                    <select id="oppLeg_vehicle_id_edit_{{ $item->id }}" name="vehicle_id" class="form-control">
                                        <option></option>
                                        @foreach($vehicles as $data)
                                            <option value="{{ $data->id }}"
                                                @if($data->id == $item->vehicle_id)
                                                selected=""
                                                @endif
                                                >{{ $data->vehicle_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </span>
                        </td>
                        <td>
                            <span>
                                <div class="form-group">
                                    <div class="">
                                        <input id="oppLeg_has_multiple_trips_edit_{{ $item->id }}" name="has_multiple_trips" {{ $item->has_multiple_trips=='1'?'checked=""':'' }} type="checkbox">
                                    </div>
                                </div>
                            </span>
                        </td>
                        <td>
                            <div class="form-group">
                                <textarea id="oppLeg_notes_edit_{{ $item->id }}" name="notes" class="form-control">{{ $item->notes }}</textarea>
                            </div>
                            <div class="d-flex justify-content-start align-items-center m-t-10">
                                <button type="button" class="btn btn-light cancel_update_oppLeg_btn" data-id="{{ $item->id }}"> Cancel</button>
                                <button type="button" class="btn btn-success ml-2 update_oppLeg_btn" data-id="{{ $item->id }}"> Update</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7">No record available !</td>
                </tr>
            @endif 


            <tr id="oppLeg_line_div_new" class="bgblu oppLeg_line_div hidden" data-row="0">
                <td>
                    
                </td>
                <td>
                    <span>
                        <div class="form-group">                                
                            <input class="form-control oppLeg_pickup_address_new geo-address" name="pickup_address" placeholder="Pickup Address" value="{{ $job->pickup_address." ".$job->pickup_suburb }}"/>
                        </div>
                        <div class="form-group">                                
                            <input class="form-control oppLeg_drop_off_address_new geo-address" name="drop_off_address" placeholder="Drop-off Address" value="{{ $job->drop_off_address." ".$job->delivery_suburb }}"/>
                        </div>
                    </span>
                    <hr/>
                    <div class="form-group">
                        <label>Status: </label>
                        <select name="leg_status" class="form-control oppLeg_leg_status_new">
                            <option value=""></option>
                            @foreach($leg_status as $data)
                                <option value="{{ $data->list_option }}">
                                    {{ $data->list_option }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </td>
                <td>
                    <span>
                        <div class="form-group">
                            <div class="input-group">
                                {{-- <span class="input-group-prepend"><span class="input-group-text"><i class="icon-calendar22"></i></span></span> --}}
                                <input name="leg_date" type="text" class="form-control oppLeg_leg_date_new daterange-single" value="{{ date('d/m/Y') }}">
                            </div>
                            <br/>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-12" style="padding: 0px!important;">
                                    <input type="time" class="form-control oppLeg_leg_start_time_new pickatime" placeholder="Start">
                                    </div>
                                    <div class="col-12" style="padding: 0px!important;">
                                    <input type="time" class="form-control oppLeg_leg_finish_time_new pickatime" placeholder="Finish">
                                    </div>
                                </div>
                        </div>
                    </span>
                </td>
                {{-- <td>
                    <span>
                        <div class="form-group">
                            <select name="leg_status" class="form-control oppLeg_leg_status_new">
                                @foreach($leg_status as $data)
                                    <option value="{{ $data->list_option }}">
                                        {{ $data->list_option }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="job_type" class="form-control oppLeg_job_type_new">
                                @foreach($job_type as $data)
                                    <option value="{{ $data->list_option }}">
                                        {{ $data->list_option }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </span>
                </td> --}}
                <td>
                    <span>
                        <div class="form-group">
                            <select name="driver_id" class="form-control oppLeg_driver_id_new">
                                <option></option>
                                @foreach($drivers as $data)
                                    <option value="{{ $data->id }}">
                                        {{ $data->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" data-toggle="modal" data-target="#offsiders_popup" class="btn btn-light" style="width: 100%;" title="Select Offsiders">Offsiders</button>
                                                                          
                    </span>
                </td>
                <div id="offsiders_popup" class="modal fade" tabindex="-1">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                            <div class="modal-header" style="border: 1px solid #f0f0f0;padding-bottom:10px">
                                <span style="font-size:18px;font-weight: 400;">Select Offsiders</span>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                    <div class="form-body">                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <select multiple="multiple" class="form-control form-control-sm select oppLeg_offsider_ids_new" name="offsider_ids">
                                                        @foreach($people as $data)
                                                            <option value="{{ $data->id }}">
                                                                {{ $data->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer" style="padding: 0px">
                                            <button type="button" class="btn btn-link oppLeg_offsider_ids_new_clear" style="margin-top: -10px;"> Clear All</button>
                                            <button type="button" class="btn btn-primary" data-dismiss="modal">Save</button>            
                                    </div>
                            </div>
                        </div>
                    </div>
                </div> 
                <td>
                    <span>
                        <div class="form-group">
                            <select name="vehicle_id" class="form-control oppLeg_vehicle_id_new">
                                <option></option>
                                @foreach($vehicles as $data)
                                    <option value="{{ $data->id }}">
                                        {{ $data->vehicle_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </span>
                </td>
                <td>
                    <span>
                        <div class="form-group">
                            <div class="">
                                <input name="has_multiple_trips" class="oppLeg_has_multiple_trips_new" type="checkbox">
                            </div>
                        </div>
                    </span>
                </td>
                <td>
                    <div class="form-group">
                        <textarea name="notes" class="form-control oppLeg_notes_new"></textarea>
                    </div>
                    <div class="d-flex justify-content-start align-items-center m-t-10">
                        <button type="button" class="btn btn-light cancel_oppLeg_btn"> Cancel</button>
                        <button type="button" class="btn btn-success ml-2 save_oppLeg_btn"> Save</button>
                    </div>
                </td>
            </tr>

    </tbody>                
    </table>

<div class="float-left">
    <button id="add_oppLeg_line" type="button" class="btn plus_btn"><i class="icon-plus3"></i></button>
</div>
</article>  
<script>
    $('.oppLeg_offsider_ids_new_clear').on('click', function () { 
        $('.oppLeg_offsider_ids_new').val(null).trigger('change'); 
    });
    $('.offsiders_popup_edit_clear').on('click', function () { 
        var row_id = $(this).data('id');
        $('#oppLeg_offsider_ids_edit_'+row_id).val(null).trigger('change'); 
    });
    
</script>