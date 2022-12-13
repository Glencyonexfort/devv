<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\RoleUser;
use App\User;
use App\Role;
use App\PplPeople;
use Carbon\Carbon;
use App\Helper\Reply;
use App\Lists;
use Exception;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;


class PeopleOperationsController extends AdminBaseController
{
    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('app.menu.peopleOperationsEmployees');
        $this->pageIcon = 'ti-file';
        //        $this->middleware(function ($request, $next) {
        //            if (!in_array('estimates', $this->user->modules)) {
        //                abort(403);
        //            }
        //            return $next($request);
        //        });
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $this->employees = PplPeople::select('ppl_people.*', 'users.email', 'roles.display_name')
                            ->leftJoin('users', 'ppl_people.user_id', '=', 'users.id')
                            ->leftJoin('role_user', 'ppl_people.user_id', '=', 'role_user.user_id')
                            ->leftJoin('roles', 'role_user.role_id', '=', 'roles.id')
                            ->where(['ppl_people.tenant_id' => auth()->user()->tenant_id, 'ppl_people.deleted' => 'N'])->get();
        //dd($this->employees);
        return view('admin.people-operations.listEmployees', $this->data);
    }

    public function data()
    {
        $employees = PplPeople::select('ppl_people.*', 'users.email', 'roles.display_name')
                            ->leftJoin('users', 'ppl_people.user_id', '=', 'users.id')
                            ->leftJoin('role_user', 'ppl_people.user_id', '=', 'role_user.user_id')
                            ->leftJoin('roles', 'role_user.role_id', '=', 'roles.id')
                            ->where(['ppl_people.tenant_id' => auth()->user()->tenant_id, 'ppl_people.deleted' => 'N'])->get();
         return DataTables::of($employees)
                        ->addColumn('employee_number', function ($row) {
                            return '<h6 style="margin-bottom:0px;"><a href="'. route("admin.list-employees.edit", $row->id) .'" style="color: #6d91ba;">'. $row->employee_number .'</a></h6>';
                            
                        })
                        ->addColumn('action', function ($row) {
                            return '<a class="sa-params" href="javascript:;" data-row-id="' . $row->id . '"><i class="fa fa-times"></i> Delete</a>';
                        })
                        ->rawColumns(['action','employee_number'])
                        ->removeColumn('id')
                        ->removeColumn('tenant_id')
                        ->removeColumn('created_by')
                        ->removeColumn('updated_at')
                        ->removeColumn('updated_by')
                        ->removeColumn('created_at')
                        ->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->employee = PplPeople::select('ppl_people.*', 'users.email', 'users.status', 'roles.display_name', 'roles.id as role_id')
                            ->leftJoin('users', 'ppl_people.user_id', '=', 'users.id')
                            ->leftJoin('role_user', 'ppl_people.user_id', '=', 'role_user.user_id')
                            ->leftJoin('roles', 'role_user.role_id', '=', 'roles.id')
                            ->where(['ppl_people.id' => $id])->first();
        //PplPeople::findOrFail($id);
        $this->roles    = Role::select()->where('tenant_id', '=', auth()->user()->tenant_id)->orWhere('tenant_id', '=', '0')->orderBy('tenant_id', 'ASC')->orderBy('display_name', 'ASC')->get();
        $this->sysModules = Lists::sys_job_type(); 
        return view('admin.people-operations.edit', $this->data);
    }

        /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $employee = PplPeople::findOrFail($id);
        $employee->first_name = $request->input('first_name');
        $employee->last_name = $request->input('last_name');
        $employee->employee_number = $request->input('employee_number');
        $employee->mobile = $request->input('mobile');
        $employee->sys_job_type = $request->input('sys_job_type');
        $employee->is_system_user = ($request->input('is_system_user') == 'Y' ? 'Y' : 'N');
        $employee->email_signature = $request->input('email_signature');
        $employee->updated_at = time();
        $employee->updated_by = $this->user->id;

        if($request->input('is_system_user') == 'Y')
        {
            $user_id = $employee->user_id;
            $role_id = $request->input('role_id');
            $getRoleName = Role::select('name')->where(['id' => $role_id])->first();
    
            if($user_id)
            {
                if ($request->password != '') 
                {
                    $this->validate($request, [
                        'email' => 'required',
                        'password' => 'min:6|required_with:confirm_password|same:confirm_password',
                        'confirm_password' => 'min:6'
                    ]);
                }
                $update_data =[
                    'email' => $request->email,
                    'name' => $request->input('first_name').' '. $request->input('last_name'),
                    'status' => isset($request->status) == 'active' ? 'active' : 'deactive'
                ];
                if($request->input('password')!=""){
                    $update_data['password'] = Hash::make($request->input('password'));
                }
                $user = DB::table('users')->where('id', $user_id)->update($update_data);

                     //add or update user_id
                $employee->user_id = $employee->user_id;

                $role_user = DB::table('role_user')
                                ->where('user_id', $employee->user_id)
                                ->where('tenant_id', auth()->user()->tenant_id)
                                ->first();
                if(!$role_user)
                {
                    DB::table('role_user')->insert(
                        [
                            'tenant_id' => auth()->user()->tenant_id, 
                            'user_id'   => $employee->user_id, 
                            'role_id'   => $request->input('role_id')
                        ]
                    );
                } 
                else 
                {

                    DB::table('role_user')->where(['tenant_id' => auth()->user()->tenant_id, 'user_id'   => $employee->user_id])
                    ->update(['role_id' => $request->input('role_id')]);

                }

            } 
            else 
            {
                $user = new User();
                $user->email = $request->input('email');
                $user->tenant_id = auth()->user()->tenant_id;
                $this->validate($request, [
                    'email' => 'required|unique:users,email',
                    'password' => 'min:6|required_with:confirm_password|same:confirm_password',
                    'confirm_password' => 'min:6'
                ]);
                $user->password = Hash::make($request->input('password'));
                $user->mobile = $request->input('mobile');
                $user->name = $request->input('first_name').' '. $request->input('last_name');
                $user->status = isset($request->status) == 'active' ? 'active' : 'deactive';
                $user->save();

                //add or update user_id
                $employee->user_id = $user->id;

                $role_user = DB::table('role_user')->where('user_id', $user->id)->where('tenant_id', auth()->user()->tenant_id)->first();
                if(!$role_user){
                    DB::table('role_user')->insert(
                        [
                            'tenant_id' => auth()->user()->tenant_id, 
                            'user_id'   => $user->id, 
                            'role_id'   => $request->input('role_id')
                        ]
                    );
                } else {

                    DB::table('role_user')->where(['tenant_id' => auth()->user()->tenant_id, 'user_id'   => $user->id])
                    ->update(['role_id' => $request->input('role_id')]);

                }
            }

            if($getRoleName && $getRoleName->name == 'driver'){
                $this->validate($request, [
                    'sys_job_type' => 'required'
                ],
                [
                    'sys_job_type.required' => 'The System Job Type field is required.'
                ]
                );
            }
        } 
        else 
        {

            $user_id = PplPeople::select('ppl_people.user_id')->where(['ppl_people.id' => $id])->first();

            if($user_id && $user_id->user_id>0){
                $user = User::findOrFail($user_id->user_id);
                $user->status = 'deactive';
                $user->save();
            }
        }
        $employee->save();

        return Reply::redirect(route('admin.list-employees.index'), __('messages.employeeUpdated'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->roles    = Role::select()->where('tenant_id', '=', auth()->user()->tenant_id)->orWhere('tenant_id', '=', '0')->orderBy('tenant_id', 'ASC')->orderBy('display_name', 'ASC')->get();
        $this->sysModules = Lists::sys_job_type(); 
        return view('admin.people-operations.add', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $employee = new PplPeople();
        $employee->first_name = $request->input('first_name');
        $employee->last_name = $request->input('last_name');
        $employee->employee_number = $request->input('employee_number');
        $employee->mobile = $request->input('mobile');
        $employee->tenant_id = auth()->user()->tenant_id;
        $employee->sys_job_type = $request->input('sys_job_type');
        $employee->is_system_user = ($request->input('is_system_user') == 'Y' ? 'Y' : 'N');
        $employee->email_signature = $request->input('email_signature');
        $employee->created_at = time();
        $employee->created_by = $this->user->id;
        
        if($request->input('is_system_user') == 'Y'){
            
            $user = new User();
            $user->tenant_id = auth()->user()->tenant_id;
            $role_id = $request->input('role_id');
            $getRoleName = Role::select('name')->where(['id' => $role_id])->first();

            if($getRoleName && $getRoleName->name == 'driver'){
                $this->validate($request, [
                    'sys_job_type' => 'required'
                ],
                [
                    'sys_job_type.required' => 'The System Job Type field is required.'
                ]
                );
            }

            $this->validate($request, [
                    'email' => 'required|unique:users,email',
                    'password' => 'min:6|required|required_with:confirm_password|same:confirm_password',
                    'confirm_password' => 'min:6|required'
                ]);

            
            $user->password = Hash::make($request->input('password'));
            $user->mobile = $request->input('mobile');
            $user->name = $request->input('first_name').' '. $request->input('last_name');
            $user->email = $request->input('email');
            $user->status = ($request->input('status') == 'active' ? 'active' : 'deactive');
            $user->save();

            //add or update user_id
            $employee->user_id = $user->id;

            $role_user = DB::table('role_user')->where('user_id', $user->id)->where('tenant_id', auth()->user()->tenant_id)->first();
            if(!$role_user){
                DB::table('role_user')->insert(
                    [
                        'tenant_id' => auth()->user()->tenant_id, 
                        'user_id'   => $user->id, 
                        'role_id'   => $request->input('role_id')
                    ]
                );
            } else {

                DB::table('role_user')->where(['tenant_id' => auth()->user()->tenant_id, 'user_id'   => $user->id])
                ->update(['role_id' => $request->input('role_id')]);

            }
        } else {
            $employee->user_id = '0';
        }
        $employee->save();

        return Reply::redirect(route('admin.list-employees.index'), __('messages.employeeAdded'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) 
    {
        $employee = PplPeople::findOrFail($id);
        $employee->deleted = 'Y';
        $employee->save();

        $user_id = PplPeople::select('ppl_people.user_id')->where(['ppl_people.id' => $id])->first();

        if($user_id && $user_id->user_id>0){
            $user = User::findOrFail($user_id->user_id);
            $user->status = 'deactive';
            $user->save();
        }
        return Reply::success(__('messages.employeeDeleted'));
    }
}
