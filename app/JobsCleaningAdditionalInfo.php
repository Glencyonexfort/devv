<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class JobsCleaningAdditionalInfo extends Model {

    protected $table = 'jobs_cleaning_additional_info';

    use Notifiable;
}
