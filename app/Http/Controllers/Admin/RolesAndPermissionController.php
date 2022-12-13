<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Module;
use App\Permission;
use App\PermissionRole;
use App\Role;
use App\RoleUser;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RolesAndPermissionController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.rolesAndPermission');
        $this->pageIcon = 'icon-new-tab';
    }

    public function manageRoles()
    {
        $this->roles = Role::where('tenant_id', '=', auth()->user()->tenant_id)->orWhere('tenant_id', '=', '0')->orderBy('tenant_id', 'ASC')->orderBy('display_name', 'ASC')->get();
        return view('admin.role-permission.roles_permission', $this->data);
    }

    public function ajaxCreateRole(Request $request){

        $validatedData = $request->validate([
            'display_name' => 'required',
            'description' => 'required'
        ]);
        $name = strtolower($this->cleanString($request->display_name));
        $data = [
            'display_name' => $request->display_name,
            'name' => $name,
            'description' => $request->description,
            'tenant_id' => auth()->user()->tenant_id,
            'created_at' => Carbon::now()
        ];

        $modal = Role::create($data);

        //-----Response
        $this->roles = Role::where('tenant_id', '=', auth()->user()->tenant_id)->orWhere('tenant_id', '=', '0')->orderBy('tenant_id', 'ASC')->orderBy('display_name', 'ASC')->get();
        $response['error'] = 0;
        $response['message'] = "Role has been created successfully!";
        $response['html'] = view('admin.role-permission.roles_permission_grid', $this->data)->render();
        return json_encode($response);
    }

    public function ajaxUpdateRole(Request $request){

        $validatedData = $request->validate([
            'display_name' => 'required',
            'description' => 'required'
        ]);
        $name = strtolower($this->cleanString($request->display_name));
        $data = [
            'display_name' => $request->display_name,
            'name' => $name,
            'description' => $request->description,
            'updated_at' => Carbon::now()
        ];

        $modal = Role::where('id',$request->id)->update($data);

        //-----Response
        $this->roles = Role::where('tenant_id', '=', auth()->user()->tenant_id)->orWhere('tenant_id', '=', '0')->orderBy('tenant_id', 'ASC')->orderBy('display_name', 'ASC')->get();
        $response['error'] = 0;
        $response['message'] = "Role has been update successfully!";
        $response['html'] = view('admin.role-permission.roles_permission_grid', $this->data)->render();
        return json_encode($response);
    }

    public function ajaxDestroyRole(Request $request){
        $id = $request->id;
        if (RoleUser::where('role_id', '=', $id)->exists()) {
            $response['error'] = 1;
            $response['message'] = "This role is assigned to user(s).";
        }else{
            $modal = Role::where('id', $request->id)->delete();
            $response['error'] = 0;
            $response['message'] = "Role has been deleted successfully!";
        }

        //-----Response
        $this->roles = Role::where('tenant_id', '=', auth()->user()->tenant_id)->orWhere('tenant_id', '=', '0')->orderBy('tenant_id', 'ASC')->orderBy('display_name', 'ASC')->get();        
        $response['html'] = view('admin.role-permission.roles_permission_grid', $this->data)->render();
        return json_encode($response);
    }

    public function rolePermissions($id){
        $this->role = Role::find($id);
        $this->app_modules = Module::where('active','=','1')->get();
        return view('admin.role-permission.permissions', $this->data);
    }

    public function updateRolePermissions(Request $request){

        if($request->has_access=='Y'){
            $permission = PermissionRole::where(['role_id'=>$request->role_id, 'permission_id'=>$request->permission_id, 'tenant_id'=>auth()->user()->tenant_id])->exists();
            if(!$permission){
                $data = new PermissionRole();
                $data->role_id = $request->role_id;
                $data->permission_id = $request->permission_id;
                $data->tenant_id = auth()->user()->tenant_id;
                $data->has_access = 'N';
                $data->save();
            }
        }elseif($request->has_access=='N'){
            $modal = PermissionRole::where(['role_id'=>$request->role_id, 'permission_id'=>$request->permission_id, 'tenant_id'=>auth()->user()->tenant_id])->delete();
        }
        $response['error'] = 0;
        $response['message'] = "Permission has been updated!";
        return json_encode($response);
    }

    protected function cleanString($string) {
        $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.
     
        return preg_replace('/[^A-Za-z\_]/', '', $string); // Removes special chars.
     }
}
