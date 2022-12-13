@extends('layouts.app')

@section('task_grid')
@include('admin.crm-leads.task_grid')
@endsection

@section('contact_grid')
@include('admin.crm-leads.contact_grid')
@endsection

@section('job_detail_grid')
@include('admin.list-jobs.jobs.job_detail_grid')
@endsection

@section('pickup_grid')
@include('admin.list-jobs.jobs.pickup_grid')
@endsection

@section('dropoff_grid')
@include('admin.list-jobs.jobs.dropoff_grid')
@endsection

@section('activity_notes_grid')
@include('admin.crm-leads.activity_notes_grid')
@endsection

@section('operations_leg_grid')
@include('admin.list-jobs.jobs.operations_leg_grid')
@endsection

@section('operations_trip_grid')
@include('admin.list-jobs.jobs.operations_trip_grid')
@endsection

@section('attachments_grid')
@include('admin.list-jobs.jobs.attachments_grid')
@endsection

@section('invoice_grid')
@include('admin.list-jobs.jobs.invoice_grid')
@endsection

@section('payment_grid')
@include('admin.list-jobs.jobs.payment_grid')
@endsection

@section('actual_hours_grid')
@include('admin.list-jobs.jobs.actual_hours_grid')
@endsection

@section('inventory_grid')
@include('admin.crm-leads.inventory_grid')
@endsection


@section('material_issues')
@include('admin.list-jobs.jobs.material_issues')
@endsection

@section('material_returns')
@include('admin.list-jobs.jobs.material_returns')
@endsection

@section('pickup_ohs_risk_assessment')
@include('admin.list-jobs.jobs.pickup_risk_assessment_grid')
@endsection

@section('delivery_ohs_risk_assessment')
@include('admin.list-jobs.jobs.delivery_risk_assessment_grid')
@endsection


@section('page-title')
<style>
    html { visibility:hidden; }
    .attachment-img {
    width: 24px;
    margin-right: 3px;
    }
    .files input {
        outline: 2px dashed #92b0b3;
        outline-offset: -10px;
        -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
        transition: outline-offset .15s ease-in-out, background-color .15s linear;
        padding: 120px 0px 85px 35%;
        text-align: center !important;
        margin: 0;
        width: 100% !important;
    }
    .files input:focus{     outline: 2px dashed #92b0b3;  outline-offset: -10px;
        -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
        transition: outline-offset .15s ease-in-out, background-color .15s linear; border:1px solid #92b0b3;
    }
    .files{ position:relative}
    .files:after {  pointer-events: none;
        pointer-events: none;
        position: absolute;
        top: 35px;
        left: 240px;
        width: 100%;
        content: "\f0ee";
        font-family: FontAwesome;
        display: block;
        margin: 0 auto;
        background-size: 100%;
        background-repeat: no-repeat;
        font-size: 80px;
        opacity: 0.4;
    }
    .color input{ background-color:#f1f1f1;}
    .files:before {
        position: absolute;
        bottom: 0px;
        font-size: 22px;
        left: 0;  pointer-events: none;
        width: 100%;
        right: 0;
        height: 57px;
        content: " or drag it here. ";
        display: block;
        margin: 0 auto;
        color: #999;
        font-weight: 600;
        text-align: center;
    }
    .attachment-link{
        font-size: 16px;
        color: #f44336!important;
        font-weight: 400;
    }
    #recalculate {
        padding: 5px;
    }
</style>
<div class="page-header page-header-light view_blade_page_header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex view_blade_page_padding">
            <h4>
            <img class="view_blade_page_img_header" src="../../../../../newassets/img/statistics (1)@2x.png">
                <span class="view_blade_page_span_header">Jobs </span>
        </div>
    </div>
</div>
@endsection

@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/tagify-master/dist/tagify.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">    
@endpush

@section('content')
{{ Form::hidden('job_id', $job_id) }}
<input id="op_job_type" value="Moving" type="hidden"/>
<input id="op_job_id" value="{{ $job_id }}" type="hidden"/>
<input id="crm_opportunity_id" value="{{ $job->crm_opportunity_id }}" type="hidden"/>
<div class="row">
    <!-- START:: Left content -->
    <div class="col-12 col-lg-3 px-2">        
        <div class="text-left lead_title">
            <h4 class="font-weight-semibold mb-1 view_blade_1_person_name"><a href="{{ route("admin.crm-leads.view-customer-leads", $crmlead->id) }}">{{ $crmlead->name }}</a><br/>
                <p>{{ $crmlead->lead_type }} <br/>
                <span style="font-weight: 500;font-size: 14px">Job # {{$job->job_number}}</span>
            </p>
            </h4>
        </div>
        {{-- Job Detail --}}
        <div class="card view_blade_4_card">
            <span class="view_blade_4_card_span">
                <div class="card-header header-elements-inline view_blade_4_card_header">
                    <h6 class="card-title card-title-mg view_blade_4_card_task">Job Details</h6>
                </div>
            </span>
            <div id="update_jo_detail_form" class="card-body light-blue-bg p10 hidden body_margin">
                <form id="job_detail_form" class="custom-form" action="#">
                    @csrf
                    <div class="form-group">
                        <label>Company</label>
                        <select name="company_id" class="form-control">
                            @foreach($company_list as $data)
                                <option value="{{ $data->id }}"
                                    @if($data->id == $job->company_id)
                                    selected=""
                                    @endif
                                    >{{ $data->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
            
                    <div class="form-group">
                        <label>Total Cubic Meters</label>
                        <input name="total_cbm" type="number" class="form-control" value="{{ $job->total_cbm }}">
                    </div>
            
                    <div class="form-group">
                        <label>Status</label>
                        <select name="job_status" class="form-control">
                            @foreach($job_status as $data)
                                <option value="{{ $data->options }}"
                                    @if($data->options == $job->job_status)
                                    selected=""
                                    @endif
                                    >{{ $data->options }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex justify-content-start align-items-center m-t-10">
                        <button type="reset" class="btn btn-light show_update_job_detail_btn">Cancel</button>
                        <button type="button" id="update_job_detail_btn" class="btn bg-blue ml-3">Update</button>
                    </div>
            
                </form>
            </div>
            <div id="update_jo_detail_view">
                <div id="job_detail_grid">
                    @yield('job_detail_grid')
                </div>
                <div style="border-left:3px solid #89dd88;" class="card-body job_left_panel_body1">
                    <div class="job-label-txt">
                        Fixed Price:
                        <table class="left_panel_table">
                            <tbody>
                            <tr>
                                <td>
                                    Total:
                                </td>
                                <td>
                                    <span id="totalInvoiceAmount">{{ $global->currency_symbol }}{{ number_format((float)($totalAmount), 2, '.', '') }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Payments:
                                </td>
                                <td>
                                    <span id="totalPaidAmount">{{ $global->currency_symbol }}{{ number_format((float)($paidAmount), 2, '.', '') }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Balance:
                                </td>
                                <td>
                                    <span id="totalBalanceAmount">{{ $global->currency_symbol }}{{ number_format((float)($totalAmount-$paidAmount), 2, '.', '') }}</span>
                                </td>
                            </tr>
                        </tbody>
                        </table>
                    </div>
                    <div id="payment_status">
                        <?php echo \App\Http\Controllers\Admin\ListJobsController::paymentStatus($totalAmount,$paidAmount);?>
                    </div>
                </div>
            </div>
            
        </div>
        {{-- Pickup --}}    
        <div class="card view_blade_4_card">
            <span class="view_blade_4_card_span">
                <div class="card-header header-elements-inline view_blade_4_card_header">
                    <h6 class="card-title card-title-mg view_blade_4_card_task" >Pickup</h6>
                </div>
            </span>
            <div id="update_pickup_form" class="card-body light-blue-bg p10 hidden body_margin">
                <form id="job_pickup_form" class="custom-form" action="#">
                    @csrf
                    @if ($crmlead->lead_type == 'Commercial')
                        <div class="form-group">
                            <label>Contact Name</label>
                            <input type="text" name="pickup_contact_name" class="form-control" value="{{ $job->pickup_contact_name }}" />
                        </div>
                        <div class="form-group">
                            <label>Mobile</label>
                            <input type="text" name="pickup_mobile" class="form-control" value="{{ $job->pickup_mobile }}"/>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" name="pickup_email" class="form-control" value="{{ $job->pickup_email }}"/>
                        </div>
                    @endif
                    <div class="form-group">
                        <label>Job Date</label>
                        <div class="input-group">
                            <span class="input-group-prepend"><span class="input-group-text"><i class="icon-calendar22"></i></span></span>
                            <input name="job_date" type="text" class="form-control daterange-single" value="{{ date('d/m/Y' , strtotime($job->job_date)) }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" name="pickup_address" class="form-control" value="{{ $job->pickup_address }}">
                    </div>

                    <div class="form-group">
                        <label>Suburb</label>
                        <input name="pickup_suburb" type="text" class="form-control" value="{{ $job->pickup_suburb }}">
                    </div>
                    <div class="form-group">
                        <label>Postcode</label>
                        <input name="pickup_postcode" type="text" class="form-control" value="{{ $job->pickup_post_code }}">
                    </div>
                    <div class="form-group">
                        <label for="Access Instructions">Access Instructions</label>
                        <textarea name="pickup_access_restrictions" class="form-control" id="pickup_access_restrictions" cols="30" rows="2">{{ $job->pickup_access_restrictions }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Bedrooms</label>
                        <input type="number" name="pickup_bedrooms" class="form-control" value="{{ $job->pickup_bedrooms }}">
                    </div>
                    <div class="d-flex justify-content-start align-items-center m-t-10">
                        <button type="reset" class="btn btn-light show_update_pickup_btn">Cancel</button>
                        <button type="button" id="update_job_pickup_btn" class="btn bg-blue ml-3">Update</button>
                    </div>
            
                </form>
            </div>
            <div id="update_pickup_view">
                <div id="job_pickup_grid" class="card-body job_left_panel_body1">
                    @yield('pickup_grid')
                </div>
            </div>
        </div>
        {{-- Drop Off --}}
        <div class="card view_blade_4_card">
            <span class="view_blade_4_card_span">
                <div class="card-header header-elements-inline view_blade_4_card_header">
                    <h6 class="card-title card-title-mg view_blade_4_card_task" >Drop Off</h6>
                </div>
            </span>
            <div id="update_dropoff_form" class="card-body light-blue-bg p10 hidden body_margin">
                <form id="job_dropoff_form" class="custom-form" action="#">
                    @csrf
                    @if ($crmlead->lead_type == 'Commercial')
                        <div class="form-group">
                            <label>Contact Name</label>
                            <input type="text" name="drop_off_contact_name" class="form-control" value="{{ $job->pickup_contact_name }}" />
                        </div>
                        <div class="form-group">
                            <label>Mobile</label>
                            <input type="text" name="drop_off_mobile" class="form-control" value="{{ $job->pickup_mobile }}"/>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" name="drop_off_email" class="form-control" value="{{ $job->pickup_email }}"/>
                        </div>
                    @endif
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" name="drop_off_address" class="form-control" value="{{ $job->drop_off_address }}">
                    </div>
            
                    <div class="form-group">
                        <label>Suburb</label>
                        <input name="delivery_suburb" type="text" class="form-control" value="{{ $job->delivery_suburb }}">
                    </div>
                    <div class="form-group">
                        <label>Postcode</label>
                        <input name="drop_off_postcode" type="text" class="form-control" value="{{ $job->drop_off_post_code }}">
                    </div>
                    <div class="form-group">
                        <label for="Access Instructions">Access Instructions</label>
                        <textarea name="drop_off_access_restrictions" class="form-control" id="drop_off_access_restrictions" cols="30" rows="2">{{ $job->drop_off_access_restrictions }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Bedrooms</label>
                        <input type="number" name="drop_off_bedrooms" class="form-control" value="{{ $job->drop_off_bedrooms }}">
                    </div>
                    <div class="d-flex justify-content-start align-items-center m-t-10">
                        <button type="reset" class="btn btn-light show_update_dropoff_btn">Cancel</button>
                        <button type="button" id="update_job_dropoff_btn" class="btn bg-blue ml-3">Update</button>
                    </div>
            
                </form>
            </div>
            <div id="update_dropoff_view">
                <div id="job_dropoff_grid" class="card-body job_left_panel_body1">
                    @yield('dropoff_grid')
                </div>
            </div>
        </div>

        <div class="card view_blade_4_card">
            <span class="view_blade_4_card_span">
                <div class="card-header header-elements-inline view_blade_4_card_header">
                    <h6 class="card-title card-title-mg view_blade_4_card_task">Tasks <b id="tasksCount">{{ $totalTasks }}</b></h6>
                    <div class="header-elements">
                        <div class="list-icons">
                            <span class="cursor-pointer add_new_task_btn"><img src="{{ asset('newassets/img/icon-add.png') }}"></span>
                            {{-- <a class="list-icons-item" data-action="collapse"></a> --}}
                        </div>
                    </div>
                </div>
            </span>
            <div id="add_new_task_form_grid" class="card-body light-blue-bg hidden p10 body_margin">
                <form id="add_new_task_form" class="custom-form" action="#">
                        @csrf
                        {{ Form::hidden('lead_id', $lead_id) }}
                        <div class="form-group">
                            <label>Task Decription</label>
                            <input name="description" type="text" class="form-control" placeholder="">
                        </div>
                        <div class="form-group">
                            <label>Date</label>
                            <div class="input-group">
                                <span class="input-group-prepend"><span class="input-group-text"><i class="icon-calendar22"></i></span></span>
                                <input name="task_date" type="text" class="form-control daterange-single" value="{{ date('d/m/Y') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Time</label>
                            <div class="input-group">
                                <span class="input-group-prepend"><span class="input-group-text"><i class="icon-alarm"></i></span></span>
                                <input name="task_time" type="text" class="form-control pickatime-editable" value="{{ date('h:m:s') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Assign User</label>
                            <select name="user_assigned_id" data-placeholder="" class="form-control">
                                @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                        @if($user->id == auth()->user()->id)
                                        selected=""
                                        @endif
                                        >{{ ucwords($user->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex justify-content-start align-items-center m-t-10">
                            <button type="reset" class="btn btn-light add_new_task_btn">Cancel</button>
                            <button id="create_task_btn" type="button" class="btn bg-blue ml-3">Save</button>
                        </div>
                </form>
            </div>
            <div id="tasks_grid" style="padding-top:0px !important;" class="card-body p-t-10 p10 view_blade_4_card_body_task">
                @yield('task_grid')
            </div>
        </div>

        {{-- Contact --}}
        <div class="card view_blade_4_card">
            <span class="view_blade_4_card_span">
                <div class="card-header header-elements-inline view_blade_4_card_header">
                    <h6 class="card-title card-title-mg view_blade_4_card_task">Contacts <b id="contactsCount"></b></h6>
                    <div class="header-elements">
                        <div class="list-icons">
                            <span class="cursor-pointer add_new_contact_btn">
                                <img src="{{ asset('newassets/img/icon-add.png') }}">
                                <!-- <i class="icon-add" style="left: -7px;"></i> --> 
                            </span>
                                {{-- <a class="list-icons-item" data-action="collapse"></a> --}}
                        </div>
                    </div>
                </div>
            </span>

            <div id="add_new_contact_form_grid" class="card-body light-blue-bg p10 hidden body_margin">
                <form id="add_new_contact_form" class="custom-form" action="#">
                    @csrf
                    {{ Form::hidden('lead_id', $lead_id) }}
                    <?php
                        $contact_detail = array(
                            'Office' => 'Office',
                            'Mobile' => 'Mobile',
                            'Email' => 'Email',
                            'Home'   => 'Home',
                            'Direct' => 'Direct',
                            'Fax'    => 'Fax',
                            'URL'    => 'URL',
                            'Other'  => 'Other',
                            );
                    ?>
                    <div class="form-group">
                        <label>Name</label>
                        <input name="name" type="text" class="form-control" placeholder="">
                    </div>

                    <div class="form-group">
                        <label>Title</label>
                        <input name="description" type="text" class="form-control" placeholder="">
                    </div>

                    <div class="form-group">
                        <label>Contact Detail</label>
                        <div class="input-group contact_detail_div mgb-10">
                            <input name="contact_detail[]" type="text" class="form-control contact_detail" placeholder="Phone, email or URL" autocomplete="false">
                            <div class="input-group-append">
                                {{ Form::select('contact_detail_type[]', $contact_detail, null, ['class' => 'form-control form-control-uniform']) }}
                            </div>
                        </div>
                    </div>

                    {{-- <div class="form-group">
                        <label><b>Custom Fields</b></label>

                    </div>
                    <div class="d-flex justify-content-start align-items-center">
                        <button type="submit" class="btn btn-light hgt">Create a new Custom Field</button>

                    </div> --}}

                    <div class="d-flex justify-content-start align-items-center m-t-10">
                        <button type="reset" class="btn btn-light add_new_contact_btn">Cancel</button>
                        <button id="create_contact_btn" type="button" class="btn bg-blue ml-3">Save</button>
                    </div>

                </form>
            </div>
            <div id="contacts_grid" class="card-body p10 view_blade_4_card_body_contact">
                @yield('contact_grid')
            </div>
        </div>
    </div>
    <!-- END:: Left content -->

    <!-- START:: Right content -->
    <div class="col-12 col-lg-9 px-2 pt-2">   
            <ul class="nav nav-tabs view_blade_5_navs_tabbs_box_shadow nobackground" id="submenu">
                <li class="nav-item noborder"><a href="#activity_tab" class="nav-link view_blade_5_navs_tabbs_nav_item_link" data-toggle="tab">Activity</a></li>
                <li class="nav-item noborder"><a href="#operations_tab" class="nav-link view_blade_5_navs_tabbs_nav_item_link" data-toggle="tab">Operations</a></li>
                <li class="nav-item noborder"><a href="#invoice_tab" class="nav-link view_blade_5_navs_tabbs_nav_item_link" data-toggle="tab">Invoice</a></li>
                <li class="nav-item noborder"><a href="#inventory_tab" class="nav-link view_blade_5_navs_tabbs_nav_item_link" data-toggle="tab">Inventory</a></li>
                <li class="nav-item noborder"><a id="storage_tab_btn" href="#storage_tab" class="nav-link view_blade_5_navs_tabbs_nav_item_link" data-toggle="tab">Storage</a></li>
                {{-- <li class="nav-item noborder"><a href="#insurance_tab" class="nav-link view_blade_5_navs_tabbs_nav_item_link" data-toggle="tab">Insurance</a></li> --}}

            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="activity_tab">
                    <div class="row">
                        <div class="col-md-6 col-xs-6 text-left">
                            <button type="button" class="btn add_notes_btn rounded-round btn-bg-grey pr-3 mr-2"><b><i class="icon-pencil4 mr-2 icon-color-blue"></i></b> Note</button>
                            <button type="button" class="btn add_email_btn rounded-round btn-bg-grey pr-3 mr-2"><b><i class="icon-envelop mr-2 icon-color-blue"></i></b> Email</button>
                            <button type="button" class="btn add_sms_btn rounded-round btn-bg-grey pr-3 mr-2"><b><i class="icon-bubble-dots3 mr-2 icon-color-blue"></i></b> SMS</button>                       
                        </div>

                        <div class="col-md-6 col-xs-6">
                            <div class="btn-group float-right">
                                <button type="button" id="btn-activity" class="btn btn-link dropdown-toggle dropdown-black" data-toggle="dropdown" aria-expanded="false">All Activities</button>
                                <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(143px, 36px, 0px);">
                                    <a href="#" class="dropdown-item activities" data-type="allactivities" data-lead_id="{{ $lead_id }}" ><i class="icon-menu7"></i>All Activities</a>
                                    <a href="#" class="dropdown-item activities" data-type="notes" data-lead_id="{{ $lead_id }}"><i class="icon-menu7"></i>Notes</a>
                                    <a href="#" class="dropdown-item activities" data-type="email" data-lead_id="{{ $lead_id }}"><i class="icon-menu7"></i>Email</a>
                                    <a href="#" class="dropdown-item activities" data-type="sms" data-lead_id="{{ $lead_id }}"><i class="icon-menu7"></i>SMS</a>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="content" style="padding: 1.25rem 0px;">
                        <div class="overflow-auto m-h-490">
                        <div class="timeline timeline-left m-l-14">
                            <div class="timeline-container">
                                {{-- Activity Notes --}}
                                <div id="add_notes_box" class="timeline-row hidden">
                                    <div class="timeline-icon activity_notes_grid_3_background">
                                        <i class="icon-pencil4 timelineicon"></i>
                                    </div>
                                    <div class="card">
                                        <div style="box-shadow: 0 1px 1px rgba(0, 0, 0, 0.12), 0 1px 1px rgba(0, 0, 0, 0.12) !important;" class="card-body">
                                            <form id="activity_notes_form" action="#">
                                                @csrf
                                                {{ Form::hidden('lead_id', $lead_id) }}
                                                <textarea name="notes" class="summernote"></textarea>
                                            <div id="notes_attachment_div" class="text-left" style="float: left;">                                                
                                            </div>
                                            <div class="text-right">
                                                <div class="list-icons">
                                                    <a class="cursor-pointer list-icons-item mr-lg-2" title="Attachment"  data-type="note" data-toggle="modal" data-target="#add_attachment_popup" onclick="addAttachmentPopup('notes')"><i class="icon-attachment" style="font-size: 16px;font-weight: bold;margin-right: 15px;"></i></a>
                                                    <a class="add_notes_btn cursor-pointer list-icons-item mr-2"><i class="icon-close2"></i></a>
                                                </div>
                                                <button id="store_notes_btn" type="button" class="btn bg-teal-400">Done<i class="icon-checkmark2 ml-2"></i></button>
                                            </div>
                                        </form>
                                        </div>
                                    </div>
                                </div>
                                {{-- Activity Add SMS --}}
                                <div id="add_sms_box" class="timeline-row hidden">
                                    <div class="timeline-icon activity_notes_grid_3_background">
                                        <i class="icon-bubble-dots3 timelineicon"></i>
                                    </div>
                                    <div class="card">
                                        <div style="box-shadow: 0 1px 1px rgba(0, 0, 0, 0.12), 0 1px 1px rgba(0, 0, 0, 0.12) !important;" class="card-body">
                                            <form id="activity_sms_form" action="#">
                                                @csrf
                                                {{ Form::hidden('lead_id', $lead_id) }}
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>SMS From</label>
                                                            <input type="text" required name="sms_from" id="sms_from" class="form-control" maxlength="11" value="{{ $companies->sms_number }}" readonly/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>SMS To</label>
                                                            <select id="act_sms_send_to" name="sms_to" class="form-control">
                                                                @foreach($sms_contacts as $contact)
                                                                <option value="0">SMS To</option>
                                                                <option value="{{ $contact->detail }}">{{ $contact->name.' - '.$contact->detail }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <select id="choose_sms_template" name="sms_templates" data-placeholder="" class="form-control">
                                                            <option value="0">Choose a Template</option>
                                                            @foreach($sms_templates as $sms)
                                                            <option value="{{ $sms->id }}">{{ ucwords($sms->sms_template_name) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                </div>

                                            </div>
                                        <div class="form-group">
                                            <textarea id="sms_message" name="sms_message" rows="3" cols="3" class="form-control" onkeyup="countChar(this)"></textarea>
                                            <note class='pull-left'>Cost = <span id='sms_costspan'>1</span> credit</note>
                                            <note class='pull-right'><i>(160 characters will cost 1 credit)</i> <span id='sms_totalWords'>0</span> characters</note>
                                            <input type="hidden" name="sms_total_credits" id="sms_total_credits" value="">
                                        </div>
                                        <div class="text-right" style="margin-top: 25px;">
                                            <div class="list-icons">
                                                <a class="add_sms_btn cursor-pointer list-icons-item mr-2"><i class="icon-close2"></i></a>
                                            </div>
                                            <button id="send_sms_btn" type="button" class="btn bg-teal-400">Send<i class="icon-paperplane ml-2"></i></button>
                                        </div>
                                    </form>
                                    </div>
                                    </div>
                                </div>
                                {{-- Activity Add Email --}}
                                <div  id="add_email_box" class="timeline-row hidden">
                                    <div class="timeline-icon activity_notes_grid_3_background">
                                        <i class="icon-envelop timelineicon"></i>
                                    </div>
                                    <div class="card">
                                        <div class="card-body">
                                            <form id="activity_email_form" action="#">
                                                @csrf
                                                {{ Form::hidden('lead_id', $lead_id) }}
                                                {{-- {{ Form::hidden('from_name', $companies->contact_name) }} --}}
                                                <input type="hidden" name="job_id" id="job_id" value="{{ $job->job_id }}">
                                                <input type="hidden" name="from_name" id="act_email_from_name" value="{{ $company_list->first()->contact_name }}">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">From</span>
                                                                </div>
                                                            <select id="act_email_from" name="from_email" class="form-control form-control-lg">
                                                                @foreach($company_list as $company)
                                                                    <option value="{{ $company->email }}" data-name="{{ $company->contact_name }}">{{ $company->email }}</option>
                                                                    @endforeach
                                                            </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                        <a class="cursor-pointer" id="add_email_cc" style="margin:6px 10px">Add CC</a> 
                                                        <a class="cursor-pointer" id="add_email_bcc" style="margin:6px 10px">Add BCC</a>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">To</span>
                                                                </div>
                                                                <input type="text" id="act_email_to" name="to" data-input="to" class="search_email form-control" value="{{ $crm_contact_email }}">                                                                                                                         
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>                                            
                                        
                                                <div  class="row">
                                                    <div id="add_email_cc_box" class="col-md-6 hidden">
                                                        <div class="form-group">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">CC</span>
                                                                </div>
                                                                <input type="text" id="act_email_cc" name="cc" data-input="cc" class="search_email form-control" value=""> 
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="add_email_bcc_box" class="col-md-6 hidden">
                                                        <div class="form-group">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">BCC</span>
                                                                </div>
                                                                <input type="text" id="act_email_bcc" name="bcc" data-input="bcc" class="search_email form-control" value="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input class="form-control form-control-lg" id="email_subject" type="text" name="email_subject" placeholder="Subject">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <select id="choose_email_template" name="email_template" class="form-control form-control-lg">
                                                        <option value="0">Choose a Template</option>
                                                        @foreach($email_templates as $email)
                                                            <option value="{{ $email->id }}">{{ ucwords($email->email_template_name) }}</option>
                                                            @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <textarea name="email_body" id="email_body" class="summernote">{{ '<br/><br/>'.$ppl_people->email_signature }}</textarea>
                                        <div id="email_attachment_div" class="text-left" style="float: left;">

                                        </div>
                                        <div class="text-right" style="margin-top: 10px;">
                                            <div class="list-icons">
                                                <a class="cursor-pointer list-icons-item mr-lg-2" title="Attachment"  data-type="note" data-toggle="modal" data-target="#add_attachment_popup" onclick="addAttachmentPopup('email')"><i class="icon-attachment" style="font-size: 16px;font-weight: bold;margin-right: 15px;"></i></a>
                                                <a class="add_email_btn cursor-pointer list-icons-item mr-2"><i class="icon-close2"></i></a>
                                            </div>
                                            <button id="send_email_btn" type="button" class="btn bg-teal-400">Send<i class="icon-paperplane ml-2"></i></button>
                                        </div> 
                                        </form>                                       
                                    </div>
                                    </div>
                                </div>                            
                            
                            <div id="activity_notes_grid">
                                @yield('activity_notes_grid')
                            </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="operations_tab" style="overflow: hidden;">
                    <div class="form-group row mt-1">
                        <h3 class="col-lg-2" style="font-size: 20px;font-family: 'Poppins', sans-serif;">Legs</h3>
                    </div>
                    <div id="operations_leg_table_grid" class="card p-2" style="border: none!important">
                        @yield('operations_leg_grid')
                    </div>
                    <div class="form-group row mt-1">
                        <h3 class="col-lg-2" style="font-size: 20px;font-family: 'Poppins', sans-serif;">Trips</h3>
                    </div>
                    <div id="operations_trip_table_grid" class="card" style="border: none!important">
                        @yield('operations_trip_grid')
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <hr>
                        </div>
                    </div>
                    <div class="form-group row mt-1">
                        <div class="col-lg-10">
                            <h3 style="font-size: 20px;font-family: 'Poppins', sans-serif;"> 
                                <span>Material Issues</span>
                                <input type="hidden" name="job_id" id="job_id_material" value="{{ $job_id }}">
                                <button type="button" class="btn btn-light updateAndGenerateInvoice" data-jobid="{{ $job_id }}" data-type="Moving"><i class="icon-clipboard3 ml-2"></i> Update & Generate Invoice</button> 
                            </h3>
                        </div>
                    </div>
                    <div id="material_issue_table_grid" class="card col-md-11" style="border: none!important">
                        @yield('material_issues')
                    </div>


                    <div class="form-group row mt-1">
                        <h3 class="col-lg-10" style="font-size: 20px;font-family: 'Poppins', sans-serif;">Material Returns</h3>
                    </div>
                    <div id="material_return_table_grid" class="card col-md-11" style="border: none!important">
                        @yield('material_returns')
                    </div>

                    <div class="form-group row mt-1">
                        <h3 class="col-lg-10" style="font-size: 20px;font-family: 'Poppins', sans-serif;">Pickup - OHS Risk Assessment</h3>
                    </div>
                    <div id="pickup_ohs_risk_assessment_table_grid" class="card col-md-12" style="border: none!important">
                        @yield('pickup_ohs_risk_assessment')
                    </div>

                    <div class="form-group row mt-1">
                        <h3 class="col-lg-10" style="font-size: 20px;font-family: 'Poppins', sans-serif;">Delivery - OHS Risk Assessment</h3>
                    </div>
                    <div id="delivery_ohs_risk_assessment_table_grid" class="card col-md-12" style="border: none!important">
                        @yield('delivery_ohs_risk_assessment')
                    </div>

                    <div id="attachments_grid">
                        @yield('attachments_grid')
                    </div>
                </div>
                <div class="tab-pane fade" id="invoice_tab" style="overflow: hidden;">
                    <div class="row">
                        @if ($invoice->id>0)
                        <div class="col-lg-4">
                            <button type="button" class="btn btn-sm btn-light listJobInvoiceGenerate" data-jobid="{{$job_id}}" data-type="Moving"><i class="icon-clipboard3"></i> Generate Invoice PDF</button>
                            <button type="button" id="listJobInvoiceDownload" class="btn btn-sm btn-light ml-2 listJobInvoiceDownload" data-jobid="{{$job_id}}" @if (empty($invoice) || $invoice->file_original_name == null) disabled @endif><i class="icon-file-pdf"></i> Download</button>
                        </div>
                        @endif
                        <div class="col-lg-4">
                            <button type="button" class="btn btn-sm btn-light listJobPODGenerate" data-jobid="{{$job_id}}" data-type="Moving"><i class="icon-clipboard3"></i> Generate POD PDF</button>
                            <button type="button" id="listJobPODDownload" class="btn btn-sm btn-light ml-2 listJobPODDownload" data-jobid="{{$job_id}}" @if (empty($job) || $job->pod_file_name == null) disabled @endif><i class="icon-file-pdf"></i> Download</button>
                        </div>
                        <div class="col-lg-4">
                            <button type="button" class="btn btn-sm btn-light listJobWorkOrderGenerate" data-jobid="{{$job_id}}" data-type="Moving"><i class="icon-clipboard3"></i> Generate Work Order PDF</button>
                            <button type="button" id="listWorkOrderInvoiceDownload" class="btn btn-sm btn-light ml-2 listJobWorkOrderDownload" data-jobid="{{$job_id}}" @if (empty($job) || $job->work_order_file_name == null) disabled @endif><i class="icon-file-pdf"></i> Download</button>
                        </div>
                    </div><br/>
                    <div class="row">
                        <div class="col-lg-6">
                            <button type="button" id="jobGenerateInsurance" class="btn btn-sm btn-light" @if ($coverFreight_connected==false) disabled @endif><i class="icon-clipboard3 ml-2"></i> Generate Insurance Quote</button>
                            <button type="button" id="jobDownloadInsurance" class="btn btn-sm btn-light ml-2" @if (empty($job) || $job->insurance_file_name == null) disabled @endif><i class="icon-file-pdf ml-2"></i> Download</button><br/>
                        </div>
                    </div>
                    @if($invoice->id>0)
                    <div class="form-group row mt-1">
                        <h3 class="col-lg-2" style="font-weight: 500;font-size: 14px;">Invoice # {{ $invoice->invoice_number .' - '. $invoice->inv_version}}</h3>
                    </div>
                    @endif
                    <div id="invoice_table_grid" class="card mt-1" style="border: none!important">
                        @yield('invoice_grid')
                    </div>
                    <p class="job-label-txt job-status green-status">
                        PAYMENTS
                    </p>
                    @if($invoice->id>0 && isset($stripe->account_key) && !empty($stripe->account_key))
                        <input type="hidden" id="invoice_id" value="{{ $invoice->id }}"/>
                        <input type="hidden" id="new_stripe_payment_amount"/>
                        <button id="payButton" type="button" class="btn btn-light inv-stripe-btn" data-invoiceid="{{ $invoice->id }}">Add Stripe Payment</button> 
                        {{-- quickfee button   @else{
                            <input type="hidden" id="invoice_id" value="{{ $invoice->id }}"/>
                            <input type="hidden" id="new_quickfee_payment_amount"/>
                            <button id="QuickFeeButton" type="button" class="btn btn-light inv-stripe-btn" data-invoiceid="{{ $invoice->id }}">Add QuickFee Payment</button>

                        } --}}
                    @endif
                    <div id="payment_table_grid" class="card" style="border: none!important;">
                        @yield('payment_grid')
                    </div>
                    <p class="job-label-txt job-status green-status">
                        ACTUAL HOURS
                    </p>
                    @if($invoice->id>0)
                    <div id="actual_hours_table_grid" class="card" style="border: none!important;">
                        @yield('actual_hours_grid')
                    </div>
                    <button id="update_regenerate_invoice" type="button" class="btn btn-light" data-jobid="{{$job_id}}" data-invoiceid="{{ $invoice->id }}"><i class="icon-clipboard3 ml-2"></i> Update & Generate Invoice</button>
                    @endif
                </div>
                <div class="tab-pane fade" id="inventory_tab" style="overflow: hidden;">
                    @yield('inventory_grid')
                </div>
                <div class="tab-pane fade" id="storage_tab" style="overflow: hidden;">
                    <div id="storage_reservation" class="mb-4"></div>                  
                    <div id="storage_invoice" class="card" style="border: none!important"></div>
                    <p class="job-label-txt job-status green-status">
                        PAYMENTS
                    </p>
                    <div id="storage_payment" class="card" style="border: none!important"></div>
                </div>
                {{-- <div class="tab-pane fade" id="insurance_tab" style="overflow: hidden;">
                    @yield('insurance_grid')
                </div> --}}
            </div>
    </div>
    <!-- END:: Right content -->
</div>    
<!-- /Add Attachment Popup -->
<div id="add_attachment_popup" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="border: 1px solid #f0f0f0;padding-bottom:10px">
                <span style="font-size:18px;font-weight: 400;">Add Attachment</span>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
        
            <form id="new_attachment_form" action="" method="post">
                @csrf
                <div class="modal-body">
                        <div class="form-body">                        
                            <div class="row">
                                <div class="col-md-12">
                                        <div class="form-group files">
                                            <label>Upload Your File </label>
                                            <input id="activity_attachment" type="file" class="form-control" name="attachment">
                                            <input id="activity_attachment_type" type="hidden" name="type">
                                            {{ Form::hidden('job_id', $job_id) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer" style="background-color: #f5f5f5!important;padding: 10px 20px!important;">
                    <button type="button" class="btn btn-link add_attachment_btn" data-dismiss="modal">Cancel</button>
                    <button id="upload_attachment_btn" type="button" class="btn btn-success">Upload</button>
                    {{-- <input id="create_leatn" type="submit" value="Create Lead" class="btn btn-success"/> --}}            
                </div>
            </form>
    </div>
</div>
</div>
<!-- /Add Attachment Popup -->
@endsection

@push('footer-script')
<script src="{{ asset('newassets/global_assets/js/plugins/editors/summernote/summernote.min.js') }}"></script>
<script src="{{ asset('js/summernote-ext-print.js') }}"></script>
<script src="{{ asset('newassets/global_assets/js/plugins//forms/styling/uniform.min.js') }}"></script>
<script src="{{ asset('newassets/global_assets/js/plugins/pickers/daterangepicker.js') }}"></script>
<script src="{{ asset('newassets/global_assets/js/demo_pages/picker_date.js') }}"></script>
<script src="{{ asset('plugins/bower_components/sweetalert/sweetalert2.min.js') }}"></script>
<script src="{{ asset('js/ajax-requests/ajax-requests-jobs.js?v=2') }}"></script>
<script src="{{ asset('js/ajax-requests/ajax-requests-invoice.js') }}"></script>
<script src="{{ asset('js/ajax-requests/ajax-requests-activity.js?v=5') }}"></script>
<script src="{{ asset('js/ajax-requests/ajax-requests-payments.js?v=1') }}"></script>
<script src="{{ asset('js/ajax-requests/ajax-requests-material_issues.js') }}"></script>
<script src="{{ asset('js/ajax-requests/ajax-requests-material_returns.js') }}"></script>
{{-- <script src="{{ asset('js/ajax-requests/ajax-requests-opperations-leg.js') }}"></script> --}}
<script src="{{ asset('js/ajax-requests/ajax-requests-opperations-leg-offsiders.js') }}"></script>
<script src="{{ asset('js/ajax-requests/ajax-requests-opperations-trip.js') }}"></script>
<script src="{{ asset('js/ajax-requests/ajax-requests-job-storage.js') }}"></script>
{{-- <script src="https://maps.googleapis.com/maps/api/js?key={{ $google_api_key }}&libraries=places"></script> --}}

<script type="text/jscript" src="https://checkout.stripe.com/checkout.js"></script>
<style>
    .swal-button,.swal-button:hover {
        padding: 8px 20px;
        text-shadow: none;
        font-weight: normal;
        border-radius: 2px;
        background-color: #4ca79a;
    }
    .swal-button--cancel {
        background-color: #e8e8e8!important;
    }
</style>
<script>

$("body").off('click', '.updateAndGenerateInvoice').on('click', '.updateAndGenerateInvoice', function () {
        var job_id = $(this).data('jobid');
        var _token = $('input[name="_token"]').val();
        $.ajax({
            url: "/admin/moving/ajaxUpdateAndGenerateInvoice",
            method: 'post',
            data: {
                '_token': _token,
                'job_id': job_id,
            },
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) 
                {
                    var job_id = $('#job_id_material').val();
                    //Regenrate Invoice PDF
                    $.ajax({
                        url: "/admin/moving/list-jobs/generateInvoice/" + job_id+"/Moving",
                        method: 'GET',
                        dataType: "json",
                        beforeSend: function () {
                            $.blockUI();
                        },
                        complete: function () {
                            $.unblockUI();
                        },
                        success: function (response) {
                            //----
                            $('#listJobInvoiceDownload').removeAttr("disabled");
                            //Notification....
                            $.toast({
                                heading: 'Success',
                                text: result.message,
                                icon: 'success',
                                position: 'top-right',
                                loader: false,
                                bgColor: '#00c292',
                                textColor: 'white'
                            });
                            //..
                        }
                    });
                    
                    location.reload();
                }else
                {
                    $.toast({
                        heading: 'Danger',
                        text: 'Something Went Wrong',
                        icon: 'success',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#00c292',
                        textColor: 'white'
                    });
                    //..
                }
            },
        });
    });
        
    var handler = StripeCheckout.configure({
        key: "{{ env('STRIPE_PUBLIC') }}",
        //key: "pk_test_E0qVMK2JSQXGItx8GHNrlqX9",
        image: '{{ request()->getSchemeAndHttpHost() }}/stripe-onex-logo.jpg',
        locale: 'auto',
        currency:'aud',
        allowRememberMe: false,
        token: function(token) {
            // You can access the token ID with `token.id`.
            // Get the token ID to your server-side code for use.
            var tkn = "{{ csrf_token() }}";
            $('#paymentDetails').hide();
            var invoice_id = $('#invoice_id').val();
            var amount = $('#new_stripe_payment_amount').val();
            $.ajax({
                    url: "/admin/moving/list-jobs/ajaxChargeStripePayment",
                    type: 'POST',
                    data: {'_token':tkn,stripeToken: token.id, stripeEmail: token.email,'stripeCustomerId':'N',
                    'invoice_id': invoice_id, 
                    'amount': amount,
                    'sys_job_type': "Moving"
                },
                dataType: "json",
                beforeSend: function(){
                    $('#payButton').html('Please wait...');
                },
                success: function(data){                      
                    if(data.status == 1){
                        location.reload();
                    } else {
                        $.toast({
                        heading: 'Error',
                        text: data.msg,
                        icon: 'error',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#fb9678',
                        textColor: 'white'
                    });
                    }
                },
                error: function(data) {
                    $('#payButton').html('Add Stripe Payment');
                    $("#error_box").show().delay(2000).fadeOut('slow');
                }
            });
        }
    });
    
        var stripe_closed = function(){
            $('#payButton').html('Add Stripe Payment');
        };
    
        var eventTggr = document.getElementById('payButton');
        if(eventTggr){
            eventTggr.addEventListener('click', function(e) {  
                var balance = $("#inv_balance_amount").val();
                var stripe_one_off_customer_id = $("#stripe_one_off_customer_id").val();
                var amountField = document.createElement('input');         
                amountField.setAttribute("placeholder", "0.00");
                amountField.setAttribute("value", balance);  
                if(stripe_one_off_customer_id.length ==""){
                    swal({
                    width: '35%',
                    title: "Process the payment using",                    
                    buttons: {
                        cancel: "No Cancel Please!",
                        new_card: {
                            text: "New Card",
                            value: "2",
                        },
                    },
                    content: amountField,
                    }).then((value) => {
                        let amount = amountField.value;
                        console.log(amount);    
                        console.log(value);
                        if (amount>0) {     
                            $('#new_stripe_payment_amount').val(amount);           
                        }else{
                            return false;
                        }     
                        switch (value) {                                                      
                            case "2":
                                $('#payButton').html('Please wait...');
                                // Open Checkout with further options:
                                handler.open({
                                    name: '{{ $organisation_settings->company_name }}',
                                    description: 'Stripe Payment',
                                    email: '{{ $crm_contact_email }}',
                                    amount: amount*100,
                                    closed:	stripe_closed
                                });                                
                            break;
                            }
                    });
                }else{      
                    swal({
                        width: '35%',
                        title: "Process the payment using",                    
                        buttons: {
                            cancel: "No Cancel Please!",
                            saved_card: {
                                text: "Saved Card",
                                value: "1",
                            },
                            new_card: {
                                text: "New Card",
                                value: "2",
                            },
                        },
                        content: amountField,
                        }).then((value) => {
                            $(".swal-button--saved_card").hide();
                            let amount = amountField.value;
                            console.log(amount);    
                            console.log(value);
                            if (amount>0) {     
                                $('#new_stripe_payment_amount').val(amount);           
                            }else{
                                return false;
                            }     
                            switch (value) {                            
                                case "1":
                                swal({
                                        title: "Are you sure?",
                                        text: "The payment will be processed using the customer's saved card!",
                                        icon: "warning",
                                        buttons: true,
                                    }).then((value) => {
                                        if (value) {
                                            var tkn = "{{ csrf_token() }}";
                                            $('#paymentDetails').hide();
                                            var invoice_id = $('#invoice_id').val();
                                            var payamount = $('#new_stripe_payment_amount').val();
                                            $.ajax({
                                                    url: "/admin/moving/list-jobs/ajaxChargeStripePayment",
                                                    type: 'POST',
                                                    data: {'_token':tkn,'invoice_id': invoice_id, 'stripeCustomerId': stripe_one_off_customer_id,'amount': payamount,
                                                    'sys_job_type': "Moving"
                                                },
                                                dataType: "json",
                                                beforeSend: function(){                                                    
                                                    $('.preloader').show();
                                                    $('#payButton').html('Please wait...');
                                                },
                                                success: function(data){                      
                                                    if(data.status == 1){
                                                        location.reload();
                                                    } else {
                                                        $.toast({
                                                        heading: 'Error',
                                                        text: data.msg,
                                                        icon: 'error',
                                                        position: 'top-right',
                                                        loader: false,
                                                        bgColor: '#fb9678',
                                                        textColor: 'white'
                                                    });
                                                    }
                                                },
                                                error: function(data) {
                                                    $('#payButton').html('Add Stripe Payment');
                                                    $("#error_box").show().delay(2000).fadeOut('slow');
                                                }
                                            });          
                                        }
                                    });                      
                                break;                            
                                case "2":
                                    $('#payButton').html('Please wait...');
                                    // Open Checkout with further options:
                                    handler.open({
                                        name: '{{ $organisation_settings->company_name }}',
                                        description: 'Stripe Payment',
                                        email: '{{ $crm_contact_email }}',
                                        amount: amount*100,
                                        closed:	stripe_closed
                                    });                                
                                break;
                                }
                        });
                    }
            e.preventDefault();
        });
    }
</script>

<script type="text/javascript">
    $(document).ready(function() {
      var autocompletesWraps = ['facility_address', 'source_address'];
      createGeoListeners(autocompletesWraps);
    });
    function createGeoListeners(autocompletesWraps) {
            var options = {types: ['geocode'],componentRestrictions: {country: "au"}};
            var inputs = $('.geo-address');
            var autocompletes = [];
            for (var i = 0; i < inputs.length; i++) {
                var autocomplete = new google.maps.places.Autocomplete(inputs[i], options);
                autocomplete.inputId = inputs[i].id;
                autocomplete.parentDiv = autocompletesWraps[i];
                autocomplete.addListener('place_changed', fillInAddressFields);
                inputs[i].addEventListener("focus", function() {
                    geoLocate(autocomplete);
                }, false);
                autocompletes.push(autocomplete);
            }
        }
        function fillInAddressFields() {
            $('.googleerror').removeClass('is-valid is-invalid');
            var place = this.getPlace();
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                var val = place.address_components[i].long_name;
                //console.log("address Type " + addressType + " val " + val + " pd " + this.parentDiv);
                $('#'+this.parentDiv).find("."+addressType).val(val);
                $('#'+this.parentDiv).find("."+addressType).attr('disabled', false);
            }
        }
        function geoLocate(autocomplete) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var geolocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    var circle = new google.maps.Circle({
                        center: geolocation,
                        radius: position.coords.accuracy
                    });
                    autocomplete.setBounds(circle.getBounds());
                });
            }
        }
        function gm_authFailure() { 
            $('.gm-err-autocomplete').addClass('is-invalid');
            swal("Error","There is a problem with the Google Maps or Places API","error");
        };

    // $(document).on('keyup', '.geo-address', function() {
    //     setTimeout(function() {
    //         var newval = $(this).val().replace(', Australia', '');
    //         $(this).val(newval);
    //     }, 10);
    // });
</script>
<script>
    $(document).ready(function() { 
        $('#submenu a').click(function(e) {
                e.preventDefault();
                $(this).tab('show');
        });
        $(window).load(function() { 
            // store the currently selected tab in the hash value
            $("ul.nav-tabs > li > a").on("shown.bs.tab", function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
            var id = $(e.target).attr("href").substr(1);
            var scrollmem = $('html,body').scrollTop();
            window.location.hash = id;
            $('html,body').scrollTop(scrollmem);
            });

            // on load of the page: switch to the currently selected tab
            var hash = window.location.hash;
            if(hash==""){
                $('#submenu a[href="#activity_tab"]').addClass("active");
            }else{
                $('#submenu a[href="' + hash + '"]').tab('show');
                $(hash).addClass("show active");
            }
        
        });
    });
    function countChar(val) {
        var len = $('#sms_message').val().length;//val.value.length;
        var credits = 0;
        if(len>0){
             credits = Math.ceil(len/160);
        }
        $('#sms_costspan').text(credits);
        $('#sms_totalWords').text(len);
        $('#sms_total_credits').val(credits);
    }
    $(document).ready(function () {
        
        $('.daterange-single').daterangepicker({ 
            singleDatePicker: true,
            locale: {
                format: 'DD/MM/YYYY'
            }
        });
$('.add_new_task_btn').click(function() {
        $("#add_new_task_form_grid").toggle(200);
    });
         //Delete Task
    $('body').on('click', '.task-remove-btn', function () {
        var id = $(this).data('taskid');
        var leadid = $(this).data('leadid');
        swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover the deleted task!",
                    icon: "warning",
                    buttons: true,
                }).then((isConfirm) => {
                    if (isConfirm) {
                // var url = "{{ route('admin.clients.destroy',':id') }}";
                // url = url.replace(':id', id);
                var token = "{{ csrf_token() }}";
                $.ajax({
            url: "/admin/crm/crm-leads/ajaxDestroyTask",
            method: 'post',
            data: {'_token': token, 'task_id': id,'lead_id':leadid},
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    $('#tasks_grid').html(result.task_html);
                    $("#tasksCount").html(result.task_count);
                    //Notification....
                    $.toast({
                        heading: 'Deleted',
                        text: result.message,
                        icon: 'success',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#00c292',
                        textColor: 'white'
                    });
                    //..
                }
            }
        });
            }
        });
    });
//end:: delete task

        //Delete contact
        $('body').on('click', '.contact-remove-btn', function () {
                var id = $(this).data('contactid');
                var leadid = $(this).data('leadid');
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover the deleted contact!",
                    icon: "warning",
                    buttons: true,
                }).then((isConfirm) => {
                    if (isConfirm) {
                        // var url = "{{ route('admin.clients.destroy',':id') }}";
                        // url = url.replace(':id', id);
                        var token = "{{ csrf_token() }}";
                        $.ajax({
                    url: "/admin/crm/crm-leads/ajaxDestroyContact",
                    method: 'post',
                    data: {'_token': token, 'contact_id': id,'lead_id':leadid},
                    dataType: "json",
                    beforeSend: function () {
                        $.blockUI();
                    },
                    complete: function () {
                        $.unblockUI();
                    },
                    success: function (result) {
                        if (result.error == 0) {
                            $('#contacts_grid').html(result.contact_html);
                            $("#contactsCount").html(result.contact_count);
                            //Notification....
                            $.toast({
                                heading: 'Deleted',
                                text: result.message,
                                icon: 'success',
                                position: 'top-right',
                                loader: false,
                                bgColor: '#00c292',
                                textColor: 'white'
                            });
                            //..
                        }
                    }
                });
                    }
                });
            });
        //end:: delete contact\
        
        //Delete Activity note
$('body').on('click', '.actNote-remove-btn', function () {
        var id = $(this).data('id');
        var leadid = $(this).data('leadid');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted note!",
            icon: "warning",
            buttons: true,
        }).then((isConfirm) => {
            if (isConfirm) {
                // var url = "{{ route('admin.clients.destroy',':id') }}";
                // url = url.replace(':id', id);
                var token = "{{ csrf_token() }}";
                $.ajax({
            url: "/admin/crm/crm-leads/ajaxDestroyNote",
            method: 'post',
            data: {'_token': token, 'id': id,'lead_id':leadid},
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) {
                if (result.error == 0) {
                    $("#act_note_form_div_"+id).toggle();
                    //Notification....
                    $.toast({
                        heading: 'Deleted',
                        text: result.message,
                        icon: 'success',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#00c292',
                        textColor: 'white'
                    });
                    //..
                }
            }
        });
            }
        });
    });
//end:: delete Activity note

//start:: Activity SMS Template
$(document).on('change', '#choose_sms_template', function() {
    var $this = $(this);
        var template_id = $this.val();
        var url = "{{ route('admin.crm-leads.getSmsTemplate', ':id') }}";
        url = url.replace(':id', template_id);
        var token = "{{ csrf_token() }}";
        var job_type = $("#op_job_type").val();
        var job_id = "{{$job->job_id}}";
        //console.log(template_id);
        $.easyAjax({
            type: 'POST',
            url: url,
            data: {
                '_token': token,
                'id': template_id,
                'lead_id' : 0,
                'job_type' : job_type,
                'job_id' : job_id
            },
            success: function(response) {
                console.log(response);
                if (response.error == 0) {
                    $('#sms_message').val(response.sms_message);
                    countChar();
                    //console.log(response.body);
                }
            }
        });
    });
//end:: Activity SMS Template

//start:: Activity Email template
$(document).on('change', '#choose_email_template', function() {
        var $this = $(this);
        var template_id = $this.val();
        var url = "{{ route('admin.crm-leads.getEmailTemplate', ':id') }}";
        url = url.replace(':id', template_id);
        var token = "{{ csrf_token() }}";
        var job_id = "{{$job->job_id}}";
        var job_type = $("#op_job_type").val();
        var crm_opportunity_id = $("#crm_opportunity_id").val();        

        $.easyAjax({
            type: 'POST',
            url: url,
            data: {
                '_token': token,
                'id': template_id,
                'job_type' : job_type,
                'job_id' : job_id,
                'lead_id':0,
                'crm_opportunity_id':crm_opportunity_id
            },
            success: function(response) {
                console.log(response);
                if (response.error == 0) {
                    $('#email_subject').val(response.subject);
                    // $('#email_body').val(response.body);
                    console.log(response.body);
                    $("#email_body").summernote('code', response.body);
                    $('#email_attachment_div').html(response.attach_html);
                }else{
                    $.toast({
                        heading: 'Error',
                        text: response.msg,
                        icon: 'error',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#fb9678',
                        textColor: 'white'
                    });
                }
            }
        });
    });
    
//end:: Activity Email Template

    // $('.daterange-basic').daterangepicker({
    //         applyClass: 'btn-primary',
    //         cancelClass: 'btn-light'
    // });
    // $('.daterange-basic').datepicker({
    //     locale: {
    //         format: 'DD/MM/YYYY'
    //     }
    // });

    $('#save-form').click(function() {
        $.easyAjax({
            url: "{{route('admin.companies.store')}}",
            container: '#createCompany',
            type: "POST",
            redirect: true,
            file: (document.getElementById("image").files.length == 0) ? false : true,
            data: $('#createCompany').serialize()
        });
    });
});

 //# Summernote editor

var Summernote = function() {
var _componentSummernote = function() {
    if (!$().summernote) {
        console.warn('Warning - summernote.min.js is not loaded.');
        return;
    }
    $('.summernote').summernote({
        height: 200,
        toolbar: [
            ['font', ['bold', 'underline']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol']],
            ['insert', ['link', 'picture']],
            ['view', ['codeview','fullscreen']],
            ['misc', ['print']],
        ],
    });
};

// Uniform
var _componentUniform = function() {
    if (!$().uniform) {
        console.warn('Warning - uniform.min.js is not loaded.');
        return;
    }

    // Styled file input
    $('.note-image-input').uniform({
        fileButtonClass: 'action btn bg-warning-400'
    });
};

return {
    init: function() {
        _componentSummernote();
        _componentUniform();
    }
}
}();
document.addEventListener('DOMContentLoaded', function() {
Summernote.init();
});

$(window).load(function() {
    document.getElementsByTagName("html")[0].style.visibility = "visible";
});
</script>
@endpush