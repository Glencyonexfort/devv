<?php

namespace App\Http\Controllers\Admin;

use App\TenantApiDetail;
use Illuminate\Http\Request;

class AdminConfigureEmailController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageIcon = 'icon-envelope';
        $this->pageTitle = __('app.menu.configureEmail');
    }

    public function index()
    {
        $this->account_key = '';
        $this->default_email = '';
        $this->domain_detail = false;
        $this->tenant_api_detail = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'PostMarkApp'])->first();
        if($this->tenant_api_detail){
            $this->default_email=$this->tenant_api_detail->to_email;
        }
        if ($this->tenant_api_detail && !empty($this->tenant_api_detail->account_key) && $this->tenant_api_detail->account_key != null) {
            $this->account_key = $this->tenant_api_detail->account_key;
            $this->domain_detail = $this->getPostMarkDomain($this->tenant_api_detail->variable1);            
        }
        //echo '<pre>';
        //print_r($this->domain_detail);
        return view('admin.email-settings.configure-email', $this->data);
    }

    public function updateEmail(Request $request)
    {                
        $response['error'] = 1;
        $response['message'] = 'Invalid email domain';
        $email = $request->email;
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $response['error'] = 1;
            $response['message'] = 'Invalid Email';
        } else {
            // If it is valid email
            $tenant_api_detail = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'PostMarkApp'])->first();
            $domain = $tenant_api_detail->account_key;
            if($domain!=NULL && !empty($domain)){                
                $domain = explode('.', $domain);
                $domain = array_reverse($domain);
                if (count($domain) > 3) { //if second level domain
                     $domain = "$domain[2].$domain[1].$domain[0]";
                 } else {
                     $domain = "$domain[1].$domain[0]";
                }
                // Check Email Domain
                $email_arr = explode('@', $email);
                $email_arr = array_reverse($email_arr);
                if($email_arr[0]==$domain){
                    //If all okay then update
                    $tenant_api_detail->from_email=$email;
                    $tenant_api_detail->to_email=$email;
                    $tenant_api_detail->save();
                    $response['error'] = 0;
                    $response['message'] = 'Email has been updated.';
                }                
            }            
        }
        return json_encode($response);
    }

    public function configureDomain(Request $request)
    {
        $domain = $request->domain;
        if ($this->is_valid_domain_name($domain)) {
            $response['error'] = 0;
            $response['message'] = 'Valid Domain';
            $check = $this->createPostMarkDomain($domain);
            if($check!=true){
                $response = json_decode($check);
                $response['error'] = 1;
            }
            return json_encode($response);
        } else {
            $response['error'] = 1;
            $response['message'] = 'Invalid Domain';
            return json_encode($response);            
        }
    }

    public function verifyPostMarkDKIM(Request $request)
    {
        $id = $request->domain_id;
        $api_key = env('POSTMARK_API_TOKEN');
        $headers = array(
            "Content-Length: 0",
            "Accept: application/json",
            "X-Postmark-Account-Token: {$api_key}"
        );
        $ch = curl_init("https://api.postmarkapp.com/domains/{$id}/verifyDkim");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $curl_error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        // do some checking to make sure it sent
        if($http_code !== 200){
            return $response;exit;
        }else{
            $res['error'] = 0;
            return json_encode($res);
        }
    }

    public function verifyPostMarkReturnPath(Request $request)
    {
        $id = $request->domain_id;
        $api_key = env('POSTMARK_API_TOKEN');
        $headers = array(
            "Content-Length: 0",
            "Accept: application/json",
            "X-Postmark-Account-Token: {$api_key}"
        );
        $ch = curl_init("https://api.postmarkapp.com/domains/{$id}/verifyReturnPath");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $curl_error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        // do some checking to make sure it sent
        if($http_code !== 200){
            return $response;exit;
        }else{
            $res['error'] = 0;
            return json_encode($res);
        }
    }

    protected function is_valid_domain_name($domain_name)
    {
        return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name) //valid chars check
             && preg_match("/^.{1,253}$/", $domain_name) //overall length check
             && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)); //length of each label
    }

    protected function createPostMarkDomain($domain)
    {
        $api_key = env('POSTMARK_API_TOKEN');
        $data = [
            'Name'=>$domain
        ];

        $headers = array(
            "Accept: application/json",
            "Content-Type: application/json",
            "X-Postmark-Account-Token: {$api_key}"
        );
        $ch = curl_init('https://api.postmarkapp.com/domains');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $curl_error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        // do some checking to make sure it sent
        if($http_code !== 200){
            return $response;exit;
        }else{
            $result = json_decode($response);
            //update tenant_api_detail table
            TenantApiDetail::where(['tenant_id'=>auth()->user()->tenant_id,'provider' => 'PostMarkApp'])->update(['account_key'=>$result->Name,'variable1'=>$result->ID]);
            return true;
        }
    }

    protected function getPostMarkDomain($id)
    {
        $api_key = env('POSTMARK_API_TOKEN');
        $headers = array(
            "Accept: application/json",
            "X-Postmark-Account-Token: {$api_key}"
        );
        $ch = curl_init("https://api.postmarkapp.com/domains/{$id}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $curl_error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        // do some checking to make sure it sent
        if($http_code !== 200){
            return false;
        }else{
            $result = json_decode($response);
            return $result;
        }
    }    

}

