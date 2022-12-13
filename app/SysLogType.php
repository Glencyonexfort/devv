<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class SysLogType extends Model
{
    protected $table = 'sys_log_types';
    protected $primaryKey  = 'id';
    use Notifiable;
}