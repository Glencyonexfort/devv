<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quotes extends Model
{
    protected $table = 'quotes';

    public $timestamps = false;

    protected $fillable = [
        'tenant_id','quote_number', 'crm_opportunity_id', 'sys_job_type','job_id','quote_date','created_by','created_date'
    ];

}
