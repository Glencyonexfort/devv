<div class="d-flex justify-content-start align-items-center float-right">
        <button class="btn btn-icon show_update_pickup_btn"><i class="icon-pencil"></i></button>
    </div>
    @if(!empty($job->pickup_suburb) || isset($job->pickup_suburb))
        <p class="job-label-txt" style="font-size: 13px;">
            {{ $job->pickup_address}}
        </p>
        <p class="job-label-txt font-bold" style="font-size: 13px;">
            {{ $job->pickup_suburb }}
            {{ $job->pickup_post_code }}
        </p>
        <p class="job-label-txt float-right" style="color: #d9424d;margin:0px 10px 0" >
            {{ date($global->date_format,strtotime($job->job_date)) }}
        </p>
        <table class="left_panel_table" style="width: 100%">
            <tbody>
                @if ($job->pickup_bedrooms)
                    <tr>
                        <td>Bedrooms:</td>
                        <td>{{ $job->pickup_bedrooms }}</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="3">{{ $job->pickup_access_restrictions }}</td>
                </tr>
            </tbody>
        </table>
        @else
        <p class="muted">No pickup</p>
@endif