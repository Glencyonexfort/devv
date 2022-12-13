<?php

namespace App\Http\Controllers\Admin;

use App\CRMActivityLog;
use App\CRMLeads;
use App\CRMTasks;
use App\SysLogType;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CRMActivityController extends AdminBaseController
{
    public function __construct() {
        parent::__construct();
        $this->pageIcon = 'icon-envelop';
        $this->pageTitle = 'Inbox';
        $this->log_types = [4,5,9,11,14];
    }
    public function index() {
        $this->sys_log_types = SysLogType::get();
        $this->users = User::where(['tenant_id'=> auth()->user()->tenant_id,'status'=>'active'])->get();
        $this->activities = DB::table('crm_activity_log')
        ->join('crm_leads', 'crm_leads.id', '=', 'crm_activity_log.lead_id')
        ->join('crm_opportunities', 'crm_opportunities.lead_id', '=', 'crm_activity_log.lead_id')
        ->leftJoin('jobs_moving', 'crm_opportunities.id', '=', 'jobs_moving.crm_opportunity_id')
        ->select('crm_activity_log.id','crm_activity_log.log_subject','crm_activity_log.log_message','crm_activity_log.log_date',
                'crm_activity_log.log_from','crm_activity_log.user_id','crm_activity_log.log_type','crm_activity_log.lead_id',
                'crm_leads.name', 'crm_opportunities.op_type','jobs_moving.crm_opportunity_id',
                'jobs_moving.job_id','jobs_moving.job_number','jobs_moving.opportunity')
        ->where(['crm_activity_log.tenant_id'=>auth()->user()->tenant_id,
        //'crm_activity_log.user_id'=>auth()->user()->id,
        'crm_activity_log.log_status'=>'unread'
        ])
        ->where('crm_opportunities.deleted', '=', 0)
        //->where('jobs_moving.deleted', '=', 0)
        ->whereIn('crm_activity_log.log_type', $this->log_types)
        ->orderBy('crm_activity_log.id', 'DESC')
        ->get();
        $this->filter="";
        // $this->activities = CRMActivityLog::take(10)->get();
        // dd($this->activities);
        
        return view('admin.crm-activity.index', $this->data);
    }

    public function getActivityData(Request $request){
        $filter = $request->input('filter');
        $user = $request->input('user');
        $this->filter=$filter;
        $this->sys_log_types = SysLogType::get();
        $this->users = User::where(['tenant_id'=> auth()->user()->tenant_id,'status'=>'active'])->get();
        if($filter=='email'){
            $type=[4,5,9,11,14];
        }elseif($filter=='sms'){
            $type=[9];
        }elseif($filter=='task'){
            
        }else{
            $type=$this->log_types;
        }     
        if(isset($user)){
            $userid=$user;
        }else{
            $userid=auth()->user()->id;
        }   

        if($filter=='task'){
            $this->activities = DB::table('crm_tasks')
            ->join('crm_leads', 'crm_leads.id', '=', 'crm_tasks.lead_id')
            ->join('users', 'users.id', '=', 'crm_tasks.user_assigned_id')
            ->join('crm_opportunities', 'crm_opportunities.lead_id', '=', 'crm_leads.id')
            ->join('jobs_moving', 'crm_opportunities.id', '=', 'jobs_moving.crm_opportunity_id')
            ->select(DB::raw('"Task" AS log_type'),
                    DB::raw('"" AS job_number'),
                    DB::raw('"" AS log_from'),
                    DB::raw('"" AS log_message'),
                    'crm_leads.id as lead_id','crm_tasks.id as id',
                    'crm_tasks.description as log_subject', 'crm_tasks.task_date as log_date','crm_opportunities.id as crm_opportunity_id',
                    'crm_tasks.user_assigned_id as user_id','crm_leads.name','users.name as user_name',
                    'crm_opportunities.op_type','jobs_moving.job_id','jobs_moving.job_number','jobs_moving.opportunity')
            ->where(['crm_tasks.tenant_id'=>auth()->user()->tenant_id,
            //'crm_tasks.user_assigned_id'=>auth()->user()->id,
            'crm_tasks.status'=>'Active',
            'crm_opportunities.deleted'=> 0
            ])
            ->orderBy('crm_tasks.task_date', 'DESC')
            ->get();
            // dd($this->activities);
            $count = count($this->activities);
            $allCount = DB::table('crm_activity_log')
            ->join('crm_leads', 'crm_leads.id', '=', 'crm_activity_log.lead_id')
            ->join('crm_opportunities', 'crm_opportunities.lead_id', '=', 'crm_activity_log.lead_id')
            ->join('jobs_moving', 'crm_opportunities.id', '=', 'jobs_moving.crm_opportunity_id')
            ->select('crm_activity_log.id','crm_activity_log.log_subject','crm_activity_log.log_message','crm_activity_log.log_date',
                    'crm_activity_log.log_from','crm_activity_log.user_id','crm_activity_log.log_type','crm_activity_log.lead_id',
                    'crm_leads.name', 'crm_opportunities.op_type','crm_opportunities.id as crm_opportunity_id',
                    'jobs_moving.job_id','jobs_moving.job_number','jobs_moving.opportunity')
            ->where(['crm_activity_log.tenant_id'=>auth()->user()->tenant_id,
            //'crm_activity_log.user_id'=>auth()->user()->id,
            'crm_activity_log.log_status'=>'unread'
            ])
            ->where('crm_opportunities.deleted', '=', 0)
            //->where('jobs_moving.deleted', '=', 0)
            ->whereIn('crm_activity_log.log_type', $this->log_types)
            ->orderBy('crm_activity_log.id', 'DESC')
            ->count();
            $allCount = $allCount + DB::table('crm_tasks')
            ->join('crm_leads', 'crm_leads.id', '=', 'crm_tasks.lead_id')
            ->select(DB::raw('"Task" AS log_type'),
                    DB::raw('"" AS job_number'),
                    DB::raw('"" AS log_from'),
                    DB::raw('"" AS log_message'),
                    'crm_leads.id as lead_id','crm_tasks.id as id',
                    'crm_tasks.description as log_subject', 'crm_tasks.task_date as log_date',
                    'crm_tasks.user_assigned_id as user_id','crm_leads.name')
            ->where(['crm_tasks.tenant_id'=>auth()->user()->tenant_id,
            //'crm_tasks.user_assigned_id'=>auth()->user()->id,
            'crm_tasks.status'=>'Active'
            ])
            ->orderBy('crm_tasks.task_date', 'DESC')
            ->count();
            if($count>0){
                $response['error'] = 0;
                $response['count'] = count($this->activities);
                $response['allCount'] = $allCount;
                $response['html'] = view('admin.crm-activity.grid', $this->data)->render();
                return json_encode($response);
            }else{
                $response['error'] = 1;
                $response['message'] = 'No record found!';
                $response['allCount'] = $allCount;
                $response['html'] = view('admin.crm-activity.grid', $this->data)->render();
                return json_encode($response);
            }
            }else{            
                $this->activities = DB::table('crm_activity_log')
                ->join('crm_leads', 'crm_leads.id', '=', 'crm_activity_log.lead_id')
                ->join('crm_opportunities', 'crm_opportunities.lead_id', '=', 'crm_activity_log.lead_id')
                ->join('jobs_moving', 'crm_opportunities.id', '=', 'jobs_moving.crm_opportunity_id')
                ->select('crm_activity_log.id','crm_activity_log.log_subject','crm_activity_log.log_message','crm_activity_log.log_date',
                        'crm_activity_log.log_from','crm_activity_log.user_id','crm_activity_log.log_type','crm_activity_log.lead_id',
                        'crm_leads.name', 'crm_opportunities.op_type','jobs_moving.crm_opportunity_id',
                        'jobs_moving.job_id','jobs_moving.job_number','jobs_moving.opportunity')
                ->where(['crm_activity_log.tenant_id'=>auth()->user()->tenant_id,
                //'crm_activity_log.user_id'=>$userid,
                'crm_activity_log.log_status'=>'unread'
                ])
                ->where('crm_opportunities.deleted', '=', 0)
                ->whereIn('crm_activity_log.log_type',$type)
                ->orderBy('crm_activity_log.id', 'DESC')
                ->get();
            }
            $count = count($this->activities);
            $allCount = DB::table('crm_activity_log')
                    ->join('crm_leads', 'crm_leads.id', '=', 'crm_activity_log.lead_id')
                    ->join('crm_opportunities', 'crm_opportunities.lead_id', '=', 'crm_activity_log.lead_id')
                    ->leftJoin('jobs_moving', 'crm_opportunities.id', '=', 'jobs_moving.crm_opportunity_id')
                    ->select('crm_activity_log.id','crm_activity_log.log_subject','crm_activity_log.log_message','crm_activity_log.log_date',
                            'crm_activity_log.log_from','crm_activity_log.user_id','crm_activity_log.log_type','crm_activity_log.lead_id',
                            'crm_leads.name', 'crm_opportunities.op_type','jobs_moving.crm_opportunity_id',
                            'jobs_moving.job_id','jobs_moving.job_number','jobs_moving.opportunity')
                    ->where(['crm_activity_log.tenant_id'=>auth()->user()->tenant_id,
                    //'crm_activity_log.user_id'=>auth()->user()->id,
                    'crm_activity_log.log_status'=>'unread'
                    ])
                    ->where('crm_opportunities.deleted', '=', 0)
                    //->where('jobs_moving.deleted', '=', 0)
                    ->whereIn('crm_activity_log.log_type', $this->log_types)
                    ->orderBy('crm_activity_log.id', 'DESC')
                    ->count();
            $allCount = $allCount + DB::table('crm_tasks')
                    ->leftJoin('crm_leads', 'crm_leads.id', '=', 'crm_tasks.lead_id')
                    ->select(DB::raw('"Task" AS log_type'),
                            DB::raw('"" AS job_number'),
                            DB::raw('"" AS log_from'),
                            DB::raw('"" AS log_message'),
                            'crm_leads.id as lead_id','crm_tasks.id as id',
                            'crm_tasks.description as log_subject', 'crm_tasks.task_date as log_date',
                            'crm_tasks.user_assigned_id as user_id','crm_leads.name')
                    ->where(['crm_tasks.tenant_id'=>auth()->user()->tenant_id,
                    //'crm_tasks.user_assigned_id'=>auth()->user()->id,
                    'crm_tasks.status'=>'Active'
                    ])
                    ->orderBy('crm_tasks.task_date', 'DESC')
                    ->count();
            if($count>0){
                    $response['error'] = 0;
                    $response['count'] = count($this->activities);
                    $response['allCount'] = $allCount;
                    $response['html'] = view('admin.crm-activity.grid', $this->data)->render();
                    return json_encode($response);
            }else{
                    $response['error'] = 2;
                    $response['message'] = 'No record found!';
                    $response['html'] = view('admin.crm-activity.grid', $this->data)->render();
                    return json_encode($response);
            }        
    }

    public function updateActivityDataInIds(Request $request)
    {
        $filter = $request->input('filter');
        $log_ids = count($request->Ids);
        if(count($request->Ids))
            {
                if($filter == 'task'){
                    foreach($request->Ids as $id){
                        CRMTasks::where('id',$id)->update(['status' => 'Done']);
                    }
                }else{
                    foreach($request->Ids as $id){
                        CRMActivityLog::where('id',$id)->update(['log_status' => 'read']);
                    }
                }
                $response['error'] = 0;
                $response['message'] = 'Record has been updated!';
            }else{
                $response['error'] = 1;
                $response['message'] = 'No Record Found!';
            }
            
            return $response;
    }

    public function updateActivityData(Request $request){
        $id = $request->input('id');
        $type = $request->input('type');
        $this->filter = ($type=='Task')?"task":"";
        $this->sys_log_types = SysLogType::get();
        $this->users = User::where(['tenant_id'=> auth()->user()->tenant_id,'status'=>'active'])->get();
        if($type=='Task'){
            $data = CRMTasks::where('id',$id)->update(['status' => 'Done']);
            $this->activities = DB::table('crm_tasks')
            ->leftJoin('crm_leads', 'crm_leads.id', '=', 'crm_tasks.lead_id')
            ->select(DB::raw('"Task" AS log_type'),
                    DB::raw('"" AS job_number'),
                    DB::raw('"" AS log_from'),
                    DB::raw('"" AS log_message'),
                    'crm_leads.id as lead_id','crm_tasks.id as id',
                    'crm_tasks.description as log_subject', 'crm_tasks.task_date as log_date',
                    'crm_tasks.user_assigned_id as user_id','crm_leads.name')
            ->where(['crm_tasks.tenant_id'=>auth()->user()->tenant_id,
            //'crm_tasks.user_assigned_id'=>auth()->user()->id,
            'crm_tasks.status'=>'Active'
            ])
            ->orderBy('crm_tasks.task_date', 'DESC')
            ->get();

        }else{
            $data = CRMActivityLog::where('id',$id)->update(['log_status' => 'read']);
            $this->activities = DB::table('crm_activity_log')
            ->join('crm_leads', 'crm_leads.id', '=', 'crm_activity_log.lead_id')
            ->join('crm_opportunities', 'crm_opportunities.lead_id', '=', 'crm_activity_log.lead_id')
            ->leftJoin('jobs_moving', 'crm_opportunities.id', '=', 'jobs_moving.crm_opportunity_id')
            ->select('crm_activity_log.id','crm_activity_log.log_subject','crm_activity_log.log_message','crm_activity_log.log_date',
                        'crm_activity_log.log_from','crm_activity_log.user_id','crm_activity_log.log_type','crm_activity_log.lead_id',
                        'crm_leads.name', 'crm_opportunities.op_type','jobs_moving.crm_opportunity_id',
                        'jobs_moving.job_id','jobs_moving.job_number','jobs_moving.opportunity')
                        ->where(['crm_activity_log.tenant_id'=>auth()->user()->tenant_id,
                        //'crm_activity_log.user_id'=>auth()->user()->id,
                        'crm_activity_log.log_status'=>'unread'
                        ])
                        ->where('crm_opportunities.deleted', '=', 0)
                        ->whereIn('crm_activity_log.log_type',$this->log_types)
            ->orderBy('crm_activity_log.id', 'DESC')
            ->get();
        }
        $count = count($this->activities);
        $allCount = DB::table('crm_activity_log')
            ->join('crm_leads', 'crm_leads.id', '=', 'crm_activity_log.lead_id')
            ->join('crm_opportunities', 'crm_opportunities.lead_id', '=', 'crm_activity_log.lead_id')
            ->leftJoin('jobs_moving', 'crm_opportunities.id', '=', 'jobs_moving.crm_opportunity_id')
            ->select('crm_activity_log.id','crm_activity_log.log_subject','crm_activity_log.log_message','crm_activity_log.log_date',
                    'crm_activity_log.log_from','crm_activity_log.user_id','crm_activity_log.log_type','crm_activity_log.lead_id',
                    'crm_leads.name', 'crm_opportunities.op_type','jobs_moving.crm_opportunity_id',
                    'jobs_moving.job_id','jobs_moving.job_number','jobs_moving.opportunity')
            ->where(['crm_activity_log.tenant_id'=>auth()->user()->tenant_id,
            //'crm_activity_log.user_id'=>auth()->user()->id,
            'crm_activity_log.log_status'=>'unread'
            ])
            ->where('crm_opportunities.deleted', '=', 0)
            //->where('jobs_moving.deleted', '=', 0)
            ->whereIn('crm_activity_log.log_type', $this->log_types)
            ->orderBy('crm_activity_log.id', 'DESC')
            ->count();
        $allCount = $allCount + DB::table('crm_tasks')
            ->leftJoin('crm_leads', 'crm_leads.id', '=', 'crm_tasks.lead_id')
            ->select(DB::raw('"Task" AS log_type'),
                    DB::raw('"" AS job_number'),
                    DB::raw('"" AS log_from'),
                    DB::raw('"" AS log_message'),
                    'crm_leads.id as lead_id','crm_tasks.id as id',
                    'crm_tasks.description as log_subject', 'crm_tasks.task_date as log_date',
                    'crm_tasks.user_assigned_id as user_id','crm_leads.name')
            ->where(['crm_tasks.tenant_id'=>auth()->user()->tenant_id,
            //'crm_tasks.user_assigned_id'=>auth()->user()->id,
            'crm_tasks.status'=>'Active'
            ])
            ->orderBy('crm_tasks.task_date', 'DESC')
            ->count();
            
            $response['error'] = 0;
            $response['message'] = 'Record has been updated!';
            $response['count'] = $count;
            $response['allCount'] = $allCount;
            $response['html'] = view('admin.crm-activity.grid', $this->data)->render();
            return json_encode($response); 
    }

    // START:: Top Search 
    public function ajaxMainSearch(Request $request){
        if($request->get('query')){
            $this->query = $request->get('query');
            $type = $request->get('type');
            if($type=="customer"){
                $data = DB::table('crm_leads')
                    ->select('crm_leads.id', 'crm_leads.name', 'crm_contact_details.detail as contact')
                    ->leftjoin('crm_contacts', 'crm_leads.id', '=', 'crm_contacts.lead_id')
                    ->leftjoin('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                    ->where('crm_contacts.deleted', 'N')
                    ->where(['crm_leads.tenant_id' => auth()->user()->tenant_id])
                    ->where(function ($q) {
                        $q->where('crm_contact_details.detail', 'LIKE', "%{$this->query}%")
                            ->orWhere('crm_leads.name', 'LIKE', "%{$this->query}%");
                    })
                    ->limit(10)->get();

                $output = '<div id="top_search_result_list" class="dropdown-menu show">';
                if(count($data)){
                    foreach($data as $row){
                        $output .= '<a href="/admin/crm/view-customer-leads/'.$row->id.'" class="dropdown-item"><b>'.$row->name.'</b>';
                        if($row->contact!=""){
                            $output .='<br/><span>'.$row->contact.'</span>';
                        }
                        $output .='</a>';
                    }
                }else{
                    $output .= '<a href="#" class="dropdown-item">Not result found. </a>';
                }
                $output .= '</div>';
            }else{
                $data = DB::table('jobs_moving')
                ->select(
                    'jobs_moving.job_id',
                    'crm_leads.name as lead_name', 
                    'jobs_moving.job_number',
                    'crm_leads.id as lead_id', 
                    'jobs_moving.crm_opportunity_id', 
                    'jobs_moving.opportunity')
                ->join('crm_leads', 'jobs_moving.customer_id', '=', 'crm_leads.id')
                ->where(['jobs_moving.tenant_id' => auth()->user()->tenant_id, 'jobs_moving.deleted' => 0])
                ->where(function ($q) {
                    $q->where('jobs_moving.job_number', 'LIKE', "%{$this->query}%")
                        ->orWhere('crm_leads.name', 'LIKE', "%{$this->query}%");
                })
                ->limit(10)->get();

                $output = '<div id="top_search_result_list" class="dropdown-menu show">';
                if(count($data)){
                    foreach($data as $row){
                        if($row->opportunity=="Y"){
                            $output .= '<a href="/admin/crm/view-opportunity/'.$row->lead_id.'/'.$row->crm_opportunity_id.'" class="dropdown-item"><b>'.$row->lead_name.'</b>';
                            $output .='<br/><span>Opportunity # '.$row->job_number.'</span>';
                            $output .='</a>';
                        }else{
                            $output .= '<a href="/admin/moving/view-job/'.$row->job_id.'" class="dropdown-item"><b>'.$row->lead_name.'</b>';
                            $output .='<br/><span>Job # '.$row->job_number.'</span>';
                            $output .='</a>';
                        }
                    }
                }else{
                    $output .= '<a href="#" class="dropdown-item">Not result found. </a>';
                }
                $output .= '</div>';
            }
            echo $output;
        }
    }
    // END:: Top Search
}
