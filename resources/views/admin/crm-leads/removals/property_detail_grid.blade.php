<div class="card view_blade_4_card">
    <span class="view_blade_4_card_span">
        <div class="card-header header-elements-inline view_blade_4_card_header">
            <h6 class="card-title card-title-mg view_blade_4_card_task">Property Details</h6>
        </div>
    </span>
    {{-- VIEW --}}
    <div id="update_property_detail_view">
        <div style="border-left:3px solid #89dd88;min-height: 20rem;" class="card-body job_left_panel_body1">
            @if($removal_jobs_moving->opportunity == 'Y')
                <div class="d-flex justify-content-start align-items-center float-right">
                    <button class="show_update_property_detail_btn btn btn-icon"><i class="icon-pencil"></i></button>
                </div>
            @endif
            <div class="job-label-txt">
                <table class="left_panel_table" style="width: auto!important;">
                    <tbody>
                    <tr>
                        <td>
                            Property Type:
                        </td>
                        <td class="textalign-left">
                            <span>{{ $removal_jobs_moving->pickup_property_type }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Furnishing:
                        </td>
                        <td class="textalign-left">
                            <span>{{ $removal_jobs_moving->pickup_furnishing }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Bedrooms:
                        </td>
                        <td class="textalign-left">
                            <span>{{ $removal_jobs_moving->pickup_bedrooms }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Living Areas:
                        </td>
                        <td class="textalign-left">
                            <span>{{ $removal_jobs_moving->pickup_living_areas }}</span>                                   
                        </td>
                    </tr>
                </tbody>
                </table>
                <hr style="margin:5px 0"/>
                @php
                    $other_room_ary = @explode(',', $removal_jobs_moving->pickup_other_rooms);
                    $special_item_ary = @explode(',', $removal_jobs_moving->pickup_speciality_items);
                @endphp
                <table class="left_panel_table" style="width: auto!important;">
                    <tbody>
                        <tr>
                            <td>
                                Other Rooms:
                            </td>
                        </tr>
                        <tr>
                            <td class="textalign-left">
                                <ul class="checkbox-grid">
                                    @foreach($other_room as $optn)
                                            <li class="w400 txt12 m-r-5" style="display: inline-block;">                                                
                                                @if(in_array($optn->options, $other_room_ary))
                                                    <i class="icon-checkbox-checked2 txt12 m-r-5"></i>
                                                @else
                                                    <i class="icon-checkbox-unchecked2 txt12 m-r-5"></i>
                                                @endif
                                                {{$optn->options}}
                                            </li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Speciality Items:
                            </td>
                        </tr>
                        <tr>
                            <td class="textalign-left">
                                <ul class="checkbox-grid">
                                    @foreach($special_item as $optn)
                                    <li class="w400 txt12" style="display: inline-block;">                                         
                                        @if(in_array($optn->options, $special_item_ary))
                                            <i class="icon-checkbox-checked2 txt12 m-r-5"></i>
                                        @else
                                            <i class="icon-checkbox-unchecked2 txt12 m-r-5"></i>
                                        @endif
                                        {{$optn->options}}
                                    </li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Instructions:
                            </td>
                        </tr>
                        <tr>
                            <td class="textalign-left">
                                <span class="txt12">
                                    {{ $removal_jobs_moving->other_instructions }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    {{-- FORM --}}
    <div id="update_property_detail_form" class="card-body p10 hidden body_margin">
        <form id="property_detail_form" class="custom-form" action="#">
            @csrf
            {{ Form::hidden('lead_id', $removal_opportunities->lead_id) }}
            {{ Form::hidden('opp_id', $removal_opportunities->id) }}
            <div class="form-group">
                <label>Property Type</label>
                <select name="pickup_property_type" class="form-control">
                    @foreach($property_types as $data)
                    <option value="{{ $data->options }}" @if($data->options == $removal_jobs_moving->pickup_property_type)
                        selected=""
                        @endif
                        >{{ $data->options }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Furnishing</label>
                <select name="pickup_furnishing" class="form-control">
                    @foreach($furnishing as $data)
                    <option value="{{ $data->options }}" @if($data->options == $removal_jobs_moving->pickup_furnishing)
                        selected=""
                        @endif
                        >{{ $data->options }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Bedrooms</label>
                <select name="pickup_bedrooms" class="form-control">
                    @foreach($bedroom as $data)
                    <option value="{{ $data->options }}" @if($data->options == $removal_jobs_moving->pickup_bedrooms)
                        selected=""
                        @endif
                        >{{ $data->options }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Living Areas</label>
                <select name="pickup_living_areas" class="form-control">
                    @foreach($living_room as $data)
                    <option value="{{ $data->options }}" @if($data->options == $removal_jobs_moving->pickup_living_areas)
                        selected=""
                        @endif
                        >{{ $data->options }}</option>
                    @endforeach
                </select> 
            </div>
            <hr style="margin:5px 0"/>
            @php
                    $other_room_ary = @explode(',', $removal_jobs_moving->pickup_other_rooms);
                    $special_item_ary = @explode(',', $removal_jobs_moving->pickup_speciality_items);
                @endphp
            <div class="form-group">
                <label>Other Rooms</label><br/>
                @foreach($other_room as $optn)
                <span class="form-check form-check-inline form-check-right">
                    <label class="form-check-label w400 txt12">
                        {{$optn->options}} 
                        <input class="form-check-input" type="checkbox" name="other_room[]" {{ in_array($optn->options, $other_room_ary)?'checked=""':''}} value="{{$optn->options}}"> 
                    </label>
                </span>
                @endforeach
            </div>
            <div class="form-group">
                <label>Speciality Items</label><br/>
                @foreach($special_item as $optn)
                <span class="form-check form-check-inline form-check-right">
                    <label class="form-check-label w400 txt12">
                        {{$optn->options}} 
                        <input class="form-check-input" type="checkbox" name="special_item[]" {{ in_array($optn->options, $special_item_ary)?'checked=""':''}} value="{{$optn->options}}">
                    </label>
                </span>
                @endforeach                
            </div>
            <div class="form-group">
                <label>Instructions</label>
                <textarea name="other_instructions" class="form-control">{{ $removal_jobs_moving->other_instructions }}</textarea>
            </div>
            <div class="d-flex justify-content-start align-items-center m-t-10">
                <button type="reset" class="btn btn-light show_update_property_detail_btn">Cancel</button>
                <button type="button" id="update_property_detail_btn" class="btn bg-blue ml-3">Update</button>
            </div>

        </form>
    </div>
</div>
