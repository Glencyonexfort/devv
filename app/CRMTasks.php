<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class CRMTasks extends Model
{
    use Notifiable;
    protected $table = 'crm_tasks';
    
    protected $fillable = [
        'lead_id','tenant_id','user_assigned_id', 'description','task_date','task_time', 'lead_status','updated_by'
    ];

    public function lead()
    {
        return $this->belongsTo(CRMLeads::class, 'lead_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_assigned_id', 'id');
    }

}
