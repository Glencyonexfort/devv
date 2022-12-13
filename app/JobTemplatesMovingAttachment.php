<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class JobTemplatesMovingAttachment extends Model {

    protected $table = 'job_template_moving_attachments';
    use Notifiable;
}
