<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class JobsMovingRegionToRegionPricing extends Model
{

    protected $table = 'jobs_moving_region_to_region_pricing';
    protected $primaryKey  = 'id';
    protected $dates = ['created_at'];
    use Notifiable;

    public function from_region()
    {
        return $this->belongsTo(JobsMovingPricingRegions::class, 'from_region_id', 'id');
    }

    public function to_region()
    {
        return $this->belongsTo(JobsMovingPricingRegions::class, 'to_region_id', 'id');
    }
}
