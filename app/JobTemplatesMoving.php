<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class JobTemplatesMoving extends Model {

    protected $table = 'job_templates_moving';
    use Notifiable;
}
