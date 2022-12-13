@if(count($notes)>0)
@foreach($notes as $note)
<?php
    $attachments = \App\CRMActivityLogAttachment::where(['log_id'=>$note->id])->get();
    $user_firstname = '';
    $last_lastname = '';
    if($note->user_id!=0){
    $ppl_user = \App\PplPeople::where('user_id',$note->user_id)->first();
            //$name = explode(" ", $ppl_user->name, 2);
            if($ppl_user){
                $user_firstname = $ppl_user->first_name;
                $last_lastname = $ppl_user->last_name;
            }
    }
    $activity_date = date("Y-m-d h:i A",strtotime($note->log_date));
?>
@if($note->log_type==7)
  <div class="timeline-row">
    <div class="timeline-icon activity_notes_grid_3_background">
        <i class="icon-pencil4 timelineicon"></i>
    </div>
    <div id="act_note_view_div_{{ $note->id }}" class="card">
        <div class="card-header header-elements-inline activity_notes_grid_border">
            <h5 class="card-title">Notes</h5>
            <div class="header-elements">
                <div class="list-icons">
                    <a class="list-icons-item" data-action="collapse"></a>
                </div>
            </div>
        </div>
        <div class="card-body">
                    <p id="act_note_message_{{ $note->id }}">{!! $note->log_message !!}</p>
                    <span style="display: inline-grid;">
                    @if(count($attachments)>0)
                            @foreach ($attachments as $attachment) 
                                <a href="\admin\crm\crm-leads\viewActivityAttachment\{{ $attachment->id}}" target="_blank" class="badge-light badge-striped badge-striped-left border-left-info mb-2" style="color:#2196f3">
                                    {{ $attachment->attachment_type }}
                                </a>
                                @endforeach
                    @endif
                    </span>
                    <br><br/>
                    <i style="color: #999">Note Written {{ \Carbon\Carbon::parse($note->log_date)->diffForHumans() }} {{ ($user_firstname!='' && $last_lastname!='')? ' by '.$user_firstname.' '.$last_lastname:'' }} {{ " On ".$activity_date }}</i><br/>
                    <button type="button" class="btn btn-outline-primary btn-sm edit_act_note_btn" data-id="{{ $note->id }}">Edit <i class="icon-pencil5 ml-2"></i></button>
        </div>
    </div>
    <div id="act_note_form_div_{{ $note->id }}" class="card hidden">
    <div class="card-body">
        <form id="act_note_form_{{ $note->id }}" action="#">
            @csrf
            {{ Form::hidden('id', $note->id) }}
            {{ Form::hidden('lead_id', $note->lead_id) }}
        <div class="form-group">
            <textarea name="notes" class="summernote">{{ $note->log_message }}</textarea>
        </div>
        <div class="text-right">
            <div class="list-icons">
                <a class="actNote-remove-btn cursor-pointer list-icons-item mr-2" data-id="{{ $note->id }}" data-leadid="{{ $note->lead_id }}"><i class="icon-bin"></i></a>
                <a class="edit_act_note_btn cursor-pointer btn btn-light mr-2" data-id="{{ $note->id }}">Cancel</a>
            </div>
            <button data-id="{{ $note->id }}" type="button" class="update_act_note_btn btn bg-teal-400">Update<i class="icon-checkmark2 ml-2"></i></button>
        </div>
    </form>
    </div>
</div>
</div>
@elseif($note->log_type==8)
<div class="timeline-row">
    <div class="timeline-icon activity_notes_grid_3_background">
        <i class="icon-bubble-dots3 timelineicon"></i>
    
    </div>
    <div class="card">
        <div class="card-header header-elements-inline activity_notes_grid_border">
            <h5 class="card-title">SMS</h5>
            <div class="header-elements">
                <div class="list-icons">
                    <a class="list-icons-item" data-action="collapse"></a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <p>From: {{ $note->log_from }}<br>
                <b>To:</b>{{ $note->log_to }}</p>
                    <p>{{$note->log_message}}</p>
                    <br>
                    <i style="color: #999">SMS Sent {{ \Carbon\Carbon::parse($note->log_date)->diffForHumans() }} {{ ($user_firstname!='' && $last_lastname!='')? ' by '.$user_firstname.' '.$last_lastname:'' }} {{ " On ".$activity_date }}</i><br/>
        </div>
    </div>
</div>
@elseif($note->log_type==3)
<div  id="reply_email_form_{{ $note->id }}" class="timeline-row hidden">
    <div class="timeline-icon activity_notes_grid_3_background">
        <i class="icon-envelop timelineicon"></i>
    </div>
    <div class="card">
        <div class="card-body">
            <form id="activity_email_reply_form_{{ $note->id }}" action="#">
                @csrf
                {{ Form::hidden('lead_id', $lead_id) }}
                <input type="hidden" id="ac_email_type_value" value="0">
                <input id="is_reply" name="is_reply" type="hidden" value="{{ auth()->user()->email }}"/>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><b>From</b></span>
                                </div>
                            <select id="" name="from" class="form-control form-control-lg">
                                @foreach($companies_list as $company)
                                    <option value="{{ $company->email }}">{{ $company->email }}</option>
                                    @endforeach
                            </select>
                            </div>
                        </div>
                        <div class="form-group">                                    
                            <a class="cursor-pointer add_email_reply_cc" style="margin:6px 10px" data-key="{{ $note->id }}">Add CC</a> 
                            <a class="cursor-pointer add_email_reply_bcc" style="margin:6px 10px" data-key="{{ $note->id }}">Add BCC</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend" style="width: 100%;">
                                    <span class="input-group-text"><b>To</b></span>
                                    <input type="text" id="act_email_reply_to_{{ $note->id }}" name="to" data-input="to" class="search_email form-control" value="{{ $note->log_from }}"> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        
                <div id="add_email_reply_cc_box_{{ $note->id }}" class="row hidden">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">CC</span>
                                </div>
                                <input type="text" name="cc" data-input="cc" class="search_email form-control" value=""> 
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="note_id" value="{{ $note->id }}">
                <div id="add_email_reply_bcc_box_{{ $note->id }}" class="row hidden">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">BCC</span>
                                </div>
                                <input type="text" name="bcc" data-input="bcc" class="search_email form-control" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <input class="form-control form-control-lg" id="email_reply_subject_{{ $note->id }}" type="text" name="email_subject" placeholder="Subject" value="{{ "Re: ".$note->log_subject }}">
                        </div>
                    </div>
                </div>
                <textarea name="email_body" id="email_reply_body_{{ $note->id }}" class="summernote">
                    <p>
                        <br/><br/><br/>
                        {{ $ppl_people->email_signature }}
                    </p>
                    <hr/>
                    <p>
                        <b>From: </b>{{ $note->log_from }}<br/>
                        <b>Sent: </b><?php echo date('l, F d, Y h:i:s A', strtotime($note->log_date)) ?><br/>
                        <b>To: </b>{{ $note->log_to }}<br/>
                        <b>Subject: </b>{{ $note->log_subject }}                
                    </p>
                    <br/>
                    {!! $note->log_message !!}
                </textarea>
                <div id="email_reply_attachment_div_{{ $note->id }}" class="text-left" style="float: left;">

                </div>
                <div class="text-right" style="margin-top: 10px;">
                    <div class="list-icons">
                        <a class="cursor-pointer list-icons-item mr-lg-2" title="Attachment"  data-type="note" data-toggle="modal" data-target="#add_attachment_popup" onclick="addEmailReplyAttachmentPopup({{ $note->id }})"><i class="icon-attachment" style="font-size: 16px;font-weight: bold;margin-right: 15px;"></i></a>
                        <a class="cancel_email_reply cursor-pointer list-icons-item mr-2" title="Cancel" data-key="{{ $note->id }}"><i class="icon-close2"></i></a>
                    </div>
                    <button type="button" class="send_email_reply_btn btn bg-teal-400" data-key="{{ $note->id }}">Send<i class="icon-paperplane ml-2"></i></button>
                </div> 
        </form>                                       
    </div>
    </div>
</div>
<div  id="forward_email_form_{{ $note->id }}" class="timeline-row hidden">
    <div class="timeline-icon activity_notes_grid_3_background">
        <i class="icon-envelop timelineicon"></i>
    </div>
    <div class="card">
        <div class="card-body">
            <form id="activity_email_forward_form_{{ $note->id }}" action="#">
                @csrf
                {{ Form::hidden('lead_id', $lead_id) }}
                <input id="is_reply" name="is_reply" type="hidden" value=""/>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><b>From</b></span>
                                </div>
                            <select id="" name="from_email" class="form-control form-control-lg">
                                @foreach($companies_list as $company)
                                    <option value="{{ $company->email }}">{{ $company->email }}</option>
                                    @endforeach
                            </select>
                            </div>
                        </div>
                        <div class="form-group">                                    
                            <a class="cursor-pointer add_email_forward_cc" style="margin:6px 10px" data-key="{{ $note->id }}">Add CC</a> 
                            <a class="cursor-pointer add_email_forward_bcc" style="margin:6px 10px" data-key="{{ $note->id }}">Add BCC</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend" style="width: 100%;">
                                    <span class="input-group-text"><b>To</b></span>
                                    <input type="text" id="act_email_forward_to_{{ $note->id }}" name="to" data-input="to" class="search_email form-control" value=""> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        
                <div id="add_email_forward_cc_box_{{ $note->id }}" class="row hidden">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">CC</span>
                                </div>
                                <input type="text" name="cc" data-input="cc" class="search_email form-control" value=""> 
                            </div>
                        </div>
                    </div>
                </div>
                <div id="add_email_forward_bcc_box_{{ $note->id }}" class="row hidden">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">BCC</span>
                                </div>
                                <input type="text" name="bcc" data-input="bcc" class="search_email form-control" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <input class="form-control form-control-lg" id="email_forward_subject_{{ $note->id }}" type="text" name="email_subject" placeholder="Subject" value="{{ "Fw: ".$note->log_subject }}">
                        </div>
                    </div>
                </div>
                <textarea name="email_body" id="email_forward_body_{{ $note->id }}" class="summernote">
                    <p>
                        <br/><br/><br/>
                        {{ $ppl_people->email_signature }}
                    </p>
                    <hr/>
                    <p>
                        <b>From: </b>{{ $note->log_from }}<br/>
                        <b>Sent: </b><?php echo date('l, F d, Y h:i:s A', strtotime($note->log_date)) ?><br/>
                        <b>To: </b>{{ $note->log_to }}<br/>
                        <b>Subject: </b>{{ $note->log_subject }}                
                    </p>
                    <br/>
                    {!! $note->log_message !!}
                </textarea>
                <div id="email_forward_attachment_div_{{ $note->id }}" class="text-left" style="float: left;">

                </div>
                <div class="text-right" style="margin-top: 10px;">
                    <div class="list-icons">
                        <a class="cursor-pointer list-icons-item mr-lg-2" title="Attachment"  data-type="note" data-toggle="modal" data-target="#add_attachment_popup" onclick="addEmailReplyAttachmentPopup({{ $note->id }})"><i class="icon-attachment" style="font-size: 16px;font-weight: bold;margin-right: 15px;"></i></a>
                        <a class="cancel_email_reply cursor-pointer list-icons-item mr-2" title="Cancel" data-key="{{ $note->id }}" data-action="forward"><i class="icon-close2"></i></a>
                    </div>
                    <button type="button" class="send_email_forward_btn btn bg-teal-400" data-key="{{ $note->id }}">Send<i class="icon-paperplane ml-2"></i></button>
                </div> 
        </form>                                       
    </div>
    </div>
</div>
<div class="timeline-row" id="email_view_{{ $note->id }}">
    <div class="timeline-icon activity_notes_grid_3_background">
        <i class="icon-envelop timelineicon"></i>
    </div>
    <div class="card">
        <div class="card-header header-elements-inline activity_notes_grid_border">
            <h5 class="card-title">{{ $note->log_subject }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    <i class="icon-undo mr-3 icon-2x cursor-pointer email_reply_btn" style="font-size: 18px;" data-key="{{ $note->id }}" data-action="reply" title="Reply"></i> 
                    <i class="icon-arrow-right14 mr-3 icon-2x cursor-pointer email_reply_btn" style="font-size: 26px;" data-key="{{ $note->id }}" data-action="forward" title="Forward"></i> 
                    <a class="list-icons-item" data-action="collapse" title="Collapse"></a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <p><b>From:</b> {{ $note->log_from }} <br>
                <b>To:</b>{{ $note->log_to }} <br>
                @if ($note->log_cc)
                    <b>CC:</b>{{ $note->log_cc }} <br>
                @endif
                @if ($note->log_bcc)
                    <b>BCC:</b>{{ $note->log_bcc }} <br>
                @endif
            </p>
                    {!! $note->log_message !!}
                    <span style="display: inline-grid;">
                        @if(count($attachments)>0)
                                @foreach ($attachments as $attachment) 
                                    <a href="\admin\crm\crm-leads\viewActivityAttachment\{{ $attachment->id}}" target="_blank" class="badge-light badge-striped badge-striped-left border-left-info mb-2" style="color:#2196f3">
                                        {{ $attachment->attachment_type }}
                                    </a>
                                    @endforeach
                        @endif
                        </span>
                        <br/>
                    <br>
                    <i style="color: #999">Email Sent {{ \Carbon\Carbon::parse($note->log_date)->diffForHumans() }} {{ ($user_firstname!='' && $last_lastname!='')? ' by '.$user_firstname.' '.$last_lastname:'' }} {{ " On ".$activity_date }}</i><br/>
        </div>
    </div>
</div>
@elseif($note->log_type==5)
<?php
    $log_message_detail  = $note->beautifyEmailContent($note->log_message);
?>
<div  id="reply_email_form_{{ $note->id }}" class="timeline-row hidden">
    <div class="timeline-icon activity_notes_grid_3_background">
    <i class="icon-envelop timelineicon"></i>
    </div>
    <div class="card">
        <div class="card-body">
            <form id="activity_email_reply_form_{{ $note->id }}" action="#">
                @csrf
                {{ Form::hidden('lead_id', $lead_id) }}
                <input id="is_reply" name="is_reply" type="hidden" value="{{ auth()->user()->email }}"/>
                <div class="row">
                <div class="col-md-6">
                <div class="form-group">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><b>From</b></span>
                                                                </div>
                                                            <select id="" name="from" class="form-control form-control-lg">
                                                                @foreach($companies_list as $company)
                                                                    <option value="{{ $company->email }}">{{ $company->email }}</option>
                                                                    @endforeach
                                                            </select>
                                                            </div>
                                                        </div>
            <div class="form-group">                                    
                    <a class="cursor-pointer add_email_reply_cc" style="margin:6px 10px" data-key="{{ $note->id }}">Add CC</a> 
                    <a class="cursor-pointer add_email_reply_bcc" style="margin:6px 10px" data-key="{{ $note->id }}">Add BCC</a>
            </div>
                </div>
            <div class="col-md-6">
                <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend" style="width: 100%;">
                        <span class="input-group-text"><b>To</b></span>
                        <input type="text" id="act_email_reply_to_{{ $note->id }}" name="to" data-input="to" class="search_email form-control" value="{{ $note->log_from }}"> 
                    </div>
                </div>
                </div>
            </div>
                </div>
        
        <div id="add_email_reply_cc_box_{{ $note->id }}" class="row hidden">
            <div class="col-md-6">
                <div class="form-group">
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text">CC</span>
                        </div>
                        <input type="text" name="cc" data-input="cc" class="search_email form-control" value=""> 
                    </div>
                </div>
            </div>
        </div>
        <div id="add_email_reply_bcc_box_{{ $note->id }}" class="row hidden">
            <div class="col-md-6">
                <div class="form-group">
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text">BCC</span>
                        </div>
                        <input type="text" name="bcc" data-input="bcc" class="search_email form-control" value="">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <input class="form-control form-control-lg" id="email_reply_subject_{{ $note->id }}" type="text" name="email_subject" placeholder="Subject" value="{{ "Re: ".$note->log_subject }}">
                </div>
            </div>
            {{-- <div class="col-md-6">
                <div class="form-group">
                    <select id="choose_email_reply_template" name="email_template" class="form-control form-control-lg">
                        <option value="0">Choose a Template</option>
                        @foreach($email_templates as $email)
                            <option value="{{ $email->id }}">{{ ucwords($email->email_template_name) }}</option>
                            @endforeach
                    </select>
                </div>
            </div> --}}
        </div>
        <textarea name="email_body" id="email_reply_body_{{ $note->id }}" class="summernote">
            <p>
                <br/><br/><br/>
                {{ $ppl_people->email_signature }}
            </p>
            <hr/>
            <p>
                <b>From: </b>{{ $note->log_from }}<br/>
                <b>Sent: </b><?php echo date('l, F d, Y h:i:s A', strtotime($note->log_date)) ?><br/>
                <b>To: </b>{{ $note->log_to }}<br/>
                <b>Subject: </b>{{ $note->log_subject }}                
            </p>
            <br/>
            {!! $log_message_detail !!}
        </textarea>
        <div id="email_reply_attachment_div_{{ $note->id }}" class="text-left" style="float: left;">

        </div>
        <div class="text-right" style="margin-top: 10px;">
            <div class="list-icons">
                <a class="cursor-pointer list-icons-item mr-lg-2" title="Attachment"  data-type="note" data-toggle="modal" data-target="#add_attachment_popup" onclick="addEmailReplyAttachmentPopup({{ $note->id }})"><i class="icon-attachment" style="font-size: 16px;font-weight: bold;margin-right: 15px;"></i></a>
                <a class="cancel_email_reply cursor-pointer list-icons-item mr-2" title="Cancel" data-key="{{ $note->id }}"><i class="icon-close2"></i></a>
            </div>
            <button type="button" class="send_email_reply_btn btn bg-teal-400" data-key="{{ $note->id }}">Send<i class="icon-paperplane ml-2"></i></button>
        </div> 
        </form>                                       
    </div>
    </div>
</div>
<div class="timeline-row">
    <div class="timeline-icon activity_notes_grid_3_background">
        <i class="icon-envelop timelineicon"></i>
    </div>
    <div class="card">
        <div class="card-header header-elements-inline activity_notes_grid_border">
            <h5 class="card-title">{{ $note->log_subject }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    <i class="icon-undo2 mr-3 icon-2x cursor-pointer email_reply_btn" style="font-size: 18px;" data-key="{{ $note->id }}"></i> <a class="list-icons-item" data-action="collapse"></a>
                </div>
            </div>
        </div>

        <div class="card-body">
                <p><b>From:</b> {{ $note->log_from }} <br>
                    <b>To:</b>{{ $note->log_to }} <br>
                    @if ($note->log_cc)
                        <b>CC:</b>{{ $note->log_cc }} <br>
                    @endif
                    @if ($note->log_bcc)
                        <b>BCC:</b>{{ $note->log_bcc }} <br>
                    @endif
                </p>
                    {!! $log_message_detail !!}
                    
                    <span style="display: inline-grid;">
                        @if(count($attachments)>0)
                                @foreach ($attachments as $attachment) 
                                    <a href="\admin\crm\crm-leads\viewActivityAttachment\{{ $attachment->id}}" target="_blank" class="badge-light badge-striped badge-striped-left border-left-info mb-2" style="color:#2196f3">
                                        {{ $attachment->attachment_type }}
                                    </a>
                                    @endforeach
                        @endif
                        </span>
                        <br/>
                    <br>
                    <i style="color: #999">Email Received {{ \Carbon\Carbon::parse($note->log_date)->diffForHumans() }} {{ ($user_firstname!='' && $last_lastname!='')? ' by '.$user_firstname.' '.$last_lastname:'' }} {{ " On ".$activity_date }}</i><br/>
        </div>
    </div>
</div>
@elseif($note->log_type==4)
<div class="timeline-row">
    <div class="timeline-icon activity_notes_grid_3_background">
        <i class="icon-envelop timelineicon"></i>
    </div>
    <div class="card">
        <div class="card-header header-elements-inline activity_notes_grid_border">
            <h5 class="card-title">{{ $note->log_subject }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    <a class="list-icons-item" data-action="collapse"></a>
                </div>
            </div>
        </div>

        <div class="card-body">
                    {!! $note->log_message !!}
                    
                        <br/>
                    <br>
                    <i style="color: #999">Email Opened {{ \Carbon\Carbon::parse($note->log_date)->diffForHumans() }} {{ " On ".$activity_date }}</i><br/>
        </div>
    </div>
</div>
@elseif($note->log_type==11)
<div class="timeline-row">
    <div class="timeline-icon activity_notes_grid_3_background">
        <i class="icon-list timelineicon"></i>
    </div>
    <div class="card">
        <div class="card-header header-elements-inline activity_notes_grid_border">
            <h5 class="card-title">{{ "Inventory List" }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    <a class="list-icons-item" data-action="collapse"></a>
                </div>
            </div>
        </div>

        <div class="card-body">
                    {!! $note->log_message !!}
                    
                        <br/>
                    <br>
                    <i style="color: #999">{{ \Carbon\Carbon::parse($note->log_date)->diffForHumans() }} {{ " On ".$activity_date }}</i><br/>
        </div>
    </div>
</div>
@elseif($note->log_type==14)
<div class="timeline-row">
    <div class="timeline-icon activity_notes_grid_3_background">
        <i class="icon-list timelineicon"></i>
    </div>
    <div class="card">
        <div class="card-header header-elements-inline activity_notes_grid_border">
            <h5 class="card-title">{{ "Inventory List" }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    <a class="list-icons-item" data-action="collapse"></a>
                </div>
            </div>
        </div>

        <div class="card-body">
                    {!! $note->log_message !!}
                    
                        <br/>
                    <br>
                    <i style="color: #999">{{ \Carbon\Carbon::parse($note->log_date)->diffForHumans() }} {{ " On ".$activity_date }}</i><br/>
        </div>
    </div>
</div>
@elseif($note->log_type==15)
<div class="timeline-row">
    <div class="timeline-icon activity_notes_grid_3_background">
        <i class="icon-pencil4 timelineicon"></i>
    </div>
    <div class="card">
        <div class="card-header header-elements-inline activity_notes_grid_border">
            <h5 class="card-title">{{ "Driver Notes" }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    <a class="list-icons-item" data-action="collapse"></a>
                </div>
            </div>
        </div>

        <div class="card-body">
                    {!! $note->log_message !!}
                    
                        <br/>
                    <br>
                    <i style="color: #999">{{ \Carbon\Carbon::parse($note->log_date)->diffForHumans() }} {{ ($user_firstname!='' && $last_lastname!='')? ' by '.$user_firstname.' '.$last_lastname:'' }} {{ " On ".$activity_date }}</i><br/>
        </div>
    </div>
</div>
@endif
@endforeach
@else
<div class="card">
    <div class="card-body">
    No activity
</div>
</div>
@endif