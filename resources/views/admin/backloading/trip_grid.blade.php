<table class="w3-table w3-striped w3-border table-bordered table-hover toggle-circle" id="tableData">
    <tr>
        <th style="width: 30%">Trip</th>
        <th style="width: 15%">Dates</th>
        <th style="width: 25%">Capacity Loading</th>
        <th style="width: 23%">Jobs</th>
        <th style="width: 7%">Action</th>
    </tr>
    @if ($trips != null)
        @foreach ($trips as $trip)
            <tr>
                <td>
                    <a href="{{ route('admin.backloading.assignJob', ['trip_id'=>$trip['id']]) }}">{{ $trip['trip_name'] }}</a> {{ $trip['start_city'] }}, {{ $trip['finish_city'] }} {{ $trip['license_plate_number'] }} {{ $trip['vehicle_name'] }} {{ $trip['waybill_number'] }}
                </td>
                <td>
                    {{ date('d/m/y',strtotime($trip['start_date'])) }} - {{ date('d/m/y',strtotime($trip['finish_date'])) }}
                </td>
                <td>
                    <div class="card card-body border-top-primary">
                        <div class="progress rounded-pill" style="height: 20px;">
                            <div class="progress-bar bg-teal" style="width: {{ $trip['capacity_loading'] }}%">
                                <span>{{ $trip['capacity_loading'] }}%</span>
                            </div>
                        </div>
                    </div>
                <td>
                    @if ($trip['all_jobs'] != null)
                        @foreach ($trip['all_jobs'] as $job)
                            <a href="{{ route("admin.list-jobs.view-job", $job['job_id']) }}" target="_blank" type="button" class="btn btn-primary" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Bottom tooltip">{{ $job['job_number'] }}</a> 
                        @endforeach
                    @endif
                </td>
                <td>
                    <div class="list-icons float-right">
                        <div class="dropdown">
                            <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown"><i class="icon-menu"></i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="#" class="delete_backloading_trip dropdown-item" data-tripid="{{ $trip['id'] }}" style="color: #ff0000"><i class="icon-trash"></i> Delete</a>        
                                </div>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
    @endif
</table>