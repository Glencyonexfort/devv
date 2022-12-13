<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\CRMContactDetail;
use App\CRMContacts;
use App\CRMLeads;
use App\Product;
use Illuminate\Http\Request;
use LangleyFoxall\XeroLaravel\OAuth2;
use League\OAuth2\Client\Token\AccessToken;
use App\TenantApiDetail;
use Exception;
use Illuminate\Support\Facades\DB;
use LangleyFoxall\XeroLaravel\XeroApp;
use XeroPHP\Models\Accounting\Payment;
use XeroPHP\Models\Accounting\Contact;
use XeroPHP\Models\Accounting\Invoice;
use XeroPHP\Models\Accounting\Invoice\LineItem;
use XeroPHP\Models\Accounting\Phone;

class XeroSyncing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xero-syncing';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to invoice payments with xero.';

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
    
        $tenants = DB::table('tenant_api_details as t1')            
            ->select('t1.*')
            ->where(['t1.provider' => 'Xero'])
            ->get();
        foreach($tenants as $tenant){
            //if Account configuration didn't saved
            if(!isset($tenant->account_key) || $tenant->account_key=='Select account'){
                continue;
            }
            //Refresh Access Token
            $token = (array)json_decode($tenant->variable1);
            $this->refreshAccessTokenIfNecessary($token,$tenant->tenant_id);
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
                if(!$crm_contacts){
                    \App\Invoice::where('id', '=', $invoice->id)->update(['sync_with_xero'=>'N']);
                    continue;
                }
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
                        if($contact){
                            //.....
                        }else{
                            \App\Invoice::where('id', '=', $invoice->id)->update(['sync_with_xero'=>'N']);
                            continue;
                        }
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
                        
                            if (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
                              $contact->setName($crm_contacts->name.'-'.$job->job_number)
                            ->setFirstName($first_name)
                            ->setLastName($last_name)
                            ->addPhone($phone); 
                            }else{
                                $contact->setName($crm_contacts->name.'-'.$job->job_number)
                            ->setFirstName($first_name)
                            ->setLastName($last_name)
                            ->setEmailAddress($customer_email)
                            ->addPhone($phone); 
                            }
                        }
                        $contact->save();
                        if(isset($contact->ContactID)){
                            $ContactID = $contact->ContactID; 
                        }else{
                            \App\Invoice::where('id', '=', $invoice->id)->update(['sync_with_xero'=>'N']);
                            continue;
                        }
                                    
                        //update xero_id
                        CRMLeads::where('id', '=', $crm_contacts->lead_id)->update(['xero_id'=>$ContactID]);                
                    }
                    //END::--------------- Contact----------------->

                    //START::------------- Invoice----------------->
                    
                    //adding invoice line items
                    if(count($invoiceItems)){
                        if(isset($invoice->xero_id)){ //Updating invoice
                            $Xinvoice = $xero->invoices()->find($invoice->xero_id);  
                            if(!$Xinvoice){
                                \App\Invoice::where('id', '=', $invoice->id)->update(['sync_with_xero'=>'N']);
                                continue;
                            }else{
                                if($Xinvoice->getStatus()!="AUTHORISED"){
                                    \App\Invoice::where('id', '=', $invoice->id)->update(['sync_with_xero'=>'N']);
                                    continue;
                                }
                            }                          
                        }else{ // Create New Invoice
                            $Xinvoice = new Invoice($xero);                                                                                 
                        }
                    foreach($invoiceItems as $item){
                        $product = Product::where(['id'=>$item->product_id])->first();
                        if($product && $product->xero_account_id !=NULL && $product->xero_account_id !=0){
                            $itemAcount = $product->xero_account_id;
                        }else{
                            $itemAcount = $tenant->account_key;
                        }

                        $tax = \App\Tax::where('id', '=', $item->tax_id)->first();
                        if($tax){
                            $tax_percent = $tax->rate_percent;
                            $UnitPrice = ($item->unit_price) * (1 + $tax_percent / 100);
                        }else{
                            $taxCodeLine = '';
                            $UnitPrice = $item->unit_price;
                        }
                        
                        //echo $itemAcount;exit;
                        $lineItem = new LineItem($xero);
                        $lineItem->setDescription($item->item_name.' - '.$item->description)
                            ->setUnitAmount($UnitPrice)
                            ->setQuantity($item->quantity)
                            ->setAccountCode($itemAcount); 
                        $Xinvoice->addLineItem($lineItem);                            
                    }
                    //end loop----
                    $Xinvoice->setContact($contact)
                                ->setType('ACCREC')
                                ->setLineAmountType("Inclusive")
                                ->setReference($invoice->invoice_number)
                                ->setDate($invoice->issue_date)
                                ->setDueDate($invoice->due_date)
                                ->setStatus('AUTHORISED');  
                                //print_r($Xinvoice->getLineItems());exit;
                            try{
                                $response = $Xinvoice->save();
                            }catch(Exception $ex){
                                \App\Invoice::where('id', '=', $invoice->id)->update(['sync_with_xero'=>'N']);
                                continue;
                            }
                      
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
                if(isset($tenant->smtp_secret) || $tenant->smtp_secret!='Select account'){
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
                            try{
                                $response = $XPayment->save();
                            }catch(Exception $ex){
                                \App\Invoice::where('id', '=', $invoice->id)->update(['sync_with_xero'=>'N']);
                                continue;
                            }
                        
                        $PaymentID = $XPayment->PaymentID;
                        //update xero_id
                        \App\Payment::where('id', '=', $payment->id)->update(['xero_id'=>$PaymentID]);
                    }
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

    public function refreshAccessTokenIfNecessary($token,$tenant_id)
    {
        // Step 5 - Before using the access token, check if it has expired and refresh it if necessary.                      
                $accessToken = new AccessToken($token);
                $accessToken = $this->getOAuth2()->refreshAccessToken($accessToken);
                $tenant_api_details = TenantApiDetail::where(['tenant_id' => $tenant_id, 'provider' => 'Xero'])->first();
                $tenant_api_details->variable1 = json_encode($accessToken);
                $tenant_api_details->save();
        
    }

    private function getOAuth2()
    {
        return new OAuth2();
    }

    public function getRandNum()
	{
		$randNum = strval(rand(10,1000)); 

		return $randNum;
	}
}