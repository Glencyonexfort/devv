<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class JobsCleaningType extends Model {

    protected $table = 'sys_cleaning_job_types';

    use Notifiable;
}
