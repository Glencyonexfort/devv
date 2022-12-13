<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CRMOpportunities extends Model
{
    protected $table = 'crm_opportunities';

    protected $fillable = [
        'tenant_id', 'lead_id', 'op_type', 'op_status', 'est_job_date',
        'confidence', 'value', 'op_frequency', 'contact_id', 'user_id','created_by','updated_by','updated_at','notes'
    ];

    public function lead()
    {
        return $this->belongsTo(CRMLeads::class);
    }
}
