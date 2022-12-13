            <span class="view_blade_4_card_span">
                <div class="card-header header-elements-inline view_blade_4_card_header">
                    <h6 class="card-title card-title-mg view_blade_4_card_task">Moving To</h6>
                </div>
            </span>
            {{-- VIEW --}}
            <div id="update_movingto_view">
                <div style="border-left:3px solid #89dd88;" class="card-body job_left_panel_body1">
                    @if($removal_jobs_moving->opportunity == 'Y')
                        <div class="d-flex justify-content-start align-items-center float-right">
                            <button class="show_update_movingto_btn btn btn-icon"><i class="icon-pencil"></i></button>
                        </div>
                    @endif
                    <div class="job-label-txt">
                        <table class="left_panel_table" style="width: auto!important;">
                            <tbody>
                            <tr>
                                <td>
                                    <span class="w400 txt12">                                        
                                        {{ $removal_jobs_moving->drop_off_address?$removal_jobs_moving->drop_off_address.', ':'' }}
                                        {{ $removal_jobs_moving->delivery_suburb?$removal_jobs_moving->delivery_suburb:'' }}<br/>
                                        {{ $removal_jobs_moving->drop_off_post_code?$removal_jobs_moving->drop_off_post_code:'' }}
                                    </span>
                                </td>                                
                            </tr>                            
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
