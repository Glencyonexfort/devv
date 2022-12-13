<?php
namespace App\Http\Controllers;

use App\Companies;
use App\CRMContactDetail;
use App\CRMContacts;
use App\CRMLeads;
use App\CRMOpportunities;
use App\CustomerDetails;
use App\DevLogs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\File;
use Stripe\Stripe;
use App\Invoice;
use App\InvoiceItems;
use App\InvoiceItemsForApproval;
use App\Payment;
use App\InvoiceSetting;
use App\JobsCleaning;
use App\JobsMoving;
use App\JobsMovingLegs;
use App\OrganisationSettings;
use App\Product;
use App\Quotes;
use App\Setting;
use App\StorageUnitAllocation;
use App\Tax;
use App\TenantApiDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;

class StripePaymentController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Payment';
    }

    //START::Pay Now Invoice Payment
    public function payNowInvoice($params)
    {
        $params = explode('&',base64_decode($params));
        $this->invoice_id = (Int)ltrim($params[0],"invoice_id=");
        $this->payment_amount = ltrim($params[1],"payment_amount=");
        $this->invoice = DB::table('invoices')->where(['id' => $this->invoice_id])->first();

        if(!$this->invoice){
            //START:: Dev Logs
                $dev['action'] = 'Invoice not found after Pay Now';
                $dev['log'] = implode(',', $params);
                $dev['created_at'] = Carbon::now();
                DevLogs::create($dev);
            //END:: Dev Logs
        }                

        if(isset($this->invoice->stripe_one_off_customer_id) && !empty($this->invoice->stripe_one_off_customer_id)){
            $stripeCustomerId = 'Y';
        }else{
            $stripeCustomerId = 'N';
        }
        $this->organisation = DB::table('organisation_settings')->where('tenant_id', '=', $this->invoice->tenant_id)->first();
        $this->job = DB::table('jobs_moving')->where(['job_id' => $this->invoice->job_id, 'tenant_id' => $this->invoice->tenant_id])->first();

        if($this->invoice->sys_job_type=="Moving"){
            $this->job = DB::table('jobs_moving')->where(['job_id' => $this->invoice->job_id, 'tenant_id' => $this->invoice->tenant_id])->first();
        }elseif($this->invoice->sys_job_type=="Cleaning"){
            $this->job = DB::table('jobs_cleaning')->where(['job_id' => $this->invoice->job_id, 'tenant_id' => $this->invoice->tenant_id])->first();
        }

        $this->company = DB::table('companies')->where(['id' => $this->job->company_id, 'tenant_id' => $this->invoice->tenant_id])->first();
        $this->lead = DB::table('crm_leads')->where(['id' => $this->job->customer_id, 'tenant_id' => $this->invoice->tenant_id])->first();
        $this->email = CRMContacts::select('crm_contact_details.detail')
                                    ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                                    ->where(['crm_contacts.tenant_id' => $this->invoice->tenant_id, 'crm_contacts.lead_id' => $this->lead->id, 'crm_contact_details.detail_type' => 'Email'])
                                    ->pluck('detail')
                                    ->first();
        $this->mobile = CRMContacts::select('crm_contact_details.detail')
                                    ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                                    ->where(['crm_contacts.tenant_id' => $this->invoice->tenant_id, 'crm_contacts.lead_id' => $this->lead->id, 'crm_contact_details.detail_type' => 'Mobile'])
                                    ->pluck('detail')
                                    ->first(); 
        $this->google_api_key = TenantApiDetail::where(['tenant_id'=> $this->invoice->tenant_id,'provider'=>'GoogleMaps'])->pluck('account_key')->first();

        // if Processing Fee set in invoice setting                                     
        $invoice_setting = InvoiceSetting::where(['tenant_id'=> $this->invoice->tenant_id])->first();  
        if($invoice_setting->cc_processing_fee_percent > 0){
            $this->processing_fee = $this->payment_amount * $invoice_setting->cc_processing_fee_percent/100;  
            $this->processing_fee = number_format((float)$this->processing_fee, 2, '.', '');
        }else{
            $this->processing_fee = 0;
        } 
        //end processing fee

        if (File::exists(public_path() . '/user-uploads/company-logo/' . $this->company->logo)) {
            $this->company_logo_exists = true;
        }else{
            $this->company_logo_exists = false;
        }                                    

        return view('external-stripe-payment',[
            'company_logo_exists'=>$this->company_logo_exists,
            'invoice_id'=>$this->invoice_id,
            'booking_fee'=>0,
            'deposit_required'=>$this->payment_amount,
            'processing_fee'=>$this->processing_fee,
            'organisation'=>$this->organisation,
            'company'=>$this->company,
            'job'=>$this->job,       
            'sys_job_type'=>$this->invoice->sys_job_type,
            'email'=>$this->email,
            'lead'=>$this->lead,
            'mobile'=>$this->mobile,
            'google_api_key'=>$this->google_api_key,
            'stripeCustomerId'=>$stripeCustomerId
        ]); 
    }
    //END::Pay Now Invoice Payment

    //START::Book Now Stripe Payment
    public function payNow($params)
    {
        $params = explode('&',base64_decode($params));
        $this->quote_id = (Int)ltrim($params[0],"quote_id=");
        $this->deposit_required = ltrim($params[1],"deposit_required=");
        $this->quote = DB::table('quotes')->where(['id' => $this->quote_id])->first();

        if(!$this->quote){
            //START:: Dev Logs
                $dev['action'] = 'Quote not found after Book Now';
                $dev['log'] = implode(',', $params);
                $dev['created_at'] = Carbon::now();
                DevLogs::create($dev);
            //END:: Dev Logs
        }
        
        $this->organisation = DB::table('organisation_settings')->where('tenant_id', '=', $this->quote->tenant_id)->first();
        
        if($this->quote->sys_job_type=="Moving"){
            $this->job = DB::table('jobs_moving')->where(['job_id' => $this->quote->job_id, 'tenant_id' => $this->quote->tenant_id])->first();
        }elseif($this->quote->sys_job_type=="Cleaning"){
            $this->job = DB::table('jobs_cleaning')->where(['job_id' => $this->quote->job_id, 'tenant_id' => $this->quote->tenant_id])->first();
        }

        $this->company = DB::table('companies')->where('id', '=', $this->job->company_id)->first();
        $this->lead = DB::table('crm_leads')->where('id', '=', $this->job->customer_id)->first();
        $this->email = CRMContacts::select('crm_contact_details.detail')
                                    ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                                    ->where(['crm_contacts.tenant_id' => $this->quote->tenant_id, 'crm_contacts.lead_id' => $this->lead->id, 'crm_contact_details.detail_type' => 'Email'])
                                    ->pluck('detail')
                                    ->first();
        $this->mobile = CRMContacts::select('crm_contact_details.detail')
                                    ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                                    ->where(['crm_contacts.tenant_id' => $this->quote->tenant_id, 'crm_contacts.lead_id' => $this->lead->id, 'crm_contact_details.detail_type' => 'Mobile'])
                                    ->pluck('detail')
                                    ->first();  
        $this->google_api_key = TenantApiDetail::where(['tenant_id'=> $this->quote->tenant_id,'provider'=>'GoogleMaps'])->pluck('account_key')->first();   
                
        // if Processing Fee set in invoice setting                                     
        $invoice_setting = InvoiceSetting::where(['tenant_id'=> $this->quote->tenant_id])->first();  
        if($invoice_setting->cc_processing_fee_percent > 0){
            $this->processing_fee = $this->deposit_required * $invoice_setting->cc_processing_fee_percent/100;  
            $this->processing_fee = number_format((float)$this->processing_fee, 2, '.', '');
        }else{
            $this->processing_fee = 0;
        } 
        //end processing fee
        
                                    
        if (File::exists(public_path() . '/user-uploads/company-logo/' . $this->company->logo)) {
            $this->company_logo_exists = true;
        }else{
            $this->company_logo_exists = false;
        }                                    

        return view('external-stripe-payment',[
            'company_logo_exists'=>$this->company_logo_exists,
            'invoice_id'=>0,
            'booking_fee'=>0,
            'deposit_required'=>$this->deposit_required,
            'processing_fee'=>$this->processing_fee,
            'organisation'=>$this->organisation,
            'company'=>$this->company,
            'job'=>$this->job,       
            'sys_job_type'=>$this->quote->sys_job_type,
            'email'=>$this->email,
            'lead'=>$this->lead,
            'mobile'=>$this->mobile,
            'google_api_key'=>$this->google_api_key,
            'stripeCustomerId'=>'N'
        ]);
    }
    //END::Book Now Stripe Payment

    //START::Book Now Stripe Payment Booking Fee
    public function payNowBookingFee($params)
    {
        $params = explode('&',base64_decode($params));
        $this->quote_id = ltrim($params[0],"quote_id=");
        $this->deposit_required = ltrim($params[1],"booking_fee=");

        $this->quote = DB::table('quotes')->where(['id' => $this->quote_id])->first();
        $this->organisation = DB::table('organisation_settings')->where('tenant_id', '=', $this->quote->tenant_id)->first();
        
        if($this->quote->sys_job_type=="Moving"){
            $this->job = DB::table('jobs_moving')->where(['job_id' => $this->quote->job_id, 'tenant_id' => $this->quote->tenant_id])->first();
        }elseif($this->quote->sys_job_type=="Cleaning"){
            $this->job = DB::table('jobs_cleaning')->where(['job_id' => $this->quote->job_id, 'tenant_id' => $this->quote->tenant_id])->first();
        }
        
        $this->company = DB::table('companies')->where(['id' => $this->job->company_id, 'tenant_id' => $this->quote->tenant_id])->first();
        $this->lead = DB::table('crm_leads')->where(['id' => $this->job->customer_id, 'tenant_id' => $this->quote->tenant_id])->first();
        $this->email = CRMContacts::select('crm_contact_details.detail')
                                    ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                                    ->where(['crm_contacts.tenant_id' => $this->quote->tenant_id, 'crm_contacts.lead_id' => $this->lead->id, 'crm_contact_details.detail_type' => 'Email'])
                                    ->pluck('detail')
                                    ->first();
        $this->mobile = CRMContacts::select('crm_contact_details.detail')
                                    ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                                    ->where(['crm_contacts.tenant_id' => $this->quote->tenant_id, 'crm_contacts.lead_id' => $this->lead->id, 'crm_contact_details.detail_type' => 'Mobile'])
                                    ->pluck('detail')
                                    ->first();    
        $this->google_api_key = TenantApiDetail::where(['tenant_id'=> $this->quote->tenant_id,'provider'=>'GoogleMaps'])->pluck('account_key')->first();   

        // if Processing Fee set in invoice setting                                     
        $invoice_setting = InvoiceSetting::where(['tenant_id'=> $this->quote->tenant_id])->first();  
        if($invoice_setting->cc_processing_fee_percent > 0){
            $this->processing_fee = $this->deposit_required * $invoice_setting->cc_processing_fee_percent/100;  
            $this->processing_fee = number_format((float)$this->processing_fee, 2, '.', '');
        }else{
            $this->processing_fee = 0;
        } 
        //end processing fee           
        
        
        if (File::exists(public_path() . '/user-uploads/company-logo/' . $this->company->logo)) {
            $this->company_logo_exists = true;
        }else{
            $this->company_logo_exists = false;
        }                                    

        return view('external-stripe-payment',[
            'company_logo_exists'=>$this->company_logo_exists,
            'invoice_id'=>0,
            'booking_fee'=>1,
            'deposit_required'=>$this->deposit_required,
            'processing_fee'=>$this->processing_fee,
            'organisation'=>$this->organisation,
            'company'=>$this->company,
            'job'=>$this->job,       
            'sys_job_type'=>$this->quote->sys_job_type,
            'email'=>$this->email,
            'lead'=>$this->lead,
            'mobile'=>$this->mobile,
            'google_api_key'=>$this->google_api_key,
            'invoice_setting'=>$invoice_setting,
            'stripeCustomerId'=>'N'
        ]);
    }
    //END::Book Now Stripe Payment Booking Fee

    //START::Pay Now Pending Amount
    public function payNowPendingAmount($params)
    {
        $params = explode('&',base64_decode($params));
        $this->invoice_id = ltrim($params[0],"invoice_id=");                

        $this->invoice = Invoice::where(['id' => $this->invoice_id])->first();
        if(isset($this->invoice->stripe_one_off_customer_id) && !empty($this->invoice->stripe_one_off_customer_id)){
            $stripeCustomerId = 'Y';
        }else{
            $stripeCustomerId = 'N';
        }
        $this->new_items = InvoiceItemsForApproval::where('invoice_id', '=', $this->invoice_id)->where(['approved' => 'N', 'tenant_id' => $this->invoice->tenant_id])->get();
        $this->organisation = DB::table('organisation_settings')->where('tenant_id', '=', $this->invoice->tenant_id)->first();
        
        $this->job = DB::table('jobs_moving')->where(['job_id' => $this->invoice->job_id, 'tenant_id' => $this->invoice->tenant_id])->first();

        if($this->invoice->sys_job_type=="Moving"){
            $this->job = DB::table('jobs_moving')->where(['job_id' => $this->invoice->job_id, 'tenant_id' => $this->invoice->tenant_id])->first();
        }elseif($this->invoice->sys_job_type=="Cleaning"){
            $this->job = DB::table('jobs_cleaning')->where(['job_id' => $this->invoice->job_id, 'tenant_id' => $this->invoice->tenant_id])->first();
        }

        $this->company = DB::table('companies')->where(['id' => $this->job->company_id, 'tenant_id' => $this->invoice->tenant_id])->first();
        $this->lead = DB::table('crm_leads')->where(['id' => $this->job->customer_id, 'tenant_id' => $this->invoice->tenant_id])->first();
        $this->email = CRMContacts::select('crm_contact_details.detail')
                                    ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                                    ->where(['crm_contacts.tenant_id' => $this->invoice->tenant_id, 'crm_contacts.lead_id' => $this->lead->id, 'crm_contact_details.detail_type' => 'Email'])
                                    ->pluck('detail')
                                    ->first();
        $this->mobile = CRMContacts::select('crm_contact_details.detail')
                                    ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                                    ->where(['crm_contacts.tenant_id' => $this->invoice->tenant_id, 'crm_contacts.lead_id' => $this->lead->id, 'crm_contact_details.detail_type' => 'Mobile'])
                                    ->pluck('detail')
                                    ->first(); 
        $this->google_api_key = TenantApiDetail::where(['tenant_id'=> $this->invoice->tenant_id,'provider'=>'GoogleMaps'])->pluck('account_key')->first();

        $this->paidAmount = 0;
        $this->totalAmount = 0;
        if (isset($this->invoice->id)) :
            $this->paidAmount = $this->invoice->getPaidAmount();
            $this->totalAmount = $this->invoice->getTotalAmount();
        endif;

        $this->pending_amount = ($this->totalAmount - $this->paidAmount) + $this->invoice->getTotalApprovalItems();

        // if Processing Fee set in invoice setting                                     
        $invoice_setting = InvoiceSetting::where(['tenant_id'=> $this->invoice->tenant_id])->first();  
        if($invoice_setting->cc_processing_fee_percent > 0){
            $this->processing_fee = $this->pending_amount * $invoice_setting->cc_processing_fee_percent/100;  
            $this->processing_fee = number_format((float)$this->processing_fee, 2, '.', '');
        }else{
            $this->processing_fee = 0;
        } 
        //end processing fee

        if (File::exists(public_path() . '/user-uploads/company-logo/' . $this->company->logo)) {
            $this->company_logo_exists = true;
        }else{
            $this->company_logo_exists = false;
        }                                    

        return view('external-stripe-payment-for-approval',[
            'company_logo_exists'=>$this->company_logo_exists,
            'invoice_id'=>$this->invoice_id,
            'booking_fee'=>0,
            'approval_page'=>1,
            'deposit_required'=>$this->pending_amount,
            'pending_amount'=>$this->pending_amount,
            'processing_fee'=>$this->processing_fee,
            'organisation'=>$this->organisation,
            'company'=>$this->company,
            'job'=>$this->job,       
            'sys_job_type'=>$this->invoice->sys_job_type,
            'email'=>$this->email,
            'lead'=>$this->lead,
            'mobile'=>$this->mobile,
            'google_api_key'=>$this->google_api_key,
            'totalAmount'=>$this->totalAmount,
            'paidAmount'=>$this->paidAmount,
            'new_items'=>$this->new_items,
            'stripeCustomerId'=>$stripeCustomerId
        ]); 
    }
    //END::Pay Now Pending Amount
    
    public function paymentCharge()
    {
        $job_id  = Request::input('job_id');
        $sys_job_type  = Request::input('sys_job_type');
        $invoice_id  = Request::input('invoice_id');
        $booking_fee  = Request::input('booking_fee');
        $tenant_id  = Request::input('tenant_id');
        $deposit_required = round(Request::input('deposit_required'),2);
        $processing_fee = Request::input('processing_fee');

        if($sys_job_type=="Moving"){
            $pickup_address  = Request::input('pickup_address');
            $drop_off_address  = Request::input('drop_off_address');
        }
        $quote = Quotes::where(['job_id'=> $job_id, 'sys_job_type'=>$sys_job_type, 'tenant_id' => $tenant_id])->first();
        
        $autoQuoting = DB::table('jobs_moving_auto_quoting as t1')
            ->where(['t1.tenant_id' => $tenant_id])
            ->first();
            
        // Stripe API secret key
        $secret_key = env('STRIPE_SECRET');
        Stripe::setApiKey($secret_key);

        $tenant_api_details = TenantApiDetail::where(['tenant_id'=> $tenant_id, 'provider'=>'Stripe'])->first();
        $invoice_setting = InvoiceSetting::where(['tenant_id'=> $tenant_id])->first();
        if(!$tenant_api_details){
            $response = array(
                'status' => 0,
                'msg' => 'Stripe account is not connected!'
            );
            return json_encode($response);
        }
        //-----------------
        if($invoice_id==0){
            $invoice = Invoice::where(['job_id'=> $job_id,'sys_job_type'=>$sys_job_type, 'tenant_id' => $tenant_id])->first();
        }else{
            $invoice = Invoice::where(['id' => $invoice_id, 'tenant_id' => $tenant_id])->first();           
        }        

        $response = array();
        // Check whether stripe token is not empty
        if(!empty($_POST['stripeToken']) || $_POST['stripeCustomerId']=='Y'){
            if(isset($invoice->stripe_one_off_customer_id) && !empty($invoice->stripe_one_off_customer_id)){
                $stripeCustomerId = $invoice->stripe_one_off_customer_id;
                $old_customer=1;
            }else{
                // Get token, card and item info
                $token  = Request::input('stripeToken');
                $email  = Request::input('stripeEmail');
                try {
                    // Add customer to stripe
                    $customer = \Stripe\Customer::create(array(
                        'email' => $email,
                        'source'  => $token
                    ),['stripe_account' => $tenant_api_details->variable1]);
                    $stripeCustomerId = $customer->id;
                }catch(\Stripe\Exception\CardException $e){
                    $response = array(
                        'status' => 0,
                        'msg' => $e->getMessage()
                    );
                    return json_encode($response);
                }
                $old_customer=0;
            }
            try{
                if($invoice_setting->stripe_pre_authorise=='Y'){
                    // Authoriized charge amount
                    $charge = \Stripe\Charge::create(array(
                        'customer' => $stripeCustomerId,
                        'amount'   => $deposit_required * 100,
                        'currency' => 'AUD',
                        'capture' => false,
                        'description' => 'Amount deposit for job number '.Request::input('jobNumber'),
                    ),['stripe_account' => $tenant_api_details->variable1]);
                }else{
                    // Charge a credit or a debit card
                    $charge = \Stripe\Charge::create(array(
                        'customer' => $stripeCustomerId,
                        'amount'   => $deposit_required * 100,
                        'currency' => 'AUD',
                        //'source'  => $token,
                        'description' => 'Amount deposit for job number '.Request::input('jobNumber'),
                    ),['stripe_account' => $tenant_api_details->variable1]);
                }
            }catch(\Stripe\Exception\CardException $e){
                $response = array(
                    'status' => 0,
                    'msg' => $e->getMessage()
                );
                return json_encode($response);
            }            
            // Retrieve charge details
            $chargeJson = $charge->jsonSerialize();
            // Check whether the charge is successful
            if($chargeJson['amount_refunded'] == 0 && empty($chargeJson['failure_code']) && $chargeJson['paid'] == 1){                
                // Order details 
                $amount = $chargeJson['amount'];
                $currency = $chargeJson['currency'];
                $txnID = $chargeJson['balance_transaction'];
                $status = $chargeJson['status'];
                $transactionID = $chargeJson['id'];
                $payerName = $chargeJson['source']['name'];
                                            
                // If payment succeeded
                if($status == 'succeeded'){
                    // Old Invoice Payment 
                    if($invoice_id!=0 && $booking_fee==0){
                        if($invoice_setting->cc_processing_fee_percent > 0){
                            $processing_item = Product::where('id','=',$invoice_setting->cc_processing_product_id)->first();
                            if($processing_item){
                                $p_deposit_required = $processing_fee;
                                $obj_item = new InvoiceItems();
                                $obj_item->tenant_id = $tenant_id;
                                $obj_item->invoice_id = $invoice->id;
                                $obj_item->product_id = $processing_item->id;
                                $obj_item->item_name = $processing_item->name;
                                $obj_item->item_summary = '';
                                $obj_item->type = $processing_item->product_type;
                                $obj_item->quantity = 1;
                                $obj_item->unit_price = $p_deposit_required;
                                $obj_item->amount = ($obj_item->unit_price * $obj_item->quantity);
                                $obj_item->save();
                                unset($obj_item);
                            }
                        }

                        //Add Invoice Payment
                        $payment = new Payment();
                        $payment->tenant_id = $tenant_id;
                        $payment->invoice_id = $invoice->id;
                        $payment->gateway = 'Stripe';
                        $payment->transaction_id = $transactionID;
                        $payment->remarks = 'Payment for invoice '.$invoice->invoice_number;
                        $payment->amount = $deposit_required;
                        $payment->paid_on = Carbon::now();
                        $payment->created_at = Carbon::now();
                        $payment->save();
                        // 
                    }else{  
                        //New Invoice Payment with Booking fee invoice
                            $tenant_api_details = TenantApiDetail::where(['tenant_id' => $tenant_id, 'provider' => 'Xero'])->first();                                      
                            if($booking_fee==1){
                                $binvoice = new Invoice();
                                $binvoice->tenant_id = $tenant_id;
                                $binvoice->job_id = 0;
                                $binvoice->invoice_number = 0;
                                $binvoice->sys_job_type = $quote->sys_job_type;
                                $binvoice->project_id = 1;
                                $binvoice->issue_date = date('Y-m-d');
                                $binvoice->due_date = date('Y-m-d');  
                                $binvoice->note = 'Booking Fee';
                                $binvoice->status='paid';
                                $b_deposit_required = $deposit_required;
                                $binvoice->save();
                                //Booking Fee Line Item
                                $booking_item = new InvoiceItems();
                                $booking_item->tenant_id = $tenant_id;
                                $booking_item->invoice_id = $binvoice->id;
                                $booking_item->item_name = 'Booking Fee for Job number:'.$quote->quote_number;
                                $booking_item->item_summary = '';
                                $booking_item->quantity = 1;
                                $booking_item->unit_price = $b_deposit_required;
                                $booking_item->amount = $b_deposit_required;
                                $booking_item->save();
                                unset($booking_item);
                            }                                                                                 
                            if(!$invoice){
                                //START:: If Invoice not already created with Confirm booking Button
                                $invoice = new Invoice();
                                $invoice->tenant_id = $tenant_id;
                                $invoice->job_id = $job_id;
                                $invoice->invoice_number = $quote->quote_number;
                                $invoice->sys_job_type = $quote->sys_job_type;
                                $invoice->discount_type = $quote->discount_type;
                                $invoice->discount = $quote->discount;
                                $invoice->project_id = 1;
                                $current_date = date('Y-m-d');
                                $invoice->issue_date = $current_date;
                                $due_after = 15;
                                if ($invoice_setting) {
                                    $due_after = $invoice_setting->due_after;
                                }
                                $invoice->due_date = date('Y-m-d', strtotime($current_date . ' + ' . $due_after . ' days'));   
                                $invoice->save();
                                
                                //Saving Invoice items                
                                if($quote){
                                    $quoteItem = DB::table('quote_items')
                                        ->where(['quote_id' => $quote->id])
                                        ->get();
                                    foreach($quoteItem as $q){
                                        $obj_item = new InvoiceItems();
                                        $obj_item->tenant_id = $tenant_id;
                                        $obj_item->invoice_id = $invoice->id;
                                        $obj_item->product_id = $q->product_id;
                                        $obj_item->item_name = $q->name;
                                        $obj_item->item_summary = $q->description;
                                        $obj_item->type = $q->type;
                                        $obj_item->tax_id = $q->tax_id;
                                        $obj_item->quantity = $q->quantity;
                                        $obj_item->unit_price = $q->unit_price;
                                        $obj_item->amount = $q->amount;
                                        $obj_item->save();
                                        unset($obj_item);
                                    }                                                        
                                }
                                //END:: If Invoice not already created with Confirm booking Button
                            }

                            //Add processing fee line item
                            if($invoice_setting->cc_processing_fee_percent > 0){
                                $processing_item = Product::where(['id' => $invoice_setting->cc_processing_product_id, 'tenant_id' => $tenant_id])->first();
                                if($processing_item){
                                    $p_deposit_required = $processing_fee;
                                    $obj_item = new InvoiceItems();
                                    $obj_item->tenant_id = $tenant_id;
                                    $obj_item->invoice_id = $invoice->id;
                                    $obj_item->product_id = $processing_item->id;
                                    $obj_item->item_name = $processing_item->name;
                                    $obj_item->item_summary = '';
                                    $obj_item->type = $processing_item->product_type;
                                    $obj_item->quantity = 1;
                                    $obj_item->unit_price = $p_deposit_required;
                                    $obj_item->amount = ($obj_item->unit_price * $obj_item->quantity);
                                    $obj_item->save();
                                    unset($obj_item);
                                }
                            }
                        //-->
            
                            //Add Invoice Payment
                            $payment = new Payment();
                            $payment->tenant_id = $tenant_id;                                
                            if($booking_fee==1){
                                $payment->invoice_id = $binvoice->id;
                                $payment->remarks = 'Booking Fee Payment';
                            }else{
                                if($invoice_setting->stripe_pre_authorise=='Y'){
                                    $payment->status = 'pending';
                                    $payment->transaction_id = $transactionID;
                                }
                                $payment->invoice_id = $invoice->id;
                                $payment->remarks = 'Job confirmation payment';
                            }
                            $payment->gateway = 'Stripe';                            
                            $payment->amount = $deposit_required;
                            $payment->paid_on = Carbon::now();
                            $payment->created_at = Carbon::now();
                            $payment->save();
                            //
                            $this->invoice = $invoice;       
                            ///Generating Invoice PDF
                            $pdf_url = $this->generateInvoicePDF($job_id,$this->invoice->id,$sys_job_type,$tenant_id);  
            
                    }
                        $totalAmount = InvoiceItems::where(['invoice_id' => $invoice->id, 'tenant_id' => $tenant_id])->sum('amount');
                        $paidAmount = Payment::where('invoice_id', $invoice->id)->where(['status' => 'complete', 'tenant_id' => $tenant_id])->sum('amount');  
                        if($paidAmount<$totalAmount && $paidAmount>0){
                            $invoice->status='partial';
                        }elseif($paidAmount==$totalAmount){
                            $invoice->status='paid';
                        }else{
                            $invoice->status = 'unpaid';
                        }
                        if($old_customer==0){
                            $invoice->stripe_one_off_customer_id = $stripeCustomerId;
                        }
                        //Update Invoice Status
                        $invoice->save();

                    //START:: Add a defailt Job Leg
                if($quote->sys_job_type=="Moving"){
                    if($invoice_setting->stripe_pre_authorise=='Y'){ 
                        //--Update Job Satatus
                        JobsMoving::where(['job_id'=>$job_id,'tenant_id'=>$tenant_id])
                        ->update([
                            'pickup_address'=>$pickup_address,
                            'drop_off_address'=>$drop_off_address
                            ]);
                            $job = JobsMoving::where(['tenant_id' => $tenant_id, 'job_id' => $job_id])->first();
                    }else{
                        //--Update Job Satatus
                        if($invoice_id==0){
                            JobsMoving::where(['job_id'=>$job_id,'tenant_id'=>$tenant_id])
                            ->update([
                                'opportunity'=>'N',
                                'job_status'=>'New',
                                'pickup_address'=>$pickup_address,
                                'drop_off_address'=>$drop_off_address
                                ]);  
                        }else{
                            JobsMoving::where(['job_id'=>$job_id,'tenant_id'=>$tenant_id])
                            ->update([
                                'pickup_address'=>$pickup_address,
                                'drop_off_address'=>$drop_off_address
                                ]);
                        }            
                        $job = JobsMoving::where(['tenant_id' => $tenant_id, 'job_id' => $job_id])->first();

                        //Finding deo locations
                        $api_key = TenantApiDetail::where(['tenant_id' => $tenant_id, 'provider' => 'GoogleMaps'])->pluck('account_key')->first();
                        $pickup_address = $job->pickup_address; //
                        $drop_off_address = $job->drop_off_address; //

                        $pickup_geo_location = JobsMovingLegs::getGeoLocation($api_key, $pickup_address);
                        $drop_off_geo_location = JobsMovingLegs::getGeoLocation($api_key, $drop_off_address);
                        //end--->
                        $jobLeg = JobsMovingLegs::where(['job_id' => $job_id, 'tenant_id' => $tenant_id])->first();
                        if(!$jobLeg){
                            $job_leg = new JobsMovingLegs();
                            $job_leg->job_id = $job->job_id;
                            $job_leg->tenant_id = $tenant_id;
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

                        //Update Moving Storage Unit Status
                        $storage_unit = StorageUnitAllocation::where(['job_id'=>$job->job_id,'job_type'=>'Moving','allocation_status'=>'Reserved','deleted'=>'0'])
                                        ->update(['allocation_status'=>'Occupied']);
                        //end
                    }
                }elseif($quote->sys_job_type=="Cleaning"){
                    if($invoice_setting->stripe_pre_authorise=='N'){ 
                        $job = JobsCleaning::where(['tenant_id' => $tenant_id, 'job_id' => $job_id])->first();
                        //--Update Job Satatus
                        JobsCleaning::where(['job_id'=>$job_id,'tenant_id'=>$tenant_id])
                                    ->update([
                                        'opportunity'=>'N',
                                        'job_status'=>'New',
                                        ]);
                    }
                }
                    //END:: Add a defailt Job Leg
                    if($invoice_setting->stripe_pre_authorise=='Y'){ 
                        //Update Opportunity Status
                            CRMOpportunities::where(['id' => $job->crm_opportunity_id, 'tenant_id' => $tenant_id])->update(['op_status'=>$invoice_setting->stripe_pre_authorised_op_status]);
                        //Update Opportunity Status                 
                    }else{
                        //START:: Update Lead & Opportunity Status
                        CRMOpportunities::where(['id' => $job->crm_opportunity_id, 'tenant_id' => $tenant_id])->update(['op_status'=>'Confirmed']);
                        CRMLeads::where(['id' => $job->customer_id, 'tenant_id' => $tenant_id])->update(['lead_status'=>'Customer']);
                        //END:: Update Lead & Opportunity Status        
                    }                        
                        //----
                    $response = array(
                        'status' => 1,
                        'is_redirect' => 0,
                        'msg' => 'Your payment was successful.',
                        'txnData' => $chargeJson
                    );
                }else{
                    $response = array(
                        'status' => 0,
                        'msg' => 'Transaction has been failed.'
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'msg' => 'Transaction has been failed.'
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'msg' => 'Form submission error...'
            );
        }
        if($autoQuoting){
        if($autoQuoting->redirect_to_inven_form_after_quote_payment=='Y'){
            $external_inventory_form_param = base64_encode('tenant_id='.$tenant_id.'&job_id='.$job_id);
            $external_inventory_form = request()->getSchemeAndHttpHost().'/removals-inventory-form/'.$external_inventory_form_param;
            $response = array(
                'status' => 1,
                'is_redirect' => 1,
                'inventory_form_url'=>$external_inventory_form
            );
            echo json_encode($response);exit;
        }
    }
        echo json_encode($response);
    }
    // START:: Payment for Invoice Item Approval

    public function paymentChargeApproval()
    {    
        $job_id  = Request::input('job_id');
        $sys_job_type  = Request::input('sys_job_type');
        $invoice_id  = Request::input('invoice_id');
        $tenant_id  = Request::input('tenant_id');
        $deposit_required = Request::input('deposit_required');
        $processing_fee = Request::input('processing_fee');
                
        $autoQuoting = DB::table('jobs_moving_auto_quoting as t1')
            ->where(['t1.tenant_id' => $tenant_id])
            ->first();
            
        // Stripe API secret key
        $secret_key = env('STRIPE_SECRET');
        Stripe::setApiKey($secret_key);

        $tenant_api_details = TenantApiDetail::where(['tenant_id'=> $tenant_id, 'provider'=>'Stripe'])->first();
        $invoice_setting = InvoiceSetting::where(['tenant_id'=> $tenant_id])->first();
        if(!$tenant_api_details){
            $response = array(
                'status' => 0,
                'msg' => 'Stripe account is not connected!'
            );
            return json_encode($response);
        }
        //-----------------
        $invoice = Invoice::where('id', '=', $invoice_id)->first();
        $new_items = InvoiceItemsForApproval::where(['invoice_id'=> $invoice_id, 'approved'=>'N'])->get();        

        $response = array();
        // Check whether stripe token is not empty
        if(!empty($_POST['stripeToken'])){
            
            // Get token, card and item info
            $token  = Request::input('stripeToken');
            $email  = Request::input('stripeEmail');
            
            if(isset($invoice->stripe_one_off_customer_id) && !empty($invoice->stripe_one_off_customer_id)){
                $stripeCustomerId = $invoice->stripe_one_off_customer_id;
                $old_customer=1;
            }else{
                try {
                    // Add customer to stripe
                    $customer = \Stripe\Customer::create(array(
                        'email' => $email,
                        'source'  => $token
                    ),['stripe_account' => $tenant_api_details->variable1]);
                    $stripeCustomerId = $customer->id;
                }catch(\Stripe\Error\OAuth\OAuthBase $e){
                    $response = array(
                        'status' => 0,
                        'msg' => $e->getMessage()
                    );
                    return json_encode($response);
                }
                $old_customer=0;
            }
            try{
                // Charge a credit or a debit card
                $charge = \Stripe\Charge::create(array(
                    'customer' => $stripeCustomerId,
                    'amount'   => $deposit_required * 100,
                    'currency' => 'AUD',
                    //'source'  => $token,
                    'description' => 'Amount deposit for job number '.Request::input('jobNumber'),
                ),['stripe_account' => $tenant_api_details->variable1]);

            }catch(\Stripe\Error\OAuth\OAuthBase $e){
                $response = array(
                    'status' => 0,
                    'msg' => $e->getMessage()
                );
                return json_encode($response);
            }            
            // Retrieve charge details
            $chargeJson = $charge->jsonSerialize();

            // Check whether the charge is successful
            if($chargeJson['amount_refunded'] == 0 && empty($chargeJson['failure_code']) && $chargeJson['paid'] == 1 && $chargeJson['captured'] == 1){                
                // Order details 
                $amount = $chargeJson['amount'];
                $currency = $chargeJson['currency'];
                $txnID = $chargeJson['balance_transaction'];
                $status = $chargeJson['status'];
                $transactionID = $chargeJson['id'];
                $payerName = $chargeJson['source']['name'];
                                            
                // If payment succeeded
                if($status == 'succeeded'){
                        // Add Processing fee line item
                        if($invoice_setting->cc_processing_fee_percent > 0){
                            $processing_item = Product::where('id','=',$invoice_setting->cc_processing_product_id)->first();
                            if($processing_item){
                                $p_deposit_required = $processing_fee;
                                $obj_item = new InvoiceItems();
                                $obj_item->tenant_id = $tenant_id;
                                $obj_item->invoice_id = $invoice->id;
                                $obj_item->product_id = $processing_item->id;
                                $obj_item->item_name = $processing_item->name;
                                $obj_item->item_summary = '';
                                $obj_item->type = $processing_item->product_type;
                                $obj_item->quantity = 1;
                                $obj_item->unit_price = $p_deposit_required;
                                $obj_item->amount = ($obj_item->unit_price * $obj_item->quantity);
                                $obj_item->save();
                                unset($obj_item);
                            }
                        }
                        //Copying New Items to original invoice
                        if($new_items){
                            foreach($new_items as $q){
                                $obj_item = new InvoiceItems();
                                $obj_item->tenant_id = $tenant_id;
                                $obj_item->invoice_id = $invoice->id;
                                $obj_item->product_id = $q->product_id;
                                $obj_item->item_name = $q->item_name;
                                $obj_item->item_summary = $q->item_summary;
                                $obj_item->type = $q->type;
                                $obj_item->quantity = $q->quantity;
                                $obj_item->unit_price = $q->unit_price;
                                $obj_item->amount = $q->amount;
                                $obj_item->tax_id = $q->tax_id;
                                $obj_item->save();
                                unset($obj_item);

                                // update approved items to Y
                                InvoiceItemsForApproval::where(['id'=>$q->id])
                                    ->update([
                                        'approved'=>'Y'
                                    ]);
                                // -->
                            }                        
                        }

                        //Add Invoice Payment
                        $payment = new Payment();
                        $payment->tenant_id = $tenant_id;
                        $payment->invoice_id = $invoice->id;
                        $payment->gateway = 'Stripe';
                        $payment->transaction_id = $transactionID;
                        $payment->remarks = 'Invoice item approval payment';
                        $payment->amount = $deposit_required;
                        $payment->paid_on = Carbon::now();
                        $payment->created_at = Carbon::now();
                        $payment->save();
                        //                                                                                  

                        $this->invoice = $invoice;       
                        ///Generating Invoice PDF
                        $this->generateInvoicePDF($job_id,$this->invoice->id,$sys_job_type,$tenant_id);
                            
            
                    }                                      
                    //----
                    $response = array(
                        'status' => 1,
                        'is_redirect' => 0,
                        'msg' => 'Your payment was successful.',
                        'txnData' => $chargeJson
                    );
                }else{
                    $response = array(
                        'status' => 0,
                        'msg' => 'Transaction has been failed.'
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'msg' => 'StripeToken invalid, Transaction has been failed.'
                );
            }
        if($autoQuoting){
            if($autoQuoting->redirect_to_inven_form_after_quote_payment=='Y'){
                $external_inventory_form_param = base64_encode('tenant_id='.$tenant_id.'&job_id='.$job_id);
                $external_inventory_form = request()->getSchemeAndHttpHost().'/removals-inventory-form/'.$external_inventory_form_param;
                $response = array(
                    'status' => 1,
                    'is_redirect' => 1,
                    'inventory_form_url'=>$external_inventory_form
                );
                echo json_encode($response);exit;
            }
        }
    echo json_encode($response);
    }

    public function generateInvoicePDF($job_id,$invoice_id,$type,$tenant_id)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '3000M');

            $this->sub_total = 0;
            $this->grand_total = 0;
            $this->tax_total = 0;
            $this->total_paid = 0;
            $this->balance_payment = 0;
            $this->count = 0;
            $this->stripe_connected=0;
            $this->invoice_settings = InvoiceSetting::where('tenant_id', $tenant_id)->first();

            $stripe = TenantApiDetail::where('tenant_id', $tenant_id)
                    ->where('provider', 'Stripe')->first();
            if($stripe){
                if(isset($stripe->account_key) && !empty($stripe->account_key)){
                    $this->stripe_connected=1;
                }
            }

            $this->invoice = Invoice::where(['id'=> $invoice_id,'sys_job_type'=>$type])
                    ->where('tenant_id', '=', $tenant_id)
                    ->first();
                    

            $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', $tenant_id)->first();         

            $this->taxs = Tax::select('taxes.*')
                ->where(['taxes.tenant_id' => $tenant_id, 'invoice_items.invoice_id'=>$this->invoice->id])
                ->whereNotNull('invoice_items.tax_id')
                ->join('invoice_items', 'invoice_items.tax_id', '=', 'taxes.id')->first();

            $sub_total = InvoiceItems::select(DB::raw('sum(invoice_items.unit_price * invoice_items.quantity) as total'))
                ->where('invoice_items.invoice_id', '=', $invoice_id)->first();
            $this->sub_total = $sub_total->total;            

            $total_paid = Payment::select(DB::raw('sum(payments.amount) as total'))
                ->where('payments.invoice_id', '=', $invoice_id)->first();
            $this->total_paid = $total_paid->total;    
            if($this->invoice->sys_job_type=="Moving"){
                $this->job = JobsMoving::where('job_id', '=', $job_id)->where('tenant_id', '=', $tenant_id)->first();

            }elseif($this->invoice->sys_job_type=="Cleaning"){                
                $this->job = JobsCleaning::where('job_id', '=', $job_id)->where('tenant_id', '=', $tenant_id)->first();
            }
            $this->companies = Companies::where('id', '=', $this->job->company_id)->first();
            $this->crm_leads = CRMLeads::where('id', '=', $this->job->customer_id)->first();
            $this->customer_detail = CustomerDetails::where('customer_id', '=', $this->job->customer_id)->first();
            $this->crm_contacts = CRMContacts::where('lead_id', '=', $this->job->customer_id)->first();
            $this->crm_contact_email = CRMContactDetail::where('contact_id', '=', $this->crm_contacts->id)->where('detail_type', '=', 'Email')->first();
            $this->crm_contact_phone = CRMContactDetail::where('contact_id', '=', $this->crm_contacts->id)->where('detail_type', '=', 'Mobile')->first();
            $this->invoice_items = InvoiceItems::where('invoice_id', '=', $this->invoice->id)->where('tenant_id', '=', $tenant_id)->get();
            $this->company_logo_exists = false;

            // $this->settings = Setting::findOrFail(1);
            // $this->invoiceSetting = InvoiceSetting::first();
            //Calculating Total Values
            if($this->invoice->discount>0){
                if($this->invoice->discount_type=="percent"){
                    $this->sub_total_after_discount = $this->sub_total - ($this->invoice->discount/100 * $this->sub_total);
                }else{
                    $this->sub_total_after_discount = $this->sub_total - $this->invoice->discount;
                }
            }else{
                $this->sub_total_after_discount = $this->sub_total;
            }
            if($this->taxs){
                $this->tax_total = ($this->taxs->rate_percent * $this->sub_total_after_discount)/100;
            }else{
                $this->tax_total=0;
            }
            $this->invoice_total = $this->tax_total + $this->sub_total_after_discount; 
            $this->balance_payment = $this->invoice_total - $this->total_paid;
            //END:: Calculation values

            $this->url_params = base64_encode('invoice_id=' . $this->invoice->id . '&payment_amount=' . $this->balance_payment);
            $this->url_link = request()->getSchemeAndHttpHost() . '/pay-now-inv/' . $this->url_params;

            //return view('admin.list-jobs.invoice', $this->data);

            $file_number = 1;
            if (!empty($this->invoice->file_original_name)) {
                $filename = str_replace('.pdf', '', $this->invoice->file_original_name);
                $fn_ary = explode('_', $filename);
                $file_number = intval($fn_ary[2]) + 1;
            }

            $filename = 'Invoice_Job_' . $this->invoice->invoice_number . '_' . rand() . '.pdf';
            
            if($this->companies){
                if (File::exists(public_path() . '/user-uploads/app-logo/' . $this->companies->logo)) {
                    $this->company_logo_exists = true;
                }
            }
            
            $pdf = app('dompdf.wrapper');
            $html = view('admin.list-jobs.invoice', [
                'global'=>$this->global,
                'organisation_settings'=>$this->organisation_settings,
                'companies'=>$this->companies,
                'invoice'=>$this->invoice,
                // 'invoiceSetting'=>$this->invoiceSetting,
                // 'settings'=>$this->settings,
                'invoice_settings'=>$this->invoice_settings,
                'company_logo_exists'=>$this->company_logo_exists,
                'invoice_items'=>$this->invoice_items,
                'count'=>0,
                'crm_contact_phone'=>$this->crm_contact_phone,
                'crm_contact_email'=>$this->crm_contact_email,
                'crm_contacts'=>$this->crm_contacts,
                'crm_leads'=>$this->crm_leads,
                'customer_detail'=>$this->customer_detail,
                'job'=>$this->job,
                'taxs'=>$this->taxs,                                    
                'tax_total'=>$this->tax_total,
                'sub_total_after_discount'=>$this->sub_total_after_discount,
                'invoice_total'=>$this->invoice_total,
                'sub_total'=>$this->sub_total,
                'total_paid'=>$this->total_paid,
                'balance_payment'=>$this->balance_payment,
                'url_link'=>$this->url_link,
                'stripe_connected' => $this->stripe_connected,
                'is_storage_invoice' => 0
            ]);

            $pdf->loadHtml($html, 'UTF-8');
            $pdf->getDomPDF()->set_option("enable_php", true);
            // return $pdf->stream(); // to view pdf
            // return $pdf->download('tmp.pdf');
            $pdf->save('invoice-files/' . $filename);

            if (File::exists(public_path() . '/invoice-files/' . $this->invoice->file_original_name)) {
                File::delete(public_path() . '/invoice-files/' . $this->invoice->file_original_name);
            }
            $this->invoice->file_original_name = $filename;
            $this->invoice->save();
            return $filename;
    }
}