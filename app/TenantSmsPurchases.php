<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TenantSmsPurchases extends Model
{
    public $timestamps = false;
    protected $table = 'tenant_sms_purchases';
}
