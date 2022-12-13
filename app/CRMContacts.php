<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CRMContacts extends Model
{
    protected $table = 'crm_contacts';

    protected $fillable = [
        'tenant_id','name', 'description', 'lead_id','created_by','updated_by'
    ];

    public function lead()
    {
        return $this->belongsTo(CRMLeads::class, 'lead_id', 'id');
    }

}
