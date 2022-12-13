<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class TenantServicingCities extends Model {

    protected $table = 'tenant_servicing_cities';
    protected $primaryKey  = 'id';
    use Notifiable;
}
