<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class JobsCleaningTeams extends Model {

    protected $table = 'jobs_cleaning_teams';

    use Notifiable;
}
