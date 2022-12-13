<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class JobsCleaningShifts extends Model {

    protected $table = 'jobs_cleaning_shifts';

    use Notifiable;
}
