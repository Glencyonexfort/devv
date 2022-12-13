<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class PplPeople extends Model
{
	use Notifiable;
    protected $table = 'ppl_people';
    protected $primaryKey  = 'id';
    protected $fillable = [
        'tenant_id','user_id', 'employee_number', 'first_name','last_name','mobile','is_system_user','sys_job_type'
    ];
    
}
