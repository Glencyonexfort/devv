<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CRMOpPipelineStatuses;
use App\EmailTemplates;
use App\Tax;
use App\User;
use App\Helper\Reply;
use App\JobsCleaningAutoQuoting;
use App\Product;
use App\SMSTemplates;

class ManageCleaningAutoQuoteController extends AdminBaseController
{
     public function __construct() {
        parent::__construct();
        $this->pageTitle = __('app.menu.enableAutoQuote');
        $this->pageIcon = 'ti-file';
    }

    public function index() {
        $this->autoQuoting = JobsCleaningAutoQuoting::where(['tenant_id'=> auth()->user()->tenant_id])->first();
        $this->sms_templates = SMSTemplates::where(['tenant_id' => auth()->user()->tenant_id])->get();
        $this->pipelineStatuses = CRMOpPipelineStatuses::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('sort_order', 'ASC')->get();

        $this->emailTemplates = EmailTemplates::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('id', 'ASC')->get();
        $this->taxes = Tax::where(['tenant_id'=> auth()->user()->tenant_id])->get();
        $this->users = User::where(['tenant_id'=> auth()->user()->tenant_id])->get();
        $this->products = Product::where(['tenant_id' => auth()->user()->tenant_id])->get();

        return view('admin.cleaning-auto-quote.index', $this->data);
    }


    public function saveAutoQuoteData(Request $request, $id) {
        if($request->initial_op_status_id == $request->quoted_op_status_id){
            $response['error']=1;
            $response['message']='The Successfully Quoted Opportunity Status and the Initial Opportunity Status should not be the same';  
            return json_encode($response);exit;          
        }elseif($request->initial_op_status_id == $request->failed_op_status_id){
            $response['error']=1;
            $response['message']='The Quoting Failed Opportunity Status and the Initial Opportunity Status should not be the same';
            return json_encode($response);exit;
        }
        $credential = JobsCleaningAutoQuoting::findOrFail($id);
        //$credential->tenant_id = auth()->user()->tenant_id;
        $credential->tax_id_for_quote = $request->tax_id_for_quote;
        $credential->initial_op_status_id = $request->initial_op_status_id;
        $credential->auto_quote_enabled = $request->has('auto_quote_enabled') && $request->input('auto_quote_enabled') == 'Y' ? 'Y' : 'N';

        $credential->quoted_op_status_id = $request->quoted_op_status_id;
        $credential->failed_op_status_id = $request->failed_op_status_id;
        $credential->send_quote_fail_email_to = $request->send_quote_fail_email_to;
        $credential->quote_line_item_product_id = $request->quote_line_item_product_id;
        // $credential->redirect_to_inven_form_after_quote_payment = $request->has('redirect_to_inven_form_after_quote_payment') && $request->input('redirect_to_inven_form_after_quote_payment') == 'Y' ? 'Y' : 'N';
        $credential->send_auto_quote_email_to_customer = $request->has('send_auto_quote_email_to_customer') && $request->input('send_auto_quote_email_to_customer') == 'Y' ? 'Y' : 'N';
        $credential->send_auto_quote_sms_to_customer = $request->has('send_auto_quote_sms_to_customer') && $request->input('send_auto_quote_sms_to_customer') == 'Y' ? 'Y' : 'N';
        $credential->quote_sms_template_id = $request->quote_sms_template_id;

        $credential->quote_email_template_id = $request->quote_email_template_id;
        $credential->fail_email_template_id = $request->fail_email_template_id;
        //$credential->created_by = $request->quoted_op_status_id;
        $credential->updated_by = $this->user->id;
        $credential->save();

        return Reply::success(__('messages.auotoQuoteUpdated'));
    }


    public function createAutoQuoteData(Request $request) {

        if($request->initial_op_status_id == $request->quoted_op_status_id){
            $response['error']=1;
            $response['message']='The Successfully Quoted Opportunity Status and the Initial Opportunity Status should not be the same';  
            return json_encode($response);exit;          
        }elseif($request->initial_op_status_id == $request->failed_op_status_id){
            $response['error']=1;
            $response['message']='The Quoting Failed Opportunity Status and the Initial Opportunity Status should not be the same';
            return json_encode($response);exit;
        }
        

        //$autoQuoting = JobsCleaningAutoQuoting::where(['tenant_id'=> auth()->user()->tenant_id])->first();
        $credential = new JobsCleaningAutoQuoting();
        $credential->tenant_id = auth()->user()->tenant_id;
        $credential->tax_id_for_quote = $request->tax_id_for_quote;
        $credential->initial_op_status_id = $request->initial_op_status_id;
        $credential->auto_quote_enabled = $request->has('auto_quote_enabled') && $request->input('auto_quote_enabled') == 'Y' ? 'Y' : 'N';

        $credential->quoted_op_status_id = $request->quoted_op_status_id;
        $credential->failed_op_status_id = $request->failed_op_status_id;
        $credential->send_quote_fail_email_to = $request->send_quote_fail_email_to;
        $credential->quote_line_item_product_id = $request->quote_line_item_product_id;
        // $credential->redirect_to_inven_form_after_quote_payment = $request->has('redirect_to_inven_form_after_quote_payment') && $request->input('redirect_to_inven_form_after_quote_payment') == 'Y' ? 'Y' : 'N';
        $credential->send_auto_quote_email_to_customer = $request->has('send_auto_quote_email_to_customer') && $request->input('send_auto_quote_email_to_customer') == 'Y' ? 'Y' : 'N';
        $credential->send_auto_quote_sms_to_customer = $request->has('send_auto_quote_sms_to_customer') && $request->input('send_auto_quote_sms_to_customer') == 'Y' ? 'Y' : 'N';
        $credential->quote_sms_template_id = $request->quote_sms_template_id;

        $credential->quote_email_template_id = $request->quote_email_template_id;
        $credential->fail_email_template_id = $request->fail_email_template_id;
        $credential->created_by = $this->user->id;
        //$credential->updated_by = $this->user->id;
        $credential->save();

        return Reply::success(__('messages.auotoQuoteSaved'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

}
