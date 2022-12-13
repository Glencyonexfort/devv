<style>
    .offsiders_leg {
        border: 1px solid lightgray;
    }
    /* #opperationlegs_row:nth-of-type(odd) {
        background: #efefef;
    } */
    .offsiders {
        border: 1px solid lightgray;
    }
    #header {
        margin: 10px;
    }
    .heading {
        border-right: 1px solid lightgray;
    }
    .section1 {
        display: flex;
    }
    .date {
        margin-right: 20px;
    }
    .buttons button {
        margin: 10px;
    }
    .buttons span {
        float: left;
        margin: 10px
    }
    .border-none {
        border-collapse: collapse;
        border: none;
    }
    .border-none tr td:first-child {
        border-left: none;
        border-right: none;
        border-bottom: none;
        border-top: none;
    }

    .border-none tr td:last-child {
        border-right: none;
        border-left: none;
        border-bottom: none;
        border-top: none;
    }
    .leg_menu {
        color: #333;
        margin: 20px;
        float: right;
    }
    .offsiders-table td{
        padding: 0.8em 0.3em;
        color: #333;
    }
    .offsiders_plus_btn {
        background-color: #dceffc;
        border-radius: 0px;
    }
    #notes-dispatch {
        margin: 5px;
    }
    #leg-table2 tbody{
        color: #333;
    }
    #leg-table2 td{
        padding: 0px 0.8em;
    }
    .offsiders-table-inside {
        margin: 15px;
    }
    .inside_add_oppLeg_offsiders_line {
        margin-left: 16px;
        margin-top: -12px;
    }
</style>
@if(count($job_legs))
    @foreach($job_legs as $item)
    <div id="opperationlegs_row">
        <article id="oppLeg_line_div_view_{{ $item->id }}" class="offsiders_leg oppLeg_line_div">
            <div class="row offsiders m-0">
                <div class="col-md-6 heading">
                    <div class="row">
                        <div class="col-1 p-2">
                            <p><b>#{{ $item->leg_number }}</b></p>
                        </div>
                        <div class="col-11 p-2">
                            <p class="mb-0"><b class="date">{{ date($global->date_format,strtotime($item->leg_date)) }}</b><span><b>{{ date('H:i',strtotime($item->est_start_time)) }} - {{ date('H:i',strtotime($item->est_finish_time)) }}</b></span></p>
                            <p class="mb-0">{{ $item->pickup_address }}</p>
                            <p>{{ $item->drop_off_address }}</p>
        
                            <div class="dispatch">
                                <p class="mb-0"><b>Dispatch Notes:</b></p>
                                <p>{{ $item->notes }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="border-none m-2" id="leg-table2" >
                                <tr>
                                    <td><b>Driver:</b></td>
                                    <td>
                                        @if ($item->driver_id && count($drivers))
                                            @foreach($drivers as $d)
                                                @if($d->id == $item->driver_id)
                                                    {{ $d->name }}
                                                    @break
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Vehicle:</b></td>
                                    <td>
                                        @if ($item->vehicle_id && count($vehicles))
                                            @foreach($vehicles as $d)
                                                @if($d->id == $item->vehicle_id)
                                                    {{ $d->vehicle_name }}
                                                    @break
                                                @endif
                                            @endforeach
                                        @endif        
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Multiple Trips:</b></td>
                                    <td>
                                        @if($item->has_multiple_trips==1)
                                            {{ "Yes" }}
                                        @else
                                            {{ "No" }}
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="list-icons float-right">
                                <div class="dropdown">
                                    <a href="#" class="list-icons-item dropdown-toggle caret-0 ml-2 leg_menu" data-toggle="dropdown"><i class="icon-menu"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="javascript:void(0)" class="dropdown-item edit_leg_btn" data-id="{{ $item->id }}"><i class="icon-pencil"></i> Edit</a>
                                            <a href="javascript:void(0)" class="delete_oppLeg_btn dropdown-item" data-id="{{ $item->id }}" style="color: #ff0000"><i class="icon-trash"></i>Delete</a>        
                                        </div>
                                </div>
                            </div>
                            {{-- <a href="#" ><i class="icon-leg_menu"></i></a> --}}
                        </div>
                    </div>
                    <?php
                        if($item->leg_status==NULL || empty($item->leg_status) || $item->leg_status == 'New'){
                            $notify_btn_status = "";
                        }else{
                            $notify_btn_status = "readonly_field";
                        }

                        if($item->leg_status==NULL || $item->leg_status=="Awaiting Confirmation" || $item->leg_status=="Confirmed" || $item->leg_status == 'New'){
                            $reassign_btn_status = "";
                        }else{
                            $reassign_btn_status = "readonly_field";
                        }
                    ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="buttons ml-2">
                                <button type="button" class="btn btn-light leg_sm_btn notify_driver_btn {{ $notify_btn_status }}" title="Notify Driver" data-id="{{ $item->id }}">Notify Driver</button>
                                @if($item->leg_status=="Awaiting Confirmation")
                                    <span class="leg_status_txt text-red" style="margin: 0px">{{ $item->leg_status }}</span>
                                @elseif($notify_btn_status!="")
                                    <span class="leg_status_txt text-green" style="margin: 0px">{{ $item->leg_status }}</span>
                                @endif 
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="buttons">
                                <button type="button" class="btn btn-light leg_sm_btn {{ $reassign_btn_status }}" data-toggle="modal" data-target="#reassign_driver_popup_{{ $item->id }}" title="(Re)Assign Driver" data-id="{{ $item->id }}">(Re)Assign Driver</button>
                                {{-- <br/><span class="text-green">Confirmed</span> --}}
                            </div>
                        </div>
                        {{--START: ReAssign Modal For Driver --}}
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
                        {{-- END: ReAssign Modal For Driver --}}
                    </div>
                </div>
            </div>
            <div class="row">
                <div id="operations_leg_offsiders_grid_{{ $item->id }}" class="col-6">
                    @php
                        $leg_id = $item->id;
                    @endphp
                    @include('admin.list-jobs.jobs.operations_leg_offsiders_grid')
                </div>
            </div>
        </article>
        <article id="oppLeg_line_div_edit_{{ $item->id }}" class="bgblu oppLeg_line_div hidden">
            <div class="row offsiders m-0">
                <div class="col-md-6 heading">
                    <div class="row">
                        <div class="col-md-2 p-2">
                            <p><b>#{{ $item->leg_number }}</b></p>
                        </div>
                        <div class="col-md-10 p-2">
                            <div class="row p-1">
                                <div class="col-md-4">
                                    <input id="oppLeg_leg_date_edit_{{ $item->id }}" name="leg_date" type="text" class="form-control daterange-single" value="{{ date($global->date_format, strtotime($item->leg_date)) }}">
                                </div>
                                <div class="col-md-4">
                                    <input id="oppLeg_leg_start_time_edit_{{ $item->id }}" type="time" class="form-control pickatime" value="{{ $item->est_start_time }}" placeholder="Start">
                                </div>
                                <div class="col-md-4">
                                    <input id="oppLeg_leg_finish_time_edit_{{ $item->id }}" type="time" class="form-control pickatime" value="{{ $item->est_finish_time }}" placeholder="Finish">
                                </div>
                            </div>
                            <div class="row p-1">
                                <div class="col-md-8">
                                    <input id="oppLeg_pickup_address_edit_{{ $item->id }}" class="form-control mt-1 geo-address" name="pickup_address" value="{{ $item->pickup_address }}" placeholder="Pickup Address">
                                </div>
                            </div>
                            <div class="row p-1">
                                <div class="col-md-8">
                                    <input id="oppLeg_drop_off_address_edit_{{ $item->id }}" class="form-control mt-1 geo-address" name="drop_off_address" value="{{ $item->drop_off_address }}" placeholder="Drop-off Address">
                                </div>
                            </div>
        
                            <p class="m-1"><b>Dispatch Notes</b></p>
                            <div class="row">
                                <div class="col-md-12">
                                    <textarea id="oppLeg_notes_edit_{{ $item->id }}" name="notes" class="form-control">{{ $item->notes }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row p-2">
                                <div class="col-md-2 p-2">
                                    <label for="status"><b>Status</b></label>
                                </div>
                                <div class="col-md-10">
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
                            </div>
                            <div class="row p-1">
                                <div class="col-md-2 p-2">
                                    <label for="status"><b>Driver</b></label>
                                </div>
                                <div class="col-md-10">
                                    <select id="oppLeg_driver_id_edit_{{ $item->id }}" name="driver_id" class="form-control
                                        @if($item->leg_status!=null || !empty($item->leg_status))
                                        readonly_field 
                                        @endif
                                        "
                                        >
                                        <option></option>
                                        @if(count($drivers))
                                            @foreach($drivers as $data)
                                                <option value="{{ $data->id }}"
                                                    @if($data->id == $item->driver_id)
                                                    selected=""
                                                    @endif
                                                    >
                                                    {{ $data->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="row p-1">
                                <div class="col-md-2 p-2">
                                    <label for="status"><b>Vehicle</b></label>
                                </div>
                                <div class="col-md-10">
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
                            </div>
                            <div class="row p-1">
                                <div class="col-md-4 p-2">
                                    <label for="status"><b>Multiple Trips?</b></label>
                                </div>
                                <div class="col-md-8 p-2">
                                    <input id="oppLeg_has_multiple_trips_edit_{{ $item->id }}" name="has_multiple_trips" {{ $item->has_multiple_trips=='1'?'checked=""':'' }} type="checkbox">
                                </div>
                            </div>
                            <div class="row p-2">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-light cancel_update_oppLeg_btn" data-id="{{ $item->id }}">Cancel</button>
                                    <button type="button" class="btn btn-success update_oppLeg_btn" data-id="{{ $item->id }}">Update</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </article>
        
    </div>
    @endforeach
    @else
    <article>
        <table>
            <tr>
                <td>No leg record found!</td>
            </tr>
        </table>
    </article>
@endif
<article id="oppLeg_line_div_new" class="bgblu oppLeg_line_div hidden" data-row="0">
    <div class="row offsiders m-0">
        <div class="col-md-6 heading">
            <div class="row">
                <div class="col-md-12 p-2">
                    <div class="row p-1">
                        <div class="col-md-4">
                            <input name="leg_date" type="text" class="form-control oppLeg_leg_date_new daterange-single" value="{{ date('d/m/Y') }}">
                        </div>
                        <div class="col-md-4">
                            <input type="time" class="form-control oppLeg_leg_start_time_new pickatime" placeholder="Start">
                        </div>
                        <div class="col-md-4">
                            <input type="time" class="form-control oppLeg_leg_finish_time_new pickatime" placeholder="Finish">
                        </div>
                    </div>
                    <div class="row p-1">
                        <div class="col-md-8">
                            <input class="form-control oppLeg_pickup_address_new geo-address" name="pickup_address" placeholder="Pickup Address" value="{{ $job->pickup_address." ".$job->pickup_suburb }}">
                        </div>
                    </div>
                    <div class="row p-1">
                        <div class="col-md-8">
                            <input class="form-control oppLeg_drop_off_address_new geo-address" name="drop_off_address" placeholder="Drop-off Address" value="{{ $job->drop_off_address." ".$job->delivery_suburb }}">
                        </div>
                    </div>

                    <p class="m-1"><b>Dispatch Notes</b></p>
                    <div class="row">
                        <div class="col-md-12">
                            <textarea name="notes" class="form-control oppLeg_notes_new"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12">
                    <div class="row p-2">
                        <div class="col-md-2 p-2">
                            <label for="status"><b>Status</b></label>
                        </div>
                        <div class="col-md-10">
                            <select name="leg_status" class="form-control oppLeg_leg_status_new">
                                <option value=""></option>
                                @foreach($leg_status as $data)
                                    <option value="{{ $data->list_option }}">
                                        {{ $data->list_option }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row p-1">
                        <div class="col-md-2 p-2">
                            <label for="status"><b>Driver</b></label>
                        </div>
                        <div class="col-md-10">
                            <select name="driver_id" class="form-control oppLeg_driver_id_new">
                                <option></option>
                                @foreach($drivers as $data)
                                    <option value="{{ $data->id }}">
                                        {{ $data->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row p-1">
                        <div class="col-md-2 p-2">
                            <label for="status"><b>Vehicle</b></label>
                        </div>
                        <div class="col-md-10">
                            <select name="vehicle_id" class="form-control oppLeg_vehicle_id_new">
                                <option></option>
                                @foreach($vehicles as $data)
                                    <option value="{{ $data->id }}">
                                        {{ $data->vehicle_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row p-1">
                        <div class="col-md-4 p-2">
                            <label for="status"><b>Multiple Trips?</b></label>
                        </div>
                        <div class="col-md-8 p-2">
                            <input name="has_multiple_trips" class="oppLeg_has_multiple_trips_new" type="checkbox">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 p-2">
                            <button type="button" class="btn btn-light cancel_oppLeg_offsiders_btn">Cancel</button>
                            <button type="button" class="btn btn-success save_oppLeg_btn" >Add</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article> 
<div class="float-left">
    <button id="add_oppLeg_line" type="button" class="btn plus_btn"><i class="icon-plus3"></i>  Add New Leg</button>
</div>
<script>
    $('.oppLeg_offsider_ids_new_clear').on('click', function () { 
        $('.oppLeg_offsider_ids_new').val(null).trigger('change'); 
    });
    $('.offsiders_popup_edit_clear').on('click', function () { 
        var row_id = $(this).data('id');
        $('#oppLeg_offsider_ids_edit_'+row_id).val(null).trigger('change'); 
    });
    
</script>