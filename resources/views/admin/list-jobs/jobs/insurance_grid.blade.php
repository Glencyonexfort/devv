<div class="card">
        {!! Form::open(['id'=>'generalForm','class'=>'ajax-form','method'=>'POST']) !!}
            <div class="card-body">                    
                    <div class="simple_table">
                        
                        <div class="row">

                            <div class="col-6">
                                <div class="btn_add_invoice">
                                    <img src="{{asset('img/coverfreight-logo.png')}}">          
                                </div>
                            </div>
                            @if($tenant_details && $tenant_details->insurance_tab_enabled == 'Y')
                            @if($request_id)
                            <div class="col-6">
                                    <a href="{{route('admin.list-jobs.send-quote-to-customer', $job->job_id)}}" class="btn btn-success pull-right">@lang('modules.insurance.send_quote_to_customer')</a>
                            </div>
                            @endif
                        </div>
                        
                        <div class="row">
                            
                                <div class="table-responsive col-md-12">
                                    <table class="tablee pull-left" width="100%" id="miscellaneous-table">
                                        <thead  class="hidden">
                                            <tr>
                                                <th width="30%"></th>
                                                <th width="70%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>@lang('modules.insurance.name')</strong></td>
                                                <td>{{ $lead_name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('modules.insurance.email')</strong></td>
                                                <td>{{ $crm_contact_email }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('modules.insurance.pickup_suburb')</strong></td>
                                                <td>{{ $job->pickup_suburb }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('modules.insurance.delivery_suburb')</strong></td>
                                                <td>{{ $job->delivery_suburb }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('modules.insurance.value_of_goods')</strong></td>
                                                <td>{{ $job->goods_value }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('modules.insurance.comodity')</strong></td>
                                                <td>Household goods and personal effects</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('modules.insurance.conveyance')</strong></td>
                                                <td>Enclosed Truck / Container.</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('modules.insurance.reference')</strong></td>
                                                <td>{{'T-'.auth()->user()->tenant_id.'-J-'.$job->job_number }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                        </div>
                        @if($request_id && $tenant_details && $tenant_details->insurance_tab_enabled == 'Y'):
                        <br/>
                        <p class="job-label-txt job-status green-status">
                            INSURANCE
                        </p>
                        <div class="row">
                                <div class="table-responsive col-md-12">
                                    <table class="tablee pull-left" width="100%" id="miscellaneous-table">
                                        <thead class="hidden">
                                            <tr>
                                                <th width="30%"></th>
                                                <th width="70%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>@lang('modules.insurance.reference')</strong></td>
                                                <td>
                                                    @if($insurance_response != '' && $insurance_response->reference)
                                                    {{$insurance_response->reference}}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('modules.insurance.premium')</strong></td>
                                                <td>
                                                    @if($insurance_response != '' && $insurance_response->premium)
                                                    $ {{$insurance_response->premium}}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('modules.insurance.gst')</strong></td>
                                                <td>
                                                    @if($insurance_response != '' && $insurance_response->gst)
                                                    $ {{$insurance_response->gst}}
                                                    @endif
                                                </td>
                                            </tr>
                                            <!-- <tr>
                                                <td><strong>@lang('modules.insurance.fee')</strong></td>
                                                <td>
                                                    @if($insurance_response != '' && $insurance_response->fee)
                                                    {{$insurance_response->fee}}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('modules.insurance.fee_gst')</strong></td>
                                                <td>
                                                    @if($insurance_response != '' && $insurance_response->fee_gst)
                                                    {{$insurance_response->fee_gst}}
                                                    @endif
                                                </td>
                                            </tr> -->
                                            <tr>
                                                <td><strong>@lang('modules.insurance.quote')</strong></td>
                                                <td>
                                                    @if($insurance_response != '' && $insurance_response->insurance_quote_id)
                                                    {{$insurance_response->insurance_quote_id}}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('modules.insurance.comment')</strong></td>
                                                <td>
                                                    @if($insurance_response != '' && $insurance_response->comment)
                                                    {{$insurance_response->comment}}
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            
                        </div>
                        @endif
                    </div>
                    @else
                    <h4 style="color:#ed4040;">To enable this tab, please contact Coverfreight on info@coverfreight.com.au or </h4>
                    <h4 style="color:#ed4040;">07 3613 7901 or contact Onexfort support at support@onexfort.com</h4>
                    @endif
            </div>
        {!! Form::close() !!}
</div>