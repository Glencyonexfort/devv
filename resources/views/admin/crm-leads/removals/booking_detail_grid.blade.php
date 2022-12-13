<div class="card view_blade_4_card">
            <span class="view_blade_4_card_span">
                <div class="card-header header-elements-inline view_blade_4_card_header">
                    <h6 class="card-title card-title-mg view_blade_4_card_task">Booking Details</h6>
                </div>
            </span>
            {{-- VIEW --}}
            <div id="update_booking_detail_view">
                <div style="border-left:3px solid #fcbd2e;min-height: 20rem;" class="card-body job_left_panel_body1">
                    @if($removal_jobs_moving->opportunity == 'Y')
                        <div class="d-flex justify-content-start align-items-center float-right">
                            <button class="show_update_booking_detail_btn btn btn-icon"><i class="icon-pencil"></i></button>
                        </div>
                    @endif
                    <div class="job-label-txt">
                        <table class="left_panel_table" style="width: auto!important;">
                            <tbody>
                            <tr>
                                <td>
                                    Opportunity:
                                </td>
                                <td class="textalign-left">
                                    <span>{{$removal_jobs_moving->job_number}}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Estimated Job Date:
                                </td>
                                <td class="textalign-left">
                                    <span>{{$removal_jobs_moving->job_date->format($global->date_format)}}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Company:
                                </td>
                                <td class="textalign-left">
                                    <span>{{ isset($removal_jobs_moving->company->company_name) ? $removal_jobs_moving->company->company_name : ''}}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p class="job-label-txt job-status orange-status">
                                        {{$removal_opportunities->op_status}}
                                    </p>                                    
                                </td>
                            </tr>
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- FORM --}}
            <div id="update_booking_detail_form" class="card-body p10 hidden body_margin">
                <form id="booking_detail_form" class="custom-form" action="#">
                    @csrf
                    {{ Form::hidden('lead_id', $removal_opportunities->lead_id) }}
                    <div class="form-group">
                        <label>Opportunity</label>
                        <div class="input-group">
                            <strong>{{$removal_jobs_moving->job_number}}</strong>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Estimated Job Date</label>
                        <div class="input-group">
                            <span class="input-group-prepend">
                                <span class="input-group-text"><i class="icon-calendar22"></i></span>
                            </span>
                            <input name="job_date" type="text" class="form-control daterange-single" value="{{ $removal_jobs_moving->job_date->format($global->date_format) }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Company</label>
                        <select name="company_id" class="form-control">
                            @foreach($removal_companies as $data)
                            <option value="{{ $data->id }}" @if($data->id == $removal_jobs_moving->company_id)
                                selected=""
                                @endif
                                >{{ $data->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="op_status" class="form-control">
                            @foreach($removal_pipeline_statuses as $data)
                            <option value="{{ $data->pipeline_status }}" @if($data->pipeline_status == $removal_opportunities->op_status)
                                selected=""
                                @endif
                                >{{ $data->pipeline_status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex justify-content-start align-items-center m-t-10">
                        <button type="reset" class="btn btn-light show_update_booking_detail_btn">Cancel</button>
                        <button type="button" id="update_booking_detail_btn" class="btn bg-blue ml-3">Update</button>
                    </div>

                </form>
            </div>            

        </div>
