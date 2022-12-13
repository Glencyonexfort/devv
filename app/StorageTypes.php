<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StorageTypes extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'tenant_id','name','inside_cubic_capacity','max_gross_weight_kg','tare_weight_kg',
        'ext_length_m','ext_width_m','ext_height_m','int_length_m','int_width_m','int_height_m'
        ,'active','created_date','updated_date','created_by','updated_by'
    ];
}
