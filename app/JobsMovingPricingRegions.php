<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class JobsMovingPricingRegions extends Model {

    protected $table = 'jobs_moving_pricing_regions';
    protected $primaryKey  = 'id';    
    protected $dates = ['created_at'];
    use Notifiable;
}
