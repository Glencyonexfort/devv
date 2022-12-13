<table id="oppLeg_line_div_edit_offsiders_{{ $leg_id }}" class="offsiders-table-inside">
    <thead>
        <td style="width: 25%"><span>Name</span></td>
        <td style="width: 20%"><span>System User?</span></td>
        <td style="width: 30%"><span>Confirmation Status</span></td>
        <td style="width: 15%"><span>Notify</span></td>
        <td style="width: 10%"><span>Action</span></td>
    </thead>
    @if (count($all_leg_offsiders))
        @foreach ($all_leg_offsiders as $offsider)
            <tr id="inside_oppLeg_offsiders_line_div_view_{{ $leg_id }}_{{ $offsider->id }}" class="oppLeg_offsiders_line_div">
                <td>
                    @if (count($people))
                        @foreach ($people as $data)
                            @if ($data->id == $offsider->people_id)
                                {{ $data->name }}
                            @endif
                        @endforeach
                    @endif
                </td>
                <td>Y</td>
                <td>Confirmed</td>
                <td>
                    <button type="button" class="btn btn-light">Notify</button>
                </td>
                <td>
                    <div class="list-icons float-right">
                        <div class="dropdown">
                            <a href="#" class="list-icons-item dropdown-toggle caret-0 ml-2" data-toggle="dropdown"><i class="icon-menu"></i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="#" class="dropdown-item inside_edit_offsiders_legs_btn" data-legid="{{ $leg_id }}" data-offsiderid="{{ $offsider->id }}"><i class="icon-pencil"></i> Edit</a>
                                    <a href="#" class="inside_delete_offsiders_legs_btn dropdown-item" data-legid="{{ $leg_id }}" data-offsiderid="{{ $offsider->id }}" style="color: #ff0000"><i class="icon-trash"></i> Delete</a>        
                                </div>
                        </div>
                    </div>
                </td>
            </tr>
            <tr id="inside_oppLeg_offsiders_line_div_edit_{{ $leg_id }}_{{ $offsider->id }}" class="oppLeg_offsiders_line_div hidden">
                <td colspan="2">
                    <select id="inside_oppLeg_offsiders_name_line_edit_{{ $leg_id }}_{{ $offsider->id }}" name="oppLeg_offsiders_name_line_new" class="form-control">
                        <option value=""></option>
                        @foreach($people as $data)
                            <option value="{{ $data->id }}" @if ($data->id == $offsider->people_id)
                                selected
                            @endif>
                                {{ $data->name }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td colspan="3">
                    <div class="row">
                        <div class="col-md-12 p-2 text-right">
                            <button type="button" class="btn btn-light inside_cancel_edit_oppLeg_offsiders_div_btn" data-legid="{{ $leg_id }}" data-offsiderid="{{ $offsider->id }}">Cancel</button>
                            <button type="button" class="btn btn-success inside_update_oppLeg_offsiders_div_btn" data-legid="{{ $leg_id }}" data-offsiderid="{{ $offsider->id }}">Update</button>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
    @endif
    <tr id="inside_oppLeg_offsiders_line_div_new_{{ $leg_id }}" class="bgblu oppLeg_offsiders_line_div hidden" data-row="0">
        <td colspan="2">
            <select id="inside_oppLeg_offsiders_name_line_new_{{ $leg_id }}" name="oppLeg_offsiders_name_line_new" class="form-control">
                <option value=""></option>
                @foreach($people as $data)
                    <option value="{{ $data->id }}">{{ $data->name }}</option>
                @endforeach
            </select>
        </td>
        <td colspan="3">
            <div class="row">
                <div class="col-md-12 p-2 text-right">
                    <button type="button" class="btn btn-light inside_cancel_oppLeg_offsiders_div_btn" data-legid="{{ $leg_id }}">Cancel</button>
                    <button type="button" class="btn btn-success inside_store_oppLeg_offsiders_div_btn" data-legid="{{ $leg_id }}">Add</button>
                </div>
            </div>
        </td>
    </tr>   
</table>
<div class="float-left mb-4">
    <button type="button" class="btn offsiders_plus_btn inside_add_oppLeg_offsiders_line" style="padding: 0.5em 0.7em;" data-legid="{{ $leg_id }}"><i class="icon-plus3"></i></button>
</div>