<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class VehicleGroups extends Model
{
    use Notifiable;

    protected $table = 'vehicle_groups';
    protected $primaryKey  = 'id';

}
