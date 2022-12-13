<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class JobsCleaningPricing extends Model {

    protected $table = 'jobs_cleaning_pricing';

    use Notifiable;
}
