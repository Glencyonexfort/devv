<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BackLoadingTrips extends Model
{
    protected $table = 'backloading_trips';
    protected $guarded = [];

    public  function scopeLike($query, $field, $value)
    {
        return $query->orWhere($field, 'LIKE', "%$value%");
    }
}
