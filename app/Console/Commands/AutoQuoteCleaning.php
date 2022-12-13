<?php

namespace App\Console\Commands;

use App\Companies;
use Illuminate\Console\Command;
use App\CRMActivityLog;
use App\CRMActivityLogAttachment;
use App\CRMContactDetail;
use App\CRMContacts;
use App\CRMLeads;
use App\CRMOpPipelineStatuses;
use App\CRMOpportunities;
use App\Customers;
use App\EmailTemplateAttachments;
use App\EmailTemplates;
use App\Invoice;
use App\JobsCleaning;
use App\JobsMoving;
use App\JobsMovingInventory;
use App\Mail\CustomerMail;
use App\Mail\sendMail;
use App\OrganisationSettings;
use App\QuoteItem;
use App\Quotes;
use App\SMSTemplates;
use App\Tax;
use App\TenantApiDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;

class AutoQuoteCleaning extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto-quote-cleaning';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to auto quote cleaning.';

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
    //START:: Removal Auto Qoute Program
    public function handle()
    {
        ini_set('max_execution_time', 0);
        ini_set("memory_limit", "-1");
        
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
    
                            $crm_contacts = CRMContacts::where('lead_id', '=', $job->customer_id)->first();
                            $crm_contact_email = CRMContactDetail::where('contact_id', '=', $crm_contacts->id)->where('detail_type', '=', 'Email')->first();
                            $crm_contact_phone = CRMContactDetail::where('contact_id', '=', $crm_contacts->id)->where('detail_type', '=', 'Mobile')->first();
                            $customer_email = ($crm_contact_email)? $crm_contact_email->detail:'';
                            $customer_phone = ($crm_contact_phone)? $crm_contact_phone->detail:'';
                            $organisation_settings = OrganisationSettings::where('tenant_id', '=', $job->tenant_id)->first();
                            $paidAmount = 0;
                            $totalAmount = 0;

                            $name = explode(" ", $crm_contacts->name, 2);
                            if(count($name)>1){
                                $l_firstname = $name[0];
                                $l_lastname = $name[1];
                            }else{
                                $l_firstname = $crm_contacts->name;
                                $l_lastname = '';
                            }

                            //inventory_list parameter
                            $mov_inv = new JobsMovingInventory();
                            $inv_list = $mov_inv->getInventoryListForEmail($job->tenant_id, $job->job_id);
                            //-->

                            $external_inventory_form_param = base64_encode('tenant_id='.$job->tenant_id.'&job_id='.$job->job_id);
                            $external_inventory_form = request()->getSchemeAndHttpHost().'/removals-inventory-form/'.$external_inventory_form_param;

                        $data = [
                            'job_id' => $job->job_number,
                            'first_name' => $l_firstname,
                            'last_name' => $l_lastname,
                            'mobile' => $customer_phone,
                            'email' => $customer_email,
                            'job_date' => date('d-m-Y', strtotime($job->job_date)),
                            'total_amount' => $totalAmount,
                            'total_paid' => $paidAmount,
                            'total_due' => ($totalAmount - $paidAmount),
                            'external_inventory_form' => $external_inventory_form,
                            'inventory_list' => $inv_list
                        ];
                        $files = [];
    
                            //if ($var_status != 'Fail') {

                            //START::Sending Success email
                                $email_template = EmailTemplates::where('id', '=', $tenant->quote_email_template_id)->first();
                                if ($email_template) {                                                                     
                                    $emailData = $this->setEmailParameter($email_template->email_subject, $email_template->email_body, $data);
                                    $email_data['from_email'] = $email_template->from_email;
                                    $email_data['from_name'] = $email_template->from_email_name;
                                    $email_data['email_subject'] = $emailData['subject'];
                                    $email_data['email_body'] = $emailData['body'];
                                    $email_data['reply_to'] = $organisation_settings->company_email;
                                    $email_data['job_id'] = $job->customer_id;
                                    $email_data['cc'] = '';
                                    $email_data['bcc'] = '';
                                    $email_data['to'] = $customer_email;                                 
    
                                    if($email_template->attach_quote=='Y'){
                                        $files[]=$quote_file_url;
                                    }
                                    $email_data['files'] = $files;
                                    }                                                                        
                                    Mail::to($email_data['to'])->send(new CustomerMail($email_data));
                                    echo '<br/><br/>Success Email Sent';                                                            
                            
                                    //Add Activity Log for email                                   
                                    $activitydata['lead_id'] = $job->customer_id;
                                    $activitydata['job_id'] = $job->job_id;
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
                                            $attach['attachment_type'] = $crm_contacts->name.' - Estimate';
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
                            $crm_contacts = CRMContacts::where('lead_id', '=', $job->customer_id)->first();
                            $crm_contact_email = CRMContactDetail::where('contact_id', '=', $crm_contacts->id)->where('detail_type', '=', 'Email')->first();
                            $crm_contact_phone = CRMContactDetail::where('contact_id', '=', $crm_contacts->id)->where('detail_type', '=', 'Mobile')->first();
                            $customer_email = ($crm_contact_email)? $crm_contact_email->detail:'';
                            $customer_phone = ($crm_contact_phone)? $crm_contact_phone->detail:'';
                            $organisation_settings = OrganisationSettings::where('tenant_id', '=', $job->tenant_id)->first();
                            $paidAmount = 0;
                            $totalAmount = 0;
                            $external_inventory_form_param = base64_encode('tenant_id='.$job->tenant_id.'&job_id='.$job->job_id);
                            $external_inventory_form = request()->getSchemeAndHttpHost().'/removals-inventory-form/'.$external_inventory_form_param;

                        $sms_data = [
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
                            $this->sendSMS($job->customer_id,$tenant->tenant_id,$customer_phone,$tenant->quote_sms_template_id,$quote_file_url_sms,$sms_data);
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
                        $data['job_id'] = $job->job_id;
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

    public function generateCleaningQuote($opportunity_id,$tenant_id){
        
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
            $this->stripe_connected=0;

            $stripe = TenantApiDetail::where('tenant_id', $tenant_id)
                    ->where('provider', 'Stripe')->first();
            if($stripe){
                if(isset($stripe->account_key) && !empty($stripe->account_key)){
                    $this->stripe_connected=1;
                }
            }

            // Job Deposit Required for the tenant-------------------//

            $auto_quote = DB::table('jobs_cleaning_auto_quoting as t1')
                ->where(['t1.auto_quote_enabled' => 'Y','tenant_id'=>$tenant_id])
                ->first();    

            $this->job = JobsCleaning::where('crm_opportunity_id', '=', $this->opportunity->id)->where('tenant_id', '=', $tenant_id)->first();
            $this->quote = Quotes::where('crm_opportunity_id', '=', $this->opportunity->id)->where('tenant_id', '=', $tenant_id)->first();
            if ($this->quote) {
                $this->quoteItems = QuoteItem::where('quote_id', '=', $this->quote->id)->get();

                $sub_total = QuoteItem::select(DB::raw('sum(quote_items.unit_price * quote_items.quantity) as total'))
                    ->where('quote_items.quote_id', '=', $this->quote->id)->first();
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
            $this->crm_leads = CRMLeads::where('id', '=', $this->opportunity->lead_id)->first();
            $this->crm_contacts = CRMContacts::where('lead_id', '=', $this->opportunity->lead_id)->first();
            $this->crm_contact_email = CRMContactDetail::where('contact_id', '=', $this->crm_contacts->id)->where('detail_type', '=', 'Email')->first();
            $this->crm_contact_phone = CRMContactDetail::where('contact_id', '=', $this->crm_contacts->id)->where('detail_type', '=', 'Mobile')->first();     
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
                    //$file_number = intval($fn_ary[3]) + 1;
                }

                if (File::exists(public_path() . '/user-uploads/company-logo/' . $this->companies->logo)) {
                    $this->company_logo_exists = true;
                }

                $filename1 = 'Estimate_' . $this->companies->company_name . '_'  . $this->quote->quote_number . '_' . rand();
                $filename = str_replace(' ', '-', $filename1); // Replaces all spaces with hyphens.                 
                $filename = preg_replace('/[^A-Za-z0-9-]/', '', $filename).'.pdf'; // Removes special chars.                

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
                    'estimate_lower_percent' => $this->estimate_lower_percent,
                    'stripe_connected' => $this->stripe_connected
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

    private function sendSMS($lead_id,$tenant_id,$sms_to,$template_id,$pdf_link,$sms_data){
        $tenant_details = \App\TenantDetail::where('tenant_id', $tenant_id)->first();
        $template = SMSTemplates::where(['id' => $template_id])->first();
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
            $smsbody = $this->setSMSParameter($template->sms_message,$sms_data);

            if($template->attach_quote=='Y'){
                $sms_message = $smsbody."\n".$pdf_link;
            }else{
                $sms_message = $smsbody;
            }

            $sms_total_credits = ceil(strlen($sms_message)/160);

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
    private function setSMSParameter($body, $data)
    {
        $template = $body;
        if (preg_match_all("/{(.*?)}/", $template, $m)) {
            foreach ($m[1] as $i => $varname) {
                $template = str_replace($m[0][$i], sprintf('%s', $data[$varname]), $template);
            }
        }
        return $template;
    }
}
