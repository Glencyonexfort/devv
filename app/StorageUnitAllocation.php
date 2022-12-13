<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StorageUnitAllocation extends Model
{
    public $timestamps = false;
    protected $table = 'storage_unit_allocation';

    protected $fillable = [
        'tenant_id','unit_id','job_type','job_id','from_date','to_date',
        'allocation_status','created_by','created_date','updated_date','updated_by'
];
}
