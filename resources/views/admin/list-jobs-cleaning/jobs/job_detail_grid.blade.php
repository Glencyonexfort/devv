<?php
            if($job->job_status=='Completed'){
                $status_color = 'green-status';
                $border_color = 'border-left:3px solid #89dd88;';
            }else{
                $status_color = 'orange-status';
                $border_color = 'border-left:3px solid #fcbd2e;';
            }
?>
<div style="{{ $border_color }}" class="card-body job_left_panel_body1">
    <div class="d-flex justify-content-start align-items-center m-t-10 float-right">
        <button class="show_update_job_detail_btn btn btn-icon"><i class="icon-pencil"></i></button>
    </div>
    <p class="job-label-txt">
        @if($cleaning_job_type)
            {{ $cleaning_job_type }}
        @endif
    </p>
            <p class="job-label-txt">
                @if($companies)
                    {{ $companies->company_name }}
                @endif
            </p>

            <span>
                    {{ $job->job_date->format($global->date_format) }}
                    {{ ' ('.$cleaning_job_shift.')' }}
            </span><br/>
            <span>
                {{ $job->address }}
            </span><br/>
            <span>
                {{ $job->bedrooms.' Bedrooms, '. $job->bathrooms.' Bathrooms'}}
            </span><br/>
            
            <p class="job-label-txt job-status {{ $status_color }}">
                {{ $job->job_status }}
            </p>
</div>