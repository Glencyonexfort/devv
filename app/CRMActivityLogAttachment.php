<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CRMActivityLogAttachment extends Model
{
    protected $table = 'crm_activity_log_attachments';

    public $timestamps = false;

    protected $fillable = [
        'tenant_id', 'log_id', 'attachment_type','attachment_content','created_by','created_at','updated_by','updated_at'
    ];
}
