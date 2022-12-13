<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class MovingInventoryGroups extends Model
{
    use Notifiable;

    protected $table = 'moving_inventory_groups';
    protected $primaryKey  = 'id';

    // public function definitions()
    // {
    //     return $this->hasMany('App\MovingInventoryDefinitions');
    // }
}
