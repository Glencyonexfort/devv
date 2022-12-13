<?php

namespace App\Console\Commands;

use App\Companies;
use App\CRMActivityLog;
use App\CRMActivityLogAttachment;
use App\CRMLeads;
use App\CRMOpportunities;
use App\CRMOpStatusLog;
use App\DevLogs;
use App\EmailSequenceSettings;
use App\EmailTemplateAttachments;
use App\EmailTemplates;
use App\Invoice;
use App\JobsCleaning;
use App\JobsCleaningStatusLog;
use App\JobsMoving;
use App\JobsMovingInventory;
use App\JobsMovingLegs;
use App\JobsMovingStatusLog;
use App\Mail\sendMail;
use App\OrganisationSettings;
use App\QuoteItem;
use App\Quotes;
use App\SMSTemplates;
use App\SysApiSettings;
use App\TenantApiDetail;
use App\TenantDetail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Config;

class AutoEmailEveryFourHour extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto-email-every-four-hours';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to auto email every 4 hours.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('max_execution_time', 0);
        Log::debug(date('Y-m-d H:i:s'));
        /*$data=[
            'to' => "muhammad_fayyaz@live.com",
            'from_name' => "Onexfortx",
            'from_email' => "no-reply@onexfort.com",
            'reply_to' => "no-reply@onexfort.com",            
        ];
        $data['email_subject'] = "Foolow Up 1";
        $data['email_body'] = "Follow Up email body";
        Mail::to("muhammad_fayyaz@live.com")->send(new sendMail($data));
        exit;*/
        $sequences = EmailSequenceSettings::where('check_frequency', '=', '240')
            ->where('active', '=', 'Y')->get();           
        if ($sequences) {
            foreach ($sequences as $sequence) {            
                $tenant_details = TenantDetail::where('tenant_id', '=', $sequence->tenant_id)->first();
                $company_sms_number = Companies::where('id', '=', $sequence->company_id)->pluck('sms_number')->first();
                $organisation_settings = OrganisationSettings::where('tenant_id', '=', $sequence->tenant_id)->first();
                if ($sequence->is_opportunity == 'Y') {
                    $opportunities = CRMOpportunities::select("crm_opportunities.*")
                        ->join('jobs_moving', 'jobs_moving.crm_opportunity_id', 'crm_opportunities.id')
                        ->where('crm_opportunities.op_status', '=', $sequence->initial_status)
                        ->where('jobs_moving.company_id', '=', $sequence->company_id)
                        ->where('crm_opportunities.tenant_id', '=', $sequence->tenant_id)
                        ->where('crm_opportunities.deleted', '=', '0')
                        ->get();
                    if ($opportunities) {
                        foreach ($opportunities as $oppertunity) {
                            $vals = [];                            
                            $op_log = CRMOpStatusLog::where('tenant_id', '=', $sequence->tenant_id)
                                ->where('crm_opportunity_id', '=', $oppertunity->id)
                                ->orderBy('created_at', 'DESC')->first();     
                            if ($op_log) {                                
                                if ($op_log->new_status == $sequence->initial_status) {    
                                    // $start = Carbon::parse($op_log->created_at)->format('Y/m/d');
                                    $start = $op_log->created_at;
                                    $start = Carbon::createFromFormat('Y-m-d H:i:s', $start, 'UTC')->setTimezone('Australia/Melbourne');
                                    $end = Carbon::now();
                                    $days=0;
                                    $hours = $end->diffInHours($start);  
                                    if((int)$hours>=24){
                                        $days = floor($hours/24);
                                    }
                                    if($sequence->days_after_initial_status<0){
                                        $days = (-1*$days);
                                    }
                                    if ((int)($days) == (int)($sequence->days_after_initial_status)) { 
                                        //Update Opportunity Status
                                        CRMOpportunities::where('id', $op_log->crm_opportunity_id)->update([
                                            'op_status' => $sequence->post_status,
                                        ]);
                                        ///--->
                                        // //START:: Dev Logs
                                        // $dev['action'] = 'Auto Email Every Hour First Check';
                                        // $dev['log'] = "Opp ID: ".$oppertunity->id;
                                        // $dev['created_at'] = Carbon::now();
                                        // DevLogs::create($dev);
                                        // //END:: Dev Logs
                                        //try {
                                        $contact_details = CRMOpStatusLog::select('crm_contact_details.detail', 'crm_contacts.name')
                                            ->leftJoin('crm_opportunities', 'crm_op_status_log.crm_opportunity_id', '=', 'crm_opportunities.id')
                                            ->leftJoin('crm_contacts', 'crm_opportunities.lead_id', '=', 'crm_contacts.lead_id')
                                            ->leftJoin('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                                            ->where('crm_op_status_log.id', '=', $op_log->id)
                                            ->where('crm_contact_details.detail_type', '=', 'Email')->first();

                                        $contact_details_mobile = CRMOpStatusLog::select('crm_contact_details.detail')
                                            ->leftJoin('crm_opportunities', 'crm_op_status_log.crm_opportunity_id', '=', 'crm_opportunities.id')
                                            ->leftJoin('crm_contacts', 'crm_opportunities.lead_id', '=', 'crm_contacts.lead_id')
                                            ->leftJoin('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                                            ->where('crm_op_status_log.id', '=', $op_log->id)
                                            ->where('crm_contact_details.detail_type', '=', 'Mobile')->first();  
                                            
                                            $this->paidAmount = 0;
                                            $this->totalAmount = 0;
                                            $this->lead_id = $oppertunity->lead_id;
                                            $this->job_id = 0;
                                            $this->moving_job_id = 0;
                                            $this->job_date = '';
                                            $this->pickup_suburb = '';
                                            $this->delivery_suburb = '';
                                            $this->pickup_address = '';
                                            $this->drop_off_address = '';    
                                            $this->job_date = ''; 
                                            $this->job_start_time='';
                                            $this->job=null;
                                            $est_first_leg_start_time=''; 

                                            $full_name = '';
                                                $mobile = '';
                                                
                                                $email_to = $contact_details->detail;

                                                if (isset($contact_details_mobile->detail)) {
                                                    $mobile = $contact_details_mobile->detail;
                                                }

                                                if (isset($contact_details->name)) {
                                                    $full_name = $contact_details->name;
                                                }

                                                $full_name_ary = @explode(' ', $full_name, 2);
                                                if (count($full_name_ary) > 1) {
                                                    $first_name = $full_name_ary[0];
                                                    $last_name = $full_name_ary[1];
                                                } else {
                                                    $first_name = $full_name_ary[0];
                                                    $last_name = '';
                                                }

                                                //if($sequence->is_opportunity=='N'){                                                        
                                                        //if ($sequence->sys_job_type == 'Moving') {
                                                            $this->job = JobsMoving::where('crm_opportunity_id', '=', $op_log->crm_opportunity_id)->where('tenant_id', '=', $sequence->tenant_id)->first();
                                                            $this->job_leg_start_time = JobsMovingLegs::where('job_id', '=', $this->job->job_id)->pluck("est_start_time")->first();
                                                            if($this->job_leg_start_time){
                                                                $est_first_leg_start_time=$this->job_leg_start_time;
                                                            }
                                                        // } elseif ($sequence->sys_job_type == 'Cleaning') {
                                                        //     $this->job = JobsCleaning::where('crm_opportunity_id', '=', $op_log->crm_opportunity_id)->where('tenant_id', '=', $sequence->tenant_id)->first();
                                                        // }
                                                    // }else{
                                                    //     $this->job=null;
                                                    // }

                                                    if ($this->job) {
                                                        //if sequence company_id is not same as job then skip
                                                        if($sequence->company_id!=$this->job->company_id){
                                                            continue;
                                                        }
                                                        //end
                                                        $this->jobs_moving_id = $this->job->job_id;
                                                        $this->job_id = $this->job->job_number;
                                                        $this->moving_job_id = $this->job->job_id;
                                                        $this->pickup_suburb = $this->job->pickup_suburb;
                                                        $this->job_date = $this->job->job_date;
                                                        $this->delivery_suburb = $this->job->delivery_suburb;
                                                        $this->pickup_address = $this->job->pickup_address . " " . $this->job->pickup_suburb . " " . $this->job->pickup_post_code;
                                                        $this->drop_off_address = $this->job->drop_off_address . " " . $this->job->delivery_suburb . " " . $this->job->drop_off_post_code;
                                                        $this->job_date = date('d-m-Y', strtotime($this->job->job_date));
                                                        $this->job_start_time = $this->job->job_start_time;

                                                        $this->invoice = Invoice::where('job_id', '=', $this->job->job_id)->where('sys_job_type', '=', $sequence->sys_job_type)->where('tenant_id', '=', $sequence->tenant_id)->first();
                                                        if (isset($this->invoice->id)):
                                                            $this->paidAmount = $this->invoice->getPaidAmount();
                                                            $this->totalAmount = $this->invoice->getTotalAmount();
                                                        endif;
                                                    }

                                                    //inventory_list parameter
                                                    $mov_inv = new JobsMovingInventory();
                                                    $inv_list = $mov_inv->getInventoryListForEmail($sequence->tenant_id, $this->job_id);
                                                    //-->

                                                    $external_inventory_form_param = base64_encode('tenant_id=' . $sequence->tenant_id . '&job_id=' . $this->jobs_moving_id);
                                                    $external_inventory_form = request()->getSchemeAndHttpHost() . '/removals-inventory-form/' . $external_inventory_form_param; 
                                                    $external_inventory_form_button = '<a href="'.$external_inventory_form.'" style="color: #fff;background-color: #26a69a;border-radius: 3px;cursor: pointer;padding: 8px 14px;line-height: 30px;" >Inventory Form</a>';

                                                    $stripe = TenantApiDetail::check_stripe_by_tenant($sequence->tenant_id);
                                                    $this->quote = Quotes::where('crm_opportunity_id', '=', $oppertunity->id)->where('tenant_id', '=', $sequence->tenant_id)->first();
                                                    if($stripe==1  && $this->quote && $this->job){
                                                        $this->url_link="";
                                                        $this->deposit_required="";
                                                        $this->booking_fee="";
                                                        $this->quote_total = 0;
                                                            if ($this->quote) {
                                                                $this->quoteItems = QuoteItem::where('quote_id', '=', $this->quote->id)->get();

                                                                $sub_total = QuoteItem::select(DB::raw('sum(quote_items.amount) as total'))
                                                                ->where('quote_items.quote_id', '=', $this->quote->id)->first();
                                                                $this->quote_total = $sub_total->total;
                                                            }
                                                        if($oppertunity->op_type=="Moving"){
                                                            $job_price_additional = DB::table('jobs_moving_pricing_additional as t1')
                                                            ->select('t1.*')
                                                            ->where(['t1.tenant_id' => $sequence->tenant_id])
                                                            ->first();


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
                                                        }elseif($oppertunity->op_type=="Cleaning"){
                                                            $jobs_cleaning_auto_quoting = DB::table('jobs_cleaning_auto_quoting as t1')
                                                            ->select('t1.*')
                                                            ->where(['t1.tenant_id' => $sequence->tenant_id])
                                                            ->first();
                                                            $this->deposit_required = $jobs_cleaning_auto_quoting->deposit_amount;
                                                        }
                                                        //Book now url
                                                        if($oppertunity->op_type=="Moving"){
                                                            if($this->job->price_structure=='Hourly' && $job_price_additional->hourly_pricing_has_booking_fee=='Y'){
                                                                $this->url_params = base64_encode('quote_id=' . $this->quote->id . '&booking_fee=' . $this->booking_fee);
                                                                $this->url_link = request()->getSchemeAndHttpHost() . '/pay-now-booking-fee/' . $this->url_params;
                                                            }else{
                                                                $this->url_params = base64_encode('quote_id=' . $this->quote->id . '&deposit_required=' . $this->deposit_required);
                                                                $this->url_link = request()->getSchemeAndHttpHost() . '/pay-now/' . $this->url_params;
                                                            }
                                                        }elseif($oppertunity->op_type=="Cleaning"){
                                                            $this->url_params = base64_encode('quote_id=' . $this->quote->id . '&deposit_required=' . $this->deposit_required);
                                                            $this->url_link = request()->getSchemeAndHttpHost() . '/pay-now/' . $this->url_params;
                                                        }                
                                                        $book_now_button = '<a href="'.$this->url_link.'" style="color: #fff;background-color: #1d3ad2;border-radius: 3px;cursor: pointer;padding: 8px 14px;line-height: 30px;" >BOOK NOW</a>';
                                                        //$book_now_button = $this->url_link;
                                                        //$book_now_button = 'quote_id=' . $this->quote->id . '&booking_fee=' . $this->booking_fee.'&job_id=' . $this->job->job_id .'&job_type=' . $opportunity->op_type;
                                                    }else{
                                                        $book_now_button = "";
                                                    }

                                        if (!empty($contact_details->detail)) {
                                            if ($sequence->send_email == 'Y') {                                            
                                                $template = EmailTemplates::where('id', '=', $sequence->email_template_id)->first();                                                                                                                                           
                                                if ($template) {
                                                    $email_subject = $template->email_subject;
                                                    $email_body = $template->email_body;                                                                                              
                                                    $data = [
                                                        'to' => $email_to,
                                                        'from_name' => $sequence->from_email_name,
                                                        'from_email' => $sequence->from_email,
                                                        'reply_to' => $sequence->from_email,
                                                        'jobs_moving_id' => $this->jobs_moving_id,
                                                        'job_id' => $this->job_id,
                                                        'lead_id' => $this->lead_id,
                                                        'first_name' => $first_name,
                                                        'last_name' => $last_name,
                                                        'pickup_suburb' => $this->pickup_suburb,
                                                        'delivery_suburb' => $this->delivery_suburb,
                                                        'pickup_address' => $this->pickup_address,
                                                        'delivery_address' => $this->drop_off_address,
                                                        'phone' => $mobile,
                                                        'mobile' => $mobile,
                                                        'job_date' => $this->job_date,
                                                        'email' => $email_to,
                                                        'total_amount' => number_format((float)($this->totalAmount), 2, '.', ','),
                                                        'total_paid' => number_format((float)($this->paidAmount), 2, '.', ','),
                                                        'total_due' => number_format((float)($this->totalAmount - $this->paidAmount), 2, '.', ','),
                                                        'external_inventory_form' => $external_inventory_form,
                                                        'external_inventory_form_button' => $external_inventory_form_button,
                                                        'inventory_list' => $inv_list,
                                                        'book_now_button' => $book_now_button,
                                                        'user_first_name' => '',
                                                        'user_last_name' => '',
                                                        'est_start_time' => $this->job_start_time,
                                                        'est_first_leg_start_time' => $est_first_leg_start_time,
                                                        'user_email_signature' => ''
                                                    ];
                                                    if (preg_match_all("/{(.*?)}/", $email_subject, $m)) {
                                                        foreach ($m[1] as $i => $varname) {
                                                            $email_subject = str_replace($m[0][$i], sprintf('%s', $data[$varname]), $email_subject);
                                                        }
                                                    }

                                                    if (preg_match_all("/{(.*?)}/", $email_body, $m)) {
                                                        foreach ($m[1] as $i => $varname) {
                                                            $email_body = str_replace($m[0][$i], sprintf('%s', $data[$varname]), $email_body);
                                                        }
                                                    }
                                                    $files = [];
                                                    //attach quote file
                                                    if ($template->attach_quote == "Y") {
                                                        $quote = Quotes::where('crm_opportunity_id', '=', $op_log->crm_opportunity_id)->where('sys_job_type', '=', 'Moving')->first();
                                                        if ($quote) {
                                                            if (File::exists(public_path() . '/quote-files/' . $quote->quote_file_name)) {
                                                                $quote_path['path'] = public_path('quote-files') . '/' . $quote->quote_file_name;
                                                                $quote_path['name'] = $quote->quote_file_name;
                                                                $files[] = $quote_path;
                                                            }
                                                        }
                                                    }
                                                     //end:: attach quote

                                                    //attach insurance quote file
                                                    if($template->attach_insurance=='Y'){
                                                        $coverFreight_connected = TenantApiDetail::where(['tenant_id' => $sequence->tenant_id, 'provider' => 'CoverFreight'])->first();
                                                        if($coverFreight_connected){
                                                            if($this->job){
                                                                if($this->job->insurance_file_name != NULL){
                                                                    $insurance_file_url = '/insurance-quote/' . $this->job->insurance_file_name;
                                                                    $insurance_file_name = $this->job->insurance_file_name;
                                                                    if (File::exists(public_path() . $insurance_file_url)) {
                                                                        $insurance_path['path'] = public_path() . $insurance_file_url;
                                                                        $insurance_path['name'] = $insurance_file_name;
                                                                        $files[] = $insurance_path;
                                                                    }
                                                                }else{
                                                                    // call coverfreight api and get insurance quote
                                                                    try{
                                                                        $crmlead_model = new CRMLeads();
                                                                        $r = $crmlead_model->generateInsuranceQuote($op_log->crm_opportunity_id, $sequence->tenant_id);
                                                                        if($r['status']==1){
                                                                            $this->job = JobsMoving::find($this->job->job_id);
                                                                            $insurance_file_url = '/insurance-quote/' . $this->job->insurance_file_name;
                                                                            $insurance_file_name = $this->job->insurance_file_name;
                                                                            if (File::exists(public_path() . $insurance_file_url)) {
                                                                                $insurance_path['path'] = public_path() . $insurance_file_url;
                                                                                $insurance_path['name'] = $insurance_file_name;
                                                                                $files[] = $insurance_path;
                                                                            }
                                                                        }
                                                                    }catch(\Exception $ex){
                                                                    // //START:: Dev Logs
                                                                    $dev['action'] = 'Insurance Quote Error 1';
                                                                    $dev['log'] = "Opp ID: ".$op_log->crm_opportunity_id;
                                                                    $dev['created_at'] = Carbon::now();
                                                                    DevLogs::create($dev);
                                                                    // //END:: Dev Logs
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    //end:: attach insurance quote file

                                                    $attachments = EmailTemplateAttachments::where(['email_template_id'=>$template->id])->get();
                                                    if($attachments){
                                                        foreach($attachments as $attach){
                                                            if (File::exists(public_path().$attach->attachment_file_location)) {
                                                                $attach_path['path'] = public_path().$attach->attachment_file_location;
                                                                $attach_path['name'] = $attach->attachment_file_name;
                                                                $files[] = $attach_path;
                                                            }
                                                        }
                                                     }
                                                   
                                                    $data['files'] = $files;
                                                    $data['auto'] = 1; // if email send from cron job
                                                    $data['email_subject'] = $email_subject;
                                                    $data['email_body'] = $email_body;

                                                    //print_r($data['files']);exit;
                                                    Mail::to($email_to)->send(new sendMail($data));
                                                    //print_r(Mail::failures());exit;
                                                    $vals['log_subject'] = $data['email_subject'];
                                                    $vals['log_message'] = $data['email_body'];
                                                    $vals['log_from'] = $data['from_email'];
                                                    $vals['log_to'] = $data['to'];
                                                    $vals['job_id'] = $this->moving_job_id;
                                                    $vals['lead_id'] = $this->lead_id;
                                                    $vals['tenant_id'] = $sequence->tenant_id;
                                                    $vals['log_type'] = 3; // Activity Email
                                                    $vals['log_date'] = Carbon::now();
                                                    $model = CRMActivityLog::create($vals);
                                                    unset($vals);
                                                    // Log email attachments files
                                                     if($files){
                                                        foreach($files as $attach){
                                                            $attach_log['attachment_type'] = $attach['name'];
                                                            $attach_log['attachment_content'] = $attach['path'];
                                                            $attach_log['log_id'] = $model->id;
                                                            $attach_log['tenant_id'] = $sequence->tenant_id;
                                                            $attach_log['created_at'] = Carbon::now();
                                                            $attach_log['updated_at'] = Carbon::now();
                                                            CRMActivityLogAttachment::create($attach_log);
                                                        }
                                                    }
                                                }                                                
                                            }
                                        } 
                                        if ($sequence->send_sms == 'Y' && $tenant_details->sms_credit > 0) {
                                                $template = SMSTemplates::where('id', '=', $sequence->sms_template_id)->first();
                                                if ($template) {
                                                    if (!empty($mobile)) {
                                                        // Set Dynamic Parameters
                                                        $smsdata = [
                                                            'job_id' => $this->job->job_number,
                                                            'first_name' => $first_name,
                                                            'last_name' => $last_name,
                                                            'pickup_suburb' => $this->job->pickup_suburb,
                                                            'delivery_suburb' => $this->job->delivery_suburb,
                                                            'pickup_address' => $this->job->pickup_address." ".$this->job->pickup_suburb." ".$this->job->pickup_post_code,
                                                            'delivery_address' => $this->job->drop_off_address." ".$this->job->delivery_suburb." ".$this->job->drop_off_post_code,
                                                            'mobile' => $mobile,
                                                            'email' => $email_to,
                                                            'job_date' => date('d-m-Y', strtotime($this->job->job_date)),    
                                                            'user_first_name' => '',
                                                            'user_last_name' => '',
                                                            'est_start_time' => $this->job_start_time,
                                                            'est_first_leg_start_time' => $est_first_leg_start_time,  
                                                            'total_amount' => number_format((float)($this->totalAmount), 2, '.', ','),
                                                            'total_paid' => number_format((float)($this->paidAmount), 2, '.', ','),
                                                            'total_due' => number_format((float)($this->totalAmount - $this->paidAmount), 2, '.', ','),          
                                                            'external_inventory_form' => $external_inventory_form
                                                        ];
                                            
                                                        $sms_message = $template->sms_message;                                            
                                            
                                                        if (preg_match_all("/{(.*?)}/", $sms_message, $m)) {
                                                            foreach ($m[1] as $i => $varname) {
                                                                $sms_message = str_replace($m[0][$i], sprintf('%s', $smsdata[$varname]), $sms_message);
                                                            }
                                                        }     
                                                        // End

                                                        $sys_api_details = SysApiSettings::where('type', '=', 'sms_gateway')->first();
                                                        $sys_api_details->user;
                                                        $sys_api_details->password;

                                                        $username = $sys_api_details->user;
                                                        $password = $sys_api_details->password;
                                                        $destination = $mobile; //Multiple numbers can be entered, separated by a comma
                                                        $source = $company_sms_number;
                                                        $text = $sms_message;
                                                        $ref = $sequence->id;

                                                        $smsDelayMinutes = $this->calculateSMSDelay($organisation_settings->timezone);
                                                        $content = 'username=' . rawurlencode($username) .
                                                        '&password=' . rawurlencode($password) .
                                                        '&to=' . rawurlencode($destination) .
                                                        '&from=' . rawurlencode($source) .
                                                        '&message=' . rawurlencode($text) .
                                                        '&maxsplit=5' .
                                                        '&delay=' . rawurlencode($smsDelayMinutes) .
                                                        '&ref=' . rawurlencode($ref);

                                                        $smsbroadcast_response = $this->sendSMSFunc($content);
                                                        $response_lines = explode("\n", $smsbroadcast_response);

                                                        if ($response_lines) {
                                                            foreach ($response_lines as $data_line) {
                                                                $message_data = "";
                                                                $message_data = explode(':', $data_line);
                                                                if ($message_data[0] == "OK") {
                                                                    $subtractCredits = intval($tenant_details->sms_credit) - 1;
                                                                    TenantDetail::where('tenant_id', '=', $sequence->tenant_id)->update(array('sms_credit' => $subtractCredits));
                                                                   
                                                                    //Run SMS Auto Top Up Program 
                                                                    $this->smsAutoTopUp($tenant_details);
                                                                    //END::----------->

                                                                    $vals['log_message'] = $sms_message;
                                                                    $vals['log_from'] = $company_sms_number;
                                                                    $vals['log_to'] = $mobile;
                                                                    $vals['lead_id'] = $this->lead_id;
                                                                    $vals['job_id'] = $this->moving_job_id;
                                                                    $vals['tenant_id'] = $sequence->tenant_id;
                                                                    $vals['log_type'] = 8; // Activity Email
                                                                    $vals['log_date'] = Carbon::now();
                                                                    CRMActivityLog::create($vals);
                                                                    unset($vals);
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            
                                             
                                        //  } catch (\Exception $ex) {
                                        //      continue; // jump to the next iteration
                                        //  }

                                    }

                                }
                            }
                        }
                    }
                } else if ($sequence->sys_job_type == 'Moving') {
                    $est_first_leg_start_time="";
                    $jobsmoving = JobsMoving::where('job_status', '=', $sequence->initial_status)
                    ->where('company_id', '=', $sequence->company_id)
                    ->where('tenant_id', '=', $sequence->tenant_id)
                    ->where('deleted', '=', '0')->get();
                        
                    if ($jobsmoving) {
                        foreach ($jobsmoving as $jobmoving) {                            
                            // //if sequence company_id is not same as job then skip
                            // if($sequence->company_id!=$jobmoving->company_id){
                            //     continue;
                            // }
                            //end                                                                                                                    
                                $send_now = false;
                                if ($sequence->sequence_type == 'Status Date') {
                                    $op_log = JobsMovingStatusLog::where('tenant_id', '=', $sequence->tenant_id)
                                        ->where('job_id', '=', $jobmoving->job_id)
                                        ->orderBy('created_at', 'DESC')->first();                                    

                                    $start = $op_log->created_at;
                                    $start = Carbon::createFromFormat('Y-m-d H:i:s', $start, 'UTC')->setTimezone('Australia/Melbourne');
                                    $end = Carbon::now('UTC')->setTimezone('Australia/Melbourne');
                                    $days=0;
                                    $hours = $end->diffInHours($start);  
                                    if((int)$hours>=24){
                                        $days = floor($hours/24);
                                    }
                                    $days_before_after = $sequence->days_after_initial_status;
                                    if($days_before_after<0){
                                        $days = (-1*$days);
                                    }
                                    if ((int)($days) == (int)($days_before_after)) {
                                        $send_now = true;
                                    }
                                    $contact_details = JobsMovingStatusLog::select('crm_contact_details.detail', 'crm_contacts.name')
                                        ->leftJoin('jobs_moving', 'jobs_moving_status_log.job_id', '=', 'jobs_moving.job_id')
                                        ->leftJoin('crm_contacts', 'jobs_moving.customer_id', '=', 'crm_contacts.lead_id')
                                        ->leftJoin('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                                        ->where('jobs_moving_status_log.id', '=', $op_log->id)
                                        ->where('crm_contact_details.detail_type', '=', 'Email')->first();

                                        $contact_details_mobile = JobsMovingStatusLog::select('crm_contact_details.detail')
                                        ->leftJoin('jobs_moving', 'jobs_moving_status_log.job_id', '=', 'jobs_moving.job_id')
                                        ->leftJoin('crm_contacts', 'jobs_moving.customer_id', '=', 'crm_contacts.lead_id')
                                        ->leftJoin('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                                        ->where('jobs_moving_status_log.id', '=', $op_log->id)
                                        ->where('crm_contact_details.detail_type', '=', 'Mobile')->first();
                                } else {
                                    $start = Carbon::createFromFormat('Y-m-d H:i:s', $jobmoving->job_date);
                                    $end = Carbon::today();
                                    $days_before_after = $sequence->days_before_after_job_date;
                                    $days = $end->diffInDays($start);
                                    if($days_before_after<0){
                                        if($end->gt($start)){continue;}
                                    }else{
                                        if($end->lt($start)){continue;}
                                    }
                                    $days = ($days_before_after<0)? ((-1)*$days):$days;

                                    if ((int)($days) == (int)($days_before_after)) {
                                        $send_now = true;
                                    }
                                    $contact_details = DB::table('crm_contacts')
                                            ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                                            ->select('crm_contacts.name', 'crm_contact_details.detail')
                                            ->where(['crm_contacts.lead_id' => $jobmoving->customer_id, 'crm_contact_details.detail_type' => 'Email'])
                                            ->first();
                                        $contact_details_mobile = DB::table('crm_contacts')
                                            ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                                            ->select('crm_contacts.name', 'crm_contact_details.detail')
                                            ->where(['crm_contacts.lead_id' => $jobmoving->customer_id, 'crm_contact_details.detail_type' => 'Mobile'])
                                            ->first();
                                }                                
                                if ($send_now) {
                                    JobsMoving::where('job_id', $jobmoving->job_id)->update([
                                        'job_status' => $sequence->post_status,
                                    ]);
                                    $this->job = $jobmoving;
                                    $this->lead_id = $this->job->customer_id;
                                    $this->paidAmount = 0;
                                    $this->totalAmount = 0;

                                    $full_name = '';
                                    $mobile = '';
                                    $email_to = $contact_details->detail;

                                    if (isset($contact_details_mobile->detail)) {
                                        $mobile = $contact_details_mobile->detail;
                                    }

                                    if (isset($contact_details->name)) {
                                        $full_name = $contact_details->name;
                                    }

                                    $full_name_ary = @explode(' ', $full_name, 2);
                                    if (count($full_name_ary) > 1) {
                                        $first_name = $full_name_ary[0];
                                        $last_name = $full_name_ary[1];
                                    } else {
                                        $first_name = $full_name_ary[0];
                                        $last_name = '';
                                    }    
                                    if (isset($this->job->job_id)) {

                                        $this->jobs_moving_id = $this->job->job_id;
                                        $this->job_id = $this->job->job_number;
                                        $this->moving_job_id = $this->job->job_id;
                                        $this->pickup_suburb = $this->job->pickup_suburb;
                                        $this->job_date = $this->job->job_date;
                                        $this->delivery_suburb = $this->job->delivery_suburb;
                                        $this->pickup_address = $this->job->pickup_address . " " . $this->job->pickup_suburb . " " . $this->job->pickup_post_code;
                                        $this->drop_off_address = $this->job->drop_off_address . " " . $this->job->delivery_suburb . " " . $this->job->drop_off_post_code;
                                        $this->job_date = date('d-m-Y', strtotime($this->job->job_date));
                                        $this->job_start_time = $this->job->job_start_time;
                                        $this->invoice = Invoice::where('job_id', '=', $this->job->job_id)->where('sys_job_type', '=', 'Moving')->where('tenant_id', '=', $sequence->tenant_id)->first();
                                        $this->storage_invoice = Invoice::where('job_id', '=', $this->job->job_id)->where('sys_job_type', '=', 'Moving_Storage')->where('tenant_id', '=', $sequence->tenant_id)->first();
                                        if (isset($this->invoice->id)):
                                            $this->paidAmount = $this->invoice->getPaidAmount();
                                            $this->totalAmount = $this->invoice->getTotalAmount();
                                        endif;
                                        $this->job_leg_start_time = JobsMovingLegs::where('job_id', '=', $this->job->job_id)->pluck("est_start_time")->first();
                                        if($this->job_leg_start_time){
                                            $est_first_leg_start_time=$this->job_leg_start_time;
                                        } 
                                    }

                                    //inventory_list parameter
                                    $mov_inv = new JobsMovingInventory();
                                    $inv_list = $mov_inv->getInventoryListForEmail($sequence->tenant_id, $this->job->job_id);
                                    //-->

                                    $external_inventory_form_param = base64_encode('tenant_id=' . $sequence->tenant_id . '&job_id=' . $this->job->job_id);
                                    $external_inventory_form = request()->getSchemeAndHttpHost() . '/removals-inventory-form/' . $external_inventory_form_param;
                                    $external_inventory_form_button = '<a href="'.$external_inventory_form.'" style="color: #fff;background-color: #26a69a;border-radius: 3px;cursor: pointer;padding: 8px 14px;line-height: 30px;" >Inventory Form</a>';


                                    //try{
                                    if (!empty($contact_details->detail)) {
                                        if ($sequence->send_email == 'Y') {                                        

                                            $template = EmailTemplates::where('id', '=', $sequence->email_template_id)->first();
                                            if ($template) {

                                                $email_subject = $template->email_subject;
                                                $email_body = $template->email_body;                                                
                                                $data = [
                                                    'to' => $email_to,
                                                    'from_name' => $sequence->from_email_name,
                                                    'from_email' => $sequence->from_email,
                                                    'reply_to' => $sequence->from_email,
                                                    'jobs_moving_id' => $this->job->job_id,
                                                    'job_id' => $this->job->job_number,
                                                    'lead_id' => $this->job->customer_id,
                                                    'first_name' => $first_name,
                                                    'last_name' => $last_name,
                                                    'pickup_suburb' => $this->job->pickup_suburb,
                                                    'delivery_suburb' => $this->job->delivery_suburb,
                                                    'pickup_address' => $this->job->pickup_address . " " . $this->job->pickup_suburb . " " . $this->job->pickup_post_code,
                                                    'delivery_address' => $this->job->drop_off_address . " " . $this->job->delivery_suburb . " " . $this->job->drop_off_post_code,
                                                    'phone' => $mobile,
                                                    'mobile' => $mobile,
                                                    'job_date' => date('d-m-Y', strtotime($this->job->job_date)),
                                                    'email' => $email_to,
                                                    'total_amount' => number_format((float)($this->totalAmount), 2, '.', ','),
                                                    'total_paid' => number_format((float)($this->paidAmount), 2, '.', ','),
                                                    'total_due' => number_format((float)($this->totalAmount - $this->paidAmount), 2, '.', ','),
                                                    'external_inventory_form' => $external_inventory_form,
                                                    'external_inventory_form_button' => $external_inventory_form_button,
                                                    'inventory_list' => $inv_list,
                                                    'book_now_button' => '',
                                                    'user_first_name' => '',
                                                    'user_last_name' => '',
                                                    'est_start_time' => $this->job->job_start_time,
                                                    'est_first_leg_start_time' => $est_first_leg_start_time,
                                                    'user_email_signature' => ''                                                    
                                                ];

                                                if (preg_match_all("/{(.*?)}/", $email_subject, $m)) {
                                                    foreach ($m[1] as $i => $varname) {
                                                        $email_subject = str_replace($m[0][$i], sprintf('%s', $data[$varname]), $email_subject);
                                                    }
                                                }

                                                if (preg_match_all("/{(.*?)}/", $email_body, $m)) {
                                                    foreach ($m[1] as $i => $varname) {
                                                        $email_body = str_replace($m[0][$i], sprintf('%s', $data[$varname]), $email_body);
                                                    }
                                                }

                                                $files = [];
                                                //attach quote file
                                                if ($template->attach_quote == "Y") {
                                                    $quote = Quotes::where('job_id', '=', $jobmoving->job_id)->where('sys_job_type', '=', 'Moving')->first();
                                                    if ($quote) {
                                                        if (File::exists(public_path() . '/quote-files/' . $quote->quote_file_name)) {
                                                            //$files[] = public_path('quote-files') . '/' . $quote->quote_file_name;
                                                            $quote_path['path'] = public_path('quote-files') . '/' . $quote->quote_file_name;
                                                            $quote_path['name'] = $quote->quote_file_name;
                                                            $files[] = $quote_path;
                                                        }
                                                    }
                                                }
                                                //end:: attach quote

                                                // Attach Invoice PDF
                                                if($template->attach_invoice=='Y'){
                                                    if($this->job){
                                                        if ($this->invoice && $this->invoice->file_original_name != NULL) {
                                                            $invoice_file_url = '/invoice-files/' . $this->invoice->file_original_name;
                                                            $invoice_file_name = $this->invoice->file_original_name;
                                                            if (File::exists(public_path() . $invoice_file_url)) {
                                                                $invoice_path['path'] = public_path() . $invoice_file_url;
                                                                $invoice_path['name'] = $invoice_file_name;
                                                                $files[] = $invoice_path;
                                                            }
                                                        }
                                                    }
                                                }
                                        
                                                // Attach Storage Invoice PDF
                                                if($template->attach_storage_invoice=='Y'){
                                                    if($this->job){
                                                        if ($this->storage_invoice && $this->storage_invoice->file_original_name != NULL) {
                                                            $storage_invoice_file_url = '/invoice-files/' . $this->storage_invoice->file_original_name;
                                                            $storage_invoice_file_name = $this->storage_invoice->file_original_name;
                                                            if (File::exists(public_path() . $storage_invoice_file_url)) {
                                                                $storage_invoice_path['path'] = public_path() . $storage_invoice_file_url;
                                                                $storage_invoice_path['name'] = $storage_invoice_file_name;
                                                                $files[] = $storage_invoice_path;
                                                            }
                                                        }
                                                    }
                                                }

                                                if($template->attach_work_order=='Y'){
                                                    if($this->job){
                                                        if($this->job->work_order_file_name != NULL){
                                                                $workorder_file_url = '/invoice-files/' . $this->job->work_order_file_name;
                                                                $workorder_file_name = $this->job->work_order_file_name;
                                                                if (File::exists(public_path() . $workorder_file_url)) {
                                                                    $wo_path['path'] = public_path() . $workorder_file_url;
                                                                    $wo_path['name'] = $workorder_file_name;
                                                                    $files[] = $wo_path;
                                                                }
                                                        }
                                                    }
                                                }
                                        
                                                if($template->attach_pod=='Y'){
                                                    if($this->job){
                                                        if($this->job->pod_file_name != NULL){
                                                                $pod_file_url = '/invoice-files/' . $this->job->pod_file_name;
                                                                $pod_file_name = $this->job->pod_file_name;
                                                                if (File::exists(public_path() . $pod_file_url)) {
                                                                    $pod_path['path'] = public_path() . $pod_file_url;
                                                                    $pod_path['name'] = $pod_file_name;
                                                                    $files[] = $pod_path;
                                                                }
                                                        }
                                                    }
                                                }

                                                //attach insurance quote file
                                                if($template->attach_insurance=='Y'){
                                                    $coverFreight_connected = TenantApiDetail::where(['tenant_id' => $sequence->tenant_id, 'provider' => 'CoverFreight'])->first();
                                                    if($coverFreight_connected){
                                                        if($this->job){
                                                            if($this->job->insurance_file_name != NULL){
                                                                $insurance_file_url = '/insurance-quote/' . $this->job->insurance_file_name;
                                                                $insurance_file_name = $this->job->insurance_file_name;
                                                                if (File::exists(public_path() . $insurance_file_url)) {
                                                                    $insurance_path['path'] = public_path() . $insurance_file_url;
                                                                    $insurance_path['name'] = $insurance_file_name;
                                                                    $files[] = $insurance_path;
                                                                }
                                                            }else{
                                                                // call coverfreight api and get insurance quote
                                                                try{
                                                                    $crmlead_model = new CRMLeads();
                                                                    $r = $crmlead_model->generateInsuranceQuote($op_log->crm_opportunity_id, $sequence->tenant_id);
                                                                    if($r['status']==1){
                                                                        $this->job = JobsMoving::find($this->job->job_id);
                                                                        $insurance_file_url = '/insurance-quote/' . $this->job->insurance_file_name;
                                                                        $insurance_file_name = $this->job->insurance_file_name;
                                                                        if (File::exists(public_path() . $insurance_file_url)) {
                                                                            $insurance_path['path'] = public_path() . $insurance_file_url;
                                                                            $insurance_path['name'] = $insurance_file_name;
                                                                            $files[] = $insurance_path;
                                                                        }
                                                                    }
                                                                }catch(\Exception $ex){
                                                                    // //START:: Dev Logs
                                                                    $dev['action'] = 'Insurance Quote Error 2';
                                                                    $dev['log'] = "Opp ID: ".$op_log->crm_opportunity_id;
                                                                    $dev['created_at'] = Carbon::now();
                                                                    DevLogs::create($dev);
                                                                    // //END:: Dev Logs
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                //end:: attach insurance quote file

                                                $attachments = EmailTemplateAttachments::where(['email_template_id'=>$template->id])->get();
                                                if($attachments){
                                                        foreach($attachments as $attach){
                                                            if (File::exists(public_path().$attach->attachment_file_location)) {
                                                                $attach_path['path'] = public_path().$attach->attachment_file_location;
                                                                $attach_path['name'] = $attach->attachment_file_name;
                                                                $files[] = $attach_path;
                                                            }
                                                        }
                                                }

                                                $data['files'] = $files;
                                                $data['auto'] = 1; // if email send from cron job
                                                $data['email_subject'] = $email_subject;
                                                $data['email_body'] = $email_body;
                                                Mail::to($email_to)->send(new sendMail($data));

                                                $vals['log_subject'] = $data['email_subject'];
                                                $vals['log_message'] = $data['email_body'];
                                                $vals['log_from'] = $data['from_email'];
                                                $vals['log_to'] = $data['to'];
                                                $vals['job_id'] = $this->job->job_id;
                                                $vals['lead_id'] = $this->job->customer_id;
                                                $vals['tenant_id'] = $sequence->tenant_id;
                                                $vals['log_type'] = 3; // Activity Email
                                                $vals['log_date'] = Carbon::now();
                                                $model = CRMActivityLog::create($vals);
                                                unset($vals);

                                                // Log email attachments files
                                                if($files){
                                                    foreach($files as $attach){
                                                        $attach_log['attachment_type'] = $attach['name'];
                                                        $attach_log['attachment_content'] = $attach['path'];
                                                        $attach_log['log_id'] = $model->id;
                                                        $attach_log['tenant_id'] = $sequence->tenant_id;
                                                        $attach_log['created_at'] = Carbon::now();
                                                        $attach_log['updated_at'] = Carbon::now();
                                                        CRMActivityLogAttachment::create($attach_log);
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    if ($sequence->send_sms == 'Y' && $tenant_details->sms_credit > 0) {
                                        $template = SMSTemplates::where('id', '=', $sequence->sms_template_id)->first();                        
                                            if ($template) {
                                                if (!empty($mobile)) {
                                                    // Set Dynamic Parameters
                                                    $smsdata = [
                                                        'job_id' => $this->job->job_number,
                                                        'first_name' => $first_name,
                                                        'last_name' => $last_name,
                                                        'pickup_suburb' => $this->job->pickup_suburb,
                                                        'delivery_suburb' => $this->job->delivery_suburb,
                                                        'pickup_address' => $this->job->pickup_address." ".$this->job->pickup_suburb." ".$this->job->pickup_post_code,
                                                        'delivery_address' => $this->job->drop_off_address." ".$this->job->delivery_suburb." ".$this->job->drop_off_post_code,
                                                        'mobile' => $mobile,
                                                        'email' => $email_to,
                                                        'job_date' => date('d-m-Y', strtotime($this->job->job_date)),    
                                                        'user_first_name' => '',
                                                        'user_last_name' => '',
                                                        'est_start_time' => $this->job_start_time,
                                                        'est_first_leg_start_time' => $est_first_leg_start_time,  
                                                        'total_amount' => number_format((float)($this->totalAmount), 2, '.', ','),
                                                        'total_paid' => number_format((float)($this->paidAmount), 2, '.', ','),
                                                        'total_due' => number_format((float)($this->totalAmount - $this->paidAmount), 2, '.', ','),          
                                                        'external_inventory_form' => $external_inventory_form
                                                    ];
                                        
                                                    $sms_message = $template->sms_message;
                                        
                                                    if (preg_match_all("/{(.*?)}/", $sms_message, $m)) {
                                                        foreach ($m[1] as $i => $varname) {
                                                            $sms_message = str_replace($m[0][$i], sprintf('%s', $smsdata[$varname]), $sms_message);
                                                        }
                                                    }     
                                                    // End

                                                    $sys_api_details = SysApiSettings::where('type', '=', 'sms_gateway')->first();
                                                    $sys_api_details->user;
                                                    $sys_api_details->password;

                                                    $username = $sys_api_details->user;
                                                    $password = $sys_api_details->password;
                                                    $destination = $mobile; //Multiple numbers can be entered, separated by a comma
                                                    $source = $company_sms_number; //'Onexfort';
                                                    $text = $sms_message;
                                                    $ref = $sequence->id;

                                                    $smsDelayMinutes = $this->calculateSMSDelay($organisation_settings->timezone);
                                                    $content = 'username=' . rawurlencode($username) .
                                                    '&password=' . rawurlencode($password) .
                                                    '&to=' . rawurlencode($destination) .
                                                    '&from=' . rawurlencode($source) .
                                                    '&message=' . rawurlencode($text) .
                                                    '&maxsplit=5' .
                                                    '&delay=' . rawurlencode($smsDelayMinutes) .
                                                    '&ref=' . rawurlencode($ref);

                                                    $smsbroadcast_response = $this->sendSMSFunc($content);
                                                    $response_lines = explode("\n", $smsbroadcast_response);

                                                    if ($response_lines) {
                                                        foreach ($response_lines as $data_line) {
                                                            $message_data = "";
                                                            $message_data = explode(':', $data_line);
                                                            if ($message_data[0] == "OK") {
                                                                $subtractCredits = intval($tenant_details->sms_credit) - 1;
                                                                TenantDetail::where('tenant_id', '=', $sequence->tenant_id)->update(array('sms_credit' => $subtractCredits));

                                                                //Run SMS Auto Top Up Program 
                                                                    $this->smsAutoTopUp($tenant_details);
                                                                //END::----------->

                                                                $vals['log_message'] = $sms_message;
                                                                $vals['log_from'] = $company_sms_number;
                                                                $vals['log_to'] = $mobile;
                                                                $vals['job_id'] = $this->job->job_id;
                                                                $vals['lead_id'] = $this->job->customer_id;
                                                                $vals['tenant_id'] = $sequence->tenant_id;
                                                                $vals['log_type'] = 8; // Activity Email
                                                                $vals['log_date'] = Carbon::now();
                                                                CRMActivityLog::create($vals);
                                                                unset($vals);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }                                        
                                    
                                    // } catch (\Exception $ex) {
                                    //     continue; // jump to the next iteration
                                    // }
                                }
                        }
                    }
                } else if ($sequence->sys_job_type == 'Cleaning') {

                    $jobscleaning = JobsCleaning::where('job_status', '=', $sequence->initial_status)
                        ->where('tenant_id', '=', $sequence->tenant_id)
                        ->where('company_id', '=', $sequence->company_id)
                        ->where('deleted', '=', '0')->get();

                    if ($jobscleaning) {
                        foreach ($jobscleaning as $jobcleaning) {
                            
                            //if sequence company_id is not same as job then skip
                            if($sequence->company_id!=$jobcleaning->company_id){
                                continue;
                            }
                            //end                        

                                $send_now = false;
                                if ($sequence->sequence_type == 'Status Date') {
                                    $jc_log = JobsCleaningStatusLog::where('tenant_id', '=', $sequence->tenant_id)
                                        ->where('job_id', '=', $jobcleaning->id)
                                        ->orderBy('created_at', 'DESC')->first();
                                    
                                    $start = $jc_log->created_at;
                                    $start = Carbon::createFromFormat('Y-m-d H:i:s', $start, 'UTC')->setTimezone('Australia/Melbourne');
                                    $end = Carbon::today();
                                    $days = $end->diffInDays($start);
                                    $days_before_after = $sequence->days_after_initial_status;
                                    $days = ($days_before_after<0)? ((-1)*$days):$days;

                                    if ((int)($days) > (int)($days_before_after)) {
                                        $send_now = true;
                                        $contact_details = JobsMovingStatusLog::select('crm_contact_details.detail', 'crm_contacts.name')
                                        ->leftJoin('jobs_moving', 'jobs_moving_status_log.job_id', '=', 'jobs_moving.job_id')
                                        ->leftJoin('crm_contacts', 'jobs_moving.customer_id', '=', 'crm_contacts.lead_id')
                                        ->leftJoin('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                                        ->where('jobs_moving_status_log.id', '=', $jc_log->id)
                                        ->where('crm_contact_details.detail_type', '=', 'Email')->first();

                                        $contact_details_mobile = JobsMovingStatusLog::select('crm_contact_details.detail')
                                        ->leftJoin('jobs_moving', 'jobs_moving_status_log.job_id', '=', 'jobs_moving.job_id')
                                        ->leftJoin('crm_contacts', 'jobs_moving.customer_id', '=', 'crm_contacts.lead_id')
                                        ->leftJoin('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                                        ->where('jobs_moving_status_log.id', '=', $jc_log->id)
                                        ->where('crm_contact_details.detail_type', '=', 'Mobile')->first();
                                    }
                                } else {
                                    $start = $jobscleaning->created_at;
                                    $start = Carbon::createFromFormat('Y-m-d H:i:s', $start, 'UTC')->setTimezone('Australia/Melbourne');
                                    $end = Carbon::today();
                                    $days = $end->diffInDays($start);
                                    $days_before_after = $sequence->days_before_after_job_date;
                                    $days = ($days_before_after<0)? ((-1)*$days):$days;

                                    if ((int)($days) == (int)($days_before_after)) {
                                        $send_now = true;
                                        $contact_details = DB::table('crm_contacts')
                                            ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                                            ->select('crm_contacts.name', 'crm_contact_details.detail')
                                            ->where(['crm_contacts.lead_id' => $jobscleaning->customer_id, 'crm_contact_details.detail_type' => 'Email'])
                                            ->first();
                                        $contact_details_mobile = DB::table('crm_contacts')
                                            ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                                            ->select('crm_contacts.name', 'crm_contact_details.detail')
                                            ->where(['crm_contacts.lead_id' => $jobscleaning->customer_id, 'crm_contact_details.detail_type' => 'Mobile'])
                                            ->first();
                                    }
                                }

                                if ($send_now) {
                                    JobsCleaning::where('job_id', $jobcleaning->job_id)->update([
                                        'job_status' => $sequence->post_status,
                                    ]);
                                    //try{
                                        $this->paidAmount = 0;
                                        $this->totalAmount = 0;
                                        $this->job = $jobcleaning;
                                    if (!empty($contact_details->detail)) {
                                        if ($sequence->send_email == 'Y') {

                                            $full_name = '';
                                            $mobile = '';
                                            $email_to = $contact_details->detail;

                                            if (isset($contact_details_mobile->detail)) {
                                                $mobile = $contact_details_mobile->detail;
                                            }

                                            if (isset($contact_details->name)) {
                                                $full_name = $contact_details->name;
                                            }

                                            $full_name_ary = @explode(' ', $full_name, 2);
                                            if (count($full_name_ary) > 1) {
                                                $first_name = $full_name_ary[0];
                                                $last_name = $full_name_ary[1];
                                            } else {
                                                $first_name = $full_name_ary[0];
                                                $last_name = '';
                                            }

                                            $template = EmailTemplates::where('id', '=', $sequence->email_template_id)->first();
                                            if ($template) {

                                                $email_subject = $template->email_subject;
                                                $email_body = $template->email_body;
                                                if (isset($this->job->job_id)) {
                                                    $this->invoice = Invoice::where('job_id', '=', $this->job->job_id)->where('sys_job_type', '=', 'Cleaning')->where('tenant_id', '=', $sequence->tenant_id)->first();

                                                    if (isset($this->invoice->id)):
                                                        $this->paidAmount = $this->invoice->getPaidAmount();
                                                        $this->totalAmount = $this->invoice->getTotalAmount();
                                                    endif;
                                                }

                                                //inventory_list parameter
                                                $mov_inv = new JobsMovingInventory();
                                                $inv_list = $mov_inv->getInventoryListForEmail($sequence->tenant_id, $this->job->job_id);
                                                //-->

                                                $external_inventory_form = '';
                                                $external_inventory_form_param = base64_encode('tenant_id=' . $sequence->tenant_id . '&job_id=' . $this->job->job_id);
                                                $external_inventory_form = request()->getSchemeAndHttpHost() . '/removals-inventory-form/' . $external_inventory_form_param;

                                                $data = [
                                                    'to' => $email_to,
                                                    'from_name' => $sequence->from_email_name,
                                                    'from_email' => $sequence->from_email,
                                                    'reply_to' => $sequence->from_email,
                                                    'jobs_moving_id' => $this->job->job_id,
                                                    'job_id' => $this->job->job_number,
                                                    'lead_id' => $this->job->customer_id,
                                                    'first_name' => $first_name,
                                                    'last_name' => $last_name,
                                                    'pickup_suburb' => $this->job->pickup_suburb,
                                                    'delivery_suburb' => $this->job->delivery_suburb,
                                                    'pickup_address' => $this->job->pickup_address . " " . $this->job->pickup_suburb . " " . $this->job->pickup_post_code,
                                                    'delivery_address' => $this->job->drop_off_address . " " . $this->job->delivery_suburb . " " . $this->job->drop_off_post_code,
                                                    'phone' => $mobile,
                                                    'mobile' => $mobile,
                                                    'job_date' => date('d-m-Y', strtotime($this->job->job_date)),
                                                    'email' => $email_to,
                                                    'total_amount' => $this->totalAmount,
                                                    'total_paid' => $this->paidAmount,
                                                    'total_due' => $this->totalAmount - $this->paidAmount,
                                                    'external_inventory_form' => $external_inventory_form,
                                                    'inventory_list' => $inv_list,
                                                    'book_now_button' => '',
                                                    'user_first_name' => '',
                                                    'user_last_name' => '',
                                                    'est_start_time' => $this->job->job_start_time,
                                                    'est_first_leg_start_time' => '',
                                                    'user_email_signature' => ''  
                                                ];

                                                if (preg_match_all("/{(.*?)}/", $email_subject, $m)) {
                                                    foreach ($m[1] as $i => $varname) {
                                                        $email_subject = str_replace($m[0][$i], sprintf('%s', $data[$varname]), $email_subject);
                                                    }
                                                }

                                                if (preg_match_all("/{(.*?)}/", $email_body, $m)) {
                                                    foreach ($m[1] as $i => $varname) {
                                                        $email_body = str_replace($m[0][$i], sprintf('%s', $data[$varname]), $email_body);
                                                    }
                                                }

                                                //attach quote file
                                                if ($template->attach_quote == "Y") {
                                                    $quote = Quotes::where('job_id', '=', $jobcleaning->id)->where('sys_job_type', '=', 'Cleaning')->first();
                                                    if ($quote) {
                                                        $files = [];
                                                        if (File::exists(public_path() . '/quote-files/' . $quote->quote_file_name)) {
                                                            $files[] = public_path('quote-files') . '/' . $quote->quote_file_name;
                                                        }
                                                        $data['files'] = $files;
                                                    }
                                                }
                                                //end:: attach quote

                                                $data['email_subject'] = $email_subject;
                                                $data['email_body'] = $email_body;
                                                Mail::to($email_to)->send(new sendMail($data));
                                            }

                                            $vals['log_subject'] = $data['email_subject'];
                                            $vals['log_message'] = $data['email_body'];
                                            $vals['log_from'] = $data['from_email'];
                                            $vals['log_to'] = $data['to'];
                                            $vals['job_id'] = $this->job->job_id;
                                            $vals['lead_id'] = $this->job->customer_id;
                                            $vals['tenant_id'] = $sequence->tenant_id;
                                            $vals['log_type'] = 3; // Activity Email
                                            $vals['log_date'] = Carbon::now();
                                            CRMActivityLog::create($vals);
                                            unset($vals);
                                        }
                                    }
                                        if ($sequence->send_sms == 'Y' && $tenant_details->sms_credit > 0) {
                                            $full_name = '';
                                            $mobile = '';
                                            if (isset($contact_details_mobile->detail)) {
                                                $mobile = $contact_details_mobile->detail;
                                            }

                                            if (isset($contact_details->name)) {
                                                $full_name = $contact_details->name;
                                            }

                                            $full_name_ary = @explode(' ', $full_name, 2);
                                            if (count($full_name_ary) > 1) {
                                                $first_name = $full_name_ary[0];
                                                $last_name = $full_name_ary[1];
                                            } else {
                                                $first_name = $full_name_ary[0];
                                                $last_name = '';
                                            }

                                            $template = SMSTemplates::where('id', '=', $sequence->sms_template_id)->first();
                                            if ($template) {
                                                if (!empty($mobile)) {
                                                    $sms_message = $template->sms_message;

                                                    $sys_api_details = SysApiSettings::where('type', '=', 'sms_gateway')->first();
                                                    $sys_api_details->user;
                                                    $sys_api_details->password;

                                                    $username = $sys_api_details->user;
                                                    $password = $sys_api_details->password;
                                                    $destination = $mobile; //Multiple numbers can be entered, separated by a comma
                                                    $source = $company_sms_number; //'Onexfort';
                                                    $text = $sms_message;
                                                    $ref = $sequence->id;

                                                    $smsDelayMinutes = $this->calculateSMSDelay($organisation_settings->timezone);
                                                    $content = 'username=' . rawurlencode($username) .
                                                    '&password=' . rawurlencode($password) .
                                                    '&to=' . rawurlencode($destination) .
                                                    '&from=' . rawurlencode($source) .
                                                    '&message=' . rawurlencode($text) .
                                                    '&maxsplit=5' .
                                                    '&delay=' . rawurlencode($smsDelayMinutes) .
                                                    '&ref=' . rawurlencode($ref);

                                                    $smsbroadcast_response = $this->sendSMSFunc($content);
                                                    $response_lines = explode("\n", $smsbroadcast_response);

                                                    if ($response_lines) {
                                                        foreach ($response_lines as $data_line) {
                                                            $message_data = "";
                                                            $message_data = explode(':', $data_line);
                                                            if ($message_data[0] == "OK") {
                                                                $subtractCredits = intval($tenant_details->sms_credit) - 1;
                                                                TenantDetail::where('tenant_id', '=', $sequence->tenant_id)->update(array('sms_credit' => $subtractCredits));

                                                                //Run SMS Auto Top Up Program 
                                                                $this->smsAutoTopUp($tenant_details);
                                                                //END::----------->

                                                                $vals['log_message'] = $sms_message;
                                                                $vals['log_from'] = $company_sms_number;
                                                                $vals['log_to'] = $mobile;
                                                                $vals['job_id'] = $this->job->job_id;
                                                                $vals['lead_id'] = $this->job->customer_id;
                                                                $vals['tenant_id'] = $sequence->tenant_id;
                                                                $vals['log_type'] = 8; // Activity Email
                                                                $vals['log_date'] = Carbon::now();
                                                                CRMActivityLog::create($vals);
                                                                unset($vals);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }                                        
                                    
                                // } catch (\Exception $ex) {
                                //     continue; // jump to the next iteration
                                // }
                                }
                        }
                    }
                }
            }
        }
    }

    protected function smsAutoTopUp($tenant_details){
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
    protected function calculateSMSDelay($timezone){
        // 9pm-12pm time slot
        $slotA1 = Carbon::createFromTimeString('21:00')->setTimezone($timezone);
        $slotA2 = Carbon::createFromTimeString('23:59')->setTimezone($timezone);

        // 12am-7am time slot
        $slotB1 = Carbon::createFromTimeString('00:00')->setTimezone($timezone);
        $slotB2 = Carbon::createFromTimeString('07:00')->setTimezone($timezone);

        $currentTime  = Carbon::now('UTC')->setTimezone($timezone);

        if ($currentTime->between($slotA1,$slotA2)) {
            $timeDiffMin = (7*60 + (1+$currentTime->diffInMinutes($slotA2)));
        }else if($currentTime->between($slotB1,$slotB2)){
            $timeDiffMin = $currentTime->diffInMinutes($slotB2);
        }else{
            $timeDiffMin=0;
        }
        return $timeDiffMin;
    }
}
