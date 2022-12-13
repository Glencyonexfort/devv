<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class SysNotificationSetting extends Model
{
    protected $table = 'sys_notification_settings';
    protected $primaryKey  = 'id';
    public $timestamps = false;
    use Notifiable;
}