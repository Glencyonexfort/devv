<ul class="media-list">
    <li class="media broderline1 contact_grid_1" style="border-bottom: none!important;">
        <div class="media-body">
            <span>{{ $trip->trip_name }}</span>
            <div class="text-muted">Loaded {{ $total_jobs_cbm }}m3 of {{ (int)$trip->cubic_capacity."m3" }}</div>
        </div>
        <strong class="pull-right" style="margin-right: 3rem;font-size: 14px;color: red;">{{ $trip->waybill_number }}</strong>
        <div class="header-elements broderline">
            <div class="list-icons">
                <div class="dropdown">
                    <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown">
                        <img class="contact_grid_2" src="{{ asset('newassets/img/icon-edit-1.png') }}">            
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="trip-update-btn dropdown-item cursor-pointer" title="Edit"><i class="icon-pencil5"></i>Edit</a>
                    </div>
                </div>
            </div>
        </div>
    </li>
</ul>
<div id="update_side_trip_form_grid" class="card-body light-blue-bg p10 hidden">
    <form id="update_trip_form" class="custom-form" action="#">
        @csrf
        {{ Form::hidden('trip_id', $trip->id) }}
        <div class="form-group">
            <label>Trip Name</label>
            <input name="trip_name" type="text" class="form-control" value="{{ $trip->trip_name }}">
        </div>

        <div class="form-group">
            <label>Start City</label>
            <input name="start_city" type="text" class="form-control" value="{{ $trip->start_city }}">
        </div>

        <div class="form-group">
            <label>Finish City</label>
            <input name="finish_city" type="text" class="form-control" value="{{ $trip->finish_city }}">
        </div>

        <div class="form-group">
            <label>Start Date</label>
            <input name="start_date" type="date" class="form-control" value="{{ $trip->start_date }}">
        </div>

        <div class="form-group">
            <label>Finish Date</label>
            <input name="finish_date" type="date" class="form-control" value="{{ $trip->finish_date }}">
        </div>

        <div class="form-group">
            <label>Vehicle</label>
            <select name="vehicle_id" class="form-control">
                <option value=""></option>
                @if (count($vehicles))
                    @foreach ($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" @if ($vehicle->id == $trip->vehicle_id)
                            selected
                        @endif>{{ $vehicle->vehicle_name }} {{ $vehicle->license_plate_number }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="form-group">
            <label>Driver</label>
            <select name="driver_id" class="form-control">
                <option value=""></option>
                @if (count($drivers))
                    @foreach ($drivers as $driver)
                        <option value="{{ $driver->id }}" @if ($driver->id == $trip->driver_id)
                            selected
                        @endif>{{ $driver->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" class="form-control">{{ $trip->notes }}</textarea>
        </div>

        <div class="d-flex justify-content-start align-items-center m-t-10">
            <button type="reset" class="btn btn-light cancel-trip-update-btn">Cancel</button>
            <button type="button" class="update_trip_btn btn bg-blue ml-3">Update</button>
        </div>

    </form>
</div>
<div class="card-body light-blue-bg p10" style="border: 1px solid #d2d2d2;border-top: none;">
    <form action="#">
        <div class="form-group">
            <p>{{ $trip->start_city }}&nbsp&nbsp&nbsp - &nbsp&nbsp&nbsp{{ $trip->finish_city }}</p>
        </div>

        <div class="form-group">
            <p>{{ $trip->start_date }}&nbsp&nbsp&nbsp - &nbsp&nbsp&nbsp{{ $trip->finish_date }}</p>
        </div>
        <div class="form-group">
            <p>{{ $trip->license_plate_number }}&nbsp&nbsp&nbsp - &nbsp&nbsp&nbsp{{ $trip->vehicle_name }}</p>
        </div>
        <div class="form-group">
            <p>{{ $trip->first_name }} {{ $trip->last_name }}</p>
        </div>
        <div class="form-group">
            <p>{{ $trip->notes }}</p>
        </div>
    </form>
</div>