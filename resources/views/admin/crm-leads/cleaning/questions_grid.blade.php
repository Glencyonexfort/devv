<?php
    $cleaning_form_setup = \App\JobsCleaningQuoteFormSetup::where('tenant_id', '=', auth()->user()->tenant_id)->where('job_type_id', '=', '1')->first();
    $question_list =  array();
    if($cleaning_form_setup){
        $questions_list_ids = $cleaning_form_setup->questions_list_type_id;
        $questions_list_ids_ary = explode(',', $questions_list_ids);
        foreach ($questions_list_ids_ary as $qid) {
            $question_list[$qid]['question'] = \App\ListTypes::select('id', 'list_name')->where('tenant_id', '=', auth()->user()->tenant_id)->where('id', '=', $qid)->first();
            $question_list[$qid]['list'] = \App\ListOptions::select('id', 'list_option')->where('tenant_id', '=', auth()->user()->tenant_id)->where('list_type_id', '=', $qid)->get();
        }
    }
?>
<div class="card view_blade_4_card">
    {{-- VIEW --}}
    <div id="update_questions_view">
        <div style="border-left:3px solid #fcbd2e;min-height: 20rem;" class="card-body job_left_panel_body1">
            @if($jobs_cleaning->opportunity == 'Y')
                <div class="d-flex justify-content-start align-items-center float-right">
                    <button class="show_update_questions_btn btn btn-icon"><i class="icon-pencil"></i></button>
                </div>
            @endif
            <div class="job-label-txt">
                @if(count($jobs_cleaning_additional))
                @foreach($jobs_cleaning_additional as $data)
                    <p class="moving-text font-weight-semibold align-left">{{ $data->question }}</p>
                    <p class="suburb align-left cleaning_reply"> {{ $data->reply}}</p>
                @endforeach
                @endif
            </div>
        </div>
    </div>
    {{-- FORM --}}
    <div id="update_questions_form" class="card-body p10 hidden body_margin">
        <form id="questions_form" class="custom-form" action="#">
            @csrf
            {{ Form::hidden('lead_id', $removal_opportunities->lead_id) }}
            {{ Form::hidden('opp_id', $removal_opportunities->id) }}
            <p class="moving-text font-weight-semibold align-left"></p>
                <p class="suburb align-left"> 
                    @if(count($question_list))
                    @foreach($question_list as $qid => $rs)
                    <?php
                        $reply="";
                        $reply = \App\JobsCleaningAdditionalInfo::where(
                            ['tenant_id' => auth()->user()->tenant_id, 
                            'job_id' => $jobs_cleaning->job_id, 
                            'question'=>$rs['question']->list_name]
                            )->pluck('reply')->first();
                    ?>
                    <div class="form-group">
                        <label>{{$rs['question']->list_name}}</label>
                        <input type="hidden" name="additional[{{ $qid }}][question]" value="{{ $rs['question']->list_name }}"/>
                        <select name="additional[{{ $qid }}][reply]" class="form-control">
                            @foreach($rs['list'] as $optn)
                            <option value="{{$optn->list_option}}" 
                                @if($optn->list_option == $reply)
                                selected
                                @endif
                                >{{$optn->list_option}}</option>
                            @endforeach
                        </select>
                    </div>  
                    @endforeach      
                    @endif            
                </p>
            <div class="d-flex justify-content-start align-items-center m-t-10">
                <button type="reset" class="btn btn-light show_update_questions_btn">Cancel</button>
                <button type="button" id="update_questions_btn" class="btn bg-blue ml-3">Update</button>
            </div>

        </form>
    </div>
</div>
