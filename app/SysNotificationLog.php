<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class SysNotificationLog extends Model
{
    protected $table = 'notification_log';
    protected $primaryKey  = 'id';
    public $timestamps = false;
    use Notifiable;
}