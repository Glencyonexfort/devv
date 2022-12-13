<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ListOptions extends Model
{
    use Notifiable;

    protected $table = 'list_options';
    protected $primaryKey  = 'id';

    // public function group()
    // {
    //     return $this->belongsTo('App\MovingInventoryGroups', 'group_id', 'group_id');
    // }
}
