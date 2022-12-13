<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class CustomerDetails extends Model
{
    use Notifiable;
    public $timestamps = false;
   
}
