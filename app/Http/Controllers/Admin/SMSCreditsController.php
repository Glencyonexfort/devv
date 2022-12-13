<?php

namespace App\Http\Controllers\Admin;

use App\OrganisationSettings;
use App\Tenant;
use App\TenantApiDetail;
use App\TenantDetail;
use App\TenantSmsPurchases;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Carbon\Carbon;

class SMSCreditsController extends AdminBaseController
{

    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('app.menu.smsCredits');
        $this->pageIcon = 'icon-envelop4';        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->sms_balance_lower_limit=0;
        $this->sms_balance_top_up_qty=0;
        $this->sms_auto_top_up='N';
        $this->stripe_customer_id='N';
        $tenant_api_detail = new TenantApiDetail;
        $this->tenant_details = \App\TenantDetail::where('tenant_id', auth()->user()->tenant_id)
                    ->first();
        if($this->tenant_details){
            if($this->tenant_details->stripe_customer_id!=NULL){
                $this->stripe_customer_id = $this->tenant_details->stripe_customer_id;
            }
            $this->sms_balance_lower_limit = $this->tenant_details->sms_balance_lower_limit;
            $this->sms_balance_top_up_qty = $this->tenant_details->sms_balance_top_up_qty;
            $this->sms_auto_top_up = $this->tenant_details->sms_auto_top_up;
        }

        $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $this->sys_api_settings = \App\SysApiSettings::where('type', 'tenant_sms_purchase')
                    ->where('in_use', '1')
                    ->first();
        $this->sms_purchase_history = TenantSmsPurchases::where('tenant_id', '=', auth()->user()->tenant_id)->orderby('transaction_date','DESC')->get();
        return view('admin.sms-credits.index', $this->data);
    }

    

    public function buyCredits(Request $request)
    {
        $sms_credit_data=$request->all();
        $sms_credit_data['tenant_id'] = auth()->user()->tenant_id;
        $sms_credit_data['stripe_customer_id'] = 'N';
        $tenant_detail = new TenantDetail();
        $response = $tenant_detail->smsStripeCharge($sms_credit_data);
        
        return json_encode($response);
    }

}
