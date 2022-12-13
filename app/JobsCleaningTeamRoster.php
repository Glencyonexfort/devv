<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class JobsCleaningTeamRoster extends Model {

    protected $table = 'jobs_cleaning_team_roster';

    use Notifiable;
}
