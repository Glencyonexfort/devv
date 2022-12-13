@section('removal_booking_detail_grid')
@include('admin.crm-leads.removals.booking_detail_grid')
@endsection

@section('removal_property_detail_grid')
@include('admin.crm-leads.removals.property_detail_grid')
@endsection

@section('moving_from_grid')
@include('admin.crm-leads.removals.moving_from_grid')
@endsection

@section('moving_to_grid')
@include('admin.crm-leads.removals.moving_to_grid')
@endsection

<input id="op_job_id" value="{{ $removal_jobs_moving->job_id }}" type="hidden"/>
<div class="card card-removaltab">
    <div class="card-body padding-0">
    <div class="row row-margin-tp">
            <div class="col-lg-6 pr-lg-0">
                <div class="card view_blade_4_card">
                    <div id="moving_from_grid">
                        @yield('moving_from_grid')
                    </div>
                {{-- FORM --}}
                    <div id="update_movingfrom_form" class="card-body p10 hidden body_margin">
                        <form id="movingfrom_form" class="custom-form" action="#">
                            @csrf
                            {{ Form::hidden('lead_id', $removal_opportunities->lead_id) }}
                            {{ Form::hidden('job_id', $removal_jobs_moving->job_id) }}
                            {{ Form::hidden('opp_id', $removal_opportunities->id,array("id"=>"removal_opp_id_hidden_field")) }}
                            @if ($removal_opportunities->lead_type == 'Commercial')
                                <div class="form-group">
                                    <label>Contact Name</label>
                                    <input type="text" name="pickup_contact_name" class="form-control" value="{{ $removal_jobs_moving_data->pickup_contact_name }}" />
                                </div>
                                <div class="form-group">
                                    <label>Mobile</label>
                                    <input type="text" name="pickup_mobile" class="form-control" value="{{ $removal_jobs_moving_data->pickup_mobile }}"/>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="text" name="pickup_email" class="form-control" value="{{ $removal_jobs_moving_data->pickup_email }}"/>
                                </div>
                            @endif
                            <div class="form-group">
                                <label>Address</label>
                                <input id="region_address_from" type="text" name="pickup_address" class="form-control region_suburb_addressname" value="{{ $removal_jobs_moving->pickup_address }}"/>
                            </div>
                            <div class="form-group">
                                <label>Suburb</label>
                                <input id="region_suburb_name_from" type="text" name="pickup_suburb" class="form-control region_suburb_name" value="{{ $removal_jobs_moving->pickup_suburb }}"/>
                            </div>
                            <div class="form-group">
                                <label>Postcode</label>
                                <input id="postcode_from" type="text" name="pickup_post_code" class="form-control" value="{{ $removal_jobs_moving->pickup_post_code }}"/>
                            </div>
                            <div class="form-group">
                                <label>Access Instructions</label>
                                <textarea row="2" id="pickup_access_restrictions" type="text" name="pickup_access_restrictions" class="form-control">{{ $removal_jobs_moving->pickup_access_restrictions }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Bedrooms</label>
                                <input id="pickup_bedrooms" type="text" name="pickup_bedrooms" class="form-control" value="{{ $removal_jobs_moving->pickup_bedrooms }}"/>
                            </div>
                            <div class="d-flex justify-content-start align-items-center m-t-10">
                                <button type="reset" class="btn btn-light show_update_movingfrom_btn">Cancel</button>
                                <button type="button" id="update_movingfrom_btn" class="btn bg-blue ml-3">Update</button>
                            </div>

                        </form>
                    </div> 
                </div>
            </div>

            <div class="col-lg-6 pr-lg-0">
                <div class="card view_blade_4_card">
                <div id="moving_to_grid">
                    @yield('moving_to_grid')
                </div>
                            {{-- FORM --}}
            <div id="update_movingto_form" class="card-body p10 hidden body_margin">
                <form id="movingto_form" class="custom-form" action="#">
                    @csrf
                    {{ Form::hidden('job_id', $removal_jobs_moving->job_id) }}
                    {{ Form::hidden('lead_id', $removal_opportunities->lead_id) }}
                    {{ Form::hidden('opp_id', $removal_opportunities->id) }}
                    @if ($removal_opportunities->lead_type == 'Commercial')
                        <div class="form-group">
                            <label>Contact Name</label>
                            <input type="text" name="drop_off_contact_name" class="form-control" value="{{ $removal_jobs_moving_data->drop_off_contact_name }}"/>
                        </div>
                        <div class="form-group">
                            <label>Mobile</label>
                            <input type="text" name="drop_off_mobile" class="form-control" value="{{ $removal_jobs_moving_data->drop_off_mobile }}"/>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" name="drop_off_email" class="form-control" value="{{ $removal_jobs_moving_data->drop_off_email }}"/>
                        </div>
                    @endif
                    <div class="form-group">
                        <label>Address</label>
                        <input id="region_address_to" type="text" name="drop_off_address" class="form-control region_suburb_addressname" value="{{ $removal_jobs_moving->drop_off_address }}"/>
                    </div>
                    <div class="form-group">
                        <label>Suburb</label>
                        <input id="region_suburb_name_to" type="text" name="delivery_suburb" class="form-control region_suburb_name" value="{{ $removal_jobs_moving->delivery_suburb }}"/>
                    </div>
                    <div class="form-group">
                        <label>Postcode</label>
                        <input id="postcode_to" type="text" name="drop_off_post_code" class="form-control" value="{{ $removal_jobs_moving->drop_off_post_code }}"/>
                    </div>
                    <div class="form-group">
                        <label>Access Instructions</label>
                        <textarea row="2" id="drop_off_access_restrictions" type="text" name="drop_off_access_restrictions" class="form-control">{{ $removal_jobs_moving->drop_off_access_restrictions }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Bedrooms</label>
                        <input id="drop_off_bedrooms" type="text" name="drop_off_bedrooms" class="form-control" value="{{ $removal_jobs_moving->drop_off_bedrooms }}"/>
                    </div>
                    <div class="d-flex justify-content-start align-items-center m-t-10">
                        <button type="reset" class="btn btn-light show_update_movingto_btn">Cancel</button>
                        <button type="button" id="update_movingto_btn" class="btn bg-blue ml-3">Update</button>
                    </div>

                </form>
            </div>           
            </div>
            </div>
        </div>
        <div class="row mt-3 row-margin-tp">
            <div class="col-lg-6 pr-lg-0">
                <div id="removal_booking_detail_grid">
                    @yield('removal_booking_detail_grid')
                </div>
            </div>
            <div class="col-lg-6 pr-lg-0">
                <div id="removal_property_detail_grid">
                    @yield('removal_property_detail_grid')
                </div>
            </div>
        </div>

    </div>
</div>