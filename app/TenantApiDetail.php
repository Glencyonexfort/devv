<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TenantApiDetail extends Model
{
    public $timestamps = false;

    public static function check_stripe(){
    $stripe = self::where('tenant_id', auth()->user()->tenant_id)
                    ->where('provider', 'Stripe')->first();
    if($stripe){
        if(isset($stripe->account_key) && !empty($stripe->account_key)){
            return 1;
        }
    }
    return 0;
    }

    public static function check_stripe_by_tenant($tenant_id){
        $stripe = self::where('tenant_id', $tenant_id)
                        ->where('provider', 'Stripe')->first();
        if($stripe){
            if(isset($stripe->account_key) && !empty($stripe->account_key)){
                return 1;
            }
        }
        return 0;
        }
}