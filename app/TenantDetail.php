<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Stripe\Stripe;
use Carbon\Carbon;

class TenantDetail extends Model
{
    public $timestamps = false;
    protected $table = 'tenant_details';
    
    //START:: SMS Auto Top up 

    //Check if sms auto top up is Y or N
    public function CheckSmsAutoTopup($tenant_id){
        $this->sys_api_settings = \App\SysApiSettings::where('type', 'tenant_sms_purchase')->where('in_use', '1')->first();
        $tenant_detail = TenantDetail::where('tenant_id', $tenant_id)->first();
        if($tenant_detail){
            return $tenant_detail->sms_auto_top_up;
        }else{
            return 'N';
        }          
    }

    public function smsStripeCharge($data)
    {
        
        $tenant_id = $data['tenant_id'];
        $stripe_customer_id = $data['stripe_customer_id'];
        $stripeToken = $data['stripeToken'];
        $stripeEmail = $data['stripeEmail'];
        $amount_paid = round($data['amount']);
        $sms_credit_data=$data;

        // Stripe API secret key
        $secret_key = env('STRIPE_SECRET');
        Stripe::setApiKey($secret_key);
        $tenant = Tenant::where('tenant_id', $tenant_id)->first();

        try {
            if($stripe_customer_id=='N'){
                try {
                    // Add customer to stripe
                    $customer = \Stripe\Customer::create(array(
                        'email' => $stripeEmail,
                        'source' => $stripeToken,
                    ));
                    $stripeCustomerId = $customer->id;
                } catch (\Stripe\Error\OAuth\OAuthBase $e) {
                    $response = array(
                        'status' => 0,
                        'msg' => $e->getMessage(),
                    );
                    return json_encode($response);
                }
             }else{
                $stripeCustomerId = $stripe_customer_id;
             }
            // Charge a credit or a debit card
            $charge = \Stripe\Charge::create(array(
                'customer' => $stripeCustomerId,
                'amount' => $amount_paid,
                'currency' => 'AUD',
                'description' => 'SMS Credit Buy - ' . $tenant->tenant_name,
            ));
        } catch (\Stripe\Error\OAuth\OAuthBase $e) {
            $response = array(
                'status' => 0,
                'msg' => $e->getMessage(),
            );
            return json_encode($response);
        }

        $chargeJson = $charge->jsonSerialize();
        if ($chargeJson['amount_refunded'] == 0 && empty($chargeJson['failure_code']) && $chargeJson['paid'] == 1 && $chargeJson['captured'] == 1) {
            $transactionID = $chargeJson['id'];
            $sms_credit_data['stripe_customer_id'] = $stripeCustomerId;
            //Start::Add / Update  Tenant Detail Table
            $this->updateTenantDetail($sms_credit_data);

            //Start::Add record into Tenant SMS Purchases Table
            $this->updateTenantSmsPurchases($sms_credit_data, $transactionID);
            
            $response = array(
                'status' => 1,
                'transactionID' => $chargeJson['id'],
                'msg' => 'SMS credits has been purchased',
            );
        } else {
            $response = array(
                'status' => 0,
                'msg' => 'Transaction has been failed.',
            );
        }
        return $response;
    }

    public function updateTenantDetail($data)
    {
        $tenant_detail = TenantDetail::where('tenant_id', auth()->user()->tenant_id)->first(); 
        if(!$tenant_detail){
            $tenant_detail = new TenantDetail();
            $tenant_detail->tenant_id=$data['tenant_id'];
            $tenant_detail->sms_credit = $data['sms_credit'];
            $tenant_detail->stripe_customer_id = $data['stripe_customer_id'];
            $tenant_detail->sms_auto_top_up = $data['auto_topup'];
            $tenant_detail->sms_balance_lower_limit = $data['sms_balance_lower_limit'];
            $tenant_detail->sms_balance_top_up_qty = $data['sms_balance_top_up_qty'];
            $tenant_detail->save();
        }else{
            TenantDetail::where('tenant_id', $data['tenant_id'])->update([
                'sms_credit' => $tenant_detail->sms_credit + $data['sms_credit'],
                'stripe_customer_id' => $data['stripe_customer_id'],
                'sms_auto_top_up' => $data['auto_topup'],
                'sms_balance_lower_limit' => $data['sms_balance_lower_limit'],
                'sms_balance_top_up_qty' => $data['sms_balance_top_up_qty'],
            ]); 
        }
        
    }

    public function updateTenantSmsPurchases($data,$transaction_id)
    {
        $sms_purchases = new TenantSmsPurchases();
        $sms_purchases->tenant_id=$data['tenant_id'];
        $sms_purchases->gateway = 'Stripe';
        $sms_purchases->transaction_id = $transaction_id;
        $sms_purchases->qty_purchased = $data['sms_credit'];
        $sms_purchases->payment_amount = $data['amount'] / 100;
        $sms_purchases->transaction_date = Carbon::now();
        $sms_purchases->status = 'complete';
        $sms_purchases->save();
    }

    //END:: SMS Auto Top up 

}
