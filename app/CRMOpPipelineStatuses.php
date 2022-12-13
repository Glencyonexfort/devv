<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CRMOpPipelineStatuses extends Model
{
    public $timestamps = false;
    protected $table = 'crm_op_pipeline_statuses';

    public function lead()
    {
        return $this->belongsTo(CRMLeads::class, 'lead_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function pipeline()
    {
        return $this->belongsTo(CRMOpPipelines::class, 'pipeline_id', 'id');
    }

}
