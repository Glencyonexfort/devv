<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class MovingInventoryDefinitions extends Model
{
    use Notifiable;

    protected $table = 'moving_inventory_definitions';
    protected $primaryKey  = 'id';

    // public function group()
    // {
    //     return $this->belongsTo('App\MovingInventoryGroups', 'group_id', 'group_id');
    // }
}
