<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class JobsCleaningAutoQuoting extends Model {

    protected $table = 'jobs_cleaning_auto_quoting';
    protected $primaryKey  = 'id';    
    protected $dates = ['created_at'];
    use Notifiable;
}
