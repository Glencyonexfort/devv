<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class EmailSequenceSettings extends Model {

    use Notifiable;
    
    protected $table = 'email_sequence_settings';
}
