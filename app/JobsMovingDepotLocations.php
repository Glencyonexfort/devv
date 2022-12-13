<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class JobsMovingDepotLocations extends Model {

    protected $table = 'jobs_moving_depot_locations';
    protected $primaryKey  = 'id';    
    protected $dates = ['created_at'];
    use Notifiable;
}
