<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class JobsMoving extends Model {

    protected $table = 'jobs_moving';
    protected $primaryKey  = 'job_id';    
    protected $dates = ['job_date'];

    protected $fillable = [
        'tenant_id','company_id', 'customer_id', 'crm_opportunity_id','job_number','opportunity','job_type','job_status',
        'pickup_furnishing','pickup_property_type','pickup_bedrooms','pickup_suburb','delivery_suburb','no_of_legs','job_date'
    ];

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id');
    }

    public function company()
    {
        return $this->belongsTo(Companies::class, 'company_id');
    }

    use Notifiable;
}
