<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class JobsMovingPricingAdditional extends Model {

    protected $table = 'jobs_moving_pricing_additional';
    protected $primaryKey  = 'id';    
    protected $dates = ['created_at'];
    use Notifiable;
}
