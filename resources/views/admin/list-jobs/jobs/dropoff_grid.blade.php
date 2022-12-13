<div class="d-flex justify-content-start align-items-center float-right">
        <button class="btn btn-icon show_update_dropoff_btn"><i class="icon-pencil"></i></button>
    </div>
    @if(!empty($job->delivery_suburb) || isset($job->delivery_suburb))
        <p class="job-label-txt" style="font-size: 13px;">
            {{ $job->drop_off_address }}
        </p>
        <p class="job-label-txt font-bold" style="font-size: 13px;">
            {{ $job->delivery_suburb }}
            {{ $job->drop_off_post_code }}
        </p>                                                       
        <table class="left_panel_table" style="width: 100%">
            <tbody>
                @if ($job->drop_off_bedrooms)
                    <tr>
                        <td>Bedrooms:</td>
                        <td>{{ $job->drop_off_bedrooms }}</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="3">{{ $job->drop_off_access_restrictions }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <p class="muted">No dropoff</p>
    @endif