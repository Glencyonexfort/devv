<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class JobsMovingLegsTeam extends Model {

    protected $table = 'jobs_moving_legs_team';
    protected $primaryKey  = 'id';
    protected $guarded = [];

    public $timestamps = false;

    public function isSystemUser()
    {
        $is_system_user = PplPeople::where('id', $this->people_id)->pluck('is_system_user')->first();
        return $is_system_user;
    }
}
