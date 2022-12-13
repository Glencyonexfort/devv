<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Vehicles extends Model {

    use Notifiable;
    protected $fillable = [
        'vehicle_name','vehicle_colour','tenant_id','active'
    ];
}
