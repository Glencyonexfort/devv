<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class JobsMovingLegTrips extends Model {

    protected $table = 'jobs_moving_leg_trips';
    protected $primaryKey  = 'id';
    use Notifiable;
}
