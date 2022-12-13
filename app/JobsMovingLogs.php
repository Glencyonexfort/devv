<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class JobsMovingLogs extends Model
{
    protected $table = 'jobs_moving_logs';
    protected $primaryKey  = 'id';
    protected $dates = ['log_date'];
    use Notifiable;
    public function sys_log_types()
    {
        return $this->belongsTo('App\SysLogType', 'log_type_id');
    }
    public function users()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}