<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class DevLogs extends Model
{
    protected $table = 'development_logs';
    protected $fillable = [
        'action', 'log', 'created_at'
    ];

    public $timestamps = false;
   
}
