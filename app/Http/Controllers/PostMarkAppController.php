<?php
namespace App\Http\Controllers;

//use App\ClientPayment;

use App\CRMActivityLog;
use App\CRMActivityLogAttachment;
use App\CRMOpportunities;
use App\Mail\sendMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PostMarkAppController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
    }

    public function processInboundEmailOpened()
    {
        try {
            $response = json_decode(file_get_contents('php://input'), true);
            if ($response) {
                $job_id_tag = $response['Tag'];
                //$toEmail = $response["ToFull"][0]["Email"];
                //$fromEmail = $response['From'];

                //$OriginalRecipient = $response["OriginalRecipient"];
                //echo json_encode($getTenantDetails);exit;
                //$emailDateTime = $response["Date"];

                $jobDetails = \App\JobsMoving::where('job_id', $job_id_tag)->first();
                    if ($jobDetails) {
                        $leadID = $jobDetails->customer_id;
                        $job_id     = $jobDetails->job_id;
                        $tenant_id = $jobDetails->tenant_id;
                    } else {
                        return true;
                    }
        
                        $opportunity = CRMOpportunities::where(['tenant_id' => $tenant_id, 'lead_id' => $leadID, 'op_type' => 'Moving'])->first();        
                        if($opportunity){
                            $user_id=$opportunity->user_id;
                        }else{
                            $user_id=0;
                        }
                        $time = date('Y-m-d H:i:s');

                        //Add Activity Log
                        $data['tenant_id'] = $tenant_id;
                        $data['lead_id'] = $leadID;
                        $data['user_id'] = $user_id;
                        $data['job_id'] = $job_id;
                        $data['log_type'] = 4; // Activity Email Opened
                        $data['log_message'] = 'Email opened by the Lead/Customer at '.$time;     
                        $data['log_date'] = Carbon::now();          
                        $data['log_status'] = 'unread';                
                        $data['external_message_id'] = $response['MessageID'];                
                        $model = CRMActivityLog::create($data);
                    $msg = 'done';
            } else {
                $msg = 'No JSON posted.';
            }
            echo json_encode($msg);
            exit;
        } catch (\Exception $ex) {
            echo json_encode($ex->getMessage());
            exit;
        }
    }

    public function processInboundEmailReceived()
    {
        //try {
            $response = json_decode(file_get_contents('php://input'), true);
            if ($response) {

                $res = str_replace(array('\'', '"', ',', ';', '<', '>', '-', '#', '&', '*', '!', '%'), '', $response['Subject']);


                $job_number = (int) filter_var($res, FILTER_SANITIZE_NUMBER_INT);
                //$job_id = $response['Tag'];

                //$toEmail = $response["ToFull"][0]["Email"];
                $fromEmail = $response['From'];

                $OriginalRecipient = $response["OriginalRecipient"];

                $getTenantDetails = \App\TenantApiDetail::where('incoming_email', $OriginalRecipient)->first();

                $tenant_id = $getTenantDetails->tenant_id;
                //echo json_encode($getTenantDetails);exit;
                $emailDateTime = $response["Date"];

                $getContactDetails = \App\CRMContactDetail::where('detail', $fromEmail)->where('tenant_id',$tenant_id)->where('detail_type','Email') ->first();
                
                $leadID = 0;
                $job_id     = 0;
                if($getContactDetails){

                    $getLeadID = \App\CRMContacts::where('id', $getContactDetails->contact_id)->first();

                    if($getLeadID){
                        $leadID = $getLeadID->lead_id;
                    }

                }
                /*$jobDetails = \App\JobsMoving::where('job_number', $job_number)->where('tenant_id',$tenant_id)
                        ->first();

                
                //echo json_encode($jobDetails);exit;
                if ($jobDetails) {
                    $leadID = $jobDetails->customer_id;
                    $job_id     = $jobDetails->job_id;
                } else {
                    $leadID = 0;
                    $job_id     = 0;
                }*/
                if($leadID!=0){
                    $jobDetails = \App\JobsMoving::where('customer_id', $leadID)->where('tenant_id',$tenant_id)->where('deleted', '0')->orderBy('job_id', 'DESC')->first();
                    if($jobDetails){
                        $job_id  = $jobDetails->job_id;
                    }
                }
                $opportunity = CRMOpportunities::where(['tenant_id' => $tenant_id, 'lead_id' => $leadID, 'op_type' => 'Moving', 'deleted' => '0'])->first();        
                if($opportunity){
                    $user_id=$opportunity->user_id;
                }else{
                    $user_id=0;
                }

                if ($response['HtmlBody'] && $response['HtmlBody'] != "") {
                    // try{
                    //     $logDetails = $this->removeElementByTagName('style', $response['HtmlBody']);
                    //     //$logDetails = $this->removeElementByTagName('base', $response['HtmlBody']);
                    // }catch (\Exception $ex) {
                    //     $logDetails = $response['HtmlBody'];
                    // }
                    $logDetails = $response['HtmlBody'];
                } else {
                    // try{
                    //     $logDetails = $this->removeElementByTagName('style', $response['TextBody']);
                    //     //$logDetails = $this->removeElementByTagName('base', $response['TextBody']);
                    // }catch (\Exception $ex) {
                    //     $logDetails = $response['TextBody'];
                    // }
                    $logDetails = $response['TextBody'];
                }

                //Add Activity Log
                if($leadID!=0){
                    $data['tenant_id'] = $tenant_id;
                    $data['lead_id'] = $leadID;
                    $data['user_id'] = $user_id;
                    $data['log_type'] = 5; // Activity Email Recieved
                    $data['log_from'] = $response['From'];
                    $data['job_id'] = $job_id;
                    $data['log_cc'] = $response['Cc'];
                    $data['log_bcc'] = $response['Bcc'];
                    $data['log_subject'] = $response['Subject'];
                    $data['log_message'] = $logDetails;     
                    $data['log_date'] = Carbon::now();          
                    $data['log_status'] = 'unread';                
                    $data['external_message_id'] = $response['MessageID'];                
                    $model = CRMActivityLog::create($data);

                    // Save In Receive email attachments
                    $destinationPath = public_path('/user-uploads/tenants/' . $tenant_id.'/temp');
                    if(count($response["Attachments"])){
                        $attachments = $response["Attachments"];
                        foreach($attachments as $attach){
                            //Save file to path
                            $file = base64_decode ($attach['Content']);
                            $filename = date('Y') . '-' . $attach['Name'];
                            File::isDirectory($destinationPath) or File::makeDirectory($destinationPath, 0777, true, true);
                            file_put_contents($destinationPath.'/'.$filename, $file);

                            $location = public_path().'/user-uploads/tenants/' . $tenant_id.'/temp/'.$filename;
                            $attach['attachment_type'] = isset($attach['Name'])?$attach['Name']:'Email Attachment';
                            $attach['attachment_content'] = $location;
                            $attach['log_id'] = $model->id;
                            $attach['tenant_id'] = $tenant_id;
                            $attach['created_at'] = Carbon::now();
                            $attach['updated_at'] = Carbon::now();
                            $model2 = CRMActivityLogAttachment::create($attach);
                        }
                    }
                }

                $msg = 'done';
            } else {
                $msg = 'No JSON posted.';
            }
            echo json_encode($msg);
            exit;
        // } catch (\Exception $ex) {
        //     echo json_encode($ex->getMessage());
        //     exit;
        // }
    }

    function removeElementByTagName($tagName, $html)
    {
        $doc = new \DOMDocument('1.0', 'utf-8');
        $doc->loadHTML($html);
        $nodeList = $doc->getElementsByTagName($tagName);
        for ($nodeIdx = $nodeList->length; --$nodeIdx >= 0;){
            $node = $nodeList->item($nodeIdx);
            $node->parentNode->removeChild($node);
        }
        $Html = $doc->saveHTML();
        return $Html;
    }

    public function processInboundEmailBounced()
    {
        try {
            $response = json_decode(file_get_contents('php://input'), true);
            if ($response) {
                $email_data['to'] = $response['From'];  
                $email_data['from_email'] = 'no-reply@onexfort.com';
                $email_data['from_name'] = 'no-reply@onexfort.com';
                $email_data['email_subject'] = 'The email sent to '.$response['Email'].' bounced ';
                $email_data['email_body'] = $response['Subject'].'<br/>'.$response['Description'];                                                                                                                      
                Mail::to($email_data['to'])->send(new sendMail($email_data));
                    $msg = 'Done';
            } else {
                $msg = 'No JSON posted.';
            }
            echo json_encode($msg);
            exit;
        } catch (\Exception $ex) {
            echo json_encode($ex->getMessage());
            exit;
        }
    }

}