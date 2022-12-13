<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class TestTable extends Model
{

    protected $table = 'test_table';
    public $timestamps = false;
    // use Notifiable;
}
