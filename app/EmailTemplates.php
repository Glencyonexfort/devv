<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class EmailTemplates extends Model {

    use Notifiable;
    protected $table = 'email_templates';

    protected $fillable = [
        'tenant_id','company_id', 'from_email', 'from_email_name','email_template_name',
        'email_subject','email_body','attach_quote','attach_invoice','active','created_at','updated_at'
    ];
}
