<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class VehicleUnavailability extends Model {

    use Notifiable;
    public $timestamps = false;
    protected $table = 'vehicle_unavailability';
    protected $fillable = [
        'vehicle_id','from_date','to_date','from_time','to_time','tenant_id','reason','created_date','updated_date','created_by','updated_by'
    ];
    public static function getData($exceptId = NULL)
    {
        $vehicles = VehicleUnavailability::select(
                                            'vehicles.vehicle_colour',
                                            'vehicle_unavailability.*'
        )
            ->where('vehicle_unavailability.deleted', 'N')
            ->where('vehicle_unavailability.tenant_id', auth()->user()->tenant_id)
            ->join('vehicles', 'vehicles.id', 'vehicle_unavailability.vehicle_id')
            ->get();

        return $vehicles;
    }
}
