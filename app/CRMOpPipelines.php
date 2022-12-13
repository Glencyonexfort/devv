<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CRMOpPipelines extends Model
{
    public $timestamps = false;
    protected $table = 'crm_op_pipelines';

    public function pipeline_status()
    {
        return $this->hasMany(CRMOpPipelineStatuses::class, 'pipeline_id','id');
    }
}
