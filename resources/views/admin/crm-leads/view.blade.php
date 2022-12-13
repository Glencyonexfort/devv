@extends('layouts.app')

@section('task_grid')
@include('admin.crm-leads.task_grid')
@endsection

@section('opportunity_grid')
@include('admin.crm-leads.opportunity_grid')
@endsection

@section('contact_grid')
@include('admin.crm-leads.contact_grid')
@endsection

@section('activity_notes_grid')
@include('admin.crm-leads.activity_notes_grid')
@endsection



@if($job_type == 'Moving' && isset($job->opportunity) && $job->opportunity == 'Y')
    @section('estimate_grid')
    @include('admin.crm-leads.estimate_grid')
    @endsection

    @section('inventory_grid')
    @include('admin.crm-leads.inventory_grid')
    @endsection
@endif

@section('page-title')
<p>'hello'</p>

<style>
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
</style>
<div class="page-header page-header-light view_blade_page_header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex view_blade_page_padding">
            <h4>
            <img class="view_blade_page_img_header" src="../../../../../newassets/img/lightbulb.png">
                <span class="view_blade_page_span_header">Opportunity </span>
        </div>
    </div>
    <!-- <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> @lang('app.menu.home')</a>
                <a href="content_page_header.html" class="breadcrumb-item">&nbsp;</a>
                <span class="breadcrumb-item active">{{ $pageTitle }}</span>
            </div>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div> -->
</div>
@endsection

@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/tagify-master/dist/tagify.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
    
@endpush

@section('content')

<div class="row">
    <div class="col-12 col-lg-3 px-2">        
        <div class="text-left lead_title">
            <h4 class="font-weight-semibold mb-1 view_blade_1_person_name"><a href="{{ route("admin.crm-leads.view-customer-leads", $crmlead->id) }}">{{ $crmlead->name }}</a><br/>
                <p>{{ $crmlead->lead_type }}</p>    
            </h4>    
                
        </div>

        {{-- <h4 id="lead_status_view" class="font-weight-semibold py-2 view_blade_4_card">
            @foreach($crmleadstatuses as $st)
                @if($st->lead_status == $crmlead->lead_status)
                    {{  $st->lead_status }}
                @endif
            @endforeach
            <a href="#" class="edit_lead_status_btn" style="float: right;color: #111"><i class="icon-pencil5"></i></a>
        </h4> --}}

        <div id="lead_status_form" class="view_blade_4_card card py-2 hidden">          
            <select class="view_blade_2_select" id="lead_status" class="form-control">
                @foreach($crmleadstatuses as $st)
                <option value="{{ $st->lead_status }}" {{ $st->lead_status == $crmlead->lead_status ? "selected" : "" }}>{{ $st->lead_status }}</option>
                @endforeach
            </select>
            <div class="d-flex justify-content-start align-items-center m-t-10 text-right" style="padding: 0 12px;">
                <button class="btn btn-light edit_lead_status_btn">Cancel</button>
                <button id="update_lead_status" type="button" class="btn bg-blue ml-3">Save</button>
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
                            <label>Indian Time</label>
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
            
        <div class="card view_blade_4_card">
            <span class="view_blade_4_card_span">
                <div class="card-header header-elements-inline view_blade_4_card_header">
                    <h6 class="card-title card-title-mg view_blade_4_card_task" >Opportunities <b id="OppCount">{{ $totalOpportunities }}</b></h6>
                    <div class="header-elements">
                        <div class="list-icons">
                            <span class="cursor-pointer add_new_opp_btn"><img src="{{ asset('newassets/img/icon-add.png') }}"></span>
                            {{-- <a class="list-icons-item" data-action="collapse"></a> --}}
                        </div>
                    </div>
                </div>
            </span>
            <div id="add_new_opp_form_grid" class="card-body light-blue-bg p10 hidden body_margin">
                <form id="add_new_opp_form" class="custom-form" action="#">
                        @csrf
                        {{ Form::hidden('lead_id', $lead_id) }}
                        <div class="form-group">
                            <label>Company</label>
                            <select name="company_id" class="form-control">
                                @foreach($companies_list as $data)
                                    <option value="{{ $data->id }}">{{ $data->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Job Type</label>
                            <select name="op_type" class="form-control op_job_type_field">
                                @foreach($op_type as $data)
                                    <option value="{{ $data->options }}">{{ $data->options }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="op_status" class="form-control">
                                @foreach($op_status as $data)
                                    <option value="{{ $data->pipeline_status }}">{{ $data->pipeline_status }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Estimated Job Date</label>
                            <div class="input-group">
                                <span class="input-group-prepend">
                                    <span class="input-group-text"><i class="icon-calendar52"></i></span>
                                </span>
                                <input name="est_job_date" type="text" class="form-control daterange-single" value="{{ date('d/m/Y') }}">
                            </div>
                        </div>

                        <div id="Moving_job_fields" class="all_job_fields">
                            <div class="form-group">
                                <label>Estimated Start Time</label>
                                <div class="input-group">
                                    <span class="input-group-prepend">
                                        <span class="input-group-text"><i class="icon-watch"></i></span>
                                    </span>                                
                                    <input name="job_start_time" type="time" class="form-control oppLeg_leg_start_time_new pickatime" placeholder="Estimated Start Time">
                                </div>
                            </div>
                        </div>

                        <div id="Cleaning_job_fields" class="all_job_fields hidden">
                            <div class="form-group">
                                <label>Estimated Time Range</label>
                                <select name="preferred_time_range" class="form-control">
                                    @if(count($cleaning_shifts)){
                                        @foreach($cleaning_shifts as $data)
                                            <option value="{{ $data->id }}">{{ $data->shift_display_start_time }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        {{-- <div class="form-group slidecontainer">
                            <label>Confidence</label><output id="confidenceOutputId" class="txt-green" style="float: right;margin-top: 10px;font-weight: 500">0</output>
                            <input id="confidenceInputId" name="confidence" type="range" value="0" class="slider" min="0" max="100" oninput="confidenceOutputId.value = confidenceInputId.value">
                        </div> 

                        <div class="form-group">
                            <label>Value</label>
                            <input name="value" type="text" class="form-control" placeholder="">
                        </div>

                        <div class="form-group">
                            <label>Frequency</label>
                            <select name="op_frequency" class="form-control">
                                @foreach($frequency as $data)
                                    <option value="{{ $data->list_option }}">{{ $data->list_option }}</option>
                                @endforeach
                            </select>
                        </div>

                        --}}

                        <div class="form-group">
                            <label>Contact</label>
                            <select name="contact_id" class="form-control">
                                @foreach($contacts as $data)
                                    <option value="{{ $data->id }}">{{ $data->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Users</label>
                            <select name="user_id" class="form-control">
                                @foreach($without_worker_users as $data)
                                    <option value="{{ $data->user_id }}"  
                                    @if($data->id == auth()->user()->id) selected="" @endif
                                    >{{ $data->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Lead info</label>
                            <select name="lead_info" class="form-control">
                                @foreach($lead_info as $data)
                                    <option value="{{ $data->list_option }}">{{ $data->list_option }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" rows="3" cols="3" class="form-control"></textarea>
                        </div>
                        <div class="d-flex justify-content-start align-items-center m-t-10">
                            <button type="reset" class="btn btn-light cancel_new_opp_btn">Cancel</button>
                            <button type="button" id="create_opp_btn" class="btn bg-blue ml-2">Save</button>
                        </div>
                </form>
            </div>
            <div id="opportunity_grid" style="padding-top:0px !important;" class="card-body p-t-10 p10 view_blade_4_card_body_oppurtunity">
                    @yield('opportunity_grid')
            </div>
        </div>

        <div class="card view_blade_4_card">
                <span class="view_blade_4_card_span">
                    <div class="card-header header-elements-inline view_blade_4_card_header">
                        <h6 class="card-title card-title-mg view_blade_4_card_task">Contacts <b id="contactsCount">{{ $totalContacts }}</b></h6>
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
                                    <select name="contact_detail_type[]" class="form-control form-control-uniform">
                                        @foreach($contact_types as $data)
                                            <option value="{{ $data->list_option }}">{{ $data->list_option }}</option>
                                        @endforeach
                                    </select>
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

    <!-- Right content -->
    <div class="col-12 col-lg-9 px-2 pt-2">   
            <ul class="nav nav-tabs view_blade_5_navs_tabbs_box_shadow nobackground" id="submenu">
                <li class="nav-item noborder"><a href="#activity_tab" class="nav-link view_blade_5_navs_tabbs_nav_item_link" data-toggle="tab">Activity</a></li>

                @if($job_type == 'Moving' && isset($job->opportunity) && $job->opportunity == 'Y')
                    <li class="nav-item noborder"><a href="#removal_tab" class="nav-link view_blade_5_navs_tabbs_nav_item_link" data-toggle="tab">Removals</a></li>
                @elseif($job_type == 'Cleaning' && isset($job->opportunity) && $job->opportunity == 'Y')
                    <li class="nav-item noborder"><a href="#cleaning_tab" class="nav-link view_blade_5_navs_tabbs_nav_item_link" data-toggle="tab">Cleaning</a></li>
                @endif
                @if($job_type == 'Moving' && isset($job->opportunity) && $job->opportunity == 'Y')
                    <li class="nav-item noborder"><a href="#inventory_tab" class="nav-link view_blade_5_navs_tabbs_nav_item_link" data-toggle="tab">Inventory</a></li>
                    <li class="nav-item noborder"><a id="storage_tab_btn" href="#storage_tab" class="nav-link view_blade_5_navs_tabbs_nav_item_link" data-toggle="tab">Storage</a></li>
                    <li class="nav-item noborder"><a href="#estimate_tab" class="nav-link view_blade_5_navs_tabbs_nav_item_link" data-toggle="tab">Estimate</a></li>
                @endif
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
                                                    <a class="add_notes_btn cursor-pointer list-icons-item mr-2" title="Cancel"><i class="icon-close2"></i></a>
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
                                                <div class="row">
                                                    @if($job)
                                                    <input type="hidden" name="job_id" id="job_id" value="{{ $job->job_id }}">
                                                    @endif
                                                    <input type="hidden" name="from_name" id="act_email_from_name" value="{{ $removal_companies->first()->contact_name }}">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><b>From</b></span>
                                                                </div>
                                                            <select id="act_email_from" name="from_email" class="form-control form-control-lg">
                                                                @foreach($removal_companies as $company)
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
                                                                    <span class="input-group-text"><b>To</b></span>
                                                                </div>
                                                                <input type="text" id="act_email_to" name="to" data-input="to" class="search_email form-control" value="{{ $lead_email }}">                                                                                                                         
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
                                                <a class="add_email_btn cursor-pointer list-icons-item mr-2" title="Cancel"><i class="icon-close2"></i></a>
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
                    <!-- /timeline -->

                </div>
                <div class="tab-pane fade" id="removal_tab">
                    <input id="op_job_type" value="Moving" type="hidden"/>
                    @if($job_type == 'Moving' && isset($job->opportunity) && $job->opportunity == 'Y')                                        
                    <div class="form-group row mt-1">
                        <label class="col-lg-2 col-form-label">Opportunity # </label>
                        <div class="col-lg-4">
                            <select class="form-control" id="removal_opp_id">
                                @if(count($opportunity_jobs)>0)
                                    @foreach($opportunity_jobs as $opp)
                                    <option value="{{ $opp->id }}" data-type="{{ $opp->op_type }}" @if($opp->id == $current_opportunity_id) selected="" @endif>
                                        {{ $opp->op_type.' '.$opp->job_number}}
                                    </option>
                                    @endforeach
                                @else
                                    <option></option>
                                @endif
                            </select>
                        </div>
                        @if($removal_opportunities)
                        <div class="col-lg-6 textalign-right">
                            <button type="button" class="btn btn-booking removal-confirm-booking"
                                data-token="{{ csrf_token() }}"  data-leadid="{{ $removal_opportunities->lead_id }}">Confirm Booking<img class="icon-booking" src="../../../../../newassets/img/Icon map-location-arrow@2x.png"></b>
                            </button>
                        </div>
                        @endif
                    </div>
                    <div id="removal_table_grid">
                        @if($removal_opportunities)
                            @include('admin.crm-leads.removals.index')
                        @endif
                    </div>
                    @endif
                </div>
                <div class="tab-pane fade" id="cleaning_tab">
                    @if($job_type == 'Cleaning' && isset($job->opportunity) && $job->opportunity == 'Y')
                    <input id="op_job_type" value="Cleaning" type="hidden"/>                                    
                    <div class="form-group row mt-1">
                        <label class="col-lg-2 col-form-label">Opportunity # </label>
                        <div class="col-lg-4">
                            <select class="form-control" id="cleaning_opp_id">
                            @foreach($opportunity_jobs as $opp)
                            <option value="{{ $opp->id }}" data-type="{{ $opp->op_type }}" @if($opp->id == $current_opportunity_id) selected="" @endif>{{ $opp->op_type.' '.$opp->job_number}}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6 textalign-right">
                            <button type="button" class="btn btn-booking removal-confirm-booking"
                                data-token="{{ csrf_token() }}" data-leadid="{{ $removal_opportunities->lead_id }}">Confirm Booking<img class="icon-booking" src="../../../../../newassets/img/Icon map-location-arrow@2x.png"></b>
                            </button>
                        </div>
                    </div>
                    <div id="cleaning_table_grid">
                        @include('admin.crm-leads.cleaning.index')
                    </div>
                    @endif
                </div>
                <div class="tab-pane fade" id="inventory_tab">
                    @if($job_type == 'Moving' && isset($job->opportunity) && $job->opportunity == 'Y')
                        @yield('inventory_grid')
                    @endif
                </div>
                <div class="tab-pane fade" id="storage_tab">
                    <div id="storage_reservation"></div>
                    <div id="storage_estimate"></div>
                </div>
                @if($job_type == 'Moving' && isset($job->opportunity) && $job->opportunity == 'Y')
                <div class="tab-pane fade" id="estimate_tab" style="overflow: hidden;">
                    <div class="row">
                        <div class="col-lg-6">
                            <button type="button" class="btn btn-light leadEstimateGenerateQuote"><i class="icon-clipboard3 ml-2"></i> Generate Estimate PDF</button>
                        <button type="button" class="btn btn-light ml-2 leadEstimateDownloadQuote"><i class="icon-file-pdf ml-2"></i> Download</button><br/>
                        </div>
                        <div class="col-lg-6" style="text-align: right">
                            <button type="button" class="btn btn-light leadEstimateGenerateInsurance" @if ($coverFreight_connected==false) disabled @endif><i class="icon-clipboard3 ml-2"></i> Generate Insurance Quote</button>
                            <button type="button" class="btn btn-light ml-2 leadEstimateDownloadInsurance" @if (empty($job) || $job->insurance_file_name == null) disabled @endif><i class="icon-file-pdf ml-2"></i> Download</button><br/>
                        </div>
                    </div>
                    <div class="form-group row mt-1">
                        <label class="col-lg-2 col-form-label">Opportunity # </label>
                        <div class="col-lg-4">
                            <select class="form-control" id="estimate_opp_id">
                                @foreach($opportunity_jobs as $opp)
                                    <option value="{{ $opp->id }}" data-type="{{ $opp->op_type }}" @if($opp->id == $current_opportunity_id) selected="" @endif>
                                        {{ $opp->op_type.' '.$opp->job_number}}
                                    </option>
                                @endforeach
                            </select>
                    </div>
                    </div>
                    <div id="estimate_table_grid" class="card" style="border: none!important;padding-bottom: 4rem;">
                        @yield('estimate_grid')
                    </div>
                </div>
                @endif
            </div>
        </div>
</div>
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
                <input id="ac_email_type_value" type="hidden" value="0"/>
            <div class="modal-body">
                    <div class="form-body">                        
                        <div class="row">
                            <div class="col-md-12">
                                    <div class="form-group files">
                                        <label>Upload Your File </label>
                                        <input id="activity_attachment" type="file" class="form-control" name="attachment">
                                        <input id="activity_attachment_type" type="hidden" name="type">
                                        {{ Form::hidden('lead_id', $lead_id) }}
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
<script src="{{ asset('plugins/tagify-master/dist/tagify.js') }}"></script> <!-- Theme JS files -->
{{-- <script src="{{ asset('newassets/global_assets/js/plugins/editors/ckeditor/ckeditor.j') }}s"></script> --}}
<script src="{{ asset('newassets/global_assets/js/plugins/forms/selects/select2.min.js') }}"></script>

{{-- <script src="{{ asset('newassets/global_assets/js/demo_pages/editor_ckeditor_default.js') }}"></script> --}}

<script src="{{ asset('newassets/global_assets/js/plugins/editors/summernote/summernote.min.js') }}"></script>
<script src="{{ asset('js/summernote-ext-print.js') }}"></script>
	<script src="{{ asset('newassets/global_assets/js/plugins//forms/styling/uniform.min.js') }}"></script>    
    <script src="{{ asset('newassets/global_assets/js/demo_pages/picker_date.js') }}"></script>
    <script src="{{ asset('js/ajax-requests/ajax-requests.js?v=5') }}"></script>
    <script src="{{ asset('js/ajax-requests/ajax-requests-storage.js') }}"></script>    
    <script src="{{ asset('js/ajax-requests/ajax-requests-activity.js?v=5') }}"></script>   
    <script src="{{ asset('js/ajax-requests/ajax-requests-jobs.js?v=1') }}"></script>      
    {{-- <script src="https://maps.googleapis.com/maps/api/js?key={{ $google_api_key }}&libraries=places"></script> --}}
    <script type="text/javascript">
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
            // var scrollmem = $('html,body').scrollTop();
            window.location.hash = id;
            $('html,body').scrollTop(0);
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

    function initialize1() {
            var options = {
                types: ['(cities)'],
                componentRestrictions: {
                    country: "au"
                }
            };
            var allDepotInputs = document.getElementsByClassName('region_suburb_name');
            var autocompletes = [];
            for (var i = 0; i < allDepotInputs.length; i++) {
                //console.log(allDepotInputs[i]);
                var autocomplete = new google.maps.places.Autocomplete(allDepotInputs[i], options);
                autocomplete.inputId = allDepotInputs[i].id;
                autocomplete.addListener('place_changed', fillInFields1);                
                autocompletes.push(autocomplete);
            }        
    
        }

        function fillInFields1() {
            var elemId = this.inputId;
            var place = this.getPlace();
            var postal_code = '';
            var suburb = '';
            //Set Postcode field
            console.log(place.address_components);
            $.each(place.address_components, function( key, value ) {
                if(value.types[0]=="locality" || value.types[0]=="colloquial_area"){
                    suburb=value.long_name;
                }
                if(value.types[0]=="administrative_area_level_1"){
                    suburb=suburb+' '+value.short_name;
                }
                if(value.types[0]=="postal_code"){
                    postal_code=value.long_name;
                }
            });
            if(elemId=="region_suburb_name_from"){
                $("#region_suburb_name_from").val(suburb);
                $("#postcode_from").val(postal_code);
            }else if(elemId=="region_suburb_name_to"){
                $("#region_suburb_name_to").val(suburb);
                $("#postcode_to").val(postal_code);
            }
        }        

// Address autocomplete field----//

        function initialize() {
            var options = {
                fields: ["address_components", "geometry"],
                types: ['address'],
                componentRestrictions: {
                    country: "au"
                }
            };
            var allDepotInputs = document.getElementsByClassName('region_suburb_addressname');
            var autocompletes = [];
            for (var i = 0; i < allDepotInputs.length; i++) {
                //console.log(allDepotInputs[i]);
                var autocomplete = new google.maps.places.Autocomplete(allDepotInputs[i], options);
                autocomplete.inputId = allDepotInputs[i].id;
                autocomplete.addListener('place_changed', fillInFields);                
                autocompletes.push(autocomplete);
            }        
    
        }
        function fillInFields() {
            var elemId = this.inputId;
            var place = this.getPlace();
            var address = '';
            var suburb = '';
            var postal_code = '';
            // console.log(place.address_components);
            //Set Postcode field
            $.each(place.address_components, function( key, value ) {
                if(value.types[0]=="subpremise"){
                    address=address+' '+value.long_name+'/';
                }
                if(value.types[0]=="street_number"){
                    address=address+value.long_name;
                }
                if(value.types[0]=="route"){
                    address=address+' '+value.long_name;
                }
                if(value.types[0]=="locality"){
                    suburb=value.long_name;
                }
                if(value.types[0]=="administrative_area_level_1"){
                    suburb=suburb+' '+value.short_name;
                }
                if(value.types[0]=="postal_code"){
                    postal_code=value.long_name;
                }
            });
            if(elemId=="region_address_from"){
                $("#region_address_from").val(address);
                $("#region_suburb_name_from").val(suburb);
                $("#postcode_from").val(postal_code);
            }else if(elemId=="region_address_to"){
                $("#region_address_to").val(address);
                $("#region_suburb_name_to").val(suburb);
                $("#postcode_to").val(postal_code);
            }
        }
    
        google.maps.event.addDomListener(window, 'load', initialize1);
        google.maps.event.addDomListener(window, 'load', initialize);    
        document.addEventListener('DOMNodeInserted', function(event) {
            // console.log(event);
    
        });
    
        function removeCountryName(id) {
            setTimeout(function() {
                    var newval = $("#region_address_"+id).val().replace(', Australia', '');
                    $("#region_address_"+id).val(newval);
            }, 5);
        }
    
    </script>   
<script>
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
    // Show or Hide form
    $('.edit_lead_status_btn').on('click', function(e){
        $("#lead_status_view").toggle();
        $("#lead_status_form").toggle();
    });
    $('.add_new_task_btn').click(function() {
        $("#add_new_task_form_grid").toggle(200);
    });
    $('.add_new_opp_btn').click(function() {
        swal({
            title: "Are you sure?",
            text: "You want to create a NEW opportunity?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#26a69a",
            confirmButtonText: "Yes!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
                $("#add_new_opp_form_grid").toggle(200);
            }
        });
    });
    $('.cancel_new_opp_btn').click(function() {
        $("#add_new_opp_form_grid").toggle(200);
    });
    $('#op_confidence').on('change', function(e){
        $('#op_confidence_label').html($(this).val());
    });
    $('.op_job_type_field').on('change', function(e){
        $(".all_job_fields").hide();
        $("#"+$(this).val()+"_job_fields").show();
    });
    
    
    //----
    //(... rest of your JS code)
    $(document).on('click', '#update_lead_status', function() {
        var status = $("#lead_status").find(":selected").val();
        var url = "{{ route('admin.crm-leads.ajaxUpdateLeadStatus') }}";
        var token = "{{ csrf_token() }}";
        
        $.easyAjax({
            type: 'POST',
            url: url,
            data: {
                '_token': token,
                'id': {{ $crmlead->id }},
                'status': status
            },
            success: function(response) {
                if (response.status == "success") {
                    // $("#lead_status_view").toggle();
                    // $("#lead_status_form").toggle();
                    $.toast({
                        heading: 'Updated',
                        text: 'Lead status has been updated',
                        icon: 'success',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#00c292',
                        textColor: 'white'
                    });
                    location.reload();
                }
            }
        });
    });
    //Delete Task
    $('body').on('click', '.task-remove-btn', function () {
        var id = $(this).data('taskid');
        var leadid = $(this).data('leadid');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted task!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
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

//Delete Opportunity
$('body').on('click', '.opportunity-remove-btn', function () {
        var id = $(this).data('oppid');
        var leadid = $(this).data('leadid');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted opportunity!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
                // var url = "{{ route('admin.clients.destroy',':id') }}";
                // url = url.replace(':id', id);
                var token = "{{ csrf_token() }}";
                $.ajax({
            url: "/admin/crm/crm-leads/ajaxDestroyOpportunity",
            method: 'post',
            data: {'_token': token, 'opp_id': id,'lead_id':leadid},
            dataType: "json",
            beforeSend: function () {
                $.blockUI();
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (result) 
            {
                console.log(result)
                if(result.error==2){
                    swal({
                        title: "Info",
                        text: result.message,
                        type: "info",
                        button: "OK",
                    });
                }else if (result.error == 0) {
                //     $('#opportunity_grid').html(result.opp_html);
                //     $("#OppCount").html(result.opp_count);
                //     $('.daterange-single').daterangepicker({ 
                //     singleDatePicker: true,
                //     locale: {
                //         format: 'DD/MM/YYYY'
                //     }
                // });
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
                    location.reload();
                }
            }
        });
            }
        });
    });
//end:: delete Opportunity

//Delete contact
$('body').on('click', '.contact-remove-btn', function () {
        var id = $(this).data('contactid');
        var leadid = $(this).data('leadid');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted contact!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
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
//end:: delete contact

//Delete Activity note
$('body').on('click', '.actNote-remove-btn', function () {
        var id = $(this).data('id');
        var leadid = $(this).data('leadid');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted note!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
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
        var lead_id = "{{$lead_id}}";
        var job_type = $("#op_job_type").val();
        var job_id = $("#op_job_id").val();
        //console.log(template_id);
        $.easyAjax({
            type: 'POST',
            url: url,
            data: {
                '_token': token,
                'id': template_id,
                'lead_id' : lead_id,
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
        var lead_id = "{{$lead_id}}";
        // var job_type = $("#op_job_type").val();
        var job_id = $("#op_job_id").val();
        var crm_opportunity_id = $('#estimate_opp_id').find(":selected").val();
        var job_type = $('#estimate_opp_id').find(":selected").data('type');
        //console.log(url);
        $.easyAjax({
            type: 'POST',
            url: url,
            data: {
                '_token': token,
                'id': template_id,
                'lead_id' : lead_id,
                'job_id' : job_id,
                'job_type' : job_type,
                'crm_opportunity_id' : crm_opportunity_id
            },
            success: function(response) {
                if (response.error == 0) {
                    $('#email_subject').val(response.subject);
                    // $('#email_body').val(response.body);
                    $("#email_body").summernote('code', response.body);
                    $('#email_attachment_div').html(response.attach_html);
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
</script>
@endpush