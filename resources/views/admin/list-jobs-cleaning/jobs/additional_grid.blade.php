<?php
    $cleaning_form_setup = \App\JobsCleaningQuoteFormSetup::where('tenant_id', '=', auth()->user()->tenant_id)->where('job_type_id', '=', '2')->first();
    $questions_list_ids = $cleaning_form_setup->questions_list_type_id;
    $questions_list_ids_ary = explode(',', $questions_list_ids);
    $question_list =  array();
    foreach ($questions_list_ids_ary as $qid) {
        $question_list[$qid]['question'] = \App\ListTypes::select('id', 'list_name')->where('tenant_id', '=', auth()->user()->tenant_id)->where('id', '=', $qid)->first();
        $question_list[$qid]['list'] = \App\ListOptions::select('id', 'list_option')->where('tenant_id', '=', auth()->user()->tenant_id)->where('list_type_id', '=', $qid)->get();
    }
?>
<article>                    
    <table class="payment">
        <thead>
            <tr>
                <th><span >Question</span></th>
                <th><span >Reply</span></th>
            </tr>
        </thead>
        <tbody>
            @if($question_list)
            @foreach($question_list as $qid => $rs)
            <?php
                    $reply="";
                    $id="";
                    $this_additional = \App\JobsCleaningAdditionalInfo::where(
                    ['tenant_id' => auth()->user()->tenant_id, 
                    'job_id' => $job->job_id, 
                    'question'=>$rs['question']->list_name])
                    ->first();
                    if($this_additional){
                        $reply = $this_additional->reply;
                        $id = $this_additional->id;
                    }
                ?>   
                <tr id="additional_line_div_view_{{ $id }}">
                    <td>
                        <strong>{{ $rs['question']->list_name }}</strong>
                    </td>
                    <td>                        
                        {{ $reply }}
                            <div class="list-icons float-right">
                                <div class="dropdown">
                                    <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown"><i class="icon-menu"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="#" class="dropdown-item edit_additional_btn" data-toggle="modal" data-target="#call" data-id="{{ $id }}"><i class="icon-pencil"></i> Edit</a>                                            
                                        </div>
                                </div>
                            </div> 
                        </span>
                    </td>
                </tr>                
{{-- Edit Mode --}}                                                                                 
                <tr id="additional_line_div_edit_{{ $id }}" class="bgblu hidden" data-row="0">
                    <td>
                        <strong>{{ $rs['question']->list_name }}</strong>
                    </td>
                    <td>                       
                        <span data-prefix style="margin-right: 22px;">                            
                            <select id="additional_reply_{{ $id }}" class="form-control">
                                @foreach($rs['list'] as $optn)
                                <option value="{{$optn->list_option}}" 
                                    @if($optn->list_option == $reply)
                                    selected
                                    @endif
                                    >{{$optn->list_option}}</option>
                                @endforeach
                            </select>
                        <div class="d-flex justify-content-start align-items-center m-t-10">
                            <button type="button" class="btn btn-light cancel_edit_additional_btn" data-id="{{ $id }}"> Cancel</button>
                            <button type="button" class="btn btn-success ml-2 update_additional_btn" data-id="{{ $id }}"> Update</button>
                        </div>
                    </td>
                </tr>
            @endforeach
            @endif

</tbody>                
</table>
</article>  