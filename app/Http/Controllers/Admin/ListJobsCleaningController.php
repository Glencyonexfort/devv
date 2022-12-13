<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\JobsMoving;
use App\Companies;
use App\CRMActivityLog;
use App\CRMContactDetail;
use App\CRMContacts;
use App\CRMLeads;
use App\CRMOpportunities;
use App\JobsMovingLogs;
use App\Lists;
use App\SMSTemplates;
use App\EmployeeDetails;
use App\Mail\CustomerMail;
use App\Vehicles;
use App\Http\Requests\Admin\User\StoreUser;
use App\Http\Requests\Admin\User\UpdateDriver;
use App\JobTemplatesMovingAttachment;
use App\Customers;
use App\Role;
use App\RoleUser;
use App\MovingInventoryDefinitions;
use App\MovingInventoryGroups;
use App\JobsMovingInventory;
use App\OrganisationSettings;
use App\JobsMovingLegs;
use App\JobTemplatesMoving;
use App\Invoice;
use App\InvoiceItems;
use App\User;
use App\Event;
use App\Setting;
use App\InvoiceSetting;
use App\EmailTemplates;
use App\Http\Requests\ListJobs\StoreNewJob;
use App\JobsCleaning;
use App\JobsCleaningAdditionalInfo;
use App\JobsCleaningShifts;
use App\JobsCleaningTeamMembers;
use App\JobsCleaningTeamRoster;
use App\JobsCleaningTeams;
use App\JobsCleaningType;
use App\JobsMovingLegTrips;
use App\ListTypes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Artisan;
use Exception;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Client;
use App\MovingInsuranceQuoteRequest;
use App\MovingInsuranceQuoteResponse;
use App\OfflinePaymentMethod;
use App\Payment;
use App\PplPeople;
use App\Product;
use App\PropertyCategoryOptions;
use App\QuoteItem;
use App\Quotes;
use App\SysCountryStates;
use App\SysNotificationLog;
use App\SysNotificationSetting;
use App\Tax;
use App\TenantApiDetail;
use Illuminate\Support\Facades\Hash;

class ListJobsCleaningController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.list_jobs');
        $this->pageIcon = 'ti-file';
    }

    public function index()
    {
        $this->job_status = Lists::job_status();
        $this->payment_status = Lists::payment_status();
        return view('admin.list-jobs-cleaning.index', $this->data);
    }

    public function changeStatus(Request $request, $job_leg_id)
    {
        try {
            $obj = JobsMovingLegs::find($job_leg_id);
            $obj->leg_status = $request->input('leg_status');
            $obj->save();
            return Reply::redirect(route('admin.list-jobs-cleaning.edit-job', [$obj->job_id]), __('messages.jobLegsStatusChanges'));
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function data(Request $request)
    {
        try {
            
            $result = JobsCleaning::select(
                'jobs_cleaning.job_id',
                'jobs_cleaning.job_number',
                'jobs_cleaning.job_date',
                'jobs_cleaning.address',
                'jobs_cleaning.job_status',
                'crm_leads.id as lead_id',
                'crm_leads.name'
            )
                ->leftjoin('crm_leads', 'crm_leads.id', 'jobs_cleaning.customer_id');

            $result = $result->where(['jobs_cleaning.tenant_id' => auth()->user()->tenant_id, 'jobs_cleaning.opportunity' => 'N'])
                ->orderBy('jobs_cleaning.job_id', 'desc');


            if ($request->created_date_start !== null && $request->created_date_start != 'null' && $request->created_date_start != '') {
                $startDate = Carbon::createFromFormat($this->global->date_format, $request->created_date_start)->toDateString();
                $result = $result->where(DB::raw('DATE(jobs_cleaning.`job_date`)'), '>=', $startDate);
            }
            if ($request->created_date_end !== null && $request->created_date_end != 'null' && $request->created_date_end != '') {
                $created_date_end = Carbon::createFromFormat($this->global->date_format, $request->created_date_end)->toDateString();
                $result = $result->where(DB::raw('DATE(jobs_cleaning.`job_date`)'), '<=', $created_date_end);
            }
            if ($request->job_date_start !== null && $request->job_date_start != 'null' && $request->job_date_start != '') {
                $startDate = Carbon::createFromFormat($this->global->date_format, $request->job_date_start)->toDateString();
                $result = $result->where(DB::raw('DATE(jobs_cleaning.`job_date`)'), '=', $startDate);
            }
            if ($request->job_status !== null && $request->job_status != 'null' && $request->job_status != '') {
                $job_status = explode(",", $request->job_status);
                $result = $result->wherein('job_status', $job_status);
            }
            if ($request->payment_status !== null && $request->payment_status != 'null' && $request->payment_status != '') {
                $payment_status = explode(",", $request->payment_status);
                $result = $result->wherein('status', $payment_status);
            }
            if ($request->hide_deleted_archived !== null && $request->hide_deleted_archived != 'null' && $request->hide_deleted_archived == '1') {
                $result = $result->where('jobs_cleaning.deleted', '=', '0');
            }
            $result = $result->get();
            // dd($result);
            return DataTables::of($result)
                ->addColumn('customer_name', function ($row) {
                    return $row->name;
                })
                ->editColumn('job_date', function ($row) {
                    return date('d/m/Y', strtotime($row->job_date));
                })
                ->addColumn('email', function ($row) {
                    if (isset($row->lead_id)) {
                        $email = CRMContacts::select('crm_contact_details.detail')
                            ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                            ->where(['crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.lead_id' => $row->lead_id, 'crm_contact_details.detail_type' => 'Email'])
                            ->pluck('detail')
                            ->first();
                        return $email;
                    } else {
                        return '';
                    }
                })
                ->addColumn('mobile', function ($row) {
                    if (isset($row->lead_id)) {
                        $mobile = CRMContacts::select('crm_contact_details.detail')
                            ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                            ->where(['crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.lead_id' => $row->lead_id, 'crm_contact_details.detail_type' => 'Mobile'])
                            ->pluck('detail')
                            ->first();
                        return $mobile;
                    } else {
                        return '';
                    }
                })
                ->addColumn('payment_status', function ($row) {
                    $status = '';
                    $invoice = Invoice::where(['job_id'=>$row->job_id, 'sys_job_type'=>'Cleaning'])
                        ->first();
                    if ($invoice) {
                        $status = ucfirst($invoice->status);
                    }
                    return $status;
                })
                ->addColumn('balance_payment', function ($row) {
                    $amount = 0;
                    $invoice = Invoice::where(['job_id'=>$row->job_id, 'sys_job_type'=>'Cleaning'])
                        ->first();
                    if ($invoice) {
                        // $sum_invoice_items = InvoiceItems::where('invoice_id', $invoice->id)
                        //     ->sum('amount');
                        $sum_invoice_items=$invoice->getTotalAmount();
                        $sum_payment = \App\Payment::where('invoice_id', $invoice->id)
                            ->sum('amount');
                        $amount = $sum_invoice_items - $sum_payment;
                    }
                    return $this->global->currency_symbol . number_format((float)$amount, 2, '.', '');
                })
                ->editColumn('job_number', function ($row) {
                    return '<a class="badge bg-blue" href="' . route("admin.list-jobs-cleaning.view-job", $row->job_id) . '" >' . $row->job_number . '</a>';
                    //return '<a class="badge bg-blue" href="#" >' . $row->job_number . '</a>';
                })
                ->rawColumns(['job_number'])
                ->make(true);
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function viewJob($job_id){                
        //try {
            if (empty($job_id)) {
                    return redirect(route('admin.list-jobs.index'));
            }
            $this->ppl_people = PplPeople::where('user_id',auth()->user()->id)->first();
            $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', auth()->user()->tenant_id)->first();
            $this->google_api_key = TenantApiDetail::where(['tenant_id'=> auth()->user()->tenant_id,'provider'=>'GoogleMaps'])->pluck('account_key')->first();
            $this->job_id = $job_id;
            $this->job = JobsCleaning::where('job_id', '=', $job_id)
            ->where('tenant_id', '=', auth()->user()->tenant_id)->first();

            $this->cleaning_shifts = JobsCleaningShifts::where(['tenant_id'=> auth()->user()->tenant_id])->get();

            $job_type = JobsCleaningType::where('id',$this->job->job_type_id)->first();    
            $job_shift = JobsCleaningShifts::where('id',$this->job->preferred_time_range)->first();
            $this->cleaning_job_type = ($job_type)?$job_type->job_type_name:'';
            $this->cleaning_job_shift = ($job_shift)?$job_shift->shift_display_start_time:'';
            
            //START:: *****************Invoice Tab***********************
            $this->stripe = TenantApiDetail::where('tenant_id', auth()->user()->tenant_id)
                    ->where('provider', 'Stripe')->first();
            $this->invoice = Invoice::where(['job_id'=> $job_id,'sys_job_type'=>'Cleaning'])->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            
            $this->invoice_items = 0;
            $this->payment_items = 0;
            if (isset($this->invoice->id)) :
                $this->invoice_items = InvoiceItems::where(['invoice_id'=>$this->invoice->id,'tenant_id'=>auth()->user()->tenant_id])->whereIn('type',['Item','Service'])->get();
                $this->invoice_charges = InvoiceItems::where(['invoice_id'=>$this->invoice->id,'tenant_id'=>auth()->user()->tenant_id,'type'=>'Charge'])->get();            
                $this->payment_items = Payment::where('invoice_id', '=', $this->invoice->id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
            endif;
            $this->payment_methods = OfflinePaymentMethod::where('tenant_id', '=', auth()->user()->tenant_id)->get();
            $this->taxs = Tax::where(['tenant_id' => auth()->user()->tenant_id])->get();            
            $this->paidAmount = 0;
            $this->totalAmount = 0;
            if (isset($this->invoice->id)) :
                $this->paidAmount = $this->invoice->getPaidAmount();
                $this->totalAmount = $this->invoice->getTotalAmount();
            endif;
            $this->products = Product::where(['tenant_id' => auth()->user()->tenant_id])->where('product_type', '<>', 'Charge')->get();
        $this->charges = Product::where(['tenant_id' => auth()->user()->tenant_id])->where('product_type', '=', 'Charge')->get();
            
        //START:: *****************Activity Tab***********************
            $this->lead_id = CRMOpportunities::where('id', '=', $this->job->crm_opportunity_id)
                                             ->where('tenant_id', '=', auth()->user()->tenant_id)->pluck('lead_id')->first();                                             
            $this->lead_name = CRMLeads::where('id', '=', $this->job->customer_id)->pluck('name')->first();

        $this->notes = CRMActivityLog::where('lead_id', $this->lead_id)
            ->whereIn('log_type', array(3, 4, 5, 7, 8, 11, 14, 15))
            ->orderBy('id', 'DESC')
            ->get();

            $this->job_status = Lists::job_status();
            // $this->price_structure = Lists::price_structure();
            // $this->payment_status = Lists::payment_status();
            // $this->lead_info = Lists::lead_info();
            $this->job_type = Lists::job_type();
            $this->leg_status = Lists::leg_status();              
            
            $this->contacts = CRMContacts::where(['tenant_id' => auth()->user()->tenant_id, 'lead_id' => $this->lead_id])->orderBy('id', 'DESC')->get();
            $this->crm_contacts = CRMContacts::where('lead_id', '=', $this->lead_id)->first();
            $this->crm_contact_email = CRMContactDetail::where('contact_id', '=', $this->crm_contacts->id)->where('detail_type', '=', 'Email')->first();
            $this->crm_contact_phone = CRMContactDetail::where('contact_id', '=', $this->crm_contacts->id)->where('detail_type', '=', 'Mobile')->first();
            $this->crm_contact_email = ($this->crm_contact_email)? $this->crm_contact_email->detail:'';
            $this->crm_contact_phone = ($this->crm_contact_phone)? $this->crm_contact_phone->detail:'';
            $this->companies = Companies::where(['tenant_id'=> auth()->user()->tenant_id,'id'=>$this->job->company_id])->first();
            $this->company_list = Companies::where(['tenant_id' => auth()->user()->tenant_id])->get();

            $this->sms_templates = SMSTemplates::where(['tenant_id' => auth()->user()->tenant_id])->get();
            $this->email_templates = EmailTemplates::where(['tenant_id' => auth()->user()->tenant_id])->get();
            $this->sms_contacts = DB::table('crm_contacts')
            ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
            ->select('crm_contacts.name', 'crm_contact_details.detail')
            ->where(['crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.lead_id' => $this->lead_id, 'crm_contact_details.detail_type' => 'Mobile'])
            ->get();
                        
            $this->states = SysCountryStates::where(['country_id'=>$this->organisation_settings->business_country_id])->get();
            //END:: Activity Tab
            
            //START:: Operation Tab
            $this->team_roaster = JobsCleaningTeamRoster::where(['job_id'=> $job_id,'tenant_id'=>auth()->user()->tenant_id])->first();
            $this->extras = DB::table('invoices')
            ->where(['invoices.job_id'=> $job_id, 'invoices.tenant_id' => auth()->user()->tenant_id,'invoice_items.item_name'=>'Extra service'])
            ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->select('invoice_items.*')
            ->get();

            $this->attachments = DB::table('crm_activity_log_attachments')
            ->join('crm_activity_log', 'crm_activity_log.id', '=', 'crm_activity_log_attachments.log_id')
            ->select('crm_activity_log_attachments.*')
            ->where(['crm_activity_log.tenant_id' => auth()->user()->tenant_id, 'crm_activity_log.lead_id' => $this->lead_id])
            ->get();

            //END:: Operation Tab
            $this->contact_types = ListTypes::where(['list_types.tenant_id' => auth()->user()->tenant_id, 'list_types.list_name' => 'Contact Type'])
                ->join('list_options', 'list_options.list_type_id', '=', 'list_types.id')
                ->select('list_options.list_option')
                ->get();
            
            return view('admin.list-jobs-cleaning.jobs.view_job', $this->data);
        // } catch (Exception $ex) {
        //     dd($ex->getMessage());
        // }
    }

    public function ajaxUpdateJobDetail(Request $request){
        
        $job_id = $request->input('job_id');
        $this->job_id = $job_id;
        $obj = JobsCleaning::where('job_id', '=', $job_id)
            ->where('tenant_id', '=', auth()->user()->tenant_id)->first();

        $obj->company_id = $request->input('company_id');
        $obj->preferred_time_range = $request->input('preferred_time_range');
        $obj->address = $request->input('address');
        $obj->bedrooms = $request->input('bedrooms');
        $obj->bathrooms = $request->input('bathrooms');
        $obj->job_status = $request->input('job_status');
        $obj->created_at = Carbon::now();
        $obj->save();

        $this->job=$obj;
        $this->companies = Companies::where(['tenant_id'=> auth()->user()->tenant_id,'id'=>$this->job->company_id])->first();
        $job_type = JobsCleaningType::where('id',$this->job->job_type_id)->first();    
        $job_shift = JobsCleaningShifts::where('id',$this->job->preferred_time_range)->first();
        $this->cleaning_job_type = ($job_type)?$job_type->job_type_name:'';
        $this->cleaning_job_shift = ($job_shift)?$job_shift->shift_display_start_time:'';
            

        $response['error'] = 0;
        $response['message'] = 'Job detail has been updated';
        $response['html'] = view('admin.list-jobs-cleaning.jobs.job_detail_grid', $this->data)->render();
        return json_encode($response);
    }

    public function ajaxUpdateTeamRoaster(Request $request){
        
        $id = $request->input('team_roaster_id');
        if($id==0){
            $obj = new JobsCleaningTeamRoster();
            $obj->job_date = Carbon::createFromFormat('d/m/Y', $request->input('job_date'))->format('Y-m-d');
            $obj->job_shift_id = $request->input('job_shift_id');
            $obj->team_id = $request->input('team_id');
            $obj->job_id = $request->input('job_id');
            $obj->tenant_id = auth()->user()->tenant_id;
            $obj->job_type_id = 2;
            $obj->created_at = Carbon::now();
            $obj->created_by = auth()->user()->id;
            $obj->save();
        }else{
            $obj = JobsCleaningTeamRoster::where('id', '=', $id)
                ->where('tenant_id', '=', auth()->user()->tenant_id)->first();

            $obj->job_date = Carbon::createFromFormat('d/m/Y', $request->input('job_date'))->format('Y-m-d');
            $obj->job_shift_id = $request->input('job_shift_id');
            $obj->team_id = $request->input('team_id');
            $obj->updated_at = Carbon::now();
            $obj->save();

        }

        $this->team_roaster=$obj;          

        $response['error'] = 0;
        $response['message'] = 'Record has been saved';
        $response['html'] = view('admin.list-jobs-cleaning.jobs.team_grid', $this->data)->render();
        return json_encode($response);
    }

    public function ajaxUpdateAdditionalInfo(Request $request){
        
        $id = $request->input('id');
        $job_id = $request->input('job_id');
        $reply = $request->input('reply');
        JobsCleaningAdditionalInfo::where('id', '=', $id)->update(['reply'=>$reply]);

        $this->job = JobsCleaning::where('job_id', '=', $job_id)
            ->where('tenant_id', '=', auth()->user()->tenant_id)->first();

        $response['error'] = 0;
        $response['message'] = 'Record has been updated';
        $response['html'] = view('admin.list-jobs-cleaning.jobs.additional_grid', $this->data)->render();
        return json_encode($response);
    }  

    public function ajaxNotifyTeamLead(Request $request){
        $id = $request->input('id');
        $job_id = $request->input('job_id');
        $this->job_id = $job_id;

        $this->job = JobsCleaning::where('job_id', '=', $job_id)
            ->where('tenant_id', '=', auth()->user()->tenant_id)->first(); 

        $obj = JobsCleaningTeamRoster::where('id', '=', $id)
                    ->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $obj->roster_status = 'Awaiting Confirmation';
        $obj->updated_at = Carbon::now();
        $obj->save();        

        //Response   
        $this->team_roaster=$obj;
        $response['error'] = 0;
        $response['team_id'] = $obj->team_id;
        $response['job_id'] = $this->job->job_id;
        $response['job_number'] = $this->job->job_number;
        $response['message'] = 'Status has been updated';
        $response['html'] = view('admin.list-jobs-cleaning.jobs.team_grid', $this->data)->render();
        return json_encode($response);
    }

    public function sendPushNotification(Request $request){
        $team_id = $request->input('team_id');
        $job_number = $request->input('job_number');
        $job_id = $request->input('job_id');

        $users = JobsCleaningTeamMembers::select('ppl_people.user_id')
                    ->join('ppl_people', 'ppl_people.id', '=', 'jobs_cleaning_team_members.person_id')
                    ->where('jobs_cleaning_team_members.team_id','=',$team_id)
                    ->where('jobs_cleaning_team_members.team_lead','=','Y')->get();
        if($users){
            foreach($users as $user){
            $device_token = User::where('id','=',$user->user_id)->whereNotNull('device_token')->pluck('device_token')->first();
            if($device_token){
            $params=[                    
                'job_number'=>$job_number
            ];

            $sys_notify = SysNotificationSetting::where('id','=',2)->first();
            if($sys_notify){                
                if($sys_notify->send_push=='Y'){
                    $template = $sys_notify->notification_message;                                            
                    if (preg_match_all("/{(.*?)}/", $template, $m)) {
                        foreach ($m[1] as $i => $varname) {
                            $template = str_replace($m[0][$i], sprintf('%s', $params[$varname]), $template);
                        }
                    } 


                    //Curl for FCM push notification
                    $url = env('FCM_SERVER_URI');
                    $serverKey = env('FCM_SERVER_KEY');
                    $title = $sys_notify->notification_name;
                    $body = $template;
                    $notification = array('title' =>$title , 'body' => $body, 'sound' => 'default', 'badge' => '1','url'=>'url=onexfort://s=job&j='.$job_id);
                    $arrayToSend = array('to' => $device_token, 'notification' => $notification,'priority'=>'high');
                    //print_r($arrayToSend);exit;

                    $json = json_encode($arrayToSend);
                    $headers = array();
                    $headers[] = 'Content-Type: application/json';
                    $headers[] = 'Authorization: key='. $serverKey;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
                    //Send the request
                    $response = curl_exec($ch);
                    //Close request
                    if ($response === FALSE) {
                        die('FCM Send Error: ' . curl_error($ch));
                    }
                    curl_close($ch);
                    $notification = new SysNotificationLog();
                    $notification->sys_notification_id = 2;
                    $notification->tenant_id = auth()->user()->tenant_id;
                    $notification->notification_type = 'push';
                    $notification->sent_to_id = $user->user_id;
                    $notification->sent_at = Carbon::now();
                    $notification->save();
                                       
                }
            }
        }
        }
        }
        
    }

    public function ajaxReassignTeam(Request $request){
        $id = $request->input('id');
        $job_id = $request->input('job_id');
        $team_id = $request->input('team_id');
        $this->job_id = $job_id;

        $this->job = JobsCleaning::where('job_id', '=', $job_id)
            ->where('tenant_id', '=', auth()->user()->tenant_id)->first(); 
            $obj = JobsCleaningTeamRoster::where('id', '=', $id)
                    ->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            $obj->roster_status = NULL;
            $obj->team_id = $team_id;
            $obj->updated_at = Carbon::now();
            $obj->save();

        //Response   
        $this->team_roaster=$obj;          
        $response['error'] = 0;
        $response['message'] = 'Team has been reassigned';
        $response['html'] = view('admin.list-jobs-cleaning.jobs.team_grid', $this->data)->render();
        return json_encode($response);
    }


    public function teamCalendar() {
        try {
            $this->pageTitle = __('app.menu.teamCalendar');
            $this->pageIcon = 'icon-calender';
            $this->employees = User::all();
            $this->events = Event::all();
            return view('admin.list-jobs-cleaning.team-calendar', $this->data);
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }  

    public function getTeams() {
        $teams = JobsCleaningTeams::where(['tenant_id' => $this->user->tenant_id, 'active' => 'Y'])
                ->get();
        $post_data = array();
        foreach ($teams as $team) {
            $post_data[] = array('id' => $team->id, 'title' => $team->team_name);
        }
        return response()->json($post_data);
    }

    public function getJobs() {
        $teams = JobsCleaningTeams::where(['tenant_id' => $this->user->tenant_id, 'active' => 'Y'])
                ->get();
        $post_data = array();
        foreach ($teams as $team) {
            $jobs = JobsCleaningTeamRoster::where('team_id', $team->id)
                    ->where('tenant_id', $this->user->tenant_id)
                    ->get();

            foreach ($jobs as $job) {

                $jobCleaning = JobsCleaning::where('job_id', '=', $job->job_id)
                    ->where('tenant_id', '=', auth()->user()->tenant_id)->first();

                $customer = CRMLeads::find($jobCleaning->customer_id);

                $numberOfHoursForJob = $jobCleaning->number_of_hours;

                $jobsCleaningShift = JobsCleaningShifts::where('id', '=', $job->job_shift_id)
                    ->where('tenant_id', '=', auth()->user()->tenant_id)->first();

                $shiftDisplayStartTime = $jobsCleaningShift->shift_display_start_time;
                $shiftDisplayStartTime = substr($shiftDisplayStartTime, 0, strpos($shiftDisplayStartTime, ' '));
                //echo "<pre>";
                    //var_dump();exit;
                $title = 'Job# ' . $jobCleaning->job_number . ' (' . strtoupper($customer->name) . ')';
                //$start = strtotime(date('Y-m-d ' . $job->job_start_time)) * 1000;
                //$end = strtotime(date('Y-m-d ' . $job->job_end_time)) * 1000;
                $start = date('Y-m-d', strtotime($jobCleaning->job_date)) . 'T' . date('H:i:s', strtotime($shiftDisplayStartTime));

                $endTimeCalculation = date('H:i:s', strtotime($shiftDisplayStartTime. ' +'. $numberOfHoursForJob. ' hours'));

                $post_data[] = array(
                    'allDay' => false,
                    'title' => $title,
                    'job_id' => $job->job_id,
                    'start' => date('Y-m-d', strtotime($jobCleaning->job_date)) . 'T' . date('H:i:s', strtotime($shiftDisplayStartTime)),
                    'end' => date('Y-m-d', strtotime($jobCleaning->job_date)) . 'T' . date('H:i:s', strtotime($endTimeCalculation)),
                    'resourceId' => $job->team_id,
                    //'className' => $eventBgClass,
                    'color' => $team->team_colour,
                );
            }
        }
        return response()->json($post_data);
    }

    /*public function updateScheduleEvent() {
        return view('updateScheduleEvent');
    }

    public function updateScheduleEventPost(Request $request) {
        $input = $request->all();

        $obj = JobsMoving::findOrFail($request->job_id);
        $obj->vehicle_id = $request->vehicle_id;
        $obj->job_start_time = $request->start_time;
        $obj->job_end_time = $request->end_time;
        $obj->job_date = $request->job_date;
        $obj->save();

        return response()->json(['success' => 'Schedule Updated!']);
    }*/

}
