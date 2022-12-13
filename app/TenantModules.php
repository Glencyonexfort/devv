<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TenantModules extends Model
{
	public $timestamps = false;
    protected $table = 'tenant_modules';
}
