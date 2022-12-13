<?php

namespace App\Http\Controllers\Admin;

use App\CRMContactDetail;
use App\CRMContacts;
use App\CRMLeads;
use App\CustomerDetails;
use App\Product;
use Illuminate\Http\Request;
use League\OAuth2\Client\Token\AccessToken;
use App\TenantApiDetail;
use Illuminate\Support\Facades\DB;

class ConnectMyobController extends AdminBaseController
{
    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('app.menu.connectMyob');
        $this->pageIcon = 'icon-loop';   
        $this->MYOB_CLIENT_ID = env('MYOB_CLIENT_ID');
        $this->MYOB_CLIENT_SECRET = env('MYOB_CLIENT_SECRET');
        $this->MYOB_REDIRECT_URI = env('MYOB_REDIRECT_URI');   
        $this->api_uri = 'https://api.myob.com';  
    }

    public function handleCallbackFromMyob(Request $request)
    {
        $tokens = $this->getAccessToken(0,$request->code);

        $this->tenant_api_details = new TenantApiDetail;
        $this->tenant_api_details->tenant_id = auth()->user()->tenant_id;
        $this->tenant_api_details->provider = 'MYOB';
        $this->tenant_api_details->variable1 = $tokens;        
        $this->tenant_api_details->save();
        return $this->index();
    }

    public function getAccessToken($refresh,$authorization_code)
    {
        $curl = curl_init();
        if($refresh==1){
            $params = 'client_id=' . rawurlencode($this->MYOB_CLIENT_ID) .
            '&client_secret=' . rawurlencode($this->MYOB_CLIENT_SECRET) .
            '&grant_type=refresh_token' .
            '&refresh_token=' . $authorization_code;
        }else{
            $params = 'client_id=' . rawurlencode($this->MYOB_CLIENT_ID) .
            '&client_secret=' . rawurlencode($this->MYOB_CLIENT_SECRET) .
            '&grant_type=authorization_code' .
            '&code=' . rawurlencode($authorization_code) .
            '&redirect_uri=' . rawurlencode($this->MYOB_REDIRECT_URI);
        }
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://secure.myob.com/oauth2/v1/authorize/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $params,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
        ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function index()
    {
        $this->xero_connected = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'Xero'])->first();            
            if($this->xero_connected){ 
                $this->tenant_api_details=false;
                $this->invoices=array();
                return view('admin.connect-myob.index', $this->data);
            }
        //try{
            $this->tenant_api_details=NULL;
            $this->auth_url = 'https://secure.myob.com/oauth2/account/authorize?client_id='.$this->MYOB_CLIENT_ID.'&redirect_uri='.$this->MYOB_REDIRECT_URI.'&response_type=code&scope=CompanyFile';

            $this->myob = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'MYOB'])->first();            
            if($this->myob){ 
                $token = (array)json_decode($this->myob->variable1);

                if(isset($token['error'])){
                    $this->invoices=array();
                }else{
                    
                    $tokens = $this->getAccessToken(1,$token['refresh_token']);
                    $this->tenant_api_details = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'MYOB'])->first();
                    $this->tenant_api_details->variable1 = $tokens;
                    $this->tenant_api_details->save();
                    $access_token_arr = (array)json_decode($tokens);

                    $this->myob_companies = $this->getMyobCompanies($access_token_arr['access_token']);
                    $this->accounts = $this->getMyobAccounts($this->tenant_api_details->url ,$access_token_arr['access_token']);
                    $this->taxcodes = $this->getTaxCodes($this->tenant_api_details->url ,$access_token_arr['access_token']);


                    $this->invoices = \App\Invoice::where('sync_with_myob', '=', 'Y')
                        ->join('jobs_moving', 'jobs_moving.job_id', '=', 'invoices.job_id')
                        ->where('invoices.tenant_id', '=', auth()->user()->tenant_id)
                        ->select('invoices.*','jobs_moving.job_number','jobs_moving.job_type','jobs_moving.customer_id')
                        ->get();
                }
            }else{
                $this->invoices=array();
            }
        // } catch (\Exception $ex) {
        //     return $ex->getMessage();
        // }
        return view('admin.connect-myob.index', $this->data);
    }

    public function getMyobCompanies($access_token){
        $curl = curl_init();
        $authorization = 'Authorization: Bearer '.$access_token;
        $headers = array(
            $authorization,
            'x-myobapi-key: '.$this->MYOB_CLIENT_ID,
            'x-myobapi-version: v2',
            'Accept-Encoding: gzip,deflate'
        );

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->api_uri.'/accountright',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }


    public function getMyobAccounts($company_uri, $access_token){
        $curl = curl_init();
        $authorization = 'Authorization: Bearer '.$access_token;
        $headers = array(
            $authorization,
            'x-myobapi-key: '.$this->MYOB_CLIENT_ID,
            'x-myobapi-version: v2',
            'Accept-Encoding: gzip,deflate'
        );

        curl_setopt_array($curl, array(
        CURLOPT_URL => $company_uri.'/GeneralLedger/Account',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }

    public function getTaxCodes($company_uri, $access_token){
        $curl = curl_init();
        $authorization = 'Authorization: Bearer '.$access_token;
        $headers = array(
            $authorization,
            'x-myobapi-key: '.$this->MYOB_CLIENT_ID,
            'x-myobapi-version: v2',
            'Accept-Encoding: gzip,deflate'
        );

        curl_setopt_array($curl, array(
        CURLOPT_URL => $company_uri.'/GeneralLedger/TaxCode',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }

    public function disconnect()
    {
        try{
            $tenant_api_details = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'MYOB'])->delete();                        
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
        return $this->index();
    }

    public function storeCompanyDetail(Request $request)
    {
        try{
            $tenant_api_details = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'MYOB'])
            ->update(['url'=>$request->url]);                        
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
        $response['error'] = 0;
        $response['message'] = 'Conifg has been saved';        
        return json_encode($response);
    } 
      
    public function storeConfig(Request $request)
    {
        try{
            $tenant_api_details = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'MYOB'])
            ->update(['smtp_user'=>$request->smtp_user, 'account_key'=>$request->account_id, 'smtp_secret'=>$request->payment_account_id]);                        
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
        $response['error'] = 0;
        $response['message'] = 'Configuration has been saved';        
        return json_encode($response);
    } 

    public function refreshAccessToken($authorization_code)
    {
        $curl = curl_init();
        $params = 'client_id=' . rawurlencode($this->MYOB_CLIENT_ID) .
        '&client_secret=' . rawurlencode($this->MYOB_CLIENT_SECRET) .
        '&grant_type=refresh_token' .
        '&refresh_token=' . $authorization_code;
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://secure.myob.com/oauth2/v1/authorize/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $params,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
        ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'MYOB'])->update(
            [
                'variable1' => $response
            ]
        );
    }

    public function syncInvoice(){

        ini_set('max_execution_time', 0);
    
        $tenants = DB::table('tenant_api_details as t1')            
            ->select('t1.*')
            ->where(['t1.provider' => 'MYOB'])
            ->get();
        foreach($tenants as $tenant){
            //if Account configuration didn't saved
            if(!isset($tenant->smtp_secret) || !isset($tenant->account_key)){
                continue;
            }
            //Refresh Access Token
            $old_token = (array)json_decode($tenant->variable1);
            $this->refreshAccessToken($old_token['refresh_token']);

            $tenant = TenantApiDetail::where(['tenant_id' => $tenant->tenant_id, 'provider' => 'MYOB'])->first();            
            $token = (array)json_decode($tenant->variable1);

            //----
            $invoices = \App\Invoice::where(['sync_with_myob'=>'Y','tenant_id'=>$tenant->tenant_id])->get();
            foreach($invoices as $invoice){
                
                $invoicePayments = \App\Payment::where('invoice_id', '=', $invoice->id)->get();
                $paidPayments = \App\Payment::where('invoice_id', '=', $invoice->id)->get();
                if($invoice->sys_job_type=="Moving"){
                    $job = \App\JobsMoving::where('job_id', '=', $invoice->job_id)->first();
                }else{
                    $job = \App\JobsCleaning::where('job_id', '=', $invoice->job_id)->first();
                }
                // anything wrong with the job
                if(!$job){
                    continue;
                }
                $crm_lead = CRMLeads::where('id', '=', $job->customer_id)->first();
                //-------Sync Customer 
                $Customer_UID = $this->createCustomer($tenant,$token['access_token'],$crm_lead);

                //-------Sync Invoice 
                $Invoice_UID = $this->createInvoice($tenant,$token['access_token'],$invoice, $Customer_UID, $job->job_number);
                
                //-------Sync Payments 
                if(count($invoicePayments)){
                    foreach($invoicePayments as $payment){
                        if(isset($payment->myob_id)){
                            continue;
                        }
                        $Payment_UID = $this->createPayment($tenant,$token['access_token'],$payment, $Customer_UID, $Invoice_UID);
                    }
                }
                \App\Invoice::where('id', '=', $invoice->id)->update(['myob_id'=>$Invoice_UID]); 
                //\App\Invoice::where('id', '=', $invoice->id)->update(['sync_with_myob'=>'N']);
                //END::----------------------->
                echo '<br/>Invoice ID: '.$invoice->invoice_number;
                echo '____________________________';

                }
            }        

    }
    
    public function createCustomer($tenant,$access_token,$model){
        $company_url = $tenant->url;
        $taxcode = $tenant->smtp_user;

        $crm_contacts = CRMContacts::where('lead_id', '=', $model->id)->first();
        $crm_contact_email = CRMContactDetail::where('contact_id', '=', $crm_contacts->id)->where('detail_type', '=', 'Email')->first();
        $crm_contact_phone = CRMContactDetail::where('contact_id', '=', $crm_contacts->id)->where('detail_type', '=', 'Mobile')->first();
        $name = explode(' ',$crm_contacts->name,2);
        $first_name = isset($name[0])?$name[0]:'';
        $last_name = isset($name[1])?$name[1]:'';
        $customer_email = ($crm_contact_email)? $crm_contact_email->detail:'';
        $customer_phone = ($crm_contact_phone)? $crm_contact_phone->detail:'';

        $address = '';
        $post_code = '';
        $suburb = '';

        if($model->lead_type=="Residential"){
            $customer_detail = CustomerDetails::where('customer_id', '=', $model->id)->first();
            if($customer_detail){
                $address = $customer_detail->billing_address;
                $post_code = $customer_detail->billing_post_code;
                $suburb = $customer_detail->billing_suburb;
            }
        }

        $curl = curl_init();
        $authorization = 'Authorization: Bearer '.$access_token;
        $headers = array(
            $authorization,
            'x-myobapi-key: '.$this->MYOB_CLIENT_ID,
            'x-myobapi-version: v2',
            'Accept-Encoding: gzip,deflate',
        );
        curl_setopt_array($curl, array(
        CURLOPT_URL => $company_url.'/Contact/Customer?returnBody=true',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS =>'{
                    "LastName": "'.$first_name.'",
                    "FirstName": "'.$last_name.'",
                    "IsIndividual": true,
                    "DisplayID": "C'.$this->getRandNum().'",
                    "SellingDetails": {
                        "TaxCode": {
                            "UID": "'.$taxcode.'"
                        },
                        "FreightTaxCode": {
                            "UID": "'.$taxcode.'"
                        }
                    },
                    "Addresses" : [
                        {
                        "Location" : 1,
                        "Street" : "'.$address.'",
                        "State" : "'.$suburb.'",
                        "PostCode" : "'.$post_code.'",
                        "Country" : "Australia",
                        "Phone1" : "'.$customer_phone.'",
                        "Email" : "'.$customer_email.'",
                        "ContactName" : "'.$first_name.'",
                        }
                    ],
        }'
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $record = json_decode($response);
        return $record->UID;
    } 

    public function createInvoice($tenant,$access_token,$invoice, $UID, $job_number){
        $company_url = $tenant->url;
        $taxcode = $tenant->smtp_user;

        $invoiceItems = \App\InvoiceItems::where('invoice_id', '=', $invoice->id)->get();
        $lines = '[';
        $linecount = count($invoiceItems);
        foreach($invoiceItems as $line){
            $linecount--;
            $line_item = '{ 
                "Type": "Transaction", 
                "Description": "'.$line->item_name.' : '.$line->item_summary.'", 
                "ShipQuantity": "'.$line->quantity.'", 
                "UnitCount": "'.$line->quantity.'", 
                "UnitPrice": "'.$line->unit_price.'", 
                "TaxCode": { 
                        "UID": "'.$taxcode.'"
                }, 
                "Account": {
                        "UID": "'.$tenant['account_key'].'"
                }
            }';
            $lines .= $line_item;
            if($linecount>0){
                $lines .= ',';
            }
        }
        $lines .=']';
        $curl = curl_init();
        $authorization = 'Authorization: Bearer '.$access_token;
        $headers = array(
            $authorization,
            'x-myobapi-key: '.$this->MYOB_CLIENT_ID,
            'x-myobapi-version: v2',
            'Accept-Encoding: gzip,deflate'
        );
        curl_setopt_array($curl, array(
        CURLOPT_URL => $company_url.'/Sale/Invoice/Item?returnBody=true',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS =>'{ 
            "Number": "'.$invoice->invoice_number.'", 
            "Date": "'.$invoice->issue_date.'", 
            "PromisedDate": "'.$invoice->due_date.'", 
            "SupplierInvoiceNumber": null, 
            "Customer": { 
                "UID": "'.$UID.'"
            }, 
            "IsTaxInclusive": false, 
            "IsReportable": false, 
            "Lines": '.$lines.', 
            "Comment": "Invoice for Job Number '.$job_number.'", 
            "Status": "Open" 
        }'
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $record = json_decode($response);
        return $record->UID;
    } 

    public function createPayment($tenant,$access_token,$payment,$Customer_UID, $Invoice_UID){
        $company_url = $tenant->url;
        $account = $tenant->smtp_secret;
        $payment_date = str_replace(" ","T",$payment->paid_on);

        $curl = curl_init();
        $authorization = 'Authorization: Bearer '.$access_token;
        $headers = array(
            $authorization,
            'x-myobapi-key: '.$this->MYOB_CLIENT_ID,
            'x-myobapi-version: v2',
            'Accept-Encoding: gzip,deflate',
        );
        curl_setopt_array($curl, array(
        CURLOPT_URL => $company_url.'/Sale/CustomerPayment?returnBody=true',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS =>'{
            "DepositTo": "Account",
            "Account": {
                "UID": "'.$account.'"
            },
            "Customer": {
                "UID": "'.$Customer_UID.'"
            },
            "Invoices": [{
                "UID": "'.$Invoice_UID.'",
                "AmountApplied" : "'.$payment->amount.'",
                "AmountAppliedForeign" : null,
                "Type": "Invoice"
            }],
            "Date": "'.$payment_date.'",
            "Memo": "'.$payment->remarks.'",
            "PaymentMethod" : "'.$payment->gateway.'",
            "AmountReceived": "'.$payment->amount.'"
        }',
        ));

        $response = curl_exec($curl);
        echo $response;
        curl_close($curl);
        // $record = json_decode($response);
        // return $record->UID;
    } 

    public function getRandNum()
	{
		$randNum = strval(rand(10,1000)); 
		return $randNum;
	}
}
