<?php

namespace App\Http\Controllers\Admin;

use App\Companies;
use App\CRMOpPipelineStatuses;
use App\CRMOpportunities;
use App\RoleUser;
use Carbon\Carbon;
use App\Helper\Reply;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Exception;


class OpportunityController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.lead');
        $this->pageIcon = 'icon-new-tab';
    }

    public function pipeline()
    {
        $this->statuses = CRMOpPipelineStatuses::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('sort_order', 'ASC')->get();
        return view('admin.opportunity.pipeline', $this->data);
    }

    public function movestatus(Request $request)
    {
        $id = $request->input('id');
        $from_status = $request->input('from_status');
        $to_status = $request->input('to_status');
        $data = CRMOpportunities::where(['id' => $id, 'tenant_id' => auth()->user()->tenant_id])->update(['op_status' => $to_status]);

        $to_total = CRMOpportunities::where(['op_status'=>$to_status, 'tenant_id'=>auth()->user()->tenant_id])->count();
        $to_total_value = CRMOpportunities::where(['op_status'=>$to_status, 'tenant_id'=>auth()->user()->tenant_id])->sum('value');

        $from_total = CRMOpportunities::where(['op_status'=>$from_status, 'tenant_id'=>auth()->user()->tenant_id])->count();
        $from_total_value = CRMOpportunities::where(['op_status'=>$from_status, 'tenant_id'=>auth()->user()->tenant_id])->sum('value');

        $response['error']=0;
        $response['to_total']=$to_total;
        $response['to_total_value']=$this->global->currency_symbol.$to_total_value;
        $response['from_total']=$from_total;
        $response['from_total_value']=$this->global->currency_symbol.$from_total_value;
        $response['message']='Opportunity moved successfully';
        return json_encode($response);
    }

    public function listdata()
    {
        $this->statuses = CRMOpPipelineStatuses::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('sort_order', 'ASC')->get();
        $selectedStatuses = CRMOpPipelineStatuses::where(['tenant_id'=> auth()->user()->tenant_id])->whereIn('pipeline_id',[1,2])->get();

        $statusToSelect = array();
        foreach($selectedStatuses as $row)
        {
            $select_status = $row->pipeline_status;

            $statusToSelect[] = $select_status;

        }

        $this->statusToSelect = $statusToSelect;
        //DB::enableQueryLog();
        $this->users = RoleUser::select(
                        'role_user.user_id',
                        'users.name')
                        ->leftjoin('users', 'users.id', '=', 'role_user.user_id')
                        ->where(['role_user.tenant_id'=>auth()->user()->tenant_id, 'users.status'=>'active', 'role_user.role_id' => '1'])
                        ->orWhere(['role_user.role_id' => '2'])
                        ->where(['role_user.tenant_id'=>auth()->user()->tenant_id])
                        ->get();
        return view('admin.opportunity.listdata', $this->data);
    }

    public function data(Request $request)
    {
        try {
            $result = CRMOpportunities::select(
                    'crm_opportunities.id',
                        'crm_opportunities.lead_id',
                        'crm_leads.name',
                        'crm_opportunities.op_status',
                        'crm_opportunities.op_type',
                        'crm_opportunities.value',
                        'crm_opportunities.updated_at',
                        'crm_opportunities.created_at',
                        'jobs_moving.job_date',
                        'jobs_moving.company_id',
                        'jobs_moving.job_id',
                        'jobs_moving.job_number',
                        'jobs_moving.lead_info',
                        'jobs_cleaning.job_date as cleaning_job_date',
                        'jobs_cleaning.company_id as cleaning_company_id',
                        'jobs_cleaning.job_id as cleaning_job_id',
                        'jobs_cleaning.job_number as cleaning_job_number',
                        'users.name as user_name'
                    )
                    ->join('crm_leads', 'crm_leads.id', '=', 'crm_opportunities.lead_id')
                    ->leftjoin('users', 'users.id', '=', 'crm_opportunities.user_id')
                    ->leftjoin('jobs_moving', function ($join) {
                            $join->on('crm_opportunities.id', '=', 'jobs_moving.crm_opportunity_id')
                                ->where('crm_opportunities.op_type', '=', 'Moving');
                        })
                    ->leftjoin('jobs_cleaning', function ($join) {
                            $join->on('crm_opportunities.id', '=', 'jobs_cleaning.crm_opportunity_id')
                                ->where('crm_opportunities.op_type', '=', 'Cleaning');
                        });
                    
                    $result = $result->where(['crm_opportunities.tenant_id'=>auth()->user()->tenant_id, 'crm_opportunities.deleted'=>0]);
                    $result = $result->where(function ($query) {
                            $query->orWhere('jobs_moving.opportunity', '=', 'Y')
                                  ->orWhere('jobs_cleaning.opportunity', '=', 'Y');
                        });
            
            if ($request->created_date_start !== null && $request->created_date_start != 'null' && $request->created_date_start != '') {
                $startDate = Carbon::createFromFormat($this->global->date_format, $request->created_date_start)->toDateString();
                // $result = $result->where(DB::raw('DATE(crm_opportunities.`est_job_date`)'), '>=', $startDate);
                $result = $result->where(DB::raw('DATE(crm_opportunities.`created_at`)'), '>=', $startDate);
            }
            if ($request->created_date_end !== null && $request->created_date_end != 'null' && $request->created_date_end != '') {
                $created_date_end = Carbon::createFromFormat($this->global->date_format, $request->created_date_end)->toDateString();
                // $result = $result->where(DB::raw('DATE(crm_opportunities.`est_job_date`)'), '<=', $created_date_end);
                $result = $result->where(DB::raw('DATE(crm_opportunities.`created_at`)'), '<=', $created_date_end);
            }

            if ($request->job_date_start !== null && $request->job_date_start != 'null' && $request->job_date_start != '') {
                $job_date_start = Carbon::createFromFormat($this->global->date_format, $request->job_date_start)->toDateString();
                // $result = $result->where(DB::raw('DATE(crm_opportunities.`est_job_date`)'), '>=', $startDate);
                $result = $result->where(DB::raw('DATE(jobs_moving.`job_date`)'), '>=', $job_date_start);
            }
            if ($request->job_date_end !== null && $request->job_date_end != 'null' && $request->job_date_end != '') {
                $job_date_end = Carbon::createFromFormat($this->global->date_format, $request->job_date_end)->toDateString();
                // $result = $result->where(DB::raw('DATE(crm_opportunities.`est_job_date`)'), '<=', $created_date_end);
                $result = $result->where(DB::raw('DATE(jobs_moving.`job_date`)'), '<=', $job_date_end);
            }
            
            if ($request->opportunity_status !== null && $request->opportunity_status != 'null' && $request->opportunity_status != '') {
                $opportunity_status = explode(",", $request->opportunity_status);
                $result = $result->wherein('op_status', $opportunity_status);
            }
            if ($request->user_id !== null && $request->user_id != 'null' && $request->user_id != '') {
                $user_id = explode(",", $request->user_id);
                $result = $result->wherein('user_id', $user_id);
            }

            if ($request->sorting_order !== null && $request->sorting_order != 'null' && $request->sorting_order != '') {
                
                if($request->sort_descending !== null && $request->sort_descending == '1'){
                    $sortBy = 'desc';
                } else {
                    $sortBy = 'asc';
                }
                $result = $result->orderBy('crm_opportunities.'.$request->sorting_order, $sortBy);
            }
            $result = $result->get();
            // dd($result);
            return DataTables::of($result)
                            ->editColumn('opportunity_number', function ($row) {
                                if($row->op_type=='Moving'){
                                    return $row->job_number;
                                } else {
                                    return $row->cleaning_job_number;
                                }
                                
                            })
                            ->editColumn('lead', function ($row) {

                                return '<h6 style="margin-bottom:0px;"><a href="'. route("admin.crm-leads.view", [$row->lead_id, $row->id]) .'" style="color: #6d91ba;">'. $row->name .'</a></h6>';
                            })
                            ->editColumn('mobile', function ($row) {
                                $lead_mobile = DB::table('crm_contacts')
                                    ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                                    ->select('crm_contact_details.detail')
                                    ->where(['crm_contacts.lead_id' => $row->lead_id, 'crm_contact_details.detail_type' => 'Mobile', 'crm_contacts.deleted' => 'N'])
                                    ->pluck('crm_contact_details.detail')->first();
                                return $lead_mobile;
                            }) 
                            ->editColumn('lead_info', function ($row) {

                                return $row->lead_info;
                            })                            
                            ->editColumn('job_date', function ($row) {
                                if($row->op_type=='Moving'){
                                    return date('Y/m/d', strtotime($row->job_date));
                                } else {
                                    return date('Y/m/d', strtotime($row->cleaning_job_date));
                                }
                                
                            })
                            ->editColumn('company', function ($row) {
                                if($row->op_type=='Moving'){
                                    $company = Companies::where('id', $row->company_id)->pluck("company_name")->first();
                                    if($company){
                                        $company = explode(" ",$company);
                                        return $company[0];
                                    }else{
                                        return "";
                                    }                                    
                                } else {
                                    $company = Companies::where('id', $row->cleaning_company_id)->pluck("company_name")->first();
                                    if($company){
                                        $company = explode(" ",$company);
                                        return $company[0];
                                    }else{
                                        return "";
                                    }
                                }
                            })
                            ->editColumn('type', function ($row) {
                                return $row->op_type;
                            })
                            ->editColumn('created_date', function ($row) {
                                return date('Y/m/d', strtotime($row->created_at));    
                            })
                            ->editColumn('status', function ($row) {
                                return $row->op_status;
                            })
                            ->editColumn('user', function ($row) {
                                return $row->user_name;
                            })
                            ->rawColumns(['lead'])
                            ->make(true);
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }
}
 
