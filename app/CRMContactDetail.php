<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CRMContactDetail extends Model
{
    protected $table = 'crm_contact_details';

    protected $fillable = [
        'tenant_id', 'contact_id', 'detail', 'detail_type','created_by','updated_by'
    ];

}
