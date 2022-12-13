@extends('layouts.app')

@section('contact_grid')
@include('admin.crm-leads.contact_grid')
@endsection

@section('job_detail_grid')
@include('admin.list-jobs-cleaning.jobs.job_detail_grid')
@endsection

@section('activity_notes_grid')
@include('admin.crm-leads.activity_notes_grid')
@endsection

@section('additional_grid')
@include('admin.list-jobs-cleaning.jobs.additional_grid')
@endsection

@section('attachments_grid')
@include('admin.list-jobs-cleaning.jobs.attachments_grid')
@endsection

@section('team_grid')
@include('admin.list-jobs-cleaning.jobs.team_grid')
@endsection

@section('extras_grid')
@include('admin.list-jobs-cleaning.jobs.extras_grid')
@endsection

@section('invoice_grid')
@include('admin.list-jobs-cleaning.jobs.invoice_grid')
@endsection

@section('payment_grid')
@include('admin.list-jobs-cleaning.jobs.payment_grid')
@endsection

@section('page-title')
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
<input id="op_job_type" value="Cleaning" type="hidden"/>
<div class="row">
    <!-- START:: Left content -->
    <div class="col-12 col-lg-3 px-2">        
        <div class="text-left lead_title">
            <h4 class="font-weight-semibold mb-1 view_blade_1_person_name">{{ $lead_name }}<br/>
                <p style="font-weight: 500;font-size: 14px">Job # {{$job->job_number}}</p>
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
                            <label>Preferred Time</label>
                            <select name="preferred_time_range" class="form-control">
                                @foreach($cleaning_shifts as $data)
                                <option value="{{ $data->id }}" 
                                    @if($data->id == $job->preferred_time_range)
                                    selected=""
                                    @endif
                                    >{{ $data->shift_display_start_time }}</option>
                                @endforeach
                            </select>
                        </div>                    
            
                        <div class="form-group">
                            <label>Cleaning Address</label>
                            <textarea name="address" class="form-control">{{ $job->address}}</textarea>
                        </div> 
                        <div class="form-group">
                            <label>Bedrooms</label>
                            <input class="form-control" name="bedrooms" value="{{ $job->bedrooms }}" type="number"/>
                        </div>  
                        <div class="form-group">
                            <label>Bathrooms</label>
                            <input class="form-control" name="bathrooms" value="{{ $job->bathrooms }}" type="number"/>
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
                    <input name="job_type" value="Cleaning" type="hidden"/>
                    <div class="d-flex justify-content-start align-items-center m-t-10">
                        <button type="reset" class="btn btn-light show_update_job_detail_btn">Cancel</button>
                        <button type="button" id="update_job_detail_btn" class="btn bg-blue ml-3">Update</button>
                    </div>
            
                </form>
            </div>
            <div id="update_jo_detail_view">
                <div id="job_detail_grid" style="line-height: 2;">
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
                                    <span id="totalInvoiceAmount">{{ $global->currency_symbol }}{{ $totalAmount }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Payments:
                                </td>
                                <td>
                                    <span id="totalPaidAmount">{{ $global->currency_symbol }}{{ $paidAmount }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Balance:
                                </td>
                                <td>
                                    <span id="totalBalanceAmount">{{ $global->currency_symbol }}{{ $totalAmount-$paidAmount }}</span>
                                </td>
                            </tr>
                        </tbody>
                        </table>
                    </div>
                    <?php echo \App\Http\Controllers\Admin\ListJobsController::paymentStatus($totalAmount,$paidAmount);?>
                </div>
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
            <ul class="nav nav-tabs view_blade_5_navs_tabbs_box_shadow nobackground">
                <li class="nav-item noborder"><a href="#activity_tab" class="nav-link active view_blade_5_navs_tabbs_nav_item_link" data-toggle="tab">Activity</a></li>
                <li class="nav-item noborder"><a href="#operations_tab" class="nav-link view_blade_5_navs_tabbs_nav_item_link" data-toggle="tab">Operations</a></li>
                <li class="nav-item noborder"><a href="#invoice_tab" class="nav-link view_blade_5_navs_tabbs_nav_item_link" data-toggle="tab">Invoice</a></li>                
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="activity_tab">
                    <div class="row">
                    <div class="col-md-6 col-xs-6 text-left">
                        <button type="button" class="btn add_notes_btn rounded-round btn-bg-grey pr-3 mr-2"><b><i class="icon-pencil4 mr-2 icon-color-blue"></i></b> Note</button>

                        <button type="button" class="btn add_email_btn rounded-round btn-bg-grey pr-3 mr-2"><b><i class="icon-envelop mr-2 icon-color-blue"></i></b> Email</button>

                        <button type="button" class="btn add_sms_btn rounded-round btn-bg-grey pr-3"><b><i class="icon-bubble-dots3 mr-2 icon-color-blue"></i></b> SMS</button>
                    </div>
                    <div class="col-md-6 col-xs-6">
                        <div class="btn-group">
                                    <button type="button" class="btn btn-link dropdown-toggle dropdown-black" data-toggle="dropdown" aria-expanded="false">All Activities</button>
                                    <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(143px, 36px, 0px);">
                                        <a href="#" class="dropdown-item"><i class="icon-menu7"></i> Action</a>
                                    </div>
                        </div>

                        <div class="btn-group">
                                    <button type="button" class="btn btn-link dropdown-toggle dropdown-black" data-toggle="dropdown" aria-expanded="false">All Users</button>
                                    <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(143px, 36px, 0px);">
                                        <a href="#" class="dropdown-item"><i class="icon-menu7"></i> Action</a>
                                    </div>
                        </div>

                        <div class="btn-group">
                                    <button type="button" class="btn btn-link dropdown-toggle dropdown-black" data-toggle="dropdown" aria-expanded="false">All Contacts</button>
                                    <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(143px, 36px, 0px);">
                                        <a href="#" class="dropdown-item"><i class="icon-menu7"></i> Action</a>
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
                                            <div class="form-group">
                                                <textarea name="notes" rows="3" cols="3" class="form-control" placeholder="Add a note about this lead"></textarea>
                                            </div>
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
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">From</span>
                                                                </div>
                                                            <select id="act_email_from" name="from_email" class="form-control form-control-lg">
                                                                @foreach($company_list as $company)
                                                                    <option value="{{ $company->email }}">{{ $company->email }}</option>
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
                        <h3 class="col-lg-6" style="font-size: 20px;font-family: 'Poppins', sans-serif;">Team</h3>
                    </div>
                    <div id="team_table_grid" class="card" style="border: none!important">
                        @yield('team_grid')
                    </div>
                    <div class="form-group row mt-1">
                        <h3 class="col-lg-6" style="font-size: 20px;font-family: 'Poppins', sans-serif;">Extras</h3>
                    </div>
                    <div id="extras_table_grid" class="card" style="border: none!important">
                        @yield('extras_grid')
                    </div>
                    <div class="form-group row mt-1">
                        <h3 class="col-lg-6" style="font-size: 20px;font-family: 'Poppins', sans-serif;">Additional Info</h3>
                    </div>
                    <div id="additional_table_grid" class="card" style="border: none!important">
                        @yield('additional_grid')
                    </div>
                    <div class="form-group row mt-1">
                        <h3 class="col-lg-6" style="font-size: 20px;font-family: 'Poppins', sans-serif;">Attachments</h3>
                    </div>
                    <div id="attachments_grid" class="card" style="border: none!important">
                        @yield('attachments_grid')
                    </div>
                </div>
                <div class="tab-pane fade" id="invoice_tab" style="overflow: hidden;">
                    <button type="button" class="btn btn-light listJobInvoiceGenerate" data-jobid="{{$job_id}}" data-type="Cleaning"><i class="icon-clipboard3 ml-2"></i> Generate Invoice PDF</button>
                    <button type="button" class="btn btn-light ml-2 listJobInvoiceDownload" data-jobid="{{$job_id}}"><i class="icon-file-pdf ml-2"></i> Download</button><br/><br/>
                    @if(isset($invoice))
                    <div class="form-group row mt-1">
                        <h3 class="col-lg-2" style="font-weight: 500;font-size: 14px;">Invoice # {{ $invoice->invoice_number }}</h3>
                    </div>
                    @endif
                    <div id="invoice_table_grid" class="card" style="border: none!important">
                        @yield('invoice_grid')
                    </div>
                    <p class="job-label-txt job-status green-status">
                        PAYMENTS
                    </p>
                    @if(isset($invoice) && isset($stripe->account_key) && !empty($stripe->account_key))
                        <input type="hidden" id="invoice_id" value="{{ $invoice->id }}"/>
                        <input type="hidden" id="new_stripe_payment_amount"/>
                        <button id="payButton" type="button" class="btn btn-light inv-stripe-btn" data-invoiceid="{{ $invoice->id }}">Add Stripe Payment</button>
                    @endif
                    <div id="payment_table_grid" class="card" style="border: none!important;">
                        @yield('payment_grid')
                    </div>
                </div>
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
<script src="{{ asset('newassets/global_assets/js/plugins//forms/styling/uniform.min.js') }}"></script>
<script src="{{ asset('newassets/global_assets/js/plugins/pickers/daterangepicker.js') }}"></script>
<script src="{{ asset('newassets/global_assets/js/demo_pages/picker_date.js') }}"></script>
<script src="{{ asset('js/ajax-requests/ajax-requests-jobs.js') }}"></script>
<script src="{{ asset('js/ajax-requests/ajax-requests-invoice.js') }}"></script>
<script src="{{ asset('js/ajax-requests/ajax-requests-cleaning.js') }}"></script>
<script src="{{ asset('js/ajax-requests/ajax-requests-activity.js') }}"></script>
<script src="{{ asset('js/ajax-requests/ajax-requests-payments.js') }}"></script>
<script src="{{ asset('js/ajax-requests/ajax-requests-opperations-leg.js') }}"></script>
<script src="{{ asset('js/ajax-requests/ajax-requests-opperations-trip.js') }}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ $google_api_key }}&libraries=places"></script>
<script type="text/jscript" src="https://checkout.stripe.com/checkout.js"></script>
<script>
        
    var handler = StripeCheckout.configure({
        key: "{{ env('STRIPE_PUBLIC') }}",
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
                    data: {'_token':tkn,stripeToken: token.id, stripeEmail: token.email,
                    'invoice_id': invoice_id, 
                    'amount': amount,
                    'sys_job_type': "Cleaning"
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
            //--->
            var amount = parseFloat(prompt("Please enter amount to proceed:"));
            console.log(amount);
            if (amount>0) {     
                $('#new_stripe_payment_amount').val(amount);           
            }else{
                return false;
            }            
            $('#payButton').html('Please wait...');
            // Open Checkout with further options:
            handler.open({
                name: '{{ $organisation_settings->company_name }}',
                description: 'Stripe Payment',
                email: '{{ $crm_contact_email }}',
                amount: amount*100,
                closed:	stripe_closed
            });
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
        //end:: delete contact\
        
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
        var job_type = $("#op_job_type").val();
        var job_id = "{{$job->job_id}}";
        console.log(url);
        $.easyAjax({
            type: 'POST',
            url: url,
            data: {
                '_token': token,
                'id': template_id,
                'job_type' : job_type,
                'job_id' : job_id,
                'lead_id':0
            },
            success: function(response) {
                console.log(response);
                if (response.error == 0) {
                    $('#email_subject').val(response.subject);
                    // $('#email_body').val(response.body);
                    console.log(response.body);
                    $(".summernote").summernote('code', response.body);
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
            ['color', ['color']],
            ['para', ['ul', 'ol']],
            ['insert', ['link', 'picture']],
            ['view', ['codeview','fullscreen']],
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