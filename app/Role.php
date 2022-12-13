<?php namespace App;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
   public $timestamps = false;
    
    protected $fillable = [
        'tenant_id','name','display_name','description','created_at','updated_at'
    ];

    public function permissions(){
       return $this->hasMany(PermissionRole::class, 'role_id');
    }

    public function roleuser(){
       return $this->hasMany(RoleUser::class, 'role_id');
    }
}