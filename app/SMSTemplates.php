<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SMSTemplates extends Model {

	protected $table = 'sms_templates';

    use Notifiable;
}
