@php
    $job_legs_team_offsiders = App\JobsMovingLegsTeam::where(['tenant_id' => auth()->user()->tenant_id, 'leg_id' => $leg_id, 'driver' => 'N'])->get();
@endphp
<p id="header"><b>Offsiders</b></p>
<table id="opperation_offsider_{{ $leg_id }}" class="offsiders-table">
    <thead>
        <td style="width: 25%"><span>Name</span></td>
        <td style="width: 20%"><span>System User?</span></td>
        <td style="width: 30%"><span>Status</span></td>
        <td style="width: 15%"><span>Notify</span></td>
        <td style="width: 10%"><span>Action</span></td>
    </thead>
    @if (count($job_legs_team_offsiders))
        @foreach ($job_legs_team_offsiders as $offsider)
        @php
            $is_system_user = $offsider->isSystemUser();
        @endphp
        <tr id="oppLeg_offsiders_line_div_view_{{ $leg_id }}_{{ $offsider->id }}" class="oppLeg_offsiders_line_div">
            <td>
                @if (count($people))
                    @foreach ($people as $data)
                        @if ($data->id == $offsider->people_id)
                            {{ $data->name }}
                        @endif
                    @endforeach
                @endif
            </td>
            <td><?=$is_system_user?></td>
            <td>
                @if($offsider->confirmation_status == "Awaiting Confirmation")
                    <span class="text-red" style="margin: 0px">{{ $offsider->confirmation_status }}</span>
                @else
                    <span class="text-green">{{ $offsider->confirmation_status }}</span>
                @endif
                {{-- {{ $offsider->confirmation_status }} --}}
            </td>
            <td>
                @if($is_system_user=='Y')
                @php
                    if($offsider->confirmation_status == null || empty($offsider->confirmation_status) || $offsider->confirmation_status == 'New')
                    {
                            $notify_btn_status = "";
                        }else{
                            $notify_btn_status = "readonly_field";
                    }
                @endphp
                <button type="button" class="btn btn-light notify_offsider_btn {{ $notify_btn_status }}" data-legid="{{ $leg_id }}" data-offsiderid="{{ $offsider->id }}" data-peopleid="{{ $offsider->people_id }}">Notify</button>
                @endif
            </td>
            <td>
                <div class="list-icons float-right">
                    <div class="dropdown">
                        <a href="#" class="list-icons-item dropdown-toggle caret-0 ml-2" data-toggle="dropdown"><i class="icon-menu"></i></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="#" class="dropdown-item edit_offsiders_legs_btn" data-legid="{{ $leg_id }}" data-offsiderid="{{ $offsider->id }}"><i class="icon-pencil"></i> Edit</a>
                                <a href="#" class="delete_offsiders_legs_btn dropdown-item" data-legid="{{ $leg_id }}" data-offsiderid="{{ $offsider->id }}" style="color: #ff0000"><i class="icon-trash"></i> Delete</a>        
                            </div>
                    </div>
                </div>
            </td>
        </tr>
        <tr id="oppLeg_offsiders_line_div_edit_{{ $leg_id }}_{{ $offsider->id }}" class="oppLeg_offsiders_line_div hidden">
            <td colspan="2">
                <select id="oppLeg_offsiders_name_line_edit_{{ $leg_id }}_{{ $offsider->id }}" name="oppLeg_offsiders_name_line_new" class="form-control">
                    <option value=""></option>
                    @foreach($people as $data)
                        <option value="{{ $data->id }}" @if ($data->id == $offsider->people_id)
                            selected
                        @endif>{{ $data->name }}</option>
                    @endforeach
                </select>
            </td>
            <td colspan="3">
                <div class="row">
                    <div class="col-md-12 p-2 text-right">
                        <button type="button" class="btn btn-light cancel_edit_oppLeg_offsiders_div_btn" data-legid="{{ $leg_id }}" data-offsiderid="{{ $offsider->id }}">Cancel</button>
                        <button type="button" class="btn btn-success update_oppLeg_offsiders_div_btn" data-legid="{{ $leg_id }}" data-offsiderid="{{ $offsider->id }}">Update</button>
                    </div>
                </div>
            </td>
        </tr>  
        @endforeach
    @else
    {{-- <tr><td colspan="5">No record available!</td></tr> --}}
    @endif
    <tr id="oppLeg_offsiders_line_div_new_{{ $leg_id }}" class="bgblu oppLeg_offsiders_line_div hidden" data-row="0">
        <td colspan="2">
            <select id="oppLeg_offsiders_name_line_new_{{ $leg_id }}" name="oppLeg_offsiders_name_line_new" class="form-control">
                <option value=""></option>
                @foreach($people as $data)
                    <option value="{{ $data->id }}">{{ $data->name }}</option>
                @endforeach
            </select>
        </td>
        <td colspan="3">
            <div class="row">
                <div class="col-md-12 p-2 text-right">
                    <button type="button" class="btn btn-light cancel_oppLeg_offsiders_div_btn" data-legid="{{ $leg_id }}">Cancel</button>
                    <button type="button" class="btn btn-success store_oppLeg_offsiders_div_btn" data-legid="{{ $leg_id }}">Add</button>
                </div>
            </div>
        </td>
    </tr>   
</table>
<div class="float-left mb-4">
    <button type="button" class="btn offsiders_plus_btn add_oppLeg_offsiders_line" style="padding: 0.5em 0.7em;" data-legid="{{ $leg_id }}"><i class="icon-plus3"></i></button>
</div>