<?php

namespace App\Http\Controllers\Admin;

use App\Companies;
use App\CRMActivityLog;
use App\CRMActivityLogAttachment;
use App\CRMContactDetail;
use App\CRMContacts;
use App\CRMLeads;
use App\CRMLeadStatuses;
use App\CRMOpPipelineStatuses;
use App\CRMOpportunities;
use App\CRMTasks;
use App\CustomerDetails;
use App\Customers;
use App\EmailTemplateAttachments;
use App\EmailTemplates;
use App\Http\Requests\Companies\StoreCompany;
use App\Invoice;
use App\InvoiceItems;
use App\InvoiceSetting;
use App\JobsCleaning;
use App\JobsCleaningAdditionalInfo;
use App\JobsCleaningShifts;
use App\JobsMoving;
use Stripe\Stripe;
use App\Mail\sendMail;
use App\OrganisationSettings;
use App\Product;
use App\QuoteItem;
use App\Quotes;
use App\SMSTemplates;
use App\Tax;
use App\User;
use App\PropertyCategoryOptions;
use App\SysCountryStates;
use Carbon\Carbon;
use App\MovingInventoryDefinitions;
use App\MovingInventoryGroups;
use App\JobsMovingInventory;
use App\JobsMovingLegs;
use App\JobsMovingPricingAdditional;
use App\ListTypes;
use App\Payment;
use App\PplPeople;
use App\StorageTypes;
use App\StorageUnitAllocation;
use App\StorageUnits;
use App\TenantApiDetail;
use CrmContactDetails;
use CrmContacts as GlobalCrmContacts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Crypt;
use Exception;
use Config;
use Illuminate\Support\Facades\Session;

class CRMLeadsController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.customers');
        $this->pageIcon = 'icon-new-tab';
    }

    public function index()
    {
        $this->lead_statuses = CRMLeadStatuses::select('id', 'lead_status')->where('tenant_id', '=', auth()->user()->tenant_id)->orderBy('sort_order', 'asc')->get();
        return view('admin.crm-leads.index', $this->data);
    }
    public function residential()
    {
        $this->pageTitle = 'Residential Contomer';
        $this->pageIcon = 'icon-new-tab';
        $this->lead_statuses = CRMLeadStatuses::select('id', 'lead_status')->where('tenant_id', '=', auth()->user()->tenant_id)->orderBy('sort_order', 'asc')->get();
        return view('admin.crm-leads.residential', $this->data);
    }

    public function commercial()
    {
        $this->pageTitle = 'Commercial Contomer';
        $this->pageIcon = 'icon-new-tab';
        $this->lead_statuses = CRMLeadStatuses::select('id', 'lead_status')->where('tenant_id', '=', auth()->user()->tenant_id)->orderBy('sort_order', 'asc')->get();
        return view('admin.crm-leads.commercial', $this->data);
    }

    public function newdesign()
    {
        return view('admin.crm-leads.newdesign', $this->data);
    }

    public function view($id, $opportunity_id=null)
    {
        $this->current_opportunity_id = $opportunity_id;
        $this->lead_id = $id;
        $this->crmlead = CRMLeads::where(["id"=>$this->lead_id, 'tenant_id'=>auth()->user()->tenant_id])->firstOrFail();
        $this->ppl_people = PplPeople::where(['user_id' => auth()->user()->id, 'tenant_id' => auth()->user()->tenant_id])->first();
        $this->crmleadstatuses = CRMLeadStatuses::select('id', 'lead_status')->where('tenant_id', '=', auth()->user()->tenant_id)->orderBy('sort_order', 'asc')->get();

        $this->countInvItems = 0;
        $this->totalOpportunities = 0;
        $this->quoteItem = NULL;
        $this->quote = NULL;
        $this->jobs_cleaning_additional=NULL;
        $this->cleaning_shifts = JobsCleaningShifts::where(['tenant_id'=> auth()->user()->tenant_id])->get();

        $this->removal_opportunities = DB::table('crm_opportunities')->select(
                                                    'crm_opportunities.*',
                                                    'crm_leads.lead_type'
                                                    )
                                                ->leftjoin('crm_leads', 'crm_leads.id', 'crm_opportunities.lead_id')
                                                ->join('jobs_moving', 'jobs_moving.crm_opportunity_id', '=', 'crm_opportunities.id')
                                                ->where('jobs_moving.opportunity', '=', 'Y')
                                                ->where(['crm_opportunities.lead_id' => $id, 'crm_opportunities.tenant_id' => auth()->user()->tenant_id])
                                                ->first();
        if($this->removal_opportunities){
            $job_type=$this->removal_opportunities->op_type;
        }else{
            $job_type="Moving";
        }

        $this->users = User::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $this->without_worker_users = User::allPeopleWithSystemUsersWithNoDriver();

        $this->contact_types = ListTypes::where(['list_types.tenant_id' => auth()->user()->tenant_id, 'list_types.list_name' => 'Contact Type'])
        ->join('list_options', 'list_options.list_type_id', '=', 'list_types.id')
        ->select('list_options.list_option')
        ->get();

        $this->op_status = CRMOpPipelineStatuses::where(['tenant_id' => auth()->user()->tenant_id])->orderBy('sort_order', 'ASC')->get();
        $this->tasks = CRMTasks::where(['lead_id' => $id, 'tenant_id' => auth()->user()->tenant_id])->orderBy('id', 'DESC')->get();

        if($job_type=="Moving"){
            $this->opportunities = CRMOpportunities::where(['crm_opportunities.tenant_id' => auth()->user()->tenant_id, 'crm_opportunities.lead_id' => $id, 'crm_opportunities.op_type'=> $job_type, 'crm_opportunities.deleted'=>0])
            ->join('jobs_moving', 'jobs_moving.crm_opportunity_id', '=', 'crm_opportunities.id')
            ->where('jobs_moving.opportunity', '=', 'Y')
            ->orderBy('crm_opportunities.id', 'ASC')
            ->get();
        }elseif($job_type=="Cleaning"){
            $this->opportunities = CRMOpportunities::where(['crm_opportunities.tenant_id' => auth()->user()->tenant_id, 'crm_opportunities.lead_id' => $id, 'crm_opportunities.op_type'=> $job_type, 'crm_opportunities.deleted'=>0])
            ->join('jobs_cleaning', 'jobs_cleaning.crm_opportunity_id', '=', 'crm_opportunities.id')
            ->where('jobs_cleaning.opportunity', '=', 'Y')
            ->orderBy('crm_opportunities.id', 'DESC')
            ->get();
        }
        $this->contacts = CRMContacts::where(['lead_id' => $id, 'tenant_id' => auth()->user()->tenant_id, 'deleted' => 'N'])->orderBy('id', 'DESC')->get();
        $this->totalTasks = count($this->tasks);
        $this->totalOpportunities = count($this->opportunities);
        $this->totalContacts = count($this->contacts);
        $this->job_ids=[0];
        if($this->totalOpportunities>0){
            $this->job_ids = $this->getJobIdsByOppIds($job_type,$this->opportunities);
        }
        $this->notes = CRMActivityLog::where(['lead_id' => $id, 'tenant_id' => auth()->user()->tenant_id])
            ->whereIn('log_type', array(3, 4, 5, 7, 8, 11, 14, 15))
            ->where(function ($query) {
                $query->whereIn('job_id', $this->job_ids)
                    ->orWhereNull('job_id');
            })
            ->orderBy('id', 'DESC')
            ->get();    
        $this->sms_templates = SMSTemplates::where(['tenant_id' => auth()->user()->tenant_id,'active'=>'Y'])->orderBy('sms_template_name', 'ASC')->get();
        $this->email_templates = EmailTemplates::where(['tenant_id' => auth()->user()->tenant_id,'active'=>'Y'])->orderBy('email_template_name', 'ASC')->get();
        $this->sms_contacts = DB::table('crm_contacts')
            ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
            ->select('crm_contacts.name', 'crm_contact_details.detail')
            ->where(['crm_contacts.lead_id' => $id, 'crm_contact_details.detail_type' => 'Mobile', 'crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.deleted' => 'N'])
            ->get();
        $default_contact_email = DB::table('crm_contacts')
            ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
            ->select('crm_contact_details.detail')
            ->where(['crm_contacts.lead_id' => $id, 'crm_contact_details.detail_type' => 'Email', 'crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.deleted' => 'N'])
            ->first();  
        $this->lead_email = ($default_contact_email)? $default_contact_email->detail:'';  
        
        $this->lead_info = ListTypes::where(['list_types.tenant_id' => auth()->user()->tenant_id, 'list_types.list_name' => 'Lead Info', 'list_types.tenant_id' => auth()->user()->tenant_id])
            ->join('list_options', 'list_options.list_type_id', '=', 'list_types.id')
            ->select('list_options.list_option')
            ->get();

    
        //Estimate Section
        $this->taxs = Tax::where(['tenant_id' => auth()->user()->tenant_id])->get();
        if($this->crmlead->lead_type == 'Residential')
        {
            $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                        ->where(function ($query) {
                                            $query->orWhere('customer_type', 'Residential')
                                                ->orWhere('customer_type', 'Both');
                                        })
                                        ->orderBy('name', 'asc')
                                        ->get();
        }
        else
        {
            $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                        ->where(function ($query) {
                                            $query->orWhere('customer_type', 'Commercial')
                                                ->orWhere('customer_type', 'Both');
                                        })
                                        ->where(function ($query) {
                                            $query->orWhere('customer_id', NULL)
                                                ->orWhere('customer_id', $this->crmlead->id);
                                        })
                                        ->orderBy('name', 'asc')
                                        ->get();
        }
        if($job_type=="Moving"){
            $this->opportunity_jobs = DB::table('jobs_moving')
            ->join('crm_opportunities', 'jobs_moving.crm_opportunity_id', '=', 'crm_opportunities.id')
            ->select('jobs_moving.job_number', 'crm_opportunities.id', 'crm_opportunities.op_type')
            ->where(['jobs_moving.opportunity' => 'Y', 'crm_opportunities.lead_id' => $id, 'crm_opportunities.deleted' => 0, 'crm_opportunities.tenant_id' => auth()->user()->tenant_id])
            ->orderBy('crm_opportunities.id', 'DESC')->get();
        }elseif($job_type=="Cleaning"){
            $this->opportunity_jobs = DB::table('jobs_cleaning')
            ->join('crm_opportunities', 'jobs_cleaning.crm_opportunity_id', '=', 'crm_opportunities.id')
            ->select('jobs_cleaning.job_number', 'crm_opportunities.id', 'crm_opportunities.op_type')
            ->where(['jobs_cleaning.opportunity' => 'Y', 'crm_opportunities.lead_id' => $id, 'crm_opportunities.deleted' => 0, 'crm_opportunities.tenant_id' => auth()->user()->tenant_id])
            ->orderBy('crm_opportunities.id', 'DESC')->get();
        }

        if (count($this->opportunity_jobs) > 0) {
            if($this->current_opportunity_id)
            {
                foreach($this->opportunity_jobs as $opportunity)
                {
                    if($opportunity->id == $this->current_opportunity_id)
                    {
                        $op_id_first = $opportunity->id;
                        break;
                    }
                }
            }
            else
            {
                $op_id_first = $this->opportunity_jobs[0]->id;
            }
            if($op_id_first>0){
                $this->quote = Quotes::where(['crm_opportunity_id' => $op_id_first, 'sys_job_type' => $job_type, 'tenant_id' => auth()->user()->tenant_id])->first();
                if($this->quote){
                    $this->quoteItem = DB::table('quote_items')
                    ->select('*')
                    ->where(['quote_id' => $this->quote->id, 'tenant_id' => auth()->user()->tenant_id])
                    ->get();
                }   
            }else{
                $op_id_first=0;
            }         
        }else{
            $op_id_first=0;
        }

        // Inventory Section
        if($job_type=="Moving"){
            $this->job = JobsMoving::where(['crm_opportunity_id' => $op_id_first, 'opportunity'=>'Y', 'deleted'=> 0, 'tenant_id' => auth()->user()->tenant_id])->first();
        }elseif($job_type=="Cleaning"){
            $this->job = JobsCleaning::where(['crm_opportunity_id' => $op_id_first,'opportunity'=>'Y', 'deleted'=> 0, 'tenant_id' => auth()->user()->tenant_id])->first();
        }
        $this->inventory_groups = MovingInventoryGroups::where('tenant_id', '=', auth()->user()->tenant_id)
        ->orderBy('moving_inventory_groups.group_name','asc')
        ->get();
        $this->getInventoryItems = MovingInventoryDefinitions::where('tenant_id', '=', auth()->user()->tenant_id)
        
        ->get();
        if($this->job){
            $this->job_id=$this->job->job_id;
            // $this->countInvItems = JobsMovingInventory::where('inventory_id', '>', 9000)->where('job_id', $this->job->job_id)->count();
            if($this->job->company_id>0)
                $this->companies = Companies::where(['tenant_id' => auth()->user()->tenant_id, 'id' => $this->job->company_id, 'active' => 'Y'])->first();
            else
                $this->companies = Companies::where(['tenant_id' => auth()->user()->tenant_id, 'active' => 'Y'])->first();
        }else{
            $this->job_id=0;
            $this->companies = Companies::where(['tenant_id'=>auth()->user()->tenant_id,'active'=>'Y'])->first();
        }
        if($this->job){
            $this->miscllanceous_items = JobsMovingInventory::where(['job_id' => $this->job->job_id, 'misc_item' => 'Y', 'tenant_id' => auth()->user()->tenant_id])->get();
        }else{
            $this->miscllanceous_items = [];
        }
        
        //Removal Section        
        if ($this->removal_opportunities && $this->job) {
            if($job_type=="Moving"){
                $this->removal_jobs_moving = $this->job;
                $this->removal_jobs_moving_data = DB::table('jobs_moving')->select(
                                                        'pickup_contact_name',
                                                        'pickup_mobile',
                                                        'pickup_email',
                                                        'drop_off_contact_name',
                                                        'drop_off_mobile',
                                                        'drop_off_email'
                                                    )
                                                    ->where(['job_id' => $this->job->job_id, 'tenant_id' => auth()->user()->tenant_id])
                                                    ->first();
            }elseif($job_type=="Cleaning"){
                $this->jobs_cleaning = JobsCleaning::where(['crm_opportunity_id' => $this->removal_opportunities->id, 'deleted' => 0, 'tenant_id' => auth()->user()->tenant_id])->first();
                if($this->jobs_cleaning){
                    $this->jobs_cleaning_additional = JobsCleaningAdditionalInfo::where(['job_id' => $this->jobs_cleaning->job_id, 'tenant_id' => auth()->user()->tenant_id])->get();
                }
            }
            
        }

        $this->removal_companies = Companies::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $this->companies_list = $this->removal_companies;
        $this->removal_pipeline_statuses = CRMOpPipelineStatuses::where('tenant_id', '=', auth()->user()->tenant_id)->orderBy('sort_order', 'asc')->get();
        $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $this->sys_country_states = SysCountryStates::where('country_id', '=', $this->organisation_settings->business_country_id)->get();

        $this->property_types = PropertyCategoryOptions::select('options')->where('tenant_id', '=', auth()->user()->tenant_id)->where('category_id', '=', '1')->get();
        $this->furnishing = PropertyCategoryOptions::select('options')->where('tenant_id', '=', auth()->user()->tenant_id)->where('category_id', '=', '2')->get();
        $this->bedroom = PropertyCategoryOptions::select('options')->where('tenant_id', '=', auth()->user()->tenant_id)->where('category_id', '=', '3')->get();
        $this->living_room = PropertyCategoryOptions::select('options')->where('tenant_id', '=', auth()->user()->tenant_id)->where('category_id', '=', '4')->get();
        $this->other_room = PropertyCategoryOptions::select('options')->where('tenant_id', '=', auth()->user()->tenant_id)->where('category_id', '=', '5')->get();
        $this->special_item = PropertyCategoryOptions::select('options')->where('tenant_id', '=', auth()->user()->tenant_id)->where('category_id', '=', '6')->get();
        $this->google_api_key = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'GoogleMaps'])->pluck('account_key')->first();
        $this->attachment=[];
        $this->job_type = $job_type;  

        //Cover Freight Insurance setting
        $coverFreight = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'CoverFreight'])->first();            
        if($coverFreight){ 
            $this->coverFreight_connected=true;
        }else{
            $this->coverFreight_connected=false;
        }
        
        // Estimate Tab Deposit Required
        $this->job_price_additional = DB::table('jobs_moving_pricing_additional as t1')
                ->select('t1.*')
                ->where(['t1.tenant_id' => auth()->user()->tenant_id])
                ->first();
        session()->forget('notes_attachment');
        session()->forget('email_attachment');


        return view('admin.crm-leads.view', $this->data);
    }

    private function getJobIdsByOppIds($job_type,$opportunities){
        $job_ids[]=0;
            if($job_type=="Moving"){ // For Moving 
                foreach($opportunities as $opp){
                    $job_ids[] = JobsMoving::where(['crm_opportunity_id' => $opp->id, 'tenant_id' => auth()->user()->tenant_id])->pluck('job_id')->first();
                }
            }elseif($job_type=="Cleaning"){ //For Cleaning
                foreach($opportunities as $opp){
                    $job_ids[] = JobsCleaning::where(['crm_opportunity_id' => $opp->id,'tenant_id' => auth()->user()->tenant_id])->pluck('job_id')->first();
                }
            }
            return $job_ids;
    }

    public function viewCustomerLeads($id)
    {
        $this->lead_id = $id;
        $this->crmlead = CRMLeads::find($this->lead_id);
        $this->customer_detail = CustomerDetails::where(['customer_id' => $this->lead_id, 'tenant_id' => auth()->user()->tenant_id])->first();
        if(!$this->customer_detail){
            $this->customer_detail = new CustomerDetails();
            $this->customer_detail=(object) [
                'id' => 0,
                'billing_address' => '',
                'billing_suburb' => '',
                'billing_post_code' => '',
                'account_number' => '',
                'invoice_terms' => '',
                'payment_instructions' => '',
            ];
        }
        $this->crmleadstatuses = CRMLeadStatuses::select('id', 'lead_status')->where('tenant_id', '=', auth()->user()->tenant_id)->orderBy('sort_order', 'asc')->get();

        $this->contact_types = ListTypes::where(['list_types.tenant_id' => auth()->user()->tenant_id, 'list_types.list_name' => 'Contact Type'])
        ->join('list_options', 'list_options.list_type_id', '=', 'list_types.id')
        ->select('list_options.list_option')
        ->get();

        $this->contacts = CRMContacts::where(['lead_id' => $id, 'tenant_id' => auth()->user()->tenant_id, 'deleted' => 'N'])->orderBy('id', 'DESC')->get();
        $this->totalContacts = count($this->contacts);
        $this->google_api_key = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'GoogleMaps'])->pluck('account_key')->first();
        $this->companies = Companies::where('tenant_id', auth()->user()->tenant_id)->get();
        $this->jobs = JobsMoving::select(
            'jobs_moving.job_id',
            'jobs_moving.job_number',
            'jobs_moving.job_date',
            'jobs_moving.job_start_time',
            'jobs_moving.pickup_suburb',
            'jobs_moving.pickup_address',
            'jobs_moving.pickup_state',
            'jobs_moving.delivery_suburb',
            'jobs_moving.drop_off_address',
            'jobs_moving.drop_off_state',
            'jobs_moving.pickup_post_code',
            'jobs_moving.drop_off_post_code',
            'jobs_moving.total_cbm',
            'jobs_moving.job_status',
            'jobs_moving.payment_instructions',
            'jobs_moving.rate_per_cbm',
            'jobs_moving.lead_info',
            'jobs_moving.created_at'
        )
            ->where(['jobs_moving.tenant_id' => auth()->user()->tenant_id, 'jobs_moving.opportunity' => 'N'])
            ->where(['jobs_moving.customer_id' => $id])
            ->orderBy('jobs_moving.job_id', 'desc')
            ->get();



            $this->opportunities = CRMOpportunities::select(
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
                    'jobs_moving.pickup_address',
                    'jobs_moving.pickup_suburb',
                    'jobs_moving.delivery_suburb',
                    'jobs_moving.drop_off_address',
                    'jobs_moving.pickup_post_code',
                    'jobs_moving.drop_off_post_code',
                    'users.name as user_name',
                    'companies.company_name'
                )
                ->where(['crm_opportunities.lead_id' => $id, 'crm_opportunities.tenant_id' => auth()->user()->tenant_id, 'crm_opportunities.deleted'=>0])
                ->where('jobs_moving.opportunity', 'Y')
                ->leftjoin('crm_leads', 'crm_leads.id', '=', 'crm_opportunities.lead_id')
                ->leftjoin('jobs_moving', 'jobs_moving.crm_opportunity_id', 'crm_opportunities.id')
                ->leftjoin('users', 'users.id', '=', 'crm_opportunities.user_id')
                ->leftjoin('companies', 'companies.id', 'jobs_moving.company_id')
                ->orderBy('crm_opportunities.created_at', 'desc')
                ->get();

            $this->mobile = CRMContacts::select('crm_contact_details.detail')
                ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                ->where(['crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.lead_id' => $this->crmlead->id, 'crm_contact_details.detail_type' => 'Mobile'])
                ->pluck('detail')
                ->first();
            $this->email = CRMContacts::select('crm_contact_details.detail')
                ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                ->where(['crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.lead_id' => $this->crmlead->id, 'crm_contact_details.detail_type' => 'Email'])
                ->pluck('detail')
                ->first();
        return view('admin.crm-leads.customer.index', $this->data);
    }

    public function ajaxSaveCustomerDetail(Request $request){
        $crmlead = CRMLeads::find($request->lead_id);
        $crmlead->name=$request->lead_name;
        $crmlead->lead_type=$request->lead_type;
        $crmlead->lead_status=$request->lead_status;
        if($crmlead->save()){
            if($request->customer_detail_id==0){
                $customer_detail = new CustomerDetails();
                $customer_detail->created_by=auth()->user()->id;
            }else{
                $customer_detail = CustomerDetails::find($request->customer_detail_id);
                $customer_detail->updated_by=auth()->user()->id;
            }
            if($customer_detail){
                    $customer_detail->billing_address=$request->billing_address;
                    $customer_detail->billing_suburb=$request->billing_suburb;
                    $customer_detail->billing_post_code=$request->billing_post_code;
                    $customer_detail->customer_id=$crmlead->id;
                    $customer_detail->tenant_id=auth()->user()->tenant_id;
                if($request->lead_type=="Commercial"){
                    $customer_detail->account_number=$request->account_number;
                    $customer_detail->invoice_terms=$request->invoice_terms;
                    $customer_detail->payment_instructions=$request->payment_instructions;
                }else{
                    $customer_detail->account_number=NULL;
                    $customer_detail->invoice_terms=NULL;
                    $customer_detail->payment_instructions=NULL;
                }
                $customer_detail->save();
            }
            $response['error'] = 0;
            $response['message'] = 'Customer detail has been updated successfully';  
            return json_encode($response);  
        }
        

    }
    public function store(Request $request)
    {

        // $company = new Companies();
        // $company->company_name = $request->input('company_name');
        // $company->contact_name = $request->input('contact_name');
        // $company->email = $request->input('email');
        // $company->address = $request->input('address');
        // $company->sms_number = $request->input('sms_number');
        // $company->phone = $request->input('phone');
        // $company->abn = $request->input('abn');
        // $company->default1 = ($request->input('default1') == 'Y' ? 'Y' : '');
        // $company->active = ($request->input('active') == 'Y' ? 'Y' : 'N');
        // $company->created_at = time();
        // $company->updated_at = time();
        // $company->tenant_id = auth()->user()->tenant_id;

        // if ($request->hasFile('image')) {
        //     File::delete('user-uploads/company-logo/' . $company->logo);

        //     $company->logo = $request->image->hashName();
        //     $request->image->store('user-uploads/company-logo');

        //     // resize the image to a width of 300 and constrain aspect ratio (auto height)
        //     $img = Image::make('user-uploads/company-logo/' . $company->logo);
        //     $img->resize(300, null, function ($constraint) {
        //         $constraint->aspectRatio();
        //     });
        //     $img->save();
        // }

        // $company->save();

        // return Reply::redirect(route('admin.companies.index'), __('messages.companyCreated'));
    }

    public function edit($id)
    {
        // $this->company = Companies::findOrFail($id);
        // return view('admin.companies.edit', $this->data);
    }

    public function update(StoreCompany $request, $id)
    {

        // $company = Companies::findOrFail($id);
        // $company->company_name = $request->input('company_name');
        // $company->contact_name = $request->input('contact_name');
        // $company->email = $request->input('email');
        // $company->address = $request->input('address');
        // $company->sms_number = $request->input('sms_number');
        // $company->phone = $request->input('phone');
        // $company->abn = $request->input('abn');
        // $company->default1 = ($request->input('default1') == 'Y' ? 'Y' : '');
        // $company->active = ($request->input('active') == 'Y' ? 'Y' : 'N');
        // $company->updated_at = time();

        // if ($request->hasFile('image')) {
        //     File::delete('user-uploads/company-logo/' . $company->logo);

        //     $company->logo = $request->image->hashName();
        //     $request->image->store('user-uploads/company-logo');

        //     // resize the image to a width of 300 and constrain aspect ratio (auto height)
        //     $img = Image::make('user-uploads/company-logo/' . $company->logo);
        //     $img->resize(300, null, function ($constraint) {
        //         $constraint->aspectRatio();
        //     });
        //     $img->save();
        // }

        // $company->save();

        // return Reply::redirect(route('admin.companies.index'), __('messages.companyUpdated'));
    }

    public function ajaxUpdateLeadStatus(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');

        $lead = CRMLeads::findOrFail($id);
        $lead->lead_status = $status;
        $lead->save();

        $response['status']='success';
        return json_encode($response);
    }

    public function commercialData(Request $request)
    {
        $crmleads = CRMLeads::select('id', 'name', 'lead_status')
                            ->where('tenant_id', '=', auth()->user()->tenant_id)
                            ->where('lead_type', 'Commercial');

        if ($request->created_date_start !== null && $request->created_date_start != 'null' && $request->created_date_start != '') {
                $startDate = Carbon::createFromFormat($this->global->date_format, $request->created_date_start)->toDateString();
                $crmleads = $crmleads->where(DB::raw('DATE(`created_at`)'), '>=', $startDate);
        }
        if ($request->created_date_end !== null && $request->created_date_end != 'null' && $request->created_date_end != '') {
                $created_date_end = Carbon::createFromFormat($this->global->date_format, $request->created_date_end)->toDateString();
                $crmleads = $crmleads->where(DB::raw('DATE(`created_at`)'), '<=', $created_date_end);
        }

        if ($request->lead_status !== null && $request->lead_status != 'null' && $request->lead_status != '') {
                $lead_status = explode(",", $request->lead_status);
                $crmleads = $crmleads->wherein('lead_status', $lead_status);
        }

        if ($request->sorting_order !== null && $request->sorting_order != 'null' && $request->sorting_order != '') {
                
                if($request->sort_descending !== null && $request->sort_descending == '1'){
                    $sortBy = 'desc';
                } else {
                    $sortBy = 'asc';
                }
                $crmleads = $crmleads->orderBy('crm_leads.'.$request->sorting_order, $sortBy);
        }

        $crmleads = $crmleads->get();
        return DataTables::of($crmleads)
            ->editColumn('name', function ($row) {
                return '<a href="' . route("admin.crm-leads.view-customer-leads", $row->id) . '" >' . $row->name . '</a>';
            })
            ->editColumn('mobile', function ($row) {
                $mobile = CRMContacts::select('crm_contact_details.detail')
                ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                ->where(['crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.lead_id' => $row->id, 'crm_contact_details.detail_type' => 'Mobile'])
                ->pluck('detail')
                ->first();
                return $mobile;
            })
            ->editColumn('email', function ($row) {
                $email = CRMContacts::select('crm_contact_details.detail')
                ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                ->where(['crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.lead_id' => $row->id, 'crm_contact_details.detail_type' => 'Email'])
                ->pluck('detail')
                ->first();
                return $email;
            })
            ->rawColumns(['name', 'mobile', 'email'])
            ->removeColumn('id')
            ->make(true);
    }

    public function residentialData(Request $request)
    {
        $crmleads = CRMLeads::select('id', 'name', 'lead_status')
                                ->where('tenant_id', '=', auth()->user()->tenant_id)
                                ->where('lead_type', 'Residential');

        if ($request->created_date_start !== null && $request->created_date_start != 'null' && $request->created_date_start != '') {
                $startDate = Carbon::createFromFormat($this->global->date_format, $request->created_date_start)->toDateString();
                $crmleads = $crmleads->where(DB::raw('DATE(`created_at`)'), '>=', $startDate);
        }
        if ($request->created_date_end !== null && $request->created_date_end != 'null' && $request->created_date_end != '') {
                $created_date_end = Carbon::createFromFormat($this->global->date_format, $request->created_date_end)->toDateString();
                $crmleads = $crmleads->where(DB::raw('DATE(`created_at`)'), '<=', $created_date_end);
        }

        if ($request->lead_status !== null && $request->lead_status != 'null' && $request->lead_status != '') {
                $lead_status = explode(",", $request->lead_status);
                $crmleads = $crmleads->wherein('lead_status', $lead_status);
        }

        if ($request->sorting_order !== null && $request->sorting_order != 'null' && $request->sorting_order != '') {
                
                if($request->sort_descending !== null && $request->sort_descending == '1'){
                    $sortBy = 'desc';
                } else {
                    $sortBy = 'asc';
                }
                $crmleads = $crmleads->orderBy('crm_leads.'.$request->sorting_order, $sortBy);
        }

        $crmleads = $crmleads->get();
        return DataTables::of($crmleads)
            ->editColumn('name', function ($row) {
                return '<a href="' . route("admin.crm-leads.view-customer-leads", $row->id) . '" >' . $row->name . '</a>';
            })
            ->editColumn('mobile', function ($row) {
                $mobile = CRMContacts::select('crm_contact_details.detail')
                ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                ->where(['crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.lead_id' => $row->id, 'crm_contact_details.detail_type' => 'Mobile'])
                ->pluck('detail')
                ->first();
                return $mobile;
            })
            ->editColumn('email', function ($row) {
                $email = CRMContacts::select('crm_contact_details.detail')
                ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                ->where(['crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.lead_id' => $row->id, 'crm_contact_details.detail_type' => 'Email'])
                ->pluck('detail')
                ->first();
                return $email;
            })
            ->rawColumns(['name', 'mobile', 'email'])
            ->removeColumn('id')
            ->make(true);
    }

    public function destroy($id)
    {
        // Companies::destroy($id);
        // return Reply::success(__('messages.companyDeleted'));
    }

    public function export()
    {

        // $companies = Companies::select('companies.id', 'companies.company_name', 'companies.email', 'companies.address', 'companies.contact_name', 'companies.phone', 'companies.abn', 'companies.default1', 'companies.active', 'companies.created_at', 'companies.updated_at')
        //     ->where('companies.tenant_id', '=', auth()->user()->tenant_id);

        // $companies = $companies->orderBy('companies.id', 'desc')->get();

        // // Initialize the array which will be passed into the Excel
        // // generator.
        // $exportArray = [];

        // // Define the Excel spreadsheet headers
        // $exportArray[] = ['ID', 'Company Name', 'Email', 'Address', 'Contact Name', 'Phone', 'ABM', 'Default', 'Active', 'Created At', 'Updated At'];

        // // Convert each member of the returned collection into an array,
        // // and append it to the payments array.
        // foreach ($companies as $row) {
        //     $exportArray[] = $row->toArray();
        // }

        // // Generate and return the spreadsheet
        // Excel::create('companies', function ($excel) use ($exportArray) {

        //     // Set the spreadsheet title, creator, and description
        //     $excel->setTitle('Companies');
        //     $excel->setCreator('Website')->setCompany($this->companyName);
        //     $excel->setDescription('Companies list file');

        //     // Build the spreadsheet, passing in the payments array
        //     $excel->sheet('sheet1', function ($sheet) use ($exportArray) {
        //         $sheet->fromArray($exportArray, null, 'A1', false, false);

        //         $sheet->row(1, function ($row) {

        //             // call row manipulation methods
        //             $row->setFont(array(
        //                 'bold' => true
        //             ));
        //         });
        //     });
        // })->download('xlsx');
    }
    public function ajaxStore(Request $request)
    {       
        // try {
            $op_type = $request->input('op_type');
            if($request->type == 'Residential')
            {
                $validatedData = $request->validate([
                    'lead_name' => 'required',
                    'email' => 'required',
                    'mobile' => 'required',
                    'company_id' => 'required',
                    'op_type' => 'required',
                    'est_job_date' => 'required',
                    'type' => 'required'            
                ]);

                $contact_detail = CRMContactDetail::select('crm_contacts.lead_id')
                        ->join('crm_contacts', 'crm_contact_details.contact_id', '=', 'crm_contacts.id')
                        ->where('crm_contact_details.tenant_id', '=', auth()->user()->tenant_id)
                        ->where('crm_contact_details.detail', '=', $request->input('email'))
                        ->first();
                if ($contact_detail) {
                    $lead_id = $contact_detail->lead_id;

                        $contact = CRMContacts::select('id')->where('lead_id', '=', $lead_id)
                            ->where('tenant_id', '=', auth()->user()->tenant_id)
                            ->where('name', '=', $request->input('lead_name'))->first();
                        if ($contact) {
                            $contact_id = $contact->id;
                        } else {
                            $contact = new CRMContacts();
                            $contact->name = $request->input('lead_name');
                            $contact->lead_id = $lead_id;
                            $contact->tenant_id = auth()->user()->tenant_id;
                            $contact->save();
                            $contact_id = $contact->id;
                        }

                        $contact_detail = CRMContactDetail::select('id')->where('detail', '=', $request['phone'])
                            ->where('tenant_id', '=', auth()->user()->tenant_id)
                            ->where('contact_id', '=', $contact_id)->first();
                        if ($contact_detail) {
                            // do nothing
                        } else {
                            $detail = new CRMContactDetail();
                            $detail->contact_id = $contact_id;
                            $detail->detail_type = 'Mobile';
                            $detail->detail = $request->input('mobile');
                            $detail->tenant_id = auth()->user()->tenant_id;
                            $detail->save();
                        }
                }else{
                    $model = new CRMLeads();
                    $model->name = $request->input('lead_name');
                    $model->lead_type = $request->type;
                    $model->description = $request->input('lead_name');
                    $model->lead_status = 'Potential';
                    $model->tenant_id = auth()->user()->tenant_id;
                    $model->created_by = auth()->user()->id;
                    $model->updated_by = auth()->user()->id;
                    $model->created_at = time();
                    $model->updated_at = time();
                    $model->save();
                    $lead_id = $model->id;

                    $contact = new CRMContacts();
                    $contact->name = $request->input('lead_name');
                    $contact->description = $request->input('lead_name');
                    $contact->lead_id = $model->id;
                    $contact->tenant_id = auth()->user()->tenant_id;
                    $contact->created_by = auth()->user()->id;
                    $contact->updated_by = auth()->user()->id;
                    $contact->created_at = time();
                    $contact->updated_at = time();
                    $contact->save();

                    // Store Contact Detail for Mobile
                    if($request->input('mobile')!=""){
                        $detail = new CRMContactDetail();
                        $detail->detail = $request->input('mobile');
                        $detail->detail_type = 'Mobile';
                        $detail->contact_id = $contact->id;
                        $detail->tenant_id = auth()->user()->tenant_id;
                        $detail->created_by = auth()->user()->id;
                        $detail->updated_by = auth()->user()->id;
                        $detail->created_at = time();
                        $detail->updated_at = time();
                        $detail->save();
                        unset($detail);
                    }
                    if($request->input('email')!=""){
                        // Store Contact Detail for Email
                        $detail = new CRMContactDetail();
                        $detail->detail = $request->input('email');
                        $detail->detail_type = 'Email';
                        $detail->contact_id = $contact->id;
                        $detail->tenant_id = auth()->user()->tenant_id;
                        $detail->created_by = auth()->user()->id;
                        $detail->updated_by = auth()->user()->id;
                        $detail->created_at = time();
                        $detail->updated_at = time();
                        $detail->save();
                        unset($detail);
                    }
                }
            }else{
                $validatedData = $request->validate([
                    'pickup_contact_name' => 'required',
                    'pickup_email' => 'required',
                    'pickup_mobile' => 'required',
                    'customer_id' => 'required',
                    'company_id' => 'required',
                    'op_type' => 'required',
                    'est_job_date' => 'required',
                    'type' => 'required'            
                ]);
                $lead_id = $request->customer_id;
                $contact = DB::table('crm_contacts')->where(['lead_id' => $lead_id, 'tenant_id' => auth()->user()->tenant_id])->first();
            }
            
            //Creating Opportunity
            $data['tenant_id'] = auth()->user()->tenant_id;
            $data['est_job_date'] = Carbon::createFromFormat('d/m/Y', $request->input('est_job_date'))->format('Y-m-d');
            $data['op_type'] = $op_type;
            $data['op_status'] = 'New';
            $data['contact_id'] = $contact->id;
            $data['lead_id'] = $lead_id;        
            $data['created_by'] = auth()->user()->id;
            $data['updated_by'] = auth()->user()->id;
            $data['created_at'] = time();
            $data['updated_at'] = time();
            $opportunity = CRMOpportunities::create($data);

            // Get Minimum Goods Value for Insurance Quote
            $minimum_goods_value = DB::table('jobs_moving_pricing_additional as t1')
                    ->select('t1.*')
                    ->where(['t1.tenant_id' => auth()->user()->tenant_id])->pluck("minimum_goods_value")
                    ->first();

            if($request->type == 'Residential')
            {
                if ($op_type == 'Moving') 
                {
                    $res = JobsMoving::select(DB::raw('MAX(job_number) as max_job_number'))->where('jobs_moving.tenant_id', '=', auth()->user()->tenant_id)->first();
                    $new_job_number = intval($res->max_job_number) + 1;
                    $job = new JobsMoving();
                    $job->tenant_id = auth()->user()->tenant_id;
                    $job->company_id = $request->input('company_id');
                    $job->crm_opportunity_id = $opportunity->id;
                    $job->opportunity = 'Y';
                    $job->job_type = 'Moving';
                    $job->customer_id = $lead_id;
                    $job->job_number = $new_job_number;
                    $job->job_date = $opportunity->est_job_date;
                    $job->created_by = auth()->user()->id;
                    $job->updated_by = auth()->user()->id;
                    $job->created_at = time();
                    $job->updated_at = time();
                    $job->goods_value = $minimum_goods_value;
                    $job->pickup_address = ($request->input('residential_suburb') == 'on') ? "":$request->pickup_address;
                    $job->drop_off_address = ($request->input('residential_suburb') == 'on') ? "":$request->drop_off_address;
                    $job->pickup_post_code = $request->pickup_post_code;
                    $job->drop_off_post_code = $request->drop_off_post_code;
                    $job->pickup_suburb = $request->pickup_suburb;
                    $job->delivery_suburb = $request->delivery_suburb;
                    $job->lead_info = $request->lead_info;
                    $job->save();
                    //--->
                }
                elseif ($op_type == 'Cleaning') 
                {
                    $res = JobsCleaning::select(DB::raw('MAX(job_number) as max_job_number'))->where('jobs_cleaning.tenant_id', '=', auth()->user()->tenant_id)->first();
                    $new_job_number = intval($res->max_job_number) + 1;
                    $job = new JobsCleaning();
                    $job->tenant_id = auth()->user()->tenant_id;
                    $job->company_id = $request->input('company_id');
                    $job->crm_opportunity_id = $opportunity->id;
                    $job->opportunity = 'Y';
                    $job->customer_id = $lead_id;
                    $job->job_number = $new_job_number;
                    $job->job_date = $opportunity->est_job_date;
                    $job->created_by = auth()->user()->id;
                    $job->updated_by = auth()->user()->id;
                    $job->created_at = time();
                    $job->updated_at = time();
                    $job->save();
                    //--->
                }
            }
            else
            { // Creating Commercial Customer
                
                if ($op_type == 'Moving') {
                    $res = JobsMoving::select(DB::raw('MAX(job_number) as max_job_number'))->where('jobs_moving.tenant_id', '=', auth()->user()->tenant_id)->first();
                    $new_job_number = intval($res->max_job_number) + 1;
                    $job = new JobsMoving();
                    $job->tenant_id = auth()->user()->tenant_id;
                    $job->company_id = $request->input('company_id');
                    $job->crm_opportunity_id = $opportunity->id;
                    $job->opportunity = 'Y';
                    $job->job_type = 'Moving';
                    $job->customer_id = $lead_id;
                    $job->job_number = $new_job_number;
                    $job->job_date = $opportunity->est_job_date;
                    $job->created_by = auth()->user()->id;
                    $job->updated_by = auth()->user()->id;
                    $job->created_at = time();
                    $job->updated_at = time();
                    $job->goods_value = $minimum_goods_value;
                    $job->pickup_address = ($request->input('commercial_suburb') == 'on') ? "":$request->pickup_address;
                    $job->drop_off_address = ($request->input('commercial_suburb') == 'on') ? "":$request->drop_off_address;
                    $job->pickup_post_code = $request->pickup_post_code;
                    $job->drop_off_post_code = $request->drop_off_post_code;
                    $job->pickup_suburb = $request->pickup_suburb;
                    $job->delivery_suburb = $request->delivery_suburb;
                    $job->pickup_contact_name = $request->pickup_contact_name;
                    $job->drop_off_contact_name = $request->pickup_contact_name;
                    $job->pickup_email = $request->pickup_email;
                    $job->drop_off_email = $request->pickup_email;
                    $job->pickup_mobile = $request->pickup_mobile;
                    $job->drop_off_mobile = $request->pickup_mobile;
                    $job->save();

                    //--->
                }elseif ($op_type == 'Cleaning') {
                    $res = JobsCleaning::select(DB::raw('MAX(job_number) as max_job_number'))->where('jobs_cleaning.tenant_id', '=', auth()->user()->tenant_id)->first();
                    $new_job_number = intval($res->max_job_number) + 1;
                    $job = new JobsCleaning();
                    $job->tenant_id = auth()->user()->tenant_id;
                    $job->company_id = $request->input('company_id');
                    $job->crm_opportunity_id = $opportunity->id;
                    $job->opportunity = 'Y';
                    $job->customer_id = $lead_id;
                    $job->job_number = $new_job_number;
                    $job->job_date = $opportunity->est_job_date;
                    $job->created_by = auth()->user()->id;
                    $job->updated_by = auth()->user()->id;
                    $job->created_at = time();
                    $job->updated_at = time();
                    $job->save();
                    //--->
                }
            }
            $response['error'] = 0;
            $response['id'] = $lead_id;
            $response['opportunity_id'] = $opportunity->id;
            $response['message'] = 'Opportunity has been added';  
            return json_encode($response);
        // } catch(\Exception $e) {
        //     return $e->getMessage();
        // }     
    }

    public function ajaxFindLeads(Request $request)
    {
        $key = $request->input('lead_name');
        $leads = CRMLeads::where('name', 'like', '%' . $key . '%')->where('tenant_id', '=', auth()->user()->tenant_id)->get();
        
        if ($leads->isNotEmpty()) {
            $response['html'] = view('admin.crm-leads.leads-popup')->with(['leads' => $leads])->render();
        } else {
            $response['html'] = '';
        }
        return json_encode($response);
    }

    public function ajaxFindLeadsByNumber(Request $request)
    {
        $key = $request->input('mobile');
        $crmContactDetails = CRMContactDetail::where('detail', 'like', '%'. $key . '%')->where('tenant_id', auth()->user()->tenant_id)->get();
        // $leads = CRMLeads::where('name', 'like', '%' . $key . '%')->where('tenant_id', '=', auth()->user()->tenant_id)->get();
        // dd($crmContactDetails);
        if ($crmContactDetails->isNotEmpty()) {
            $response['html'] = view('admin.crm-leads.mobile-leads-popup')->with(['crmContactDetails' => $crmContactDetails])->render();
        } else {
            $response['html'] = '';
        }
        return json_encode($response);
    }

    //START:: Task Section
    public function ajaxStoreTask(Request $request)
    {
        $lead_id = $request->input('lead_id');
        $model = new CRMTasks();
        $model->description = $request->input('description');
        $model->task_date = Carbon::createFromFormat('d/m/Y', $request->input('task_date'))->format('Y-m-d');
        $model->task_time = $request->input('task_time');
        $model->lead_id = $request->input('lead_id');
        $model->user_assigned_id = $request->input('user_assigned_id');
        $model->tenant_id = auth()->user()->tenant_id;
        $model->created_by = auth()->user()->id;
        $model->updated_by = auth()->user()->id;
        $model->created_at = time();
        $model->updated_at = time();
        if ($model->save()) {
            $tasks = CRMTasks::where(['tenant_id' => auth()->user()->tenant_id, 'lead_id' => $lead_id])->orderBy('id', 'DESC')->get();
            $users = User::where('tenant_id', '=', auth()->user()->tenant_id)->get();
            $response['error'] = 0;
            $response['id'] = $model->id;
            $response['message'] = 'Task has been added';
            $response['task_count'] = count($tasks);
            $response['task_html'] = view('admin.crm-leads.task_grid')->with(['tasks' => $tasks, 'lead_id' => $lead_id, 'users' => $users])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxUpdateTask(Request $request)
    {
        $lead_id = $request->input('lead_id');
        $task_id = $request->input('task_id');
        $model = CRMTasks::find($task_id);
        $model->description = $request->input('description');
        $model->task_date = Carbon::createFromFormat('d/m/Y', $request->input('task_date'))->format('Y-m-d');
        $model->task_time = $request->input('task_time');
        $model->user_assigned_id = $request->input('user_assigned_id');
        $model->updated_by = auth()->user()->id;
        $model->updated_at = time();
        //print_r($model);exit;
        if ($model->save()) {
            $tasks = CRMTasks::where(['tenant_id' => auth()->user()->tenant_id, 'lead_id' => $lead_id])->orderBy('id', 'DESC')->get();
            $users = User::where('tenant_id', '=', auth()->user()->tenant_id)->get();
            $response['error'] = 0;
            $response['id'] = $task_id;
            $response['message'] = 'Task has been updated';
            $response['task_count'] = count($tasks);
            $response['task_html'] = view('admin.crm-leads.task_grid')->with(['tasks' => $tasks, 'lead_id' => $lead_id, 'users' => $users])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxDestroyTask(Request $request)
    {
        CRMTasks::destroy($request->task_id, $request->lead_id);
        $users = User::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $tasks = CRMTasks::where(['tenant_id' => auth()->user()->tenant_id, 'lead_id' => $request->lead_id])->orderBy('id', 'DESC')->get();
        $response['error'] = 0;
        $response['message'] = 'Task has been deleted';
        $response['task_count'] = count($tasks);
        $response['task_html'] = view('admin.crm-leads.task_grid')->with(['tasks' => $tasks, 'lead_id' => $request->lead_id, 'users' => $users])->render();
        return json_encode($response);
    }
    //END:: Task Section

    //START:: Task Section
    public function ajaxStoreOpportunity(Request $request)
    {
        $lead_id = $request->input('lead_id');
        $op_type = $request->input('op_type');        
        $company_id = $request->input('company_id');
        
        /*$value = $request->input('value');
        if(empty($value) || $value==0){
            $response['error'] = 1;
            $response['message'] = 'Oppotunity value should not be empty or 0';
            return json_encode($response);
        }
        if($op_type=="Moving" && empty($request->input('job_start_time'))){
            $response['error'] = 1;
            $response['message'] = 'Estimated Start Time should not be empty';
            return json_encode($response);
        }
        */
        $data = $request->all();
        unset($data['_token'],$data['company_id'],$data['job_start_time'], $data['preferred_time_range'],$data['lead_info']);
        $data['tenant_id'] = auth()->user()->tenant_id;
        $data['est_job_date'] = Carbon::createFromFormat('d/m/Y', $data['est_job_date'])->format('Y-m-d');
        $data['created_by'] = auth()->user()->id;
        $data['updated_by'] = auth()->user()->id;
        $data['created_at'] = time();
        $data['updated_at'] = time();
        //print_r($data);exit;
        $model = CRMOpportunities::create($data);

        if ($data['op_type'] == 'Moving') {
            $res = JobsMoving::select(DB::raw('MAX(job_number) as max_job_number'))->where('jobs_moving.tenant_id', '=', auth()->user()->tenant_id)->first();
            $new_job_number = intval($res->max_job_number) + 1;
            $job = new JobsMoving();
            $job->tenant_id = auth()->user()->tenant_id;
            $job->company_id = $company_id;
            $job->crm_opportunity_id = $model->id;
            $job->opportunity = 'Y';
            $job->job_type = 'Moving';
            $job->customer_id = $lead_id;
            $job->job_number = $new_job_number;
            $job->job_date = $model->est_job_date;
            $job->job_start_time = $request->input('job_start_time');            
            $job->lead_info = $request->input('lead_info');
            $job->created_by = auth()->user()->id;
            $job->updated_by = auth()->user()->id;
            $job->created_at = time();
            $job->updated_at = time();
            $job->save();
            //--->
        }elseif ($data['op_type'] == 'Cleaning') {
            $res = JobsCleaning::select(DB::raw('MAX(job_number) as max_job_number'))->where('jobs_cleaning.tenant_id', '=', auth()->user()->tenant_id)->first();
            $new_job_number = intval($res->max_job_number) + 1;
            $job = new JobsCleaning();
            $job->tenant_id = auth()->user()->tenant_id;
            $job->company_id = $company_id;
            $job->crm_opportunity_id = $model->id;
            $job->opportunity = 'Y';
            $job->customer_id = $lead_id;
            $job->job_number = $new_job_number;
            $job->job_date = $model->est_job_date;
            $job->preferred_time_range = $request->input('preferred_time_range');
            $job->lead_info = $request->input('lead_info');
            $job->created_by = auth()->user()->id;
            $job->updated_by = auth()->user()->id;
            $job->created_at = time();
            $job->updated_at = time();
            $job->save();
            //--->
        }

         //   $opp = CRMOpportunities::where(['tenant_id' => auth()->user()->tenant_id, 'lead_id' => $lead_id])->orderBy('id', 'DESC')->get();
            // $contacts = CRMContacts::where(['tenant_id' => auth()->user()->tenant_id, 'lead_id' => $lead_id, 'deleted' => 'N'])->orderBy('id', 'DESC')->get();
            // $users = User::where('tenant_id', '=', auth()->user()->tenant_id)->get();
            // $op_type = Lists::sys_job_type();
            // $frequency = ListTypes::where(['list_types.tenant_id' => auth()->user()->tenant_id, 'list_types.list_name' => 'Op Frequency'])
            // ->join('list_options', 'list_options.list_type_id', '=', 'list_types.id')
            // ->select('list_options.list_option')
            // ->get();
            // $op_status = CRMOpPipelineStatuses::where(['tenant_id' => auth()->user()->tenant_id])->orderBy('sort_order', 'ASC')->get();

            $response['error'] = 0;
            $response['id'] = $model->id;
            $response['message'] = 'Opportunity has been added';
            //$response['opp_count'] = count($opp);
            // $response['opp_html'] = view('admin.crm-leads.opportunity_grid')->with(
            //     [
            //         'opportunities' => $opp, 'lead_id' => $lead_id, 'users' => $users, 'contacts' => $contacts,
            //         'op_type' => $op_type, 'frequency' => $frequency, 'op_status' => $op_status, 'global' => $this->global
            //     ]
            // )->render();
            return json_encode($response);        
    }

    public function ajaxUpdateOpportunity(Request $request)
    {
        $lead_id = $request->input('lead_id');
        $opp_id = $request->input('opp_id');
        $company_id = $request->input('company_id');

        /*$value = $request->input('value');
        if(empty($value) || $value==0){
            $response['error'] = 1;
            $response['message'] = 'Oppotunity value should not be empty or 0';
            return json_encode($response);
        }*/

        $data = $request->all();
        unset($data['_token'], $data['lead_id'], $data['opp_id'], $data['company_id'], $data['preferred_time_range'],$data['job_start_time'],$data['lead_info']);
        $data['est_job_date'] = Carbon::createFromFormat('d/m/Y', $data['est_job_date'])->format('Y-m-d');
        $data['updated_by'] = auth()->user()->id;
        $data['updated_at'] = time();

        //print_r($data);exit;
        CRMOpportunities::where(['id' => $opp_id, 'tenant_id' => auth()->user()->tenant_id])->update($data);
        if ($data['op_type'] == 'Moving') {
            JobsMoving::where(['crm_opportunity_id' => $opp_id, 'tenant_id' => auth()->user()->tenant_id])->update([
                'company_id' => $company_id,
                'job_type' => 'Moving',
                'job_date' => date('Y-m-d', strtotime($data['est_job_date'])),
                'job_start_time' => $request->input('job_start_time'),
                'lead_info' => $request->input('lead_info'),
                'updated_by' => auth()->user()->id,
                'updated_at' => time(),
            ]);
            //--->
        }elseif ($data['op_type'] == 'Cleaning') {
            JobsCleaning::where(['crm_opportunity_id' => $opp_id, 'tenant_id' => auth()->user()->tenant_id])->update([
                'company_id' => $company_id,
                'job_date' => date('Y-m-d', strtotime($data['est_job_date'])),
                'preferred_time_range' => $request->input('preferred_time_range'),
                'lead_info' => $request->input('lead_info'),
                'updated_by' => auth()->user()->id,
                'updated_at' => time(),
            ]);
            //--->
        }

        //$opp = CRMOpportunities::where(['tenant_id' => auth()->user()->tenant_id, 'lead_id' => $lead_id])->orderBy('id', 'DESC')->get();
        // $contacts = CRMContacts::where(['tenant_id' => auth()->user()->tenant_id, 'lead_id' => $lead_id, 'deleted' => 'N'])->orderBy('id', 'DESC')->get();
        // $users = User::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        // $op_type = Lists::sys_job_type();
        // $frequency = ListTypes::where(['list_types.tenant_id' => auth()->user()->tenant_id, 'list_types.list_name' => 'Op Frequency'])
        //     ->join('list_options', 'list_options.list_type_id', '=', 'list_types.id')
        //     ->select('list_options.list_option')
        //     ->get();
        // $op_status = CRMOpPipelineStatuses::where(['tenant_id' => auth()->user()->tenant_id])->orderBy('sort_order', 'ASC')->get();

        $response['error'] = 0;
        $response['id'] = $opp_id;
        $response['message'] = 'Opportunity has been updated';
        //$response['opp_count'] = count($opp);
        // $response['opp_html'] = view('admin.crm-leads.opportunity_grid')->with(
        //     [
        //         'opportunities' => $opp, 'lead_id' => $lead_id, 'users' => $users, 'contacts' => $contacts,
        //         'op_type' => $op_type, 'frequency' => $frequency, 'op_status' => $op_status, 'global' => $this->global
        //     ]
        // )->render();
        return json_encode($response);
    }

    public function ajaxDestroyOpportunity(Request $request)
    {
        $response['error'] = 0;
        $response['message'] = 'Opportunity has been deleted';
        $lead_id = $request->lead_id;
        $crm_opportunity_id = $request->opp_id;

        $opportunity = CRMOpportunities::where(['id' => $crm_opportunity_id, 'tenant_id' => auth()->user()->tenant_id])->first();
        if ($opportunity->op_type == 'Moving') {
            $job = JobsMoving::where(['tenant_id' => auth()->user()->tenant_id, 'crm_opportunity_id' => $crm_opportunity_id])->first();
            if ($job) {
                if ($job->opportunity == 'N') {
                    $response['error'] = 2;
                    $response['message'] = 'This Opportunity is already a confirmed booking. It cannot be deleted';
                }
            }            
            if ($response['error'] == 0) {
                $opportunity->deleted=1;
                $opportunity->save();
                JobsMoving::where(['crm_opportunity_id' => $crm_opportunity_id, 'tenant_id' => auth()->user()->tenant_id])->update([
                    'deleted' => 1
                ]);
            }
            //--->
        }elseif ($opportunity->op_type == 'Cleaning') {
            $job = JobsCleaning::where(['tenant_id' => auth()->user()->tenant_id, 'crm_opportunity_id' => $crm_opportunity_id])->first();
            if ($job) {
                if ($job->opportunity == 'N') {
                    $response['error'] = 2;
                    $response['message'] = 'This Opportunity is already a confirmed booking. It cannot be deleted';
                }
            }
            if ($response['error'] == 0) {
                $opportunity->deleted=1;
                $opportunity->save();
                JobsCleaning::where(['crm_opportunity_id' => $crm_opportunity_id, 'tenant_id' => auth()->user()->tenant_id])->update([
                    'deleted' => 1
                ]);
            }
            //--->
        }    
        //---
        // $opp = CRMOpportunities::where(['tenant_id' => auth()->user()->tenant_id, 'lead_id' => $lead_id])->orderBy('id', 'DESC')->get();
        // $users = User::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        // $op_type = Lists::sys_job_type();
        // $frequency = ListTypes::where(['list_types.tenant_id' => auth()->user()->tenant_id, 'list_types.list_name' => 'Op Frequency'])
        //     ->join('list_options', 'list_options.list_type_id', '=', 'list_types.id')
        //     ->select('list_options.list_option')
        //     ->get();
        // $op_status = CRMOpPipelineStatuses::where(['tenant_id' => auth()->user()->tenant_id])->orderBy('sort_order', 'ASC')->get();

        //$response['opp_count'] = count($opp);
        // $response['opp_html'] = view('admin.crm-leads.opportunity_grid')->with(
        //     [
        //         'opportunities' => $opp, 'lead_id' => $lead_id, 'users' => $users,
        //         'op_type' => $op_type, 'frequency' => $frequency, 'op_status' => $op_status, 'global' => $this->global
        //     ]
        // )->render();
        return json_encode($response);
    }
    //END:: Opportunity Section

    //START:: Contact Section
    public function ajaxStoreContact(Request $request)
    {
        $lead_id = $request->input('lead_id');
        $model = new CRMContacts();
        $model->description = $request->input('description');
        $model->name = $request->input('name');
        $model->lead_id = $request->input('lead_id');
        $model->tenant_id = auth()->user()->tenant_id;
        $model->created_by = auth()->user()->id;
        $model->updated_by = auth()->user()->id;
        $model->created_at = time();
        $model->updated_at = time();
        if ($model->save()) {
            //Insert Contact Detail
            $contact_detail = $request->input('contact_detail');
            $contact_detail_type = $request->input('contact_detail_type');
            foreach ($contact_detail as $row => $cd) {
                if (isset($cd) && !empty($cd)) {
                    $detail = new CRMContactDetail();
                    $detail->detail = $cd;
                    $detail->detail_type = $contact_detail_type[$row];
                    $detail->contact_id = $model->id;
                    $detail->tenant_id = auth()->user()->tenant_id;
                    $detail->created_by = auth()->user()->id;
                    $detail->updated_by = auth()->user()->id;
                    $detail->created_at = time();
                    $detail->updated_at = time();
                    $detail->save();
                    unset($detail);
                }
            }
            //----
            $contact_types = ListTypes::where(['list_types.tenant_id' => auth()->user()->tenant_id, 'list_types.list_name' => 'Contact Type'])
            ->join('list_options', 'list_options.list_type_id', '=', 'list_types.id')
            ->select('list_options.list_option')
            ->get();
            $contacts = CRMContacts::where(['tenant_id' => auth()->user()->tenant_id, 'lead_id' => $lead_id, 'deleted' => 'N'])->orderBy('id', 'DESC')->get();
            $response['error'] = 0;
            $response['id'] = $model->id;
            $response['message'] = 'Contact has been added';
            $response['contact_count'] = count($contacts);
            $response['contact_html'] = view('admin.crm-leads.contact_grid')->with(['contacts' => $contacts, 'lead_id' => $lead_id,'contact_types'=>$contact_types])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxUpdateContact(Request $request)
    {
        $lead_id = $request->input('lead_id');
        $contact_id = $request->input('contact_id');

        $data['description'] = $request->input('description');
        $data['name'] = $request->input('name');
        $data['updated_by'] = auth()->user()->id;
        $data['updated_at'] = time();

        //print_r($data);exit;
        CRMContacts::where(['id' => $contact_id, 'tenant_id' => auth()->user()->tenant_id])->update($data);
        //Insert Contact Detail
        $contact_detail = $request->input('contact_detail');
        $contact_detail_type = $request->input('contact_detail_type');
        //delete old contact detail
        DB::delete('delete from crm_contact_details where contact_id = ?', [$request->contact_id]);
        foreach ($contact_detail as $row => $cd) {
            if (isset($cd) && !empty($cd)) {
                $detail = new CRMContactDetail();
                $detail->detail = $cd;
                $detail->detail_type = $contact_detail_type[$row];
                $detail->contact_id = $contact_id;
                $detail->tenant_id = auth()->user()->tenant_id;
                $detail->created_by = auth()->user()->id;
                $detail->updated_by = auth()->user()->id;
                $detail->created_at = time();
                $detail->updated_at = time();
                $detail->save();
                unset($detail);
            }
        }
        //----
        $contact_types = ListTypes::where(['list_types.tenant_id' => auth()->user()->tenant_id, 'list_types.list_name' => 'Contact Type'])
            ->join('list_options', 'list_options.list_type_id', '=', 'list_types.id')
            ->select('list_options.list_option')
            ->get();
        $contacts = CRMContacts::where(['tenant_id' => auth()->user()->tenant_id, 'lead_id' => $lead_id,'deleted' => 'N'])->orderBy('id', 'DESC')->get();
        $response['error'] = 0;
        $response['id'] = $contact_id;
        $response['message'] = 'Contact has been updated';
        $response['contact_count'] = count($contacts);
        $response['contact_html'] = view('admin.crm-leads.contact_grid')->with(['contacts' => $contacts, 'lead_id' => $lead_id,'contact_types'=>$contact_types])->render();
        return json_encode($response);
    }

    public function ajaxDestroyContact(Request $request)
    {
        CRMContacts::where('id',$request->contact_id)->update(['deleted'=>'Y']);
        CRMContactDetail::where('contact_id',$request->contact_id)->update(['deleted'=>'Y']);
        $contact_types = ListTypes::where(['list_types.tenant_id' => auth()->user()->tenant_id, 'list_types.list_name' => 'Contact Type'])
            ->join('list_options', 'list_options.list_type_id', '=', 'list_types.id')
            ->select('list_options.list_option')
            ->get();
        $contacts = CRMContacts::where(['tenant_id' => auth()->user()->tenant_id, 'lead_id' => $request->lead_id, 'deleted' => 'N'])->orderBy('id', 'DESC')->get();
        $response['error'] = 0;
        $response['message'] = 'Contact has been deleted';
        $response['contact_count'] = count($contacts);
        $response['contact_html'] = view('admin.crm-leads.contact_grid')->with(['contacts' => $contacts, 'lead_id' => $request->lead_id,'contact_types'=>$contact_types])->render();
        return json_encode($response);
    }
    //END:: Contact Section

    //START:: Activity Note Section\

    public function ajaxStoreNote(Request $request)
    {
        if($request->input('notes')==''){
            $response['error'] = 1;
            $response['message'] = 'Notes should not be empty.';
            return json_encode($response);
        }
        $lead_id = $request->input('lead_id');
        $job_id = $request->input('job_id');
        $data['log_message'] = $request->input('notes');
        $data['lead_id'] = $lead_id;
        $data['job_id'] = $job_id;
        $data['tenant_id'] = auth()->user()->tenant_id;
        $data['user_id'] = auth()->user()->id;
        $data['log_type'] = 7; // Activity Notes
        $data['log_date'] = Carbon::now();
        $model = CRMActivityLog::create($data);

        if ($request->session()->has('notes_attachment')) {
            $attachments = session('notes_attachment'); 
            foreach($attachments as $attach){
                $attach['attachment_type'] = $attach['name'];
                $attach['attachment_content'] = public_path().$attach['path'];
                $attach['log_id'] = $model->id;
                $attach['tenant_id'] = auth()->user()->tenant_id;
                $attach['created_by'] = auth()->user()->id;
                $attach['updated_by'] = auth()->user()->id;
                $attach['created_at'] = Carbon::now();
                $attach['updated_at'] = Carbon::now();
                $model2 = CRMActivityLogAttachment::create($attach);
            }
        }

        $companies_list = Companies::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $notes = CRMActivityLog::where(['lead_id' => $lead_id, 'tenant_id' => auth()->user()->tenant_id])
            ->whereIn('log_type', array(3, 4, 5, 7, 8, 11, 14, 15))
            ->orderBy('id', 'DESC')
            ->get(); 
        $ppl_people = PplPeople::where(['user_id' => auth()->user()->id, 'tenant_id' => auth()->user()->tenant_id])->first();    
        $response['error'] = 0;
        $response['message'] = 'Note has been added';
        $response['html'] = view('admin.crm-leads.activity_notes_grid')->with(['notes' => $notes, 'lead_id' => $lead_id, 'ppl_people'=>$ppl_people, 'companies_list'=>$companies_list])->render();
        return json_encode($response);
    }

    public function ajaxUpdateNote(Request $request)
    {
        $lead_id = $request->input('lead_id');
        $id = $request->input('id');

        $data['log_message'] = $request->input('notes');
        $data['user_id'] = auth()->user()->id;
        CRMActivityLog::where(['id' => $id, 'tenant_id' => auth()->user()->tenant_id])->update($data);
        
        $companies_list = Companies::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $notes = CRMActivityLog::where(['lead_id' => $lead_id, 'tenant_id' => auth()->user()->tenant_id])
            ->whereIn('log_type', array(3, 4, 5, 7, 8, 11, 14, 15))
            ->orderBy('id', 'DESC')
            ->get(); 
        $ppl_people = PplPeople::where(['user_id' => auth()->user()->id, 'tenant_id' => auth()->user()->tenant_id])->first();    

        $response['error'] = 0;
        $response['message'] = 'Note has been added';
        $response['note'] = $request->input('notes');
        $response['html'] = view('admin.crm-leads.activity_notes_grid')->with(['notes' => $notes, 'lead_id' => $lead_id, 'ppl_people'=>$ppl_people, 'companies_list'=>$companies_list])->render();
        return json_encode($response);
    }

    public function ajaxDestroyNote(Request $request)
    {
        $lead_id = $request->input('lead_id');
        $id = $request->input('id');
        CRMActivityLog::destroy($id);

        $companies_list = Companies::where(['tenant_id' => auth()->user()->tenant_id])->get();
        $notes = CRMActivityLog::where(['lead_id' => $lead_id, 'tenant_id' => auth()->user()->tenant_id])
            ->whereIn('id', array(3, 4, 5, 7, 8, 11, 14, 15))
            ->orderBy('id', 'DESC')
            ->get();    
        $ppl_people = PplPeople::where(['user_id' => auth()->user()->id, 'tenant_id' => auth()->user()->tenant_id])->first();            
        $response['error'] = 0;
        $response['message'] = 'Note has been added';
        $response['html'] = view('admin.crm-leads.activity_notes_grid')->with(['notes' => $notes, 'lead_id' => $lead_id, 'ppl_people'=>$ppl_people, 'companies_list'=>$companies_list])->render();
        return json_encode($response);
    }
    //END:: Activity Note Section

    //START:: Activity SMS Section

    public function getSmsTemplate(Request $request, $id)
    {
        $lead_id = $request->input('lead_id');        
        $job_id = $request->input('job_id');
        $job_type = $request->input('job_type');
        $est_first_leg_start_time="";
        $time_format = 'H:i A';

        $this->sms_template = SMSTemplates::where(['id' => $id, 'tenant_id' => auth()->user()->tenant_id])->first();
        $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', auth()->user()->tenant_id)->first();
        if($this->organisation_settings){
            $time_format=$this->organisation_settings->time_format;
        }
        
        if($lead_id==0){
            //-----request is from Job page
            if($job_type=="Moving"){
                $this->job = JobsMoving::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
                $this->job_leg_start_time = JobsMovingLegs::where(['job_id' => $this->job->job_id, 'tenant_id' => auth()->user()->tenant_id])->pluck("est_start_time")->first();
                if($this->job_leg_start_time){
                    $est_first_leg_start_time=$this->job_leg_start_time;
                } 
            }elseif($job_type=="Cleaning"){
                $this->job = JobsCleaning::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            }    
            //SMS Parameters   
            $crm_contacts = CRMContacts::where(['lead_id' => $this->job->customer_id, 'tenant_id' => auth()->user()->tenant_id, 'deleted' => 'N'])->first();
            $crm_contact_email = CRMContactDetail::where(['contact_id' => $crm_contacts->id, 'detail_type' => 'Email', 'tenant_id' => auth()->user()->tenant_id])->first();
            $crm_contact_phone = CRMContactDetail::where(['contact_id' => $crm_contacts->id, 'detail_type' => 'Mobile', 'tenant_id' => auth()->user()->tenant_id])->first();
            $customer_email = ($crm_contact_email)? $crm_contact_email->detail:'';
            $customer_phone = ($crm_contact_phone)? $crm_contact_phone->detail:'';

            $name = explode(" ", $crm_contacts->name, 2);
            if(count($name)>1){
                $l_firstname = $name[0];
                $l_lastname = $name[1];
            }else{
                $l_firstname = $crm_contacts->name;
                $l_lastname = '';
            }

            $this->invoice = Invoice::where('job_id', '=', $request->job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();

            $this->paidAmount = 0;
            $this->totalAmount = 0;
            if (isset($this->invoice->id)) :
                $this->paidAmount = $this->invoice->getPaidAmount();
                $this->totalAmount = $this->invoice->getTotalAmount();
            endif;
        }else{
            ///-----request is from lead page
            if($job_type=="Moving"){
                $this->job = JobsMoving::where('customer_id', '=', $request->lead_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
                $this->job_leg_start_time = JobsMovingLegs::where(['job_id' => $this->job->job_id, 'tenant_id' => auth()->user()->tenant_id])->pluck("est_start_time")->first();
                if($this->job_leg_start_time){
                    $est_first_leg_start_time=$this->job_leg_start_time;
                } 
            }elseif($job_type=="Cleaning"){
                $this->job = JobsCleaning::where('customer_id', '=', $request->lead_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            }   
            //SMS Parameters            
            $crm_contacts = CRMContacts::where('lead_id', '=', $lead_id)->where('deleted', 'N')->first();
            $crm_contact_email = CRMContactDetail::where(['contact_id' => $crm_contacts->id, 'detail_type' => 'Email', 'tenant_id' => auth()->user()->tenant_id])->first();
            $crm_contact_phone = CRMContactDetail::where(['contact_id' => $crm_contacts->id, 'detail_type' => 'Mobile', 'tenant_id' => auth()->user()->tenant_id])->first();
            $customer_email = ($crm_contact_email)? $crm_contact_email->detail:'';
            $customer_phone = ($crm_contact_phone)? $crm_contact_phone->detail:'';            

            $name = explode(" ", $crm_contacts->name, 2);
            if(count($name)>1){
                $l_firstname = $name[0];
                $l_lastname = $name[1];
            }else{
                $l_firstname = $crm_contacts->name;
                $l_lastname = '';
            }

            $this->invoice = Invoice::where('job_id', '=', $request->job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();

            $this->paidAmount = 0;
            $this->totalAmount = 0;
            if (isset($this->invoice->id)) :
                $this->paidAmount = $this->invoice->getPaidAmount();
                $this->totalAmount = $this->invoice->getTotalAmount();
            endif;
        }
        
        if ($this->sms_template) {

            $ppl_user = PplPeople::where(['user_id' => auth()->user()->id, 'tenant_id' => auth()->user()->tenant_id])->first();
            //$name = explode(" ", $crm_contacts->name, 2);
            if($ppl_user){
                $user_firstname = $ppl_user->first_name;
                $last_lastname = $ppl_user->last_name;
            }else{
                $user_firstname = '';
                $last_lastname = '';
            }

            $external_inventory_form_param = base64_encode('tenant_id='.auth()->user()->tenant_id.'&job_id='.$this->job->job_id);
            $external_inventory_form = request()->getSchemeAndHttpHost().'/removals-inventory-form/'.$external_inventory_form_param;

            $data = [
                'job_id' => $this->job->job_number,
                'first_name' => $l_firstname,
                'last_name' => $l_lastname,
                'pickup_suburb' => $this->job->pickup_suburb,
                'delivery_suburb' => $this->job->delivery_suburb,
                'pickup_address' => $this->job->pickup_address." ".$this->job->pickup_suburb." ".$this->job->pickup_post_code,
                'delivery_address' => $this->job->drop_off_address." ".$this->job->delivery_suburb." ".$this->job->drop_off_post_code,
                'mobile' => $customer_phone,
                'email' => $customer_email,
                'job_date' => date('d-m-Y', strtotime($this->job->job_date)),    
                'user_first_name' => $user_firstname,
                'user_last_name' => $last_lastname,
                'est_start_time' => date($time_format, strtotime($this->job->job_start_time)),
                'est_first_leg_start_time' => date($time_format, strtotime($est_first_leg_start_time)),            
                'total_amount' => $this->totalAmount,
                'total_paid' => $this->paidAmount,
                'total_due' => $this->totalAmount - $this->paidAmount,
                'external_inventory_form' => $external_inventory_form
            ];

            $template = $this->sms_template->sms_message;


            if (preg_match_all("/{(.*?)}/", $template, $m)) {
                foreach ($m[1] as $i => $varname) {
                    $template = str_replace($m[0][$i], sprintf('%s', $data[$varname]), $template);
                }
            }            
            
            echo json_encode(array(
                'error' => 0,
                'status' => 'success',
                'sms_message' => $template
            ));
         } else {
            $response['error'] = 1;
            return json_encode($response);
        }
    }

    public function ajaxSendSms(Request $request)
    {
        //print_r($request->all());exit;
        $lead_id = $request->input('lead_id');
        $job_id = $request->input('job_id');
        $sms_from = $request->input('sms_from');
        $sms_to = $request->input('sms_to');
        $sms_message = $request->input('sms_message');
        $sms_total_credits = $request->input('sms_total_credits');

        $tenant_details = \App\TenantDetail::where('tenant_id', auth()->user()->tenant_id)->first();
        if ($tenant_details->sms_credit < $sms_total_credits) {
            $response['error'] = 1;
            $response['message'] = 'SMS can not be sent. Beacause you have Insufficient credit. Please buy more credits.';
            return json_encode($response);
            exit;
        } else {        
            $sys_api_details = \App\SysApiSettings::where('type', '=', 'sms_gateway')->first();
            $sys_api_details->user;
            $sys_api_details->password;

            $username = $sys_api_details->user;
            $password = $sys_api_details->password;

            $content = 'username=' . rawurlencode($username) .
                '&password=' . rawurlencode($password) .
                '&to=' . rawurlencode($sms_to) .
                '&from=' . rawurlencode($sms_from) .
                '&message=' . rawurlencode($sms_message) .
                '&maxsplit=5'.
                '&ref=' . rawurlencode($lead_id);
            //Send SMS
            $smsbroadcast_response = $this->sendSMSFunc($content);
            $response_lines = explode("\n", $smsbroadcast_response);
            //--
            foreach ($response_lines as $data_line) {
                $message_data = "";
                $message_data = explode(':', $data_line);
                if ($message_data[0] == "OK") {
                    //Update company credit
                    $tenant_total_credits = $tenant_details->sms_credit;
                    $subtractCredits = $tenant_details->sms_credit - $tenant_total_credits;
                    $subtractCredits = $tenant_total_credits - $sms_total_credits;
                    $tenant_details->id = auth()->user()->tenant_id;
                    $UpdateTenantCredits = \App\TenantDetail::where('tenant_id', '=', auth()->user()->tenant_id)->update(array('sms_credit' => $subtractCredits));
                    //---

                    //Run SMS Auto Top Up Program 
                        $this->smsAutoTopUp($tenant_details);
                    //END::----------->

                    //Add Activity Log
                    $data['log_message'] = $sms_message;
                    $data['lead_id'] = $lead_id;
                    $data['job_id'] = $job_id;
                    $data['log_from'] = $sms_from;
                    $data['log_to'] = $sms_to;
                    $data['tenant_id'] = auth()->user()->tenant_id;
                    $data['user_id'] = auth()->user()->id;
                    $data['log_type'] = 8; // Activity SMS
                    $data['log_date'] = Carbon::now();
                    $model = CRMActivityLog::create($data);

                    //Response
                    $companies_list = Companies::where('tenant_id', '=', auth()->user()->tenant_id)->get();
                    $notes = CRMActivityLog::where(['lead_id' => $lead_id, 'tenant_id' => auth()->user()->tenant_id])
                    ->whereIn('log_type', array(3, 4, 5, 7, 8, 11, 14, 15))
                    ->orderBy('id', 'DESC')
                    ->get();
                    $ppl_people = PplPeople::where(['user_id' => auth()->user()->id, 'tenant_id' => auth()->user()->tenant_id])->first(); 
                    $response['error'] = 0;
                    $response['message'] = 'SMS has been sent';
                    $response['html'] = view('admin.crm-leads.activity_notes_grid')->with(['notes' => $notes, 'lead_id' => $lead_id, 'ppl_people'=>$ppl_people, 'companies_list'=>$companies_list])->render();
                    return json_encode($response);
                } elseif ($message_data[0] == "BAD") {
                    $response['error'] = 1;
                    $response['message'] = 'SMS has not been sent to ' . $message_data[1] . '. Reason is ' . $message_data[2];
                    return json_encode($response);
                } elseif ($message_data[0] == "ERROR") {
                    $response['error'] = 1;
                    $response['message'] = 'There was an error in the request. Please try again later!';
                    return json_encode($response);
                }
            }
        }
    }


    protected function smsAutoTopUp($tenant_details)
    {
        if($tenant_details->sms_auto_top_up=='Y' && $tenant_details->sms_credit <= $tenant_details->sms_balance_lower_limit){
            if($tenant_details->stripe_customer_id!=NULL && $tenant_details->stripe_customer_id!=""){

                $sms = \App\SysApiSettings::where('type', 'tenant_sms_purchase')->where('in_use', '1')->first();

                $pay_amount = ((($tenant_details->sms_balance_top_up_qty * $sms->per_unit_cost) * (1+$sms->variable1/100)) + $sms->variable2) * 100;
                $topup_data['tenant_id'] = $tenant_details->tenant_id;
                $topup_data['stripe_customer_id'] = $tenant_details->stripe_customer_id;
                $topup_data['auto_topup'] = 'Y';
                $topup_data['sms_credit'] = $tenant_details->sms_balance_top_up_qty;
                $topup_data['sms_balance_lower_limit'] = $tenant_details->sms_balance_lower_limit;
                $topup_data['sms_balance_top_up_qty'] = $tenant_details->sms_balance_top_up_qty;
                $topup_data['amount'] = $pay_amount;
                $topup_data['stripeToken'] = '';
                $topup_data['stripeEmail'] = '';
                $res = $tenant_details->smsStripeCharge($topup_data);
            }
        }
    }

    protected function sendSMSFunc($content)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.smsbroadcast.com.au/api-adv.php?' . $content);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        if ($output === false) {
            //echo "Error Number:".curl_errno($ch)."<br>";
            //echo "Error String:".curl_error($ch);
        }
        //dd($output[]);
        curl_close($ch);
        return $output;
    }
    //END:: Activity SMS Section

    //START:: Activity EMail Section
    public function getEmailTemplate(Request $request, $id)
    {
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        ini_set('memory_limit', '3000M'); //This might be too large, but depends on the data set

        $request->session()->forget('email_attachment');
        $lead_id = $request->input('lead_id');        
        $job_id = $request->input('job_id');
        $job_type = $request->input('job_type');
        $crm_opportunity_id = $request->input('crm_opportunity_id');

        $quote_file_name='N';
        $invoice_file_name='N';
        $storage_invoice_file_name='N';
        $workorder_file_name='N';
        $pod_file_name='N';
        $insurance_file_name='N';
        $est_first_leg_start_time="";
        $this->job = NULL;
        $time_format = 'H:i A';
        $this->email_template = EmailTemplates::where(['id' => $id, 'tenant_id' => auth()->user()->tenant_id])->first();
        $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', auth()->user()->tenant_id)->first();
        if($this->organisation_settings){
            $time_format=$this->organisation_settings->time_format;
        }
        if($lead_id==0){
            //request is from Job page
            if($job_type=="Moving"){
                $this->job = JobsMoving::find($job_id);
                $this->job_leg_start_time = JobsMovingLegs::where(['job_id' => $this->job->job_id, 'tenant_id' => auth()->user()->tenant_id])->pluck("est_start_time")->first();
                if($this->job_leg_start_time){
                    $est_first_leg_start_time=$this->job_leg_start_time;
                } 
            }elseif($job_type=="Cleaning"){
                $this->job = JobsCleaning::where(['job_id' => $job_id, 'tenant_id' => auth()->user()->tenant_id])->first();
            }
            $opportunity = CRMOpportunities::find($this->job->crm_opportunity_id);
        }else{
            if($job_type=="Moving"){
                $this->job = JobsMoving::where(['job_id' => $job_id, 'tenant_id' => auth()->user()->tenant_id])->first();
                $this->job_leg_start_time = JobsMovingLegs::where(['job_id' => $this->job->job_id, 'tenant_id' => auth()->user()->tenant_id])->pluck("est_start_time")->first();
                if($this->job_leg_start_time){
                    $est_first_leg_start_time=$this->job_leg_start_time;
                }                
            }elseif($job_type=="Cleaning"){
                $this->job = JobsCleaning::where('crm_opportunity_id', '=', $crm_opportunity_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            } 
            $opportunity = CRMOpportunities::find($crm_opportunity_id);
        }
        if($this->job){
            $job_id = $this->job->job_id;
        }
        
        $this->quote = Quotes::where('crm_opportunity_id', '=', $opportunity->id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();

        if($this->email_template->attach_quote=='Y'){            
            if($opportunity){                
                if ($this->quote && $this->quote->quote_file_name != NULL) {
                    $quote_file_url = '/quote-files/' . $this->quote->quote_file_name;
                    $quote_file_name = $this->quote->quote_file_name;
                }
            }
        }
        if($this->email_template->attach_invoice=='Y'){
            if($job_id){
                $this->invoice = Invoice::where(['job_id'=> $job_id,'sys_job_type'=>$job_type])->where('tenant_id', '=', auth()->user()->tenant_id)->first();
                if ($this->invoice && $this->invoice->file_original_name != NULL) {
                    $invoice_file_url = '/invoice-files/' . $this->invoice->file_original_name;
                    $invoice_file_name = $this->invoice->file_original_name;
                }
            }
        }
        if($this->email_template->attach_storage_invoice=='Y'){
            if($job_id){
                $this->invoice = Invoice::where(['job_id'=> $job_id,'sys_job_type'=>'Moving_Storage'])->where('tenant_id', '=', auth()->user()->tenant_id)->first();
                if ($this->invoice && $this->invoice->file_original_name != NULL) {
                    $storage_invoice_file_url = '/invoice-files/' . $this->invoice->file_original_name;
                    $storage_invoice_file_name = $this->invoice->file_original_name;
                }
            }
        }

        if($this->email_template->attach_work_order=='Y'){
            if($this->job!=NULL && $this->job->work_order_file_name != NULL){
                    $workorder_file_url = '/invoice-files/' . $this->job->work_order_file_name;
                    $workorder_file_name = $this->job->work_order_file_name;
            }
        }

        if($this->email_template->attach_pod=='Y'){
            if($this->job!=NULL && $this->job->pod_file_name != NULL){
                    $pod_file_url = '/invoice-files/' . $this->job->pod_file_name;
                    $pod_file_name = $this->job->pod_file_name;
            }
        }

        if($this->email_template->attach_insurance=='Y'){
            $insurance_resp["status"]=0;
            $coverFreight_connected = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'CoverFreight'])->first();
            if($coverFreight_connected){
                if($this->job!=NULL){
                    if($this->job->insurance_file_name != NULL){
                        $insurance_file_url = '/insurance-quote/' . $this->job->insurance_file_name;
                        $insurance_file_name = $this->job->insurance_file_name;
                        $insurance_resp["status"]=1;
                    }else{
                        // call coverfreight api and get insurance quote
                        try{
                            $crmlead_model = new CRMLeads;
                            $insurance_resp = $crmlead_model->generateInsuranceQuote($crm_opportunity_id, auth()->user()->tenant_id);
                            $this->job = JobsMoving::find($job_id);
                            $insurance_file_url = '/insurance-quote/' . $this->job->insurance_file_name;
                            $insurance_file_name = $this->job->insurance_file_name;
                        }catch(Exception $ex){

                        }
                    }
                }
            }
        }
        
        $this->attachments = EmailTemplateAttachments::where(['email_template_id'=>$id,'tenant_id'=>auth()->user()->tenant_id])->get();
        if($lead_id==0){                           
            $crm_contacts = CRMContacts::where(['lead_id' => $this->job->customer_id, 'tenant_id' => auth()->user()->tenant_id, 'deleted' => 'N'])->first();
            $crm_contact_email = CRMContactDetail::where(['contact_id' => $crm_contacts->id, 'detail_type' => 'Email', 'tenant_id' => auth()->user()->tenant_id])->first();
            $crm_contact_phone = CRMContactDetail::where(['contact_id' => $crm_contacts->id, 'detail_type' => 'Mobile', 'tenant_id' => auth()->user()->tenant_id])->first();
            $customer_email = ($crm_contact_email)? $crm_contact_email->detail:'';
            $customer_phone = ($crm_contact_phone)? $crm_contact_phone->detail:'';

            $name = explode(" ", $crm_contacts->name, 2);
            if(count($name)>1){
                $l_firstname = $name[0];
                $l_lastname = $name[1];
            }else{
                $l_firstname = $crm_contacts->name;
                $l_lastname = '';
            }

            $this->invoice = Invoice::where(['job_id'=> $job_id, 'sys_job_type'=>$job_type])->where('tenant_id', '=', auth()->user()->tenant_id)->first();

            $this->paidAmount = 0;
            $this->totalAmount = 0;
            if (isset($this->invoice->id)) :
                $this->paidAmount = $this->invoice->getPaidAmount();
                $this->totalAmount = $this->invoice->getTotalAmount();
            endif;
        }else{              
            //Email Parameters            
            $crm_contacts = CRMContacts::where('lead_id', '=', $lead_id)->where('deleted', 'N')->first();
            if(!$crm_contacts){
                $response['error'] = 1;
                $response['msg'] = "Contact detail not found!";
                return json_encode($response);
            }
            $crm_contact_email = CRMContactDetail::where(['contact_id' => $crm_contacts->id, 'detail_type' => 'Email', 'tenant_id' => auth()->user()->tenant_id])->first();
            $crm_contact_phone = CRMContactDetail::where(['contact_id' => $crm_contacts->id, 'detail_type' => 'Mobile', 'tenant_id' => auth()->user()->tenant_id])->first();
            $customer_email = ($crm_contact_email)? $crm_contact_email->detail:'';
            $customer_phone = ($crm_contact_phone)? $crm_contact_phone->detail:'';            

            $name = explode(" ", $crm_contacts->name, 2);
            if(count($name)>1){
                $l_firstname = $name[0];
                $l_lastname = $name[1];
            }else{
                $l_firstname = $crm_contacts->name;
                $l_lastname = '';
            }

            $this->invoice = Invoice::where(['job_id'=> $job_id, 'sys_job_type'=>$job_type])->where('tenant_id', '=', auth()->user()->tenant_id)->first();

            $this->paidAmount = 0;
            $this->totalAmount = 0;
            if (isset($this->invoice->id)) :
                $this->paidAmount = $this->invoice->getPaidAmount();
                $this->totalAmount = $this->invoice->getTotalAmount();
            endif;
        }
        
        if ($this->email_template) {            
            $ppl_user = PplPeople::where(['user_id' => auth()->user()->id, 'tenant_id' => auth()->user()->tenant_id])->first();
            //$name = explode(" ", $crm_contacts->name, 2);
            if($ppl_user){
                $user_firstname = $ppl_user->first_name;
                $last_lastname = $ppl_user->last_name;
                $user_signature = $ppl_user->email_signature;
            }else{
                $user_firstname = '';
                $last_lastname = '';
                $user_signature = '';
            }
            //inventory_list parameter
            $mov_inv = new JobsMovingInventory();
            $inv_list = $mov_inv->getInventoryListForEmail(auth()->user()->tenant_id, $this->job->job_id);
            //-->
            $external_inventory_form_param = base64_encode('tenant_id='.auth()->user()->tenant_id.'&job_id='.$this->job->job_id);
            $external_inventory_form = request()->getSchemeAndHttpHost()."/removals-inventory-form/".$external_inventory_form_param;
            $external_inventory_form_link = '<a href="'.$external_inventory_form.'" style="cursor:pointer">'.$external_inventory_form.'</a>';
            $external_inventory_form_button = '<a href="'.$external_inventory_form.'" style="color: #fff;background-color: #26a69a;border-radius: 3px;cursor: pointer;padding: 8px 14px;line-height: 30px;" >Inventory Form</a>';
            $stripe = TenantApiDetail::check_stripe();
            $this->quote = Quotes::where('crm_opportunity_id', '=', $opportunity->id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            if($stripe==1  && $this->quote && $this->job){
                $this->url_link="";
                $this->deposit_required="";
                $this->booking_fee="";
                $this->quote_total = 0;
                    if ($this->quote) {
                        $this->quoteItems = QuoteItem::where(['quote_id' => $this->quote->id, 'tenant_id' => auth()->user()->tenant_id])->get();

                        $sub_total = QuoteItem::select(DB::raw('sum(quote_items.amount) as total'))
                        ->where(['quote_items.quote_id' => $this->quote->id, 'quote_items.tenant_id' => auth()->user()->tenant_id])->first();
                        $this->quote_total = $sub_total->total;
                        if($this->quote->deposit_required>0 && $this->quote->deposit=='Y'){
                            $this->deposit_required = $this->quote->deposit_required;
                        }
                    }
                if($opportunity->op_type=="Moving"){
                    $job_price_additional = DB::table('jobs_moving_pricing_additional as t1')
                    ->select('t1.*')
                    ->where(['t1.tenant_id' => auth()->user()->tenant_id])
                    ->first();
                    if($this->deposit_required==0){
                        if ($this->job->price_structure == 'Fixed') {
                            if ($job_price_additional->is_deposit_for_fixed_pricing_fixed_amt == 'Y') {
                                $this->deposit_required = $job_price_additional->deposit_amount_fixed_pricing;
                            } else {
                                $this->deposit_required = $job_price_additional->deposit_percent_fixed_pricing * $this->quote_total;
                            }
                        }else {
                            if($job_price_additional->hourly_pricing_has_booking_fee=='Y'){
                                $this->booking_fee = $job_price_additional->hourly_pricing_booking_fee;
                            }else{
                                if ($job_price_additional->is_deposit_for_hourly_pricing_fixed_amt == 'Y') {
                                    $this->deposit_required = $job_price_additional->deposit_amount_hourly_pricing;
                                } else {
                                    $this->deposit_required = $job_price_additional->deposit_percent_hourly_pricing * $this->quote_total;
                                }
                            }        
                        }
                    }
                 }elseif($opportunity->op_type=="Cleaning"){
                    $jobs_cleaning_auto_quoting = DB::table('jobs_cleaning_auto_quoting as t1')
                    ->select('t1.*')
                    ->where(['t1.tenant_id' => auth()->user()->tenant_id])
                    ->first();
                    $this->deposit_required = $jobs_cleaning_auto_quoting->deposit_amount;
                 }
                 //Book now url
                if($opportunity->op_type=="Moving"){
                    if($this->job->price_structure=='Hourly' && $job_price_additional->hourly_pricing_has_booking_fee=='Y'){
                        $this->url_params = base64_encode('quote_id=' . $this->quote->id . '&booking_fee=' . $this->booking_fee);
                        $this->url_link = request()->getSchemeAndHttpHost() . '/pay-now-booking-fee/' . $this->url_params;
                    }else{
                        $this->url_params = base64_encode('quote_id=' . $this->quote->id . '&deposit_required=' . $this->deposit_required);
                        $this->url_link = request()->getSchemeAndHttpHost() . '/pay-now/' . $this->url_params;
                    }
                }elseif($opportunity->op_type=="Cleaning"){
                    $this->url_params = base64_encode('quote_id=' . $this->quote->id . '&deposit_required=' . $this->deposit_required);
                    $this->url_link = request()->getSchemeAndHttpHost() . '/pay-now/' . $this->url_params;
                }                
                $book_now_button = '<a href="'.$this->url_link.'" style="color: #fff;background-color: #1d3ad2;border-radius: 3px;cursor: pointer;padding: 8px 14px;line-height: 30px;" >BOOK NOW</a>';
                //$book_now_button = $this->url_link;
                //$book_now_button = 'quote_id=' . $this->quote->id . '&booking_fee=' . $this->booking_fee.'&job_id=' . $this->job->job_id .'&job_type=' . $opportunity->op_type;
            }else{
                $book_now_button = "";
            }
///data 
///data in FROM jobs_moving table
            $data = [
                'job_id' => $this->job->job_number,
                'first_name' => $l_firstname,
                'last_name' => $l_lastname,
                'pickup_suburb' => $this->job->pickup_suburb,
                'delivery_suburb' => $this->job->delivery_suburb,
                'pickup_address' => $this->job->pickup_address." ".$this->job->pickup_suburb." ".$this->job->pickup_post_code,
                'delivery_address' => $this->job->drop_off_address." ".$this->job->delivery_suburb." ".$this->job->drop_off_post_code,
                'mobile' => $customer_phone,
                'email' => $customer_email,
                'job_date' => date('d-m-Y', strtotime($this->job->job_date)),                
                'total_amount' => $this->global->currency_symbol.number_format((float)($this->totalAmount), 2, '.', ','),
                'total_paid' => $this->global->currency_symbol.number_format((float)($this->paidAmount), 2, '.', ','),
                'total_due' => $this->global->currency_symbol.number_format((float)($this->totalAmount - $this->paidAmount), 2, '.', ','),
                'external_inventory_form' => $external_inventory_form_link,
                'external_inventory_form_button' => $external_inventory_form_button,
                'inventory_list' => $inv_list,
                'book_now_button' => $book_now_button,
                'user_first_name' => $user_firstname,
                'user_last_name' => $last_lastname,
                'pick_up_access' => ($this->job->pickup_access_restrictions),
                'drop_off_access' => ($this->job->drop_off_access_restrictions),
                

                'est_start_time' => date($time_format, strtotime($this->job->job_start_time)),
                'est_first_leg_start_time' => date($time_format, strtotime($est_first_leg_start_time)),
                'user_email_signature' => $user_signature

            ];




            $subject = $this->email_template->email_subject;
            if (preg_match_all("/{(.*?)}/", $subject, $m)) {
                foreach ($m[1] as $i => $varname) {
                    $subject = str_replace($m[0][$i], sprintf('%s', $data[$varname]), $subject);
                }
            }

            $template = $this->email_template->email_body;


            if (preg_match_all("/{(.*?)}/", $template, $m)) {
                foreach ($m[1] as $i => $varname) {
                    $template = str_replace($m[0][$i], sprintf('%s', $data[$varname]), $template);
                }
            }
            
            $type = 'email';
            $is_attachment=false;  
            //--> Attach Quote
            if($quote_file_name!='N'){
                if ($request->session()->has($type.'_attachment')) {
                    $array = session($type.'_attachment');            
                }else{
                    $array = [];
                }
                $key = $lead_id.'_'.$type.'_'.rand(1,100);
                $attachment = ['key' => $key,'name'=>$quote_file_name,'type'=>$type,'filetype'=>null,'path'=>$quote_file_url];
                $array[$key]=$attachment;
                session([$type.'_attachment'=>$array]); 
                $is_attachment=true;                     
            }
            // Attach Invoice
            if($invoice_file_name!='N'){
                if ($request->session()->has($type.'_attachment')) {
                    $array = session($type.'_attachment');            
                }else{
                    $array = [];
                }
                $key = $type.'_'.rand(1,100);
                $attachment = ['key' => $key,'name'=>$invoice_file_name,'type'=>$type,'filetype'=>null,'path'=>$invoice_file_url];
                $array[$key]=$attachment;
                session([$type.'_attachment'=>$array]);  
                $is_attachment=true;                    
            }

            // Attach Storage Invoice
            if($storage_invoice_file_name!='N'){
                if ($request->session()->has($type.'_attachment')) {
                    $array = session($type.'_attachment');            
                }else{
                    $array = [];
                }
                $key = $type.'_'.rand(1,100);
                $attachment = ['key' => $key,'name'=>$storage_invoice_file_name,'type'=>$type,'filetype'=>null,'path'=>$storage_invoice_file_url];
                $array[$key]=$attachment;
                session([$type.'_attachment'=>$array]);  
                $is_attachment=true;                    
            }

            // Attach Work order invoice
            if($workorder_file_name!='N'){
                if ($request->session()->has($type.'_attachment')) {
                    $array = session($type.'_attachment');            
                }else{
                    $array = [];
                }
                $key = $type.'_'.rand(1,100);
                $attachment = ['key' => $key,'name'=>$workorder_file_name,'type'=>$type,'filetype'=>null,'path'=>$workorder_file_url];
                $array[$key]=$attachment;
                session([$type.'_attachment'=>$array]);     
                $is_attachment=true;                 
            }

            // Attach POD invoice
            if($pod_file_name!='N'){
                if ($request->session()->has($type.'_attachment')) {
                    $array = session($type.'_attachment');            
                }else{
                    $array = [];
                }
                $key = $type.'_'.rand(1,100);
                $attachment = ['key' => $key,'name'=>$pod_file_name,'type'=>$type,'filetype'=>null,'path'=>$pod_file_url];
                $array[$key]=$attachment;
                session([$type.'_attachment'=>$array]);   
                $is_attachment=true;                   
            }
            //-->

            // Attach Insurance Quote
            if($insurance_file_name!='N' && $insurance_resp["status"]!=0){
                if ($request->session()->has($type.'_attachment')) {
                    $array = session($type.'_attachment');            
                }else{
                    $array = [];
                }
                $key = $type.'_'.rand(1,100);
                $attachment = ['key' => $key,'name'=>$insurance_file_name,'type'=>$type,'filetype'=>null,'path'=>$insurance_file_url];
                $array[$key]=$attachment;
                session([$type.'_attachment'=>$array]);   
                $is_attachment=true;             
            }
            //-->

            if($this->attachments){
                foreach($this->attachments as $attach){
                $key = $type.'_'.rand(1,100);
                if ($request->session()->has($type.'_attachment')) {
                    $array = session($type.'_attachment');            
                }else{
                    $array = [];
                }
                $attachment = ['key' => $key,'name'=>$attach->attachment_file_name,'type'=>$type,'filetype'=>null,'path'=>$attach->attachment_file_location];
                $array[$key]=$attachment;
                session([$type.'_attachment'=>$array]);
                }            
                $is_attachment=true;                  
            }

            if($is_attachment==true){
                $attach_html = view('admin.crm-leads.attachment_div')->with(['attachment'=>session($type.'_attachment')])->render();
            }else{
                $attach_html = view('admin.crm-leads.attachment_div')->with(['attachment'=>0])->render();
            }
//email body data         
            echo json_encode(array(
                'error' => 0,
                'status' => 'success',
                'subject' => $subject,
                'body' => $template,
                //'body' => "",
                'attach_html' => $attach_html
            ));
            
         } else {
            $response['error'] = 1;
            return json_encode($response);
        }
    }

    public function ajaxFindContactEmail(Request $request)
    {

        $lead_id = $request->input('lead_id');
        $key = $request->input('key');
        $contacts = DB::table('crm_contacts')
            ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
            ->select('crm_contacts.id', 'crm_contacts.name', 'crm_contact_details.detail')
            ->where(['crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.lead_id' => $lead_id, 'crm_contact_details.detail_type' => 'Email'])
            ->where('crm_contact_details.detail', 'LIKE', "%{$key}%")
            ->get();

        $response = array();
        foreach ($contacts as $contact) {
            $response[] = array("value" => $contact->detail, "label" => $contact->name);
        }

        echo json_encode($response);
    }

    public function ajaxSendEmail(Request $request)
    {
        $attachments = session('email_attachment');
        $data = $request->all();
        if($data['to']==""){
            $response['error'] = 1;
            $response['message'] = 'Please enter sent To email!';
            return json_encode($response);
        }
        $is_reply=$request->input('is_reply');
        $lead_id = $request->input('lead_id');
        $job_id = $request->job_id;
        $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $this->tenant_api_details = \App\TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'PostMarkApp'])->first();
        $data['lead_id'] = $lead_id;
        $data['jobs_moving_id'] = $job_id;
        if($is_reply!=''){
            $data['from_name'] = $this->organisation_settings->company_name;
            $data['from_email'] = $request->input('from');
            $data['reply_to'] = $request->input('from');
        }else{
            $data['from_name'] = $request->input('from_name');
            $data['from_email'] = $request->input('from_email');
            $data['reply_to'] = $request->input('from_email');            
        }        

        if ($request->session()->has('email_attachment')) {
            $attachments = session('email_attachment'); 
            $data['files'] = $attachments;
        }else{
            $data['files']='';
        }
        if($request->job_id)
        {
            $job = JobsMoving::where('job_id', $request->job_id)->first();
            $job->work_order_file_name = $request->template_attachment;
            $job->update();
        }
        Config::set('mail.username', $this->tenant_api_details->smtp_user);
        Config::set('mail.password', $this->tenant_api_details->smtp_secret);

        $to_email = str_replace(' ', '', $data['to']);
        $to_email_array = explode(',', $to_email);
        if(count($to_email_array) > 1){
            foreach($to_email_array as $to){
                    Mail::to($to)->send(new sendMail($data));
            }
        }else{
            Mail::to($to_email)->send(new sendMail($data));
        }
        //Add Activity Log
        $data['log_message'] = $data['email_body'];
        $data['lead_id'] = $lead_id;
        $data['job_id'] = $job_id;
        $data['log_from'] = $data['from_email'];
        $data['log_to'] = $data['to'];
        $data['log_subject'] = $data['email_subject'];
        $data['log_cc'] = $data['cc'];
        $data['log_bcc'] = $data['bcc'];
        $data['tenant_id'] = auth()->user()->tenant_id;
        $data['user_id'] = auth()->user()->id;
        $data['log_type'] = 3; // Activity Email
        $data['log_date'] = Carbon::now();
        $model = CRMActivityLog::create($data);

        if ($request->session()->has('email_attachment')) {
            $attachments = session('email_attachment'); 
            foreach($attachments as $attach){
                $attach['attachment_type'] = isset($attach['name'])?$attach['name']:'Email Attachment';
                $attach['attachment_content'] = public_path().$attach['path'];
                $attach['log_id'] = $model->id;
                $attach['tenant_id'] = auth()->user()->tenant_id;
                $attach['created_by'] = auth()->user()->id;
                $attach['updated_by'] = auth()->user()->id;
                $attach['created_at'] = Carbon::now();
                $attach['updated_at'] = Carbon::now();
                $model2 = CRMActivityLogAttachment::create($attach);
            }
        }

        //Response
        $companies_list = Companies::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $notes = CRMActivityLog::where(['lead_id' => $lead_id, 'tenant_id' => auth()->user()->tenant_id])
            ->whereIn('log_type', array(3, 4, 5, 7, 8, 11, 14, 15))
            ->orderBy('id', 'DESC')
            ->get();
        $ppl_people = PplPeople::where(['user_id' => auth()->user()->id, 'tenant_id' => auth()->user()->tenant_id])->first();
        Session::forget('email_attachment'); 
        $response['error'] = 0;
        $response['message'] = 'Email has been sent';
        $response['html'] = view('admin.crm-leads.activity_notes_grid')->with(['notes' => $notes, 'lead_id' => $lead_id, 'ppl_people'=>$ppl_people, 'companies_list' => $companies_list])->render();
        return json_encode($response);
    }
    //END:: Activity Email Section

    //START::Set Product Description Parameter
    public function ajaxSetProductDescParameter(Request $request){
        $lead_id = $request->lead_id;
        $job_type = $request->job_type;
        $job_id = $request->job_id;
        $description = $request->description;

        $pickup_suburb = "";
        $delivery_suburb = "";
        $pickup_address = "";
        $delivery_address = "";
        $inv_list="";

        //Email Parameters   
        
        $job = JobsMoving::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        if($job){
            $pickup_suburb = $job->pickup_suburb;
            $delivery_suburb = $job->delivery_suburb;
            $pickup_address = $job->pickup_address." ".$job->pickup_suburb." ".$job->pickup_post_code;
            $delivery_address = $job->drop_off_address." ".$job->delivery_suburb." ".$job->drop_off_post_code;
            //inventory_list parameter
            $mov_inv = new JobsMovingInventory();
            $inv_list = $mov_inv->getInventoryListForField(auth()->user()->tenant_id, $job->job_id);
            //-->
        }
        if($lead_id==0){
            $lead_id = $job->customer_id;
        }
        $crm_contacts = CRMContacts::where(['lead_id' => $lead_id, 'tenant_id' => auth()->user()->tenant_id, 'deleted' => 'N'])->first();
        $crm_contact_email = CRMContactDetail::where(['contact_id' => $crm_contacts->id, 'detail_type' => 'Email', 'tenant_id' => auth()->user()->tenant_id])->first();
        $crm_contact_phone = CRMContactDetail::where(['contact_id' => $crm_contacts->id, 'detail_type' => 'Mobile', 'tenant_id' => auth()->user()->tenant_id])->first();
        $customer_email = ($crm_contact_email)? $crm_contact_email->detail:'';
        $customer_phone = ($crm_contact_phone)? $crm_contact_phone->detail:'';            

        $name = explode(" ", $crm_contacts->name, 2);
        if(count($name)>1){
            $l_firstname = $name[0];
            $l_lastname = $name[1];
        }else{
            $l_firstname = $crm_contacts->name;
            $l_lastname = '';
        }        

        $data = [
            'first_name' => $l_firstname,
            'last_name' => $l_lastname,
            'pickup_suburb' => $pickup_suburb,
            'delivery_suburb' => $delivery_suburb,
            'pickup_address' => $pickup_address,
            'delivery_address' => $delivery_address,
            'mobile' => $customer_phone,
            'email' => $customer_email,
            'inventory_list'=>$inv_list
        ];

        if (preg_match_all("/{(.*?)}/", $description, $m)) {
            foreach ($m[1] as $i => $varname) {
                $description = str_replace($m[0][$i], sprintf('%s', $data[$varname]), $description);
            }
        }
        $response['desc'] = $description; 
        return json_encode($response);
    }
    //END::--->
    //START:: Estimate Section
    public function ajaxSaveEstimate(Request $request)
    {
        $opp_id = $request->input('crm_opportunity_id');
        $sys_job_type = $request->input('sys_job_type');
        $quote_id = $request->input('quote_id');
        if($sys_job_type=='Moving' || $sys_job_type=='Moving_Storage'){
            $job = JobsMoving::where('crm_opportunity_id', '=', $opp_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        }elseif($sys_job_type=='Cleaning'){
            $job = JobsCleaning::where('crm_opportunity_id', '=', $opp_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        }

        if($quote_id==0){
            $data['tenant_id'] = auth()->user()->tenant_id;
            $data['crm_opportunity_id'] = $opp_id;
            $data['quote_number'] = $job['job_number'];
            $data['sys_job_type'] = $sys_job_type;
            $data['job_id'] = $job['job_id'];
            $data['quote_date'] = Carbon::now();
            $data['created_by'] = auth()->user()->id;
            $data['created_date'] = Carbon::now();
            $Quote = Quotes::create($data);
        }else{
            $Quote = Quotes::where(['tenant_id'=> auth()->user()->tenant_id, 'id'=>$quote_id])->first();
        }

        if (isset($Quote->id)) {
            $data2['tenant_id'] = auth()->user()->tenant_id;
            $data2['quote_id'] = $Quote->id;
            $data2['product_id'] = $request->input('product_id');
            $data2['name'] = $request->input('name');
            $data2['description'] = $request->input('description');
            $data2['tax_id'] = $request->input('tax_id');
            $data2['unit_price'] = $request->input('unit_price');
            $data2['type'] = $request->input('type');
            $data2['quantity'] = $request->input('quantity');
            $data2['amount'] = $request->input('amount');
            $data2['created_by'] = auth()->user()->id;
            $data2['created_date'] = Carbon::now();
            $QuoteItem = QuoteItem::create($data2);

            //response
            $this->quoteItem = DB::table('quote_items')
                ->select('quote_items.*')
                ->where(['quote_id' => $Quote->id, 'tenant_id' => auth()->user()->tenant_id])
                ->get();
            $this->quote = $Quote;    
            $this->taxs = Tax::where(['tenant_id' => auth()->user()->tenant_id])->get();
            // Estimate Tab Deposit Required
            $this->job = $job;
            $this->job_price_additional = DB::table('jobs_moving_pricing_additional as t1')
            ->select('t1.*')
            ->where(['t1.tenant_id' => auth()->user()->tenant_id])
            ->first();

            if($sys_job_type=='Moving_Storage'){
                $this->products = Product::select("products.*")
                    ->join('product_categories', 'product_categories.id', 'products.category_id')
                    ->where(['products.tenant_id' => auth()->user()->tenant_id,'product_categories.category_name'=>'Storage'])
                    ->get();
                    $response['error'] = 0;
                    $response['message'] = 'Estimate item has been saved';
                    $response['html'] = view('admin.crm-leads.storage.storage_estimate_grid', $this->data)->render();
            }else{
                $this->customer_model = CRMLeads::find($job->customer_id);
                if($this->customer_model->lead_type == 'Residential'){
                    $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_type', 'Residential')
                                                        ->orWhere('customer_type', 'Both');
                                                })->get();
                }else{
                    $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_type', 'Commercial')
                                                        ->orWhere('customer_type', 'Both');
                                                })
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_id', NULL)
                                                        ->orWhere('customer_id', $this->customer_model->id);
                                                })->get();
                }
                $response['error'] = 0;
                $response['message'] = 'Estimate item has been saved';
                $response['html'] = view('admin.crm-leads.estimate_grid', $this->data)->render();
            }                        
            
            return json_encode($response);
        }
    }

    public function ajaxUpdateEstimate(Request $request)
    {
        $opp_id = $request->input('crm_opportunity_id');
        $id = $request->input('id');
        $sys_job_type = $request->input('sys_job_type');
        $quote_id = $request->input('quote_id');
        if($sys_job_type=='Cleaning')
        {
            $job = JobsCleaning::where('crm_opportunity_id', '=', $opp_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        }else{
            $job = JobsMoving::where('crm_opportunity_id', '=', $opp_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        }

        $this->quote = Quotes::where(['tenant_id'=> auth()->user()->tenant_id, 'id'=>$quote_id])->first();

        $data2['name'] = $request->input('name');
        //$data2['product_id'] = $request->input('product_id');
        $data2['description'] = $request->input('description');
        $data2['tax_id'] = $request->input('tax_id');
        $data2['unit_price'] = $request->input('unit_price');
        $data2['quantity'] = $request->input('quantity');
        $data2['type'] = $request->input('type');
        $data2['amount'] = $request->input('amount');
        $data2['updated_by'] = auth()->user()->id;
        $data2['updated_date'] = Carbon::now();
        QuoteItem::where('id', $id)->update($data2);

        //response
        $this->quoteItem = DB::table('quote_items')
                ->select('quote_items.*')
                ->where(['quote_id' => $quote_id, 'tenant_id' => auth()->user()->tenant_id])
                ->get();        
        $this->taxs = Tax::where(['tenant_id' => auth()->user()->tenant_id])->get();       
        
        // Estimate Tab Deposit Required
        $this->job = $job;
        $this->job_price_additional = DB::table('jobs_moving_pricing_additional as t1')
                ->select('t1.*')
                ->where(['t1.tenant_id' => auth()->user()->tenant_id])
                ->first();

        if($this->quote->sys_job_type=="Moving_Storage"){
            $this->products = Product::select("products.*")
                    ->join('product_categories', 'product_categories.id', 'products.category_id')
                    ->where(['products.tenant_id' => auth()->user()->tenant_id,'product_categories.category_name'=>'Storage'])
                    ->get();
            $response['error'] = 0;
            $response['message'] = 'Estimate item has been updated';
            $response['html'] = view('admin.crm-leads.storage.storage_estimate_grid', $this->data)->render();
        }else{
            $this->customer_model = CRMLeads::find($job->customer_id);
                if($this->customer_model->lead_type == 'Residential'){
                    $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_type', 'Residential')
                                                        ->orWhere('customer_type', 'Both');
                                                })->get();
                }else{
                    $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_type', 'Commercial')
                                                        ->orWhere('customer_type', 'Both');
                                                })
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_id', NULL)
                                                        ->orWhere('customer_id', $this->customer_model->id);
                                                })->get();
                }
            $response['error'] = 0;
            $response['message'] = 'Estimate item has been updated';
            $response['html'] = view('admin.crm-leads.estimate_grid', $this->data)->render();
        }        
        return json_encode($response);
    }
    public function ajaxSaveEstimateDiscount(Request $request)
    {
        $quote_id = $request->input('quote_id');
        $discount_type = $request->input('discount_type');
        $discount = $request->input('discount');

        $opp_id = $request->input('crm_opportunity_id');
        $sys_job_type = $request->input('sys_job_type');
        if($sys_job_type=='Cleaning')
        {
            $job = JobsCleaning::where('crm_opportunity_id', '=', $opp_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        }else{
            $job = JobsMoving::where('crm_opportunity_id', '=', $opp_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        }

        $this->quote = Quotes::where(['tenant_id'=> auth()->user()->tenant_id, 'id'=>$quote_id])->first();
        if($this->quote){
            $this->quote->discount=$discount;
            $this->quote->discount_type=$discount_type;
            $this->quote->save();
        }
        $this->quoteItem = DB::table('quote_items')
                ->select('quote_items.*')
                ->where(['quote_id' => $quote_id, 'tenant_id' => auth()->user()->tenant_id])
                ->get();
        $this->taxs = Tax::where(['tenant_id' => auth()->user()->tenant_id])->get();

        ////response--->

        // Estimate Tab Deposit Required
        $this->job = $job;
        $this->job_price_additional = DB::table('jobs_moving_pricing_additional as t1')
                ->select('t1.*')
                ->where(['t1.tenant_id' => auth()->user()->tenant_id])
                ->first();
        if($this->quote->sys_job_type=="Moving_Storage"){
            $this->products = Product::select("products.*")
                    ->join('product_categories', 'product_categories.id', 'products.category_id')
                    ->where(['products.tenant_id' => auth()->user()->tenant_id,'product_categories.category_name'=>'Storage'])
                    ->get();
            $response['error'] = 0;
            $response['message'] = 'Discount has been updated';
            $response['html'] = view('admin.crm-leads.storage.storage_estimate_grid', $this->data)->render();
        }else{
            $this->customer_model = CRMLeads::find($job->customer_id);
                if($this->customer_model->lead_type == 'Residential'){
                    $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_type', 'Residential')
                                                        ->orWhere('customer_type', 'Both');
                                                })->get();
                }else{
                    $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_type', 'Commercial')
                                                        ->orWhere('customer_type', 'Both');
                                                })
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_id', NULL)
                                                        ->orWhere('customer_id', $this->customer_model->id);
                                                })->get();
                }
            $response['error'] = 0;
            $response['message'] = 'Discount has been updated';
            $response['html'] = view('admin.crm-leads.estimate_grid', $this->data)->render();
        }        
        return json_encode($response);
    }  
    public function ajaxSaveDepositRequired(Request $request)
    {
        $quote_id = $request->input('quote_id');
        $deposit = $request->input('deposit');
        if($deposit=='Y'){
            $deposit_required = $request->input('deposit_required');
        }else{
            $deposit_required = 0;
        }
        

        $opp_id = $request->input('crm_opportunity_id');
        $sys_job_type = $request->input('sys_job_type');
        if($sys_job_type=='Moving' || $sys_job_type=='Moving_Storage'){
            $job = JobsMoving::where('crm_opportunity_id', '=', $opp_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        }elseif($sys_job_type=='Cleaning'){
            $job = JobsCleaning::where('crm_opportunity_id', '=', $opp_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        }

        $this->quote = Quotes::where(['tenant_id'=> auth()->user()->tenant_id, 'id'=>$quote_id])->first();
        if($this->quote){
            $this->quote->deposit=$deposit;
            $this->quote->deposit_required=$deposit_required;
            $this->quote->save();
        }
        $this->quoteItem = DB::table('quote_items')
                ->select('quote_items.*')
                ->where(['quote_id' => $quote_id, 'tenant_id' => auth()->user()->tenant_id])
                ->get();
        $this->taxs = Tax::where(['tenant_id' => auth()->user()->tenant_id])->get();

        ////response--->

        // Estimate Tab Deposit Required
        $this->job = $job;
        $this->job_price_additional = DB::table('jobs_moving_pricing_additional as t1')
                ->select('t1.*')
                ->where(['t1.tenant_id' => auth()->user()->tenant_id])
                ->first();
        if($this->quote->sys_job_type=="Moving_Storage"){
            $this->products = Product::select("products.*")
                    ->join('product_categories', 'product_categories.id', 'products.category_id')
                    ->where(['products.tenant_id' => auth()->user()->tenant_id,'product_categories.category_name'=>'Storage'])
                    ->get();
            $response['error'] = 0;
            $response['message'] = 'Discount has been updated';
            $response['html'] = view('admin.crm-leads.storage.storage_estimate_grid', $this->data)->render();
        }else{
            $this->customer_model = CRMLeads::find($job->customer_id);
                if($this->customer_model->lead_type == 'Residential'){
                    $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_type', 'Residential')
                                                        ->orWhere('customer_type', 'Both');
                                                })->get();
                }else{
                    $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_type', 'Commercial')
                                                        ->orWhere('customer_type', 'Both');
                                                })
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_id', NULL)
                                                        ->orWhere('customer_id', $this->customer_model->id);
                                                })->get();
                }
            $response['error'] = 0;
            $response['message'] = 'Deposit required has been updated';
            $response['html'] = view('admin.crm-leads.estimate_grid', $this->data)->render();
        }        
        return json_encode($response);
    }   

    public function ajaxDestroyQuoteItem(Request $request)
    {
        $id = $request->input('id');
        $quote_id = $request->input('quote_id');
        $opp_id = $request->input('crm_opportunity_id');
        $sys_job_type = $request->input('sys_job_type');
        if($sys_job_type=='Cleaning')
        {
            $job = JobsCleaning::where('crm_opportunity_id', '=', $opp_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        }else{
            $job = JobsMoving::where('crm_opportunity_id', '=', $opp_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        }
        QuoteItem::destroy($id);

        //response
        $this->quote = Quotes::where(['tenant_id'=> auth()->user()->tenant_id, 'id'=>$quote_id])->first();

        $this->quoteItem = DB::table('quote_items')
                ->select('quote_items.*')
                ->where(['quote_id' => $quote_id, 'tenant_id' => auth()->user()->tenant_id])
                ->get();
        $this->taxs = Tax::where(['tenant_id' => auth()->user()->tenant_id])->get();       

        // Estimate Tab Deposit Required
        $this->job = $job;
        $this->job_price_additional = DB::table('jobs_moving_pricing_additional as t1')
        ->select('t1.*')
        ->where(['t1.tenant_id' => auth()->user()->tenant_id])
        ->first();

        if($this->quote->sys_job_type=="Moving_Storage"){
            $this->products = Product::select("products.*")
                    ->join('product_categories', 'product_categories.id', 'products.category_id')
                    ->where(['products.tenant_id' => auth()->user()->tenant_id,'product_categories.category_name'=>'Storage'])
                    ->get();
            $response['error'] = 0;
            $response['message'] = 'Estimate item has been deleted';
            $response['html'] = view('admin.crm-leads.storage.storage_estimate_grid', $this->data)->render();
        }else{
            $this->customer_model = CRMLeads::find($job->customer_id);
                if($this->customer_model->lead_type == 'Residential'){
                    $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_type', 'Residential')
                                                        ->orWhere('customer_type', 'Both');
                                                })->get();
                }else{
                    $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_type', 'Commercial')
                                                        ->orWhere('customer_type', 'Both');
                                                })
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_id', NULL)
                                                        ->orWhere('customer_id', $this->customer_model->id);
                                                })->get();
                }
            $response['error'] = 0;
            $response['message'] = 'Estimate item has been deleted';
            $response['html'] = view('admin.crm-leads.estimate_grid', $this->data)->render();
        }       

        return json_encode($response);
    }

    public function ajaxLoadQuoteItem(Request $request)
    {

        $crm_opportunity_id = $request->input('crm_opportunity_id');
        $opportunity = CRMOpportunities::find($crm_opportunity_id);
        $this->job = JobsMoving::where('crm_opportunity_id', '=', $crm_opportunity_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();

        //response
        $this->quote = Quotes::where(['tenant_id' => auth()->user()->tenant_id, 'crm_opportunity_id'=>$crm_opportunity_id])->first();
        if($this->quote){
            $this->quoteItem = DB::table('quote_items')
                ->select('quote_items.*')
                ->where(['quote_id' => $this->quote->id, 'tenant_id' => auth()->user()->tenant_id])
                ->get();
        }else{
            $this->quote = NULL;
            $this->quoteItem = NULL;
        }
        
        $this->taxs = Tax::where(['tenant_id' => auth()->user()->tenant_id])->get();

        // Estimate Tab Deposit Required
        $this->job_price_additional = DB::table('jobs_moving_pricing_additional as t1')
                ->select('t1.*')
                ->where(['t1.tenant_id' => auth()->user()->tenant_id])
                ->first();

        $this->customer_model = CRMLeads::find($opportunity->lead_id);
                if($this->customer_model->lead_type == 'Residential'){
                    $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_type', 'Residential')
                                                        ->orWhere('customer_type', 'Both');
                                                })->get();
                }else{
                    $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_type', 'Commercial')
                                                        ->orWhere('customer_type', 'Both');
                                                })
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_id', NULL)
                                                        ->orWhere('customer_id', $this->customer_model->id);
                                                })->get();
                }
        $this->products = Product::where(['tenant_id' => auth()->user()->tenant_id])->get();

        $response['error'] = 0;
        $response['message'] = 'Estimate item has been saved';
        $response['html'] = view('admin.crm-leads.estimate_grid', $this->data)->render();
        return json_encode($response);
    }


    public function generateQuote($opportunity_id)
    {
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        ini_set('memory_limit', '3000M'); //This might be too large, but depends on the data set

        $model = new CRMLeads;
        $response['error'] = 1;
        $response['message'] = 'Some error occured, try again later.';

        $result = $model->generateQuote($opportunity_id,$this->global);
        return json_encode($result);
    }

    public function downloadQuote($opportunity_id)
    {
        $response['error'] = 1;
        $download = Input::get("force");

        try {
            $this->quote = Quotes::where('crm_opportunity_id', '=', $opportunity_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            if ($this->quote) {
                $file_url = public_path('quote-files') . '/' . $this->quote->quote_file_name;
                if (!empty($this->quote->quote_file_name) && file_exists($file_url)) {
                    if (isset($download) && $download == '1') {
                        return response()->download($file_url);
                    } else {
                        // $response['error'] = 0;
                        // $response['url'] = route('admin.crm-leads.downloadQuote', [$opportunity_id]) . '?force=1&key=' . rand(1111, 9999);
                        $response['error'] = 0;
                        $response['url'] = url('quote-files') . '/' . $this->quote->quote_file_name;
                    }
                }
            }
            return json_encode($response);
        } catch (Exception $ex) {
            $response['error'] = 1;
            $response['message'] = $ex->getMessage();
            return json_encode($response);
        }
    }

    public function generateInsuranceQuote($opportunity_id)
    {
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        ini_set('memory_limit', '3000M'); //This might be too large, but depends on the data set
        $model = new CRMLeads;
        $result = $model->generateInsuranceQuote($opportunity_id, auth()->user()->tenant_id);
        return json_encode($result);
    }

    public function downloadInsuranceQuote($opportunity_id)
    {
        $response['error'] = 1;
        $download = Input::get("force");

        try {
            $job = JobsMoving::where('crm_opportunity_id', '=', $opportunity_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            if ($job) {
                $file_url = public_path('insurance-quote') . '/' . $job->insurance_file_name;
                if (!empty($job->insurance_file_name) && file_exists($file_url)) {
                    if (isset($download) && $download == '1') {
                        return response()->download($file_url);
                    } else {
                        $response['error'] = 0;
                        $response['url'] = url('insurance-quote') . '/' . $job->insurance_file_name;
                    }
                }
            }
            return json_encode($response);
        } catch (Exception $ex) {
            $response['error'] = 1;
            $response['message'] = $ex->getMessage();
            return json_encode($response);
        }
    }
    //END:: Estimate Section

    //START:: Removal Section
    public function ajaxLoadJobDetail(Request $request)
    {
        // dd($request->all());
        $crm_opportunity_id = $request->input('crm_opportunity_id');
        //response
        $this->removal_opportunities = CRMOpportunities::where(['tenant_id' => auth()->user()->tenant_id, 'id' => $crm_opportunity_id])->first();
        if($this->removal_opportunities){
            $job_type=$this->removal_opportunities->op_type;
        }else{
            $job_type="Moving";
        }
        if ($this->removal_opportunities) {
            if($job_type=="Moving"){
                $this->removal_jobs_moving = JobsMoving::where(['tenant_id' => auth()->user()->tenant_id, 'crm_opportunity_id' => $this->removal_opportunities->id])->first();
            }elseif($job_type=="Cleaning"){
                $this->cleaning_shifts = JobsCleaningShifts::where(['tenant_id'=> auth()->user()->tenant_id])->get();
                $this->jobs_cleaning = JobsCleaning::where(['tenant_id' => auth()->user()->tenant_id, 'crm_opportunity_id' => $this->removal_opportunities->id])->first();
                $this->jobs_cleaning_additional = JobsCleaningAdditionalInfo::where(['tenant_id' => auth()->user()->tenant_id, 'job_id' => $this->jobs_cleaning->job_id])->get();
            }
            
        }
        $this->removal_companies = Companies::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $this->removal_pipeline_statuses = CRMOpPipelineStatuses::where('tenant_id', '=', auth()->user()->tenant_id)->orderBy('sort_order', 'asc')->get();
        $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $this->sys_country_states = SysCountryStates::where('country_id', '=', $this->organisation_settings->business_country_id)->get();

        $this->property_types = PropertyCategoryOptions::select('options')->where('tenant_id', '=', auth()->user()->tenant_id)->where('category_id', '=', '1')->get();
        $this->furnishing = PropertyCategoryOptions::select('options')->where('tenant_id', '=', auth()->user()->tenant_id)->where('category_id', '=', '2')->get();
        $this->bedroom = PropertyCategoryOptions::select('options')->where('tenant_id', '=', auth()->user()->tenant_id)->where('category_id', '=', '3')->get();
        $this->living_room = PropertyCategoryOptions::select('options')->where('tenant_id', '=', auth()->user()->tenant_id)->where('category_id', '=', '4')->get();
        $this->other_room = PropertyCategoryOptions::select('options')->where('tenant_id', '=', auth()->user()->tenant_id)->where('category_id', '=', '5')->get();
        $this->special_item = PropertyCategoryOptions::select('options')->where('tenant_id', '=', auth()->user()->tenant_id)->where('category_id', '=', '6')->get();
        
        $response['error'] = 0;
        $response['type'] = $job_type;
        if($job_type=="Moving"){
            $response['html'] = view('admin.crm-leads.removals.index', $this->data)->render();
        }elseif($job_type=="Cleaning"){
            $response['html'] = view('admin.crm-leads.cleaning.index', $this->data)->render();
        }
        return json_encode($response);
    }

    public function ajaxUpdateRemovalBookingDetail(Request $request)
    {
        $lead_id = $request->input('lead_id');
        $opp_id = $request->input('opp_id');

        $opportunity = CRMOpportunities::where(['id' => $opp_id, 'tenant_id' => auth()->user()->tenant_id])->first();

        $data = $request->all();

        CRMOpportunities::where(['id' => $opp_id, 'tenant_id' => auth()->user()->tenant_id])->update([
            'op_status' => $data['op_status'],
            'updated_by' => auth()->user()->id,
            'updated_at' => time()
        ]);

        $view_data['removal_opportunities'] = CRMOpportunities::where(['tenant_id' => auth()->user()->tenant_id, 'id' => $opp_id])->first();

        if($opportunity->op_type=="Moving"){
            JobsMoving::where(['crm_opportunity_id' => $opp_id, 'tenant_id' => auth()->user()->tenant_id])->update([
                'job_date' => Carbon::createFromFormat('d/m/Y', $data['job_date'])->format('Y-m-d'),
                'company_id' => $data['company_id'],
                'updated_by' => auth()->user()->id,
                'updated_at' => time()
            ]);
            $view_data['removal_jobs_moving'] = JobsMoving::where(['tenant_id' => auth()->user()->tenant_id, 'crm_opportunity_id' => $opp_id])->first();
        }elseif($opportunity->op_type=="Cleaning"){
            JobsCleaning::where('crm_opportunity_id', $opp_id)->update([
                'job_date' => Carbon::createFromFormat('d/m/Y', $data['job_date'])->format('Y-m-d'),
                'company_id' => $data['company_id'],
                'updated_by' => auth()->user()->id,
                'updated_at' => time()
            ]);
            $view_data['jobs_cleaning'] = JobsCleaning::where(['tenant_id' => auth()->user()->tenant_id, 'crm_opportunity_id' => $view_data['removal_opportunities']->id])->first();
        }

        $view_data['lead_id'] = $lead_id;        
        $view_data['removal_companies'] = Companies::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $view_data['removal_pipeline_statuses'] = CRMOpPipelineStatuses::where('tenant_id', '=', auth()->user()->tenant_id)->orderBy('sort_order', 'asc')->get();
        $view_data['global'] = $this->global;
        $response['error'] = 0;
        $response['message'] = 'Record has been updated';

        if($opportunity->op_type=="Moving"){
            $response['type']="Moving";
            $response['res_html'] = view('admin.crm-leads.removals.booking_detail_grid')->with($view_data)->render();
        }elseif($opportunity->op_type=="Cleaning"){
            $response['type']="Cleaning";
            $response['res_html'] = view('admin.crm-leads.cleaning.booking_detail_grid')->with($view_data)->render();
        }
        return json_encode($response);
    }

    public function ajaxUpdateRemovalPropertyDetail(Request $request)
    {
        $lead_id = $request->input('lead_id');
        $opp_id = $request->input('opp_id');        
        $data = $request->all();

        JobsMoving::where(['crm_opportunity_id' => $opp_id, 'tenant_id' => auth()->user()->tenant_id])->update([
            'pickup_property_type' => $data['pickup_property_type'],
            'pickup_furnishing' => $data['pickup_furnishing'],
            'pickup_bedrooms' => $data['pickup_bedrooms'],
            'pickup_living_areas' => $data['pickup_living_areas'],
            'pickup_other_rooms' =>  @implode(",", $data['other_room']),
            'pickup_speciality_items' =>  @implode(",", $data['special_item']),
            'other_instructions' => $data['other_instructions']
        ]);
        $view_data['lead_id'] = $lead_id;   
        $view_data['removal_opportunities'] = CRMOpportunities::where(['tenant_id' => auth()->user()->tenant_id, 'id' => $opp_id])->first();         
        $view_data['removal_jobs_moving'] = JobsMoving::where(['tenant_id' => auth()->user()->tenant_id, 'crm_opportunity_id' => $opp_id])->first();
        $view_data['property_types'] = PropertyCategoryOptions::select('options')->where('tenant_id', '=', auth()->user()->tenant_id)->where('category_id', '=', '1')->get();
        $view_data['furnishing'] = PropertyCategoryOptions::select('options')->where('tenant_id', '=', auth()->user()->tenant_id)->where('category_id', '=', '2')->get();
        $view_data['bedroom'] = PropertyCategoryOptions::select('options')->where('tenant_id', '=', auth()->user()->tenant_id)->where('category_id', '=', '3')->get();
        $view_data['living_room'] = PropertyCategoryOptions::select('options')->where('tenant_id', '=', auth()->user()->tenant_id)->where('category_id', '=', '4')->get();
        $view_data['other_room'] = PropertyCategoryOptions::select('options')->where('tenant_id', '=', auth()->user()->tenant_id)->where('category_id', '=', '5')->get();
        $view_data['special_item'] = PropertyCategoryOptions::select('options')->where('tenant_id', '=', auth()->user()->tenant_id)->where('category_id', '=', '6')->get();

        $view_data['global'] = $this->global;
        $response['error'] = 0;
        $response['message'] = 'Record has been updated';
        $response['res_html'] = view('admin.crm-leads.removals.property_detail_grid')->with($view_data)->render();
        return json_encode($response);
    }

    public function ajaxUpdateRemovalMovingFrom(Request $request)
    {
        $lead_id = $request->input('lead_id');
        $opp_id = $request->input('opp_id');
        $job_id = $request->input('job_id');
        $opportunity = CRMOpportunities::where('id', $opp_id)->first();
        $view_data['removal_opportunities'] = CRMOpportunities::where(['tenant_id' => auth()->user()->tenant_id, 'id' => $opp_id])->first();
        $view_data['organisation_settings'] = OrganisationSettings::where('tenant_id', '=', auth()->user()->tenant_id)->first();        

        $data = $request->all();
        if($opportunity->op_type=="Moving"){
            JobsMoving::where(['job_id' => $job_id, 'tenant_id' => auth()->user()->tenant_id])->update([
                'pickup_address' => $data['pickup_address'],
                'pickup_suburb' => $data['pickup_suburb'],
                'pickup_post_code' => $data['pickup_post_code'],
                'pickup_access_restrictions' => $data['pickup_access_restrictions'],
                'pickup_bedrooms' => $data['pickup_bedrooms'],
                'pickup_contact_name' => isset($data['pickup_contact_name']) ? $data['pickup_contact_name'] : '',
                'pickup_mobile' => isset($data['pickup_mobile']) ? $data['pickup_mobile'] : '',
                'pickup_email' => isset($data['pickup_email']) ? $data['pickup_email'] : ''
            ]);
            $view_data['removal_jobs_moving'] = JobsMoving::where(['job_id' => $job_id, 'tenant_id' => auth()->user()->tenant_id])->first();
            $view_data['sys_country_states'] = SysCountryStates::where('country_id', '=', $view_data['organisation_settings']->business_country_id)->get();
        }
        elseif($opportunity->op_type=="Cleaning"){
            JobsCleaning::where(['crm_opportunity_id' => $opp_id, 'tenant_id' => auth()->user()->tenant_id])->update([
                'preferred_time_range' => $data['preferred_time_range'],
                'address' => $data['address'],
                'bedrooms' => $data['bedrooms'],
                'bathrooms' => $data['bathrooms']
            ]);
            $view_data['jobs_cleaning'] = JobsCleaning::where(['tenant_id' => auth()->user()->tenant_id, 'crm_opportunity_id' => $opp_id])->first();
            $view_data['cleaning_shifts'] = JobsCleaningShifts::where(['tenant_id'=> auth()->user()->tenant_id])->get();
        }

        $view_data['lead_id'] = $lead_id;                        
        $view_data['global'] = $this->global;
        $response['error'] = 0;
        $response['message'] = 'Record has been updated';

        if($opportunity->op_type=="Moving"){
            $response['res_html'] = view('admin.crm-leads.removals.moving_from_grid')->with($view_data)->render();
        }elseif($opportunity->op_type=="Cleaning"){
            $response['res_html'] = view('admin.crm-leads.cleaning.bottom-left')->with($view_data)->render();
        }
        return json_encode($response);
    }

    public function ajaxUpdateRemovalMovingTo(Request $request)
    {
        $lead_id = $request->input('lead_id');
        $opp_id = $request->input('opp_id');
        $job_id = $request->input('job_id');

        $opportunity = CRMOpportunities::where('id', $opp_id)->first();
        $view_data['removal_opportunities'] = CRMOpportunities::where(['tenant_id' => auth()->user()->tenant_id, 'id' => $opp_id])->first();
        $view_data['organisation_settings'] = OrganisationSettings::where('tenant_id', '=', auth()->user()->tenant_id)->first();        

        $data = $request->all();

        if($opportunity->op_type=="Moving"){
            JobsMoving::where(['job_id' => $job_id, 'tenant_id' => auth()->user()->tenant_id])->update([
                'drop_off_address' => $data['drop_off_address'],
                'delivery_suburb' => $data['delivery_suburb'],
                'drop_off_post_code' => $data['drop_off_post_code'],
                'drop_off_access_restrictions' => $data['drop_off_access_restrictions'],
                'drop_off_bedrooms' => $data['drop_off_bedrooms'],
                'drop_off_contact_name' => isset($data['drop_off_contact_name']) ? $data['drop_off_contact_name'] : '',
                'drop_off_mobile' => isset($data['drop_off_mobile']) ? $data['drop_off_mobile'] : '',
                'drop_off_email' => isset($data['drop_off_email']) ? $data['drop_off_email'] : ''
            ]);
            $view_data['removal_jobs_moving'] = JobsMoving::where(['job_id' => $job_id, 'tenant_id' => auth()->user()->tenant_id])->first();
        }elseif($opportunity->op_type=="Cleaning"){
            
            $view_data['jobs_cleaning'] = JobsCleaning::where(['tenant_id' => auth()->user()->tenant_id, 'crm_opportunity_id' => $opp_id])->first();
            //Remove old questionaire
            JobsCleaningAdditionalInfo::where(['tenant_id' => auth()->user()->tenant_id, 'job_id' => $view_data['jobs_cleaning']->job_id])->delete();            
            //add new questionaire
            if($data['additional']){
                foreach($data['additional'] as $q){
                    $obj = new JobsCleaningAdditionalInfo();
                    $obj->tenant_id = auth()->user()->tenant_id;
                    $obj->job_id  = $view_data['jobs_cleaning']->job_id;
                    $obj->question = $q['question'];
                    $obj->reply = $q['reply'];
                    $obj->save();
                    unset($obj);
                }
            }                                    
            $view_data['jobs_cleaning_additional'] = JobsCleaningAdditionalInfo::where(['tenant_id' => auth()->user()->tenant_id, 'job_id' => $view_data['jobs_cleaning']->job_id])->get();
        } 
        
        $view_data['lead_id'] = $lead_id;        
        $view_data['sys_country_states'] = SysCountryStates::where('country_id', '=', $view_data['organisation_settings']->business_country_id)->get();
        $view_data['global'] = $this->global;
        $response['error'] = 0;
        $response['message'] = 'Record has been updated';

        if($opportunity->op_type=="Moving"){
            $response['res_html'] = view('admin.crm-leads.removals.moving_to_grid')->with($view_data)->render();
        }elseif($opportunity->op_type=="Cleaning"){
            $response['res_html'] = view('admin.crm-leads.cleaning.bottom-right')->with($view_data)->render();
        }
        return json_encode($response);
    }

    public function ajaxUpdateCleaningEndOfLease(Request $request)
    {
        $lead_id = $request->input('lead_id');
        $opp_id = $request->input('opp_id');
        $opportunity = CRMOpportunities::where(['id' => $opp_id, 'tenant_id' => auth()->user()->tenant_id])->first();
        $view_data['removal_opportunities'] = CRMOpportunities::where(['tenant_id' => auth()->user()->tenant_id, 'id' => $opp_id])->first();
        $view_data['organisation_settings'] = OrganisationSettings::where('tenant_id', '=', auth()->user()->tenant_id)->first();        

        $data = $request->all();

        JobsCleaning::where(['crm_opportunity_id' => $opp_id, 'tenant_id' => auth()->user()->tenant_id])->update([
            'preferred_time_range' => $data['preferred_time_range'],
            'address' => $data['address'],
            'bedrooms' => $data['bedrooms'],
            'bathrooms' => $data['bathrooms']
        ]);
        $view_data['jobs_cleaning'] = JobsCleaning::where(['tenant_id' => auth()->user()->tenant_id, 'crm_opportunity_id' => $opp_id])->first();
        $view_data['cleaning_shifts'] = JobsCleaningShifts::where(['tenant_id'=> auth()->user()->tenant_id])->get();

        $view_data['lead_id'] = $lead_id;                        
        $view_data['global'] = $this->global;
        $response['error'] = 0;
        $response['message'] = 'Record has been updated';

        $response['res_html'] = view('admin.crm-leads.cleaning.end_of_lease_detail_grid')->with($view_data)->render();
        return json_encode($response);
    }

    public function ajaxUpdateCleaningQuestion(Request $request)
    {
        $lead_id = $request->input('lead_id');
        $opp_id = $request->input('opp_id');

        $opportunity = CRMOpportunities::where(['id' => $opp_id, 'tenant_id' => auth()->user()->tenant_id])->first();
        $view_data['removal_opportunities'] = CRMOpportunities::where(['tenant_id' => auth()->user()->tenant_id, 'id' => $opp_id])->first();
        $view_data['organisation_settings'] = OrganisationSettings::where('tenant_id', '=', auth()->user()->tenant_id)->first();        

        $data = $request->all();

        $view_data['jobs_cleaning'] = JobsCleaning::where(['tenant_id' => auth()->user()->tenant_id, 'crm_opportunity_id' => $opp_id])->first();
            //Remove old questionaire
            JobsCleaningAdditionalInfo::where(['tenant_id' => auth()->user()->tenant_id, 'job_id' => $view_data['jobs_cleaning']->job_id])->delete();            
            //add new questionaire
            if($data['additional']){
                foreach($data['additional'] as $q){
                    $obj = new JobsCleaningAdditionalInfo();
                    $obj->tenant_id = auth()->user()->tenant_id;
                    $obj->job_id  = $view_data['jobs_cleaning']->job_id;
                    $obj->question = $q['question'];
                    $obj->reply = $q['reply'];
                    $obj->save();
                    unset($obj);
                }
            }                                    
        $view_data['jobs_cleaning_additional'] = JobsCleaningAdditionalInfo::where(['tenant_id' => auth()->user()->tenant_id, 'job_id' => $view_data['jobs_cleaning']->job_id])->get(); 
        
        $view_data['lead_id'] = $lead_id;        
        $view_data['sys_country_states'] = SysCountryStates::where('country_id', '=', $view_data['organisation_settings']->business_country_id)->get();
        $view_data['global'] = $this->global;
        $response['error'] = 0;
        $response['message'] = 'Record has been updated';

        $response['res_html'] = view('admin.crm-leads.cleaning.questions_grid')->with($view_data)->render();
        return json_encode($response);
    }

    public function ajaxRemovalsConfirmBooking(Request $request)
    {
        $response['error'] = 0;
        $response['message'] = 'Record has been updated';
        $lead_id = $request->lead_id;
        $crm_opportunity_id = $request->opp_id;
        $quote=NULL;
        $quote_storage=NULL;
        $invoice=NULL;
        $invoice_settings = InvoiceSetting::where('tenant_id', auth()->user()->tenant_id)->first();

        $crmlead = CRMLeads::find($lead_id);

        if(!isset($crm_opportunity_id) || empty($crm_opportunity_id)){
            $response['error'] = 2;
            $response['message'] = 'Opportunity is not found or invalid.';
            return json_encode($response);
        }
        $quote = Quotes::where(['crm_opportunity_id' => $crm_opportunity_id, 'sys_job_type' => 'Moving', 'tenant_id' => auth()->user()->tenant_id])->first();
        $quote_storage = Quotes::where(['crm_opportunity_id' => $crm_opportunity_id, 'sys_job_type' => 'Moving_Storage', 'tenant_id' => auth()->user()->tenant_id])->first();
        // Code Commented in task FORT-741
        /*if(empty($quote)) {
            $response['error'] = 2;
            $response['message'] = 'No Estimate exists for this opportunity. Please create an estimate first.';
            return json_encode($response);
        }
        if($crmlead->lead_type=="Residential"){
            if(!$quote && !$quote_storage){
                $response['error'] = 2;
                $response['message'] = 'No Estimate exists for this opportunity. Please create an estimate first.';
                return json_encode($response);
            }
        }*/

        $opportunity = CRMOpportunities::where(['id' => $crm_opportunity_id, 'tenant_id' => auth()->user()->tenant_id])->first();

        if($opportunity->op_type=="Moving"){
            $job = JobsMoving::where(['tenant_id' => auth()->user()->tenant_id, 'crm_opportunity_id' => $crm_opportunity_id])->first();            
        }elseif($opportunity->op_type=="Cleaning"){
            $job = JobsCleaning::where(['tenant_id' => auth()->user()->tenant_id, 'crm_opportunity_id' => $crm_opportunity_id])->first();
        }

        
        if ($job) {
            if ($job->opportunity == 'N') {
                $response['error'] = 2;
                $response['message'] = 'This Opportunity is already a confirmed booking.';
            } else {
                //START:: Copy Quote to Invoice
                if($quote){
                    $invoice = Invoice::where(['job_id' => $job->job_id, 'sys_job_type' => $opportunity->op_type, 'tenant_id' => auth()->user()->tenant_id])->first();
                    if(!$invoice){
                        $invoice = new Invoice();
                        $invoice->tenant_id = auth()->user()->tenant_id;
                        $invoice->job_id = $quote->job_id;
                        $invoice->invoice_number = $quote->quote_number;
                        $invoice->sys_job_type = $quote->sys_job_type;
                        $invoice->discount_type = $quote->discount_type;
                        $invoice->discount = $quote->discount;
                        $invoice->project_id = 1;
                        $current_date = date('Y-m-d');
                        $invoice->issue_date = $current_date;
                        $due_after = 15;
                                if ($invoice_settings) {
                                    $due_after = $invoice_settings->due_after;
                                }
                        $invoice->due_date = date('Y-m-d', strtotime($current_date . ' + ' . $due_after . ' days'));   
                        $invoice->save();
                        //---> Copy quote items to invoice items
                        $quoteItem = DB::table('quote_items')
                                    ->where(['quote_id' => $quote->id, 'tenant_id' => auth()->user()->tenant_id])
                                    ->get();
                            foreach($quoteItem as $q){
                                $obj_item = new InvoiceItems();
                                $obj_item->tenant_id = auth()->user()->tenant_id;
                                $obj_item->invoice_id = $invoice->id;
                                $obj_item->product_id = $q->product_id;
                                $obj_item->item_name = $q->name;
                                $obj_item->item_summary = $q->description;
                                $obj_item->type = $q->type;
                                $obj_item->quantity = $q->quantity;
                                $obj_item->unit_price = $q->unit_price;
                                $obj_item->tax_id = $q->tax_id;
                                $obj_item->amount = $q->amount;
                                $obj_item->save();
                                unset($obj_item);
                            }
                    }                    
                }

                //Copy Storage Quote into Invoice
                if($quote_storage){
                    $invoice_storage = Invoice::where(['job_id' => $job->job_id, 'sys_job_type' => 'Moving_Storage', 'tenant_id' => auth()->user()->tenant_id])->first();
                    if(!$invoice_storage){
                        $invoice_storage = new Invoice();
                        $invoice_storage->tenant_id = auth()->user()->tenant_id;
                        $invoice_storage->job_id = $quote_storage->job_id;
                        $invoice_storage->invoice_number = $quote_storage->quote_number;
                        $invoice_storage->sys_job_type = $quote_storage->sys_job_type;
                        $invoice_storage->discount_type = $quote_storage->discount_type;
                        $invoice_storage->discount = $quote_storage->discount;
                        $invoice_storage->project_id = 1;
                        $current_date = date('Y-m-d');
                        $invoice_storage->issue_date = $current_date;
                        $due_after = 15;
                                $invoice_settings = InvoiceSetting::where('tenant_id', auth()->user()->tenant_id)->first();
                                if ($invoice_settings) {
                                    $due_after = $invoice_settings->due_after;
                                }
                        $invoice_storage->due_date = date('Y-m-d', strtotime($current_date . ' + ' . $due_after . ' days'));   
                        $invoice_storage->save();
                        //---> Copy quote items to invoice_storage items
                        $quoteItem = DB::table('quote_items')
                                    ->where(['quote_id' => $quote_storage->id, 'tenant_id' => auth()->user()->tenant_id])
                                    ->get();
                            foreach($quoteItem as $q){
                                $obj_item = new InvoiceItems();
                                $obj_item->tenant_id = auth()->user()->tenant_id;
                                $obj_item->invoice_id = $invoice_storage->id;
                                $obj_item->product_id = $q->product_id;
                                $obj_item->item_name = $q->name;
                                $obj_item->item_summary = $q->description;
                                $obj_item->type = $q->type;
                                $obj_item->quantity = $q->quantity;
                                $obj_item->unit_price = $q->unit_price;
                                $obj_item->tax_id = $q->tax_id;
                                $obj_item->amount = $q->amount;
                                $obj_item->save();
                                unset($obj_item);
                            }
                    }                    
                }

                //END:: Copy Quote to Invoice

                //START:: Add a defailt Job Leg
                if($opportunity->op_type=="Moving"){
                    //Update Moving Storage Unit Status
                    $storage_unit = StorageUnitAllocation::where(['job_id' => $job->job_id, 'job_type' => 'Moving', 'allocation_status' => 'Reserved', 'deleted' => '0', 'tenant_id' => auth()->user()->tenant_id])
                    ->update(['allocation_status'=>'Occupied']); 

                    //Finding deo locations
                    $api_key = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'GoogleMaps'])->pluck('account_key')->first();
                    $pickup_address = $job->pickup_address; //
                    $drop_off_address = $job->drop_off_address; //

                    $pickup_geo_location = JobsMovingLegs::getGeoLocation($api_key, $pickup_address);
                    $drop_off_geo_location = JobsMovingLegs::getGeoLocation($api_key, $drop_off_address);
                    //end--->

                    $job_leg = new JobsMovingLegs();
                    $job_leg->job_id = $job->job_id;
                    $job_leg->tenant_id = auth()->user()->tenant_id;
                    $job_leg->job_type = "Pickup";
                    $job_leg->leg_status = NULL;
                    $job_leg->driver_id = NULL;
                    $job_leg->leg_number = 1;
                    $job_leg->leg_date = $job->job_date;
                    $job_leg->pickup_address = $job->pickup_address." ".$job->pickup_suburb;
                    $job_leg->drop_off_address = $job->drop_off_address." ".$job->delivery_suburb;
                    $job_leg->pickup_geo_location = $pickup_geo_location;
                    $job_leg->drop_off_geo_location = $drop_off_geo_location;
                    $job_leg->est_start_time = $job->job_start_time;
                    $job_leg->est_finish_time = $job->job_end_time;                
                    $job_leg->save();
                }
                //END:: Add a defailt Job Leg

                //START:: Update Lead & Opportunity Status
                    CRMOpportunities::where(['id' => $opportunity->id, 'tenant_id' => auth()->user()->tenant_id])->update(['op_status'=>'Confirmed']);
                    CRMLeads::where(['id' => $job->customer_id, 'tenant_id' => auth()->user()->tenant_id])->update(['lead_status'=>'Customer']);
                //END:: Update Lead & Opportunity Status

                $job->opportunity = 'N';
                $job->job_status = 'New';
                $job->save();
                if($invoice_settings->stripe_pre_authorise=='Y' && $invoice){
                    $this->stripePaymentCapture($invoice);
                }
            }
        }
        $response['url'] = "/admin/moving/view-job/".$job->job_id;
        return json_encode($response);
    }
    //END:: Removal Section

    //START:: Stripe Payment Captured
    public function stripePaymentCapture($invoice)
    {
        $tenant_api_details = TenantApiDetail::where(['tenant_id'=> auth()->user()->tenant_id, 'provider'=>'Stripe'])->first();
        if($tenant_api_details){
            if(isset($tenant_api_details->account_key) && !empty($tenant_api_details->account_key)){
            //Carpure charged payment now
            $payment = Payment::where(['invoice_id' => $invoice->id, 'status' => 'pending', 'tenant_id' => auth()->user()->tenant_id])->first(); 
            if($payment){
                if($payment->transaction_id!=NULL){
                    try{
                        $secret_key = env('STRIPE_SECRET');
                        Stripe::setApiKey($secret_key);
                        $charge = \Stripe\Charge::retrieve($payment->transaction_id, ['stripe_account' => $tenant_api_details->variable1]);
                        $charge->capture();
                    }catch(\Stripe\Error\OAuth\OAuthBase $e){
                        $response = array(
                            'status' => 0,
                            'msg' => $e->getMessage()
                        );
                        return json_encode($response);
                    } 
                    $chargeJson = $charge->jsonSerialize();

                    if($chargeJson['amount_refunded'] == 0 && empty($chargeJson['failure_code']) && $chargeJson['paid'] == 1 && $chargeJson['captured'] == 1){
                        //updating payment status
                        $payment->status="complete";
                        $payment->save();
                        //--->
                        $totalAmount = InvoiceItems::where(['invoice_id' => $invoice->id, 'tenant_id' => auth()->user()->tenant_id])->sum('amount');
                        $paidAmount = Payment::where(['invoice_id' => $invoice->id, 'status' => 'complate', 'tenant_id' => auth()->user()->tenant_id])->sum('amount');  
                        if($paidAmount<$totalAmount && $paidAmount>0){
                            $invoice->status='partial';
                        }elseif($paidAmount==$totalAmount){
                            $invoice->status='paid';
                        }else{
                            $invoice->status = 'unpaid';
                        }
                        //Update Invoice Status
                        $invoice->save();
                    }
                }
            }
            }
        }
    }
    //END:: Stripe Payment Captured


    //START:: Activity Attachment
    public function uploadActivityAttachment(Request $request)
    {
        
        $id = $request->input('id');
        $type = $request->input('type');
        $is_reply = $request->input('is_reply');
        if($is_reply==1){
            $note_id = $request->input('note_id');
        }else{
            $note_id = 0;
        }
        $key = $type.'_'.rand(1,100);
        if ($request->session()->has($type.'_attachment')) {
            $array = session($type.'_attachment');            
        }else{
            $array = [];
        }
        if ($request->hasFile('attachment')) {
            $image = $request->file('attachment');
            $filetype = $image->getClientOriginalExtension();
            $input['filename'] = date('Y') . '-' . $image->getClientOriginalName();
            $destinationPath = public_path('/user-uploads/tenants/' . auth()->user()->tenant_id.'/temp');
            File::isDirectory($destinationPath) or File::makeDirectory($destinationPath, 0777, true, true);
            $request->attachment->move($destinationPath, $input['filename']);
            $location = '/user-uploads/tenants/' . auth()->user()->tenant_id.'/temp/'.$input['filename'];
            $attachment = ['key' => $key,'name'=>$input['filename'],'type'=>$type,'filetype'=>$filetype,'path'=>$location,'note_id'=>$note_id];
            $array[$key]=$attachment;
            session([$type.'_attachment'=>$array]);
            //---
            
            
            $response['html'] = view('admin.crm-leads.attachment_div')->with(['attachment'=>session($type.'_attachment')])->render();
            $response['error']=0;
            $response['message']='Attachment upload successfully';
            return json_encode($response);
        }
    }

    public function removeActivityAttachment(Request $request)
    {

        $key = $request->input('key');        
        $type = $request->input('type');
        if ($request->session()->has($type.'_attachment')) {
            $array = session($type.'_attachment');
            //Delete file from session
            unset($array[$key]);
            session([$type.'_attachment'=>$array]);
            //---
            if($request->input('is_reply') == 1) {
                $response['is_reply'] = 1;
            }
            $response['html'] = view('admin.crm-leads.attachment_div')->with(['attachment'=>session($type.'_attachment')])->render();
            $response['error']=0;
            $response['message']='Attachment removed successfully';
            return json_encode($response);
        }
    }

    public function viewActivityAttachment($id)
    {
        try {
            $this->attachments = CRMActivityLogAttachment::where('tenant_id', '=', auth()->user()->tenant_id)->where('id', '=', $id)->first();
            if ($this->attachments) {
                if ($this->attachments->attachment_content) {
                    $destinationPath = public_path();
                    if (File::exists($this->attachments->attachment_content)) {
                        return response()->file($this->attachments->attachment_content);
                      }else{
                        return response()->file($destinationPath.$this->attachments->attachment_content);
                      }
                }
            }

            return redirect(route('admin.email-templates.edit', $id));
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }
    //END:: Activity Attachment

    //START:: Storage Module
    public function storageTabContent(Request $request)
    {
        $crm_opportunity_id = $request->crm_opportunity_id;
        $job = JobsMoving::where(['tenant_id' => auth()->user()->tenant_id, 'crm_opportunity_id' => $crm_opportunity_id])->first();
        $this->quoteItem = NULL;
        $this->quote = NULL;
        $this->storage_type_list = StorageTypes::where('tenant_id', '=', auth()->user()->tenant_id)
        ->where(['deleted'=>'0'])->orderBy('name', 'ASC')->get();

        $this->storage_reservation = StorageUnitAllocation::select("storage_unit_allocation.*",
        "storage_units.serial_number",
        "storage_types.name as type_name"
        )
        ->join('storage_units', 'storage_units.id', 'storage_unit_allocation.unit_id')
        ->join('storage_types', 'storage_types.id', 'storage_units.storage_type_id')
        ->where(['storage_unit_allocation.job_id' => $job->job_id, 'job_type' => 'Moving', 'storage_unit_allocation.deleted' => '0', 'storage_unit_allocation.tenant_id' => auth()->user()->tenant_id])->get();

        //Estimate Section 
        $this->taxs = Tax::where(['tenant_id' => auth()->user()->tenant_id])->get();
        $this->products = Product::select("products.*")
        ->join('product_categories', 'product_categories.id', 'products.category_id')
        ->where(['products.tenant_id' => auth()->user()->tenant_id,'product_categories.category_name'=>'Storage'])
        ->get();

        $this->quote = Quotes::where(['crm_opportunity_id' => $crm_opportunity_id, 'sys_job_type' => 'Moving_Storage', 'tenant_id' => auth()->user()->tenant_id])->first();
        if($this->quote){
            $this->quoteItem = DB::table('quote_items')
                ->select('*')
                ->where('quote_id', $this->quote->id)
                ->get();
        }
        $response['storage_reservation_html'] = view('admin.crm-leads.storage.reservation_grid', $this->data)->render();
        $response['storage_estimate_html'] = view('admin.crm-leads.storage.storage_estimate_grid', $this->data)->render();
        $response['error']=0;
        return json_encode($response);
    }
    //END:: Storage Module
    
    public function ajaxSaveJob(Request $request)
    {
        $old_opportunity = CRMOpportunities::where(['lead_id' => $request->lead_id, 'tenant_id' => auth()->user()->tenant_id])->first();
        $old_job_moving = JobsMoving::where(['job_id' => $request->job_id, 'tenant_id' => auth()->user()->tenant_id])->first();
        $old_job_moving_legs = JobsMovingLegs::where(['job_id' => $request->job_id, 'tenant_id' => auth()->user()->tenant_id])->get();
        $old_quote = Quotes::where(['job_id' => $request->job_id, 'tenant_id' => auth()->user()->tenant_id])->first();
        if($old_quote)
        {
            $old_quote_items = QuoteItem::where(['quote_id' => $old_quote->id, 'tenant_id' => auth()->user()->tenant_id])->get();
        }
        $old_invoice = Invoice::where(['job_id' => $request->job_id, 'tenant_id' => auth()->user()->tenant_id])->first();
        if($old_invoice)
        {
            $old_invoice_items = InvoiceItems::where(['invoice_id' => $old_invoice->id, 'tenant_id' => auth()->user()->tenant_id])->get();
        }
        $old_job_moving_inventory = JobsMovingInventory::where(['tenant_id' => auth()->user()->tenant_id, 'job_id' => $request->job_id])->get();

        if(!empty($old_opportunity))
        {
            $data['tenant_id'] = auth()->user()->tenant_id;
            $data['est_job_date'] = Carbon::createFromFormat('d/m/Y', $request->input('est_job_date'))->format('Y-m-d');
            $data['op_type'] = $request->op_type;
            $data['op_status'] = 'New';
            $data['contact_id'] = $old_opportunity->contact_id;
            $data['lead_id'] = $request->lead_id;
            $data['user_id'] = auth()->user()->id;        
            $data['created_by'] = auth()->user()->id;
            $data['updated_by'] = auth()->user()->id;
            $data['created_at'] = time();
            $data['updated_at'] = time();
            $opportunity = CRMOpportunities::create($data);
        }

        if ($request->op_type == 'Moving' && $old_job_moving && !empty($opportunity)) 
        {
            $res = JobsMoving::select(DB::raw('MAX(job_number) as max_job_number'))->where('jobs_moving.tenant_id', '=', auth()->user()->tenant_id)->first();
            $new_job_number = intval($res->max_job_number) + 1;
            $job = new JobsMoving();
            $job->tenant_id = auth()->user()->tenant_id;
            $job->company_id = $request->input('company_id');
            $job->opportunity = $old_job_moving->opportunity;
            $job->job_type = 'Moving';
            $job->customer_id = $request->lead_id;
            $job->job_number = $new_job_number;
            $job->job_date = $opportunity->est_job_date;
            $job->booked_date = $old_job_moving->booked_date;
            $job->job_start_time = $old_job_moving->job_start_time;
            $job->job_end_time = $old_job_moving->job_end_time;
            $job->job_status = 'New';
            $job->crm_opportunity_id = $opportunity->id;
            $job->pickup_furnishing = $old_job_moving->pickup_furnishing;
            $job->pickup_living_areas = $old_job_moving->pickup_living_areas;
            $job->pickup_other_rooms = $old_job_moving->pickup_other_rooms;
            $job->pickup_speciality_items = $old_job_moving->pickup_speciality_items;
            $job->pickup_state = $old_job_moving->pickup_state;
            $job->pickup_post_code = $request->job_pickup_post_code;
            $job->drop_off_state = $old_job_moving->drop_off_state;
            $job->drop_off_post_code = $request->job_drop_off_post_code;
            $job->pickup_address = $request->job_pickup_address_only;
            $job->pickup_property_type = $old_job_moving->pickup_property_type;
            $job->pickup_bedrooms = $old_job_moving->bedrooms;
            $job->pickup_access_restrictions = $old_job_moving->pick_access_restrictions;
            $job->drop_off_address = $request->job_drop_off_address_only;
            $job->drop_off_property_type = $old_job_moving->drop_off_property_type;
            $job->drop_off_bedrooms = $old_job_moving->drop_off_bedrooms;
            $job->drop_off_access_restrictions = $old_job_moving->drop_off_access_restrictions;
            $job->pickup_suburb = $request->job_pickup_suburb;
            $job->delivery_suburb = $request->job_delivery_suburb;
            $job->pickup_floor = $old_job_moving->pickup_floor;
            $job->drop_off_floor = $old_job_moving->drop_off_floor;
            $job->pickup_has_lift = $old_job_moving->pickup_has_lift;
            $job->drop_off_has_lift = $old_job_moving->drop_off_has_lift;
            $job->pickup_region = $old_job_moving->pickup_region;
            $job->drop_off_region = $old_job_moving->drop_off_region;
            $job->storage_cbm = $old_job_moving->storage_cbm;
            $job->pickup_km_nearest_region = $old_job_moving->pickup_km_nearest_region;
            $job->pickup_excess_km = $old_job_moving->pickup_excess_km;
            $job->pickup_stairs_lift_charges = $old_job_moving->pickup_stairs_lift_charges;
            $job->pickup_suburb_charges = $old_job_moving->pickup_suburb_charges;
            $job->pickup_excess_charges = $old_job_moving->pickup_excess_charges;
            $job->drop_off_excess_charges = $old_job_moving->drop_off_excess_charges;
            $job->drop_off_km_nearest_region = $old_job_moving->drop_off_km_nearest_region;
            $job->drop_off_excess_km = $old_job_moving->drop_off_excess_km;
            $job->drop_off_stairs_lift_charges = $old_job_moving->drop_off_stairs_lift_charges;
            $job->drop_off_suburb_charges = $old_job_moving->drop_off_suburb_charges;
            $job->job_estimated_time_from = $old_job_moving->job_estimated_time_to;
            $job->no_of_legs = $old_job_moving->no_of_legs;
            $job->price_structure = $old_job_moving->price_structure;
            $job->fixed_other_rate = $old_job_moving->fixed_other_rate;
            $job->hourly_rate = $old_job_moving->hourly_rate;
            $job->calculated_excess_mins = $old_job_moving->calculated_excess_mins;
            $job->calculated_total_mins = $old_job_moving->calculated_total_mins;
            $job->actual_total_mins = $old_job_moving->actual_total_mins;
            $job->total_amount = $old_job_moving->total_amount;
            $job->deposit_agreed = $old_job_moving->deposit_agreed;
            $job->rate_per_cbm = $old_job_moving->rate_per_cbm;
            $job->total_cbm = $old_job_moving->total_cbm;
            $job->goods_value = $old_job_moving->goods_value;
            $job->calculated_cbm = $old_job_moving->calculated_cbm;
            $job->pickup_instructions = $old_job_moving->pickup_instructions;
            $job->drop_off_instructions = $old_job_moving->drop_off_instructions;
            $job->payment_instructions = $old_job_moving->payment_instructions;
            $job->insurance_instructions = $old_job_moving->insurance_instructions;
            $job->disclaimer_instructions = $old_job_moving->disclaimer_instructions;
            $job->other_instructions = $old_job_moving->other_instructions;
            $job->quote_file_name = $old_job_moving->quote_file_name;
            $job->vehicle_id = $old_job_moving->vehicle_id;
            $job->lead_info = $old_job_moving->lead_info;
            $job->pickup_contact_name = $old_job_moving->pickup_contact_name;
            $job->drop_off_contact_name = $old_job_moving->drop_off_contact_name;
            $job->pickup_phone = $old_job_moving->pickup_phone;
            $job->pickup_mobile = $old_job_moving->pickup_mobile;
            $job->pickup_email = $old_job_moving->pickup_email;
            $job->drop_off_phone = $old_job_moving->drop_off_phone;
            $job->drop_off_mobile = $old_job_moving->drop_off_mobile;
            $job->drop_off_email = $old_job_moving->drop_off_email;
            $job->bill_address = $old_job_moving->bill_address;
            $job->bill_state = $old_job_moving->bill_state;
            $job->deleted = 0;
            $job->created_by = auth()->user()->id;
            $job->updated_by = auth()->user()->id;
            $job->created_at = time();
            $job->save();
            //--->
        }
        elseif ($request->op_type == 'Cleaning' && !empty($opportunity)) 
        {
            $res = JobsCleaning::select(DB::raw('MAX(job_number) as max_job_number'))->where('jobs_cleaning.tenant_id', '=', auth()->user()->tenant_id)->first();
            $new_job_number = intval($res->max_job_number) + 1;
            $job = new JobsCleaning();
            $job->tenant_id = auth()->user()->tenant_id;
            $job->company_id = $request->input('company_id');
            $job->crm_opportunity_id = $opportunity->id;
            $job->opportunity = 'Y';
            $job->customer_id = $request->lead_info;
            $job->job_number = $new_job_number;
            $job->job_date = $opportunity->est_job_date;
            $job->created_by = auth()->user()->id;
            $job->updated_by = auth()->user()->id;
            $job->created_at = time();
            $job->save();
            //--->
        }
        else
        {

        }

        if(!empty($old_job_moving_legs) && !empty($job))
        {
            foreach($old_job_moving_legs as $leg)
            {
                $new_job_moving_leg = new JobsMovingLegs();
                $new_job_moving_leg->tenant_id = auth()->user()->tenant_id;
                $new_job_moving_leg->job_id = $job->job_id;
                $new_job_moving_leg->job_type = $leg->job_type;
                $new_job_moving_leg->leg_number = $leg->leg_number;
                $new_job_moving_leg->leg_date = Carbon::createFromFormat('d/m/Y', $request->input('est_job_date'))->format('Y-m-d');
                $new_job_moving_leg->pickup_address = $request->job_pickup_address_only.' '.$request->job_pickup_suburb.' '.$request->job_pickup_post_code;
                $new_job_moving_leg->drop_off_address = $request->job_drop_off_address_only.' '.$request->job_delivery_suburb.' '.$request->job_drop_off_post_code;
                $new_job_moving_leg->pickup_geo_location = $leg->pickup_geo_location;
                $new_job_moving_leg->drop_off_geo_location = $leg->drop_off_geo_location;
                $new_job_moving_leg->est_start_time = $leg->est_start_time;
                $new_job_moving_leg->est_finish_time = $leg->est_finish_time;
                $new_job_moving_leg->driver_id = $leg->driver_id;
                $new_job_moving_leg->vehicle_id = $leg->vehicle_id;
                $new_job_moving_leg->offsider_ids = $leg->offsider_ids;
                $new_job_moving_leg->notes = $leg->notes;
                $new_job_moving_leg->has_multiple_trips = $leg->has_multiple_trips;
                $new_job_moving_leg->created_at = Carbon::now();
                $new_job_moving_leg->save();
            }
        }
// Commenting this code for Jira ticket FORT-940
/*
        if(!empty($old_quote) && !empty($opportunity))
        {
            $max_quote = Quotes::select(DB::raw('MAX(quote_number) as max_quote_number'))->where('tenant_id', auth()->user()->tenant_id)->first();
            $new_quote_number = intval($max_quote->max_quote_number) + 1;
            $data['tenant_id'] = auth()->user()->tenant_id;
            $data['quote_number'] = $new_quote_number;
            $data['crm_opportunity_id'] = $opportunity->id;
            $data['sys_job_type'] = $request->op_type;
            $data['job_id'] = $job->job_id;
            $data['quote_date'] = Carbon::createFromFormat('d/m/Y', $request->input('est_job_date'))->format('Y-m-d');
            $data['discount_type'] = $old_quote->discount_type;
            $data['discount'] = $old_quote->discount;
            $data['quote_version'] = $old_quote->quote_version;
            $data['quote_file_name'] = $old_quote->quote_file_name;
            $data['quote_accepted'] = $old_quote->quote_accepted;
            $data['deposit_paid'] = $old_quote->deposit_paid;
            $data['created_by'] = auth()->user()->id;
            $data['created_date'] = Carbon::now();
            $Quote = Quotes::create($data);
        }
        

        if (isset($Quote->id) && !empty($old_quote_items)) 
        {
            foreach($old_quote_items as $item)
            {
                $data2['tenant_id'] = auth()->user()->tenant_id;
                $data2['quote_id'] = $Quote->id;
                $data2['product_id'] = $item->product_id;
                $data2['name'] = $item->name;
                $data2['description'] = $item->description;
                $data2['type'] = $item->type;
                $data2['unit_price'] = $item->unit_price;
                $data2['quantity'] = $item->quantity;
                $data2['amount'] = $item->amount;
                $data2['tax_id'] = $item->tax_id;
                $data2['created_by'] = auth()->user()->id;
                $data2['created_date'] = Carbon::now();
                QuoteItem::create($data2);
            }
        }

        if(!empty($old_invoice) && !empty($job))
        {
            $invoice = new Invoice();
            $max_invoice = Invoice::select(DB::raw('MAX(invoice_number) as max_invoice_number'))->where('tenant_id', auth()->user()->tenant_id)->first();
            $new_invoice_number = intval($max_invoice->max_invoice_number) + 1;
            $invoice->tenant_id = auth()->user()->tenant_id;
            $invoice->job_id = $job->job_id;
            $invoice->sys_job_type = $request->op_type;
            $invoice->project_id = $old_invoice->project_id;
            $invoice->invoice_number = $new_invoice_number;
            $invoice->issue_date = Carbon::now();
            $invoice->due_date = Carbon::now()->addDays(7);
            $invoice->sub_total = $old_invoice->sub_total;
            $invoice->discount = $old_invoice->discount;
            $invoice->discount_type = $old_invoice->discount_type;
            $invoice->total = $old_invoice->total;
            $invoice->status = 'unpaid';
            $invoice->stripe_one_off_customer_id = $old_invoice->stripe_one_off_customer_id;
            $invoice->inv_version = 0;
            $invoice->regenerated = $old_invoice->regenerated;
            $invoice->recurring = $old_invoice->recurring;
            $invoice->billing_cycle = $old_invoice->billing_cycle;
            $invoice->billing_interval = $old_invoice->billing_interval;
            $invoice->billing_frequency = $old_invoice->billing_frequency;
            $invoice->file = $old_invoice->file;
            $invoice->file_original_name = $old_invoice->file_original_name;
            $invoice->note = $old_invoice->note;
            $invoice->created_at = Carbon::now();
            $invoice->save();
        }

        if(isset($invoice->id) && !empty($old_invoice_items))
        {
            foreach($old_invoice_items as $item)
            {
                $invoice_item = new InvoiceItems();
                $invoice_item->tenant_id = auth()->user()->tenant_id;
                $invoice_item->invoice_id = $invoice->id;
                $invoice_item->product_id = $item->product_id;
                $invoice_item->item_name = $item->item_name;
                $invoice_item->item_summary = $item->item_summary;
                $invoice_item->type = $item->type;
                $invoice_item->quantity = $item->quantity;
                $invoice_item->unit_price = $item->unit_price;
                $invoice_item->amount = $item->amount;
                $invoice_item->tax_id = $item->tax_id;
                $invoice_item->created_at = Carbon::now();
                $invoice_item->save();
            }
        }
        */
        if(!empty($old_job_moving_inventory))
        {
            foreach($old_job_moving_inventory as $inventory)
            {
                $new_inventory = new JobsMovingInventory();
                $new_inventory->tenant_id = auth()->user()->tenant_id;
                $new_inventory->job_id = $job->job_id;
                $new_inventory->inventory_id = $inventory->inventory_id;
                $new_inventory->quantity = $inventory->quantity;
                $new_inventory->misc_item_name = $inventory->misc_item_name;
                $new_inventory->misc_item_cbm = $inventory->misc_item_cbm;
                $new_inventory->notes = $inventory->notes;
                $new_inventory->created_at = Carbon::now();
                $new_inventory->save();
            }
        }

        $response['error'] = 0;
        $response['message'] = 'Job Has Been Coped SuccessFully!';  
        return json_encode($response);
    }
    public function ajaxSaveOpportunity(Request $request)
    {
        $old_crm_opportunity = CRMOpportunities::where(['id' => $request->opportunity_id, 'tenant_id' => auth()->user()->tenant_id])->first();
        $old_job_moving = JobsMoving::where(['crm_opportunity_id' => $request->opportunity_id, 'tenant_id' => auth()->user()->tenant_id])->first();
        $old_quote = Quotes::where(['crm_opportunity_id' => $request->opportunity_id, 'tenant_id' => auth()->user()->tenant_id])->first();
        // dd($old_job_moving);
        if($old_job_moving)
        {
            $old_job_moving_inventory = JobsMovingInventory::where(['tenant_id' => auth()->user()->tenant_id, 'job_id' => $old_job_moving->job_id])->get();
        }
        if(!empty($old_quote))
        {
            $old_quote_items = QuoteItem::where(['quote_id' => $old_quote->id, 'tenant_id' => auth()->user()->tenant_id])->get();
        }
        //Creating Opportunity
        $data['tenant_id'] = auth()->user()->tenant_id;
        $data['lead_id'] = $request->lead_id;        
        $data['op_type'] = $request->op_type;
        $data['op_status'] = 'New';
        $data['est_job_date'] = Carbon::createFromFormat('d/m/Y', $request->input('est_job_date'))->format('Y-m-d');
        $data['confidence'] = $old_crm_opportunity->confidence;
        $data['value'] = $old_crm_opportunity->value;
        $data['op_frequency'] = $old_crm_opportunity->op_frequency;
        $data['contact_id'] = $old_crm_opportunity->contact_id;
        $data['user_id'] = auth()->user()->id;
        $data['notes'] = $old_crm_opportunity->notes;
        $data['deleted'] = $old_crm_opportunity->deleted;
        $data['created_by'] = auth()->user()->id;
        $data['updated_by'] = auth()->user()->id;
        $data['created_at'] = time();
        $data['updated_at'] = time();
        $opportunity = CRMOpportunities::create($data);

        if(isset($opportunity->id) && !empty($old_job_moving))
        {
            $res = JobsMoving::select(DB::raw('MAX(job_number) as max_job_number'))->where('jobs_moving.tenant_id', '=', auth()->user()->tenant_id)->first();
            $new_job_number = intval($res->max_job_number) + 1;
            $job = new JobsMoving();
            $job->tenant_id = auth()->user()->tenant_id;
            $job->company_id = $request->input('company_id');
            $job->opportunity = $old_job_moving->opportunity;
            $job->job_type = 'Moving';
            $job->customer_id = $request->lead_id;
            $job->job_number = $new_job_number;
            $job->job_date = $opportunity->est_job_date;
            $job->booked_date = $old_job_moving->booked_date;
            $job->job_start_time = $old_job_moving->job_start_time;
            $job->job_end_time = $old_job_moving->job_end_time;
            $job->job_status = 'New';
            $job->crm_opportunity_id = $opportunity->id;
            $job->pickup_furnishing = $old_job_moving->pickup_furnishing;
            $job->pickup_living_areas = $old_job_moving->pickup_living_areas;
            $job->pickup_other_rooms = $old_job_moving->pickup_other_rooms;
            $job->pickup_speciality_items = $old_job_moving->pickup_speciality_items;
            $job->pickup_state = $old_job_moving->pickup_state;
            $job->pickup_post_code = $request->opportunity_pickup_post_code;
            $job->drop_off_state = $old_job_moving->drop_off_state;
            $job->drop_off_post_code = $request->opportunity_drop_off_post_code;
            $job->pickup_address = $request->opportunity_pickup_address_only;
            $job->pickup_property_type = $old_job_moving->pickup_property_type;
            $job->pickup_bedrooms = $old_job_moving->bedrooms;
            $job->pickup_access_restrictions = $old_job_moving->pick_access_restrictions;
            $job->drop_off_address = $request->opportunity_drop_off_address_only;
            $job->drop_off_property_type = $old_job_moving->drop_off_property_type;
            $job->drop_off_bedrooms = $old_job_moving->drop_off_bedrooms;
            $job->drop_off_access_restrictions = $old_job_moving->drop_off_access_restrictions;
            $job->pickup_suburb = $request->opportunity_pickup_suburb;
            $job->delivery_suburb = $request->opportunity_delivery_suburb;
            $job->pickup_floor = $old_job_moving->pickup_floor;
            $job->drop_off_floor = $old_job_moving->drop_off_floor;
            $job->pickup_has_lift = $old_job_moving->pickup_has_lift;
            $job->drop_off_has_lift = $old_job_moving->drop_off_has_lift;
            $job->pickup_region = $old_job_moving->pickup_region;
            $job->drop_off_region = $old_job_moving->drop_off_region;
            $job->storage_cbm = $old_job_moving->storage_cbm;
            $job->pickup_km_nearest_region = $old_job_moving->pickup_km_nearest_region;
            $job->pickup_excess_km = $old_job_moving->pickup_excess_km;
            $job->pickup_stairs_lift_charges = $old_job_moving->pickup_stairs_lift_charges;
            $job->pickup_suburb_charges = $old_job_moving->pickup_suburb_charges;
            $job->pickup_excess_charges = $old_job_moving->pickup_excess_charges;
            $job->drop_off_excess_charges = $old_job_moving->drop_off_excess_charges;
            $job->drop_off_km_nearest_region = $old_job_moving->drop_off_km_nearest_region;
            $job->drop_off_excess_km = $old_job_moving->drop_off_excess_km;
            $job->drop_off_stairs_lift_charges = $old_job_moving->drop_off_stairs_lift_charges;
            $job->drop_off_suburb_charges = $old_job_moving->drop_off_suburb_charges;
            $job->job_estimated_time_from = $old_job_moving->job_estimated_time_to;
            $job->no_of_legs = $old_job_moving->no_of_legs;
            $job->price_structure = $old_job_moving->price_structure;
            $job->fixed_other_rate = $old_job_moving->fixed_other_rate;
            $job->hourly_rate = $old_job_moving->hourly_rate;
            $job->calculated_excess_mins = $old_job_moving->calculated_excess_mins;
            $job->calculated_total_mins = $old_job_moving->calculated_total_mins;
            $job->actual_total_mins = $old_job_moving->actual_total_mins;
            $job->total_amount = $old_job_moving->total_amount;
            $job->deposit_agreed = $old_job_moving->deposit_agreed;
            $job->rate_per_cbm = $old_job_moving->rate_per_cbm;
            $job->total_cbm = $old_job_moving->total_cbm;
            $job->goods_value = $old_job_moving->goods_value;
            $job->calculated_cbm = $old_job_moving->calculated_cbm;
            $job->pickup_instructions = $old_job_moving->pickup_instructions;
            $job->drop_off_instructions = $old_job_moving->drop_off_instructions;
            $job->payment_instructions = $old_job_moving->payment_instructions;
            $job->insurance_instructions = $old_job_moving->insurance_instructions;
            $job->disclaimer_instructions = $old_job_moving->disclaimer_instructions;
            $job->other_instructions = $old_job_moving->other_instructions;
            $job->quote_file_name = $old_job_moving->quote_file_name;
            $job->vehicle_id = $old_job_moving->vehicle_id;
            $job->lead_info = $old_job_moving->lead_info;
            $job->pickup_contact_name = $old_job_moving->pickup_contact_name;
            $job->drop_off_contact_name = $old_job_moving->drop_off_contact_name;
            $job->pickup_phone = $old_job_moving->pickup_phone;
            $job->pickup_mobile = $old_job_moving->pickup_mobile;
            $job->pickup_email = $old_job_moving->pickup_email;
            $job->drop_off_phone = $old_job_moving->drop_off_phone;
            $job->drop_off_mobile = $old_job_moving->drop_off_mobile;
            $job->drop_off_email = $old_job_moving->drop_off_email;
            $job->bill_address = $old_job_moving->bill_address;
            $job->bill_state = $old_job_moving->bill_state;
            $job->deleted = $old_job_moving->deleted;
            $job->created_by = auth()->user()->id;
            $job->updated_by = auth()->user()->id;
            $job->created_at = time();
            $job->save();
        }

        if(!empty($old_quote))
        {
            $max_quote = Quotes::select(DB::raw('MAX(quote_number) as max_quote_number'))->where('tenant_id', auth()->user()->tenant_id)->first();
            $new_quote_number = intval($max_quote->max_quote_number) + 1;
            $data['tenant_id'] = auth()->user()->tenant_id;
            $data['quote_number'] = $new_quote_number;
            $data['crm_opportunity_id'] = $opportunity->id;
            $data['sys_job_type'] = $old_quote->sys_job_type;
            $data['job_id'] = $job->job_id;
            $data['quote_date'] = Carbon::createFromFormat('d/m/Y', $request->input('est_job_date'))->format('Y-m-d');
            $data['discount_type'] = $old_quote->discount_type;
            $data['discount'] = $old_quote->discount;
            $data['quote_version'] = $old_quote->quote_version;
            $data['quote_file_name'] = $old_quote->quote_file_name;
            $data['quote_accepted'] = $old_quote->quote_accepted;
            $data['deposit_paid'] = $old_quote->deposit_paid;
            $data['created_by'] = auth()->user()->id;
            $data['created_date'] = Carbon::now();
            $Quote = Quotes::create($data);
        }

        if (isset($Quote->id) && !empty($old_quote_items)) 
        {
            foreach($old_quote_items as $item)
            {
                $data2['tenant_id'] = auth()->user()->tenant_id;
                $data2['quote_id'] = $Quote->id;
                $data2['product_id'] = $item->product_id;
                $data2['name'] = $item->name;
                $data2['description'] = $item->description;
                $data2['type'] = $item->type;
                $data2['unit_price'] = $item->unit_price;
                $data2['quantity'] = $item->quantity;
                $data2['amount'] = $item->amount;
                $data2['tax_id'] = $item->tax_id;
                $data2['created_by'] = auth()->user()->id;
                $data2['created_date'] = Carbon::now();
                QuoteItem::create($data2);
            }
        }
        if(!empty($old_job_moving_inventory))
        {
            foreach($old_job_moving_inventory as $inventory)
            {
                $new_inventory = new JobsMovingInventory();
                $new_inventory->tenant_id = auth()->user()->tenant_id;
                $new_inventory->job_id = $job->job_id;
                $new_inventory->inventory_id = $inventory->inventory_id;
                $new_inventory->quantity = $inventory->quantity;
                $new_inventory->misc_item_name = $inventory->misc_item_name;
                $new_inventory->misc_item_cbm = $inventory->misc_item_cbm;
                $new_inventory->notes = $inventory->notes;
                $new_inventory->created_at = Carbon::now();
                $new_inventory->save();
            }
        }

        $response['error'] = 0;
        $response['message'] = 'Opportunity Has Been Coped SuccessFully!';  
        return json_encode($response);
    }
    public function ajaxGetCustomerJobData(Request $request)
    {
        $this->lead_id = $request->lead_id;
        $this->jobs = JobsMoving::select(
            'jobs_moving.job_id',
            'jobs_moving.job_number',
            'jobs_moving.job_date',
            'jobs_moving.pickup_suburb',
            'jobs_moving.delivery_suburb',
            'jobs_moving.job_status',
            'jobs_moving.payment_instructions',
            'jobs_moving.pickup_contact_name',
            'jobs_moving.created_at'
        )
            ->where(['jobs_moving.tenant_id' => auth()->user()->tenant_id, 'jobs_moving.opportunity' => 'N'])
            ->where('jobs_moving.customer_id', $this->lead_id)
            ->orderBy('jobs_moving.job_id', 'desc')
            ->get();

            return DataTables::of($this->jobs)
            ->editColumn('job_number', function ($row) {
                return '<a class="badge bg-blue" href="' . route("admin.list-jobs.view-job", $row->job_id) . '" >' . $row->job_number . '</a>';
                
            })
            ->editColumn('name', function ($row) {
                return $row->pickup_contact_name;
            })
            ->editColumn('created', function ($row) {
                $date = Carbon::parse($row->created_at);
                return $date->format('d-m-Y');
            })
            ->editColumn('job_date', function ($row) {
                $date = Carbon::parse($row->job_date);
                return $date->format('d-m-Y');
            }) 
            ->editColumn('pickup_suburb', function ($row) {
                return $row->pickup_suburb;
            })                            
            ->editColumn('drop_off_suburb', function ($row) {
                return $row->delivery_suburb;
            })
            ->editColumn('job_status', function ($row) {
                return $row->job_status;
            })
            ->editColumn('payment_status', function ($row) {
                return $row->payment_instructions;
            })
            ->editColumn('action', function ($row) {
                $crmlead = CRMleads::where(['id' => $this->lead_id, 'tenant_id' => auth()->user()->tenant_id])->first();
                $result =  '<div class="list-icons float-right">'.
                            '<div class="dropdown">'.
                                '<a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown"><i class="icon-menu"></i></a>'.
                                    '<div class="dropdown-menu dropdown-menu-right">'.
                                        '<a href="#" class="dropdown-item copy-job-btn" data-job_id="'. $row->job_id .'" data-lead_id="'. $crmlead->id .'" ><i class="fa fa-clipboard"></i> Copy</a>'.                                                        
                                    '</div>'.
                            '</div>'.
                        '</div>';
                return $result;
            })
            ->rawColumns(['job_number', 'created', 'job_date', 'pickup_suburb', 'drop_off_suburb', 'job_status', 'payment_status', 'name', 'action'])
            ->make(true);
    }
    public function ajaxGetCustomerOpportunityData(Request $request)
    {
        // dd($request->all());
        $this->lead_id = $request->lead_id;
        $this->opportunities = CRMOpportunities::select(
            'crm_opportunities.id',
                'crm_opportunities.lead_id',
                'crm_opportunities.op_status',
                'crm_opportunities.created_at',
                'jobs_moving.job_date',
                'jobs_moving.job_id',
                'jobs_moving.job_number',
                'jobs_moving.pickup_suburb',
                'jobs_moving.delivery_suburb',
                'jobs_moving.pickup_contact_name',
                'companies.company_name'
            )
            ->where(['crm_opportunities.lead_id' => $this->lead_id, 'crm_opportunities.tenant_id' => auth()->user()->tenant_id])
            ->where('jobs_moving.opportunity', 'Y')
            ->leftjoin('crm_leads', 'crm_leads.id', '=', 'crm_opportunities.lead_id')
            ->leftjoin('jobs_moving', 'jobs_moving.crm_opportunity_id', 'crm_opportunities.id')
            ->leftjoin('users', 'users.id', '=', 'crm_opportunities.user_id')
            ->leftjoin('companies', 'companies.id', 'jobs_moving.company_id')
            ->orderBy('crm_opportunities.created_at', 'desc')
            ->get();

            return DataTables::of($this->opportunities)
            ->editColumn('job_number', function ($row) {
                return '<a class="badge bg-blue" href="' . route("admin.crm-leads.view", $row->lead_id) . '" >' . $row->job_number . '</a>';
                
            })
            ->editColumn('name', function ($row) {
                return $row->pickup_contact_name;
            })
            ->editColumn('created', function ($row) {
                $date = Carbon::parse($row->created_at);
                return $date->format('d-m-Y');
            })
            ->editColumn('job_date', function ($row) {
                $date = Carbon::parse($row->job_date);
                return $date->format('d-m-Y');
            }) 
            ->editColumn('pickup_suburb', function ($row) {
                return $row->pickup_suburb;
            })                            
            ->editColumn('drop_off_suburb', function ($row) {
                return $row->delivery_suburb;
            })
            ->editColumn('status', function ($row) {
                return $row->op_status;
            })
            ->editColumn('company', function ($row) {
                return $row->company_name;
            })
            ->editColumn('action', function ($row) {
                $crmlead = CRMleads::where(['id' => $row->lead_id, 'tenant_id' => auth()->user()->tenant_id])->first();
                $mobile = CRMContacts::select('crm_contact_details.detail')
                ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                ->where(['crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.lead_id' => $crmlead->id, 'crm_contact_details.detail_type' => 'Mobile'])
                ->pluck('detail')
                ->first();
                $email = CRMContacts::select('crm_contact_details.detail')
                    ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                    ->where(['crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.lead_id' => $crmlead->id, 'crm_contact_details.detail_type' => 'Email'])
                    ->pluck('detail')
                    ->first();
                $companies = Companies::where('tenant_id', auth()->user()->tenant_id)->get();
                $result =  '<div class="list-icons float-right">'.
                            '<div class="dropdown">'.
                                '<a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown"><i class="icon-menu"></i></a>'.
                                    '<div class="dropdown-menu dropdown-menu-right">'.
                                        '<a href="#" class="dropdown-item copy-opportunity-btn" data-lead_id="'. $row->lead_id .'" data-opportunity_id="'. $row->id .'" data-job_id="'. $row->job_id .'"><i class="fa fa-clipboard"></i> Copy</a>'.                                                        
                                    '</div>'.
                            '</div>'.
                        '</div>';
                return $result;
            })
            ->rawColumns(['job_number', 'created', 'job_date', 'pickup_suburb', 'drop_off_suburb', 'status', 'company', 'action'])
            ->make(true);
    }
    public function ajaxJobPopupData(Request $request)
    {
        // dd($request->all());
        $this->lead_id = $request->lead_id;
        $this->job_id = $request->job_id;
        $this->crmlead = CRMleads::where(['id' => $this->lead_id, 'tenant_id' => auth()->user()->tenant_id])->first();
        $this->job = $this->jobs = JobsMoving::select(
                        'jobs_moving.job_id',
                        'jobs_moving.job_number',
                        'jobs_moving.job_date',
                        'jobs_moving.pickup_address',
                        'jobs_moving.pickup_suburb',
                        'jobs_moving.pickup_post_code',
                        'jobs_moving.drop_off_address',
                        'jobs_moving.delivery_suburb',
                        'jobs_moving.drop_off_post_code',
                        'jobs_moving.job_status',
                        'jobs_moving.company_id',
                        'jobs_moving.payment_instructions',
                        'jobs_moving.created_at'
                    )
                        ->where(['jobs_moving.tenant_id' => auth()->user()->tenant_id, 'jobs_moving.opportunity' => 'N'])
                        ->where(['jobs_moving.customer_id' => $this->lead_id, 'jobs_moving.job_id' => $this->job_id])
                        ->orderBy('jobs_moving.job_id', 'desc')
                        ->first();

        $this->mobile = CRMContacts::select('crm_contact_details.detail')
                ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                ->where(['crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.lead_id' => $this->crmlead->id, 'crm_contact_details.detail_type' => 'Mobile'])
                ->pluck('detail')
                ->first();
        $this->email = CRMContacts::select('crm_contact_details.detail')
            ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
            ->where(['crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.lead_id' => $this->crmlead->id, 'crm_contact_details.detail_type' => 'Email'])
            ->pluck('detail')
            ->first();
        
        $response['error'] = 0;
        $response['data'] = $this->data;
        return json_encode($response);
    }
    public function ajaxOpportunityPopupData(Request $request)
    {
        $this->lead_id = $request->lead_id;
        $this->job_id = $request->job_id;
        $this->opportunity_id = $request->opportunity_id;
        $this->crmlead = CRMleads::where(['id' => $this->lead_id, 'tenant_id' => auth()->user()->tenant_id])->first();
        $this->opportunity = CRMOpportunities::select(
                        'crm_opportunities.id',
                            'crm_opportunities.lead_id',
                            'crm_opportunities.op_status',
                            'crm_opportunities.created_at',
                            'jobs_moving.job_date',
                            'jobs_moving.job_id',
                            'jobs_moving.job_number',
                            'jobs_moving.pickup_address',
                            'jobs_moving.pickup_suburb',
                            'jobs_moving.pickup_post_code',
                            'jobs_moving.drop_off_address',
                            'jobs_moving.delivery_suburb',
                            'jobs_moving.drop_off_post_code',
                            'companies.id AS company_id'
                        )
                        ->where('crm_opportunities.lead_id', $this->lead_id)
                        ->where('jobs_moving.opportunity', 'Y')
                        ->leftjoin('crm_leads', 'crm_leads.id', '=', 'crm_opportunities.lead_id')
                        ->leftjoin('jobs_moving', 'jobs_moving.crm_opportunity_id', 'crm_opportunities.id')
                        ->leftjoin('users', 'users.id', '=', 'crm_opportunities.user_id')
                        ->leftjoin('companies', 'companies.id', 'jobs_moving.company_id')
                        ->orderBy('crm_opportunities.created_at', 'desc')
                        ->first();
        $this->mobile = CRMContacts::select('crm_contact_details.detail')
                ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                ->where(['crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.lead_id' => $this->crmlead->id, 'crm_contact_details.detail_type' => 'Mobile'])
                ->pluck('detail')
                ->first();
        $this->email = CRMContacts::select('crm_contact_details.detail')
            ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
            ->where(['crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.lead_id' => $this->crmlead->id, 'crm_contact_details.detail_type' => 'Email'])
            ->pluck('detail')
            ->first();
        
        $response['error'] = 0;
        $response['data'] = $this->data;
        return json_encode($response);
    }
    public function getActivitiesForCustom(Request $request)
    {
        $this->job_ids=[0,$request->job_id];
        if($request->type == 'allactivities') {
            $this->notes = CRMActivityLog::where(['lead_id' => $request->lead_id, 'tenant_id' => auth()->user()->tenant_id])
            ->whereIn('log_type', array(3, 4, 5, 7, 8, 11, 14, 15))
            ->where(function ($query) {
                $query->whereIn('job_id', $this->job_ids)
                    ->orWhereNull('job_id');
            })
            ->orderBy('id', 'DESC')
            ->get();
        $response['message'] = 'All Activities';
        $response['btn_text'] = 'All Activities';
        } elseif($request->type == 'notes') {
            $this->notes = CRMActivityLog::where(['lead_id' => $request->lead_id, 'tenant_id' => auth()->user()->tenant_id])
            ->whereIn('log_type', array(7,15))
            ->where(function ($query) {
                $query->whereIn('job_id', $this->job_ids)
                    ->orWhereNull('job_id');
            })
            ->orderBy('id', 'DESC')
            ->get();
        $response['message'] = 'All Notes';
        $response['btn_text'] = 'Notes';
        } elseif($request->type == 'email') {
            $this->notes = CRMActivityLog::where(['lead_id' => $request->lead_id, 'tenant_id' => auth()->user()->tenant_id])
            ->whereIn('log_type', array(3, 4, 5))
            ->where(function ($query) {
                $query->whereIn('job_id', $this->job_ids)
                    ->orWhereNull('job_id');
            })
            ->orderBy('id', 'DESC')
            ->get();
        $response['message'] = 'All Email';
        $response['btn_text'] = 'Email';
        }else {
            $this->notes = CRMActivityLog::where(['lead_id' => $request->lead_id, 'tenant_id' => auth()->user()->tenant_id])
            ->whereIn('log_type', array(8, 9))
            ->where(function ($query) {
                $query->whereIn('job_id', $this->job_ids)
                    ->orWhereNull('job_id');
            })
            ->orderBy('id', 'DESC')
            ->get();
        $response['message'] = 'All SMS';
        $response['btn_text'] = 'SMS';
        }
        $this->lead_id = $request->lead_id;
        $this->ppl_people = PplPeople::where(['user_id' => auth()->user()->id, 'tenant_id' => auth()->user()->tenant_id])->first();
        $response['error'] = 0;
        $response['html'] = view('admin.crm-leads.activity_notes_grid', $this->data)->render();
        return json_encode($response);

    }
}