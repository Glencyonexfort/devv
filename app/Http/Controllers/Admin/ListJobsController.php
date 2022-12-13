<?php

namespace App\Http\Controllers\Admin;

use App\Companies;
use App\CRMActivityLog;
use App\CRMContactDetail;
use App\CRMContacts;
use App\CRMLeads;
use App\CRMOpportunities;
use App\CRMTasks;
use App\CustomerDetails;
use App\Customers;
use App\EmailTemplates;
use App\EmployeeDetails;
use App\Event;
use App\Helper\Reply;
use App\Http\Requests\Admin\User\StoreUser;
use App\Http\Requests\Admin\User\UpdateDriver;
use App\Http\Requests\ListJobs\StoreNewJob;
use App\Invoice;
use App\InvoiceItems;
use App\InvoiceSetting;
use App\JobsCleaning;
use App\JobsMoving;
use App\JobsMovingInventory;
use App\JobsMovingLegs;
use App\JobsMovingLegTrips;
use App\JobsMovingLegsTeam;
use App\JobsMovingLogs;
use App\JobTemplatesMoving;
use App\JobTemplatesMovingAttachment;
use App\Lists;
use App\ListTypes;
use App\Mail\CustomerMail;
use App\MovingInsuranceQuoteRequest;
use App\MovingInsuranceQuoteResponse;
use App\MovingInventoryDefinitions;
use App\MovingInventoryGroups;
use App\OfflinePaymentMethod;
use App\OrganisationSettings;
use App\Payment;
use App\PplPeople;
use App\Product;
use App\PropertyCategoryOptions;
use App\QuoteItem;
use App\Quotes;
use App\Role;
use App\Setting;
use App\SMSTemplates;
use App\StorageTypes;
use App\StorageUnitAllocation;
use App\SysCountryStates;
use App\SysNotificationLog;
use App\SysNotificationSetting;
use App\Tax;
use App\TenantApiDetail;
use App\User;
use App\Vehicles;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Intervention\Image\Facades\Image;
use Stripe\Stripe;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\DataExport;
use App\JobsMovingPricingAdditional;
use App\VehicleGroups;
// use Maatwebsite\Excel\Excel;
use Excel;
use App\VehicleUnavailability;
use Illuminate\Support\Str;

class ListJobsController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.list_jobs');
        $this->pageIcon = 'ti-file';
    }

    public function index()
    {
        $this->job_status = Lists::job_status();
        $this->payment_status = Lists::payment_status();
        return view('admin.list-jobs.index', $this->data);
    } 

    public function new_job()
    {
        try {
            $this->companies = Companies::where('tenant_id', '=', auth()->user()->tenant_id)->get();
            $this->job_status = Lists::job_status();
            $this->job_type = Lists::job_type();
            $this->price_structure = Lists::price_structure();
            $this->payment_status = Lists::payment_status();
            $this->lead_info = Lists::lead_info();
            $this->vehicles = Vehicles::select('id', 'vehicle_name')->where('tenant_id', '=', auth()->user()->tenant_id)->get();
            $this->job_templates = JobTemplatesMoving::where('tenant_id', '=', auth()->user()->tenant_id)->get();
            return view('admin.list-jobs.new-job', $this->data);
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function edit_job($job_id)
    {
        try {
            if (empty($job_id)) {
                    return redirect(route('admin.list-jobs.index'));
            }
            $this->job = JobsMoving::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            $this->customer = CRMLeads::findOrFail($this->job->customer_id);
            /* removed this from above line to fix point 6 in the word document shared by Anvar   ->first()  */
            $this->companies = Companies::where('tenant_id', '=', auth()->user()->tenant_id)->get();
            $this->job_status = Lists::job_status();
            $this->job_type = Lists::job_type();
            $this->price_structure = Lists::price_structure();
            $this->payment_status = Lists::payment_status();
            $this->lead_info = Lists::lead_info();
            $this->vehicles = Vehicles::select('id', 'vehicle_name')
                ->where('tenant_id', '=', auth()->user()->tenant_id)
                ->get();
            $this->job_logs = JobsMovingLogs::where('tenant_id', '=', auth()->user()->tenant_id)
                ->where('job_id', $job_id)
                ->orderBy('log_date', 'desc')
                ->get();
            $this->jobs_moving_legs = JobsMovingLegs::where('tenant_id', '=', auth()->user()->tenant_id)
                ->where('job_id', $job_id)
                ->orderBy('created_at', 'desc')
                ->first();
            $this->job_templates = JobTemplatesMoving::where('tenant_id', '=', auth()->user()->tenant_id)->get();
            // $this->job->job_date = Carbon::createFromFormat($this->global->date_format, $this->job->job_date)->toDateString();
            return view('admin.list-jobs.edit-job', $this->data);
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function changeStatus(Request $request, $job_leg_id)
    {
        try {
            $obj = JobsMovingLegs::find($job_leg_id);
            $obj->leg_status = $request->input('leg_status');
            $obj->save();
                return Reply::redirect(route('admin.list-jobs.edit-job', [$obj->job_id]), __('messages.jobLegsStatusChanges'));
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function destroyJob($id)
    {
        $job = JobsMoving::findOrFail($id);
        $job->deleted = '1';
        $job->save();

        return Reply::success(__('messages.jobDeleted'));
    }

    public function drivers()
    {
        $this->pageTitle = __('app.menu.driverList');
        $this->drivers = User::allDrivers();
        return view('admin.list-jobs.list-drivers', $this->data);
    }

    public function createDriver()
    {
        $this->pageTitle = __('app.menu.addDriver');
        $employee = new EmployeeDetails();
        $this->fields = $employee->getCustomFieldGroupsWithFields()->fields;
        return view('admin.list-jobs.create-driver', $this->data);
    }

    public function editDriver($driver_id)
    {
        try {
            if (empty($driver_id)) {
                return redirect(route('admin.list-jobs.driver-list'));
            }
            $this->pageTitle = __('app.menu.editDriver');
            $this->driver = User::where('id', '=', $driver_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            //$this->fields = $employee->getCustomFieldGroupsWithFields()->fields;
            return view('admin.list-jobs.edit-driver', $this->data);
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function updateDriver(UpdateDriver $request, $id)
    {
        $user = User::withoutGlobalScope('active')->findOrFail($id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        if ($request->password != '') {
            $user->password = Hash::make($request->input('password'));
        }
        //$user->mobile = $request->input('mobile');
        //$user->gender = $request->input('gender');
        // $user->status = $request->input('status');
        //$user->login = $request->login;
        $user->save();
        return Reply::redirect(route('admin.list-jobs.driver-list'), __('messages.driverUpdated'));

    }

    public function storeDriver(StoreUser $request)
    {
        $user = new User();
        $user->tenant_id = auth()->user()->tenant_id;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->save();

        $role_user = \Illuminate\Support\Facades\DB::table('role_user')->where('user_id', $user->id)
                                                    ->where('role_id', 4)
                                                    ->where(['tenant_id' => auth()->user()->tenant_id])
                                                    ->first();
        if (!$role_user) {
            \Illuminate\Support\Facades\DB::table('role_user')->insert(
                [
                    'tenant_id' => auth()->user()->tenant_id,
                    'user_id' => $user->id,
                    'role_id' => 4,
                ]
            );
        }

        $employeeDetails = new EmployeeDetails();
        $employeeDetails->tenant_id = auth()->user()->tenant_id;
        $employeeDetails->user_id = $user->id;
        $employeeDetails->job_title = 'Driver';
        $employeeDetails->joining_date = date('Y-m-d H:i:s');
        $employeeDetails->save();

        // Notify User
        //        $user->notify(new NewUser($request->input('password')));

        // $this->logSearchEntry($user->id, $user->name, 'admin.employees.show');

        return Reply::redirect(route('admin.list-jobs.driver-list'), __('messages.driverAdded'));
    }

    public function driversData(Request $request)
    {
        if ($request->role != 'all' && $request->role != '') {
            $userRoles = Role::findOrFail($request->role);
        }

        //DB::enableQueryLog();//enable query logging
        $users = User::with('role')
            ->withoutGlobalScope('active')
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email', 'users.created_at', 'roles.name as roleName', 'roles.id as roleId', 'users.image', 'users.status', \DB::raw("(select user_roles.role_id from role_user as user_roles where user_roles.user_id = users.id ORDER BY user_roles.role_id DESC limit 1) as `current_role`"))
            ->where('role_user.role_id', '=', '4')
            ->where('users.tenant_id', '=', auth()->user()->tenant_id)
            ->where('roles.name', '<>', 'client');

        /* if ($request->status != 'all' && $request->status != '') {
        $users = $users->where('users.status', $request->status);
        }

        if ($request->employee != 'all' && $request->employee != '') {
        $users = $users->where('users.id', $request->employee);
        }

        if ($request->role != 'all' && $request->role != '' && $userRoles) {
        if ($userRoles->name == 'admin') {
        $users = $users->where('roles.id', $request->role);
        } elseif ($userRoles->name == 'employee') {
        $users = $users->where(\DB::raw("(select user_roles.role_id from role_user as user_roles where user_roles.user_id = users.id ORDER BY user_roles.role_id DESC limit 1)"), $request->role)
        ->having('roleName', '<>', 'admin');
        } else {
        $users = $users->where(\DB::raw("(select user_roles.role_id from role_user as user_roles where user_roles.user_id = users.id ORDER BY user_roles.role_id DESC limit 1)"), $request->role);
        }
        }
        if ($request->skill != 'all' && $request->skill != '' && $request->skill != null && $request->skill != 'null') {
        $users = $users->join('employee_skills', 'employee_skills.user_id', '=', 'users.id')
        ->whereIn('employee_skills.skill_id', explode(',', $request->skill));
        }*/

        $users = $users->groupBy('users.id')->get();

        $roles = Role::where('name', '=', 'driver')->where(['tenant_id' => auth()->user()->tenant_id])->get();
        return DataTables::of($users)
            ->addColumn('role', function ($row) use ($roles) {
                $roleRow = '';
                if ($row->id != 1) {

                    $flag = 0;
                    foreach ($roles as $role) {

                        $roleRow .= '<div class="radio radio-info">
                              <input type="radio" name="role_' . $row->id . '" class="assign_role" data-user-id="' . $row->id . '"';

                        foreach ($row->role as $urole) {

                            if ($role->id == $urole->role_id && $flag == 0) {
                                $roleRow .= ' checked ';

                                if ($role->name == 'admin') {
                                    $flag = 1; //do not check any other role for user if is admin
                                }
                            }
                        }

                        if ($role->id <= 4) {
                            $roleRow .= 'id="none_role_' . $row->id . $role->id . '" data-role-id="' . $role->id . '" value="' . $role->id . '"> <label for="none_role_' . $row->id . $role->id . '" data-role-id="' . $role->id . '" data-user-id="' . $row->id . '">' . __('app.' . $role->name) . '</label></div>';
                        } else {
                            $roleRow .= 'id="none_role_' . $row->id . $role->id . '" data-role-id="' . $role->id . '" value="' . $role->id . '"> <label for="none_role_' . $row->id . $role->id . '" data-role-id="' . $role->id . '" data-user-id="' . $row->id . '">' . ucwords($role->name) . '</label></div>';
                        }

                        $roleRow .= '<br>';
                    }
                    return $roleRow;
                } else {
                    return __('messages.roleCannotChange');
                }
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('admin.list-jobs.edit-driver', [$row->id]) . '" class="btn btn-info btn-circle"
                      data-toggle="tooltip" data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>

                      <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-user-id="' . $row->id . '" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a>';
            })
            ->editColumn(
                'created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format($this->global->date_format);
                }
            )
            ->editColumn(
                'status', function ($row) {
                    if ($row->status == 'active') {
                        return '<label class="label label-success">' . __('app.active') . '</label>';
                    } else {
                        return '<label class="label label-danger">' . __('app.inactive') . '</label>';
                    }
                }
            )
            ->editColumn('name', function ($row) use ($roles) {
                $image = ($row->image) ? '<img src="' . asset('user-uploads/avatar/' . $row->image) . '"
                                                            alt="user" class="img-circle" width="30"> ' : '<img src="' . asset('default-profile-2.png') . '"
                                                            alt="user" class="img-circle" width="30"> ';
                if (0) {

                    return $image . ' ' . ucwords($row->name) . '<br><br> <label class="label label-danger">' . __('app.admin') . '</label>';
                } else {
                    foreach ($roles as $role) {
                        foreach ($row->role as $urole) {

                            if ($role->id == $urole->role_id && $role->id != 2) {
                                return $image . ' ' . ucwords($row->name) . '<br><br> <label class="label label-info">' . ucwords($role->name) . '</label>';
                            }
                        }
                    }
                    return $image . ' ' . ucwords($row->name) . '<br><br> <label class="label label-warning">' . __('app.employee') . '</label>';
                }
                return $image . ' ' . ucwords($row->name);
            })
            ->rawColumns(['name', 'action', 'role', 'status'])
            ->removeColumn('roleId')
            ->removeColumn('roleName')
            ->removeColumn('current_role')
            ->make(true);
    }

    public function destroyDriver($id)
    {
        $user = User::withoutGlobalScope('active')->findOrFail($id);

        if ($user->id == 1) {
            return Reply::error(__('messages.adminCannotDelete'));
        }

        User::destroy($id);
        return Reply::success(__('messages.driverDeleted'));
    }

    public function store(StoreNewJob $request)
    {
        try {

            if ($request->input('first_name') != '' || $request->input('first_name') != null) {

                $res = JobsMoving::select(DB::raw('MAX(job_number) as max_job_number'))->where('jobs_moving.tenant_id', '=', auth()->user()->tenant_id)->first();
                $new_job_number = intval($res->max_job_number) + 1;

                $customer = new Customers();
                $customer->first_name = $request->input('first_name');
                $customer->last_name = $request->input('last_name');
                $customer->phone = $request->input('phone');
                $customer->mobile = $request->input('mobile');
                $customer->email = $request->input('email');
                $customer->created_at = time();
                $customer->updated_at = time();
                $customer->tenant_id = auth()->user()->tenant_id;
                $customer->save();
                $customer_id = $customer->id;

                $obj = new JobsMoving();
                $obj->job_number = $new_job_number;
                $obj->customer_id = $customer_id;
                $obj->company_id = $request->input('company_id');
                $obj->total_cbm = $request->input('total_cbm');
                $obj->job_status = $request->input('job_status');
                $obj->job_type = $request->input('job_type');
                $obj->job_date = Carbon::createFromFormat($this->global->date_format, $request->input('job_date'))->format('Y-m-d');
                $obj->goods_value = $request->input('goods_value');
                $obj->pickup_address = $request->input('pickup_address');
                $obj->pickup_property_type = $request->input('pickup_property_type');
                $obj->pickup_bedrooms = $request->input('pickup_bedrooms');
                $obj->pickup_access_restrictions = $request->input('pickup_access_restrictions');
                $obj->drop_off_address = $request->input('drop_off_address');
                $obj->drop_off_property_type = $request->input('drop_off_property_type');
                $obj->drop_off_bedrooms = $request->input('drop_off_bedrooms');
                $obj->drop_off_access_restrictions = $request->input('drop_off_access_restrictions');
                $obj->price_structure = $request->input('price_structure');
                $obj->pickup_suburb = $request->input('pickup_suburb');
                $obj->delivery_suburb = $request->input('delivery_suburb');
                $obj->fixed_other_rate = $request->input('fixed_other_rate');
                $obj->hourly_rate = $request->input('hourly_rate');
                $obj->total_amount = $request->input('total_amount');
                $obj->deposit_agreed = $request->input('deposit_required');
                $obj->payment_instructions = $request->input('payment_instructions');
                $obj->lead_info = $request->input('lead_info');
                $obj->vehicle_id = $request->input('vehicle_id');
                $obj->other_instructions = $request->input('other_instructions');
                $obj->job_template_id = $request->input('job_template');
                $obj->created_at = time();
                $obj->updated_at = time();
                //$customer->created_by = auth()->user()->id;
                $obj->created_by = auth()->user()->id;
                $obj->tenant_id = auth()->user()->tenant_id;
                $job_template = JobTemplatesMoving::find($request->input('job_template'));
                if ($job_template) {
                    $obj->pickup_instructions = $job_template->pickup_instructions;
                    $obj->drop_off_instructions = $job_template->drop_off_instructions;
                    $obj->payment_instructions = $job_template->payment_instructions;
                    $obj->other_instructions = $job_template->other_instructions;
                }

                $obj->save();
                //dd($obj);

                $moving_attachment = \App\JobTemplatesMovingAttachment::where(['job_template_id' => $obj->job_template_id, 'tenant_id' => auth()->user()->tenant_id])
                    ->first();
                if ($moving_attachment) {
                    $log = new JobsMovingLogs();
                    $log->tenant_id = auth()->user()->tenant_id;
                    $log->job_id = $obj->job_id;
                    $log->user_id = auth()->user()->id;
                    $log->log_type_id = 10;
                    $log->log_details = $moving_attachment->attachment_file_name;
                    $log->log_date = time();
                    $log->created_at = time();
                    $log->updated_at = time();
                    $log->save();
                }

                $log = new JobsMovingLogs();
                $log->tenant_id = auth()->user()->tenant_id;
                $log->job_id = $obj->job_id;
                $log->user_id = auth()->user()->id;
                $log->log_type_id = 1;
                $log->log_details = 'Job Created';
                $log->log_date = time();
                $log->created_at = time();
                $log->updated_at = time();
                $log->save();
                return Reply::redirect(route('admin.list-jobs.edit-job', [$obj->job_id]), __('messages.newJobCreated'));
            }
        } catch (\Exception $ex) {
            return Reply::redirect(route('admin.list-jobs.edit-job', [$obj->job_id]), $ex->getMessage());
            dd($ex->getMessage());
        }
    }

    public function update(StoreNewJob $request, $job_id)
    {
        try {
            $obj = JobsMoving::findOrFail($job_id);
            $obj->company_id = $request->input('company_id');
            $obj->total_cbm = $request->input('total_cbm');
            $obj->goods_value = $request->input('goods_value');
            $obj->job_status = $request->input('job_status');
            $obj->job_type = $request->input('job_type');
            $obj->job_date = Carbon::createFromFormat($this->global->date_format, $request->input('job_date'))->format('Y-m-d');
            $obj->pickup_address = $request->input('pickup_address');
            $obj->pickup_property_type = $request->input('pickup_property_type');
            $obj->pickup_bedrooms = $request->input('pickup_bedrooms');
            $obj->pickup_access_restrictions = $request->input('pickup_access_restrictions');
            $obj->drop_off_address = $request->input('drop_off_address');
            $obj->drop_off_property_type = $request->input('drop_off_property_type');
            $obj->drop_off_bedrooms = $request->input('drop_off_bedrooms');
            $obj->drop_off_access_restrictions = $request->input('drop_off_access_restrictions');
            $obj->price_structure = $request->input('price_structure');
            $obj->pickup_suburb = $request->input('pickup_suburb');
            $obj->delivery_suburb = $request->input('delivery_suburb');
            $obj->fixed_other_rate = $request->input('fixed_other_rate');
            $obj->hourly_rate = $request->input('hourly_rate');
            $obj->total_amount = $request->input('total_amount');
            $obj->deposit_agreed = $request->input('deposit_required');
            $obj->payment_instructions = $request->input('payment_instructions');
            $obj->lead_info = $request->input('lead_info');
            $obj->vehicle_id = $request->input('vehicle_id');
            $obj->other_instructions = $request->input('other_instructions');
            $obj->updated_at = time();
            $obj->save();

            $customer = Customers::findOrFail($obj->customer_id);
            $customer->first_name = $request->input('first_name');
            $customer->last_name = $request->input('last_name');
            $customer->phone = $request->input('phone');
            $customer->mobile = $request->input('mobile');
            $customer->email = $request->input('email');
            $customer->updated_at = time();
            $customer->save();

            $log = new JobsMovingLogs();
            $log->tenant_id = auth()->user()->tenant_id;
            $log->job_id = $job_id;
            $log->user_id = auth()->user()->id;
            $log->log_type_id = '2';
            $log->log_details = 'Job Modified';
            $log->log_date = time();
            $log->save();
            return Reply::redirect(route('admin.list-jobs.edit-job', [$job_id]), __('messages.newJobUpdated'));
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
        return Reply::redirect(route('admin.job-templates.index'), __('messages.newJobUpdated'));
    }

    public function data(Request $request)
    {
        try {
            $result = JobsMoving::select(
                'jobs_moving.job_id',
                'jobs_moving.job_number',
                'crm_leads.id as lead_id',
                'crm_leads.name',
                'jobs_moving.job_date',
                'jobs_moving.job_start_time',
                'jobs_moving.pickup_suburb',
                'jobs_moving.pickup_state',
                'jobs_moving.delivery_suburb',
                'jobs_moving.drop_off_state',
                'jobs_moving.total_cbm',
                'jobs_moving.job_status',
                'jobs_moving.payment_instructions',
                'jobs_moving.rate_per_cbm',
                'jobs_moving.lead_info'
            )
                ->join('crm_leads', 'crm_leads.id', 'jobs_moving.customer_id')
                ->leftjoin('invoices', 'invoices.job_id', 'jobs_moving.job_id');
            $result = $result->where(['jobs_moving.tenant_id' => auth()->user()->tenant_id, 'jobs_moving.opportunity' => 'N'])
                ->orderBy('jobs_moving.job_id', 'desc');

                if ($request->created_date_start !== null && $request->created_date_start != 'null' && $request->created_date_start != '') {
                    $startDate = Carbon::createFromFormat($this->global->date_format, $request->created_date_start)->toDateString();
                    $result = $result->where(DB::raw('DATE(jobs_moving.`created_at`)'), '>=', $startDate);
                }
                if ($request->created_date_end !== null && $request->created_date_end != 'null' && $request->created_date_end != '') {
                    $created_date_end = Carbon::createFromFormat($this->global->date_format, $request->created_date_end)->toDateString();
                    $result = $result->where(DB::raw('DATE(jobs_moving.`created_at`)'), '<=', $created_date_end);
                }
                if ($request->removal_date_start !== null && $request->removal_date_start != 'null' && $request->removal_date_start != '') {
                    $startDate = Carbon::createFromFormat($this->global->date_format, $request->removal_date_start)->toDateString();
                    $result = $result->where(DB::raw('DATE(jobs_moving.`job_date`)'), '>=', $startDate);
                }
                if ($request->removal_date_end !== null && $request->removal_date_end != 'null' && $request->removal_date_end != '') {
                    $removal_date_end = Carbon::createFromFormat($this->global->date_format, $request->removal_date_end)->toDateString();
                    $result = $result->where(DB::raw('DATE(jobs_moving.`job_date`)'), '<=', $removal_date_end);
                }
                if ($request->job_status !== null && $request->job_status != 'null' && $request->job_status != '') {
                    $job_status = explode(",", $request->job_status);
                    $result = $result->wherein('jobs_moving.job_status', $job_status);
                }
                if ($request->payment_status !== null && $request->payment_status != 'null' && $request->payment_status != '') {
                    $payment_status = explode(",", $request->payment_status);
                    $result = $result->wherein('invoices.status', $payment_status);
                }

                if ($request->hide_deleted_archived !== null && $request->hide_deleted_archived != 'null' && $request->hide_deleted_archived == '1') {
                    $result = $result->where('jobs_moving.deleted','=', 0);
                }

            $result = $result->groupBy('jobs_moving.job_id')->get();
            return DataTables::of($result)
                ->addColumn('customer_name', function ($row) {
                    return $row->name;
                })
                ->addColumn('job_date', function ($row) {
                    return date('Y/m/d', strtotime($row->job_date));
                })
                ->addColumn('job_time', function ($row) {
                    return date('h:i A', strtotime($row->job_start_time));
                })
                ->addColumn('pickup_address', function ($row) {
                    if (isset($row->pickup_suburb) || $row->pickup_state) {
                        return $row->pickup_suburb . ',' . $row->pickup_state;
                    } else {
                        return '';
                    }
                })
                ->addColumn('delivery_address', function ($row) {
                    if (isset($row->delivery_suburb) || isset($row->drop_off_state)) {
                        return $row->delivery_suburb . ',' . $row->drop_off_state;
                    } else {
                        return '';
                    }
                })
                ->addColumn('email', function ($row) {
                    if (isset($row->lead_id)) {
                        $email = CRMContacts::select('crm_contact_details.detail')
                            ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                            ->where(['crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.lead_id' => $row->lead_id, 'crm_contact_details.detail_type' => 'Email', 'crm_contacts.deleted' => 'N'])
                            ->pluck('detail')
                            ->first();
                        return $email;
                    } else {
                        return '';
                    }
                })
                ->addColumn('mobile', function ($row) {
                    if (isset($row->lead_id)) {
                        $mobile = CRMContacts::select('crm_contact_details.detail')
                            ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                            ->where(['crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.lead_id' => $row->lead_id, 'crm_contact_details.detail_type' => 'Mobile', 'crm_contacts.deleted' => 'N'])
                            ->pluck('detail')
                            ->first();
                        return $mobile;
                    } else {
                        return '';
                    }
                })
                ->addColumn('balance_payment', function ($row) {
                    $amount = 0;
                    $invoice = Invoice::where(['job_id' => $row->job_id, 'sys_job_type' => 'Moving'])
                        ->first();
                    if ($invoice) {
                        $sum_invoice_items = $invoice->getTotalAmount();
                        $sum_payment = \App\Payment::where('invoice_id', $invoice->id)
                            ->sum('amount');
                        $amount = $sum_invoice_items - $sum_payment;
                    }
                    return "$".number_format((float)$amount, 2, '.', '');
                })
                ->addColumn('payment_status', function ($row) {
                    $status = "";
                    $invoice = Invoice::where(['job_id' => $row->job_id, 'sys_job_type' => 'Moving'])
                        ->first();
                    if ($invoice) {
                        $sum_invoice_items = $invoice->getTotalAmount();
                        $sum_payment = \App\Payment::where('invoice_id', $invoice->id)
                            ->sum('amount');
                            $status = html_entity_decode($this->paymentStatusDataTable($sum_invoice_items, $sum_payment));
                    }
                    return $status;
                })
                ->editColumn('job_number', function ($row) {
                    return '<a class="badge bg-blue" href="' . route("admin.list-jobs.view-job", $row->job_id) . '" >' . $row->job_number . '</a>';
                })
                ->addColumn('action', function ($row) {
                    return '<a class="sa-params badge badge-danger" href="javascript:;" data-row-id="' . $row->job_id . '"><i class="fa fa-trash"></i></a>';
                })
                ->rawColumns(['action', 'job_number'])
                ->make(true);
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function excel(Request $request)
    {
        $data = DB::table('jobs_moving_legs')->select('jobs_moving.job_id',
                                                    'jobs_moving.job_number',
                                                    'crm_leads.name AS customer_name',
                                                    'jobs_moving.job_date',
                                                    'jobs_moving.customer_id',
                                                    'jobs_moving.delivery_suburb',
                                                    'jobs_moving_legs.*',
                                                    )
                        ->join('jobs_moving', 'jobs_moving.job_id', '=', 'jobs_moving_legs.job_id')
                        ->leftjoin('crm_leads', 'crm_leads.id', 'jobs_moving.customer_id')
                        ->where('jobs_moving_legs.tenant_id', auth()->user()->tenant_id)
                        ->orderBy('jobs_moving_legs.job_id', 'ASC');
                        

                    if ($request->created_date_start !== null && $request->created_date_start != 'null' && $request->created_date_start != '') {
                        $startDate = Carbon::createFromFormat($this->global->date_format, $request->created_date_start)->toDateString();
                        $data = $data->where(DB::raw('DATE(jobs_moving.`created_at`)'), '>=', $startDate);
                    }
                    if ($request->created_date_end !== null && $request->created_date_end != 'null' && $request->created_date_end != '') {
                        $created_date_end = Carbon::createFromFormat($this->global->date_format, $request->created_date_end)->toDateString();
                        $data = $data->where(DB::raw('DATE(jobs_moving.`created_at`)'), '<=', $created_date_end);
                    }
                    if ($request->removal_date_start !== null && $request->removal_date_start != 'null' && $request->removal_date_start != '') {
                        $startDate = Carbon::createFromFormat($this->global->date_format, $request->removal_date_start)->toDateString();
                        $data = $data->where(DB::raw('DATE(jobs_moving.`job_date`)'), '>=', $startDate);
                    }
                    if ($request->removal_date_end !== null && $request->removal_date_end != 'null' && $request->removal_date_end != '') {
                        $removal_date_end = Carbon::createFromFormat($this->global->date_format, $request->removal_date_end)->toDateString();
                        $data = $data->where(DB::raw('DATE(jobs_moving.`job_date`)'), '<=', $removal_date_end);
                    }
                    if ($request->job_status !== null && $request->job_status != 'null' && $request->job_status != '') {
                        $job_status = explode(",", $request->job_status);
                        $data = $data->wherein('jobs_moving.job_status', $job_status);
                    }
                    // if ($request->payment_status !== null && $request->payment_status != 'null' && $request->payment_status != '') {
                    //     $payment_status = explode(",", $request->payment_status);
                    //     $data = $data->wherein('invoices.status', $payment_status);
                    // }
    
                    if ($request->hide_deleted_archived !== null && $request->hide_deleted_archived != 'null' && $request->hide_deleted_archived == '1') {
                        $data = $data->where('jobs_moving.deleted','=', 0);
                    }

                $data = $data->get()->toArray();

                // dd($data);
                $drivers = User::driverList();
                // dd($this->drivers);
            

                $display[] = array(
                            'Job id',
                            'Job Number', 
                            'Customer Name', 
                            'Customer Mobile', 
                            'Customer Email', 
                            'Job Date', 
                            'Leg No', 
                            'Actual Start Time', 
                            'Actual End Time', 
                            'Pickup Address', 
                            'Delivery Address', 
                            'Vahicle Name',
                            'Driver Name', 
                            'Invoice number',
                            'Invoice Status',
                            'Invoice Total',
                            'Payment Total',
                            'Invoice Item 1',
                            'Invoice Item 1 amount',
                            'Invoice Item 2',
                            'Invoice Item 2 amount',
                            'Invoice Item 3',
                            'Invoice Item 3 amount',
                            'Invoice Item 4',
                            'Invoice Item 4 amount',
                            'Invoice Item 5',
                            'Invoice Item 5 amount',
                            'Invoice Item 6',
                            'Invoice Item 6 amount',
                            'Invoice Item 7',
                            'Invoice Item 7 amount',
                            'Invoice Item 8',
                            'Invoice Item 8 amount'
                    );
        
        $id = $data[0]->job_id;
        $count = 0;

        foreach($data as $job)
        {
            $driver = null;
            $mobile = CRMContacts::select('crm_contact_details.detail')
                            ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                            ->where(['crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.lead_id' => $job->customer_id, 'crm_contact_details.detail_type' => 'Mobile'])
                            ->pluck('detail')
                            ->first();
            $email = CRMContacts::select('crm_contact_details.detail')
                            ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                            ->where(['crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.lead_id' => $job->customer_id, 'crm_contact_details.detail_type' => 'Email'])
                            ->pluck('detail')
                            ->first();
        
            $vehicleName = Vehicles::where('id', $job->vehicle_id)->pluck('vehicle_name')->first();
            if($job->driver_id)
            {
                foreach($drivers as $d)
                {
                    if($d->id == $job->driver_id)
                    {
                        $driver = $d->name;
                        break;
                    }
                }
            }
            // dd($driver);

            $invoice = Invoice::where('job_id', $job->job_id)->first();
            $items = null;
            $invoice_total = null;
            $totalPayment = null;
            if($invoice)
            {
                // dd($invoice);
                $items = InvoiceItems::where('invoice_id', $invoice->id)->get();
                if (isset($invoice->id) && $invoice->id>0){
                    $totalPayment = $invoice->getPaidAmount();
                    $invoice_total = $invoice->getTotalAmount();
                }
            }
            if($id == $job->job_id)
            {
                    if($count == 0)
                    {
                        $display[] = array(
                            'Job id' => $job->job_id,
                            'Job Number' => $job->job_number,
                            'customer Name' => $job->customer_name,
                            'Customer Mobile' => $mobile,
                            'Customer Email' => $email,
                            'Job Date' => $job->job_date,
                            'Leg No' => $job->leg_number,
                            'Actual Start Time' => $job->actual_start_time,
                            'Actual End Time' => $job->actual_finish_time,
                            'Pickup Address' => $job->pickup_address,
                            'Delivery Address' => $job->delivery_suburb,
                            'Vahicle Name' => $vehicleName,
                            'Driver Name' => ($driver) ? $driver : '',
                            'Invoice number' => ($items->count()) ? $invoice->invoice_number : '',
                            'Invoice Status' => ($invoice) ? $invoice->status : '',
                            'Invoice Total' => ($invoice_total) ? $invoice_total : '',
                            'Payment Total' => ($totalPayment) ? $totalPayment : '',
                            'Invoice Item 1' => isset($items[0]->item_name) ? $items[0]->item_name : '',
                            'Invoice Item 1 amount' => isset($items[0]->amount) ? $items[0]->amount : '',
                            'Invoice Item 2' => isset($items[1]->item_name) ? $items[1]->item_name : '',
                            'Invoice Item 2 amount' => isset($items[1]->amount) ? $items[1]->amount : '',
                            'Invoice Item 3 ' => isset($items[2]->item_name) ? $items[2]->item_name : '',
                            'Invoice Item 3 amount' => isset($items[2]->amount) ? $items[2]->amount : '',
                            'Invoice Item 4 ' => isset($items[3]->item_name) ? $items[3]->item_name : '',
                            'Invoice Item 4 amount' => isset($items[3]->amount) ? $items[3]->amount : '',
                            'Invoice Item 5 ' => isset($items[4]->item_name) ? $items[4]->item_name : '',
                            'Invoice Item 5 amount' => isset($items[4]->amount) ? $items[4]->amount : '',
                            'Invoice Item 6 ' => isset($items[5]->item_name) ? $items[5]->item_name : '',
                            'Invoice Item 6 amount' => isset($items[5]->amount) ? $items[5]->amount : '',
                            'Invoice Item 7 ' => isset($items[6]->item_name) ? $items[6]->item_name : '',
                            'Invoice Item 7 amount' => isset($items[6]->amount) ? $items[6]->amount : '',
                            'Invoice Item 8 ' => isset($items[7]->item_name) ? $items[7]->item_name : '',
                            'Invoice Item 8 amount' => isset($items[7]->amount) ? $items[7]->amount : ''
                        );
                        $count = 1;
                    }
                    else
                    {
                        $display[] = array(
                            'Job id' => $job->job_id,
                            'Job Number' => $job->job_number,
                            'customer Name' => '',
                            'Customer Mobile' => '',
                            'Customer Email' => '',
                            'Job Date' => $job->job_date,
                            'Leg No' => $job->leg_number,
                            'Actual Start Time' => $job->actual_start_time,
                            'Actual End Time' => $job->actual_finish_time,
                            'Pickup Address' => $job->pickup_address,
                            'Delivery Address' => $job->delivery_suburb,
                            'Vahicle Name' => '',
                            'Driver Name' => ($driver) ? $driver : '',
                            'Invoice number' => '',
                            'Invoice Status' => '',
                            'Invoice Total' => '',
                            'Payment Total' => '',
                            'Invoice Item 1' => '',
                            'Invoice Item 1 amount' => '',
                            'Invoice Item 2' => '',
                            'Invoice Item 2 amount' => '',
                            'Invoice Item 3 ' => '',
                            'Invoice Item 3 amount' => '',
                            'Invoice Item 4 ' => '',
                            'Invoice Item 4 amount' => '',
                            'Invoice Item 5 ' => '',
                            'Invoice Item 5 amount' => '',
                            'Invoice Item 6 ' => '',
                            'Invoice Item 6 amount' => '',
                            'Invoice Item 7 ' => '',
                            'Invoice Item 7 amount' => '',
                            'Invoice Item 8 ' => '',
                            'Invoice Item 8 amount' => ''
                        );
                        $count = 1;
                    }
            }
            else
            {
                $display[] = array(
                    'Job id' => $job->job_id,
                    'Job Number' => $job->job_number,
                    'customer Name' => $job->customer_name,
                    'Customer Mobile' => $mobile,
                    'Customer Email' => $email,
                    'Job Date' => $job->job_date,
                    'Leg No' => $job->leg_number,
                    'Actual Start Time' => $job->actual_start_time,
                    'Actual End Time' => $job->actual_finish_time,
                    'Pickup Address' => $job->pickup_address,
                    'Delivery Address' => $job->delivery_suburb,
                    'Vahicle Name' => $vehicleName,
                    'Driver Name' => ($driver) ? $driver : '',
                    'Invoice number' => ($invoice) ? $invoice->invoice_number : '',
                    'Invoice Status' => ($invoice) ? $invoice->status : '',
                    'Invoice Total' => ($invoice_total) ? $invoice_total : '',
                    'Payment Total' => ($totalPayment) ? $totalPayment : '',
                    'Invoice Item 1' => isset($items[0]->item_name) ? $items[0]->item_name : '',
                    'Invoice Item 1 amount' => isset($items[0]->amount) ? $items[0]->amount : '',
                    'Invoice Item 2' => isset($items[1]->item_name) ? $items[1]->item_name : '',
                    'Invoice Item 2 amount' => isset($items[1]->amount) ? $items[1]->amount : '',
                    'Invoice Item 3 ' => isset($items[2]->item_name) ? $items[2]->item_name : '',
                    'Invoice Item 3 amount' => isset($items[2]->amount) ? $items[2]->amount : '',
                    'Invoice Item 4 ' => isset($items[3]->item_name) ? $items[3]->item_name : '',
                    'Invoice Item 4 amount' => isset($items[3]->amount) ? $items[3]->amount : '',
                    'Invoice Item 5 ' => isset($items[4]->item_name) ? $items[4]->item_name : '',
                    'Invoice Item 5 amount' => isset($items[4]->amount) ? $items[4]->amount : '',
                    'Invoice Item 6 ' => isset($items[5]->item_name) ? $items[5]->item_name : '',
                    'Invoice Item 6 amount' => isset($items[5]->amount) ? $items[5]->amount : '',
                    'Invoice Item 7 ' => isset($items[6]->item_name) ? $items[6]->item_name : '',
                    'Invoice Item 7 amount' => isset($items[6]->amount) ? $items[6]->amount : '',
                    'Invoice Item 8 ' => isset($items[7]->item_name) ? $items[7]->item_name : '',
                    'Invoice Item 8 amount' => isset($items[7]->amount) ? $items[7]->amount : ''
                );
                $id = $job->job_id;
                $count = 1;
            }             
        }
        // dd($display);
        Excel::create('List Jobs', function ($excel) use ($display) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('List Job');
            $excel->setCreator('Website')->setCompany($this->companyName);
            $excel->setDescription('List Job');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function ($sheet) use ($display) {
                $sheet->fromArray($display, null, 'A1', false, false);

                $sheet->row(1, function ($row) {

                    // call row manipulation methods
                    $row->setFont(array(
                        'bold' => true
                    ));
                });
            });
        })->download('xlsx');
    }

    public function inventory($job_id)
    {
        try {
            if (empty($job_id)) {
                return redirect(route('admin.list-jobs.index'));
            }
            $this->job = JobsMoving::where('job_id', '=', $job_id)->where('jobs_moving.tenant_id', '=', auth()->user()->tenant_id)->first();
            $this->inventory_groups = MovingInventoryGroups::where('tenant_id', '=', auth()->user()->tenant_id)->get();
            $this->getInventoryItems = MovingInventoryDefinitions::where('tenant_id', '=', auth()->user()->tenant_id)->get();
            $this->countInvItems = JobsMovingInventory::where('tenant_id', '=', auth()->user()->tenant_id)->where('job_id', $job_id)->where('inventory_id', 'like', '9000%')->count();
            return view('admin.list-jobs.inventory', $this->data);
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function ajaxUpdateCbmManually(Request $request)
    {
        $this->job_id = $request->input('job_id');
        $this->total_cbm = $request->input('total_cbm');
        $this->goods_value = $request->input('goods_value');
        //$this->insurance_based_on = $request->input('insurance_based_on');

        $job = JobsMoving::where('job_id', '=', $this->job_id)->where('jobs_moving.tenant_id', '=', auth()->user()->tenant_id)
        ->update([
            'total_cbm'=>$this->total_cbm,
            'goods_value'=>$this->goods_value
        ]);

        $this->job = JobsMoving::where('job_id', '=', $this->job_id)->where('jobs_moving.tenant_id', '=', auth()->user()->tenant_id)->first();
        $this->job_price_additional = DB::table('jobs_moving_pricing_additional as t1')->select('t1.*')->where(['t1.tenant_id' => auth()->user()->tenant_id])->first();     

        $response['error'] = 0;
        $response['message'] = 'CBM / Goods Value has been updated.';  
        $response['html'] = view('admin.crm-leads.inventory_tab_top_grid', $this->data)->render();  
        return json_encode($response);    
    }

    public function saveInventoryData(Request $request, $job_id)
    {
        try {
            if (empty($job_id)) {
                return Reply::redirect(route('admin.list-jobs.index'));
            }
            if ($request->input('calc_data')) {
                $calculator_data = $request->input('calc_data');
                $oppid = $request->input('oppid');
                $explodeData = explode("&", $calculator_data);
                $job = JobsMoving::where(['tenant_id' => auth()->user()->tenant_id, 'crm_opportunity_id' => $oppid])->first();
                $job_id = ($job)?$job->job_id:$job_id;
                $saveExtraNotes = '';
                foreach ($explodeData as $row) {
                    $explodeRow = explode("=", $row);
                    $updatedata['job_id'] = $job_id;
                    $updatedata['inventory_id'] = $explodeRow[0];
                    $updatedata['quantity'] = $explodeRow[1];
                    // $checkIfSpecialItem = MovingInventoryDefinitions::select('item_name', 'special_notes')->where('id', '=', $explodeRow[0])->where('special_item', '=', 'YES')->first();
                    // $checkIfSpecialItem = $this->db->query("select item_name,special_notes from vbs_inventory_definitions WHERE id='$explodeRow[0]' AND special_item='YES' ")->row();
                    // if ($checkIfSpecialItem) {
                    // $saveExtraNotes .= $checkIfSpecialItem->item_name . ' : ' . $checkIfSpecialItem->special_notes . '\r\n';
                    //UPDATE categories SET code = CONCAT(code, '_standard') WHERE id = 1;
                    //Pool table (8 foot) : Ground floor to ground floor only;
                    // }
                    $table = "job_inventory";
                    $checkIfAlready = JobsMovingInventory::select('id')->where('job_id', '=', $job_id)->where(['inventory_id' => $explodeRow[0], 'tenant_id' => auth()->user()->tenant_id])->first();
                    if ($checkIfAlready) {
                        if($updatedata['quantity']>0){
                            $checkIfAlready->job_id = $updatedata['job_id'];
                            $checkIfAlready->inventory_id = $updatedata['inventory_id'];
                            $checkIfAlready->quantity = $updatedata['quantity'];
                            $checkIfAlready->save();
                        }else{
                            JobsMovingInventory::where('job_id', '=', $job_id)->where(['inventory_id' => $explodeRow[0], 'tenant_id' => auth()->user()->tenant_id])->delete();
                        }
                        // $inventory_idArr .= $checkIfAlready->id . ', ';
                    } else {
                        $checkIfNoAlready = new JobsMovingInventory();
                        $checkIfNoAlready->job_id = $updatedata['job_id'];
                        $checkIfNoAlready->inventory_id = $updatedata['inventory_id'];
                        $checkIfNoAlready->quantity = $updatedata['quantity'];
                        $checkIfNoAlready->tenant_id = auth()->user()->tenant_id;
                        $checkIfNoAlready->save();
                        // $inventory_idArr .= $checkIfNoAlready->id . ', ';
                    }
                    //echo $explodeRow[0].'<br/>';
                    if (isset($explodeRow[0]) && substr($explodeRow[0], 0, 4) == '9000') {
                        $tmp = explode('_', $explodeRow[0]);
                        if (isset($tmp[1]) && $tmp[1] == 'cbm') {
                            $updateMiscCBM = JobsMovingInventory::where('job_id', '=', $job_id)->where(['inventory_id' => $tmp[0], 'tenant_id' => auth()->user()->tenant_id])->first();
                            $updateMiscCBM->misc_item_cbm = $explodeRow[1];
                            $updateMiscCBM->save();
                        }
                        if (isset($tmp[1]) && $tmp[1] == 'name') {
                            $updateMiscCBM = JobsMovingInventory::where('job_id', '=', $job_id)->where(['inventory_id' => $tmp[0], 'tenant_id' => auth()->user()->tenant_id])->first();
                            $updateMiscCBM->misc_item_name = $explodeRow[1];
                            $updateMiscCBM->save();
                        }
                    }
                }
                // $this->db->query("update vbs_jobs SET special_item_notes = '$saveExtraNotes' WHERE id='$job_id' ");
                // $inventory_idArrFinal = substr($inventory_idArr, 0, strlen($inventory_idArr) - 2);
                // $this->db->query("delete from vbs_job_inventory where ID NOT IN ($inventory_idArrFinal) AND job_id='$job_id' ");
                // $getNotesFromDB = $this->db->query("select special_item_notes from vbs_jobs WHERE id = '$job_id' ")->row();
                $totalCBM = $this->calculate_total_cbm($job_id);

                $job_price_additional = DB::table('jobs_moving_pricing_additional as t1')->select('t1.*')->where(['t1.tenant_id' => auth()->user()->tenant_id])->first();     
                $goods_value_per_cbm = ($job_price_additional)?$job_price_additional->goods_value_per_cbm:0;
                $goods_value = ($totalCBM*$goods_value_per_cbm);

                JobsMoving::where(['job_id' => $job_id, 'tenant_id' => auth()->user()->tenant_id])->update([
                    'total_cbm' => $totalCBM,
                    'goods_value' => $goods_value
                ]);
                // $this->db->query("update vbs_jobs SET calculated_cbm = '$totalCBM' WHERE id='$job_id' ");
                $data = array(
                    'totalCBM' => $totalCBM,
                    'totalValue' => $goods_value,
                    'special_item_notes' => 'special notes',
                    // 'special_item_notes' => $getNotesFromDB->special_item_notes
                );
                echo json_encode($data);
            } else {
                echo json_encode("0");
            }
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function saveInventoryMiscellanceousData(Request $request, $job_id)
    {
        try {
            if (empty($job_id)) {
                return redirect(route('admin.list-jobs.index'));
            }
            JobsMovingInventory::where(['job_id' => $job_id, 'inventory_id' => 0, 'tenant_id' => auth()->user()->tenant_id])->delete();
            $totalItems = 0;
            for($i = 0; $i < count($request->name); $i++)
            {
                if($request->name[$i] != null && $request->cbm[$i] != null && $request->quantity[$i] != null)
                {
                    $miscllanceousItem = new JobsMovingInventory();
                    $miscllanceousItem->job_id = $job_id;
                    $miscllanceousItem->inventory_id = 0;
                    $miscllanceousItem->quantity = $request->quantity[$i];
                    $miscllanceousItem->misc_item = 'Y';
                    $miscllanceousItem->misc_item_name = $request->name[$i];
                    $miscllanceousItem->misc_item_cbm = $request->cbm[$i];
                    $miscllanceousItem->tenant_id = auth()->user()->tenant_id;
                    $miscllanceousItem->save();
                    $totalItems++;
                }
            }

            $totalCBM = $this->calculate_total_cbm($job_id);

            $job_price_additional = DB::table('jobs_moving_pricing_additional as t1')->select('t1.*')->where(['t1.tenant_id' => auth()->user()->tenant_id])->first();     
            $goods_value_per_cbm = ($job_price_additional)?$job_price_additional->goods_value_per_cbm:0;
            $goods_value = ($totalCBM*$goods_value_per_cbm);


            JobsMoving::where(['job_id' => $job_id, 'tenant_id' => auth()->user()->tenant_id])->update([
                'total_cbm' => $totalCBM,
                'goods_value' => $goods_value
            ]);
            
            $response['error'] = 0;
            $response['totalCBM'] = $totalCBM;
            $response['totalValue']  = $goods_value;
            $response['totalItems'] = $totalItems;
            $response['special_item_notes'] = 'special notes';
            $response['messgae'] = 'miscllanceous item has been added SuccFully';
            return json_encode($response);

        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function deleteInventoryData(Request $request)
    {
        try {
            $job_id = $request->input('job_id');
            $inv_id = $request->input('inv_id');
            if ($inv_id) {
                JobsMovingInventory::where('job_id', '=', $job_id)->where(['inventory_id' => $inv_id, 'tenant_id' => auth()->user()->tenant_id])->delete();
                $countInvItems = JobsMovingInventory::where(['job_id' => $job_id, 'tenant_id' => auth()->user()->tenant_id])->where('inventory_id', 0)->count();
                $totalCBM = $this->calculate_total_cbm($job_id);

                $job_price_additional = DB::table('jobs_moving_pricing_additional as t1')->select('t1.*')->where(['t1.tenant_id' => auth()->user()->tenant_id])->first();     
                $goods_value_per_cbm = ($job_price_additional)?$job_price_additional->goods_value_per_cbm:0;
                $goods_value = ($totalCBM*$goods_value_per_cbm);

                JobsMoving::where(['job_id' => $job_id, 'tenant_id' => auth()->user()->tenant_id])->update([
                    'total_cbm' => $totalCBM,
                    'goods_value' => $goods_value
                ]);
                $details = array(
                    'success' => '1',
                    'countInvItems' => $countInvItems,
                    'totalCBM' => $totalCBM,
                    'totalValue' => $goods_value
                );
                echo json_encode($details);
            } else {
                echo json_encode("0");
            }
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function deleteInventoryMiscllanceousData(Request $request)
    {
        try {
            $job_id = $request->input('job_id');
            $inv_id = $request->input('inv_id');
            if ($inv_id) {
                JobsMovingInventory::where('job_id', '=', $job_id)->where(['id' => $inv_id, 'tenant_id' => auth()->user()->tenant_id])->delete();
                $totalItems = JobsMovingInventory::where(['job_id' => $job_id, 'tenant_id' => auth()->user()->tenant_id])->where('inventory_id', 0)->count();
                $totalCBM = $this->calculate_total_cbm($job_id);
                
                $job_price_additional = DB::table('jobs_moving_pricing_additional as t1')->select('t1.*')->where(['t1.tenant_id' => auth()->user()->tenant_id])->first();     
                $goods_value_per_cbm = ($job_price_additional)?$job_price_additional->goods_value_per_cbm:0;
                $goods_value = ($totalCBM*$goods_value_per_cbm);

                JobsMoving::where('job_id', $job_id)->update([
                    'total_cbm' => $totalCBM,
                    'goods_value' => $goods_value
                ]);

                $response['error'] = 0;
                $response['totalCBM'] = $totalCBM;
                $response['totalValue']  = $goods_value;
                $response['totalItems'] = $totalItems;
                $response['special_item_notes'] = 'special notes';
                $response['messgae'] = 'miscllanceous item has been deleted SuccFully';
                return json_encode($response);
            } else {
                echo json_encode("0");
            }
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    protected function calculate_total_cbm($job_id)
    {
        try {
            $calculateTotalCBM = JobsMovingInventory::select(
                'jobs_moving_inventory.*', 
                'moving_inventory_definitions.cbm'
                )
                ->leftJoin('moving_inventory_definitions', 'moving_inventory_definitions.id', '=', 'jobs_moving_inventory.inventory_id')
                ->where(['jobs_moving_inventory.job_id' => $job_id, 'jobs_moving_inventory.tenant_id' => auth()->user()->tenant_id])
                ->get();
            $totalCBM = 0;
            
            if ($calculateTotalCBM) {
                foreach ($calculateTotalCBM as $calc) {
                    $qtyExp = $calc->quantity;
                    if($calc->inventory_id != 0) {
                        $cbm = $calc->cbm;
                    } else{
                        $cbm = $calc->misc_item_cbm;   
                    }
                    $cbmQty = floatval($cbm) * floatval($qtyExp);
                    $totalCBM += $cbmQty;
                }
            }
            $floatto2 = number_format((float) $totalCBM, 2, '.', '');
            return $floatto2;
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function getInventoryDetails(Request $request)
    {
        try {
            $job_id = $request->input('job_id');
            if ($job_id) {
                $jobInventoryCalculator = DB::select("select vji.*,vid.cbm from jobs_moving_inventory vji LEFT JOIN moving_inventory_definitions vid ON vid.id=vji.inventory_id WHERE job_id = '$job_id' ");
                foreach($jobInventoryCalculator as $inventory)
                {
                    $inventory->quantity = (int)$inventory->quantity;
                }
                $inventoryCalcCountbyGroupID = DB::select("select count(vid.group_id) as count,vid.group_id from jobs_moving_inventory vji LEFT JOIN moving_inventory_definitions vid ON vid.id=vji.inventory_id WHERE job_id = '$job_id' group by vid.group_id having count > 0 ");
                $details = array(
                    'inventoryCalc' => $jobInventoryCalculator,
                    'inventoryCalcCountbyGroupID' => $inventoryCalcCountbyGroupID,
                    'totalCBM' => $this->calculate_total_cbm($job_id),
                );
                echo json_encode($details);
            } else {
                echo json_encode("Job_id not posted!");
            }
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function operations($job_id)
    {
        try {
            if (empty($job_id)) {
                return redirect(route('admin.list-jobs.index'));
            }
            $this->job = JobsMoving::where('job_id', '=', $job_id)->where('jobs_moving.tenant_id', '=', auth()->user()->tenant_id)->first();
            $this->job_total_legs = JobsMovingLegs::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->count();
            if ($this->job_total_legs == 0) {
                $obj = new JobsMovingLegs();
                $obj->job_id = $this->job->job_id;
                $obj->tenant_id = auth()->user()->tenant_id;
                $obj->leg_number = 1;
                $obj->leg_date = $this->job->job_date;
                //$obj->pickup_address = $this->job->pickup_address;
                //$obj->drop_off_address = $this->job->drop_off_address;
                $obj->vehicle_id = $this->job->vehicle_id;
                $obj->job_type = $this->job->job_type;
                $obj->leg_status = $this->job->job_status;
                $obj->save();
                $this->job_total_legs = 1;
            }
            $this->job_type = Lists::job_type();
            $this->leg_status = Lists::leg_status();
            $this->drivers = User::allDrivers();
            $this->vehicles = Vehicles::select('id', 'vehicle_name')->where('tenant_id', '=', auth()->user()->tenant_id)->get();
            $this->job_legs = JobsMovingLegs::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();

            return view('admin.list-jobs.operations', $this->data);
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function operationsAddLeg($job_id)
    {
        try {
            if (empty($job_id)) {
                return redirect()->route('admin.list-jobs.operations', [$job_id]);
            }
            $this->job = JobsMoving::where('job_id', '=', $job_id)->where('jobs_moving.tenant_id', '=', auth()->user()->tenant_id)->first();
            $jleg = JobsMovingLegs::select(DB::raw('MAX(leg_number) as max_leg'))->where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            $max_leg = $jleg->max_leg + 1;
            $obj = new JobsMovingLegs();
            $obj->job_id = $this->job->job_id;
            $obj->tenant_id = auth()->user()->tenant_id;
            $obj->leg_number = $max_leg;
            $obj->leg_date = $this->job->job_date;
            //$obj->pickup_address = $this->job->pickup_address;
            //$obj->drop_off_address = $this->job->drop_off_address;
            $obj->vehicle_id = $this->job->vehicle_id;
            $obj->job_type = $this->job->job_type;
            $obj->leg_status = $this->job->job_status;
            $obj->save();
            return redirect()->route('admin.list-jobs.operations', [$job_id]);
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function operationsDeleteLeg($job_id, $leg_id)
    {
        try {
            $jleg = JobsMovingLegs::where(['id' => $leg_id, 'tenant_id' => auth()->user()->tenant_id])->delete();
            return redirect()->route('admin.list-jobs.operations', [$job_id]);
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function operationsSaveData(Request $request, $job_id)
    {
        try {
            $legs = $request->jlegs;
            if (count($legs) > 0):
                foreach ($legs as $leg_id):
                    $obj = JobsMovingLegs::find($leg_id);
                    if ($obj):
                        $obj->leg_date = Carbon::createFromFormat($this->global->date_format, $request->input('leg_date')[$leg_id])->format('Y-m-d');
                        $obj->vehicle_id = $request->input('vehicle_id')[$leg_id];
                        $obj->job_type = $request->input('job_type')[$leg_id];
                        $obj->pickup_address = $request->input('pickup_address')[$leg_id];
                        $obj->drop_off_address = $request->input('drop_off_address')[$leg_id];
                        $obj->driver_id = $request->input('driver_id')[$leg_id];
                        $obj->leg_status = $request->input('leg_status')[$leg_id];
                        $obj->notes = $request->input('dispatch_notes')[$leg_id];
                        $obj->updated_at = time();
                        $obj->save();
                    endif;
                endforeach;
            endif;
            return Reply::redirect(route('admin.list-jobs.operations', [$job_id]), __('messages.jobLegsSaved'));
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function jobSchedule($vehicleGroup="all")
    {
        try {
            $this->pageTitle = __('app.menu.jobSchedule');
            $this->pageIcon = 'icon-calender';
            $this->vehicleGroupFilter = $vehicleGroup;
            $this->vehicleGroups = VehicleGroups::where(['tenant_id'=> auth()->user()->tenant_id, 'deleted'=>0])->orderBy('group_name', 'asc')->get();
            $this->employees = User::where(['tenant_id' => auth()->user()->tenant_id])->get();
            $this->events = Event::where(['tenant_id' => auth()->user()->tenant_id])->get();
            
            if($this->global->week_starts == 'Monday') {
                $this->firstDay = 1;
            } else {
                $this->firstDay = 0;
            }

            return view('admin.list-jobs.job-schedule', $this->data);
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function getVehicles($vehicleGroup)
    {
        if($vehicleGroup=="all"){
            $vehicles = Vehicles::where('tenant_id', $this->user->tenant_id)
                                ->where('active','Y')
                                ->orderBy('vehicle_name', 'ASC')
                                ->get();
        }else{
            $vehicles = Vehicles::where('tenant_id', $this->user->tenant_id)
            ->where('active','Y')
            ->where('category','=',$vehicleGroup)
            ->orderBy('vehicle_name', 'ASC')
            ->get();
        }
        $post_data = array();
        foreach ($vehicles as $vehicle) {
            $post_data[] = array('id' => $vehicle->id, 'title' => $vehicle->vehicle_name, 'groupId'=>$vehicle->category);
        }
        $post_data[] = array('id' => '0', 'title' => 'Unassigned');
        return response()->json($post_data);
    }

    public function getJobs($vehicleGroup){
        if($vehicleGroup=="all"){
            $vehicles = Vehicles::where(['tenant_id'=> auth()->user()->tenant_id,'active'=>'Y'])->pluck('id')->toArray();
        }else{
            $vehicles = Vehicles::where(['tenant_id'=> auth()->user()->tenant_id,'active'=>'Y'])->where('category','=',$vehicleGroup)->pluck('id')->toArray();
        }

        if(count($vehicles)){
            $post_data = array();
            // dd($vehicles);
            $jobs = JobsMovingLegs::select('jobs_moving_legs.id', 
            'jobs_moving_legs.job_id', 
            'jobs_moving_legs.id AS leg_id', 
            'jobs_moving_legs.leg_status', 
            'jobs_moving_legs.leg_number', 
            'jobs_moving_legs.leg_date', 
            'jobs_moving_legs.driver_id', 
            'jobs_moving_legs.offsider_ids', 
            'jobs_moving_legs.vehicle_id', 
            'jobs_moving_legs.est_start_time', 
            'jobs_moving_legs.est_finish_time', 
            'jobs_moving_legs.pickup_address', 
            'jobs_moving_legs.drop_off_address', 
            'jobs_moving.customer_id', 
            'jobs_moving.total_cbm', 
            'jobs_moving.job_number', 
            'jobs_moving.job_date', 
            'vehicles.vehicle_name',
            'vehicles.vehicle_colour'
            // 'crm_contacts.name'
            )
                ->join('vehicles', 'vehicles.id', 'jobs_moving_legs.vehicle_id')
                ->join('jobs_moving', 'jobs_moving.job_id', 'jobs_moving_legs.job_id')
                // ->leftjoin('crm_contacts', 'crm_contacts.lead_id', 'jobs_moving.customer_id')
                ->whereIn('jobs_moving_legs.vehicle_id', $vehicles)
                ->where('jobs_moving.deleted', 0)
                ->where(['jobs_moving_legs.tenant_id' => auth()->user()->tenant_id])
                ->get();
            //->where('jobs_moving_legs.vehicle_id', $vehicle->id)
            /*JobsMovingLegs::where(function ($query) {
            $jobs->where('jobs_moving_legs.vehicle_id', '=', $vehicle->id)
            ->orWhere('jobs_moving_legs.vehicle_id', '=', '');
            });*/
            // dd($jobs);
            $allPeople = User::allPeople();
            foreach ($jobs as $job) {  
                $crm_contacts = CRMContacts::where(['lead_id' => $job->customer_id, 'tenant_id' => auth()->user()->tenant_id, 'deleted' => 'N'])->first();
                if($crm_contacts){
                    $crm_contact_phone = CRMContactDetail::where(['contact_id' => $crm_contacts->id, 'tenant_id' => auth()->user()->tenant_id])->where('detail_type', '=', 'Mobile')->first();
                    $customer_name = $crm_contacts->name;
                    $customer_phone = ($crm_contact_phone)? $crm_contact_phone->detail:'';
                }else{
                    $customer_name='';
                    $customer_phone='';
                }
                
                $driver = JobsMovingLegsTeam::where(['tenant_id' => auth()->user()->tenant_id, 'leg_id' => $job->leg_id, 'driver' => 'Y'])->first();
                $offsiders = JobsMovingLegsTeam::where(['tenant_id' => auth()->user()->tenant_id, 'leg_id' => $job->leg_id, 'driver' => 'N'])->get();
                // dd($driver);
                $mousehover_title = $title = '<b>Job #</b> ' . $job->job_number . ' <br/><b>Customer:</b> ' . strtoupper($customer_name); 
                $mousehover_title .= '<br/><b>Mobile:</b> ' . $customer_phone;
                $title = 'Job# ' . $job->job_number . ' (' . $customer_name . ')' . "\n";
                
                // if($job->vehicle_id!=""){
                //     if($job->vehicle_id==$vehicle->id){
                //         $mousehover_title .= '<br/><b>Vehicle:</b> ' . $job->vehicle_name;
                //     }
                // }
                $mousehover_title .= '<br/><b>Vehicle:</b> ' . $job->vehicle_name;
                $mousehover_title .= '<br/><b>CBM:</b>' . $job->total_cbm.' m3';

                if($driver != null){
                    foreach($allPeople as $people) {
                        if ($people->id == $driver->people_id) {
                            $title .= 'Driver: ' . $people->name;
                            $mousehover_title .= '<br/><br/><b>Driver:</b> ' . $people->name;
                            break;
                        }
                    }
                }
                if($offsiders != null){
                    // $offsider_ids = array();
                    // $offsider_ids = @explode(',', $job->offsider_ids);
                    $offsiders_names="";
                    foreach($offsiders as $offsider){   
                        $offsiders_names = ($offsiders_names!="")?$offsiders_names.', ':'';  
                        foreach($allPeople as $people) {
                            if ($people->id==$offsider->people_id) {
                                $offsiders_names .= $people->name;
                                break;
                            }
                        }
                    }
                    $mousehover_title .= '<br/><b>Offsiders:</b> ' . $offsiders_names;
                }
                // $start = strtotime(date('Y-m-d ' . $job->job_start_time)) * 1000;
                // $end = strtotime(date('Y-m-d ' . $job->job_end_time)) * 1000;

                if ($job->est_start_time != null && $job->est_start_time != '00:00:00') {
                    $startTime = $job->est_start_time;
                } else {
                    $startTime = '06:00';
                }

                if ($job->est_finish_time != null && $job->est_finish_time != '00:00:00') {
                    $endTime = $job->est_finish_time;
                } else {
                    $endTime = '07:00';
                }

                $mousehover_title .= '<br/><br/><b>Date:</b> ' . date('Y-m-d ',strtotime($job->leg_date));
                $mousehover_title .= '<br/><b>Time:</b> ' . $startTime.' to '.$endTime;
                $mousehover_title .= '<br/><br/><b>Pickup:</b> ' . $job->pickup_address;
                $mousehover_title .= '<br/><b>Delivery:</b> ' . $job->drop_off_address;

                // Getting Invoice Status for left colour
                $invoice = Invoice::where(['job_id'=>$job->job_id, 'sys_job_type'=>'Moving', 'tenant_id' => auth()->user()->tenant_id])->first();
                if($invoice){
                    $paidAmount = $invoice->getPaidAmount();
                    $totalAmount = $invoice->getTotalAmount();
                    // $totalAmount = InvoiceItems::where(['invoice_id' => $invoice->id, 'tenant_id' => auth()->user()->tenant_id])->sum('amount');
                    // $paidAmount = Payment::where(['invoice_id' => $invoice->id, 'tenant_id' => auth()->user()->tenant_id])->sum('amount');
                }else{
                    $totalAmount = 0;
                    $paidAmount = 0;
                }
                if ($paidAmount < $totalAmount && $paidAmount>0) {
                    $leftBorderClass = 'event_border_left_partial';
                } elseif ($paidAmount == $totalAmount && $paidAmount>0) {
                    $leftBorderClass = 'event_border_left_paid';
                } else{
                    $leftBorderClass = 'event_border_left_unpaid';
                }

                $mousehover_title .= '<br/><br/><b>Total:</b> ' . number_format((float)($totalAmount), 2, '.', '');
                $mousehover_title .= '<br/><b>Payments:</b> ' . number_format((float)($paidAmount), 2, '.', '');
                $mousehover_title .= '<br/><b>Balance:</b> ' . (number_format((float)($totalAmount), 2, '.', '')-number_format((float)($paidAmount), 2, '.', ''));
                //---

                // For Right Colour
                if ($job->leg_status=="New" || $job->leg_status=="Awaiting Confirmation") {
                    $rightBorderClass = 'event_border_right_new';
                } elseif ($job->leg_status=="Confirmed" || $job->leg_status=="Picked" || $job->leg_status=="Delivered") {
                    $rightBorderClass = 'event_border_right_confirm';
                } elseif($job->leg_status=="Completed"){
                    $rightBorderClass = 'event_border_right_complete';
                } else{
                    $rightBorderClass="";
                }

                $post_data[] = array(
                    'id' => $job->id, //. '&vehicle_job&leg_' . $job->job_id
                    'allDay' => false,
                    'title' => $title,
                    'mousehover_title'=>$mousehover_title,
                    'job_id' => $job->job_id,
                    'start' => date('Y-m-d', strtotime($job->leg_date)) . 'T' . date('H:i:s', strtotime($startTime)),
                    'end' => date('Y-m-d', strtotime($job->leg_date)) . 'T' . date('H:i:s', strtotime($endTime)),
                    //'start' => $start,
                    //'end' => $end,
                    'resourceId' => $job->vehicle_id,
                    'backgroundColor' => $job->vehicle_colour,
                    'className' => [$leftBorderClass,$rightBorderClass]
                );
            }
        }

        $mousehover_title="";
        $unassignedJobs = JobsMovingLegs::select('jobs_moving_legs.id', 
        'jobs_moving_legs.job_id', 
        'jobs_moving_legs.leg_status', 
        'jobs_moving_legs.leg_number', 
        'jobs_moving_legs.leg_date', 
        'jobs_moving_legs.driver_id', 
        'jobs_moving_legs.vehicle_id', 
        'jobs_moving_legs.est_start_time', 
        'jobs_moving_legs.est_finish_time', 
        'jobs_moving_legs.pickup_address', 
        'jobs_moving_legs.drop_off_address', 
        'jobs_moving.customer_id', 
        'jobs_moving.total_cbm', 
        'jobs_moving.job_number', 
        'jobs_moving.job_date')
            ->join('jobs_moving', 'jobs_moving.job_id', 'jobs_moving_legs.job_id')
            ->where(function ($query) {
                //dd($vehicle);
                $query->Where('jobs_moving_legs.vehicle_id', '=', '0')
                    ->orWhere('jobs_moving_legs.vehicle_id', '=', null);
            })
            ->where('jobs_moving_legs.tenant_id', auth()->user()->tenant_id)
            ->get();
        foreach ($unassignedJobs as $uJob) {
            $crm_contacts = CRMContacts::where(['lead_id' => $uJob->customer_id, 'tenant_id' => auth()->user()->tenant_id, 'deleted' => 'N'])->first();
                if($crm_contacts){
                    $crm_contact_phone = CRMContactDetail::where(['contact_id' => $crm_contacts->id, 'tenant_id' => auth()->user()->tenant_id])->where('detail_type', '=', 'Mobile')->first();
                    $customer_name = $crm_contacts->name;
                    $customer_phone = ($crm_contact_phone)? $crm_contact_phone->detail:'';
                }else{
                    $customer_name='';
                    $customer_phone='';
                }
            $mousehover_title = $title = '<b>Job #</b> ' . $uJob->job_number . ' <br/>Customer: ' . strtoupper($customer_name); 
            $mousehover_title .= '<br/><b>Mobile:</b> ' . $customer_phone;
            $title = 'Job# ' . $uJob->job_number . ' (' . strtoupper($customer_name) . ')';
            if ($uJob->est_start_time != null && $uJob->est_start_time != '00:00:00') {
                $startTime = date('h:i A',strtotime($uJob->est_start_time));
            } else {
                $startTime = '00:00';
            }

            if ($uJob->est_finish_time != null && $uJob->est_finish_time != '00:00:00') {
                $endTime = date('h:i A ',strtotime($uJob->est_finish_time));
            } else {
                $endTime = '00:00';
            }
            $mousehover_title .= '<br/><b>CBM:</b> ' . $uJob->total_cbm.' m3';
            $mousehover_title .= '<br/><br/><b>Date:</b> ' . date('Y-m-d ',strtotime($uJob->leg_date));
            $mousehover_title .= '<br/><b>Time:</b> ' . $startTime.' to '.$endTime;
            $mousehover_title .= '<br/><br/><b>Pickup:</b> ' . $uJob->pickup_address;
            $mousehover_title .= '<br/><b>Delivery:</b> ' . $uJob->drop_off_address;

            // Getting Invoice Status for left colour
                $invoice = Invoice::where(['job_id'=>$uJob->job_id, 'sys_job_type'=>'Moving', 'tenant_id' => auth()->user()->tenant_id])->first();
                if($invoice){
                    $totalAmount = InvoiceItems::where(['invoice_id' => $invoice->id, 'tenant_id' => auth()->user()->tenant_id])->sum('amount');
                    $paidAmount = Payment::where(['invoice_id' => $invoice->id, 'tenant_id' => auth()->user()->tenant_id])->sum('amount');
                }else{
                    $totalAmount = 0;
                    $paidAmount = 0;
                }
                if ($paidAmount < $totalAmount && $paidAmount>0) {
                    $leftBorderClass = 'event_border_left_partial';
                } elseif ($paidAmount == $totalAmount && $paidAmount>0) {
                    $leftBorderClass = 'event_border_left_paid';
                } else{
                    $leftBorderClass = 'event_border_left_unpaid';
                }

                $mousehover_title .= '<br/><br/><b>Total:</b> ' . number_format((float)($totalAmount), 2, '.', '');
                $mousehover_title .= '<br/><b>Payments:</b> ' . number_format((float)($paidAmount), 2, '.', '');
                $mousehover_title .= '<br/><b>Balance:</b> ' . (number_format((float)($totalAmount), 2, '.', '')-number_format((float)($paidAmount), 2, '.', ''));
                //---

                // For Right Colour
                if ($uJob->leg_status=="New" || $uJob->leg_status=="Awaiting Confirmation" || $uJob->leg_status=="") {
                    $rightBorderClass = 'event_border_right_new';
                } elseif ($uJob->leg_status=="Confirmed" || $uJob->leg_status=="Picked" || $uJob->leg_status=="Delivered") {
                    $rightBorderClass = 'event_border_right_confirm';
                } elseif($uJob->leg_status=="Completed"){
                    $rightBorderClass = 'event_border_right_complete';
                } else{
                    $rightBorderClass="";
                }

            $post_data[] = array(
                'id' => $uJob->id, //. '&vehicle_job&leg_' . $uJob->job_id
                'allDay' => false,
                'title' => $title,
                'mousehover_title' => $mousehover_title,
                'job_id' => $uJob->job_id,
                'start' => date('Y-m-d', strtotime($uJob->leg_date)) . 'T' . date('H:i:s', strtotime($startTime)),
                'end' => date('Y-m-d', strtotime($uJob->leg_date)) . 'T' . date('H:i:s', strtotime($endTime)),
                'resourceId' => '0',
                'className' => [$leftBorderClass,$rightBorderClass]
            );
        }
        //dd($unassignedJobs);

        $vehicle_unavailability = VehicleUnavailability::getData();
        foreach($vehicle_unavailability as $unavailable){
            if($unavailable->from_date == $unavailable->to_date){
                $post_data[] = array(
                    'id' => $unavailable->vehicle_id,
                    'allDay' => false,
                    'title' => 'Unavailable: ' . $unavailable->reason,
                    'mousehover_title' => $unavailable->reason,
                    'job_id' => 0,
                    'start' => date('Y-m-d', strtotime($unavailable->from_date)) . 'T' . date('H:i:s', strtotime($unavailable->from_time)),
                    'end' => date('Y-m-d', strtotime($unavailable->to_date)) . 'T' . date('H:i:s', strtotime($unavailable->to_time)),
                    'backgroundColor' => 'grey',
                    'borderColor' =>  'red',
                    'textColor' => 'black',
                    'resourceId' => $unavailable->vehicle_id
                );
            }else{
                $from_date = Carbon::parse($unavailable->from_date);
                $to_date = Carbon::parse($unavailable->to_date);
                
                // dd($from_date);
                while(1){
                    if($from_date->isSameDay($to_date)){
                        $post_data[] = array(
                            'id' => $unavailable->vehicle_id,
                            'allDay' => false,
                            'title' => 'Unavailable: ' . $unavailable->reason,
                            'mousehover_title' => $unavailable->reason,
                            'job_id' => 0,
                            'start' => date('Y-m-d', strtotime($from_date)) . 'T' . date('H:i:s', strtotime($unavailable->from_time)),
                            'end' => date('Y-m-d', strtotime($from_date)) . 'T' . date('H:i:s', strtotime($unavailable->to_time)),
                            'backgroundColor' => 'grey',
                            'borderColor' =>  'red',
                            'textColor' => 'black',
                            'resourceId' => $unavailable->vehicle_id
                        );
                        break;
                    }
                    $post_data[] = array(
                        'id' => $unavailable->vehicle_id,
                        'allDay' => false,
                        'title' => 'Unavailable: ' . $unavailable->reason,
                        'mousehover_title' => $unavailable->reason,
                        'job_id' => 0,
                        'start' => date('Y-m-d', strtotime($from_date)) . 'T' . date('H:i:s', strtotime($unavailable->from_time)),
                        'end' => date('Y-m-d', strtotime($from_date)) . 'T' . date('H:i:s', strtotime($unavailable->to_time)),
                        'backgroundColor' => 'grey',
                        'borderColor' =>  'red',
                        'textColor' => 'black',
                        'resourceId' => $unavailable->vehicle_id
                    );
                    $from_date->addDay();
                }
            }
        }

        return response()->json($post_data);
    }

    public function updateScheduleEvent()
    {
        return view('updateScheduleEvent');
    }

    public function updateScheduleEventPost(Request $request)
    {
        $input = $request->all();
        $obj = JobsMovingLegs::findOrFail($request->leg_id);
        $obj->vehicle_id = $request->vehicle_id;
        $obj->est_start_time = $request->start_time;
        $obj->est_finish_time = $request->end_time;
        $obj->leg_date = $request->job_date;
        $obj->save();

        return response()->json(['success' => 'Schedule Updated!']);
    }

    public function getJobsLogsBody($id)
    {
        $jobs_moving_log = JobsMovingLogs::find($id);
        if ($jobs_moving_log->log_type_id == 5) {
            //$jobs_moving_log->log_type_id = 4;
            $jobs_moving_log->email_status = 'Read';
            $jobs_moving_log->save();
        }
        $this->job_log = $jobs_moving_log;
        return view('admin.list-jobs.jobs-logs-body', $this->data);
    }

    public function invoice($job_id)
    {
        try {
            if (empty($job_id)) {
                return redirect(route('admin.list-jobs.index'));
            }
            $this->job_id = $job_id;
            $this->job = JobsMoving::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            $this->invoice = Invoice::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            $this->invoice_items = 0;
            if (isset($this->invoice->id)):
                $this->invoice_items = InvoiceItems::where('invoice_id', '=', $this->invoice->id)->where('invoice_items.tenant_id', '=', auth()->user()->tenant_id)->first();
            endif;
            $this->inventory_groups = MovingInventoryGroups::where('tenant_id', '=', auth()->user()->tenant_id)->get();
            $this->getInventoryItems = MovingInventoryDefinitions::where('tenant_id', '=', auth()->user()->tenant_id)->get();
            $this->countInvItems = JobsMovingInventory::where('tenant_id', '=', auth()->user()->tenant_id)->where('job_id', $job_id)->count();

            $this->paidAmount = 0;
            $this->totalAmount = 0;
            if (isset($this->invoice->id)):
                $this->paidAmount = $this->invoice->getPaidAmount();
                $this->totalAmount = $this->invoice->getTotalAmount();
            endif;

            //dd($this->paidAmount);
            return view('admin.list-jobs.invoice', $this->data);
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function payment($job_id)
    {
        try {
            if (empty($job_id)) {
                    return redirect(route('admin.list-jobs.index'));
            }
            $this->job_id = $job_id;
            $this->job = JobsMoving::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            $this->invoice = Invoice::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            $this->invoice_items = 0;
            if (isset($this->invoice->id)):
                $this->invoice_items = InvoiceItems::where('invoice_id', '=', $this->invoice->id)->where('invoice_items.tenant_id', '=', auth()->user()->tenant_id)->first();
            endif;
            // $this->inventory_groups = MovingInventoryGroups::where('tenant_id', '=', auth()->user()->tenant_id)->get();
            // $this->getInventoryItems = MovingInventoryDefinitions::where('tenant_id', '=', auth()->user()->tenant_id)->get();
            // $this->countInvItems = JobsMovingInventory::where('tenant_id', '=', auth()->user()->tenant_id)->where('job_id', $job_id)->count();

            $this->paidAmount = 0;
            $this->totalAmount = 0;
            if (isset($this->invoice->id)):
                $this->paidAmount = $this->invoice->getPaidAmount();
                $this->totalAmount = $this->invoice->getTotalAmount();
            endif;

            //dd($this->paidAmount);
            return view('admin.list-jobs.payment', $this->data);
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function generateQuote($job_id)
    {
        try {
            if (empty($job_id)) {
                return redirect(route('admin.list-jobs.index'));
            }

            $this->invoice = Invoice::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();

            $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', auth()->user()->tenant_id)->first();

            if (!$this->invoice) {

                $res = Invoice::select(DB::raw('MAX(invoice_number) as max_invoice_number'))->where('tenant_id', '=', auth()->user()->tenant_id)->first();
                if ($res) {
                    $new_invoice_number = intval($res->max_invoice_number) + 1;
                } else {
                    $new_invoice_number = 1;
                }

                $obj = new Invoice();
                $obj->tenant_id = auth()->user()->tenant_id;
                $obj->job_id = $job_id;
                $obj->invoice_number = $new_invoice_number;
                $obj->sys_job_type = 'Moving';
                $obj->project_id = 1;
                $current_date = date('Y-m-d');
                $obj->issue_date = $current_date;
                $due_after = 15;
                $invoice_settings = InvoiceSetting::where('tenant_id', auth()->user()->tenant_id)->first();
                if ($invoice_settings) {
                    $due_after = $invoice_settings->due_after;
                }
                $obj->due_date = date('Y-m-d', strtotime($current_date . ' + ' . $due_after . ' days'));

                // manual project_id = project_id foreign key should be removed.

                $obj->save();

                $this->invoice = $obj;
            }

            $this->job = JobsMoving::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            $this->company = Companies::find($this->job->company_id);
            $this->settings = Setting::findOrFail(1);
            $this->invoiceSetting = InvoiceSetting::first();
            $this->invoice_items = InvoiceItems::where('invoice_id', '=', $this->invoice->id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();

            if (!$this->invoice_items) {
                $obj_item = new InvoiceItems();
                $obj_item->tenant_id = auth()->user()->tenant_id;
                $obj_item->invoice_id = $this->invoice->id;
                $obj_item->item_name = 'Removal Fee';
                $obj_item->item_summary = 'From: ' . $this->job->pickup_address . '  To: ' . $this->job->drop_off_address;
                $obj_item->type = 'Item';
                $obj_item->quantity = 1.00;
                $obj_item->unit_price = $this->job->fixed_other_rate;
                $obj_item->amount = $this->job->fixed_other_rate;
                $obj_item->save();

                $this->invoice_items = $obj_item;
            }
            //dd($this->job);
            $file_number = 1;
            if (!empty($this->invoice->file_original_name)) {
                $filename = str_replace('.pdf', '', $this->invoice->file_original_name);
                $fn_ary = explode('_', $filename);
                $file_number = intval($fn_ary[2]) + 1;
            }

            $filename = 'Quote_Job' . $this->invoice->invoice_number . '_' . $this->job->job_number . '.pdf';
            if (File::exists(public_path() . '/invoice-files/' . $filename)) {
                File::delete(public_path() . '/invoice-files/' . $filename);
            }
            //$this->invoice->quote_file_name = $filename;
            $this->invoice->save();

            $this->job->quote_file_name = $filename;
            $this->job->save();

            $log = new JobsMovingLogs();
            $log->tenant_id = auth()->user()->tenant_id;
            $log->job_id = $job_id;
            $log->user_id = auth()->user()->id;
            $log->log_type_id = 13;
            $log->log_details = 'Quote Generated - ' . $filename;
            $log->log_date = time();
            $log->save();

            //Pay now url
            $payment_amount = InvoiceItems::where(['invoice_id' => $this->invoice->id, 'tenant_id' => auth()->user()->tenant_id])->sum('amount');
            $this->url_params = base64_encode('invoice_id=' . $this->invoice->id . '&payment_amount=' . $payment_amount);
            $this->url_link = request()->getSchemeAndHttpHost() . '/pay-now-inv/' . $this->url_params;

            return view('invoices.quote', $this->data);
            // return view('invoices.inventory-list', $this->data);
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('invoices.quote', $this->data);
            // return $pdf->stream(); // to view pdf
            // return $pdf->download('tmp.pdf');
            $pdf->save('invoice-files/' . $filename);

            return redirect(route('admin.list-jobs.invoice', $job_id));
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function downloadQuote($job_id)
    {
        try {
            /* $this->invoice = Invoice::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first(); */
            $this->job = JobsMoving::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();

            if ($this->job) {
                if (!empty($this->job->quote_file_name)) {
                    return response()->download(public_path('invoice-files') . '/' . $this->job->quote_file_name);
                }
            }

            return redirect(route('admin.list-jobs.invoice', $job_id));
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function viewQuote($job_id)
    {
        try {
            /* $this->invoice = Invoice::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first(); */

            $this->job = JobsMoving::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();

            if ($this->job) {
                if (!empty($this->job->quote_file_name)) {
                    return response()->file(public_path('invoice-files') . '/' . $this->job->quote_file_name);
                }
            }

            return redirect(route('admin.list-jobs.invoice', $job_id));
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function generateInvoice($job_id, $type)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '3000M');

        $response['error'] = 1;
        $response['message'] = 'Some error occured, try again later.';
        $this->invoice_settings = InvoiceSetting::where('tenant_id', auth()->user()->tenant_id)->first();
        try {
            if (empty($job_id)) {
                return redirect(route('admin.list-jobs.index'));
            }
            $this->sub_total = 0;
            $this->grand_total = 0;
            $this->tax_total = 0;
            $this->total_paid = 0;
            $this->balance_payment = 0;
            $this->count = 0;
            $this->stripe_connected=0;

            $stripe = TenantApiDetail::where('tenant_id', auth()->user()->tenant_id)
                    ->where('provider', 'Stripe')->first();
            if($stripe){
                if(isset($stripe->account_key) && !empty($stripe->account_key)){
                    $this->stripe_connected=1;
                }
            }
            $this->invoice = Invoice::where(['job_id' => $job_id, 'sys_job_type' => $type])
                ->where('tenant_id', '=', auth()->user()->tenant_id)
                ->first();
            $this->invoice_items = InvoiceItems::where('invoice_id', '=', $this->invoice->id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();    

            $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', auth()->user()->tenant_id)->first();
            $invoice_settings = InvoiceSetting::where('tenant_id', auth()->user()->tenant_id)->first();

            if (!$this->invoice) {

                $this->quotes = Quotes::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();

                $obj = new Invoice();
                $obj->tenant_id = $this->quotes->tenant_id;
                $obj->job_id = $this->quotes->job_id;
                $obj->sys_job_type = $this->quotes->sys_job_type;
                $obj->invoice_number = $this->quotes->quote_number;
                $obj->currency_id = $this->organisation_settings->currency_id;
                $obj->status = 'unpaid';
                $obj->created_at = date('Y-m-d');

                $obj->save();

                $this->invoice = $obj;
                $this->quote_items = QuoteItem::where('quote_id', '=', $this->quotes->id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
                // dd($this->quote_items);
                if ($this->quote_items) {
                    foreach ($this->quote_items as $quoteItem) {
                        $obj_item = new InvoiceItems();
                        $obj_item->tenant_id = $this->invoice->tenant_id;
                        $obj_item->invoice_id = $this->invoice->id;
                        // $obj_item->item_name = $quoteItem->name;
                        $obj_item->item_summary = $quoteItem->description;
                        $obj_item->unit_price = $quoteItem->unit_price;
                        $obj_item->quantity = $quoteItem->quantity;
                        $obj_item->type = $quoteItem->type;
                        $obj_item->amount = $quoteItem->amount;
                        $obj_item->tax_id = $quoteItem->tax_id;
                        $obj_item->created_at = date('Y-m-d');
                        // $obj_item->created_by = auth()->user()->id;
                        $obj_item->save();
                    }
                }
            }

            $current_date = date('Y-m-d');
            $this->invoice->issue_date = $current_date;
            $due_after = 15;
            if ($this->invoice_settings) {
                $due_after = $this->invoice_settings->due_after;
            }
            $this->invoice->due_date = date('Y-m-d', strtotime($current_date . ' + ' . $due_after . ' days'));

            $this->taxs = Tax::select('taxes.*')
                ->where(['taxes.tenant_id' => auth()->user()->tenant_id, 'invoice_items.invoice_id' => $this->invoice->id])
                ->whereNotNull('invoice_items.tax_id')
                ->join('invoice_items', 'invoice_items.tax_id', '=', 'taxes.id')->first();
            if ($this->invoice->sys_job_type == "Moving") {
                // $sub_total = InvoiceItems::select(DB::raw('sum(invoice_items.unit_price * invoice_items.quantity) as total'))
                //     ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
                //     ->where('invoices.job_id', '=', $job_id)->where('invoices.sys_job_type', '=', 'Moving')->first();
                // $this->sub_total = $sub_total->total;

                $total_paid = Payment::select(DB::raw('sum(payments.amount) as total'))
                                            ->join('invoices', 'invoices.id', '=', 'payments.invoice_id')
                                            ->where('invoices.job_id', '=', $job_id)
                                            ->where('invoices.sys_job_type', '=', 'Moving')
                                            ->where(['payments.tenant_id' => auth()->user()->tenant_id])
                                            ->first();
                $this->total_paid = $total_paid->total;
                $this->job = JobsMoving::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();

            } elseif ($this->invoice->sys_job_type == "Cleaning") {
                // $sub_total = InvoiceItems::select(DB::raw('sum(invoice_items.unit_price * invoice_items.quantity) as total'))
                //     ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
                //     ->where('invoices.job_id', '=', $job_id)->where('invoices.sys_job_type', '=', 'Cleaning')->first();
                // $this->sub_total = $sub_total->total;

                $total_paid = Payment::select(DB::raw('sum(payments.amount) as total'))
                                            ->join('invoices', 'invoices.id', '=', 'payments.invoice_id')
                                            ->where('invoices.job_id', '=', $job_id)
                                            ->where('invoices.sys_job_type', '=', 'Cleaning')
                                            ->where(['payments.tenant_id' => auth()->user()->tenant_id])
                                            ->first();
                $this->total_paid = $total_paid->total;
                $this->job = JobsCleaning::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            }
            $this->companies = Companies::where(['id' => $this->job->company_id, 'tenant_id' => auth()->user()->tenant_id])->first();
            $this->crm_leads = CRMLeads::where(['id' => $this->job->customer_id, 'tenant_id' => auth()->user()->tenant_id])->first();
            $this->customer_detail = CustomerDetails::where(['customer_id' => $this->job->customer_id, 'tenant_id' => auth()->user()->tenant_id])->first();
            $this->crm_contacts = CRMContacts::where(['lead_id' => $this->job->customer_id, 'tenant_id' => auth()->user()->tenant_id, 'deleted' => 'N'])->first();
            $this->crm_contact_email = CRMContactDetail::where(['contact_id' => $this->crm_contacts->id, 'tenant_id' => auth()->user()->tenant_id])->where('detail_type', '=', 'Email')->first();
            $this->crm_contact_phone = CRMContactDetail::where(['contact_id' => $this->crm_contacts->id, 'tenant_id' => auth()->user()->tenant_id])->where('detail_type', '=', 'Mobile')->first();
            $this->invoice_items = InvoiceItems::where('invoice_id', '=', $this->invoice->id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
            $this->company_logo_exists = false;

            // $this->settings = Setting::findOrFail(1);            

            $total_excl_tax=0;
            $this->tax_total=0;
            foreach($this->invoice_items as $inv_item){
                    $total_excl_tax += ($inv_item->unit_price*$inv_item->quantity);
                    $this->tax_total += $inv_item->amount - ($inv_item->unit_price*$inv_item->quantity);
            }
            $this->sub_total = $total_excl_tax; 

            //Calculating Total Values
            if ($this->invoice->discount > 0) {
                if ($this->invoice->discount_type == "percent") {
                    $this->sub_total_after_discount = $this->sub_total - ($this->invoice->discount / 100 * $this->sub_total);
                } else {
                    $this->sub_total_after_discount = $this->sub_total - $this->invoice->discount;                    
                }
            } else {
                $this->sub_total_after_discount = $this->sub_total;
            }
            // if ($this->taxs) {
            //     $this->tax_total = ($this->taxs->rate_percent * $this->sub_total_after_discount) / 100;
            // } else {
            //     $this->tax_total = 0;
            // }

            $this->invoice_settings = $invoice_settings;
            $this->invoice_total = $this->tax_total + $this->sub_total_after_discount;
            $this->balance_payment = $this->invoice_total - $this->total_paid;
            //END:: Calculation values

            $this->url_params = base64_encode('invoice_id=' . $this->invoice->id . '&payment_amount=' . $this->balance_payment);
            $this->url_link = request()->getSchemeAndHttpHost() . '/pay-now-inv/' . $this->url_params;

            //return view('admin.list-jobs.invoice', $this->data);

            $file_number = 1;
            if (!empty($this->invoice->file_original_name)) {
                $filename = str_replace('.pdf', '', $this->invoice->file_original_name);
                $fn_ary = explode('_', $filename);
                $file_number = intval($fn_ary[2]) + 1;
            }

            $filename = 'Invoice_Job_' . $this->invoice->invoice_number . '_' . rand() . '.pdf';

            if ($this->companies) {
                if (File::exists(public_path() . '/user-uploads/company-logo/' . $this->companies->logo)) {
                    $this->company_logo_exists = true;
                }
            }
            $this->is_storage_invoice=0;

            // dd($this->companies);

            // $log = new JobsMovingLogs();
            // $log->tenant_id = auth()->user()->tenant_id;
            // $log->job_id = $job_id;
            // $log->user_id = auth()->user()->id;
            // $log->log_type_id = 6;
            // $log->log_details = 'Invoice Generated - ' . $filename;
            // $log->log_date = time();
            // $log->save();
            $pdf = app('dompdf.wrapper');
            $html = view('admin.list-jobs.invoice', $this->data);
            //$pdf->loadView('admin.list-jobs.invoice', $this->data);
            $pdf->loadHtml($html, 'UTF-8');
            $pdf->getDomPDF()->set_option("enable_php", true);
            // return $pdf->stream(); // to view pdf
            // return $pdf->download('tmp.pdf');
            $pdf->save('invoice-files/' . $filename);

            if (File::exists(public_path() . '/invoice-files/' . $this->invoice->file_original_name)) {
                File::delete(public_path() . '/invoice-files/' . $this->invoice->file_original_name);
            }
            $this->invoice->file_original_name = $filename;
            $this->invoice->save();

            $response['error'] = 0;
            $response['message'] = 'Invoice generated successfully';
            return json_encode($response);
        } catch (Exception $ex) {
            $response['error'] = 1;
            $response['message'] = $ex->getMessage();
            return json_encode($response);
        }
    }

    public function downloadInvoice($job_id)
    {
        $response['error'] = 1;
        try {
            $this->invoice = Invoice::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();

            if ($this->invoice) {
                $file_url = public_path('invoice-files') . '/' . $this->invoice->file_original_name;
                if (!empty($this->invoice->file_original_name) && file_exists($file_url)) {
                    $response['error'] = 0;
                    $response['url'] = url('invoice-files') . '/' . $this->invoice->file_original_name;
                }
            }
            return json_encode($response);
        } catch (Exception $ex) {
            $response['error'] = 1;
            $response['message'] = $ex->getMessage();
            return json_encode($response);
        }
    }

    public function generateStorageInvoice($invoice_id, $type){
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '3000M');

        $response['error'] = 1;
        $response['message'] = 'Some error occured, try again later.';
        $this->invoice_settings = InvoiceSetting::where('tenant_id', auth()->user()->tenant_id)->first();
        try {
            if (empty($invoice_id)) {
                return redirect(route('admin.list-jobs.index'));
            }
            $this->sub_total = 0;
            $this->grand_total = 0;
            $this->tax_total = 0;
            $this->total_paid = 0;
            $this->balance_payment = 0;
            $this->count = 0;
            $this->stripe_connected=0;

            $stripe = TenantApiDetail::where('tenant_id', auth()->user()->tenant_id)
                    ->where('provider', 'Stripe')->first();
            if($stripe){
                if(isset($stripe->account_key) && !empty($stripe->account_key)){
                    $this->stripe_connected=1;
                }
            }
            // $job_id is actually coming invoice id in storage case
            $this->invoice = Invoice::where(['id' => $invoice_id, 'sys_job_type' => $type])
            ->where('tenant_id', '=', auth()->user()->tenant_id)
            ->first();
            if($this->invoice){
                $job_id = $this->invoice ->job_id;
            }else{
                $response['error'] = 1;
                $response['message'] = "Storage Invoice is not generated yet.";
                return json_encode($response);
            }

            $this->invoice_items = InvoiceItems::where('invoice_id', '=', $this->invoice->id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();    

            $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', auth()->user()->tenant_id)->first();
            $invoice_settings = InvoiceSetting::where('tenant_id', auth()->user()->tenant_id)->first();


            $this->taxs = Tax::select('taxes.*')
                ->where(['taxes.tenant_id' => auth()->user()->tenant_id, 'invoice_items.invoice_id' => $this->invoice->id])
                ->whereNotNull('invoice_items.tax_id')
                ->join('invoice_items', 'invoice_items.tax_id', '=', 'taxes.id')->first();
            
                $total_paid = Payment::select(DB::raw('sum(payments.amount) as total'))
                                            ->join('invoices', 'invoices.id', '=', 'payments.invoice_id')
                                            ->where('invoices.id', '=', $invoice_id)
                                            ->where(['payments.tenant_id' => auth()->user()->tenant_id])
                                            ->first();
            $this->total_paid = $total_paid->total;
            $this->job = JobsMoving::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            $this->companies = Companies::where(['id' => $this->job->company_id, 'tenant_id' => auth()->user()->tenant_id])->first();
            $this->crm_leads = CRMLeads::where(['id' => $this->job->customer_id, 'tenant_id' => auth()->user()->tenant_id])->first();
            $this->customer_detail = CustomerDetails::where(['customer_id' => $this->job->customer_id, 'tenant_id' => auth()->user()->tenant_id])->first();
            $this->crm_contacts = CRMContacts::where(['lead_id' => $this->job->customer_id, 'tenant_id' => auth()->user()->tenant_id, 'deleted' => 'N'])->first();
            $this->crm_contact_email = CRMContactDetail::where(['contact_id' => $this->crm_contacts->id, 'tenant_id' => auth()->user()->tenant_id])->where('detail_type', '=', 'Email')->first();
            $this->crm_contact_phone = CRMContactDetail::where(['contact_id' => $this->crm_contacts->id, 'tenant_id' => auth()->user()->tenant_id])->where('detail_type', '=', 'Mobile')->first();
            $this->invoice_items = InvoiceItems::where('invoice_id', '=', $this->invoice->id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
            $this->company_logo_exists = false;

            // $this->settings = Setting::findOrFail(1);            

            $total_excl_tax=0;
            $this->tax_total=0;
            foreach($this->invoice_items as $inv_item){
                    $total_excl_tax += ($inv_item->unit_price*$inv_item->quantity);
                    $this->tax_total += $inv_item->amount - ($inv_item->unit_price*$inv_item->quantity);
            }
            $this->sub_total = $total_excl_tax; 

            //Calculating Total Values
            if ($this->invoice->discount > 0) {
                if ($this->invoice->discount_type == "percent") {
                    $this->sub_total_after_discount = $this->sub_total - ($this->invoice->discount / 100 * $this->sub_total);
                } else {
                    $this->sub_total_after_discount = $this->sub_total - $this->invoice->discount;                    
                }
            } else {
                $this->sub_total_after_discount = $this->sub_total;
            }
            // if ($this->taxs) {
            //     $this->tax_total = ($this->taxs->rate_percent * $this->sub_total_after_discount) / 100;
            // } else {
            //     $this->tax_total = 0;
            // }

            $this->invoice_settings = $invoice_settings;
            $this->invoice_total = $this->tax_total + $this->sub_total_after_discount;
            $this->balance_payment = $this->invoice_total - $this->total_paid;
            //END:: Calculation values

            $this->url_params = base64_encode('invoice_id=' . $this->invoice->id . '&payment_amount=' . $this->balance_payment);
            $this->url_link = request()->getSchemeAndHttpHost() . '/pay-now-inv/' . $this->url_params;

            //return view('admin.list-jobs.invoice', $this->data);

            $file_number = 1;
            if (!empty($this->invoice->file_original_name)) {
                $filename = str_replace('.pdf', '', $this->invoice->file_original_name);
                $fn_ary = explode('_', $filename);
                $file_number = intval($fn_ary[2]) + 1;
            }

            $filename = 'Invoice_Storage_' . $this->invoice->invoice_number . '_' . rand() . '.pdf';

            if ($this->companies) {
                if (File::exists(public_path() . '/user-uploads/company-logo/' . $this->companies->logo)) {
                    $this->company_logo_exists = true;
                }
            }
            $this->is_storage_invoice=1;

            $pdf = app('dompdf.wrapper');
            $html = view('admin.list-jobs.invoice', $this->data);
            //$pdf->loadView('admin.list-jobs.invoice', $this->data);
            $pdf->loadHtml($html, 'UTF-8');
            $pdf->getDomPDF()->set_option("enable_php", true);
            // return $pdf->stream(); // to view pdf
            // return $pdf->download('tmp.pdf');
            $pdf->save('invoice-files/' . $filename);

            if (File::exists(public_path() . '/invoice-files/' . $this->invoice->file_original_name)) {
                File::delete(public_path() . '/invoice-files/' . $this->invoice->file_original_name);
            }
            $this->invoice->file_original_name = $filename;
            $this->invoice->save();

            $response['error'] = 0;
            $response['message'] = 'Storage Invoice generated successfully';
            return json_encode($response);
        } catch (Exception $ex) {
            $response['error'] = 1;
            $response['message'] = $ex->getMessage();
            return json_encode($response);
        }
    }

    public function downloadStorageInvoice($invoice_id){
        $response['error'] = 1;
        try {
            $this->invoice = Invoice::where('id', '=', $invoice_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();

            if ($this->invoice) {
                $file_url = public_path('invoice-files') . '/' . $this->invoice->file_original_name;
                if (!empty($this->invoice->file_original_name) && file_exists($file_url)) {
                    $response['error'] = 0;
                    $response['url'] = url('invoice-files') . '/' . $this->invoice->file_original_name;
                }
            }
            return json_encode($response);
        } catch (Exception $ex) {
            $response['error'] = 1;
            $response['message'] = $ex->getMessage();
            return json_encode($response);
        }
    }

    public function generateWorkOrder($job_id, $type)
    {

        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '3000M');

        $response['error'] = 1;
        $response['message'] = 'Some error occured, try again later.';
        try {
            if (empty($job_id)) {
                return redirect(route('admin.list-jobs.index'));
            }
            $this->vehicle_name = '';
            $this->start_time = '';
            $this->dispatch_notes = '';
            $this->job_driver_id = '';
            $this->job_offsider_ids = '';
            $this->vehicle_payload= '';
            $this->payment_amount = '';
            $this->call_out_fee = '';
            $this->count_offsiders = 1;
            $this->job = JobsMoving::find($job_id);

            $this->companies = Companies::where(['id' => $this->job->company_id, 'tenant_id' => auth()->user()->tenant_id])->first();
            $this->crm_leads = CRMLeads::where(['id' => $this->job->customer_id, 'tenant_id' => auth()->user()->tenant_id])->first();
            $this->customer_detail = CustomerDetails::where(['customer_id' => $this->job->customer_id, 'tenant_id' => auth()->user()->tenant_id])->first();
            $this->crm_contacts = CRMContacts::where(['lead_id' => $this->job->customer_id, 'tenant_id' => auth()->user()->tenant_id, 'deleted' => 'N'])->first();
            $this->crm_contact_email = CRMContactDetail::where(['contact_id' => $this->crm_contacts->id, 'tenant_id' => auth()->user()->tenant_id])->where('detail_type', '=', 'Email')->first();
            $this->crm_contact_phone = CRMContactDetail::where(['contact_id' => $this->crm_contacts->id, 'tenant_id' => auth()->user()->tenant_id])->where('detail_type', '=', 'Mobile')->first();
            $this->crm_contact_email = ($this->crm_contact_email) ? $this->crm_contact_email->detail : '';
            $this->crm_contact_phone = ($this->crm_contact_phone) ? $this->crm_contact_phone->detail : '';
            $this->company_logo_exists = false;

            
            $this->invoice = Invoice::where(['job_id' => $job_id, 'sys_job_type' => $type])
            ->where('tenant_id', '=', auth()->user()->tenant_id)
            ->first();
            if($this->invoice){
                $this->invoice_items = InvoiceItems::where('invoice_id', '=', $this->invoice->id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
                $this->call_out_fee = InvoiceItems::select('unit_price')
                                                ->where('invoice_id', $this->invoice->id)
                                                ->where('tenant_id', auth()->user()->tenant_id)
                                                ->where('item_name', 'LIKE', '%call%out%')
                                                ->first();
                $this->payment_amount = Payment::select(
                    'amount', 
                    'gateway',
                    'remarks'
                    )
                    ->where('invoice_id', $this->invoice->id)
                    ->where('tenant_id', auth()->user()->tenant_id)
                    ->first();
            }else{
                $this->invoice_items=0;
                $this->call_out_fee=0;
                $this->payment_amount=0;
            }

            $this->drivers = User::driverList();
            $this->people = User::allPeople();
            
            $this->job_leg = JobsMovingLegs::where('job_id', '=', $this->job->job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            if($this->job_leg){
                $this->vehicle_name = Vehicles::where(['id' => $this->job_leg->vehicle_id, 'tenant_id' => auth()->user()->tenant_id])->pluck('vehicle_name')->first();
                $this->vehicle_payload = Vehicles::where(['id' => $this->job_leg->vehicle_id, 'tenant_id' => auth()->user()->tenant_id])->pluck('payload')->first();
                $this->start_time = $this->job_leg->est_start_time;
                $this->dispatch_notes = $this->job_leg->notes;
                $this->job_driver_id = $this->job_leg->driver_id;
                $this->job_offsider_ids = $this->job_leg->offsider_ids;
                if($this->job_offsider_ids){
                    $this->count_offsiders += count(explode(',',$this->job_offsider_ids));
                }
                $this->offsiders = JobsMovingLegsTeam::where(['tenant_id' => auth()->user()->tenant_id, 'leg_id' => $this->job_leg->id, 'driver' => 'N'])->get();

            }


            if (!empty($this->job->work_order_file_name)) {
                $filename = str_replace('.pdf', '', $this->job->work_order_file_name);
                $fn_ary = explode('_', $filename);
            }

            $filename = 'Work_Order_Job_' . $this->job->job_number . '_' . rand() . '.pdf';

            if ($this->companies) {
                if (File::exists(public_path() . '/user-uploads/company-logo/' . $this->companies->logo)) {
                    $this->company_logo_exists = true;
                }
            }

            $pdf = app('dompdf.wrapper');
            // return view('admin.list-jobs.workorder-pdf', $this->data);
            $html = view('admin.list-jobs.workorder-pdf', $this->data);
            //$pdf->loadView('admin.list-jobs.invoice', $this->data);
            $pdf->loadHtml($html, 'UTF-8');
            $pdf->getDomPDF()->set_option("enable_php", true);
            // return $pdf->stream(); // to view pdf
            // return $pdf->download('tmp.pdf');
            $pdf->save('invoice-files/' . $filename);

            if (File::exists(public_path() . '/invoice-files/' . $this->job->work_order_file_name)) {
                File::delete(public_path() . '/invoice-files/' . $this->job->work_order_file_name);
            }
            $this->job->work_order_file_name = $filename;
            $this->job->save();

            $response['error'] = 0;
            $response['message'] = 'Work Order generated successfully';
            return json_encode($response);
        } catch (Exception $ex) {
            $response['error'] = 1;
            $response['message'] = $ex->getMessage();
            return json_encode($response);
        }
    }

    public function downloadWordOrder($job_id)
    {
        $response['error'] = 1;
        try {
            $this->job = JobsMoving::find($job_id);

            if ($this->job) {
                $file_url = public_path('invoice-files') . '/' . $this->job->work_order_file_name;
                if (!empty($this->job->work_order_file_name) && file_exists($file_url)) {
                    $response['error'] = 0;
                    $response['url'] = url('invoice-files') . '/' . $this->job->work_order_file_name;
                }
            }
            return json_encode($response);
        } catch (Exception $ex) {
            $response['error'] = 1;
            $response['message'] = $ex->getMessage();
            return json_encode($response);
        }
    }

    public function generatePod($job_id, $type)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '3000M');

        $response['error'] = 1;
        $response['message'] = 'Some error occured, try again later.';
        try {
            if (empty($job_id)) {
                return redirect(route('admin.list-jobs.index'));
            }
            $this->job = JobsMoving::find($job_id);

            $this->companies = Companies::where(['id' => $this->job->company_id, 'tenant_id' => auth()->user()->tenant_id])->first();
            $this->crm_leads = CRMLeads::where(['id' => $this->job->customer_id, 'tenant_id' => auth()->user()->tenant_id])->first();
            $this->customer_detail = CustomerDetails::where(['customer_id' => $this->job->customer_id, 'tenant_id' => auth()->user()->tenant_id])->first();
            $this->crm_contacts = CRMContacts::where(['lead_id' => $this->job->customer_id, 'tenant_id' => auth()->user()->tenant_id, 'deleted' => 'N'])->first();
            $this->crm_contact_email = CRMContactDetail::where(['contact_id' => $this->crm_contacts->id, 'tenant_id' => auth()->user()->tenant_id])->where('detail_type', '=', 'Email')->first();
            $this->crm_contact_phone = CRMContactDetail::where(['contact_id' => $this->crm_contacts->id, 'tenant_id' => auth()->user()->tenant_id])->where('detail_type', '=', 'Mobile')->first();
            $this->crm_contact_email = ($this->crm_contact_email) ? $this->crm_contact_email->detail : '';
            $this->crm_contact_phone = ($this->crm_contact_phone) ? $this->crm_contact_phone->detail : '';
            $this->company_logo_exists = false;
            $this->is_customer_signature=0;
            
            $this->jobs_moving_leg = JobsMovingLegs::where(['job_id' => $job_id, 'tenant_id' => auth()->user()->tenant_id])->first();
            if($this->jobs_moving_leg){
                if($this->jobs_moving_leg->leg_status != 'Completed') {
                    $response['error'] = 2;
                    $response['message'] = 'The Job must be Completed to generate a POD';
                    return json_encode($response);
                }
                if($this->jobs_moving_leg->customer_sign == null) {
                    $response['error'] = 2;
                    $response['message'] = 'Customer signature doesn’t exist to generate a POD';
                    return json_encode($response);
                }else{
                    $customer_sign = substr($this->jobs_moving_leg->customer_sign, strrpos($this->jobs_moving_leg->customer_sign, '/public') + 1);
                    $this->customer_sign = url('/'.$customer_sign);  
                    $this->is_customer_signature=1;
                }
            }else{
                    $response['error'] = 2;
                    $response['message'] = 'No Leg available.';
                    return json_encode($response);
            }

            if (!empty($this->job->pod_file_name)) {
                $filename = str_replace('.pdf', '', $this->job->pod_file_name);
                $fn_ary = explode('_', $filename);
            }

            $filename = 'POD_' . $this->job->job_number . '_' . rand() . '.pdf';

            if ($this->companies) {
                if (File::exists(public_path() . '/user-uploads/company-logo/' . $this->companies->logo)) {
                    $this->company_logo_exists = true;
                }
            }

            $pdf = app('dompdf.wrapper');
            // return view('admin.list-jobs.pod-pdf', $this->data);
            $html = view('admin.list-jobs.pod-pdf', $this->data);
            //$pdf->loadView('admin.list-jobs.invoice', $this->data);
            $pdf->loadHtml($html, 'UTF-8');
            $pdf->getDomPDF()->set_option("enable_php", true);
            // return $pdf->stream(); // to view pdf
            // return $pdf->download('tmp.pdf');
            $pdf->save('invoice-files/' . $filename);

            if (File::exists(public_path() . '/invoice-files/' . $this->job->pod_file_name)) {
                File::delete(public_path() . '/invoice-files/' . $this->job->pod_file_name);
            }
            $this->job->pod_file_name = $filename;
            $this->job->save();

            $response['error'] = 0;
            $response['message'] = 'POD generated successfully';
            return json_encode($response);
        } catch (Exception $ex) {
            $response['error'] = 1;
            $response['message'] = $ex->getMessage();
            return json_encode($response);
        }
    }

    public function downloadPod($job_id)
    {
        $response['error'] = 1;
        try {
            $this->job = JobsMoving::find($job_id);

            if ($this->job) {
                $file_url = public_path('invoice-files') . '/' . $this->job->pod_file_name;
                if (!empty($this->job->pod_file_name) && file_exists($file_url)) {
                    $response['error'] = 0;
                    $response['url'] = url('invoice-files') . '/' . $this->job->pod_file_name;
                }
            }
            return json_encode($response);
        } catch (Exception $ex) {
            $response['error'] = 1;
            $response['message'] = $ex->getMessage();
            return json_encode($response);
        }
    }

    public function generateInventoryPdf($job_id, $type)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '3000M');

        $response['error'] = 1;
        $response['message'] = 'Some error occured, try again later.';
        try {
            if (empty($job_id)) {
                return redirect(route('admin.list-jobs.index'));
            }
            $this->job = JobsMoving::find($job_id);

            $this->companies = Companies::where(['id' => $this->job->company_id, 'tenant_id' => auth()->user()->tenant_id])->first();
            $this->crm_leads = CRMLeads::where(['id' => $this->job->customer_id, 'tenant_id' => auth()->user()->tenant_id])->first();
            $this->customer_detail = CustomerDetails::where(['customer_id' => $this->job->customer_id, 'tenant_id' => auth()->user()->tenant_id])->first();
            $this->crm_contacts = CRMContacts::where(['lead_id' => $this->job->customer_id, 'tenant_id' => auth()->user()->tenant_id, 'deleted' => 'N'])->first();
            $this->crm_contact_email = CRMContactDetail::where(['contact_id' => $this->crm_contacts->id, 'tenant_id' => auth()->user()->tenant_id])->where('detail_type', '=', 'Email')->first();
            $this->crm_contact_phone = CRMContactDetail::where(['contact_id' => $this->crm_contacts->id, 'tenant_id' => auth()->user()->tenant_id])->where('detail_type', '=', 'Mobile')->first();
            $this->crm_contact_email = ($this->crm_contact_email) ? $this->crm_contact_email->detail : '';
            $this->crm_contact_phone = ($this->crm_contact_phone) ? $this->crm_contact_phone->detail : '';
            $this->company_logo_exists = false;
            $this->is_customer_signature=0;

            $this->pricingAdditional = JobsMovingPricingAdditional::where(['tenant_id' => auth()->user()->tenant_id])->first();

            if (!empty($this->job->inventory_file_name)) {
                $filename = str_replace('.pdf', '', $this->job->inventory_file_name);
                $fn_ary = explode('_', $filename);
            }

            $filename = 'Inventory_List_' . $this->job->job_number . '_' . rand() . '.pdf';

            if ($this->companies) {
                if (File::exists(public_path() . '/user-uploads/company-logo/' . $this->companies->logo)) {
                    $this->company_logo_exists = true;
                }
            }
            $this->inventory_items = JobsMovingInventory::select(
                'jobs_moving_inventory.*', 
                'moving_inventory_definitions.cbm',
                'moving_inventory_definitions.item_name',
                )
                ->leftJoin('moving_inventory_definitions', 'moving_inventory_definitions.id', '=', 'jobs_moving_inventory.inventory_id')
                ->where('jobs_moving_inventory.job_id', $job_id)
                ->where(['jobs_moving_inventory.tenant_id' => auth()->user()->tenant_id])
                ->get();

    
            $pdf = app('dompdf.wrapper');
            // return view('admin.list-jobs.inventory-pdf', $this->data);
            $html = view('admin.list-jobs.inventory-pdf', $this->data);
            //$pdf->loadView('admin.list-jobs.invoice', $this->data);
            $pdf->loadHtml($html, 'UTF-8');
            $pdf->getDomPDF()->set_option("enable_php", true);
            // return $pdf->stream(); // to view pdf
            // return $pdf->download('tmp.pdf');
            $pdf->save('invoice-files/' . $filename);

            if (File::exists(public_path() . '/invoice-files/' . $this->job->inventory_file_name)) {
                File::delete(public_path() . '/invoice-files/' . $this->job->inventory_file_name);
            }
            $this->job->inventory_file_name = $filename;
            $this->job->save();

            $response['error'] = 0;
            $response['message'] = 'Inventory List generated successfully';
            return json_encode($response);
        } catch (Exception $ex) {
            $response['error'] = 1;
            $response['message'] = $ex->getMessage();
            return json_encode($response);
        }
    }

    public function downloadInventoryPdf($job_id)
    {
        $response['error'] = 1;
        try {
            $this->job = JobsMoving::find($job_id);

            if ($this->job) {
                $file_url = public_path('invoice-files') . '/' . $this->job->inventory_file_name;
                if (!empty($this->job->inventory_file_name) && file_exists($file_url)) {
                    $response['error'] = 0;
                    $response['url'] = url('invoice-files') . '/' . $this->job->inventory_file_name;
                } else {
                    $response['error'] = 2;
                    $response['message'] = 'File will be remove please Regenerate Inventory List';
                }
            }
            return json_encode($response);
        } catch (Exception $ex) {
            $response['error'] = 1;
            $response['message'] = $ex->getMessage();
            return json_encode($response);
        }
    }

    public function viewInvoice($job_id)
    {
        try {
            $this->invoice = Invoice::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();

            if ($this->invoice) {
                if (!empty($this->invoice->file_original_name)) {
                    return response()->file(public_path('invoice-files') . '/' . $this->invoice->file_original_name);
                }
            }

            return redirect(route('admin.list-jobs.invoice', $job_id));
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function generateInventoryList($job_id)
    {
        try {
            if (empty($job_id)) {
                return redirect(route('admin.list-jobs.index'));
            }
            $this->invoice = Invoice::where('job_id', '=', $job_id)->where('invoices.tenant_id', '=', auth()->user()->tenant_id)->first();
            $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', auth()->user()->tenant_id)->first();
            if ($this->invoice) {
                $this->job = JobsMoving::where('job_id', '=', $job_id)->where('jobs_moving.tenant_id', '=', auth()->user()->tenant_id)->first();

                $this->invoiceItems = InvoiceItems::where('invoice_id', '=', $this->invoice->id)->where('invoice_items.tenant_id', '=', auth()->user()->tenant_id)->first();
                if (!$this->invoiceItems) {
                    $obj = new InvoiceItems();
                    $obj->tenant_id = auth()->user()->tenant_id;
                    $obj->invoice_id = $this->invoice->id;
                    $obj->item_name = 'Removal Fee';
                    $obj->item_summary = 'From: ' . $this->job->pickup_address . ' To: ' . $this->job->drop_off_address;
                    $obj->type = 'Item';
                    $obj->quantity = '1';
                    $obj->unit_price = floatval($this->job->fixed_other_rate);
                    $obj->amount = floatval($this->job->fixed_other_rate);

                    $obj->save();
                }
                $this->company = Companies::find($this->job->company_id);
                $this->settings = Setting::findOrFail(1);
                $this->invoiceSetting = InvoiceSetting::where(['tenant_id' => auth()->user()->tenant_id])->first();

                $nameSpace = '\\App\\';
                $modelName = $nameSpace . 'Jobs' . ucfirst($this->invoice->sys_job_type);
                $jobs = $modelName::where(['job_id' => $this->invoice->job_id, 'tenant_id' => auth()->user()->tenant_id])->first();
                if ($jobs) {
                    $this->job_id = $jobs->job_number;
                } else {
                    $this->job_id = $this->job->job_number;
                }

                $this->job_items = JobsMovingInventory::select('jobs_moving_inventory.inventory_id', 'jobs_moving_inventory.quantity', 'jobs_moving_inventory.misc_item_name', 'moving_inventory_definitions.item_name', 'moving_inventory_groups.group_name')
                    ->where('jobs_moving_inventory.tenant_id', '=', auth()->user()->tenant_id)
                    ->where('jobs_moving_inventory.job_id', '=', $job_id)
                    ->join('moving_inventory_definitions', 'moving_inventory_definitions.id', 'jobs_moving_inventory.inventory_id')
                    ->join('moving_inventory_groups', 'moving_inventory_groups.id', 'moving_inventory_definitions.group_id')
                    ->orderBy('jobs_moving_inventory.inventory_id', 'ASC')->get();

                $file_number = 1;
                if (!empty($this->invoice->file)) {
                    $filename = str_replace('.pdf', '', $this->invoice->file);
                    $fn_ary = explode('_', $filename);
                    $file_number = intval($fn_ary[2]) + 1;
                }

                $filename = 'InventoryList_Job' . $this->invoice->invoice_number . '_' . $this->job->job_number . '_' . rand() . '.pdf';
                if (File::exists(public_path() . '/invoice-files/' . $filename)) {
                    File::delete(public_path() . '/invoice-files/' . $filename);
                }
                $this->invoice->file = $filename;
                $this->invoice->save();

                $log = new JobsMovingLogs();
                $log->tenant_id = auth()->user()->tenant_id;
                $log->job_id = $job_id;
                $log->user_id = auth()->user()->id;
                $log->log_type_id = '11';
                $log->log_details = 'Inventory List Generated - ' . $filename;
                $log->log_date = time();
                $log->updated_at = time();
                $log->save();

                // return view('invoices.inventory-list', $this->data);
                $pdf = app('dompdf.wrapper');
                $pdf->loadView('invoices.inventory-list', $this->data);
                // return $pdf->stream(); // to view pdf
                // return $pdf->download('tmp.pdf');
                $pdf->save('invoice-files/' . $filename);
            }

            return redirect(route('admin.list-jobs.invoice', $job_id));
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function downloadInventoryList($job_id)
    {
        try {
            $this->invoice = Invoice::where('job_id', '=', $job_id)->where('invoices.tenant_id', '=', auth()->user()->tenant_id)->first();

            if ($this->invoice) {
                if (!empty($this->invoice->file)) {
                    return response()->download(public_path('invoice-files') . '/' . $this->invoice->file);
                }
            }

            return redirect(route('admin.list-jobs.invoice', $job_id));
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function viewInventoryList($job_id)
    {
        try {
            $this->invoice = Invoice::where('job_id', '=', $job_id)->where('invoices.tenant_id', '=', auth()->user()->tenant_id)->first();

            if ($this->invoice) {
                if (!empty($this->invoice->file)) {
                    return response()->file(public_path('invoice-files') . '/' . $this->invoice->file);
                }
            }

            return redirect(route('admin.list-jobs.invoice', $job_id));
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function email($job_id)
    {
        try {
            if (empty($job_id)) {
                return redirect(route('admin.list-jobs.index'));
            }
            $this->job = JobsMoving::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            $this->email_templates = EmailTemplates::where('tenant_id', '=', auth()->user()->tenant_id)->where('company_id', '=', $this->job->company_id)->get();

            $this->invoice = Invoice::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();

            $this->attachments = JobsMovingLogs::where('tenant_id', '=', auth()->user()->tenant_id)->where('job_id', '=', $job_id)->where('log_type_id', '=', '10')->get();

            $this->job_template_attachments = JobTemplatesMovingAttachment::where('tenant_id', '=', auth()->user()->tenant_id)->where('job_template_id', '=', $this->job->job_template_id)->get();

            return view('admin.list-jobs.email', $this->data);
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function viewAttachment($log_id)
    {
        try {
            $this->attachments = JobsMovingLogs::where('tenant_id', '=', auth()->user()->tenant_id)->where('id', '=', $log_id)->where('log_type_id', '=', '10')->first();

            if ($this->attachments) {

                if ($this->attachments->log_details) {
                    $destinationPath = public_path('/user-uploads/tenants/' . auth()->user()->tenant_id);
                    //File::isDirectory($destinationPath) or File::makeDirectory($destinationPath, 0777, true, true);
                    //dd($destinationPath . '/' . $this->attachments->log_details);
                    return response()->download($destinationPath . '/' . $this->attachments->log_details);
                }
            }

            return redirect(route('admin.list-jobs.invoice', $log_id));
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function viewJobTemplateAttachment($id)
    {        
        try {
            $this->attachments = JobTemplatesMovingAttachment::where('tenant_id', '=', auth()->user()->tenant_id)->where('id', '=', $id)->first();

            if ($this->attachments) {

                if ($this->attachments->attachment_file_name) {
                    $destinationPath = public_path('/job-template');
                    //File::isDirectory($destinationPath) or File::makeDirectory($destinationPath, 0777, true, true);
                    //dd($destinationPath . '/' . $this->attachments->log_details);
                    return response()->download($destinationPath . '/' . $this->attachments->attachment_file_name);
                }
            }

            return redirect(route('admin.list-jobs.invoice', $id));
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function emailSend(Request $request, $job_id)
    {
        try {
            if (empty($job_id)) {
                return redirect(route('admin.list-jobs.index'));
            }
            $data = $request->all();
            $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', auth()->user()->tenant_id)->first();
            $this->tenant_api_details = \App\TenantApiDetail::where('tenant_id', '=', auth()->user()->tenant_id)->first();
            $data['from_email'] = $this->tenant_api_details->from_email;
            $data['from_name'] = $this->organisation_settings->company_name;
            $data['reply_to'] = $this->tenant_api_details->to_email;
            Mail::to($data['to'])
                ->send(new CustomerMail($data));

            $this->job = JobsMoving::where('job_id', '=', $job_id)
                ->where('tenant_id', '=', auth()->user()->tenant_id)
                ->first();
            $this->email_templates = EmailTemplates::where('tenant_id', '=', auth()->user()->tenant_id)
                ->where('company_id', '=', $this->job->company_id)
                ->get();

            $attachmentFileNames = '';
            if (isset($data['files']) && $data['files'] != null) {
                foreach ($data['files'] as $file) {
                    $attachmentFileNames .= '<br/>' . $file;
                }
            }

            $job_logs = new JobsMovingLogs();
            $job_logs->tenant_id = auth()->user()->tenant_id;
            $job_logs->job_id = $job_id;
            $job_logs->user_id = auth()->user()->id;
            $job_logs->log_type_id = 3;
            $job_logs->log_details = $data['email_body'];
            //$job_logs->email_from = $data['email_body'];
            $job_logs->email_to = $data['to'];
            $job_logs->email_subject = $data['email_subject'];
            $job_logs->email_attachments = $attachmentFileNames;
            //$job_logs->email_status = $attachmentFileNames;

            $job_logs->log_date = date('Y-m-d h:i:s');
            $job_logs->save();

            return redirect(route('admin.list-jobs.email', [$job_id]));
            //return view('admin.list-jobs.email', $this->data);
            //return redirect(route('admin.list-jobs.index'));
            //            return Reply::redirect(route('admin.list-jobs.index'), 'Email Send Successfully.');
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function attachment($job_id)
    {
        try {
            if (empty($job_id)) {
                return redirect(route('admin.list-jobs.index'));
            }
            $this->job = JobsMoving::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();

            $this->attachments = JobsMovingLogs::where('tenant_id', '=', auth()->user()->tenant_id)->where('job_id', '=', $job_id)->where('log_type_id', '=', '10')->get();

            return view('admin.list-jobs.attachment', $this->data);
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function attachmentUpload(Request $request, $job_id)
    {
        try {
            if (empty($job_id)) {
                return redirect(route('admin.list-jobs.index'));
            }
            if ($request->hasFile('attachment')) {
                $image = $request->file('attachment');
                $input['imagename'] = $job_id . '-' . date('Y') . '-' . $image->getClientOriginalName();
                $destinationPath = public_path('/user-uploads/tenants/' . auth()->user()->tenant_id);
                File::isDirectory($destinationPath) or File::makeDirectory($destinationPath, 0777, true, true);
                $img = Image::make($image->getRealPath());
                $img->save($destinationPath . '/' . $input['imagename']);
                $job_logs = new JobsMovingLogs();
                $job_logs->tenant_id = auth()->user()->tenant_id;
                $job_logs->job_id = $job_id;
                $job_logs->user_id = auth()->user()->id;
                $job_logs->log_type_id = 10;
                $job_logs->log_details = $input['imagename'];
                $job_logs->log_date = date('Y-m-d h:i:s');
                $job_logs->save();
            }
            //return redirect(route('admin.list-jobs.index'));
            return redirect(route('admin.list-jobs.attachment', [$job_id]));
            /* return redirect(route('admin.list-jobs.attachment', [$job_id]), 'Attachment upload Successfully.'); */
            /* return Reply::redirect(route('admin.list-jobs.index'), 'Attachment upload Successfully.'); */
        } catch (\Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function sms($job_id)
    {
        try {
            if (empty($job_id)) {
                return redirect(route('admin.list-jobs.index'));
            }
            $this->job = JobsMoving::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();

            $this->customer = Customers::findOrFail($this->job->customer_id);

            $this->sms_templates = SMSTemplates::where('tenant_id', '=', auth()->user()->tenant_id)
                ->where('company_id', '=', $this->job->company_id)
                ->get();

            $this->companies = Companies::where('tenant_id', '=', auth()->user()->tenant_id)->first();

            $this->tenant_details = \App\TenantDetail::where('tenant_id', auth()->user()->tenant_id)
                ->first();
            return view('admin.list-jobs.sms', $this->data);
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function smsSend(Request $request, $job_id)
    {
        try {
            if (empty($job_id)) {
                return redirect(route('admin.list-jobs.index'));
            }
            //$this->tenant_api_details = \App\TenantApiDetail::where('tenant_id', '=', auth()->user()->tenant_id)->first();
            $tenant_details = \App\TenantDetail::where('tenant_id', auth()->user()->tenant_id)->first();

            $sys_api_details = \App\SysApiSettings::where('type', '=', 'sms_gateway')->first();
            $sys_api_details->user;
            $sys_api_details->password;

            $data = $request->all();

            $username = $sys_api_details->user;
            $password = $sys_api_details->password;
            $destination = $request->sms_number; //Multiple numbers can be entered, separated by a comma
            $source = $request->sms_from; //'Onexfort';
            $text = $request->sms_message;
            $ref = $request->job_id;

            $content = 'username=' . rawurlencode($username) .
            '&password=' . rawurlencode($password) .
            '&to=' . rawurlencode($destination) .
            '&from=' . rawurlencode($source) .
            '&message=' . rawurlencode($text) .
            '&maxsplit=5' .
            '&ref=' . rawurlencode($ref);
            if ($tenant_details->sms_credit < $request->total_credits) {
                return redirect()->route('admin.list-jobs.sms', [$job_id])->with('error', 'SMS can not be sent. Beacause you have Insufficient credit. Please buy more credits.');
            } else {
                $smsbroadcast_response = $this->sendSMSFunc($content);
                $response_lines = explode("\n", $smsbroadcast_response);
            }

            foreach ($response_lines as $data_line) {
                $message_data = "";
                $message_data = explode(':', $data_line);
                if ($message_data[0] == "OK") {
                    //echo "The message to ".$message_data[1]." was successful, with reference ".$message_data[2]."\n";
                    $job_logs = new JobsMovingLogs();
                    $job_logs->tenant_id = auth()->user()->tenant_id;
                    $job_logs->job_id = $job_id;
                    $job_logs->user_id = auth()->user()->id;
                    $job_logs->log_type_id = 8;
                    $job_logs->log_details = $data['sms_message'];
                    $job_logs->sms_from = $request->sms_from;
                    $job_logs->sms_to = $request->sms_number;
                    $job_logs->log_date = date('Y-m-d h:i:s');
                    $job_logs->save();

                    $tenant_total_credits = $tenant_details->sms_credit;

                    $subtractCredits = $tenant_details->sms_credit - $tenant_total_credits;

                    $subtractCredits = $tenant_total_credits - $request->total_credits;
                    $tenant_details->id = auth()->user()->tenant_id;
                    $UpdateTenantCredits = \App\TenantDetail::where('tenant_id', '=', auth()->user()->tenant_id)->update(array('sms_credit' => $subtractCredits));

                    //Run SMS Auto Top Up Program 
                    $this->smsAutoTopUp($tenant_details);
                    //END::----------->
                    
                    return redirect()->route('admin.list-jobs.sms', [$job_id])->with('success', 'SMS has been sent to ' . $message_data[1] . ' successfully!');

                } elseif ($message_data[0] == "BAD") {
                    //echo "The message to ".$message_data[1]." was NOT successful. Reason: ".$message_data[2]."\n";
                    return redirect()->route('admin.list-jobs.sms', [$job_id])->with('error', 'SMS has not been sent to ' . $message_data[1] . '. Reason is ' . $message_data[2]);
                } elseif ($message_data[0] == "ERROR") {
                    return redirect()->route('admin.list-jobs.sms', [$job_id])->with('error', 'There was an error in the request. Please try again later!');
                }
            }

            return redirect(route('admin.list-jobs.sms', [$job_id]));

        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    protected function smsAutoTopUp($tenant_details)
    {
        if($tenant_details->sms_auto_top_up=='Y' && $tenant_details->sms_credit <= $tenant_details->sms_balance_lower_limit){
            if($tenant_details->stripe_customer_id!=NULL && $tenant_details->stripe_customer_id!=""){

                $sms = \App\SysApiSettings::where('type', 'tenant_sms_purchase')->where('in_use', '1')->first();

                $pay_amount = ((($tenant_details->sms_balance_top_up_qty * $sms->per_unit_cost) * (1+$sms->variable1/100)) + $sms->variable2) * 100;
                $topup_data['tenant_id'] = $tenant_details->tenant_id;
                $topup_data['stripe_customer_id'] = $tenant_details->stripe_customer_id;
                $topup_data['auto_topup'] = 'Y';
                $topup_data['sms_credit'] = $tenant_details->sms_balance_top_up_qty;
                $topup_data['sms_balance_lower_limit'] = $tenant_details->sms_balance_lower_limit;
                $topup_data['sms_balance_top_up_qty'] = $tenant_details->sms_balance_top_up_qty;
                $topup_data['amount'] = $pay_amount;
                $topup_data['stripeToken'] = '';
                $topup_data['stripeEmail'] = '';
                $res = $tenant_details->smsStripeCharge($topup_data);
            }
        }
    }

    protected function sendSMSFunc($content)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.smsbroadcast.com.au/api-adv.php?' . $content);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        if ($output === false) {
            //echo "Error Number:".curl_errno($ch)."<br>";
            //echo "Error String:".curl_error($ch);
        }
        //dd($output[]);
        curl_close($ch);
        return $output;
    }

    public function getSMSTemplate(Request $request, $id)
    {
        try {
            $this->sms_template = SMSTemplates::where(['id' => $id, 'tenant_id' => auth()->user()->tenant_id])->first();

            if ($this->sms_template) {

                echo json_encode(array(
                    'status' => 'success',
                    'sms_message' => $this->sms_template->sms_message,
                ));

            } else {
                echo json_encode("0");
            }
        } catch (Exception $ex) {
            echo json_encode($ex->getMessage());
        }
    }

    public function insurance($job_id)
    {
        try {
            if (empty($job_id)) {
                return redirect(route('admin.list-jobs.index'));
            }
            $this->job = JobsMoving::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            $this->customer = Customers::findOrFail($this->job->customer_id);
            $request_id = 0;
            $insurance_response = '';
            $request_response = array();
            $find_moving_insurance_quote_request = MovingInsuranceQuoteRequest::where('tenant_id', auth()->user()->tenant_id)
                ->where('job_id', $job_id)
                ->first();
            if ($find_moving_insurance_quote_request) {
                $request_id = $find_moving_insurance_quote_request->id;
                $find_moving_insurance_quote_response = MovingInsuranceQuoteResponse::where('request_id', $request_id)
                    ->first();
                if ($find_moving_insurance_quote_response) {
                    $insurance_response = $find_moving_insurance_quote_response;
                }
            }
            $this->insurance_response = $insurance_response;
            $this->request_id = $request_id;
            $this->tenant_details = \App\TenantDetail::where('tenant_id', auth()->user()->tenant_id)
                ->first();
            return view('admin.list-jobs.insurance', $this->data);
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function sendQuoteToCustomer($job_id)
    {

        $this->job = JobsMoving::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $this->customer = Customers::findOrFail($this->job->customer_id);
        $request_id = 0;
        $url = "https://coverfreightonline.com.au/api/AU/quote";
        $reqParamArray['name'] = $this->customer->first_name . ' ' . $this->customer->last_name;
        $reqParamArray['email'] = $this->customer->email;
        $reqParamArray['from'] = $this->job->pickup_suburb;
        $reqParamArray['to'] = $this->job->delivery_suburb;
        $reqParamArray['value'] = $this->job->goods_value;
        $reqParamArray['commodity'] = 'household goods and personal effects';
        $reqParamArray['container'] = true;
        $reqParamArray['reference'] = 'T-' . auth()->user()->tenant_id . '-J-' . $this->job->job_number;
        $reqParamArray['key'] = 'ONEXFORT';
        $reqParamArray['fee'] = '0.0';
        $reqParamArray['fee_gst'] = '0.0';
        try {
            $http = new Client([
                'headers' => ['Content-Type' => 'application/json'],
            ]);
            $response = $http->post($url, [
                'body' => json_encode($reqParamArray),
            ]);
            $responseData = json_decode($response->getBody()->getContents());
            if ($responseData && $responseData->success) {
                $find_moving_insurance_quote_request = MovingInsuranceQuoteRequest::where('tenant_id', auth()->user()->tenant_id)
                    ->where('job_id', $this->job->job_id)
                    ->first();
                if ($find_moving_insurance_quote_request) {
                    $find_moving_insurance_quote_request->reference = $responseData->reference;
                    $find_moving_insurance_quote_request->notes = $responseData->comment;
                    $find_moving_insurance_quote_request->save();
                    $moving_insurance_quote_request_id = $find_moving_insurance_quote_request->id;
                } else {
                    $moving_insurance_quote_request = new MovingInsuranceQuoteRequest();
                    $moving_insurance_quote_request->tenant_id = auth()->user()->tenant_id;
                    $moving_insurance_quote_request->job_id = $job_id;
                    $moving_insurance_quote_request->reference = $responseData->reference;
                    $moving_insurance_quote_request->notes = $responseData->comment;
                    $moving_insurance_quote_request->save();
                    $moving_insurance_quote_request_id = $moving_insurance_quote_request->id;
                }
                $this->request_id = $moving_insurance_quote_request_id;
                $find_moving_insurance_quote_response = MovingInsuranceQuoteResponse::where('request_id', $moving_insurance_quote_request_id)
                    ->first();
                if ($find_moving_insurance_quote_response) {
                    $find_moving_insurance_quote_response->reference = $responseData->reference;
                    $find_moving_insurance_quote_response->premium = $responseData->premium;
                    $find_moving_insurance_quote_response->gst = $responseData->gst;
                    $find_moving_insurance_quote_response->fee = $responseData->fee;
                    $find_moving_insurance_quote_response->fee_gst = $responseData->fee_gst;
                    $find_moving_insurance_quote_response->insurance_quote_id = $responseData->quote;
                    $find_moving_insurance_quote_response->status = $responseData->success;
                    $find_moving_insurance_quote_response->comment = $responseData->comment;
                    $find_moving_insurance_quote_response->save();
                    $find_moving_insurance_quote_response_id = $moving_insurance_quote_response->id;
                } else {
                    $moving_insurance_quote_response = new MovingInsuranceQuoteResponse();
                    $moving_insurance_quote_response->request_id = $moving_insurance_quote_request_id;
                    $moving_insurance_quote_response->reference = $responseData->reference;
                    $moving_insurance_quote_response->premium = $responseData->premium;
                    $moving_insurance_quote_response->gst = $responseData->gst;
                    $moving_insurance_quote_response->fee = $responseData->fee;
                    $moving_insurance_quote_response->fee_gst = $responseData->fee_gst;
                    $moving_insurance_quote_response->insurance_quote_id = $responseData->quote;
                    $moving_insurance_quote_response->status = $responseData->success;
                    $moving_insurance_quote_response->comment = $responseData->comment;
                    $moving_insurance_quote_response->save();
                    $moving_insurance_quote_response_id = $moving_insurance_quote_response->id;
                }
            }

            return redirect(route('admin.list-jobs.insurance', [$job_id]));
        } catch (\Exception $ex) {
            echo $ex->getMessage();
            echo '<br>';
            echo $ex->getLine();
            exit;
        }
    }

    public function getEmailTemplate(Request $request, $id)
    {
        try {
            $this->email_template = EmailTemplates::where(['id' => $id, 'tenant_id' => auth()->user()->tenant_id])->first();

            $this->job = JobsMoving::where('job_id', '=', $request->job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            $this->customer = Customers::findOrFail($this->job->customer_id);

            $this->invoice = Invoice::where('job_id', '=', $request->job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();

            $this->paidAmount = 0;
            $this->totalAmount = 0;
            if (isset($this->invoice->id)):
                $this->paidAmount = $this->invoice->getPaidAmount();
                $this->totalAmount = $this->invoice->getTotalAmount();
            endif;

            //$this->paidAmount = $this->invoice->getPaidAmount();
            // $this->totalAmount = $this->invoice->getTotalAmount();

            if ($this->email_template) {

                $data = [
                    'job_id' => $this->job->job_number,
                    'first_name' => $this->customer->first_name,
                    'last_name' => $this->customer->last_name,
                    'pickup_address' => $this->job->pickup_address,
                    'delivery_address' => $this->job->drop_off_address,
                    'phone' => $this->customer->phone,
                    //'pick_up_access' => ($this->job->pickup_access_restrictions),
               // 'drop_off_access' => ($this->job->drop_off_access_restrictions),
                    'job_date' => date('d-m-Y', strtotime($this->job->job_date)),
                    'email' => $this->customer->email,
                    'total_amount' => $this->totalAmount,
                    'total_paid' => $this->paidAmount,
                    'total_due' => $this->totalAmount - $this->paidAmount,
                ];

                $subject = $this->email_template->email_subject;
                if (preg_match_all("/{(.*?)}/", $subject, $m)) {
                    foreach ($m[1] as $i => $varname) {
                        $subject = str_replace($m[0][$i], sprintf('%s', $data[$varname]), $subject);
                    }
                }

                $template = $this->email_template->email_body;

                if (preg_match_all("/{(.*?)}/", $template, $m)) {
                    foreach ($m[1] as $i => $varname) {
                        $template = str_replace($m[0][$i], sprintf('%s', $data[$varname]), $template);
                    }
                }

                echo json_encode(array(
                    'status' => 'success',
                    'subject' => $subject,
                    'body' => $template,
                ));
            } else {
                echo json_encode("0");
            }
        } catch (Exception $ex) {
            echo json_encode($ex->getMessage());
        }
    }

    //START:: New Design Job Moving View

    public function viewJob($job_id)
    {
        //try {
        if (empty($job_id)) {
                return redirect(route('admin.list-jobs.index'));
        }
        $this->users = User::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $this->ppl_people = PplPeople::where(['user_id' => auth()->user()->id, 'tenant_id' => auth()->user()->tenant_id])->first();
        $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $this->google_api_key = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'GoogleMaps'])->pluck('account_key')->first();
        //echo auth()->user()->tenant_id;
        $this->job_id = $job_id;
        $this->job = JobsMoving::where('job_id', '=', $job_id)
            ->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $this->crmlead = CRMLeads::findOrFail($this->job->customer_id);
        
        // dd($this->job_id);
        //START:: *****************Invoice Tab***********************
        $this->invoice_items = 0;
        $this->payment_items = 0;
        $this->paidAmount = 0;
        $this->totalAmount = 0;
        $this->crm_contact_email=null;
        $this->crm_contact_phone=null;
        $this->invoice_items = null;
        $this->invoice_charges = null;
        $this->payment_items = null;

        $this->stripe = TenantApiDetail::where('tenant_id', auth()->user()->tenant_id)
                    ->where('provider', 'Stripe')->first();
        $this->invoice = Invoice::where(['job_id'=> $job_id,'sys_job_type'=>'Moving'])->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        if (isset($this->invoice->id)):
            $this->invoice_items = InvoiceItems::where(['invoice_id'=>$this->invoice->id,'tenant_id'=>auth()->user()->tenant_id])->whereIn('type',['Item','Service'])->get();
            $this->invoice_charges = InvoiceItems::where(['invoice_id'=>$this->invoice->id,'tenant_id'=>auth()->user()->tenant_id,'type'=>'Charge'])->get();            
            $this->payment_items = Payment::where('invoice_id', '=', $this->invoice->id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
        else:
            $this->invoice = (object)['id'=>0,'discount'=>0,'discount_type'=>''];
        endif;
        $this->payment_methods = OfflinePaymentMethod::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $this->taxs = Tax::where(['tenant_id' => auth()->user()->tenant_id])->get();
        if (isset($this->invoice->id) && $this->invoice->id>0):
            $this->paidAmount = $this->invoice->getPaidAmount();
            $this->totalAmount = $this->invoice->getTotalAmount();
        endif;
        
        if($this->crmlead->lead_type == 'Residential'){
            $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                        ->where('product_type', '!=', 'Charge')
                                        ->where(function ($query) {
                                            $query->orWhere('customer_type', 'Residential')
                                                ->orWhere('customer_type', 'Both');
                                        })
                                        ->orderBy('name', 'asc')
                                        ->get();
            $this->charges = Product::where(['tenant_id' => auth()->user()->tenant_id, 'product_type' => 'Charge'])
                                        ->where(function ($query) {
                                            $query->orWhere('customer_type', 'Residential')
                                                ->orWhere('customer_type', 'Both');
                                        })
                                        ->orderBy('name', 'asc')
                                        ->get();
        }else{
            $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                        ->where('product_type', '!=', 'Charge')
                                        ->where(function ($query) {
                                            $query->orWhere('customer_type', 'Commercial')
                                                ->orWhere('customer_type', 'Both');
                                        })
                                        ->where(function ($query) {
                                            $query->orWhere('customer_id', NULL)
                                                ->orWhere('customer_id', $this->crmlead->id);
                                        })
                                        ->orderBy('name', 'asc')
                                        ->get();
            $this->charges = Product::where(['tenant_id' => auth()->user()->tenant_id, 'product_type' => 'Charge'])
                                    ->where(function ($query) {
                                        $query->orWhere('customer_type', 'Commercial')
                                            ->orWhere('customer_type', 'Both');
                                    })
                                    ->where(function ($query) {
                                        $query->orWhere('customer_id', NULL)
                                            ->orWhere('customer_id', $this->crmlead->id);
                                    })
                                    ->orderBy('name', 'asc')
                                    ->get();
        }
        //START:: *****************Activity Tab***********************

        $this->lead_id = CRMOpportunities::where('id', '=', $this->job->crm_opportunity_id)
            ->where('tenant_id', '=', auth()->user()->tenant_id)->pluck('lead_id')->first();
        $this->lead_name = CRMLeads::where(['id' => $this->job->customer_id, 'tenant_id' => auth()->user()->tenant_id])->pluck('name')->first();
        $this->job_ids=[0,$job_id];
        //Activity Section
        $this->notes = CRMActivityLog::where(['lead_id' => $this->lead_id, 'tenant_id' => auth()->user()->tenant_id])
            ->whereIn('log_type', array(3, 4, 5, 7, 8, 11, 14, 15))
            ->where(function ($query) {
                $query->whereIn('job_id', $this->job_ids)
                    ->orWhere('job_id','=', NULL);
            })
            ->orderBy('id', 'DESC')
            ->get(); 
        $this->tasks = CRMTasks::where(['lead_id' => $this->lead_id, 'tenant_id' => auth()->user()->tenant_id])->orderBy('id', 'DESC')->get();    
        $this->totalTasks = count($this->tasks);

        $this->job_status = Lists::job_status();
        //$this->price_structure = Lists::price_structure();
        //$this->payment_status = Lists::payment_status();
        //$this->lead_info = Lists::lead_info();

        $this->job_type = Lists::job_type();
        $this->leg_status = Lists::leg_status();
        $this->vehicles = Vehicles::select('id', 'vehicle_name')
            ->where('tenant_id', '=', auth()->user()->tenant_id)
            ->get();
        $this->drivers = User::driverList();
        $this->people = User::allPeople();

        $this->job_templates = JobTemplatesMoving::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $this->job_legs = JobsMovingLegs::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $this->contacts = CRMContacts::where(['tenant_id' => auth()->user()->tenant_id, 'lead_id' => $this->lead_id, 'deleted' => 'N'])->orderBy('id', 'DESC')->get();
        $this->crm_contacts = CRMContacts::where('lead_id', '=', $this->lead_id)->where('deleted', 'N')->first();
        if($this->crm_contacts){
            $this->crm_contact_email = CRMContactDetail::where(['contact_id' => $this->crm_contacts->id, 'tenant_id' => auth()->user()->tenant_id])->where('detail_type', '=', 'Email')->first();
            $this->crm_contact_phone = CRMContactDetail::where(['contact_id' => $this->crm_contacts->id, 'tenant_id' => auth()->user()->tenant_id])->where('detail_type', '=', 'Mobile')->first();
        }
        $this->crm_contact_email = ($this->crm_contact_email) ? $this->crm_contact_email->detail : '';
        $this->crm_contact_phone = ($this->crm_contact_phone) ? $this->crm_contact_phone->detail : '';
        if($this->job->company_id>0)
            $this->companies = Companies::where(['id' => $this->job->company_id, 'active' => 'Y', 'tenant_id' => auth()->user()->tenant_id])->first();
        else
            $this->companies = Companies::where(['tenant_id'=>auth()->user()->tenant_id,'active'=>'Y'])->first();
        $this->company_list = Companies::where(['tenant_id' => auth()->user()->tenant_id])->get();

        $this->sms_templates = SMSTemplates::where(['tenant_id' => auth()->user()->tenant_id,'active'=>'Y'])->orderBy('sms_template_name', 'ASC')->get();
        $this->email_templates = EmailTemplates::where(['tenant_id' => auth()->user()->tenant_id,'active'=>'Y'])->orderBy('email_template_name', 'ASC')->get();
        $this->sms_contacts = DB::table('crm_contacts')
            ->join('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
            ->select('crm_contacts.name', 'crm_contact_details.detail')
            ->where(['crm_contacts.tenant_id' => auth()->user()->tenant_id, 'crm_contacts.lead_id' => $this->lead_id, 'crm_contact_details.detail_type' => 'Mobile', 'crm_contacts.deleted' => 'N'])
            ->get();

        $this->property_category = PropertyCategoryOptions::where(['tenant_id' => auth()->user()->tenant_id])->get();
        $this->states = SysCountryStates::where(['country_id' => $this->organisation_settings->business_country_id])->get();

        //END:: Activity Tab

        //START:: *****************Operation Tab***********************
        $this->attachments = DB::table('crm_activity_log_attachments')
            ->join('crm_activity_log', 'crm_activity_log.id', '=', 'crm_activity_log_attachments.log_id')
            ->select('crm_activity_log_attachments.*')
            ->where(['crm_activity_log.tenant_id' => auth()->user()->tenant_id, 'crm_activity_log.lead_id' => $this->job->customer_id])
            ->get();

        $this->pickup_risk_assessment = DB::table('jobs_moving_ohs_checklist')
                                                ->where(['tenant_id' => auth()->user()->tenant_id, 'job_id' => $this->job_id, 'pickup_delivery' => 'Pickup'])
                                                ->get();

        $this->delivery_risk_assessment = DB::table('jobs_moving_ohs_checklist')
                                                ->where(['tenant_id' => auth()->user()->tenant_id, 'job_id' => $this->job_id, 'pickup_delivery' => 'Delivery'])
                                                ->get();
        //END:: *****************Operation Tab***********************            

        //START:: *****************Inventory Tab***********************

        $this->inventory_groups = MovingInventoryGroups::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $this->getInventoryItems = MovingInventoryDefinitions::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        // $this->countInvItems = JobsMovingInventory::where('inventory_id', '>', 9000)->where('job_id', $job_id)->count();
        $this->miscllanceous_items = JobsMovingInventory::where(['misc_item' => 'Y', 'tenant_id' => auth()->user()->tenant_id])->where('job_id', $this->job->job_id)->get();
        
        //START:: *****************Insurance Tab***********************
        /*$request_id = 0;
        $insurance_response = '';
        $request_response = array();
        $find_moving_insurance_quote_request = MovingInsuranceQuoteRequest::where(['tenant_id' => auth()->user()->tenant_id, 'job_id' => $job_id])->first();

        if ($find_moving_insurance_quote_request) {
            $request_id = $find_moving_insurance_quote_request->id;
            $find_moving_insurance_quote_response = MovingInsuranceQuoteResponse::where('request_id', $request_id)
                ->first();
            if ($find_moving_insurance_quote_response) {
                $insurance_response = $find_moving_insurance_quote_response;
            }
        }
        $this->insurance_response = $insurance_response;
        $this->request_id = $request_id;
        $this->tenant_details = \App\TenantDetail::where(['tenant_id' => auth()->user()->tenant_id])
            ->first();*/
        //END::Insurance Tab

        $this->contact_types = ListTypes::where(['list_types.tenant_id' => auth()->user()->tenant_id, 'list_types.list_name' => 'Contact Type'])
            ->join('list_options', 'list_options.list_type_id', '=', 'list_types.id')
            ->select('list_options.list_option')
            ->get();

        $this->material_issue_items = DB::table('inv_job_items_issued')->select([
                                        'inv_job_items_issued.*',
                                        'products.name',
                                        'products.description',
                                    ])
                                    ->where(['inv_job_items_issued.tenant_id' => auth()->user()->tenant_id, 'inv_job_items_issued.job_id' => $this->job_id])
                                    ->leftjoin('products', 'products.id', 'inv_job_items_issued.item_id')
                                    ->get();

        $this->material_return_items = DB::table('inv_job_items_returned')->select([
                                        'inv_job_items_returned.*',
                                        'products.name',
                                        'products.description',
                                    ])
                                    ->where(['inv_job_items_returned.tenant_id' => auth()->user()->tenant_id, 'inv_job_items_returned.job_id' => $this->job_id])
                                    ->leftjoin('products', 'products.id', 'inv_job_items_returned.item_id')
                                    ->get();

        //Cover Freight Insurance setting
                $coverFreight = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'CoverFreight'])->first();            
                if($coverFreight){ 
                    $this->coverFreight_connected=true;
                }else{
                    $this->coverFreight_connected=false;
                }     
        $this->job_price_additional = DB::table('jobs_moving_pricing_additional as t1')
                ->select('t1.*')
                ->where(['t1.tenant_id' => auth()->user()->tenant_id])
                ->first();                                       
        return view('admin.list-jobs.jobs.view_job', $this->data);
        
    }

    public function ajaxSaveInvoice(Request $request)
    {
        if($request->job_id)
        {
            $this->job = JobsMoving::where(['job_id' => $request->job_id, 'tenant_id' => auth()->user()->tenant_id])->first();
        }
        if($this->job)
        {
            $this->crmlead = CRMLeads::findOrFail($this->job->customer_id);
        }
        $job_id = $request->input('job_id');
        $sys_job_type = $request->input('sys_job_type');
        $sys_job_type = ($sys_job_type=="Moving_Storage")?"Moving_Storage":"Moving";
        $invoice_id = $request->input('invoice_id');
        $this->invoice = Invoice::where('id', '=', $invoice_id)
            ->where('tenant_id', '=', auth()->user()->tenant_id)
            ->first();
        if (!$this->invoice) {
            if($sys_job_type=="Moving" || $sys_job_type=="Moving_Storage"){
                $job = JobsMoving::where(['job_id' => $job_id, 'tenant_id' => auth()->user()->tenant_id])->first();
            }else{
                $job = JobsCleaning::where(['job_id' => $job_id, 'tenant_id' => auth()->user()->tenant_id])->first();
            }
            if ($job) {
                $new_invoice_number = $job->job_number;
            } else {
                $res = Invoice::select(DB::raw('invoice_number'))->where('tenant_id', '=', auth()->user()->tenant_id)->orderBy('id', 'DESC')->first();
                $new_invoice_number = intval($res->invoice_number) + 1;
                $new_invoice_number = 1;
            }

            $obj = new Invoice();
            $obj->tenant_id = auth()->user()->tenant_id;
            $obj->job_id = $job_id;
            $obj->invoice_number = $new_invoice_number;
            $obj->sys_job_type = $sys_job_type;
            $obj->project_id = 1;
            $current_date = date('Y-m-d');
            $obj->issue_date = $current_date;
            $due_after = 15;
            $invoice_settings = InvoiceSetting::where('tenant_id', auth()->user()->tenant_id)->first();
            if ($invoice_settings) {
                $due_after = $invoice_settings->due_after;
            }
            $obj->due_date = date('Y-m-d', strtotime($current_date . ' + ' . $due_after . ' days'));

            $obj->save();

            $this->invoice = $obj;
        }
        if (isset($this->invoice)) {
            //------->
            $obj_item = new InvoiceItems();
            $obj_item->tenant_id = auth()->user()->tenant_id;
            $obj_item->invoice_id = $this->invoice->id;
            $obj_item->item_name = $request->input('name');
            $obj_item->product_id = $request->input('product_id');
            $obj_item->item_summary = $request->input('description');
            $obj_item->tax_id = $request->input('tax_id');
            $obj_item->type = $request->input('type');
            $obj_item->quantity = $request->input('quantity');
            $obj_item->unit_price = $request->input('unit_price');
            $obj_item->amount = $request->input('amount');
            $obj_item->save();


            $total = InvoiceItems::where(['invoice_id' => $this->invoice->id, 'tenant_id' => auth()->user()->tenant_id])->sum('amount');
            $this->invoice->total = $total;
            $this->invoice->save();

            //response
            if($sys_job_type=="Moving_Storage"){
                $this->invoice_items = 0;
                $this->payment_items = 0;
                $this->paidAmount = 0;
                $this->totalAmount = 0;
                $this->crm_contact_email=null;
                $this->crm_contact_phone=null;
                $this->stripe = TenantApiDetail::where('tenant_id', auth()->user()->tenant_id)
                            ->where('provider', 'Stripe')->first();
                $this->invoice = Invoice::where(['job_id'=> $job_id,'sys_job_type'=>'Moving_Storage'])->where('tenant_id', '=', auth()->user()->tenant_id)->first();
                if (isset($this->invoice->id)):
                    $this->invoice_items = InvoiceItems::where(['invoice_id'=>$this->invoice->id,'tenant_id'=>auth()->user()->tenant_id])->whereIn('type',['Item','Service'])->get();
                    $this->invoice_charges = InvoiceItems::where(['invoice_id'=>$this->invoice->id,'tenant_id'=>auth()->user()->tenant_id,'type'=>'Charge'])->get();            
                    $this->payment_items = Payment::where('invoice_id', '=', $this->invoice->id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
                endif;
                $this->payment_methods = OfflinePaymentMethod::where('tenant_id', '=', auth()->user()->tenant_id)->get();
                $this->taxs = Tax::where(['tenant_id' => auth()->user()->tenant_id])->get();
                if (isset($this->invoice->id)):
                    $this->paidAmount = $this->invoice->getPaidAmount();
                    $this->totalAmount = $this->invoice->getTotalAmount();
                endif;
                $this->products = Product::select("products.*")
                ->join('product_categories', 'product_categories.id', 'products.category_id')
                ->where(['products.tenant_id' => auth()->user()->tenant_id,'product_categories.category_name'=>'Storage'])
                ->get();
                $response['error'] = 0;
                $response['message'] = 'Invoice item has been saved';
                $response['storage_invoice_html'] = view('admin.list-jobs.jobs.storage.storage_invoice_grid', $this->data)->render();
                $response['storage_payment_html'] = view('admin.list-jobs.jobs.storage.storage_payment_grid', $this->data)->render();
            }else{
                $this->invoice_items = InvoiceItems::where(['invoice_id'=>$this->invoice->id,'tenant_id'=>auth()->user()->tenant_id])->where('type', '<>', 'Charge')->get();
                $this->invoice_charges = InvoiceItems::where(['invoice_id'=>$this->invoice->id,'tenant_id'=>auth()->user()->tenant_id])->where('type', '=', 'Charge')->get();
                $this->paidAmount = $this->invoice->getPaidAmount();
                $this->totalAmount = $this->invoice->getTotalAmount();
                $balance = $this->totalAmount - $this->paidAmount;
                $this->taxs = Tax::where(['tenant_id' => auth()->user()->tenant_id])->get();

                if($this->crmlead->lead_type == 'Residential'){
                    $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                                ->where('product_type', '!=', 'Charge')
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_type', 'Residential')
                                                        ->orWhere('customer_type', 'Both');
                                                })->get();
                    $this->charges = Product::where(['tenant_id' => auth()->user()->tenant_id, 'product_type' => 'Charge'])
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_type', 'Residential')
                                                        ->orWhere('customer_type', 'Both');
                                                })->get();
                }else{
                    $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                                ->where('product_type', '!=', 'Charge')
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_type', 'Commercial')
                                                        ->orWhere('customer_type', 'Both');
                                                })
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_id', NULL)
                                                        ->orWhere('customer_id', $this->crmlead->id);
                                                })->get();
                    $this->charges = Product::where(['tenant_id' => auth()->user()->tenant_id, 'product_type' => 'Charge'])
                                            ->where(function ($query) {
                                                $query->orWhere('customer_type', 'Commercial')
                                                    ->orWhere('customer_type', 'Both');
                                            })
                                            ->where(function ($query) {
                                                $query->orWhere('customer_id', NULL)
                                                    ->orWhere('customer_id', $this->crmlead->id);
                                            })->get();
                }

                $response['error'] = 0;
                $response['message'] = 'Invoice item has been saved';
                $response['amount'] = $this->global->currency_symbol . '' . $this->totalAmount;
                $response['paid'] = $this->global->currency_symbol . '' . $this->paidAmount;
                $response['balance'] = $this->global->currency_symbol . '' . $balance;
                $response['invoice_id'] = $this->invoice->id;
                $response['html'] = view('admin.list-jobs.jobs.invoice_grid', $this->data)->render();
            }
            return json_encode($response);
        }
    }

    public function ajaxCalculateChargePrice(Request $request)
    {
        $invoice_id = $request->input('invoice_id');
        $charge_price = $request->input('price');

        $record = InvoiceItems::select(DB::raw('SUM(unit_price*quantity) as sub_total'))->where(['invoice_id' => $invoice_id, 'tenant_id' => auth()->user()->tenant_id])->first();
        return number_format((float)($record->sub_total * $charge_price), 2, '.', '');
    }

    public function ajaxReCalculateChargePrice(Request $request)
    {
        $invoice_id = $request->input('invoice_id');
        $job_id = $request->input('job_id');
        $service_total = 0;

        $service = InvoiceItems::where(['invoice_id' => $invoice_id, 'tenant_id' => auth()->user()->tenant_id])->where('type', '<>', 'Charge')->get();
        if(count($service))
        {
            foreach($service as $s)
            {
                $service_total += $s->unit_price * $s->quantity;
            }
        }
        else
        {
            $response['error'] = 1;
            $response['message'] = 'There is no any invoive item!';
            return $response;
        }

        $charges = InvoiceItems::where(['invoice_id' => $invoice_id, 'tenant_id' => auth()->user()->tenant_id, 'type' => 'Charge'])->get();
        $sum = $service_total;
        if(count($charges))
        {
            foreach($charges as $charge)
            {
                $product = Product::where(['tenant_id' => auth()->user()->tenant_id, 'id' => $charge->product_id])->first();
                $new_charge = $sum * $product->price;
                $sum += $new_charge;
                // dd($sum);
                $charge->update([
                    'unit_price' => $new_charge,
                    'amount' => $new_charge
                ]);
            }
        }
        else
        {
            $response['error'] = 1;
            $response['message'] = 'There is no any Charges in this invoice!';
            return $response;
        }

        $this->job_id = $job_id;
        $this->job = JobsMoving::where('job_id', '=', $job_id)
            ->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $this->crmlead = CRMLeads::findOrFail($this->job->customer_id);

        $this->invoice = Invoice::where(['id'=> $invoice_id,'sys_job_type'=>'Moving'])->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        if (isset($this->invoice->id)):
            $this->invoice_items = InvoiceItems::where(['invoice_id'=>$this->invoice->id,'tenant_id'=>auth()->user()->tenant_id])->whereIn('type',['Item','Service'])->get();
            $this->invoice_charges = InvoiceItems::where(['invoice_id'=>$this->invoice->id,'tenant_id'=>auth()->user()->tenant_id,'type'=>'Charge'])->get();            
            $this->payment_items = Payment::where('invoice_id', '=', $this->invoice->id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
        else:
            $this->invoice = (object)['id'=>0,'discount'=>0,'discount_type'=>''];
        endif;

        $this->paidAmount = $this->invoice->getPaidAmount();
        $this->totalAmount = $this->invoice->getTotalAmount();
        $balance = $this->totalAmount - $this->paidAmount;
        $this->taxs = Tax::where(['tenant_id' => auth()->user()->tenant_id])->get();
        $this->invoice_charges = InvoiceItems::where(['invoice_id'=>$this->invoice->id,'tenant_id'=>auth()->user()->tenant_id])->where('type', '=', 'Charge')->get();

        if($this->crmlead->lead_type == 'Residential'){
            $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                        ->where('product_type', '!=', 'Charge')
                                        ->where(function ($query) {
                                            $query->orWhere('customer_type', 'Residential')
                                                ->orWhere('customer_type', 'Both');
                                        })
                                        ->orderBy('name', 'asc')
                                        ->get();
            $this->charges = Product::where(['tenant_id' => auth()->user()->tenant_id, 'product_type' => 'Charge'])
                                        ->where(function ($query) {
                                            $query->orWhere('customer_type', 'Residential')
                                                ->orWhere('customer_type', 'Both');
                                        })
                                        ->orderBy('name', 'asc')
                                        ->get();
        }else{
            $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                        ->where('product_type', '!=', 'Charge')
                                        ->where(function ($query) {
                                            $query->orWhere('customer_type', 'Commercial')
                                                ->orWhere('customer_type', 'Both');
                                        })
                                        ->where(function ($query) {
                                            $query->orWhere('customer_id', NULL)
                                                ->orWhere('customer_id', $this->crmlead->id);
                                        })
                                        ->orderBy('name', 'asc')
                                        ->get();
            $this->charges = Product::where(['tenant_id' => auth()->user()->tenant_id, 'product_type' => 'Charge'])
                                    ->where(function ($query) {
                                        $query->orWhere('customer_type', 'Commercial')
                                            ->orWhere('customer_type', 'Both');
                                    })
                                    ->where(function ($query) {
                                        $query->orWhere('customer_id', NULL)
                                            ->orWhere('customer_id', $this->crmlead->id);
                                    })
                                    ->orderBy('name', 'asc')
                                    ->get();
        }

        $response['error'] = 0;
        $response['message'] = 'Invoice items has been Recalculated!';
        $response['amount'] = $this->global->currency_symbol . '' . $this->totalAmount;
        $response['paid'] = $this->global->currency_symbol . '' . $this->paidAmount;
        $response['balance'] = $this->global->currency_symbol . '' . $balance;
        $response['invoice_id'] = $this->invoice->id;
        $response['html'] = view('admin.list-jobs.jobs.invoice_grid', $this->data)->render();
        return $response;

    }

    public function ajaxDestroyInvoiceItem(Request $request)
    {
        if($request->job_id)
        {
            $this->job = JobsMoving::where(['job_id' => $request->job_id, 'tenant_id' => auth()->user()->tenant_id])->first();
        }
        if($this->job)
        {
            $this->crmlead = CRMLeads::findOrFail($this->job->customer_id);
        }
        
        $id = $request->input('id');
        $job_id = $request->input('job_id');
        $invoice_id = $request->input('invoice_id');
        InvoiceItems::destroy($id);
        $sys_job_type = $request->input('sys_job_type');
        $sys_job_type = ($sys_job_type=="Moving_Storage")?"Moving_Storage":"Moving";

        $this->invoice = Invoice::where('id', '=', $invoice_id)
            ->where('tenant_id', '=', auth()->user()->tenant_id)
            ->first();

        
        $total = InvoiceItems::where(['invoice_id' => $this->invoice->id, 'tenant_id' => auth()->user()->tenant_id])->sum('amount');
        $this->invoice->total = $total;
        $this->invoice->save();
        //------->
        //response
        if($sys_job_type=="Moving_Storage"){
            $this->invoice_items = 0;
            $this->payment_items = 0;
            $this->paidAmount = 0;
            $this->totalAmount = 0;
            $this->crm_contact_email=null;
            $this->crm_contact_phone=null;
            $this->stripe = TenantApiDetail::where('tenant_id', auth()->user()->tenant_id)
                        ->where('provider', 'Stripe')->first();
            $this->invoice = Invoice::where(['job_id'=> $job_id,'sys_job_type'=>'Moving_Storage'])->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            if (isset($this->invoice->id)):
                $this->invoice_items = InvoiceItems::where(['invoice_id'=>$this->invoice->id,'tenant_id'=>auth()->user()->tenant_id])->whereIn('type',['Item','Service'])->get();
                $this->invoice_charges = InvoiceItems::where(['invoice_id'=>$this->invoice->id,'tenant_id'=>auth()->user()->tenant_id,'type'=>'Charge'])->get();            
                $this->payment_items = Payment::where('invoice_id', '=', $this->invoice->id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
            endif;
            $this->payment_methods = OfflinePaymentMethod::where('tenant_id', '=', auth()->user()->tenant_id)->get();
            $this->taxs = Tax::where(['tenant_id' => auth()->user()->tenant_id])->get();
            if (isset($this->invoice->id)):
                $this->paidAmount = $this->invoice->getPaidAmount();
                $this->totalAmount = $this->invoice->getTotalAmount();
            endif;
            $this->products = Product::select("products.*")
            ->join('product_categories', 'product_categories.id', 'products.category_id')
            ->where(['products.tenant_id' => auth()->user()->tenant_id,'product_categories.category_name'=>'Storage'])
            ->get();
            $response['error'] = 0;
            $response['message'] = 'Invoice item has been deleted';
            $response['storage_invoice_html'] = view('admin.list-jobs.jobs.storage.storage_invoice_grid', $this->data)->render();
            $response['storage_payment_html'] = view('admin.list-jobs.jobs.storage.storage_payment_grid', $this->data)->render();
        }else{
            $this->invoice_items = InvoiceItems::where(['invoice_id'=>$this->invoice->id,'tenant_id'=>auth()->user()->tenant_id])->where('type', '<>', 'Charge')->get();
            $this->invoice_charges = InvoiceItems::where(['invoice_id'=>$this->invoice->id,'tenant_id'=>auth()->user()->tenant_id])->where('type', '=', 'Charge')->get();
            $this->paidAmount = $this->invoice->getPaidAmount();
            $this->totalAmount = $this->invoice->getTotalAmount();
            $balance = $this->totalAmount - $this->paidAmount;
            $this->taxs = Tax::where(['tenant_id' => auth()->user()->tenant_id])->get();

            if($this->crmlead->lead_type == 'Residential'){
                $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                            ->where('product_type', '!=', 'Charge')
                                            ->where(function ($query) {
                                                $query->orWhere('customer_type', 'Residential')
                                                    ->orWhere('customer_type', 'Both');
                                            })->get();
                $this->charges = Product::where(['tenant_id' => auth()->user()->tenant_id, 'product_type' => 'Charge'])
                                            ->where(function ($query) {
                                                $query->orWhere('customer_type', 'Residential')
                                                    ->orWhere('customer_type', 'Both');
                                            })->get();
            }else{
                $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                            ->where('product_type', '!=', 'Charge')
                                            ->where(function ($query) {
                                                $query->orWhere('customer_type', 'Commercial')
                                                    ->orWhere('customer_type', 'Both');
                                            })
                                            ->where(function ($query) {
                                                $query->orWhere('customer_id', NULL)
                                                    ->orWhere('customer_id', $this->crmlead->id);
                                            })->get();
                $this->charges = Product::where(['tenant_id' => auth()->user()->tenant_id, 'product_type' => 'Charge'])
                                        ->where(function ($query) {
                                            $query->orWhere('customer_type', 'Commercial')
                                                ->orWhere('customer_type', 'Both');
                                        })
                                        ->where(function ($query) {
                                            $query->orWhere('customer_id', NULL)
                                                ->orWhere('customer_id', $this->crmlead->id);
                                        })->get();
            }
            $response['error'] = 0;
            $response['message'] = 'Invoice item has been deleted';
            $response['amount'] = $this->global->currency_symbol . '' . $this->totalAmount;
            $response['paid'] = $this->global->currency_symbol . '' . $this->paidAmount;
            $response['balance'] = $this->global->currency_symbol . '' . $balance;
            $response['html'] = view('admin.list-jobs.jobs.invoice_grid', $this->data)->render();
        }
        return json_encode($response);
    }

    public function ajaxSaveInvoiceDiscount(Request $request)
    {
        $invoice_id = $request->input('invoice_id');
        $discount_type = $request->input('discount_type');
        $discount = $request->input('discount');
        $sys_job_type = $request->input('sys_job_type');
        $sys_job_type = ($sys_job_type=="Moving_Storage")?"Moving_Storage":"Moving";
        $lead_id = $request->input('lead_id');

        $this->invoice = Invoice::where('id', '=', $invoice_id)
            ->where('tenant_id', '=', auth()->user()->tenant_id)
            ->first();
        if ($this->invoice) {
            $this->invoice->discount = $discount;
            $this->invoice->discount_type = $discount_type;
            $this->invoice->save();
        }
        //------->
        //response
        if($sys_job_type=="Moving_Storage"){
            $this->invoice_items = 0;
            $this->payment_items = 0;
            $this->paidAmount = 0;
            $this->totalAmount = 0;
            $this->crm_contact_email=null;
            $this->crm_contact_phone=null;
            $this->stripe = TenantApiDetail::where('tenant_id', auth()->user()->tenant_id)
                        ->where('provider', 'Stripe')->first();
            // $this->invoice = Invoice::where(['job_id'=> $job_id,'sys_job_type'=>'Moving_Storage'])->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            if (isset($this->invoice->id)):
                $this->invoice_items = InvoiceItems::where(['invoice_id'=>$this->invoice->id,'tenant_id'=>auth()->user()->tenant_id])->whereIn('type',['Item','Service'])->get();
                $this->invoice_charges = InvoiceItems::where(['invoice_id'=>$this->invoice->id,'tenant_id'=>auth()->user()->tenant_id,'type'=>'Charge'])->get();            
                $this->payment_items = Payment::where('invoice_id', '=', $this->invoice->id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
            endif;
            $this->payment_methods = OfflinePaymentMethod::where('tenant_id', '=', auth()->user()->tenant_id)->get();
            $this->taxs = Tax::where(['tenant_id' => auth()->user()->tenant_id])->get();
            if (isset($this->invoice->id)):
                $this->paidAmount = $this->invoice->getPaidAmount();
                $this->totalAmount = $this->invoice->getTotalAmount();
            endif;
            $this->products = Product::select("products.*")
            ->join('product_categories', 'product_categories.id', 'products.category_id')
            ->where(['products.tenant_id' => auth()->user()->tenant_id,'product_categories.category_name'=>'Storage'])
            ->get();
            $response['error'] = 0;
            $response['message'] = 'Invoice item has been updated';
            $response['storage_invoice_html'] = view('admin.list-jobs.jobs.storage.storage_invoice_grid', $this->data)->render();
            $response['storage_payment_html'] = view('admin.list-jobs.jobs.storage.storage_payment_grid', $this->data)->render();
        }else{
            $this->crmlead = CRMLeads::findOrFail($lead_id);
            $this->invoice_items = InvoiceItems::where(['invoice_id'=>$this->invoice->id,'tenant_id'=>auth()->user()->tenant_id])->where('type', '<>', 'Charge')->get();
            $this->invoice_charges = InvoiceItems::where(['invoice_id'=>$this->invoice->id,'tenant_id'=>auth()->user()->tenant_id])->where('type', '=', 'Charge')->get();
            $this->paidAmount = $this->invoice->getPaidAmount();
            $this->totalAmount = $this->invoice->getTotalAmount();
            $balance = $this->totalAmount - $this->paidAmount;
            $this->taxs = Tax::where(['tenant_id' => auth()->user()->tenant_id])->get();
            if($this->crmlead->lead_type == 'Residential'){
                $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                            ->where('product_type', '!=', 'Charge')
                                            ->where(function ($query) {
                                                $query->orWhere('customer_type', 'Residential')
                                                    ->orWhere('customer_type', 'Both');
                                            })->get();
                $this->charges = Product::where(['tenant_id' => auth()->user()->tenant_id, 'product_type' => 'Charge'])
                                            ->where(function ($query) {
                                                $query->orWhere('customer_type', 'Residential')
                                                    ->orWhere('customer_type', 'Both');
                                            })->get();
            }else{
                $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                            ->where('product_type', '!=', 'Charge')
                                            ->where(function ($query) {
                                                $query->orWhere('customer_type', 'Commercial')
                                                    ->orWhere('customer_type', 'Both');
                                            })
                                            ->where(function ($query) {
                                                $query->orWhere('customer_id', NULL)
                                                    ->orWhere('customer_id', $this->crmlead->id);
                                            })->get();
                $this->charges = Product::where(['tenant_id' => auth()->user()->tenant_id, 'product_type' => 'Charge'])
                                        ->where(function ($query) {
                                            $query->orWhere('customer_type', 'Commercial')
                                                ->orWhere('customer_type', 'Both');
                                        })
                                        ->where(function ($query) {
                                            $query->orWhere('customer_id', NULL)
                                                ->orWhere('customer_id', $this->crmlead->id);
                                        })->get();
            }
            $response['error'] = 0;
            $response['message'] = 'Invoice item has been updated';
            $response['amount'] = $this->global->currency_symbol . '' . $this->totalAmount;
            $response['paid'] = $this->global->currency_symbol . '' . $this->paidAmount;
            $response['balance'] = $this->global->currency_symbol . '' . $balance;
            $response['html'] = view('admin.list-jobs.jobs.invoice_grid', $this->data)->render();
        }
        return json_encode($response);
    }

    public function ajaxUpdateInvoice(Request $request)
    {
        $job=NULL;
        if($request->job_id)
        {
            $job = JobsMoving::where(['job_id' => $request->job_id, 'tenant_id' => auth()->user()->tenant_id])->first();
        }
        if($job)
        {
            $this->crmlead = CRMLeads::findOrFail($job->customer_id);
        }
        $id = $request->input('id');
        $job_id = $request->input('job_id');
        $invoice_id = $request->input('invoice_id');
        $sys_job_type = $request->input('sys_job_type');
        $sys_job_type = ($sys_job_type=="Moving_Storage")?"Moving_Storage":"Moving";
        $this->invoice = Invoice::where('id', '=', $invoice_id)
            ->where('tenant_id', '=', auth()->user()->tenant_id)
            ->first();
        if (isset($this->invoice)) {
            $obj_item = InvoiceItems::where(['id' => $id, 'tenant_id' => auth()->user()->tenant_id])->first();
            $obj_item->tenant_id = auth()->user()->tenant_id;
            $obj_item->invoice_id = $this->invoice->id;
            $obj_item->item_name = $request->input('name');
            $obj_item->item_summary = $request->input('description');
            $obj_item->tax_id = $request->input('tax_id');
            $obj_item->type = $request->input('type');
            $obj_item->quantity = $request->input('quantity');
            $obj_item->unit_price = $request->input('unit_price');
            $obj_item->amount = $request->input('amount');
            $obj_item->save();

            $total = InvoiceItems::where(['invoice_id' => $this->invoice->id, 'tenant_id' => auth()->user()->tenant_id])->sum('amount');
            $this->invoice->total = $total;
            $this->invoice->save();

            //response
            if($sys_job_type=="Moving_Storage"){
                $this->invoice_items = 0;
                $this->payment_items = 0;
                $this->paidAmount = 0;
                $this->totalAmount = 0;
                $this->crm_contact_email=null;
                $this->crm_contact_phone=null;
                $this->stripe = TenantApiDetail::where('tenant_id', auth()->user()->tenant_id)
                            ->where('provider', 'Stripe')->first();
                $this->invoice = Invoice::where(['id' => $invoice_id, 'tenant_id' => auth()->user()->tenant_id])->first();
                if (isset($this->invoice->id)):
                    $this->invoice_items = InvoiceItems::where(['invoice_id'=>$this->invoice->id,'tenant_id'=>auth()->user()->tenant_id])->whereIn('type',['Item','Service'])->get();
                    $this->invoice_charges = InvoiceItems::where(['invoice_id'=>$this->invoice->id,'tenant_id'=>auth()->user()->tenant_id,'type'=>'Charge'])->get();            
                    $this->payment_items = Payment::where('invoice_id', '=', $this->invoice->id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
                endif;
                $this->payment_methods = OfflinePaymentMethod::where('tenant_id', '=', auth()->user()->tenant_id)->get();
                $this->taxs = Tax::where(['tenant_id' => auth()->user()->tenant_id])->get();
                if (isset($this->invoice->id)):
                    $this->paidAmount = $this->invoice->getPaidAmount();
                    $this->totalAmount = $this->invoice->getTotalAmount();
                endif;
                $this->products = Product::select("products.*")
                ->join('product_categories', 'product_categories.id', 'products.category_id')
                ->where(['products.tenant_id' => auth()->user()->tenant_id,'product_categories.category_name'=>'Storage'])
                ->get();
                $response['error'] = 0;
                $response['message'] = 'Invoice item has been updated';
                $response['storage_invoice_html'] = view('admin.list-jobs.jobs.storage.storage_invoice_grid', $this->data)->render();
                $response['storage_payment_html'] = view('admin.list-jobs.jobs.storage.storage_payment_grid', $this->data)->render();
            }else{
                $this->invoice_items = InvoiceItems::where(['invoice_id'=>$this->invoice->id,'tenant_id'=>auth()->user()->tenant_id])->where('type', '<>', 'Charge')->get();
                $this->invoice_charges = InvoiceItems::where(['invoice_id'=>$this->invoice->id,'tenant_id'=>auth()->user()->tenant_id])->where('type', '=', 'Charge')->get();
                $this->paidAmount = $this->invoice->getPaidAmount();
                $this->totalAmount = $this->invoice->getTotalAmount();
                $balance = $this->totalAmount - $this->paidAmount;
                $this->taxs = Tax::where(['tenant_id' => auth()->user()->tenant_id])->get();
                if($this->crmlead->lead_type == 'Residential'){
                    $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                                ->where('product_type', '!=', 'Charge')
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_type', 'Residential')
                                                        ->orWhere('customer_type', 'Both');
                                                })->get();
                    $this->charges = Product::where(['tenant_id' => auth()->user()->tenant_id, 'product_type' => 'Charge'])
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_type', 'Residential')
                                                        ->orWhere('customer_type', 'Both');
                                                })->get();
                }else{
                    $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                                ->where('product_type', '!=', 'Charge')
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_type', 'Commercial')
                                                        ->orWhere('customer_type', 'Both');
                                                })
                                                ->where(function ($query) {
                                                    $query->orWhere('customer_id', NULL)
                                                        ->orWhere('customer_id', $this->crmlead->id);
                                                })->get();
                    $this->charges = Product::where(['tenant_id' => auth()->user()->tenant_id, 'product_type' => 'Charge'])
                                            ->where(function ($query) {
                                                $query->orWhere('customer_type', 'Commercial')
                                                    ->orWhere('customer_type', 'Both');
                                            })
                                            ->where(function ($query) {
                                                $query->orWhere('customer_id', NULL)
                                                    ->orWhere('customer_id', $this->crmlead->id);
                                            })->get();
                }
                $response['error'] = 0;
                $response['message'] = 'Invoice item has been updated';
                $response['amount'] = $this->global->currency_symbol . '' . $this->totalAmount;
                $response['paid'] = $this->global->currency_symbol . '' . $this->paidAmount;
                $response['balance'] = $this->global->currency_symbol . '' . $balance;
                $response['html'] = view('admin.list-jobs.jobs.invoice_grid', $this->data)->render();
            }
            return json_encode($response);
        }
    }

    public function ajaxChargeStripePayment(Request $request)
    {
        $invoice_id = $request->input('invoice_id');
        $stripeToken = $request->input('stripeToken');
        $stripeEmail = $request->input('stripeEmail');
        $amount_paid = $request->input('amount');
        $stripeCustomerId = $request->input('stripeCustomerId');
        // Stripe API secret key
        $secret_key = env('STRIPE_SECRET');
        //$secret_key = "sk_test_R2xnUUqsBFBFnhP2ZcQ778jI00Of6gDfPD";
        Stripe::setApiKey($secret_key);

        $tenant_api_details = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'Stripe'])->first();

        if (!$tenant_api_details) {
            $response = array(
                'status' => 0,
                'msg' => 'Stripe account is not connected!',
            );
            return json_encode($response);
        }
        //Find Invoice data
        if (!empty($_POST['stripeToken']) || !empty($_POST['stripeCustomerId'])) {
            $invoice = Invoice::find($invoice_id);
            if ($stripeCustomerId != 'N') {
                $stripeCustomerId = $invoice->stripe_one_off_customer_id;
                $old_customer = 1;
            } else {
                // Get token, card and item info
                $token = $stripeToken;
                $email = $stripeEmail;
                try {
                    // Add customer to stripe
                    $customer = \Stripe\Customer::create(array(
                        'email' => $email,
                        'source' => $token,
                    ), ['stripe_account' => $tenant_api_details->variable1]);
                    $stripeCustomerId = $customer->id;
                } catch (\Stripe\Error\OAuth\OAuthBase $e) {
                    $response = array(
                        'status' => 0,
                        'msg' => $e->getMessage(),
                    );
                    return json_encode($response);
                }
                $old_customer = 0;
            }

            //Add stripe Payment

            try {
                // Charge a credit or a debit card
                $charge = \Stripe\Charge::create(array(
                    'customer' => $stripeCustomerId,
                    'amount' => $amount_paid * 100,
                    'currency' => 'AUD',
                    //'source'  => $token,
                    'description' => 'Payment for invoice number ' . $invoice->invoice_number,
                ), ['stripe_account' => $tenant_api_details->variable1]);

            } catch (\Stripe\Error\OAuth\OAuthBase $e) {
                $response = array(
                    'status' => 0,
                    'msg' => $e->getMessage(),
                );
                return json_encode($response);
            }
            // Retrieve charge details
            $chargeJson = $charge->jsonSerialize();
            if ($chargeJson['amount_refunded'] == 0 && empty($chargeJson['failure_code']) && $chargeJson['paid'] == 1 && $chargeJson['captured'] == 1) {
                $transactionID = $chargeJson['id'];
                //Start::Add Stripe Processing fee line item
                $invoice_setting = InvoiceSetting::where('tenant_id', auth()->user()->tenant_id)->first();  
                if($invoice_setting->cc_processing_fee_percent > 0){
                    $processing_fee = $amount_paid * $invoice_setting->cc_processing_fee_percent/100;  
                    $processing_fee = number_format((float)$processing_fee, 2, '.', '');
                }else{
                    $processing_fee = 0;
                } 
                $amount_paid = $amount_paid+$processing_fee;
                if($invoice_setting->cc_processing_fee_percent > 0){
                    $processing_item = Product::where(['id' => $invoice_setting->cc_processing_product_id, 'tenant_id' => auth()->user()->tenant_id])->first();
                    if($processing_item){
                        $p_deposit_required = $processing_fee;
                        $obj_item = new InvoiceItems();
                        $obj_item->tenant_id = auth()->user()->tenant_id;
                        $obj_item->invoice_id = $invoice->id;
                        $obj_item->product_id = $processing_item->id;
                        $obj_item->item_name = $processing_item->name;
                        $obj_item->item_summary = '';
                        $obj_item->type = $processing_item->product_type;
                        $obj_item->quantity = 1;
                        $obj_item->unit_price = $p_deposit_required;
                        $obj_item->amount = ($obj_item->unit_price * $obj_item->quantity);
                        $obj_item->save();
                        unset($obj_item);
                    }
                }
                //end::Processing fee line item
                //Add Invoice Payment
                $payment = new Payment();
                $payment->tenant_id = auth()->user()->tenant_id;
                $payment->invoice_id = $invoice->id;
                $payment->remarks = 'Stripe Payment';
                $payment->gateway = 'Stripe';
                $payment->transaction_id = $transactionID;
                $payment->amount = $amount_paid;
                $payment->paid_on = Carbon::now();
                $payment->created_at = Carbon::now();
                $payment->save();

                $totalAmount = InvoiceItems::where(['invoice_id' => $invoice->id, 'tenant_id' => auth()->user()->tenant_id])->sum('amount');
                $paidAmount = Payment::where(['invoice_id' => $invoice->id])->sum('amount');
                if ($paidAmount < $totalAmount && $paidAmount > 0) {
                    $invoice->status = 'partial';
                } elseif ($paidAmount == $totalAmount) {
                    $invoice->status = 'paid';
                }
                if ($old_customer == 0) {
                    $invoice->stripe_one_off_customer_id = $stripeCustomerId;
                }
                //Update Invoice Status
                $invoice->save();
                $response = array(
                    'status' => 1,
                    'msg' => 'Your payment was successful.',
                );
            } else {
                $response = array(
                    'status' => 0,
                    'msg' => 'Transaction has been failed.',
                );
            }

        } else {
            $response = array(
                'status' => 0,
                'msg' => 'Stripe token not submitted...',
            );            
        }
        return json_encode($response);
    }

    public function ajaxSavePayment(Request $request)
    {
        $job_id = $request->input('job_id');
        $invoice_id = $request->input('invoice_id');
        $sys_job_type = $request->input('sys_job_type');
        $sys_job_type = ($sys_job_type=="Moving_Storage")?"Moving_Storage":"Moving";

        $paid_on = Carbon::createFromFormat('d/m/Y', $request->input('paid_on'))->format('Y-m-d');

        $obj = new Payment();
        $obj->tenant_id = auth()->user()->tenant_id;
        $obj->invoice_id = $invoice_id;
        $obj->gateway = $request->input('gateway');
        $obj->remarks = $request->input('description');
        $obj->amount = $request->input('amount');
        $obj->transaction_id = $request->input('description');
        $obj->paid_on = $paid_on;
        $obj->created_at = Carbon::now();
        $obj->save();

        //Update invoice status
        $this->invoice = Invoice::where('id', '=', $invoice_id)
            ->where('tenant_id', '=', auth()->user()->tenant_id)
            ->first();
        $this->paidAmount = $this->invoice->getPaidAmount();
        $this->totalAmount = $this->invoice->getTotalAmount();
        $balance = $this->totalAmount - $this->paidAmount;


        if (floatval( (string) $this->totalAmount ) > floatval( (string) $this->paidAmount) && floatval( (string) $this->paidAmount)>0) {
            $status = 'partial';
            $response['payment_status'] = '<p class="job-label-txt orange-status job-status">Partially Paid</p>';
        } elseif (floatval( (string) $this->paidAmount) == 0) {
            $status = 'unpaid';
            $response['payment_status'] = '<p class="job-label-txt orange-status job-status">Unpaid</p>';
        } else {
            $status = 'paid';
            $response['payment_status'] = '<p class="job-label-txt green-status job-status">Paid</p>';
        }

        Invoice::where(['id' => $invoice_id, 'tenant_id' => auth()->user()->tenant_id])->update(['status' => $status]);
        //---->

        //response
        if($sys_job_type=="Moving_Storage"){
            $this->payment_items = 0;
            $this->stripe = TenantApiDetail::where('tenant_id', auth()->user()->tenant_id)
                        ->where('provider', 'Stripe')->first();
            if (isset($this->invoice->id)):
                $this->payment_items = Payment::where('invoice_id', '=', $this->invoice->id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
            endif;
            $this->payment_methods = OfflinePaymentMethod::where('tenant_id', '=', auth()->user()->tenant_id)->get();
            $response['error'] = 0;
            $response['message'] = 'Payment has been saved';
            $response['storage_payment_html'] = view('admin.list-jobs.jobs.storage.storage_payment_grid', $this->data)->render();
        }else{
            $this->payment_items = Payment::where('invoice_id', '=', $this->invoice->id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
            $this->payment_methods = OfflinePaymentMethod::where('tenant_id', '=', auth()->user()->tenant_id)->get();
            $this->paidAmount = $this->invoice->getPaidAmount();
            $this->totalAmount = $this->invoice->getTotalAmount();
            $balance = $this->totalAmount - $this->paidAmount;
            $this->taxs = Tax::where(['tenant_id' => auth()->user()->tenant_id])->get();
            $this->products = Product::where(['tenant_id' => auth()->user()->tenant_id])->get();
            $response['error'] = 0;
            $response['message'] = 'Payment has been saved';
            $response['amount'] = $this->global->currency_symbol . '' . $this->totalAmount;
            $response['paid'] = $this->global->currency_symbol . '' . $this->paidAmount;
            $response['balance'] = $this->global->currency_symbol . '' . $balance;
            $response['html'] = view('admin.list-jobs.jobs.payment_grid', $this->data)->render();
        }
        return json_encode($response);
    }

    public function ajaxUpdatePayment(Request $request)
    {
        $id = $request->input('id');
        $job_id = $request->input('job_id');
        $invoice_id = $request->input('invoice_id');
        $sys_job_type = $request->input('sys_job_type');
        $sys_job_type = ($sys_job_type=="Moving_Storage")?"Moving_Storage":"Moving";

        $paid_on = Carbon::createFromFormat('d/m/Y', $request->input('paid_on'))->format('Y-m-d');

        $obj = Payment::where(['id' => $id, 'tenant_id' => auth()->user()->tenant_id])->first();
        $obj->gateway = $request->input('gateway');
        $obj->remarks = $request->input('description');
        $obj->amount = $request->input('amount');
        $obj->paid_on = $paid_on;
        $obj->updated_at = Carbon::now();
        $obj->save();

        //Update invoice status
        $this->invoice = Invoice::where('id', '=', $invoice_id)
            ->where('tenant_id', '=', auth()->user()->tenant_id)
            ->first();

        $this->paidAmount = $this->invoice->getPaidAmount();
        $this->totalAmount = $this->invoice->getTotalAmount();
        $balance = $this->totalAmount - $this->paidAmount;

        if (floatval( (string) $this->totalAmount ) > floatval( (string) $this->paidAmount) && floatval( (string) $this->paidAmount)>0) {
            $status = 'partial';
            $response['payment_status'] = '<p class="job-label-txt orange-status job-status">Partially Paid</p>';
        } elseif (floatval( (string) $this->paidAmount) == 0) {
            $status = 'unpaid';
            $response['payment_status'] = '<p class="job-label-txt orange-status job-status">Unpaid</p>';
        } else {
            $status = 'paid';
            $response['payment_status'] = '<p class="job-label-txt green-status job-status">Paid</p>';
        }
        Invoice::where(['id' => $invoice_id, 'tenant_id' => auth()->user()->tenant_id])->update(['status' => $status]);
        //---->

        //response
        if($sys_job_type=="Moving_Storage"){
            $this->payment_items = 0;
            $this->stripe = TenantApiDetail::where('tenant_id', auth()->user()->tenant_id)
                        ->where('provider', 'Stripe')->first();
            if (isset($this->invoice->id)):
                $this->payment_items = Payment::where('invoice_id', '=', $this->invoice->id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
            endif;
            $this->payment_methods = OfflinePaymentMethod::where('tenant_id', '=', auth()->user()->tenant_id)->get();
            $response['error'] = 0;
            $response['message'] = 'Payment has been updated';
            $response['storage_payment_html'] = view('admin.list-jobs.jobs.storage.storage_payment_grid', $this->data)->render();
        }else{
            $this->payment_items = Payment::where('invoice_id', '=', $this->invoice->id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
            $this->payment_methods = OfflinePaymentMethod::where('tenant_id', '=', auth()->user()->tenant_id)->get();
            $this->paidAmount = $this->invoice->getPaidAmount();
            $this->totalAmount = $this->invoice->getTotalAmount();
            $balance = $this->totalAmount - $this->paidAmount;
            $this->taxs = Tax::where(['tenant_id' => auth()->user()->tenant_id])->get();
            $this->products = Product::where(['tenant_id' => auth()->user()->tenant_id])->get();
            $response['error'] = 0;
            $response['message'] = 'Payment has been updated';
            $response['amount'] = $this->global->currency_symbol . '' . $this->totalAmount;
            $response['paid'] = $this->global->currency_symbol . '' . $this->paidAmount;
            $response['balance'] = $this->global->currency_symbol . '' . $balance;
            $response['html'] = view('admin.list-jobs.jobs.payment_grid', $this->data)->render();
        }
        return json_encode($response);
    }

    public function ajaxDestroyPaymentItem(Request $request)
    {
        $id = $request->input('id');
        $job_id = $request->input('job_id');
        $invoice_id = $request->input('invoice_id');
        $sys_job_type = $request->input('sys_job_type');
        $sys_job_type = ($sys_job_type=="Moving_Storage")?"Moving_Storage":"Moving";

        Payment::destroy($id);

        $this->invoice = Invoice::where('id', '=', $invoice_id)
            ->where('tenant_id', '=', auth()->user()->tenant_id)
            ->first();
        $this->paidAmount = $this->invoice->getPaidAmount();
        $this->totalAmount = $this->invoice->getTotalAmount();

        if (floatval( (string) $this->totalAmount ) > floatval( (string) $this->paidAmount) && floatval( (string) $this->paidAmount)>0) {
            $status = 'partial';
            $response['payment_status'] = '<p class="job-label-txt orange-status job-status">Partially Paid</p>';
        } elseif (floatval( (string) $this->paidAmount) == 0) {
            $status = 'unpaid';
            $response['payment_status'] = '<p class="job-label-txt orange-status job-status">Unpaid</p>';
        } else {
            $status = 'paid';
            $response['payment_status'] = '<p class="job-label-txt green-status job-status">Paid</p>';
        }
        Invoice::where(['id' => $invoice_id, 'tenant_id' => auth()->user()->tenant_id])->update(['status' => $status]);

        //response
        if($sys_job_type=="Moving_Storage"){
            $this->payment_items = 0;
            $this->stripe = TenantApiDetail::where('tenant_id', auth()->user()->tenant_id)
                        ->where('provider', 'Stripe')->first();
            if (isset($this->invoice->id)):
                $this->payment_items = Payment::where('invoice_id', '=', $this->invoice->id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
            endif;
            $this->payment_methods = OfflinePaymentMethod::where('tenant_id', '=', auth()->user()->tenant_id)->get();
            $response['error'] = 0;
            $response['message'] = 'Payment has been deleted';
            $response['storage_payment_html'] = view('admin.list-jobs.jobs.storage.storage_payment_grid', $this->data)->render();
        }else{
            $this->payment_items = Payment::where('invoice_id', '=', $this->invoice->id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
            $this->payment_methods = OfflinePaymentMethod::where('tenant_id', '=', auth()->user()->tenant_id)->get();
            $this->paidAmount = $this->invoice->getPaidAmount();
            $this->totalAmount = $this->invoice->getTotalAmount();
            $balance = $this->totalAmount - $this->paidAmount;
            $this->taxs = Tax::where(['tenant_id' => auth()->user()->tenant_id])->get();
            $this->products = Product::where(['tenant_id' => auth()->user()->tenant_id])->get();
            $response['error'] = 0;
            $response['message'] = 'Payment has been deleted';
            $response['amount'] = $this->global->currency_symbol . '' . $this->totalAmount;
            $response['paid'] = $this->global->currency_symbol . '' . $this->paidAmount;
            $response['balance'] = $this->global->currency_symbol . '' . $balance;
            $response['html'] = view('admin.list-jobs.jobs.payment_grid', $this->data)->render();
        }
        return json_encode($response);
    }

    public static function paymentStatus($amount, $paid)
    {
        if (floatval( (string) $amount ) > floatval( (string) $paid) && floatval( (string) $paid)>0) {
            return '<p class="job-label-txt orange-status job-status">Partially Paid</p>';
        } elseif (floatval( (string) $paid) == 0) {
            return '<p class="job-label-txt orange-status job-status">Unpaid</p>';
        } else {
            return '<p class="job-label-txt green-status job-status">Paid</p>';
        }
    }

    public static function paymentStatusDataTable($amount, $paid)
    {
        if (floatval( (string) $amount ) > floatval( (string) $paid) && floatval( (string) $paid)>0) {
            return "Partially Paid";
        } elseif (floatval( (string) $paid) == 0) {
            return "Unpaid";
        } else {
            return "Paid";
        }
    }

    public function ajaxUpdateJobDetail(Request $request)
    {

        $job_id = $request->input('job_id');
        $new_job_status = $request->input('job_status');

        $this->job_id = $job_id;
        $obj = JobsMoving::where('job_id', '=', $job_id)
            ->where('tenant_id', '=', auth()->user()->tenant_id)->first();

        //Set in queue invoice if XERO integration is On
        if($obj->job_status != "Completed" && $new_job_status == "Completed"){
            $tenant_xero_api = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'Xero'])->first();
            $tenant_myob_api = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'MYOB'])->first();
            $invoice = Invoice::where(['job_id'=> $job_id,'sys_job_type'=>'Moving'])->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            if (isset($tenant_xero_api)) {
                $invoice->sync_with_xero = 'Y';
                $invoice->save();
            }elseif (isset($tenant_myob_api)) {
                $invoice->sync_with_myob = 'Y';
                $invoice->save();
            }
        }
        //end

        $obj->company_id = $request->input('company_id');
        $obj->total_cbm = $request->input('total_cbm');
        $obj->job_status = $new_job_status;
        $obj->created_at = Carbon::now();
        $obj->save();

        $this->job = $obj;
        $this->companies = Companies::where(['tenant_id' => auth()->user()->tenant_id, 'id' => $this->job->company_id])->first();

        $response['error'] = 0;
        $response['message'] = 'Job detail has been updated';
        $response['html'] = view('admin.list-jobs.jobs.job_detail_grid', $this->data)->render();
        return json_encode($response);
    }

    public function ajaxUpdateJobPickup(Request $request)
    {
        $job_id = $request->input('job_id');
        $this->job_id = $job_id;
        $obj = JobsMoving::where('job_id', '=', $job_id)
            ->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $obj->job_date = Carbon::createFromFormat('d/m/Y', $request->input('job_date'))->format('Y-m-d');
        $obj->pickup_bedrooms = $request->input('pickup_bedrooms');
        $obj->pickup_address = $request->input('pickup_address');
        $obj->pickup_suburb = $request->input('pickup_suburb');
        $obj->pickup_post_code = $request->input('pickup_postcode');
        if($request->pickup_contact_name)
        {
            $obj->pickup_contact_name = $request->pickup_contact_name;
        }
        if($request->pickup_mobile)
        {
            $obj->pickup_mobile = $request->pickup_mobile;
        }
        if($request->pickup_email)
        {
            $obj->pickup_email = $request->pickup_email;
        }
        $obj->pickup_access_restrictions = $request->pickup_access_restrictions;
        $obj->created_at = Carbon::now();
        $obj->save();
        $this->job = $obj;

        $response['error'] = 0;
        $response['message'] = 'Pickup detail has been updated';
        $response['html'] = view('admin.list-jobs.jobs.pickup_grid', $this->data)->render();
        return json_encode($response);
    }

    public function ajaxUpdateJobDropoff(Request $request)
    {

        $job_id = $request->input('job_id');
        $this->job_id = $job_id;
        $obj = JobsMoving::where('job_id', '=', $job_id)
            ->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $obj->drop_off_bedrooms = $request->input('drop_off_bedrooms');
        $obj->drop_off_address = $request->input('drop_off_address');
        $obj->delivery_suburb = $request->input('delivery_suburb');
        $obj->drop_off_post_code = $request->input('drop_off_postcode');
        if($request->drop_off_contact_name)
        {
            $obj->drop_off_contact_name = $request->drop_off_contact_name;
        }
        if($request->drop_off_mobile)
        {
            $obj->drop_off_mobile = $request->drop_off_mobile;
        }
        if($request->drop_off_email)
        {
            $obj->drop_off_email = $request->drop_off_email;
        }
        $obj->drop_off_access_restrictions = $request->drop_off_access_restrictions;
        $obj->updated_at = Carbon::now();
        $obj->save();
        $this->job = $obj;

        $response['error'] = 0;
        $response['message'] = 'Drop off detail has been updated';
        $response['html'] = view('admin.list-jobs.jobs.dropoff_grid', $this->data)->render();
        return json_encode($response);
    }

    private function storeJobLegTeam($user_id, $leg_id, $is_Driver,$team_id = null)
    {
        if($is_Driver=='Y'){
            $team_obj= JobsMovingLegsTeam::where(['tenant_id' => auth()->user()->tenant_id, 'leg_id' => $leg_id, 'driver'=>$is_Driver])->first();
        }else{
            $team_obj= JobsMovingLegsTeam::where(['tenant_id' => auth()->user()->tenant_id, 'id' => $team_id])->first();
        }
        if($team_obj){
            $team_obj->people_id=$user_id;
        }else{
            $team_obj = new JobsMovingLegsTeam();
            $team_obj->tenant_id = auth()->user()->tenant_id;
            $team_obj->leg_id = $leg_id;
            $team_obj->people_id = $user_id;
            $team_obj->created_by = auth()->user()->id;
            $team_obj->driver = $is_Driver;
            $team_obj->confirmation_status = 'New';
        }
        $team_obj->save();
        return true;
    }

    private function removeJobLegTeam($leg_id, $is_Driver,$team_id = null)
    {
        if($is_Driver=='Y'){
            // If leg deleted then deleted all leg related record
            JobsMovingLegsTeam::where(['tenant_id' => auth()->user()->tenant_id, 'leg_id' => $leg_id])->delete();
        }else{
            JobsMovingLegsTeam::where(['tenant_id' => auth()->user()->tenant_id, 'id' => $team_id])->delete();
        }
        return true;
    }

    public function ajaxSaveJobOperation(Request $request)
    {
        $job_id = $request->input('job_id');
        $this->job_id = $job_id;
        $offsiders = (is_array($request->input('offsider_ids'))) ? implode(',', $request->input('offsider_ids')) : null;

        $this->job = JobsMoving::where('job_id', '=', $job_id)
            ->where('tenant_id', '=', auth()->user()->tenant_id)->first();

        $jleg = JobsMovingLegs::select(DB::raw('MAX(leg_number) as max_leg'))->where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        if ($jleg) {
            $leg_number = $jleg->max_leg + 1;
        } else {
            $leg_number = 1;
        }
        //Finding deo locations
        $api_key = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'GoogleMaps'])->pluck('account_key')->first();
        $pickup_address = $request->input('pickup'); //
        $drop_off_address = $request->input('dropoff'); //

        $pickup_geo_location = JobsMovingLegs::getGeoLocation($api_key, $pickup_address);
        $drop_off_geo_location = JobsMovingLegs::getGeoLocation($api_key, $drop_off_address);

        //end--->
        $date = Carbon::createFromFormat('d/m/Y', $request->input('leg_date'))->format('Y-m-d');
        $obj = new JobsMovingLegs();
        $obj->job_id = $job_id;
        $obj->tenant_id = auth()->user()->tenant_id;
        $obj->leg_number = $leg_number;
        $obj->leg_date = $date;
        $obj->pickup_address = $pickup_address;
        $obj->drop_off_address = $drop_off_address;
        $obj->pickup_geo_location = $pickup_geo_location;
        $obj->drop_off_geo_location = $drop_off_geo_location;
        $obj->est_start_time = $request->input('est_start_time');
        $obj->est_finish_time = $request->input('est_finish_time');
        // $obj->job_start_date = $request->input('est_start_time');
        // $obj->job_end_date = $request->input('est_finish_time');
        $obj->driver_id = $request->input('driver_id');
        $obj->vehicle_id = $request->input('vehicle_id');
        $obj->offsider_ids = $offsiders;
        $obj->job_type = null;
        $obj->leg_status = $request->input('leg_status');
        $obj->has_multiple_trips = $request->input('has_multiple_trips');
        $obj->notes = $request->input('notes');
        $obj->save();
        $obj->refresh();

        //Add record in Job legs team table
        if($request->input('driver_id')!=''){
            $this->storeJobLegTeam($request->driver_id, $obj->id, 'Y');
        }
        //end

        //If Every leg_status=Completed then update Job Status to Completed aswell    
        // $legs = JobsMovingLegs::where('job_id','=', $job_id)->where('leg_status','<>','Completed')->get();
        // if (!$legs || count($legs) == 0) {
        // JobsMoving::where('job_id', $job_id)
        //     ->update([
        //         'job_status' => 'Completed'
        //     ]);
        // }
        //---->
        
        //Response
        $this->job_type = Lists::job_type();
        $this->leg_status = Lists::leg_status();
        $this->vehicles = Vehicles::select('id', 'vehicle_name')->where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $this->drivers = User::driverList();
        $this->people = User::allPeople();
        $this->job_legs = JobsMovingLegs::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $response['error'] = 0;
        $response['message'] = 'Leg has been saved';
        $response['legs'] = view('admin.list-jobs.jobs.operations_leg_grid', $this->data)->render();
        $response['trips'] = view('admin.list-jobs.jobs.operations_trip_grid', $this->data)->render();
        $response['actual_hours'] = view('admin.list-jobs.jobs.actual_hours_grid', $this->data)->render();
        return json_encode($response);
    }
    
    public function ajaxUpdateJobOperation(Request $request)
    {
        $id = $request->input('id');
        $job_id = $request->input('job_id');
        $this->job_id = $job_id;
        $this->job = JobsMoving::where('job_id', '=', $job_id)
            ->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $date = Carbon::createFromFormat('d/m/Y', $request->input('leg_date'))->format('Y-m-d');

        //Finding deo locations
        $api_key = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'GoogleMaps'])->pluck('account_key')->first();
        $pickup_address = $request->input('pickup'); //
        $drop_off_address = $request->input('dropoff'); //

        $pickup_geo_location = JobsMovingLegs::getGeoLocation($api_key, $pickup_address);
        $drop_off_geo_location = JobsMovingLegs::getGeoLocation($api_key, $drop_off_address);
        //end--->

        $obj = JobsMovingLegs::where('id', '=', $id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $obj->leg_date = $date;
        $obj->pickup_address = $pickup_address;
        $obj->drop_off_address = $drop_off_address;
        $obj->pickup_geo_location = $pickup_geo_location;
        $obj->drop_off_geo_location = $drop_off_geo_location;
        $obj->est_start_time = $request->input('est_start_time');
        $obj->est_finish_time = $request->input('est_finish_time');
        // $obj->job_start_date = $request->input('est_start_time');
        // $obj->job_end_date = $request->input('est_finish_time');
        $obj->driver_id = $request->input('driver_id');
        $obj->vehicle_id = $request->input('vehicle_id');
        $obj->job_type = null;
        $obj->leg_status = $request->input('leg_status');
        $obj->has_multiple_trips = $request->input('has_multiple_trips');
        $obj->notes = $request->input('notes');
        $obj->save();

        //Add/Update record in Job legs team table
        if($request->input('driver_id')!=''){
            $this->storeJobLegTeam($request->driver_id, $id, 'Y');
        }
        //end

        //If Every leg_status=Completed then update Job Status to Completed aswell    
        // $legs = JobsMovingLegs::where('job_id','=', $job_id)->where('leg_status','<>','Completed')->get();
        // if (!$legs || count($legs) == 0) {
        // JobsMoving::where('job_id', $job_id)
        //     ->update([
        //         'job_status' => 'Completed'
        //     ]);
        // }
        //---->

        //Response
        $this->job_type = Lists::job_type();
        $this->leg_status = Lists::leg_status();
        $this->vehicles = Vehicles::select('id', 'vehicle_name')->where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $this->drivers = User::driverList();
        $this->people = User::allPeople();
        $this->job_legs = JobsMovingLegs::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $response['error'] = 0;
        $response['message'] = 'Leg has been updated';
        $response['legs'] = view('admin.list-jobs.jobs.operations_leg_grid', $this->data)->render();
        $response['trips'] = view('admin.list-jobs.jobs.operations_trip_grid', $this->data)->render();
        $response['actual_hours'] = view('admin.list-jobs.jobs.actual_hours_grid', $this->data)->render();
        return json_encode($response);
    }

    public function ajaxDestroyJobOperation(Request $request)
    {
        $id = $request->input('id');
        $job_id = $request->input('job_id');
        $this->job_id = $job_id;
        $this->job = JobsMoving::where('job_id', '=', $job_id)
            ->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        //Remove Leg record
        JobsMovingLegs::where('id', '=', $id)->where('tenant_id', '=', auth()->user()->tenant_id)->delete();
        
        //Remove driver record from leg_team table
        $this->removeJobLegTeam($id, 'Y');

        //Response
        $this->job_type = Lists::job_type();
        $this->leg_status = Lists::leg_status();
        $this->vehicles = Vehicles::select('id', 'vehicle_name')->where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $this->drivers = User::driverList();
        $this->people = User::allPeople();
        $this->job_legs = JobsMovingLegs::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $response['error'] = 0;
        $response['message'] = 'Leg has been deleted';
        $response['legs'] = view('admin.list-jobs.jobs.operations_leg_grid', $this->data)->render();
        $response['trips'] = view('admin.list-jobs.jobs.operations_trip_grid', $this->data)->render();
        $response['actual_hours'] = view('admin.list-jobs.jobs.actual_hours_grid', $this->data)->render();
        return json_encode($response);
    }

    public function ajaxSaveJobOperationOffsider(Request $request)
    {
        $leg_id = $request->leg_id;
        $offsider_id = $request->offsider_id;
        $job_id = $request->job_id;
        if(JobsMovingLegsTeam::where(['tenant_id' => auth()->user()->tenant_id, 'leg_id' => $leg_id, 'people_id' => $offsider_id, 'driver' => 'N'])->first())
        {
            $response['error'] = 1;
            $response['message'] = 'This Offsider already has been added!';
            return $response;
        }
        $team_stored = $this->storeJobLegTeam($offsider_id, $leg_id, 'N');
        if($team_stored){
            $people = User::allPeople();
            $response['error'] = 0;
            $response['grid'] = view('admin.list-jobs.jobs.operations_leg_offsiders_grid', compact('leg_id', 'people'))->render();
            $response['message'] = 'Offsider has been added!';   
        }else{
            $response['error'] = 2;
            $response['message'] = 'Something went wrong!';
        }
        return $response; 
    }

    public function ajaxUpdateJobOperationOffsider(Request $request)
    {
        $leg_id = $request->leg_id;
        $user_id = $request->update_offsider_id;
        $team_id = $request->offsider_table_id;
        $job_id = $request->job_id;

        $updated_team = $this->storeJobLegTeam($user_id, $leg_id, 'N', $team_id);
        if($updated_team){
            $people = User::allPeople();
            $response['error'] = 0;
            $response['grid'] = view('admin.list-jobs.jobs.operations_leg_offsiders_grid', compact('leg_id', 'people'))->render();
            $response['message'] = 'Offsider has been updated!';
        }else{
            $response['error'] = 1;
            $response['message'] = 'Something went wrong!';
        }
        return $response;
    }

    public function ajaxDestroyJobOperationOffsider(Request $request)
    {
        $leg_id = $request->leg_id;
        $offsider_table_id = $request->offsider_table_id;
        $job_id = $request->job_id;

        //Remove driver record from leg_team table
        $team_deletd = $this->removeJobLegTeam($leg_id, 'N', $offsider_table_id);

        if($team_deletd){
            $people = User::allPeople();
            $response['error'] = 0;
            $response['grid'] = view('admin.list-jobs.jobs.operations_leg_offsiders_grid', compact('leg_id', 'people'))->render();
            $response['message'] = 'Offsider has been deleted!';
        }else{
            $response['error'] = 1;
            $response['message'] = 'Something went wrong!';
        }
        return $response;
    }

    private function notifyLegTeam($leg_id, $user_id, $isDriver){
        $team_obj= JobsMovingLegsTeam::where(['tenant_id' => auth()->user()->tenant_id, 'leg_id' => $leg_id,'people_id'=>$user_id, 'driver'=>$isDriver])
        ->update([
            'confirmation_status'=>'Notified'
        ]);
    }

    public function ajaxNotifyDriver(Request $request)
    {
        $id = $request->input('id');
        $job_id = $request->input('job_id');
        $this->job_id = $job_id;

        $this->job = JobsMoving::where('job_id', '=', $job_id)
            ->where('tenant_id', '=', auth()->user()->tenant_id)->first();

        $job_leg = JobsMovingLegs::where(['id' => $id, 'tenant_id' => auth()->user()->tenant_id])->first();
        $job_leg->leg_status = 'Awaiting Confirmation';
        $job_leg->save();

        //Start:: Update Leg Team Status 
            $this->notifyLegTeam($job_leg->id, $job_leg->driver_id, 'Y');
        //End::>>>>

        //Response
        $this->job_type = Lists::job_type();
        $this->leg_status = Lists::leg_status();
        $this->vehicles = Vehicles::select('id', 'vehicle_name')->where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $this->drivers = User::driverList();
        $this->people = User::allPeople();
        $this->job_legs = JobsMovingLegs::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();

        $return['error'] = 0;
        $return['ppl_id'] = $job_leg->driver_id;
        $return['job_number'] = $this->job->job_number;
        $return['message'] = 'Status has been updated';
        $return['legs'] = view('admin.list-jobs.jobs.operations_leg_grid', $this->data)->render();
        return json_encode($return);
    }

    public function ajaxNotifyOffsider(Request $request)
    {
        // dd($request->all());
        $job_id = $request->job_id;
        $leg_id = $request->leg_id;
        $offsider_id = $request->offsider_people_id;
        $offsider_table_id = $request->offsider_team_id;
        $this->job_id = $job_id;
        $this->job = JobsMoving::where('job_id', '=', $job_id)
            ->where('tenant_id', '=', auth()->user()->tenant_id)->first();


        //Start:: Update Leg Team Status 
        $this->notifyLegTeam($leg_id, $offsider_id, 'N');
        //End::>>>>

        //Response
        $this->job_type = Lists::job_type();
        $this->leg_status = Lists::leg_status();
        $this->vehicles = Vehicles::select('id', 'vehicle_name')->where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $this->drivers = User::driverList();
        $this->people = User::allPeople();
        $this->job_legs = JobsMovingLegs::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();

        $return['error'] = 0;
        $return['ppl_id'] = $offsider_id;
        $return['job_number'] = $this->job->job_number;
        $return['message'] = 'Status has been updated';
        $return['legs'] = view('admin.list-jobs.jobs.operations_leg_grid', $this->data)->render();
        return json_encode($return);
    }

    public function sendPushNotification(Request $request)
    {
        $ppl_id = $request->input('ppl_id');
        $job_number = $request->input('job_number');

        $user_id = PplPeople::where(['id' => $ppl_id, 'tenant_id' => auth()->user()->tenant_id])->pluck('user_id')->first();
        $device_token = User::where(['id' => $user_id, 'tenant_id' => auth()->user()->tenant_id])->pluck('device_token')->first();
        if ($device_token) {
            $params = [
                'job_number' => $job_number,
            ];

            $sys_notify = SysNotificationSetting::where('id', '=', 1)->first();
            if ($sys_notify) {
                if ($sys_notify->send_push == 'Y') {
                    $template = $sys_notify->notification_message;
                    if (preg_match_all("/{(.*?)}/", $template, $m)) {
                        foreach ($m[1] as $i => $varname) {
                            $template = str_replace($m[0][$i], sprintf('%s', $params[$varname]), $template);
                        }
                    }

                    //Curl for FCM push notification
                    $url = env('FCM_SERVER_URI');
                    $serverKey = env('FCM_SERVER_KEY');
                    $title = $sys_notify->notification_name;
                    $body = $template;
                    $notification = array('title' => $title, 'body' => $body, 'sound' => 'default', 'badge' => '1');
                    $arrayToSend = array('to' => $device_token, 'notification' => $notification, 'priority' => 'high');
                    //print_r($arrayToSend);exit;

                    $json = json_encode($arrayToSend);
                    $headers = array();
                    $headers[] = 'Content-Type: application/json';
                    $headers[] = 'Authorization: key=' . $serverKey;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    //Send the request
                    $response = curl_exec($ch);
                    //Close request
                    if ($response === false) {
                        die('FCM Send Error: ' . curl_error($ch));
                    }
                    curl_close($ch);
                    $obj = new SysNotificationLog();
                    $obj->sys_notification_id = 1;
                    $obj->tenant_id = auth()->user()->tenant_id;
                    $obj->notification_type = 'push';
                    $obj->sent_to_id = $user_id;
                    $obj->sent_at = Carbon::now();
                    $obj->save();
                }
            }
        }
    }

    public function ajaxReassignDriver(Request $request)
    {
        $id = $request->input('id');
        $job_id = $request->input('job_id');
        $driver_id = $request->input('driver_id');
        $this->job_id = $job_id;
        JobsMovingLegs::where(['id' => $id, 'tenant_id' => auth()->user()->tenant_id])->update([
            'driver_id' => $driver_id,
            'leg_status' => null,
        ]);

        //Start:: Update Leg Team Driver ID 
        if($driver_id!=''){
            $this->storeJobLegTeam($driver_id, $id, 'Y');
        }
        //End::>>>>

        //Response
        $this->job = JobsMoving::where('job_id', '=', $job_id)
            ->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $this->job_type = Lists::job_type();
        $this->leg_status = Lists::leg_status();
        $this->vehicles = Vehicles::select('id', 'vehicle_name')->where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $this->drivers = User::driverList();
        $this->people = User::allPeople();
        $this->job_legs = JobsMovingLegs::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $response['error'] = 0;
        $response['message'] = 'Driver has been updated';
        $response['legs'] = view('admin.list-jobs.jobs.operations_leg_grid', $this->data)->render();
        return json_encode($response);
    }

    //START:: Update Actutal Hours
    public function ajaxUpdateActualhours(Request $request)
    {
        $id = $request->input('id');
        $job_id = $request->input('job_id');
        $this->job_id = $job_id;
        JobsMovingLegs::where(['id' => $id, 'tenant_id' => auth()->user()->tenant_id])->update([
            'actual_start_time' => $request->input('actual_start_time'),
            'actual_finish_time' => $request->input('actual_finish_time'),
        ]);
        //Response
        $this->job_legs = JobsMovingLegs::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $response['error'] = 0;
        $response['message'] = 'Actual Hours has been updated';
        $response['html'] = view('admin.list-jobs.jobs.actual_hours_grid', $this->data)->render();
        return json_encode($response);
    }

    public function ajaxUpdateRegenrateInvoice(Request $request)
    {
        $job_id = $request->input('job_id');
        $invoice_id = $request->input('invoice_id');
        $this->job_id = $job_id;
        $error = 0;

        // $tenant = DB::table('jobs_moving_auto_quoting as t1')
        //     ->select('t1.quote_line_item_product_id')
        //     ->where(['t1.tenant_id' => auth()->user()->tenant_id])
        //     ->first();

        // if (isset($invoice_id) && $tenant->quote_line_item_product_id != null) {
        if (isset($invoice_id)) {
            //Get total calculated hour
            $job_legs = JobsMovingLegs::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
            $total_duration = 0;
            if (count($job_legs)) {
                foreach ($job_legs as $j) {
                    $total_duration += $j->calculateTimeDurationForUpdate();
                }
            }
            //---->
            $invoice = Invoice::where(['id' => $invoice_id, 'tenant_id' => auth()->user()->tenant_id])->first();
            if($invoice){
                $invoice->inv_version = $invoice->inv_version + 1;
                $current_date = date('Y-m-d');
                $invoice->issue_date = $current_date;
                $invoice->save();
            }
            $invoice_items = InvoiceItems::where(['invoice_id' => $invoice_id, 'type' => 'Service', 'tenant_id' => auth()->user()->tenant_id])->get();
            if (count($invoice_items)) {
                foreach ($invoice_items as $item) {
                    $product = Product::findOrFail($item->product_id);
                    if($product){
                        if($product->hourly_pricing_min_hours > 0 && $product->hourly_pricing_min_hours > $total_duration){
                            $total_duration = $product->hourly_pricing_min_hours;
                        }
                    }
                    if ($item->tax_id != null) {
                        $rate_percent = Tax::where(['id' => $item->tax_id, 'tenant_id' => auth()->user()->tenant_id])->pluck('rate_percent')->first();
                    } else {
                        $rate_percent = 0;
                    }
                    if($total_duration>0){
                        $item->quantity = $total_duration;
                    }
                    $item->amount = $item->quantity * $item->unit_price * (1 + $rate_percent / 100);
                    $item->save();
                }
            }
        } else {
            $error = 1;
        }

        if ($error == 1) {
            $response['error'] = 1;
            $response['message'] = 'Invoice not updated!';
            return json_encode($response);
        } else {
            $response['error'] = 0;
            $response['message'] = 'Invoice has been updated';
            return json_encode($response);
        }
    }
    //END:: Update Actutal Hours

    public function ajaxSaveJobOperationTrip(Request $request)
    {
        $leg_id = $request->input('leg_id');
        $job_id = $request->input('job_id');

        $jleg = JobsMovingLegTrips::select(DB::raw('MAX(trip_number) as max_leg'))->where('jobs_moving_leg_id', '=', $leg_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $trip_number = $jleg->max_leg + 1;
        $obj = new JobsMovingLegTrips();
        $obj->jobs_moving_leg_id = $leg_id;
        $obj->tenant_id = auth()->user()->tenant_id;
        $obj->trip_number = $trip_number;

        $obj->pickup_address = (empty($request->input('pickup'))) ? 'No Pickup' : $request->input('pickup');
        $obj->drop_off_address = (empty($request->input('dropoff'))) ? 'No Drop-off' : $request->input('dropoff');
        $obj->trip_notes = $request->input('notes');
        $obj->created_by = auth()->user()->id;
        $obj->save();
        //Response
        $this->job_type = Lists::job_type();
        $this->leg_status = Lists::leg_status();
        $this->vehicles = Vehicles::select('id', 'vehicle_name')->where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $this->drivers = User::driverList();
        $this->people = User::allPeople();
        $this->job_legs = JobsMovingLegs::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $response['error'] = 0;
        $response['message'] = 'Trip has been saved';
        $response['trips'] = view('admin.list-jobs.jobs.operations_trip_grid', $this->data)->render();
        return json_encode($response);
    }

    public function ajaxUpdateJobOperationTrip(Request $request)
    {
        $id = $request->input('id');
        $job_id = $request->input('job_id');

        $obj = JobsMovingLegTrips::where('id', '=', $id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $obj->pickup_address = (empty($request->input('pickup'))) ? 'No Pickup' : $request->input('pickup');
        $obj->drop_off_address = (empty($request->input('dropoff'))) ? 'No Drop-off' : $request->input('dropoff');
        $obj->trip_notes = $request->input('notes');
        $obj->updated_by = auth()->user()->id;
        $obj->save();
        //Response
        $this->job_type = Lists::job_type();
        $this->leg_status = Lists::leg_status();
        $this->vehicles = Vehicles::select('id', 'vehicle_name')->where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $this->drivers = User::driverList();
        $this->people = User::allPeople();
        $this->job_legs = JobsMovingLegs::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $response['error'] = 0;
        $response['message'] = 'Trip has been updated';
        $response['trips'] = view('admin.list-jobs.jobs.operations_trip_grid', $this->data)->render();
        return json_encode($response);
    }

    public function ajaxDestroyJobOperationTrip(Request $request)
    {
        $id = $request->input('id');
        $job_id = $request->input('job_id');

        JobsMovingLegTrips::where('id', '=', $id)->where('tenant_id', '=', auth()->user()->tenant_id)->delete();
        //Response
        $this->job_type = Lists::job_type();
        $this->leg_status = Lists::leg_status();
        $this->vehicles = Vehicles::select('id', 'vehicle_name')->where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $this->drivers = User::driverList();
        $this->people = User::allPeople();
        $this->job_legs = JobsMovingLegs::where('job_id', '=', $job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $response['error'] = 0;
        $response['message'] = 'Trip has been deleted';
        $response['trips'] = view('admin.list-jobs.jobs.operations_trip_grid', $this->data)->render();
        return json_encode($response);
    }

    //START:: Storage Module
    public function storageTabContent(Request $request)
    {
        $job_id = $request->job_id;
        $this->quoteItem = NULL;
        $this->quote = NULL;
        $this->storage_type_list = StorageTypes::where('tenant_id', '=', auth()->user()->tenant_id)
        ->where(['deleted'=>'0'])->orderBy('name', 'ASC')->get();

        $this->storage_reservation = StorageUnitAllocation::select("storage_unit_allocation.*",
        "storage_units.serial_number",
        "storage_types.name as type_name"
        )
        ->join('storage_units', 'storage_units.id', 'storage_unit_allocation.unit_id')
        ->join('storage_types', 'storage_types.id', 'storage_units.storage_type_id')
        ->where('storage_unit_allocation.tenant_id', '=', auth()->user()->tenant_id)
        ->where(['storage_unit_allocation.job_id'=>$job_id,'job_type'=>'Moving','storage_unit_allocation.deleted'=>'0'])->get();

        //Estimate Section 
        $this->taxs = Tax::where(['tenant_id' => auth()->user()->tenant_id])->get();
        $this->products = Product::select("products.*")
        ->join('product_categories', 'product_categories.id', 'products.category_id')
        ->where(['products.tenant_id' => auth()->user()->tenant_id,'product_categories.category_name'=>'Storage'])
        ->get();

        // invoice and payment grid
        $this->invoice_items = [];
        $this->payment_items = [];
        $this->invoice=[];
        $this->paidAmount = 0;
        $this->totalAmount = 0;
        $this->crm_contact_email=null;
        $this->crm_contact_phone=null;
        $this->stripe = TenantApiDetail::where('tenant_id', auth()->user()->tenant_id)
                    ->where('provider', 'Stripe')->first();
        $this->invoice = Invoice::where(['job_id'=> $job_id,'sys_job_type'=>'Moving_Storage'])->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        //print_r($this->invoice);exit;
        if (isset($this->invoice->id)):
            $this->invoice_items = InvoiceItems::where(['invoice_id'=>$this->invoice->id,'tenant_id'=>auth()->user()->tenant_id])->get();
            //$this->invoice_charges = InvoiceItems::where(['invoice_id'=>$this->invoice->id,'tenant_id'=>auth()->user()->tenant_id,'type'=>'Charge'])->get();            
            $this->payment_items = Payment::where('invoice_id', '=', $this->invoice->id)->where('tenant_id', '=', auth()->user()->tenant_id)->get();
        endif;
        $this->payment_methods = OfflinePaymentMethod::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $this->taxs = Tax::where(['tenant_id' => auth()->user()->tenant_id])->get();
        if (isset($this->invoice->id)):
            $this->paidAmount = $this->invoice->getPaidAmount();
            $this->totalAmount = $this->invoice->getTotalAmount();
        endif;
        $this->products = Product::select("products.*")
        ->join('product_categories', 'product_categories.id', 'products.category_id')
        ->where(['products.tenant_id' => auth()->user()->tenant_id,'product_categories.category_name'=>'Storage'])
        ->get();
        $response['storage_reservation_html'] = view('admin.list-jobs.jobs.storage.reservation_grid', $this->data)->render();
        $response['storage_invoice_html'] = view('admin.list-jobs.jobs.storage.storage_invoice_grid', $this->data)->render();
        $response['storage_payment_html'] = view('admin.list-jobs.jobs.storage.storage_payment_grid', $this->data)->render();
        $response['error']=0;
        return json_encode($response);
    }
    //END:: Storage Module

    public function ajaxSaveMaterialIssue(Request $request)
    {
        $this->job_id = $request->job_id;
        $this->crmlead = CRMLeads::findOrFail($request->lead_id);
        DB::table('inv_job_items_issued')->insert([
            'tenant_id' => auth()->user()->tenant_id,
            'item_id' => $request->product_id,
            'sys_job_type' => 'Moving',
            'job_id' => $request->job_id,
            'quantity' => $request->quantity,
            'created_date' => Carbon::now(),
            'created_by' => auth()->user()->id
        ]);
        if($this->crmlead->lead_type == 'Residential'){
            $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                        ->where('product_type', '!=', 'Charge')
                                        ->where(function ($query) {
                                            $query->orWhere('customer_type', 'Residential')
                                                ->orWhere('customer_type', 'Both');
                                        })->get();
        }else{
            $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                        ->where('product_type', '!=', 'Charge')
                                        ->where(function ($query) {
                                            $query->orWhere('customer_type', 'Commercial')
                                                ->orWhere('customer_type', 'Both');
                                        })
                                        ->where(function ($query) {
                                            $query->orWhere('customer_id', NULL)
                                                ->orWhere('customer_id', $this->crmlead->id);
                                        })->get();
        }

        $this->material_issue_items = DB::table('inv_job_items_issued')->select([
                                                                        'inv_job_items_issued.*',
                                                                        'products.name',
                                                                        'products.description',
                                                                    ])
                                                                    ->where(['inv_job_items_issued.tenant_id' => auth()->user()->tenant_id, 'inv_job_items_issued.job_id' => $this->job_id])
                                                                    ->leftjoin('products', 'products.id', 'inv_job_items_issued.item_id')
                                                                    ->get();

        
        $response['error'] = 0;
        $response['message'] = 'Material Issue has been saved';
        $response['html'] = view('admin.list-jobs.jobs.material_issues', $this->data)->render();
        return json_encode($response);
    }
    public function ajaxUpdateMaterialIssue(Request $request)
    {
        $this->job_id = $request->job_id;
        $this->crmlead = CRMLeads::findOrFail($request->lead_id);
        DB::table('inv_job_items_issued')
                            ->where(['id' => $request->id, 'tenant_id' => auth()->user()->tenant_id])
                            ->update([
                                'item_id' => $request->product_id,
                                'quantity' => $request->quantity,
                                'updated_date' => Carbon::now(),
                                'updated_by' => auth()->user()->id
                            ]);
        if($this->crmlead->lead_type == 'Residential'){
            $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                        ->where('product_type', '!=', 'Charge')
                                        ->where(function ($query) {
                                            $query->orWhere('customer_type', 'Residential')
                                                ->orWhere('customer_type', 'Both');
                                        })->get();
        }else{
            $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                        ->where('product_type', '!=', 'Charge')
                                        ->where(function ($query) {
                                            $query->orWhere('customer_type', 'Commercial')
                                                ->orWhere('customer_type', 'Both');
                                        })
                                        ->where(function ($query) {
                                            $query->orWhere('customer_id', NULL)
                                                ->orWhere('customer_id', $this->crmlead->id);
                                        })->get();
        }

        $this->material_issue_items = DB::table('inv_job_items_issued')->select([
                                                                        'inv_job_items_issued.*',
                                                                        'products.name',
                                                                        'products.description',
                                                                    ])
                                                                    ->where(['inv_job_items_issued.tenant_id' => auth()->user()->tenant_id, 'inv_job_items_issued.job_id' => $this->job_id])
                                                                    ->leftjoin('products', 'products.id', 'inv_job_items_issued.item_id')
                                                                    ->get();

        
        $response['error'] = 0;
        $response['message'] = 'Material Issue has been Update';
        $response['html'] = view('admin.list-jobs.jobs.material_issues', $this->data)->render();
        return json_encode($response);
    }

    public function ajaxDestroyMaterialIssue(Request $request)
    {
        $this->job_id = $request->job_id;
        $this->crmlead = CRMLeads::findOrFail($request->lead_id);
        DB::table('inv_job_items_issued')
                            ->where(['id' => $request->id, 'tenant_id' => auth()->user()->tenant_id])
                            ->delete();
        if($this->crmlead->lead_type == 'Residential'){
            $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                        ->where('product_type', '!=', 'Charge')
                                        ->where(function ($query) {
                                            $query->orWhere('customer_type', 'Residential')
                                                ->orWhere('customer_type', 'Both');
                                        })->get();
        }else{
            $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                        ->where('product_type', '!=', 'Charge')
                                        ->where(function ($query) {
                                            $query->orWhere('customer_type', 'Commercial')
                                                ->orWhere('customer_type', 'Both');
                                        })
                                        ->where(function ($query) {
                                            $query->orWhere('customer_id', NULL)
                                                ->orWhere('customer_id', $this->crmlead->id);
                                        })->get();
        }

        $this->material_issue_items = DB::table('inv_job_items_issued')->select([
                                                                        'inv_job_items_issued.*',
                                                                        'products.name',
                                                                        'products.description',
                                                                    ])
                                                                    ->where(['inv_job_items_issued.tenant_id' => auth()->user()->tenant_id, 'inv_job_items_issued.job_id' => $this->job_id])
                                                                    ->leftjoin('products', 'products.id', 'inv_job_items_issued.item_id')
                                                                    ->get();

        
        $response['error'] = 0;
        $response['message'] = 'Material Issue has been Deleted';
        $response['html'] = view('admin.list-jobs.jobs.material_issues', $this->data)->render();
        return json_encode($response);
    }

    public function ajaxSaveMaterialReturn(Request $request)
    {
        $this->job_id = $request->job_id;
        $this->crmlead = CRMLeads::findOrFail($request->lead_id);
        DB::table('inv_job_items_returned')->insert([
            'tenant_id' => auth()->user()->tenant_id,
            'item_id' => $request->product_id,
            'sys_job_type' => 'Moving',
            'job_id' => $request->job_id,
            'quantity' => $request->quantity,
            'created_date' => Carbon::now(),
            'created_by' => auth()->user()->id
        ]);
        if($this->crmlead->lead_type == 'Residential'){
            $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                        ->where('product_type', '!=', 'Charge')
                                        ->where(function ($query) {
                                            $query->orWhere('customer_type', 'Residential')
                                                ->orWhere('customer_type', 'Both');
                                        })->get();
        }else{
            $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                        ->where('product_type', '!=', 'Charge')
                                        ->where(function ($query) {
                                            $query->orWhere('customer_type', 'Commercial')
                                                ->orWhere('customer_type', 'Both');
                                        })
                                        ->where(function ($query) {
                                            $query->orWhere('customer_id', NULL)
                                                ->orWhere('customer_id', $this->crmlead->id);
                                        })->get();
        }

        $this->material_issue_items = DB::table('inv_job_items_issued')->select([
                                                'inv_job_items_issued.*',
                                                'products.name',
                                                'products.description',
                                            ])
                                            ->where(['inv_job_items_issued.tenant_id' => auth()->user()->tenant_id, 'inv_job_items_issued.job_id' => $this->job_id])
                                            ->leftjoin('products', 'products.id', 'inv_job_items_issued.item_id')
                                            ->get();

        $this->material_return_items = DB::table('inv_job_items_returned')->select([
                                                                        'inv_job_items_returned.*',
                                                                        'products.name',
                                                                        'products.description',
                                                                    ])
                                                                    ->where(['inv_job_items_returned.tenant_id' => auth()->user()->tenant_id, 'inv_job_items_returned.job_id' => $this->job_id])
                                                                    ->leftjoin('products', 'products.id', 'inv_job_items_returned.item_id')
                                                                    ->get();

        
        $response['error'] = 0;
        $response['message'] = 'Material Return has been saved';
        $response['html'] = view('admin.list-jobs.jobs.material_returns', $this->data)->render();
        return json_encode($response);
    }

    public function ajaxUpdateMaterialReturn(Request $request)
    {
        $this->job_id = $request->job_id;
        $this->crmlead = CRMLeads::findOrFail($request->lead_id);
        DB::table('inv_job_items_returned')
                            ->where(['id' => $request->id, 'tenant_id' => auth()->user()->tenant_id])
                            ->update([
                                'item_id' => $request->product_id,
                                'quantity' => $request->quantity,
                                'updated_date' => Carbon::now(),
                                'updated_by' => auth()->user()->id
                            ]);
        if($this->crmlead->lead_type == 'Residential'){
            $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                        ->where('product_type', '!=', 'Charge')
                                        ->where(function ($query) {
                                            $query->orWhere('customer_type', 'Residential')
                                                ->orWhere('customer_type', 'Both');
                                        })->get();
        }else{
            $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                        ->where('product_type', '!=', 'Charge')
                                        ->where(function ($query) {
                                            $query->orWhere('customer_type', 'Commercial')
                                                ->orWhere('customer_type', 'Both');
                                        })
                                        ->where(function ($query) {
                                            $query->orWhere('customer_id', NULL)
                                                ->orWhere('customer_id', $this->crmlead->id);
                                        })->get();
        }

        $this->material_issue_items = DB::table('inv_job_items_issued')->select([
                                                            'inv_job_items_issued.*',
                                                            'products.name',
                                                            'products.description',
                                                        ])
                                                        ->where(['inv_job_items_issued.tenant_id' => auth()->user()->tenant_id, 'inv_job_items_issued.job_id' => $this->job_id])
                                                        ->leftjoin('products', 'products.id', 'inv_job_items_issued.item_id')
                                                        ->get();

        $this->material_return_items = DB::table('inv_job_items_returned')->select([
                                                                        'inv_job_items_returned.*',
                                                                        'products.name',
                                                                        'products.description',
                                                                    ])
                                                                    ->where(['inv_job_items_returned.tenant_id' => auth()->user()->tenant_id, 'inv_job_items_returned.job_id' => $this->job_id])
                                                                    ->leftjoin('products', 'products.id', 'inv_job_items_returned.item_id')
                                                                    ->get();

        
        $response['error'] = 0;
        $response['message'] = 'Material Return has been Update';
        $response['html'] = view('admin.list-jobs.jobs.material_returns', $this->data)->render();
        return json_encode($response);
    }

    public function ajaxDestroyMaterialReturn(Request $request)
    {
        $this->job_id = $request->job_id;
        $this->crmlead = CRMLeads::findOrFail($request->lead_id);
        DB::table('inv_job_items_returned')
                            ->where(['id' => $request->id, 'tenant_id' => auth()->user()->tenant_id])
                            ->delete();
        if($this->crmlead->lead_type == 'Residential'){
            $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                        ->where('product_type', '!=', 'Charge')
                                        ->where(function ($query) {
                                            $query->orWhere('customer_type', 'Residential')
                                                ->orWhere('customer_type', 'Both');
                                        })->get();
        }else{
            $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                        ->where('product_type', '!=', 'Charge')
                                        ->where(function ($query) {
                                            $query->orWhere('customer_type', 'Commercial')
                                                ->orWhere('customer_type', 'Both');
                                        })
                                        ->where(function ($query) {
                                            $query->orWhere('customer_id', NULL)
                                                ->orWhere('customer_id', $this->crmlead->id);
                                        })->get();
        }

        $this->material_issue_items = DB::table('inv_job_items_issued')->select([
                                                    'inv_job_items_issued.*',
                                                    'products.name',
                                                    'products.description',
                                                ])
                                                ->where(['inv_job_items_issued.tenant_id' => auth()->user()->tenant_id, 'inv_job_items_issued.job_id' => $this->job_id])
                                                ->leftjoin('products', 'products.id', 'inv_job_items_issued.item_id')
                                                ->get();

        $this->material_return_items = DB::table('inv_job_items_returned')->select([
                                                                        'inv_job_items_returned.*',
                                                                        'products.name',
                                                                        'products.description',
                                                                    ])
                                                                    ->where(['inv_job_items_returned.tenant_id' => auth()->user()->tenant_id, 'inv_job_items_returned.job_id' => $this->job_id])
                                                                    ->leftjoin('products', 'products.id', 'inv_job_items_returned.item_id')
                                                                    ->get();

        
        $response['error'] = 0;
        $response['message'] = 'Material Return has been Deleted';
        $response['html'] = view('admin.list-jobs.jobs.material_returns', $this->data)->render();
        return json_encode($response);
    }

    public function ajaxUpdateAndGenerateInvoice(Request $request)
    {
        $invoice = Invoice::where(['job_id' => $request->job_id, 'sys_job_type' => 'Moving', 'tenant_id' => auth()->user()->tenant_id])->first();
        $issued_items = DB::table('inv_job_items_issued')->where(['job_id' => $request->job_id, 'tenant_id' => auth()->user()->tenant_id])->get();
        foreach($issued_items as $item)
        {
            $returned_item = DB::table('inv_job_items_returned')->where(['job_id' => $request->job_id, 'item_id' => $item->item_id])->first();
            if($returned_item)
            {
                $material_quantity[] = ['item_id' => $returned_item->item_id, 'quantity' => $item->quantity - $returned_item->quantity];
            }
            else
            {
                $material_quantity[] = ['item_id' => $item->item_id, 'quantity' => $item->quantity];
            }
        }

        if($invoice->id)
        {
            for($i = 0; $i < count($material_quantity); $i++)
            {
                $product = DB::table('products')->where('id', $material_quantity[$i]['item_id'])->first();
                $tax = Tax::where(['id' => $product->tax_id, 'tenant_id' => auth()->user()->tenant_id])->first();
                $invoice_item = InvoiceItems::where(['invoice_id' => $invoice->id, 'product_id' => $material_quantity[$i]['item_id'], 'tenant_id' => auth()->user()->tenant_id])->first();
                if($invoice_item)
                {
                    if($material_quantity[$i]['quantity'] != $invoice_item->quantity)
                    {
                        if($material_quantity[$i]['quantity'] == 0 || $material_quantity[$i]['quantity'] < 0)
                        {   
                            $invoice_item->delete();
                        }
                        else
                        {
                            $invoice_item->quantity = $material_quantity[$i]['quantity'];
                            if($tax)
                            {
                                $total_amount = ($product->price*$material_quantity[$i]['quantity'])*(1 + $tax->rate_percent/100);
                            }
                            else
                            {
                                $total_amount = $product->price*$material_quantity[$i]['quantity'];
                            }
                            $invoice_item->amount = floatval($total_amount);
                            $invoice_item->update();
                        }
                    }
                }
                else
                {
                    if($material_quantity[$i]['quantity'] != 0 && $material_quantity[$i]['quantity'] > 0)
                    {
                        if($tax)
                        {
                            $total_amount = ($product->price*$material_quantity[$i]['quantity'])*(1 + $tax->rate_percent/100);
                        }
                        else
                        {
                            $total_amount = $product->price*$material_quantity[$i]['quantity'];
                        }
                        $job = JobsMoving::where(['job_id' => $request->job_id, 'tenant_id' => auth()->user()->tenant_id])->first();
                        $new_invoice_item = new InvoiceItems();
                        $new_invoice_item->tenant_id = auth()->user()->tenant_id;
                        $new_invoice_item->invoice_id = $invoice->id;
                        $new_invoice_item->product_id = $material_quantity[$i]['item_id'];
                        $new_invoice_item->item_name = $product->name;
                        $new_invoice_item->item_summary = $product->description;
                        $new_invoice_item->type = 'Item';
                        $new_invoice_item->quantity = $material_quantity[$i]['quantity'];
                        $new_invoice_item->unit_price = $product->price;
                        $new_invoice_item->amount = floatval($total_amount);
                        $new_invoice_item->tax_id = $product->tax_id;
                        $new_invoice_item->created_at = Carbon::now();
                        $new_invoice_item->save();
                    }
                }
                
            }
        }
        else
        {
            $response['error'] = 1;
            $response['message'] = 'Cannot Update & Generate Invoice SuccessFully';
            return json_encode($response);
        }
        
        $response['error'] = 0;
        $response['message'] = 'Update & Generate Invoice SuccessFully';
        return json_encode($response);
    }

    public function ajaxGetMaterialReturn(Request $request)
    {
        $this->job_id = $request->job_id;
        $this->crmlead = CRMLeads::findOrFail($request->lead_id);
        if($this->crmlead->lead_type == 'Residential'){
            $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                        ->where('product_type', '!=', 'Charge')
                                        ->where(function ($query) {
                                            $query->orWhere('customer_type', 'Residential')
                                                ->orWhere('customer_type', 'Both');
                                        })->get();
        }else{
            $this->products = Product::where('tenant_id', auth()->user()->tenant_id)
                                        ->where('product_type', '!=', 'Charge')
                                        ->where(function ($query) {
                                            $query->orWhere('customer_type', 'Commercial')
                                                ->orWhere('customer_type', 'Both');
                                        })
                                        ->where(function ($query) {
                                            $query->orWhere('customer_id', NULL)
                                                ->orWhere('customer_id', $this->crmlead->id);
                                        })->get();
        }

        $this->material_issue_items = DB::table('inv_job_items_issued')->select([
                                                        'inv_job_items_issued.*',
                                                        'products.name',
                                                        'products.description',
                                                    ])
                                                    ->where(['inv_job_items_issued.tenant_id' => auth()->user()->tenant_id, 'inv_job_items_issued.job_id' => $this->job_id])
                                                    ->leftjoin('products', 'products.id', 'inv_job_items_issued.item_id')
                                                    ->get();

        $this->material_return_items = DB::table('inv_job_items_returned')->select([
                                                        'inv_job_items_returned.*',
                                                        'products.name',
                                                        'products.description',
                                                    ])
                                                    ->where(['inv_job_items_returned.tenant_id' => auth()->user()->tenant_id, 'inv_job_items_returned.job_id' => $this->job_id])
                                                    ->leftjoin('products', 'products.id', 'inv_job_items_returned.item_id')
                                                    ->get();

        $response['error'] = 0;
        $response['html'] = view('admin.list-jobs.jobs.material_returns', $this->data)->render();
        return json_encode($response);
    }

    public function dailyDiary()
    {
        try {
            $this->pageTitle = __('app.menu.dailyDiary');
            $this->pageIcon = 'icon-calender';
            $this->today = Carbon::parse(Carbon::now())->format('Y-m-d');
            // $this->today = Carbon::createFromFormat($this->global->date_format, $today)->toDateString();
            // dd($this->today);
            // $this->today = "2021-08-03";
            $this->display_date = date('l, M d, Y', strtotime($this->today));
            // dd($this->display_date);
            $this->legs = JobsMovingLegs::select(
                            'jobs_moving_legs.*',  
                            'jobs_moving.customer_id', 
                            'jobs_moving.job_number', 
                            'jobs_moving.job_date',
                            )
                                ->join('jobs_moving', 'jobs_moving.job_id', 'jobs_moving_legs.job_id')
                                // ->leftjoin('crm_contacts', 'crm_contacts.lead_id', 'jobs_moving.customer_id')
                                ->where('jobs_moving_legs.leg_date', $this->today)
                                ->where('jobs_moving_legs.tenant_id', auth()->user()->tenant_id)
                                ->get();
                                // dd($this->legs);
            $this->drivers = User::driverList();
            $this->people = User::allPeople();

            $post_data = null;
            foreach ($this->legs as $leg) {
                if($leg->vehicle_id)
                {
                    $vehicle = $this->vehicle_name = Vehicles::where(['id' => $leg->vehicle_id, 'tenant_id' => auth()->user()->tenant_id])->pluck('vehicle_name')->first();
                }
                else
                {
                    $vehicle = NULL;
                }
                $post_data[] = array(
                    'job_id' => $leg->job_id, //. '&vehicle_job&leg_' . $job->job_id
                    'leg_id' => $leg->id,
                    'leg_number' => $leg->leg_number,
                    'name' => CRMContacts::where('lead_id', $leg->customer_id)->where('deleted', 'N')->pluck('name')->first(),
                    'from'=> $leg->pickup_address,
                    'to'=> $leg->drop_off_address,
                    'action' => 'uplift',
                    'start' => date('h:i a', strtotime($leg->est_start_time)),
                    'finish' => date('h:i a', strtotime($leg->est_finish_time)),
                    'comment' => $leg->notes,
                    'vehicle' => $vehicle,
                    'driver' => $leg->driver_id,
                    'offsiders' => $leg->offsider_ids,
                );
            }

            // dd($post_data);
            $this->new_data = $post_data;

            // dd($this->new_data);
            return view('admin.list-jobs.daily-dairy', $this->data);
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function getDailyDiaryData(Request $request)
    {
        $this->date = Carbon::parse($request->date)->format('Y-m-d');
        $this->display_date = date('l, M d, Y', strtotime($this->date));
            $this->legs = JobsMovingLegs::select(
                            'jobs_moving_legs.*',  
                            'jobs_moving.customer_id', 
                            'jobs_moving.job_number', 
                            'jobs_moving.job_date',
                            )
                                ->join('jobs_moving', 'jobs_moving.job_id', 'jobs_moving_legs.job_id')
                                // ->leftjoin('crm_contacts', 'crm_contacts.lead_id', 'jobs_moving.customer_id')
                                ->where('jobs_moving_legs.leg_date', $this->date)
                                ->where('jobs_moving_legs.tenant_id', auth()->user()->tenant_id)
                                ->get();
                                // dd($this->legs);
            $this->drivers = User::driverList();
            $this->people = User::allPeople();

            $post_data = null;
            foreach ($this->legs as $leg) {
                if($leg->vehicle_id)
                {
                    $vehicle = $this->vehicle_name = Vehicles::where(['id' => $leg->vehicle_id, 'tenant_id' => auth()->user()->tenant_id])->pluck('vehicle_name')->first();
                }
                else
                {
                    $vehicle = NULL;
                }
                $driver_name = null;
                $offsiders_name = '';
                if($leg->driver_id) {
                    foreach($this->drivers as $d)
                    {
                        if($d->id==$leg->driver_id)
                        {
                            $driver_name = $d->name;
                            break;
                        }
                    }   
                    
                }
                if($leg->offsider_ids)
                {
                    $offsiders = explode(',',$leg->offsider_ids);
                                                
                    foreach($offsiders as $sider)
                    {
                        foreach($this->people as $p)
                        {
                            if($p->id==$sider)
                            {
                                if($offsiders_name == '')
                                {
                                    $offsiders_name = $p->name;
                                    break;
                                }else {
                                    $offsiders_name = $offsiders_name.', '.$p->name;
                                    break;
                                }
                            }
                        }
                    }                    
                }
                $pickup = Str::limit($leg->pickup_address, 15, '...');
                $drop_off = Str::limit($leg->drop_off_address, 15, '...');
                $post_data[] = array(
                    'job_id' => $leg->job_id, //. '&vehicle_job&leg_' . $job->job_id
                    'leg_id' => $leg->id,
                    'leg_number' => $leg->leg_number,
                    'name' => CRMContacts::where('lead_id', $leg->customer_id)->where('deleted', 'N')->pluck('name')->first(),
                    'from'=> $pickup,
                    'to'=> $drop_off,
                    'action' => 'uplift',
                    'start' => date('h:i', strtotime($leg->job_start_date)),
                    'finish' => date('h:if', strtotime($leg->job_end_date)),
                    'comment' => $leg->notes,
                    'vehicle' => $vehicle,
                    'driver' => $driver_name,
                    'offsiders' => $offsiders_name,
                );
            }

            $this->new_data = $post_data;

            return response()->json([
                'data' => $this->data
            ]);
    }

    public function getDailyDiaryToday(Request $request)
    {
        $this->date = Carbon::parse(Carbon::now())->format('Y-m-d');
        $this->display_date = date('l, M d, Y', strtotime($this->date));
            $this->legs = JobsMovingLegs::select(
                            'jobs_moving_legs.*',  
                            'jobs_moving.customer_id', 
                            'jobs_moving.job_number', 
                            'jobs_moving.job_date',
                            )
                                ->join('jobs_moving', 'jobs_moving.job_id', 'jobs_moving_legs.job_id')
                                // ->leftjoin('crm_contacts', 'crm_contacts.lead_id', 'jobs_moving.customer_id')
                                ->where('jobs_moving_legs.leg_date', $this->date)
                                ->where('jobs_moving_legs.tenant_id', auth()->user()->tenant_id)
                                ->get();
                                // dd($this->legs);
            $this->drivers = User::driverList();
            $this->people = User::allPeople();

            $post_data = null;
            foreach ($this->legs as $leg) {
                if($leg->vehicle_id)
                {
                    $vehicle = $this->vehicle_name = Vehicles::where(['id' => $leg->vehicle_id, 'tenant_id' => auth()->user()->tenant_id])->pluck('vehicle_name')->first();
                }
                else
                {
                    $vehicle = NULL;
                }
                $driver_name = null;
                $offsiders_name = '';
                if($leg->driver_id) {
                    foreach($this->drivers as $d)
                    {
                        if($d->id==$leg->driver_id)
                        {
                            $driver_name = $d->name;
                            break;
                        }
                    }   
                    
                }
                if($leg->offsider_ids)
                {
                    $offsiders = explode(',',$leg->offsider_ids);
                                                
                    foreach($offsiders as $sider)
                    {
                        foreach($this->people as $p)
                        {
                            if($p->id==$sider)
                            {
                                if($offsiders_name == '')
                                {
                                    $offsiders_name = $p->name;
                                    break;
                                }else {
                                    $offsiders_name = $offsiders_name.', '.$p->name;
                                    break;
                                }
                            }
                        }
                    }                    
                }
                $pickup = Str::limit($leg->pickup_address, 15, '...');
                $drop_off = Str::limit($leg->drop_off_address, 15, '...');
                $post_data[] = array(
                    'job_id' => $leg->job_id,
                    'leg_id' => $leg->id,
                    'leg_number' => $leg->leg_number,
                    'name' => CRMContacts::where('lead_id', $leg->customer_id)->where('deleted', 'N')->pluck('name')->first(),
                    'from'=> $pickup,
                    'to'=> $drop_off,
                    'action' => 'uplift',
                    'start' => date('h:i', strtotime($leg->job_start_date)),
                    'finish' => date('h:if', strtotime($leg->job_end_date)),
                    'comment' => $leg->notes,
                    'vehicle' => $vehicle,
                    'driver' => $driver_name,
                    'offsiders' => $offsiders_name,
                );
            }

            $this->new_data = $post_data;

            return response()->json([
                'data' => $this->data
            ]);
    }

    public function getDailyDiaryRightArrow(Request $request)
    {
        // dd($request->all());
        $this->date = Carbon::parse($request->date)->addDay()->format('Y-m-d');
        // dd($this->date);
        $this->display_date = date('l, M d, Y', strtotime($this->date));
            $this->legs = JobsMovingLegs::select(
                            'jobs_moving_legs.*',  
                            'jobs_moving.customer_id', 
                            'jobs_moving.job_number', 
                            'jobs_moving.job_date',
                            )
                                ->join('jobs_moving', 'jobs_moving.job_id', 'jobs_moving_legs.job_id')
                                // ->leftjoin('crm_contacts', 'crm_contacts.lead_id', 'jobs_moving.customer_id')
                                ->where('jobs_moving_legs.leg_date', $this->date)
                                ->where('jobs_moving_legs.tenant_id', auth()->user()->tenant_id)
                                ->get();
                                // dd($this->legs);
            $this->drivers = User::driverList();
            $this->people = User::allPeople();

            $post_data = null;
            foreach ($this->legs as $leg) {
                if($leg->vehicle_id)
                {
                    $vehicle = $this->vehicle_name = Vehicles::where(['id' => $leg->vehicle_id, 'tenant_id' => auth()->user()->tenant_id])->pluck('vehicle_name')->first();
                }
                else
                {
                    $vehicle = NULL;
                }
                $driver_name = null;
                $offsiders_name = '';
                if($leg->driver_id) {
                    foreach($this->drivers as $d)
                    {
                        if($d->id==$leg->driver_id)
                        {
                            $driver_name = $d->name;
                            break;
                        }
                    }   
                    
                }
                if($leg->offsider_ids)
                {
                    $offsiders = explode(',',$leg->offsider_ids);
                                                
                    foreach($offsiders as $sider)
                    {
                        foreach($this->people as $p)
                        {
                            if($p->id==$sider)
                            {
                                if($offsiders_name == '')
                                {
                                    $offsiders_name = $p->name;
                                    break;
                                }else {
                                    $offsiders_name = $offsiders_name.', '.$p->name;
                                    break;
                                }
                            }
                        }
                    }                    
                }
                $pickup = Str::limit($leg->pickup_address, 15, '...');
                $drop_off = Str::limit($leg->drop_off_address, 15, '...');
                $post_data[] = array(
                    'job_id' => $leg->job_id,
                    'leg_id' => $leg->id,
                    'leg_number' => $leg->leg_number,
                    'name' => CRMContacts::where('lead_id', $leg->customer_id)->where('deleted', 'N')->pluck('name')->first(),
                    'from'=> $pickup,
                    'to'=> $drop_off,
                    'action' => 'uplift',
                    'start' => date('h:i', strtotime($leg->job_start_date)),
                    'finish' => date('h:if', strtotime($leg->job_end_date)),
                    'comment' => $leg->notes,
                    'vehicle' => $vehicle,
                    'driver' => $driver_name,
                    'offsiders' => $offsiders_name,
                );
            }

            $this->new_data = $post_data;

            return response()->json([
                'data' => $this->data
            ]);
    }

    public function getDailyDiaryLeftArrow(Request $request)
    {
        $this->date = Carbon::parse($request->date)->subDay()->format('Y-m-d');
        $this->display_date = date('l, M d, Y', strtotime($this->date));
            $this->legs = JobsMovingLegs::select(
                            'jobs_moving_legs.*',  
                            'jobs_moving.customer_id', 
                            'jobs_moving.job_number', 
                            'jobs_moving.job_date',
                            )
                                ->join('jobs_moving', 'jobs_moving.job_id', 'jobs_moving_legs.job_id')
                                ->where('jobs_moving_legs.leg_date', $this->date)
                                ->where('jobs_moving_legs.tenant_id', auth()->user()->tenant_id)
                                ->get();
            $this->drivers = User::driverList();
            $this->people = User::allPeople();

            $post_data = null;
            foreach ($this->legs as $leg) {
                if($leg->vehicle_id)
                {
                    $vehicle = $this->vehicle_name = Vehicles::where(['id' => $leg->vehicle_id, 'tenant_id' => auth()->user()->tenant_id])->pluck('vehicle_name')->first();
                }
                else
                {
                    $vehicle = NULL;
                }
                $driver_name = null;
                $offsiders_name = '';
                if($leg->driver_id) {
                    foreach($this->drivers as $d)
                    {
                        if($d->id==$leg->driver_id)
                        {
                            $driver_name = $d->name;
                            break;
                        }
                    }   
                    
                }
                if($leg->offsider_ids)
                {
                    $offsiders = explode(',',$leg->offsider_ids);
                                                
                    foreach($offsiders as $sider)
                    {
                        foreach($this->people as $p)
                        {
                            if($p->id==$sider)
                            {
                                if($offsiders_name == '')
                                {
                                    $offsiders_name = $p->name;
                                    break;
                                }else {
                                    $offsiders_name = $offsiders_name.', '.$p->name;
                                    break;
                                }
                            }
                        }
                    }                    
                }
                $pickup = Str::limit($leg->pickup_address, 15, '...');
                $drop_off = Str::limit($leg->drop_off_address, 15, '...');
                $post_data[] = array(
                    'job_id' => $leg->job_id,
                    'leg_id' => $leg->id,
                    'leg_number' => $leg->leg_number,
                    'name' => CRMContacts::where('lead_id', $leg->customer_id)->where('deleted', 'N')->pluck('name')->first(),
                    'from'=> $pickup,
                    'to'=> $drop_off,
                    'action' => 'uplift',
                    'start' => date('h:i', strtotime($leg->job_start_date)),
                    'finish' => date('h:if', strtotime($leg->job_end_date)),
                    'comment' => $leg->notes,
                    'vehicle' => $vehicle,
                    'driver' => $driver_name,
                    'offsiders' => $offsiders_name,
                );
            }

            $this->new_data = $post_data;

            return response()->json([
                'data' => $this->data
            ]);
    }

    public function getDailyDiaryVehicles()
    {
        $vehicles = Vehicles::select('id', 'vehicle_name')
            ->where('tenant_id', '=', auth()->user()->tenant_id)
            ->get();

        return response()->json([
            'success' => 1,
            'vehicles' => $vehicles,
        ]);
    }

    public function getDailyDiaryDrivers()
    {
        $drivers = User::driverList();

        return response()->json([
            'success' => 1,
            'drivers' => $drivers,
        ]);
    }

    public function getDailyDiaryOffsiders()
    {
        $offsiders = User::allPeople();

        return response()->json([
            'success' => 1,
            'offsiders' => $offsiders,
        ]);
    }

    public function updateDailyDiary(Request $request)
    {
        if($request->tab == 'comment')
        {
            JobsMovingLegs::where(['id' => $request->leg_id, 'tenant_id' => auth()->user()->tenant_id])
                                ->update([
                                    'notes' => $request->newComment
                                ]);

            return response()->json([
                'success' => 1,
                'message' => 'Comment has been Updated SuccessFully!'
            ]);
        }
        if($request->tab == 'vehicle')
        {
            JobsMovingLegs::where(['id' => $request->leg_id, 'tenant_id' => auth()->user()->tenant_id])
                                ->update([
                                    'vehicle_id' => $request->vehicle_id
                                ]);
            $vehicle = Vehicles::where(['id' => $request->vehicle_id, 'tenant_id' => auth()->user()->tenant_id])->first();

            return response()->json([
                'success' => 1,
                'vehicle_name' => $vehicle->vehicle_name,
                'message' => 'Vehicle has been Updated SuccessFully!'
            ]);
        }
        if($request->tab == 'driver')
        {
            JobsMovingLegs::where(['id' => $request->leg_id, 'tenant_id' => auth()->user()->tenant_id])
                                ->update([
                                    'driver_id' => $request->driver_id
                                ]);
            $drivers = User::driverList();
            $driver = null;
            foreach($drivers as $d)
            {
                if($d->id == $request->driver_id)
                {
                    $driver = $d;
                }
            }

            return response()->json([
                'success' => 1,
                'driver_name' => $driver->name,
                'message' => 'Vehicle has been Updated SuccessFully!'
            ]);
        }
        if($request->tab == 'estStart')
        {
            // dd($request->all());
            JobsMovingLegs::where(['id' => $request->leg_id, 'tenant_id' => auth()->user()->tenant_id])
                                ->update([
                                    'est_start_time' => $request->newEstStart
                                ]);

            return response()->json([
                'success' => 1,
                'message' => 'Start Time has been Updated SuccessFully!'
            ]);
        }
        if($request->tab == 'estFinish')
        {
            JobsMovingLegs::where(['id' => $request->leg_id, 'tenant_id' => auth()->user()->tenant_id])
                                ->update([
                                    'est_finish_time' => $request->newEstFinish
                                ]);

            return response()->json([
                'success' => 1,
                'message' => 'Finish Time has been Updated SuccessFully!'
            ]);
        }
        if($request->tab == 'offsiders')
        {
            if($request->offsiders_ids)
            {
                $newIds = '';
                $new_offsiders_name = '';
                $all_offsiders = User::allPeople();
                $offsiders = substr_replace($request->offsiders_ids, "", -1);
                $offsiders = explode(',', $offsiders);
                foreach($offsiders as $ids)
                {
                    if($newIds == '')
                    {
                        $newIds = $ids;
                    }
                    else
                    {
                        $newIds = $newIds.','.$ids;
                    }

                    foreach($all_offsiders as $all)
                    {
                        if($all->id == $ids)
                        {
                            if($new_offsiders_name == '')
                            {
                                $new_offsiders_name = $all->name;
                            }
                            else
                            {
                                $new_offsiders_name = $new_offsiders_name.','.$all->name;
                            }
                            break;
                        }
                    }
                }
                JobsMovingLegs::where(['id' => $request->leg_id, 'tenant_id' => auth()->user()->tenant_id])
                                    ->update([
                                        'offsider_ids' => $newIds
                                    ]);

                return response()->json([
                    'success' => 1,
                    'offsiders_name' => $new_offsiders_name,
                    'message' => 'Offsiders has been Updated SuccessFully!'
                ]);
            }
        }
    }

    public function clearfix()
    {
        $exitCode = Artisan::call('cache:clear');
        $exitCode = Artisan::call('config:cache');
        $exitCode = Artisan::call('optimize');
        $exitCode = Artisan::call('route:cache');
        $exitCode = Artisan::call('route:clear');
        $exitCode = Artisan::call('view:clear');
    }

}