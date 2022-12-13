<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class JobsCleaning extends Model {

    protected $table = 'jobs_cleaning';
    protected $primaryKey  = 'job_id';    
    protected $dates = ['job_date'];

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
