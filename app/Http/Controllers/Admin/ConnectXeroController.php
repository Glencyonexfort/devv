<?php

namespace App\Http\Controllers\Admin;

use App\CRMContactDetail;
use App\CRMContacts;
use App\CRMLeads;
use App\Product;
use Illuminate\Http\Request;
use LangleyFoxall\XeroLaravel\OAuth2;
use League\OAuth2\Client\Token\AccessToken;
use App\TenantApiDetail;
use Illuminate\Support\Facades\DB;
use LangleyFoxall\XeroLaravel\XeroApp;
use XeroPHP\Models\Accounting\Payment;
use XeroPHP\Models\Accounting\Contact;
use XeroPHP\Models\Accounting\Invoice;
use XeroPHP\Models\Accounting\Invoice\LineItem;
use XeroPHP\Models\Accounting\Phone;

class ConnectXeroController extends AdminBaseController
{
    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('app.menu.connectXero');
        $this->pageIcon = 'icon-loop';        
    }

    private function getOAuth2()
    {
        // This will use the 'default' app configuration found in your 'config/xero-laravel-lf.php` file.
        // If you wish to use an alternative app configuration you can specify its key (e.g. `new OAuth2('other_app')`).
        return new OAuth2();
    }

    public function redirectUserToXero()
    {
        // Step 1 - Redirect the user to the Xero authorization URL.
        return $this->getOAuth2()->getAuthorizationRedirect();
    }

    public function handleCallbackFromXero(Request $request)
    {
        // Step 2 - Capture the response from Xero, and obtain an access token.
        $accessToken = $this->getOAuth2()->getAccessTokenFromXeroRequest($request);
        
        // Step 3 - Retrieve the list of tenants (typically Xero organisations), and let the user select one.
        $tenants = $this->getOAuth2()->getTenants($accessToken);
        $selectedTenant = $tenants[0]; // For example purposes, we're pretending the user selected the first tenant.

        // Step 4 - Store the access token and selected tenant ID against the user's account for future use.
        // You can store these anyway you wish. For this example, we're storing them in the database using Eloquent.
        $this->tenant_api_details = new TenantApiDetail;
        $this->tenant_api_details->tenant_id = auth()->user()->tenant_id;
        $this->tenant_api_details->smtp_user = $selectedTenant->tenantId;
        $this->tenant_api_details->provider = 'Xero';
        $this->tenant_api_details->variable1 = json_encode($accessToken);        
        $this->tenant_api_details->save();

        return $this->index();
    }

    public function refreshAccessTokenIfNecessary($token)
    {
        // Step 5 - Before using the access token, check if it has expired and refresh it if necessary.                      
            $accessToken = new AccessToken($token);
            //if ($token['expires']<time()) {
                $accessToken = $this->getOAuth2()->refreshAccessToken($accessToken);
                $tenant_api_details = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'Xero'])->first();
                $tenant_api_details->variable1 = json_encode($accessToken);
                $tenant_api_details->save();
            //}
        
    }

    public function index()
    {
        try{
            $this->myob_connected = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'MYOB'])->first();            
            if($this->myob_connected){ 
                $this->tenant_api_details=false;
                $this->invoices=array();
                return view('admin.connect-xero.index', $this->data);
            }
            $this->tenant_api_details = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'Xero'])->first();            
            if($this->tenant_api_details){                
                $token = (array)json_decode($this->tenant_api_details->variable1);   
                $this->refreshAccessTokenIfNecessary($token);
                $this->tenant_api_details = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'Xero'])->first();            
                $token = (array)json_decode($this->tenant_api_details->variable1);

                $this->xero = new XeroApp(
                    new AccessToken($token),
                    $this->tenant_api_details->smtp_user
                );
                $this->accounts = $this->xero->accounts()->where('Class','REVENUE')->get();
                $this->payment_accounts = $this->xero->accounts()
                    //->where('EnablePaymentsToAccount',TRUE)
                    //->where('Type','EQUITY')
                    ->where('Type','BANK')
                    ->get();
                //  echo '<pre>';
                //  print_r($this->accounts);exit;
                $this->invoices = \App\Invoice::where('sync_with_xero', '=', 'Y')
                    ->join('jobs_moving', 'jobs_moving.job_id', '=', 'invoices.job_id')
                    ->where('invoices.tenant_id', '=', auth()->user()->tenant_id)
                    ->select('invoices.*','jobs_moving.job_number','jobs_moving.job_type','jobs_moving.customer_id')
                    ->get();
            }else{
                $this->invoices=array();
            }
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
        return view('admin.connect-xero.index', $this->data);
    }

    public function disconnect()
    {
        try{
            $tenant_api_details = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'Xero'])->delete();                        
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
        return $this->index();
    }
    public function storeConfig(Request $request)
    {
        try{
            $tenant_api_details = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'Xero'])
            ->update(['account_key'=>$request->account_id, 'smtp_secret'=>$request->payment_account_id]);                        
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
        $response['error'] = 0;
        $response['message'] = 'Configuration has been saved';        
        return json_encode($response);
    }   

    public function syncInvoice(){

        ini_set('max_execution_time', 0);
    
        $tenants = DB::table('tenant_api_details as t1')            
            ->select('t1.*')
            ->where(['t1.provider' => 'Xero'])
            ->get();
        foreach($tenants as $tenant){
            //if Account configuration didn't saved
            if(!isset($tenant->smtp_secret) || !isset($tenant->account_key)){
                continue;
            }
            //Refresh Access Token
            $token = (array)json_decode($tenant->variable1);
            $this->refreshAccessTokenIfNecessary($token);
            $tenant = TenantApiDetail::where(['tenant_id' => $tenant->tenant_id, 'provider' => 'Xero'])->first();            
            $token = (array)json_decode($tenant->variable1);
            //----
            $invoices = \App\Invoice::where(['sync_with_xero'=>'Y','tenant_id'=>$tenant->tenant_id])->get();
            foreach($invoices as $invoice){
                $invoiceItems = \App\InvoiceItems::where('invoice_id', '=', $invoice->id)->get();
                $invoicePayments = \App\Payment::where('invoice_id', '=', $invoice->id)->whereNull('xero_id')->get();
                $paidPayments = \App\Payment::where('invoice_id', '=', $invoice->id)->whereNotNull('xero_id')->get();
                if($invoice->sys_job_type=="Moving"){
                    $job = \App\JobsMoving::where('job_id', '=', $invoice->job_id)->first();
                }else{
                    $job = \App\JobsCleaning::where('job_id', '=', $invoice->job_id)->first();
                }
                
                if(!$job){
                    continue;
                }
                $crm_lead = CRMLeads::where('id', '=', $job->customer_id)->first();
                $crm_contacts = CRMContacts::where('lead_id', '=', $job->customer_id)->first();
                $crm_contact_email = CRMContactDetail::where('contact_id', '=', $crm_contacts->id)->where('detail_type', '=', 'Email')->first();
                $crm_contact_phone = CRMContactDetail::where('contact_id', '=', $crm_contacts->id)->where('detail_type', '=', 'Mobile')->first();
                $name = explode(' ',$crm_contacts->name,2);
                $first_name = isset($name[0])?$name[0]:'';
                $last_name = isset($name[1])?$name[1]:'';
                $customer_email = ($crm_contact_email)? $crm_contact_email->detail:'';
                $customer_phone = ($crm_contact_phone)? $crm_contact_phone->detail:'';

                //Creating new object of XERO
                $xero = new XeroApp(
                    new AccessToken($token),
                    $tenant->smtp_user
                );
                if(count($paidPayments)){
                    //Do nothing
                    $Xinvoice = $xero->invoices()->find($invoice->xero_id);
                }else{
                    //START::-----------------Contact---------------->
                    if(isset($crm_lead->xero_id)){
                        $contact = $xero->contacts()->find($crm_lead->xero_id);
                    }else{
                        $phone = new Phone($xero);
                        $phone->setPhoneType('MOBILE')
                            ->setPhoneNumber($customer_phone);   
                        if($customer_phone==''){
                            $contact = new Contact($xero);
                        $contact->setName($crm_contacts->name.'-'.$job->job_number)
                            ->setFirstName($first_name)
                            ->setLastName($last_name)
                            ->setEmailAddress($customer_email);
                        }else{
                            $contact = new Contact($xero);
                        $contact->setName($crm_contacts->name.'-'.$job->job_number)
                            ->setFirstName($first_name)
                            ->setLastName($last_name)
                            ->setEmailAddress($customer_email)
                            ->addPhone($phone); 
                        }
                                       
                        $reposne = $contact->save();                
                        
                        $ContactID = $contact->ContactID;                    
                        //update xero_id
                        CRMLeads::where('id', '=', $crm_contacts->lead_id)->update(['xero_id'=>$ContactID]);                
                    }
                    //END::--------------- Contact----------------->

                    //START::------------- Invoice----------------->
                    
                    //adding invoice line items
                    if(count($invoiceItems)){
                        if(isset($invoice->xero_id)){ //Updating invoice
                            $Xinvoice = $xero->invoices()->find($invoice->xero_id);                            
                        }else{ // Create New Invoice
                            $Xinvoice = new Invoice($xero);                                                                                 
                        }
                    foreach($invoiceItems as $item){
                        $product = Product::where(['id'=>$item->product_id])->first();
                        if($product && $product->xero_account_id !=NULL){
                            $itemAcount = $product->xero_account_id;
                        }else{
                            $itemAcount = $tenant->account_key;
                        }
                        //echo $itemAcount;exit;
                        $lineItem = new LineItem($xero);
                        $lineItem->setDescription($item->item_name.' - '.$item->description)
                            ->setUnitAmount($item->unit_price)
                            ->setQuantity($item->quantity)
                            ->setAccountCode($itemAcount); 
                        $Xinvoice->addLineItem($lineItem);                            
                    }
                    //end loop----
                    $Xinvoice->setContact($contact)
                                ->setType('ACCREC')
                                ->setReference($invoice->invoice_number)
                                ->setDate($invoice->issue_date)
                                ->setDueDate($invoice->due_date)
                                ->setStatus('AUTHORISED');  
                    $Xinvoice->save();  
                    $InvoiceID = $Xinvoice->InvoiceID;             
                    //update xero_id
                    \App\Invoice::where('id', '=', $invoice->id)->update(['xero_id'=>$InvoiceID]);                                                     
                }else{
                    \App\Invoice::where('id', '=', $invoice->id)->update(['sync_with_xero'=>'N']);
                    continue;
                }
            }        
                //END::------------- Invoice----------------->

                //START::------------- Payment ----------------->
                $Account = $xero->accounts()->find($tenant->smtp_secret);
                
                if(count($invoicePayments)){
                    foreach($invoicePayments as $payment){
                        if(isset($payment->xero_id)){
                            continue;
                        }
                        $XPayment = new Payment($xero);
                        $XPayment->setInvoice($Xinvoice)
                            ->setAmount($payment->amount)
                            ->setDate($payment->paid_on)
                            ->setAccount($Account)
                            ->setReference($payment->gateway.' - '.$payment->remarks);
                        $response = $XPayment->save();
                        
                        $PaymentID = $XPayment->PaymentID;
                        //update xero_id
                        \App\Payment::where('id', '=', $payment->id)->update(['xero_id'=>$PaymentID]);
                    }
                }
                //END::--------------- Payment ----------------->  
                \App\Invoice::where('id', '=', $invoice->id)->update(['sync_with_xero'=>'N']);
                //end
                echo '<br/>Invoice ID: '.$invoice->id;
                echo '____________________________';
            }
        }
        exit;

    }
    

    public function getRandNum()
	{
		$randNum = strval(rand(10,1000)); 

		return $randNum;
	}
}
