<?php

namespace App\Http\Controllers;

use App\Companies;
use App\CRMActivityLog;
use App\CRMActivityLogAttachment;
use App\CRMContactDetail;
use App\CRMContacts;
use App\CRMLeads;
use App\CRMLeadStatuses;
use App\CRMOpPipelineStatuses;
use App\CRMOpportunities;
use App\Customers;
use App\EmailTemplates;
use App\EmailTemplateAttachments;
use App\Http\Controllers\Controller;
use App\Http\Requests\Quote\Quote;
use App\Invoice;
use App\JobsCleaning;
use App\JobsMoving;
use App\JobsMovingAutoQuoting;
use App\Mail\CustomerMail;
use App\Mail\sendMail;
use App\OrganisationSettings;
use App\PropertyCategoryOptions;
use App\QuoteItem;
use App\Quotes;
use App\SMSTemplates;
use App\Tax;
use App\Tenant;
use App\TenantApiDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class QuoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    // public function show_form($id)
    // {
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id, $company_id)
    {
        $session_data = array('step1' => array(), 'step2' => array(), 'step3' => array());
        $request = Input::get();
        $tenant = Tenant::select('tenant_name')->where('tenant_id', $id)->first();
        if ($tenant) {
            $view_data['step'] = '1';
            $step = Input::get('step');
            if ($step) {
                $view_data['step'] = $step;
            }
            $view_data['tenant_id'] = intval($id);
            $view_data['company_id'] = intval($company_id);

            if (isset($request['session_data']) && !empty($request['session_data'])) {
                $session_data = unserialize($request['session_data']);
            }

            $view_data['furnishing'] = PropertyCategoryOptions::select('options')->where('tenant_id', '=', $view_data['tenant_id'])->where('category_id', '=', '2')->get();
            $view_data['bedroom'] = PropertyCategoryOptions::select('options')->where('tenant_id', '=', $view_data['tenant_id'])->where('category_id', '=', '3')->get();
            $view_data['living_room'] = PropertyCategoryOptions::select('options')->where('tenant_id', '=', $view_data['tenant_id'])->where('category_id', '=', '4')->get();
            $view_data['other_room'] = PropertyCategoryOptions::select('options')->where('tenant_id', '=', $view_data['tenant_id'])->where('category_id', '=', '5')->get();
            $view_data['special_item'] = PropertyCategoryOptions::select('options')->where('tenant_id', '=', $view_data['tenant_id'])->where('category_id', '=', '6')->get();

            $view_data['step1_ary'] = $session_data['step1'];
            $view_data['step2_ary'] = $session_data['step2'];
            $view_data['step3_ary'] = $session_data['step3'];
            $view_data['session_data'] = serialize($session_data);
        } else {
            $view_data['step'] = '0';
            $view_data['tenant_id'] = '0';
            $view_data['company_id'] = '0';
            $view_data['something'] = 'wrong';
        }

        $view_data['tenant_api_details'] = TenantApiDetail::where(['tenant_id' => $id, 'provider' => 'GoogleMaps'])->first();
        $view_data['jobs_moving_auto_quoting'] = JobsMovingAutoQuoting::where(['tenant_id' => $id])->first();
        return view('quote.index', $view_data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $session_data = array('step1' => array(), 'step2' => array(), 'step3' => array());
        $request = Input::get();
        $tenant = Tenant::select('tenant_name')->where('tenant_id', $request['tenant_id'])->first();
        if ($tenant) {
            $view_data['tenant_id'] = '0';
            $view_data['company_id'] = '0';
            $view_data['step1_ary'] = array();
            $view_data['step2_ary'] = array();
            $view_data['step3_ary'] = array();

            if (isset($request['session_data']) && !empty($request['session_data'])) {
                $session_data = unserialize($request['session_data']);
                unset($request['session_data']);
            }

            $step = 1;
            $move_from_type = '';
            if (isset($request['step'])) {
                $view_data['tenant_id'] = $request['tenant_id'];
                $view_data['company_id'] = $request['company_id'];
                if ($request['step'] == '1') {
                    $session_data['step1'] = ['moving_from' => $request['moving_from'], 'moving_to' => $request['moving_to']];
                    // Session::put('step1', ['moving_from' => $request['moving_from'], 'moving_to' => $request['moving_to']]);
                    $step = 2;
                }
                if ($request['step'] == '2') {
                    // Session::put('step2', ['moving_from_type' => $request['move_from_type'], 'moving_to_type' => $request['move_to_type']]);
                    $session_data['step2'] = ['moving_from_type' => $request['move_from_type'], 'moving_to_type' => $request['move_to_type']];
                    $move_from_type = $request['move_from_type'];
                    $step = 3;
                }
                if ($request['step'] == '3') {

                    if ($request['move_from_type'] == 'Storage Facility') {
                        // Session::put('step3',['storage_cbm' => (isset($request['storage_cbm'])?$request['storage_cbm']:'')]);
                        $session_data['step3'] = ['storage_cbm' => (isset($request['storage_cbm']) ? $request['storage_cbm'] : '')];
                    } elseif ($request['move_from_type'] == 'Flat') {
                        // Session::put(
                        //     'step3',
                        //     [
                        //         'floor' => $request['floor'],
                        //         'stairs' => (isset($request['stairs'])?$request['stairs']:''),
                        //         'lift' => (isset($request['lift'])?$request['lift']:''),
                        //         'furnishing' => (isset($request['furnishing'])?$request['furnishing']:''),
                        //         'bedroom' => (isset($request['bedroom'])?$request['bedroom']:''),
                        //         'other_room' => (isset($request['other_room'])?$request['other_room']:''),
                        //         'other_room' => (isset($request['other_room'])?$request['other_room']:''),
                        //         'special_item' => (isset($request['special_item'])?$request['special_item']:'')
                        //     ]
                        // );
                        $session_data['step3'] = [
                            'floor' => $request['floor'],
                            'stairs' => (isset($request['stairs']) ? $request['stairs'] : ''),
                            'lift' => (isset($request['lift']) ? $request['lift'] : ''),
                            'furnishing' => (isset($request['furnishing']) ? $request['furnishing'] : ''),
                            'bedroom' => (isset($request['bedroom']) ? $request['bedroom'] : ''),
                            'other_room' => (isset($request['other_room']) ? $request['other_room'] : ''),
                            'other_room' => (isset($request['other_room']) ? $request['other_room'] : ''),
                            'special_item' => (isset($request['special_item']) ? $request['special_item'] : '')
                        ];
                    } else {
                        // Session::put(
                        //     'step3',
                        //     [
                        //         'furnishing' => (isset($request['furnishing'])?$request['furnishing']:''),
                        //         'bedroom' => (isset($request['bedroom'])?$request['bedroom']:''),
                        //         'living_room' => (isset($request['living_room'])?$request['living_room']:''),
                        //         'other_room' => (isset($request['other_room'])?$request['other_room']:''),
                        //         'special_item' => (isset($request['special_item'])?$request['special_item']:'')
                        //     ]
                        // );
                        $session_data['step3'] = [
                            'furnishing' => (isset($request['furnishing']) ? $request['furnishing'] : ''),
                            'bedroom' => (isset($request['bedroom']) ? $request['bedroom'] : ''),
                            'living_room' => (isset($request['living_room']) ? $request['living_room'] : ''),
                            'other_room' => (isset($request['other_room']) ? $request['other_room'] : ''),
                            'special_item' => (isset($request['special_item']) ? $request['special_item'] : '')
                        ];
                    }
                    $step = 4;
                }

                if ($request['step'] == '4') {

                    $contact_detail = CRMContactDetail::select('crm_contacts.lead_id')
                        ->join('crm_contacts', 'crm_contact_details.contact_id', '=', 'crm_contacts.id')
                        ->where('crm_contact_details.tenant_id', '=', $view_data['tenant_id'])
                        ->where('crm_contact_details.detail', '=', $request['email'])
                        ->first();

                    if ($contact_detail) {
                        $lead_id = $contact_detail->lead_id;

                        $contacts = CRMContacts::select('id')->where('lead_id', '=', $lead_id)
                            ->where('tenant_id', '=', $view_data['tenant_id'])
                            ->where('name', '=', $request['name'])->first();
                        if ($contacts) {
                            $contact_id = $contacts->id;
                        } else {
                            $obj = new CRMContacts();
                            $obj->name = $request['name'];
                            $obj->lead_id = $lead_id;
                            $obj->tenant_id = $view_data['tenant_id'];
                            $obj->save();
                            $contact_id = $obj->id;
                        }

                        $contact_detail = CRMContactDetail::select('id')->where('detail', '=', $request['phone'])
                            ->where('tenant_id', '=', $view_data['tenant_id'])
                            ->where('contact_id', '=', $contact_id)->first();
                        if ($contact_detail) {
                            // do nothing
                        } else {
                            $obj = new CRMContactDetail();
                            $obj->contact_id = $contact_id;
                            $obj->detail_type = 'Mobile';
                            $obj->detail = $request['phone'];
                            $obj->tenant_id = $view_data['tenant_id'];
                            $obj->save();
                        }

                        // $contact_detail = CRMContactDetail::select('id')->where('detail', '=', $request['email']))
                        //     ->where('tenant_id', '=', $view_data['tenant_id'])
                        //     ->where('contact_id', '=', $contact_id)->first();
                        // if ($contact_detail) {
                        //     // do nothing
                        // } else {
                        //     $obj = new CRMContactDetail();
                        //     $obj->contact_id = $contact_id;
                        //     $obj->detail_type  = 'Email';
                        //     $obj->detail = $request['email']);
                        //     $obj->tenant_id = $view_data['tenant_id'];
                        //     $obj->save();
                        // }
                    } else {

                        $lead_status = CRMLeadStatuses::select('lead_status')->where('tenant_id', '=', $view_data['tenant_id'])->where('sort_order', '=', '1')->first();

                        $obj = new CRMLeads();
                        $obj->name = $request['name'];
                        $obj->lead_status = $lead_status->lead_status;
                        $obj->tenant_id = $view_data['tenant_id'];
                        $obj->save();

                        $lead_id = $obj->id;

                        $obj = new CRMContacts();
                        $obj->name = $request['name'];
                        $obj->lead_id = $lead_id;
                        $obj->tenant_id = $view_data['tenant_id'];
                        $obj->save();

                        $contact_id = $obj->id;

                        $obj = new CRMContactDetail();
                        $obj->contact_id = $contact_id;
                        $obj->detail_type = 'Email';
                        $obj->detail = $request['email'];
                        $obj->tenant_id = $view_data['tenant_id'];
                        $obj->save();

                        $obj = new CRMContactDetail();
                        $obj->contact_id = $contact_id;
                        $obj->detail_type = 'Mobile';
                        $obj->detail = $request['phone'];
                        $obj->tenant_id = $view_data['tenant_id'];
                        $obj->save();
                    }

                    $pipeline_status = CRMOpPipelineStatuses::select('pipeline_status')->where('tenant_id', '=', $view_data['tenant_id'])->where('sort_order', '=', '1')->first();

                    $obj = new CRMOpportunities();
                    $obj->lead_id = $lead_id;
                    $obj->op_type = 'Moving';
                    $obj->op_status = $pipeline_status->pipeline_status;
                    if ($request['pickup_date_type'] == 'pickup_date') {
                        $old_data = $request['pickup_date'];
                        $new_date = @explode('/', $old_data);
                        $obj->est_job_date = $new_date[2] . '-' . $new_date[1] . '-' . $new_date[0];
                    }
                    $obj->op_frequency = 'One-time';
                    $obj->tenant_id = $view_data['tenant_id'];
                    $obj->save();

                    $opportunity_id = $obj->id;

                    // Get Minimum Goods Value for Insurance Quote
                    $minimum_goods_value = DB::table('jobs_moving_pricing_additional as t1')
                    ->select('t1.*')
                    ->where(['t1.tenant_id' => $view_data['tenant_id']])->pluck("minimum_goods_value")
                    ->first();

                    $res = JobsMoving::select(DB::raw('MAX(job_number) as max_job_number'))->where('jobs_moving.tenant_id', '=', $view_data['tenant_id'])->first();
                    $new_job_number = intval($res->max_job_number) + 1;

                    $step1_ary = $session_data['step1'];
                    $step2_ary = $session_data['step2'];
                    $step3_ary = $session_data['step3'];

                    $obj = new JobsMoving();
                    $obj->company_id = $view_data['company_id'];
                    $obj->job_type = 'Moving';
                    $obj->opportunity = 'Y';
                    $obj->crm_opportunity_id = $opportunity_id;
                    $obj->job_number = $new_job_number;
                    $obj->customer_id = $lead_id;
                    if ($request['pickup_date_type'] == 'pickup_date') {
                        $old_data = $request['pickup_date'];
                        $new_date = @explode('/', $old_data);
                        $obj->job_date = $new_date[2] . '-' . $new_date[1] . '-' . $new_date[0];
                    }
                    $obj->pickup_suburb = $step1_ary['moving_from'];
                    $obj->delivery_suburb = $step1_ary['moving_to'];
                    $obj->pickup_property_type = $step2_ary['moving_from_type'];
                    $obj->drop_off_property_type = $step2_ary['moving_to_type'];

                    if ($step2_ary['moving_from_type'] == 'Flat') {
                        $obj->pickup_furnishing = (isset($step3_ary['furnishing']) ? $step3_ary['furnishing'] : '');
                        $obj->pickup_bedrooms = (isset($step3_ary['bedroom']) ? $step3_ary['bedroom'] : '');
                        $obj->pickup_living_areas = (isset($step3_ary['living_room']) ? $step3_ary['living_room'] : '');
                        $obj->pickup_other_rooms = @implode(",", $step3_ary['other_room']);
                        $obj->pickup_speciality_items = @implode(",", $step3_ary['special_item']);
                        $obj->pickup_floor = $step3_ary['floor'];
                        $obj->pickup_has_lift = ($step3_ary['lift'] == '1') ? 'Y' : 'N';
                    } elseif ($step2_ary['moving_from_type'] == 'Storage Facility') {
                        $obj->storage_cbm = $step3_ary['storage_cbm'];
                    } else {
                        $obj->pickup_furnishing = (isset($step3_ary['furnishing']) ? $step3_ary['furnishing'] : '');
                        $obj->pickup_bedrooms = (isset($step3_ary['bedroom']) ? $step3_ary['bedroom'] : '');
                        $obj->pickup_living_areas = (isset($step3_ary['living_room']) ? $step3_ary['living_room'] : '');
                        $obj->pickup_other_rooms = @implode(",", $step3_ary['other_room']);
                        $obj->pickup_speciality_items = @implode(",", $step3_ary['special_item']);
                    }
                    $obj->other_instructions = $request['other_instructions'];
                    $obj->goods_value = $minimum_goods_value;
                    $obj->tenant_id = $view_data['tenant_id'];
                    $obj->save();

                    //Send Email to Company for new quote        
                        $company = Companies::find($view_data['company_id']);
                        if($company){
                            $email_data['to'] = $company->email;
                            $email_data['from_email'] = 'no-reply@onexfort.com';
                            $email_data['from_name'] = 'no-reply@onexfort.com';
                            $email_data['email_subject'] = 'A quote request has been submitted from the Onexfort form';
                            $email_data['email_body'] = 'Hi There<br/>
                                    <p>A quote request has been submitted from the Onexfort form on your website or landing page. The submitted request will appear in the Opportunity list of Onexfort.</p>
                                    <p>This is just a courtesy email. Please do not reply to this email.</p>
                                    <p>Kind regards,</p>
                                    <p>Team Onexfort</p>';
                            Mail::to($email_data['to'])->send(new sendMail($email_data));
                        }

                    $view_data['new_job_number'] = $new_job_number;

                    $step = 5;
                }
            }

            if ($step == '3') {
                $view_data['move_from_type'] = $move_from_type;
            }

            $view_data['furnishing'] = PropertyCategoryOptions::select('options')->where('tenant_id', '=', $view_data['tenant_id'])->where('category_id', '=', '2')->get();
            $view_data['bedroom'] = PropertyCategoryOptions::select('options')->where('tenant_id', '=', $view_data['tenant_id'])->where('category_id', '=', '3')->get();
            $view_data['living_room'] = PropertyCategoryOptions::select('options')->where('tenant_id', '=', $view_data['tenant_id'])->where('category_id', '=', '4')->get();
            $view_data['other_room'] = PropertyCategoryOptions::select('options')->where('tenant_id', '=', $view_data['tenant_id'])->where('category_id', '=', '5')->get();
            $view_data['special_item'] = PropertyCategoryOptions::select('options')->where('tenant_id', '=', $view_data['tenant_id'])->where('category_id', '=', '6')->get();

            $view_data['step'] = $step;

            $view_data['step1_ary'] = $session_data['step1'];
            $view_data['step2_ary'] = $session_data['step2'];
            $view_data['step3_ary'] = $session_data['step3'];
            $view_data['session_data'] = serialize($session_data);
        } else {
            $view_data['step'] = '0';
            $view_data['tenant_id'] = '0';
            $view_data['something'] = 'wrong';
        }
        
        $view_data['tenant_api_details'] = TenantApiDetail::where(['tenant_id' => $request['tenant_id'], 'provider' => 'GoogleMaps'])->first();
        $view_data['jobs_moving_auto_quoting'] = JobsMovingAutoQuoting::where(['tenant_id' => $request['tenant_id']])->first();
        return view('quote.index', $view_data);
    }

    public function sess()
    {
        // echo md5(0);
        // exit;
        // echo base64_encode(11);
        // echo md5(0);
        // echo encrypt(11);
        // exit;
        $data = Session::all();
        dd($data);
    }

    //START:: Cleaning Auto Quote Program
    public function autoQuoteCleaning()
    {
        ini_set('max_execution_time', 0);
        // Get All Tenants with auto quote enabled----------------------//
    
        $tenants = DB::table('jobs_cleaning_auto_quoting as t1')
            ->leftJoin('crm_op_pipeline_statuses as t2', 't2.id', '=', 't1.initial_op_status_id')
            ->select('t1.*', 't2.pipeline_status')
            ->where(['t1.auto_quote_enabled' => 'Y'])
            ->get();
    
        if (count($tenants)) {
            echo '<pre>';
            print_r($tenants);
            foreach ($tenants as $tenant) {
    
                // Job Moving for each tenant-------------------------//
    
                $job_cleaning = DB::table('crm_opportunities')
                    ->join('jobs_cleaning', 'jobs_cleaning.crm_opportunity_id', '=', 'crm_opportunities.id')
                    ->select('jobs_cleaning.*', 'crm_opportunities.id')
                    ->where(['crm_opportunities.tenant_id' => $tenant->tenant_id, 
                    'crm_opportunities.op_status' => $tenant->pipeline_status,
                    'crm_opportunities.op_type' => 'Cleaning'])
                    ->get();     
                echo '<pre>';
                    print_r($job_cleaning);
                    echo '</pre>';
                //Check Job Cleaning
                if (count($job_cleaning)) {
                    foreach ($job_cleaning as $job) {
                                     
                        $quote_file_url = $this->generateCleaningQuote($job->id,$job->tenant_id);
                        $quote_sms_file_url = substr($quote_file_url, strrpos($quote_file_url, '/public' )+1);
    
                        //****************************** Step 9 : Send Quote email to lead or Send Fail email to user ************************************
                        if ($tenant->send_auto_quote_email_to_customer == 'Y') {
    
                            $crm_contacts = CRMContacts::where(['lead_id' => $job->customer_id, 'tenant_id' => $job->tenant_id])->first();
                            $crm_contact_email = CRMContactDetail::where(['contact_id' => $crm_contacts->id, 'tenant_id' => $job->tenant_id])->where('detail_type', '=', 'Email')->first();
                            $crm_contact_phone = CRMContactDetail::where(['contact_id' => $crm_contacts->id, 'tenant_id' => $job->tenant_id])->where('detail_type', '=', 'Mobile')->first();
                            $customer_email = ($crm_contact_email)? $crm_contact_email->detail:'';
                            $customer_phone = ($crm_contact_phone)? $crm_contact_phone->detail:'';
                            $organisation_settings = OrganisationSettings::where('tenant_id', '=', $job->tenant_id)->first();
                            $paidAmount = 0;
                            $totalAmount = 0;
                            $external_inventory_form_param = base64_encode('tenant_id='.$job->tenant_id.'&job_id='.$job->job_id);
                            $external_inventory_form = request()->getSchemeAndHttpHost().'/removals-inventory-form/'.$external_inventory_form_param;

                        $data = [
                            'job_id' => $job->job_number,
                            'first_name' => $crm_contacts->name,
                            'last_name' => '',
                            'mobile' => $customer_phone,
                            'email' => $customer_email,
                            'job_date' => date('d-m-Y', strtotime($job->job_date)),
                            'total_amount' => $totalAmount,
                            'total_paid' => $paidAmount,
                            'total_due' => ($totalAmount - $paidAmount),
                            'external_inventory_form' => $external_inventory_form
                        ];
                        $files = [];
    
                            //if ($var_status != 'Fail') {

                            //START::Sending Success email
                                $email_template = EmailTemplates::where(['id' => $tenant->quote_email_template_id, 'tenant_id' => $job->tenant_id])->first();
                                if ($email_template) {                                                                     
                                    $emailData = $this->setEmailParameter($email_template->email_subject, $email_template->email_body, $data);
                                    $email_data['from_email'] = $email_template->from_email;
                                    $email_data['from_name'] = $email_template->from_email_name;
                                    $email_data['email_subject'] = $emailData['subject'];
                                    $email_data['email_body'] = $emailData['body'];
                                    $email_data['reply_to'] = $organisation_settings->company_email;
                                    $email_data['job_id'] = $job->job_id;
                                    $email_data['cc'] = '';
                                    $email_data['bcc'] = '';
                                    $email_data['to'] = $customer_email;                                 
    
                                        if($email_template->attach_quote=='Y'){
                                                $files[]=$quote_file_url;
                                        }
    
                                    }                                    
                                    $email_data['files'] = $files;
                                    Mail::to($email_data['to'])->send(new CustomerMail($email_data));
                                    echo '<br/><br/>Success Email Sent';                                                            
                            
                                    //Add Activity Log for email                                   
                                    $activitydata['lead_id'] = $job->customer_id;
                                    $activitydata['tenant_id'] = $tenant->tenant_id;
                                    $activitydata['log_type'] = 3; 
                                    $activitydata['log_from'] = $email_data['from_email'];
                                    $activitydata['log_to'] = $email_data['to'];
                                    $activitydata['log_subject'] = $email_data['email_subject'];
                                    $activitydata['log_message'] = $email_data['email_body'];
                                    $activitydata['log_date'] = Carbon::now();
                                    $model = CRMActivityLog::create($activitydata);                            

                                    if($model){
                                        //start:: Email Attachment log
                                        if(count($files)){
                                            $attach['attachment_type'] = $crm_contacts->name.' - Quote';
                                            $attach['attachment_content'] = $quote_file_url;
                                            $attach['log_id'] = $model->id;
                                            $attach['tenant_id'] = $tenant->tenant_id;
                                            $attach['created_at'] = Carbon::now();
                                            $attach['updated_at'] = Carbon::now();
                                            $model2 = CRMActivityLogAttachment::create($attach);
                                        }
                                        //end :: Email Attachment log
                                        echo '<br/><br/>Email Log Inserted';
                                    }
                                //END::Sending Success email                                                                
                        }                           
                        //End auto quoting email

                        //START:: Send SMS
                        if($tenant->send_auto_quote_sms_to_customer =='Y'){
                            $quote_file_url_sms = url('/'.$quote_sms_file_url);
                            $this->sendSMS($job->customer_id,$tenant->tenant_id,$customer_phone,$tenant->quote_sms_template_id,$quote_file_url_sms);
                        }
                        //END:: Send SMS

                        $quote_total = QuoteItem::select(DB::raw('sum(quote_items.amount) as total'))
                                ->join('quotes', 'quotes.id', '=', 'quote_items.quote_id')
                                ->where('quotes.crm_opportunity_id', '=', $job->crm_opportunity_id)->first();
                        $var_total_value = $quote_total->total;
                        
                        $OppStatus = CRMOpPipelineStatuses::where(['id' => $tenant->quoted_op_status_id, 'tenant_id' => $tenant->tenant_id])->first();
                            if ($OppStatus) {
                                CRMOpportunities::where('id', $job->crm_opportunity_id)
                                    ->update([
                                        'op_status' => $OppStatus->pipeline_status,
                                        'value' => $var_total_value,
                                    ]);
                            }
                        //Add Activity Log
                        $data['log_message'] = "Auto quote completed.";
                        $data['lead_id'] = $job->customer_id;
                        $data['tenant_id'] = $tenant->tenant_id;
                        $data['log_type'] = 7; // Success Auto Qoute Program
                        $data['log_date'] = Carbon::now();
                        $model = CRMActivityLog::create($data);
                        unset($data);
                        //-- 
                        echo '</fieldset>';
                    } // end inner for loop
                }
                //break;
            } // end outer for loop
        }
    }

    public function generateCleaningQuote($opportunity_id,$tenant_id)
    {    
        //try{
        $this->opportunity = CRMOpportunities::where('id', '=', $opportunity_id)->where('tenant_id', '=', $tenant_id)->first();
            $this->taxs = Tax::where(['tenant_id' => $tenant_id])->first();
            $this->sub_total = 0;
            $this->quote_total = 0;
            $this->tax_total = 0;
            $this->deposit_required = 0;
            $this->booking_fee = 0;
            $this->count = 0;
            $this->show_estimate_range=0;
            $this->estimate_lower_percent=0;

            // Job Deposit Required for the tenant-------------------//

            $auto_quote = DB::table('jobs_cleaning_auto_quoting as t1')
                ->where(['t1.auto_quote_enabled' => 'Y','tenant_id'=>$tenant_id])
                ->first();    

            $this->job = JobsCleaning::where('crm_opportunity_id', '=', $this->opportunity->id)->where('tenant_id', '=', $tenant_id)->first();
            $this->quote = Quotes::where('crm_opportunity_id', '=', $this->opportunity->id)->where('tenant_id', '=', $tenant_id)->first();
            if ($this->quote) {
                $this->quoteItems = QuoteItem::where(['quote_id' => $this->quote->id, 'tenant_id' => $tenant_id])->get();

                $sub_total = QuoteItem::select(DB::raw('sum(quote_items.unit_price * quote_items.quantity) as total'))
                    ->where('quote_items.quote_id', '=', $this->quote->id)
                    ->where(['quote_items.tenant_id' => $tenant_id])
                    ->first();
                $this->sub_total = $sub_total->total;

                // if ($this->quoteItems) {
                //     foreach ($this->quoteItems as $qitm) {
                //         $subtotal = floatval($qitm->amount);
                //         $this->quote_total += $subtotal;
                //         if (isset($this->taxs->rate_percent) && floatval($qitm->amount) > 0)
                //             $this->tax_total += floatval($this->taxs->rate_percent) * ((floatval($subtotal)) / 100);
                //     }
                // }

            }
            if ($auto_quote->deposit_required == 'Y') {
                $this->deposit_required = $auto_quote->deposit_amount;
            } else {
                $this->booking_fee = $this->quote_total;
            }
            // following line removed in FORT-34 
            // $this->deposit_required = (floatval($this->grand_total) / 100) * 25;
            if ($this->job) {
                $this->companies = Companies::where('id', '=', $this->job->company_id)->where('tenant_id', '=', $tenant_id)->first();
                // dd($this->companies);
            }
            $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', $tenant_id)->first();
            $this->crm_leads = CRMLeads::where(['id' => $this->opportunity->lead_id, 'tenant_id' => $tenant_id])->first();
            $this->crm_contacts = CRMContacts::where(['lead_id' => $this->opportunity->lead_id, 'tenant_id' => $tenant_id])->first();
            $this->crm_contact_email = CRMContactDetail::where(['contact_id' => $this->crm_contacts->id, 'tenant_id' => $tenant_id])->where('detail_type', '=', 'Email')->first();
            $this->crm_contact_phone = CRMContactDetail::where(['contact_id' => $this->crm_contacts->id, 'tenant_id' => $tenant_id])->where('detail_type', '=', 'Mobile')->first();     
            $this->company_logo_exists = false;
            
            //Book now url
            if ($auto_quote->deposit_required == 'Y') {
                $this->url_params = base64_encode('quote_id=' . $this->quote->id . '&deposit_required=' . $this->deposit_required);
                $this->url_link = request()->getSchemeAndHttpHost() . '/pay-now/' . $this->url_params;
                $is_booking_fee=0;
            }else{
                $this->url_params = base64_encode('quote_id=' . $this->quote->id . '&booking_fee=' . $this->booking_fee);
                $this->url_link = request()->getSchemeAndHttpHost() . '/pay-now-booking-fee/' . $this->url_params;
                $is_booking_fee=-1;                
            }
            $filename = time();
            
            if (isset($this->companies)) {

                $file_number = 1;
                if (!empty($this->quote->quote_file_name)) {
                    $filename = str_replace('.pdf', '', $this->quote->quote_file_name);
                    $fn_ary = explode('_', $filename);
                    $file_number = intval($fn_ary[3]) + 1;
                }

                if (File::exists(public_path() . '/user-uploads/company-logo/' . $this->companies->logo)) {
                    $this->company_logo_exists = true;
                }

                $filename = 'Quote_' . $this->companies->company_name . '_'  . $this->quote->quote_number . '_' . rand() . '.pdf';

                $pdf = app('dompdf.wrapper');
                $pdf->loadView('admin.crm-leads.quote', 
                [
                    'organisation_settings'=>$this->organisation_settings,
                    'companies'=>$this->companies,
                    'company_logo_exists'=>$this->company_logo_exists,
                    'count'=>0,
                    'crm_contact_phone'=>$this->crm_contact_phone,
                    'crm_contact_email'=>$this->crm_contact_email,
                    'crm_contacts'=>$this->crm_contacts,
                    'crm_leads'=>$this->crm_leads,
                    'job'=>$this->job,
                    'quote'=>$this->quote,
                    'quoteItems'=>$this->quoteItems,
                    'taxs'=>$this->taxs,
                    'deposit_required'=>$this->deposit_required,
                    'sub_total'=>$this->sub_total,
                    'url_link'=>$this->url_link,
                    'is_booking_fee'=>$is_booking_fee,
                    'booking_fee'=>$this->booking_fee,
                    'show_estimate_range' => $this->show_estimate_range,
                    'estimate_lower_percent' => $this->estimate_lower_percent
                ]);
                // return $pdf->stream(); // to view pdf
                // return $pdf->download('tmp.pdf');
                $pdf->save(public_path().'/quote-files/' . $filename);
                
                if (File::exists(public_path() . '/quote-files/' . $this->quote->quote_file_name)) {
                    File::delete(public_path() . '/quote-files/' . $this->quote->quote_file_name);
                }
                $this->quote->quote_file_name = $filename;
                $this->quote->save();
                return public_path('quote-files') . '/' . $this->quote->quote_file_name;
        }    
    }

    private function sendSMS($lead_id,$tenant_id,$sms_to,$template_id,$pdf_link)
    {
        $tenant_details = \App\TenantDetail::where('tenant_id', $tenant_id)->first();
        $template = SMSTemplates::where(['id' => $template_id, 'tenant_id' => $tenant_id])->first();
        $companies = Companies::where(['tenant_id'=>$tenant_id,'active'=>'Y'])->first();
        $sms_from = $companies->sms_number;        

        if ($tenant_details->sms_credit <= 0) {
            $response['error'] = 1;
            $response['message'] = '(‘Not enough credit to buy SMS. Please buy SMS credits.';
            //Add Activity Log
            $data['log_message'] = 'Not enough credit to buy SMS. Please buy SMS credits.';
            $data['lead_id'] = $lead_id;
            $data['log_from'] = $sms_from;
            $data['log_to'] = $sms_to;
            $data['tenant_id'] = $tenant_id;
            $data['log_type'] = 7; // Activity SMS Fail
            $data['log_date'] = Carbon::now();
            $model = CRMActivityLog::create($data);
        } else {
            $sys_api_details = \App\SysApiSettings::where('type', '=', 'sms_gateway')->first();
            $sys_api_details->user;
            $sys_api_details->password;

            if($template->attach_quote=='Y'){
                $sms_message = $template->sms_message.'\n'.$pdf_link;
            }else{
                $sms_message = $template->sms_message;
            }

            $sms_total_credits = ceil(strlen($sms_message)/160);

            $username = $sys_api_details->user;
            $password = $sys_api_details->password;

            $content = 'username=' . rawurlencode($username) .
                '&password=' . rawurlencode($password) .
                '&to=' . rawurlencode($sms_to) .
                '&from=' . rawurlencode($sms_from) .
                '&message=' . rawurlencode($sms_message) .
                '&ref=' . rawurlencode($lead_id);
            //Send SMS
            $smsbroadcast_response = $this->sendSMSFunc($content);
            $response_lines = explode("\n", $smsbroadcast_response);
            print_r($response_lines);
            //--
            foreach ($response_lines as $data_line) {
                $message_data = "";
                $message_data = explode(':', $data_line);
                if ($message_data[0] == "OK") {
                    //Update company credit
                    $tenant_total_credits = $tenant_details->sms_credit;
                    $subtractCredits = $tenant_details->sms_credit - $tenant_total_credits;
                    $subtractCredits = $tenant_total_credits - $sms_total_credits;
                    $tenant_details->id = $tenant_id;
                    $UpdateTenantCredits = \App\TenantDetail::where('tenant_id', '=', $tenant_id)->update(array('sms_credit' => $subtractCredits));
                    //---

                    //Add Activity Log
                    $data['log_message'] = $sms_message;
                    $data['lead_id'] = $lead_id;
                    $data['log_from'] = $sms_from;
                    $data['log_to'] = $sms_to;
                    $data['tenant_id'] = $tenant_id;
                    $data['log_type'] = 8; // Activity SMS
                    $data['log_date'] = Carbon::now();
                    $model = CRMActivityLog::create($data);                    
                }
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

    //START:: Removal Auto Qoute Program
    public function autoQuote()
    {
        ini_set('max_execution_time', 0);
        // Get All Tenants with auto quote enabled----------------------//
    
        $tenants = DB::table('jobs_moving_auto_quoting as t1')
            ->leftJoin('crm_op_pipeline_statuses as t2', 't2.id', '=', 't1.initial_op_status_id')
            ->select('t1.*', 't2.pipeline_status')
            ->where(['t1.auto_quote_enabled' => 'Y'])
            ->get();
    
        if (count($tenants)) {
            echo '<pre>';
            print_r($tenants);
            foreach ($tenants as $tenant) {
    
                // Job Moving Price for each tenant-------------------//
    
                $job_price = DB::table('jobs_moving_pricing_additional as t1')
                    ->select('t1.*')
                    ->where(['t1.tenant_id' => $tenant->tenant_id])
                    ->first();
    
                // echo '<pre>';
                //     print_r($job_price);
                //     echo '</pre>';
    
                // Job Moving for each tenant-------------------------//
    
                $job_moving = DB::table('crm_opportunities')
                    ->join('jobs_moving', 'jobs_moving.crm_opportunity_id', '=', 'crm_opportunities.id')
                    ->select('jobs_moving.*', 'crm_opportunities.lead_id')
                    ->where(['crm_opportunities.tenant_id' => $tenant->tenant_id, 
                    'crm_opportunities.op_status' => $tenant->pipeline_status,
                    'crm_opportunities.op_type' => 'Cleaning'])
                    ->whereNotNull('jobs_moving.pickup_property_type')
                    ->whereNotNull('jobs_moving.pickup_suburb')
                    ->whereNotNull('jobs_moving.delivery_suburb')
                    ->get();
                // echo '<pre>';
                //     print_r($job_moving);
                //     echo '</pre>';
                // Check Job Moving
                if (count($job_moving)) {
                    foreach ($job_moving as $job) {
    
                        $var_cbm = 0;
                        $var_pickup_region_id = '';
                        $var_pickup_region = '';
                        $var_pickup_km_nearest_region = 0;
                        $var_drop_off_region_id = '';
                        $var_drop_off_region = '';
                        $var_drop_off_km_nearest_region = 0;
                        $var_pickup_excess_charges = 0;
                        $var_drop_off_excess_charges = 0;
                        $var_pickup_stairs_lift_charges = 0;
                        $var_drop_off_stairs_lift_charges = 0;
                        $var_status = '';
                        $var_fail_reason = '';
                        $var_removal_fee = 0;
                        $cbm_per_bedroom = 0;
                        $cbm_per_living_room = 0;
                        $var_removal_fee = 0;
                        $var_price_structure = '';
                        $var_use_hourly_pricing = '';
                        $var_depot_to_pickup_time = 0;
                        $var_drop_off_to_depot_time = 0;
                        $var_pickup_to_dropoff_time = 0;
                        $var_loading_unloading_time = 0;
                        $var_excess_time = 0;
                        $var_hourly_rate = 0;
                        $var_total_time = 0;
    
                        //*************************/ Step 1 : Calculate the cbm **********************//
    
                        //start:: property type House or Flat
                        if ($job->pickup_property_type == "House" || $job->pickup_property_type == "Flat") {
                            $other_rooms = explode(',', $job->pickup_other_rooms);
    
                            $pickup_other_rooms_value = DB::table('property_category_options as t1')
                                ->where(['t1.tenant_id' => $tenant->tenant_id])
                                ->whereIn('t1.options', $other_rooms)
                                ->sum('t1.m3_value');
    
                            $speciality_items = explode(',', $job->pickup_speciality_items);
    
                            $pickup_speciality_items = DB::table('property_category_options as t1')
                                ->where(['t1.tenant_id' => $tenant->tenant_id])
                                ->whereIn('t1.options', $speciality_items)
                                ->sum('t1.m3_value');
    
                            $pickup_furnishing = DB::table('property_category_options as t1')
                                ->select('t1.other_value')
                                ->where(['t1.tenant_id' => $tenant->tenant_id, 't1.options' => $job->pickup_furnishing])
                                ->first();
    
                            if (isset($job_price)) {
                                $cbm_per_bedroom = $job_price->cbm_per_bedroom;
                                $cbm_per_living_room = $job_price->cbm_per_living_room;
                            }
                            $var_cbm = ($job->pickup_bedrooms * $cbm_per_bedroom) + ($job->pickup_living_areas * $cbm_per_living_room) + $pickup_other_rooms_value + $pickup_speciality_items;
    
                            if (isset($pickup_furnishing)) {
                                $var_cbm = $var_cbm * $pickup_furnishing->other_value;
                            }
                            //end:: property type House or Flat
    
                        } elseif ($job->pickup_property_type == "Storage Facility") {
                            $var_cbm = $job->storage_cbm;
                        }

                        echo '<br/><fieldset>Tenant:' . $tenant->tenant_id;
                        echo '<br/>Job Id:' . $job->job_id;
                        echo '<br/>Calculated CBM:' . $var_cbm;
                        echo '<br/><br/>Pickup Suburb: ' . $job->pickup_suburb;
                        echo '<br/><br/>Delivery Suburb: ' . $job->delivery_suburb;
    
                        //*********************/ Step 2 : Calculate pickup suburb's nearest region and distance ********************//
    
                        $pickupRegionDist = $this->calculateDistancebyNearestRegion($tenant->tenant_id, $job->pickup_suburb);
                        if ($pickupRegionDist['is_true'] == 1) {
                            $region_pickup_detail = explode('|', $pickupRegionDist['region']);
                            $var_pickup_region_id = $region_pickup_detail['0'];
                            $var_pickup_region = $region_pickup_detail['1'];
                        }
                        $var_pickup_km_nearest_region = $pickupRegionDist['min_distance'];
    
                        //********************* Step 3 : Calculate delivery suburb's nearest region and distance ********************
    
                        $deliveryRegionDist = $this->calculateDistancebyNearestRegion($tenant->tenant_id, $job->delivery_suburb);
                        if ($deliveryRegionDist['is_true'] == 1) {
                            $region_drop_off_detail = explode('|', $deliveryRegionDist['region']);
                            $var_drop_off_region_id = $region_drop_off_detail['0'];
                            $var_drop_off_region = $region_drop_off_detail['1'];
                        }
                        $var_drop_off_km_nearest_region = $deliveryRegionDist['min_distance'];
    
                        echo '<br/><br/>DISTANCE';
                        echo '<br/>var_pickup_region_id: ' . $var_pickup_region_id;
                        echo '<br/>var_pickup_region: ' . $var_pickup_region;
                        echo '<br/>var_pickup_km_nearest_region: ' . $var_pickup_km_nearest_region;
    
                        echo '<br/>var_drop_off_region_id: ' . $var_drop_off_region_id;
                        echo '<br/>var_drop_off_region: ' . $var_drop_off_region;
                        echo '<br/>var_drop_off_km_nearest_region: ' . $var_drop_off_km_nearest_region;
    
                        if ($job_price) {
    
                            //************************* Step 3.5 : Find the Price Stucture*******************************
    
                            $var_use_hourly_pricing = $job_price->use_hourly_pricing_local_moves;
                            if ($var_pickup_region_id == $var_drop_off_region_id && $var_use_hourly_pricing == "Y") {
                                $var_price_structure = 'Hourly';
                            } else {
                                $var_price_structure = 'Fixed';
                            }
                            echo '<br/><br/>var_price_structure: ' . $var_price_structure;
    
                            //********************* Step 4 : Calculate pickup excess charges and lift/stairs charges ********************
    
                            // ************* (a) pickup excess charges
                            if ($var_price_structure == 'Fixed') {
                                if ($var_pickup_km_nearest_region > $job_price->excess_km_range_max) {
                                    $var_status = 'Fail';
                                    $var_fail_reason = 'Autoquote Failed reason: Pickup Suburb is greater than Maximum Excess Km of the region';
                                    echo "<br/><br/>FAIL:::::" . $var_fail_reason;
                                } elseif ($var_pickup_km_nearest_region > $job_price->excess_km_range) {
                                    $var_pickup_excess_km = $var_pickup_km_nearest_region - $job_price->excess_km_range;
                                    $var_pickup_excess_charges = $var_pickup_excess_km * $job_price->price_per_excess_km * 2;
                                } else {
                                    $var_pickup_excess_charges = 0;
                                }
                            } else {
                                $var_pickup_excess_charges = 0;
                            }
    
                            // ************* (b) pickup lift/stairs charges
    
                            if (empty($job->pickup_floor) || is_null($job->pickup_floor)) {
                                $var_pickup_stairs_lift_charges = 0;
                            } elseif ($job->pickup_floor > 0 && $job->pickup_has_lift == 'N') {
                                $var_pickup_stairs_lift_charges = $job_price->stairs_access_charge_per_floor_per_cbm * $job->pickup_floor * $var_cbm;
                            } elseif ($job->pickup_floor > 0 && $job->pickup_has_lift == 'Y') {
                                $var_pickup_stairs_lift_charges = $job_price->lift_access_charge_per_cbm * $var_cbm;
                            } else {
                                $var_pickup_stairs_lift_charges = 0;
                            }
    
                            //********************* Step 5 : Calculate delivery excess charges and lift/stairs charges ********************
    
                            // ************* (a) delivery excess charges
                            if ($var_price_structure == 'Fixed') {
                                if ($var_drop_off_km_nearest_region > $job_price->excess_km_range_max) {
                                    $var_status = 'Fail';
                                    $var_fail_reason = 'Autoquote Failed reason: Drop off Suburb is greater than Maximum Excess Km of the region';
                                    echo "<br/><br/>FAIL:::::" . $var_fail_reason;
                                } elseif ($var_drop_off_km_nearest_region > $job_price->excess_km_range) {
                                    $var_drop_off_excess_km = $var_drop_off_km_nearest_region - $job_price->excess_km_range;
                                    $var_drop_off_excess_charges = $var_drop_off_excess_km * $job_price->price_per_excess_km * 2;
                                } else {
                                    $var_drop_off_excess_charges = 0;
                                }
                            } else {
                                $var_drop_off_excess_charges = 0;
                            }
                            // ************* (b) delivery lift/stairs charges
    
                            if (empty($job->drop_off_floor) || is_null($job->drop_off_floor)) {
                                $var_drop_off_stairs_lift_charges = 0;
                            } elseif ($job->drop_off_floor > 0 && $job->drop_off_has_lift == 'N') {
                                $var_drop_off_stairs_lift_charges = $job_price->stairs_access_charge_per_floor_per_cbm * $job->drop_off_floor * $var_cbm;
                            } elseif ($job->drop_off_floor > 0 && $job->drop_off_has_lift == 'Y') {
                                $var_drop_off_stairs_lift_charges = $job_price->lift_access_charge_per_cbm * $var_cbm;
                            } else {
                                $var_drop_off_stairs_lift_charges = 0;
                            }
                        }
                        echo '<br/><br/>_______________________________________';
                        echo '<br/><br/>var_pickup_excess_charges: ' . $var_pickup_excess_charges;
                        echo '<br/>var_pickup_stairs_lift_charges: ' . $var_pickup_stairs_lift_charges;
                        echo '<br/><br/>var_drop_off_excess_charges: ' . $var_drop_off_excess_charges;
                        echo '<br/>var_drop_off_stairs_lift_charges: ' . $var_drop_off_stairs_lift_charges;
                        echo '<br/><br/>_______________________________________';
    
                        //****************************** Step 6 : Calculate the Removal Fee ************************************
                        if ($var_price_structure == 'Hourly') {
                            $depot_locations = DB::table('jobs_moving_depot_locations as t1')
                                ->select('t1.*')
                                ->where(['t1.tenant_id' => $tenant->tenant_id, 't1.region_id' => $var_pickup_region_id])
                                ->get();
                            if (count($depot_locations)) {
                                if($job_price->hourly_pricing_include_depot_pickup=='Y'){
                                //**************** */ Find Duration between Pickup Suburb to Closest Depot
    
                                    $pickupRegionTime = $this->calculateTimebyNearestRegion($job->pickup_suburb, $depot_locations, $tenant->tenant_id);
                                    if ($pickupRegionTime['is_true'] == 1) {
                                        $region_pickup_time_detail = explode('|', $pickupRegionTime['region']);
                                        $var_depot_to_pickup_id = $region_pickup_time_detail['0'];
                                        $var_depot_to_pickup_region = $region_pickup_time_detail['1'];
                                    }
                                    $var_depot_to_pickup_time = $pickupRegionTime['min_duration'];
                                }else{
                                    $var_depot_to_pickup_time=0;
                                    $var_depot_to_pickup_id = NULL;
                                    $var_depot_to_pickup_region = NULL;
                                }
                                
                                if($job_price->hourly_pricing_include_drop_off_depot=='Y'){
                                //**************** */ Find Duration between Drop off Suburb to Closest Depot
    
                                    $deliveryRegionTime = $this->calculateTimebyNearestRegion($job->delivery_suburb, $depot_locations, $tenant->tenant_id);
                                    if ($deliveryRegionTime['is_true'] == 1) {
                                        $region_delivery_time_detail = explode('|', $deliveryRegionTime['region']);
                                        $var_drop_off_to_depot_id = $region_delivery_time_detail['0'];
                                        $var_drop_off_to_depot_region = $region_delivery_time_detail['1'];
                                    }
                                    $var_drop_off_to_depot_time = $deliveryRegionTime['min_duration'];
                                }else{
                                    $var_drop_off_to_depot_time = 0;
                                    $var_drop_off_to_depot_id = NULL;
                                    $var_drop_off_to_depot_region = NULL;
                                }
                                echo '<br/><br/>TIME';
                                echo '<br/>var_depot_to_pickup_id: ' . $var_depot_to_pickup_id;
                                echo '<br/>var_depot_to_pickup_region: ' . $var_depot_to_pickup_region;
                                echo '<br/>var_depot_to_pickup_time: ' . $var_depot_to_pickup_time;
                                echo '<br/>var_drop_off_to_depot_id: ' . $var_drop_off_to_depot_id;
                                echo '<br/>var_drop_off_to_depot_region: ' . $var_drop_off_to_depot_region;
                                echo '<br/>var_drop_off_to_depot_time: ' . $var_drop_off_to_depot_time;
    
                                //**************** */ Find Duration between Pickup Suburb to Delivery Suburb
                                if($job_price->hourly_pricing_include_pickup_drop_off=='Y'){                                    
                                    $var_pickup_to_dropoff_time = $this->getDistance($job->pickup_suburb, $job->delivery_suburb, 'T');
                                }else{
                                    $var_pickup_to_dropoff_time=0;
                                }
                                echo '<br/><br/><br/>var_pickup_to_dropoff_time: ' . $var_pickup_to_dropoff_time;
    
                                $duration_rate = DB::table('jobs_moving_local_moves')
                                    ->select('loading_mins', 'unloading_mins', 'hourly_rate')
                                    ->where('tenant_id', '=', $tenant->tenant_id)
                                    ->where('min_cbm', '<=', $var_cbm)
                                    ->where('max_cbm', '>=', $var_cbm)
                                    ->first();
                                if ($duration_rate) {
                                    $var_loading_unloading_time = $duration_rate->loading_mins + $duration_rate->unloading_mins;
                                    $var_hourly_rate = $duration_rate->hourly_rate;
                                }

                                if($job_price->hourly_pricing_include_loading_time=='N' && $job_price->hourly_pricing_include_unloading_time=='N'){
                                    $var_loading_unloading_time = 0;
                                }
                                
                                if($job_price->hourly_pricing_include_depot_pickup=='N' && $job_price->hourly_pricing_include_drop_off_depot=='N'){
                                    $var_excess_time = 0;
                                }else{

                                    $var_excess_time = $var_depot_to_pickup_time + $var_drop_off_to_depot_time;
        
                                    if ($var_excess_time < $job_price->local_move_excess_minutes_tier1) {
        
                                        $var_excess_time = $job_price->local_move_excess_minutes_tier1;
                                    } elseif ($var_excess_time < $job_price->local_move_excess_minutes_tier2) {
        
                                        $var_excess_time = $job_price->local_move_excess_minutes_tier2;
                                    }
                                }

                                echo '<br/><br/>_______________________________________';
                                echo '<br/><br/>var_excess_time: ' . $var_excess_time;
                                echo '<br/><br/>var_loading_unloading_time: ' . $var_loading_unloading_time;
                                echo '<br/><br/>var_hourly_rate: ' . $var_hourly_rate;
    
                                $var_total_time = $var_excess_time + $var_pickup_to_dropoff_time + $var_loading_unloading_time;
    
                                $var_removal_fee = (($var_total_time / 60) * $var_hourly_rate) + $var_pickup_stairs_lift_charges + $var_drop_off_stairs_lift_charges;
                            } else {
                                $var_status = 'Fail';
                                $var_fail_reason = 'Autoquote Failed reason: This is a local move and there is no depot in this region';
                                echo "<br/><br/>FAIL:::::" . $var_fail_reason;
                            }
                            //Hourly end
                        } else {
                            $region_to_region = DB::table('jobs_moving_region_to_region_pricing as t1')
                                ->select('t1.*')
                                ->where(['t1.tenant_id' => $tenant->tenant_id, 't1.from_region_id' => $var_pickup_region_id, 't1.to_region_id' => $var_drop_off_region_id])
                                ->first();
                            if ($region_to_region) {
                                $var_removal_fee = $region_to_region->price_flat + ($var_cbm * $region_to_region->price_per_cbm)
                                    + $var_pickup_excess_charges
                                    + $var_drop_off_excess_charges
                                    + $var_pickup_stairs_lift_charges
                                    + $var_drop_off_stairs_lift_charges;
    
                                if ($var_removal_fee < $region_to_region->min_price) {
                                    $var_removal_fee = $region_to_region->min_price;
                                }
                            } else {
                                $var_status = 'Fail';
                                $var_fail_reason = 'Autoquote Failed reason: No records found in the Region to Region pricing table';
                                echo "<br/><br/>FAIL:::::" . $var_fail_reason;
                            }
                        }
                        echo '<br/><br/>_______________________________________';
                        echo '<br/><br/>REMOVAL FEE: ' . $var_removal_fee;

                        //****************************** Step 7 : Update jobs_moving table ************************************
                        if ($var_price_structure == 'Hourly') {
                            JobsMoving::where(['job_id' => $job->job_id, 'tenant_id' => $tenant->tenant_id])
                                ->update([
                                    'total_cbm' => $var_cbm,
                                    'pickup_region' => $var_pickup_region,
                                    'drop_off_region' => $var_drop_off_region,
                                    'price_structure' => $var_price_structure,
                                    'hourly_rate' => $var_hourly_rate,
                                    'calculated_excess_mins' => $var_excess_time,
                                    'calculated_total_mins' => $var_total_time
    
                                ]);
                        } else {
                            JobsMoving::where(['job_id' => $job->job_id, 'tenant_id' => $tenant->tenant_id])
                                ->update([
                                    'total_cbm' => $var_cbm,
                                    'pickup_region' => $var_pickup_region,
                                    'drop_off_region' => $var_drop_off_region,
                                    'pickup_km_nearest_region' => $var_pickup_km_nearest_region,
                                    'drop_off_km_nearest_region' => $var_drop_off_km_nearest_region,
                                    'pickup_excess_charges' => $var_pickup_excess_charges,
                                    'drop_off_excess_charges' => $var_drop_off_excess_charges,
                                    'price_structure' => $var_price_structure,
                                ]);
                        }
                        if ($var_status == 'Fail') {
                            $OppStatus = CRMOpPipelineStatuses::where(['id' => $tenant->failed_op_status_id, 'tenant_id' => $tenant->tenant_id])->first();
                            if ($OppStatus) {
                                CRMOpportunities::where(['id' => $job->crm_opportunity_id, 'tenant_id' => $tenant->tenant_id])
                                    ->update([
                                        'op_status' => $OppStatus->pipeline_status,
                                    ]);
                            }
                            //Add Activity Log
                            $data['log_message'] = $var_fail_reason;
                            $data['lead_id'] = $job->customer_id;
                            $data['tenant_id'] = $tenant->tenant_id;
                            $data['log_type'] = 7; // Failed Auto Qoute Program
                            $data['log_date'] = Carbon::now();
                            $model = CRMActivityLog::create($data);
                            //--
                        } else {
                            $OppStatus = CRMOpPipelineStatuses::where(['id' => $tenant->quoted_op_status_id, 'tenant_id' => $tenant->tenant_id])->first();
                            if ($OppStatus) {
                                CRMOpportunities::where(['id' => $job->crm_opportunity_id, 'tenant_id' => $tenant->tenant_id])
                                    ->update([
                                        'op_status' => $OppStatus->pipeline_status,
                                        'value' => $var_removal_fee,
                                    ]);
                            }
                            //Add Activity Log
                            $data['log_message'] = "Auto quote completed.";
                            $data['lead_id'] = $job->customer_id;
                            $data['tenant_id'] = $tenant->tenant_id;
                            $data['log_type'] = 7; // Failed Auto Qoute Program
                            $data['log_date'] = Carbon::now();
                            $model = CRMActivityLog::create($data);
                            unset($data);
                            //--
                        }
    
                        //****************************** Step 8 : Insert/Update quotes table ************************************
    
                        if ($var_status != 'Fail') {
                            $quotes = Quotes::where(['crm_opportunity_id' => $job->crm_opportunity_id, 'tenant_id' => $tenant->tenant_id])->get();
                            $quote_tax = Tax::where(['id' => $tenant->tax_id_for_quote, 'tenant_id' => $tenant->tenant_id])->first();
    
                            if ($var_price_structure == 'Hourly') {
                                $unit_price = $var_hourly_rate;
                                $quantity = $var_total_time / 60;
                            } else {
                                $unit_price = $var_removal_fee;
                                $quantity = 1;
                            }
    
                            if ($quote_tax) {
                                $quote_amount = ($unit_price * $quantity * (1 + $quote_tax->rate_percent / 100));
                            } else {
                                $quote_amount = $var_removal_fee;
                            }
    
                            echo '<br/><br/>unit_price: ' . $unit_price;
                            echo '<br/><br/>tax_rate_percent: ' . $quote_tax->rate_percent;
                            echo '<br/><br/>quantity: ' . $quantity;
                            echo '<br/><br/>quote_amount: ' . $quote_amount;
    
                            if (count($quotes)) {
                                foreach ($quotes as $quote) {
                                    Quotes::where(['id' => $quote->crm_opportunity_id, 'tenant_id' => $tenant->tenant_id])
                                        ->update([
                                            'quote_date' => Carbon::now(),
                                            'quote_accepted' => 'N',
                                            'quote_version' => DB::raw('quote_version+1'),
                                            'updated_date' => Carbon::now(),
                                        ]);
                                    //----Delete Qoute Items
                                    QuoteItem::where(['quote_id' => $quote->id, 'tenant_id' =>$tenant->tenant_id])->delete();
    
                                    $data2['tenant_id'] = $quote->tenant_id;
                                    $data2['quote_id'] = $quote->id;
                                    $data2['product_id'] = $tenant->quote_line_item_product_id;                                    
                                    $data2['name'] = 'Removal Fee';
                                    $data2['description'] = "From " . $job->pickup_suburb . ", to " . $job->delivery_suburb;
                                    $data2['type'] = "Item";
                                    $data2['unit_price'] = $unit_price;
                                    $data2['quantity'] = $quantity;
                                    $data2['tax_id'] = $tenant->tax_id_for_quote;
                                    $data2['amount'] = $quote_amount;
                                    $data2['created_date'] = Carbon::now();
                                    $QuoteItem = QuoteItem::create($data2);
                                    unset($data2);
                                }
                            } else {
                                //If No Qoute found then add new Qoute
                                $data['tenant_id'] = $tenant->tenant_id;
                                $data['crm_opportunity_id'] = $job->crm_opportunity_id;
                                $data['quote_number'] = $job->job_number;
                                $data['sys_job_type'] = "Moving";
                                $data['job_id'] = $job->job_id;
                                $data['quote_date'] = Carbon::now();
                                $data['created_date'] = Carbon::now();
                                $Quote = Quotes::create($data);
                                unset($data);
    
                                if (isset($Quote->id)) {
                                    $data2['tenant_id'] = $tenant->tenant_id;
                                    $data2['quote_id'] = $Quote->id;
                                    $data2['product_id'] = $tenant->quote_line_item_product_id;  
                                    $data2['name'] = 'Removal Fee';
                                    $data2['description'] = "From " . $job->pickup_suburb . ", to " . $job->delivery_suburb;
                                    $data2['type'] = "Item";
                                    $data2['unit_price'] = $unit_price;
                                    $data2['quantity'] = $quantity;
                                    $data2['tax_id'] = $tenant->tax_id_for_quote;
                                    $data2['amount'] = $quote_amount;
                                    $data2['created_date'] = Carbon::now();
                                    $QuoteItem = QuoteItem::create($data2);
                                    unset($data2);
                                }
                            }
                        }
    
                        //****************************** Step 8.5 : Generating Quote PDF ************************************
    
                        $quote_file_url = $this->generateQuote($job->crm_opportunity_id,$job->tenant_id);
    
                        //****************************** Step 9 : Send Quote email to lead or Send Fail email to user ************************************
                        if ($tenant->send_auto_quote_email_to_customer == 'Y') {
    
                            $crm_contacts = CRMContacts::where(['lead_id' => $job->customer_id, 'tenant_id' => $tenant->tenant_id])->first();
                            $crm_contact_email = CRMContactDetail::where(['contact_id' => $crm_contacts->id, 'tenant_id' => $tenant->tenant_id])->where('detail_type', '=', 'Email')->first();
                            $crm_contact_phone = CRMContactDetail::where(['contact_id' => $crm_contacts->id, 'tenant_id' => $tenant->tenant_id])->where('detail_type', '=', 'Mobile')->first();
                            $customer_email = ($crm_contact_email)? $crm_contact_email->detail:'';
                            $customer_phone = ($crm_contact_phone)? $crm_contact_phone->detail:'';
                            $organisation_settings = OrganisationSettings::where('tenant_id', '=', $job->tenant_id)->first();
                            $paidAmount = 0;
                            $totalAmount = 0;

                            $external_inventory_form_param = base64_encode('tenant_id='.$job->tenant_id.'&job_id='.$job->job_id);
                            $external_inventory_form = request()->getSchemeAndHttpHost().'/removals-inventory-form/'.$external_inventory_form_param;


                        $data = [
                            'job_id' => $job->job_number,
                            'first_name' => $crm_contacts->name,
                            'last_name' => '',
                            'pickup_suburb' => $job->pickup_suburb,
                            'delivery_suburb' => $job->delivery_suburb,
                            'mobile' => $customer_phone,
                            'email' => $customer_email,
                            'job_date' => date('d-m-Y', strtotime($job->job_date)),
                            'total_amount' => $totalAmount,
                            'total_paid' => $paidAmount,
                            'total_due' => ($totalAmount - $paidAmount),
                            'external_inventory_form' => $external_inventory_form
                        ];
                        $files = [];
    
                            if ($var_status != 'Fail') {
                                //Sending Success email
                                $email_template = EmailTemplates::where(['id' => $tenant->quote_email_template_id, 'tenant_id' => $tenant->tenant_id])->first();
                                if ($email_template) {                                                                     
                                    $emailData = $this->setEmailParameter($email_template->email_subject, $email_template->email_body, $data);
                                    $email_data['from_email'] = $email_template->from_email;
                                    $email_data['from_name'] = $email_template->from_email_name;
                                    $email_data['email_subject'] = $emailData['subject'];
                                    $email_data['email_body'] = $emailData['body'];
                                    $email_data['reply_to'] = $organisation_settings->company_email;
                                    $email_data['job_id'] = $job->job_id;
                                    $email_data['cc'] = '';
                                    $email_data['bcc'] = '';
                                    $email_data['to'] = $customer_email;                                 
    
                                        if($email_template->attach_quote=='Y'){
                                                $files[]=$quote_file_url;
                                        }
    
                                    }
                                    
                                    $email_data['files'] = $files;
                                    Mail::to($email_data['to'])->send(new CustomerMail($email_data));
                                    echo '<br/><br/>Success Email Sent';
                                                            
                            } else {
                                //Sending fail email
                                $email_template = EmailTemplates::where(['id' => $tenant->fail_email_template_id, 'tenant_id' => $tenant->tenant_id])->first();
                                if ($email_template) {                                                                      
                                   // $emailData = $this->setEmailParameter($email_template->email_subject, $email_template->email_body, $data);
                                    $email_data['from_email'] = $email_template->from_email;
                                    $email_data['from_name'] = $email_template->from_email_name;
                                    $email_data['email_subject'] = $emailData['subject'];
                                    $email_data['email_body'] = $emailData['body'];
                                    $email_data['reply_to'] = $organisation_settings->company_email;
                                    $email_data['cc'] = [];
                                    $email_data['bcc'] = [];
                                    $email_data['to'] = $tenant->send_quote_fail_email_to;
                                    Mail::to($email_data['to'])->send(new sendMail($email_data));
                                    echo '<br/><br/>Failed Email Sent';
                                }else{
                                    echo '<br/><br/>No Failed Email Template found';
                                }
                            }
                            //Add Activity Log for email                                   
                            $activitydata['lead_id'] = $job->customer_id;
                            $activitydata['tenant_id'] = $tenant->tenant_id;
                            $activitydata['log_type'] = 3; 
                            $activitydata['log_from'] = $email_data['from_email'];
                            $activitydata['log_to'] = $email_data['to'];
                            $activitydata['log_subject'] = $email_data['email_subject'];
                            $activitydata['log_message'] = $email_data['email_body'];
                            $activitydata['log_date'] = Carbon::now();
                            $model = CRMActivityLog::create($activitydata);                            

                            if($model){
                                //start:: Email Attachment log
                                if(count($files)){
                                    $attach['attachment_type'] = $crm_contacts->name.' - Quote';
                                    $attach['attachment_content'] = $quote_file_url;
                                    $attach['log_id'] = $model->id;
                                    $attach['tenant_id'] = $tenant->tenant_id;
                                    // $attach['created_by'] = auth()->user()->id;
                                    // $attach['updated_by'] = auth()->user()->id;
                                    $attach['created_at'] = Carbon::now();
                                    $attach['updated_at'] = Carbon::now();
                                    $model2 = CRMActivityLogAttachment::create($attach);
                                }
                                //end :: Email Attachment log
                                echo '<br/><br/>Email Log Inserted';
                            }
                        }
                        //End auto quoting email
                        echo '</fieldset>';
                    } // end inner for loop
                }
                //break;
            } // end outer for loop
        }
    }

    private function calculateDistancebyNearestRegion($tenant_id, $suburb)
    {
        $region_suburb_name = DB::table('jobs_moving_pricing_regions as t1')
            ->select('t1.*')
            ->where(['t1.tenant_id' => $tenant_id])
            ->get();
        $distanceArr = array();
        if (count($region_suburb_name)) {
            foreach ($region_suburb_name as $region) {
                if (isset($region->id) && !empty($region->id)) {
                    $distanceArr[$region->id . '|' . $region->region_suburb_name] = $this->getDistance($suburb, $region->region_suburb_name, 'K');
                }
            }
        }
    
        if (count($distanceArr)) {
            $returnArr = array(
                'region' => min(array_keys($distanceArr, min($distanceArr))),
                'min_distance' => min($distanceArr),
                'is_true' => 1
            );
        } else {
            $returnArr = array(
                'region' => 0,
                'min_distance' => 0,
                'is_true' => 0
            );
        }
        return $returnArr;
    }
    
    private function calculateTimebyNearestRegion($from_location, $depot_locations)
    {
        $TimeArr = array();
        if (count($depot_locations)) {
            foreach ($depot_locations as $depot) {
                if (isset($depot->id) && !empty($depot->id)) {
                    $TimeArr[$depot->id . '|' . $depot->depot_suburb] = $this->getDistance($from_location, $depot->depot_suburb, 'T');
                }
            }
        }
        if (count($TimeArr)) {
            $returnArr = array(
                'region' => min(array_keys($TimeArr, min($TimeArr))),
                'min_duration' => min($TimeArr),
                'is_true' => 1
            );
        } else {
            $returnArr = array(
                'region' => 0,
                'min_duration' => 0,
                'is_true' => 0
            );
        }
        return $returnArr;
    }
    
    //************************ */ Get Distance Between to Address via Google API*********************//
    
    private function getDistance($addressFrom, $addressTo, $unit)
    {
        //Change address format
        $formattedAddrFrom = str_replace(' ', '+', $addressFrom);
        $formattedAddrTo = str_replace(' ', '+', $addressTo);
        $url = 'https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $formattedAddrFrom . '&destinations=' . $formattedAddrTo . '&key=AIzaSyB2SMtaVBlqC5v72gqS716BX8R5oXklaFc';
        $getDistance = $this->curl_get_file_contents($url);
    
        $getDistanceDecode = json_decode($getDistance);
    
        if ($unit == 'K') {
            $kmValue = $getDistanceDecode->rows[0]->elements[0]->distance->value;
            if ($kmValue) {
                $roundValue = round(($kmValue / 1000), 1);
            } else {
                $roundValue = 0;
            }
        } else {
            $timeValue = $getDistanceDecode->rows[0]->elements[0]->duration->value;
            if ($timeValue) {
                $roundValue = round(($timeValue / 60), 1); //convert seconds to minutes
            } else {
                $roundValue = 0;
            }
        }
    
        return $roundValue;
    }
    
    private function curl_get_file_contents($URL)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        $contents = curl_exec($c);
        curl_close($c);
    
        if ($contents) return $contents;
        else return FALSE;
    }
    //END:: Removal Auto Qoute Program
    
    // START:: Set Email Template parameters
    private function setEmailParameter($email_subject, $email_body, $data)
    {
        $subject = $email_subject;
        if (preg_match_all("/{(.*?)}/", $subject, $m)) {
            foreach ($m[1] as $i => $varname) {
                $subject = str_replace($m[0][$i], sprintf('%s', $data[$varname]), $subject);
            }
        }
    
        $template = $email_body;
    
        if (preg_match_all("/{(.*?)}/", $template, $m)) {
            foreach ($m[1] as $i => $varname) {
                $template = str_replace($m[0][$i], sprintf('%s', $data[$varname]), $template);
            }
        }
        $response = [
            'subject' => $subject,
            'body' => $template
        ];
        return $response;
    }
    //END:: EMail Template Parameters
    
    public function generateQuoteCleaning($opportunity_id,$tenant_id)
    {
        
        //try{
        $this->opportunity = CRMOpportunities::where('id', '=', $opportunity_id)->where('tenant_id', '=', $tenant_id)->first();
            $this->taxs = Tax::where(['tenant_id' => $tenant_id])->first();
            $this->sub_total = 0;
            $this->quote_total = 0;
            $this->tax_total = 0;
            $this->deposit_required = 0;
            $this->booking_fee = 0;
            $this->count = 0;
            $this->show_estimate_range=0;
            $this->estimate_lower_percent=0;

            // Job Deposit Required for the tenant-------------------//

            $auto_quote = DB::table('jobs_cleaning_auto_quoting as t1')
                ->where(['t1.auto_quote_enabled' => 'Y','tenant_id'=>$tenant_id, 't1.tenant_id' => $tenant_id])
                ->first();    

            $this->job = JobsCleaning::where('crm_opportunity_id', '=', $this->opportunity->id)->where('tenant_id', '=', $tenant_id)->first();
            $this->quote = Quotes::where('crm_opportunity_id', '=', $this->opportunity->id)->where('tenant_id', '=', $tenant_id)->first();
            if ($this->quote) {
                $this->quoteItems = QuoteItem::where('quote_id', '=', $this->quote->id)->get();

                $sub_total = QuoteItem::select(DB::raw('sum(quote_items.unit_price * quote_items.quantity) as total'))
                    ->join('quotes', 'quotes.id', '=', 'quote_items.quote_id')
                    ->where('quotes.crm_opportunity_id', '=', $opportunity_id)
                    ->where(['quote_items.tenant_id' => $tenant_id])
                    ->first();
                $this->sub_total = $sub_total->total;

                $tax_total = QuoteItem::select(DB::raw('sum(quote_items.amount) - sum(quote_items.unit_price * quote_items.quantity) as total'))
                    ->join('quotes', 'quotes.id', '=', 'quote_items.quote_id')
                    ->where('quotes.crm_opportunity_id', '=', $opportunity_id)
                    ->where(['quote_items.tenant_id' => $tenant_id])
                    ->first();
                $this->tax_total = $tax_total->total;

                $quote_total = QuoteItem::select(DB::raw('sum(quote_items.amount) as total'))
                    ->join('quotes', 'quotes.id', '=', 'quote_items.quote_id')
                    ->where('quotes.crm_opportunity_id', '=', $opportunity_id)
                    ->where(['quote_items.tenant_id' => $tenant_id])
                    ->first();
                $this->quote_total = $quote_total->total;

            }
            if ($auto_quote->deposit_required == 'Y') {
                $this->deposit_required = $auto_quote->deposit_amount;
            } else {
                $this->booking_fee = $this->quote_total;
            }
            $this->grand_total = floatval($this->quote_total) + floatval($this->tax_total);
            if ($this->job) {
                $this->companies = Companies::where('id', '=', $this->job->company_id)->where('tenant_id', '=', $tenant_id)->first();
                // dd($this->companies);
            }
            $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', $tenant_id)->first();
            $this->crm_leads = CRMLeads::where(['id' => $this->opportunity->lead_id, 'tenant_id' => $tenant_id])->first();
            $this->crm_contacts = CRMContacts::where(['lead_id' => $this->opportunity->lead_id, 'tenant_id' => $tenant_id])->first();
            $this->crm_contact_email = CRMContactDetail::where(['contact_id' => $this->crm_contacts->id, 'tenant_id' => $tenant_id])->where('detail_type', '=', 'Email')->first();
            $this->crm_contact_phone = CRMContactDetail::where(['contact_id' => $this->crm_contacts->id, 'tenant_id' => $tenant_id])->where('detail_type', '=', 'Mobile')->first();     
            $this->company_logo_exists = false;
            
            //Book now url
            if ($auto_quote->deposit_required == 'Y') {
                $this->url_params = base64_encode('quote_id=' . $this->quote->id . '&deposit_required=' . $this->deposit_required);
                $this->url_link = request()->getSchemeAndHttpHost() . '/pay-now/' . $this->url_params;
                $is_booking_fee=0;
            }else{
                $this->url_params = base64_encode('quote_id=' . $this->quote->id . '&booking_fee=' . $this->booking_fee);
                $this->url_link = request()->getSchemeAndHttpHost() . '/pay-now-booking-fee/' . $this->url_params;
                $is_booking_fee=-1;                
            }
            $filename = time();
            
            if (isset($this->companies)) {

                $file_number = 1;
                if (!empty($this->quote->quote_file_name)) {
                    $filename = str_replace('.pdf', '', $this->quote->quote_file_name);
                    $fn_ary = explode('_', $filename);
                    $file_number = intval($fn_ary[3]) + 1;
                }

                if (File::exists(public_path() . '/user-uploads/company-logo/' . $this->companies->logo)) {
                    $this->company_logo_exists = true;
                }

                $filename = 'Quote_' . $this->companies->company_name . '_'  . $this->quote->quote_number . '_' . rand() . '.pdf';

                $pdf = app('dompdf.wrapper');
                $pdf->loadView('admin.crm-leads.quote', 
                [
                    'organisation_settings'=>$this->organisation_settings,
                    'companies'=>$this->companies,
                    'company_logo_exists'=>$this->company_logo_exists,
                    'count'=>0,
                    'crm_contact_phone'=>$this->crm_contact_phone,
                    'crm_contact_email'=>$this->crm_contact_email,
                    'crm_contacts'=>$this->crm_contacts,
                    'crm_leads'=>$this->crm_leads,
                    'job'=>$this->job,
                    'quote'=>$this->quote,
                    'quoteItems'=>$this->quoteItems,
                    'taxs'=>$this->taxs,
                    'quote_total'=>$this->quote_total,
                    'deposit_required'=>$this->deposit_required,
                    'grand_total'=>$this->grand_total,
                    'tax_total'=>$this->tax_total,
                    'sub_total'=>$this->sub_total,
                    'url_link'=>$this->url_link,
                    'is_booking_fee'=>$is_booking_fee,
                    'booking_fee'=>$this->booking_fee,
                    'show_estimate_range' => $this->show_estimate_range,
                    'estimate_lower_percent' => $this->estimate_lower_percent
                ]);
                // return $pdf->stream(); // to view pdf
                // return $pdf->download('tmp.pdf');
                $pdf->save(public_path().'/quote-files/' . $filename);
                
                if (File::exists(public_path() . '/quote-files/' . $this->quote->quote_file_name)) {
                    File::delete(public_path() . '/quote-files/' . $this->quote->quote_file_name);
                }
                $this->quote->quote_file_name = $filename;
                $this->quote->save();
                return public_path('quote-files') . '/' . $this->quote->quote_file_name;
        }    
    }

    public function generateQuote($opportunity_id,$tenant_id)
    {
        //try{
        $this->opportunity = CRMOpportunities::where('id', '=', $opportunity_id)->where('tenant_id', '=', $tenant_id)->first();
            $this->taxs = Tax::where(['tenant_id' => $tenant_id])->first();
            $this->sub_total = 0;
            $this->quote_total = 0;
            $this->tax_total = 0;
            $this->deposit_required = 0;
            $this->booking_fee = 0;
            $this->count = 0;
            $this->show_estimate_range=0;
            $this->estimate_lower_percent=0;
                // Job Moving Price for the tenant-------------------//
            $job_price_additional = DB::table('jobs_moving_pricing_additional as t1')
            ->select('t1.*')
            ->where(['t1.tenant_id' => $tenant_id])
            ->first();

                if($this->opportunity->op_type=="Moving"){
                    $this->job = JobsMoving::where('crm_opportunity_id', '=', $this->opportunity->id)->where('tenant_id', '=', $tenant_id)->first();
                }elseif($this->opportunity->op_type=="Cleaning"){
                    $this->job = JobsCleaning::where('crm_opportunity_id', '=', $this->opportunity->id)->where('tenant_id', '=', $tenant_id)->first();
                }
            $this->quote = Quotes::where('crm_opportunity_id', '=', $this->opportunity->id)->where('tenant_id', '=', $tenant_id)->first();
            if ($this->quote) {
                $this->quoteItems = QuoteItem::where('quote_id', '=', $this->quote->id)->get();
    
                $sub_total = QuoteItem::select(DB::raw('sum(quote_items.unit_price * quote_items.quantity) as total'))
                    ->join('quotes', 'quotes.id', '=', 'quote_items.quote_id')
                    ->where('quotes.crm_opportunity_id', '=', $opportunity_id)
                    ->where(['quote_items.tenant_id' => $tenant_id])
                    ->first();
                $this->sub_total = $sub_total->total;
    
                $tax_total = QuoteItem::select(DB::raw('sum(quote_items.amount) - sum(quote_items.unit_price * quote_items.quantity) as total'))
                    ->join('quotes', 'quotes.id', '=', 'quote_items.quote_id')
                    ->where('quotes.crm_opportunity_id', '=', $opportunity_id)
                    ->where(['quote_items.tenant_id' => $tenant_id])
                    ->first();
                $this->tax_total = $tax_total->total;
    
                $quote_total = QuoteItem::select(DB::raw('sum(quote_items.amount) as total'))
                    ->join('quotes', 'quotes.id', '=', 'quote_items.quote_id')
                    ->where('quotes.crm_opportunity_id', '=', $opportunity_id)
                    ->where(['quote_items.tenant_id' => $tenant_id])
                    ->first();
                $this->quote_total = $quote_total->total;
    
            }
        if($this->opportunity->op_type=="Moving"){            
            if ($this->job->price_structure == 'Fixed') {
                if ($job_price_additional->is_deposit_for_fixed_pricing_fixed_amt == 'Y') {
                    $this->deposit_required = $job_price_additional->deposit_amount_fixed_pricing;
                } else {
                    $this->deposit_required = $job_price_additional->deposit_percent_fixed_pricing * $this->quote_total;
                }
            } else {
                if($job_price_additional->hourly_pricing_has_booking_fee=='Y'){
                    $this->booking_fee = $job_price_additional->hourly_pricing_booking_fee;
                }else{
                    if ($job_price_additional->is_deposit_for_hourly_pricing_fixed_amt == 'Y') {
                        $this->deposit_required = $job_price_additional->deposit_amount_hourly_pricing;
                    } else {
                        $this->deposit_required = $job_price_additional->deposit_percent_hourly_pricing * $this->quote_total;
                    }
                }
                //Show Estimate Range 
                if($job_price_additional->hourly_pricing_min_pricing_percent>0){
                    $this->show_estimate_range=1;
                    $this->estimate_lower_percent=$job_price_additional->hourly_pricing_min_pricing_percent;
                }
                //--
            }
        }elseif($this->opportunity->op_type=="Cleaning"){
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

            $this->grand_total = floatval($this->quote_total) + floatval($this->tax_total);
            if ($this->job) {
                $this->companies = Companies::where('id', '=', $this->job->company_id)->where('tenant_id', '=', $tenant_id)->first();
                // dd($this->companies);
            }
            $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', $tenant_id)->first();
            $this->crm_leads = CRMLeads::where(['id' => $this->opportunity->lead_id, 'tenant_id' => $tenant_id])->first();
            $this->crm_contacts = CRMContacts::where(['lead_id' => $this->opportunity->lead_id, 'tenant_id' => $tenant_id])->first();
            $this->crm_contact_email = CRMContactDetail::where(['contact_id' => $this->crm_contacts->id, 'tenant_id' => $tenant_id])->where('detail_type', '=', 'Email')->first();
            $this->crm_contact_phone = CRMContactDetail::where(['contact_id' => $this->crm_contacts->id, 'tenant_id' => $tenant_id])->where('detail_type', '=', 'Mobile')->first();     
            $this->company_logo_exists = false;
            
            //Book now url
            if($this->job->price_structure=='Hourly' && $job_price_additional->hourly_pricing_has_booking_fee=='Y'){
                $this->url_params = base64_encode('quote_id=' . $this->quote->id . '&booking_fee=' . $this->booking_fee);
                $this->url_link = request()->getSchemeAndHttpHost() . '/pay-now-booking-fee/' . $this->url_params;
                $is_booking_fee=1;
            }else{
                $this->url_params = base64_encode('quote_id=' . $this->quote->id . '&deposit_required=' . $this->deposit_required);
                $this->url_link = request()->getSchemeAndHttpHost() . '/pay-now/' . $this->url_params;
                $is_booking_fee=0;
            }
            $filename = time();
            if (isset($this->companies)) {
    
                $file_number = 1;
                if (!empty($this->quote->quote_file_name)) {
                    $filename = str_replace('.pdf', '', $this->quote->quote_file_name);
                    $fn_ary = explode('_', $filename);
                    $file_number = intval($fn_ary[3]) + 1;
                }
    
                if (File::exists(public_path() . '/user-uploads/company-logo/' . $this->companies->logo)) {
                    $this->company_logo_exists = true;
                }
    
                $filename = 'Quote_' . $this->companies->company_name . '_'  . $this->quote->quote_number . '_' . rand() . '.pdf';
    
                $pdf = app('dompdf.wrapper');
                $pdf->loadView('admin.crm-leads.quote', 
                [
                    'organisation_settings'=>$this->organisation_settings,
                    'companies'=>$this->companies,
                    'company_logo_exists'=>$this->company_logo_exists,
                    'count'=>0,
                    'crm_contact_phone'=>$this->crm_contact_phone,
                    'crm_contact_email'=>$this->crm_contact_email,
                    'crm_contacts'=>$this->crm_contacts,
                    'crm_leads'=>$this->crm_leads,
                    'job'=>$this->job,
                    'quote'=>$this->quote,
                    'quoteItems'=>$this->quoteItems,
                    'taxs'=>$this->taxs,
                    'quote_total'=>$this->quote_total,
                    'deposit_required'=>$this->deposit_required,
                    'grand_total'=>$this->grand_total,
                    'tax_total'=>$this->tax_total,
                    'sub_total'=>$this->sub_total,
                    'url_link'=>$this->url_link,
                    'is_booking_fee'=>$is_booking_fee,
                    'booking_fee'=>$this->booking_fee,
                    'show_estimate_range' => $this->show_estimate_range,
                    'estimate_lower_percent' => $this->estimate_lower_percent
                ]);
                // return $pdf->stream(); // to view pdf
                // return $pdf->download('tmp.pdf');
                $pdf->save(public_path().'/quote-files/' . $filename);
                
                if (File::exists(public_path() . '/quote-files/' . $this->quote->quote_file_name)) {
                    File::delete(public_path() . '/quote-files/' . $this->quote->quote_file_name);
                }
    
                $this->quote->quote_file_name = $filename;
                $this->quote->save();
                return public_path('quote-files') . '/' . $this->quote->quote_file_name;
        }    
    }
}
