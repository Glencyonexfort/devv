<?php

namespace App\Http\Controllers\Admin;

use App\Companies;
use App\CRMActivityLog;
use App\ProjectActivity;
use App\Traits\FileSystemSettingTrait;
use App\UniversalSearch;
use App\UserActivity;
use App\Http\Controllers\Controller;
use App\Lists;
use App\Permission;
use App\RoleUser;
use App\SysModules;
use App\TenantModules;
use Illuminate\Support\Facades\Artisan;

class AdminBaseController extends Controller
{

    use FileSystemSettingTrait;

    /**
     * @var array
     */
    public $data = [];

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name];
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * UserBaseController constructor.
     */
    public function __construct()
    {
        parent::__construct();


        $this->middleware(function ($request, $next) {
            $this->companyName = $this->global->company_name;
            //$this->setFileSystemConfigs();
            $this->list_options = Lists::lead_info_types();
            //$this->emailSetting = email_notification_setting();
            $this->languageSettings = language_setting();
            $this->adminTheme = admin_theme();
            //$this->pushSetting = push_setting();
            $this->module_removals = TenantModules::where('tenant_id', '=', auth()->user()->tenant_id)->where('sys_module_id', '=', '1')->first();
            $this->module_cleaning = TenantModules::where('tenant_id', '=', auth()->user()->tenant_id)->where('sys_module_id', '=', '2')->first();
           
            $this->user = user();
            //            echo '<pre>';echo (auth()->user()->role());exit;
            $this->modules = $this->user->modules;

            //Setting User Role Permissions
            $this->user_role = RoleUser::where('tenant_id', '=', auth()->user()->tenant_id)->where('user_id', '=', auth()->user()->id)->pluck('role_id')->first();
            $this->permissions = Permission::where(['r.tenant_id'=> auth()->user()->tenant_id,'r.role_id'=> $this->user_role])
            ->join('permission_role as r', 'r.permission_id','=', 'permissions.id')
            ->get('permissions.name');
            $this->user_permissions = array_column($this->permissions->toArray(), 'name');
            //end----->
            /*
            $this->unreadMessageCount = $this->user->user_chat->count();

            $data = \DB::table('notifications')
                ->select('type', \DB::raw('count(*) as total'))
                ->where('notifiable_id', $this->user->id)
                ->where('notifications.tenant_id', '=', auth()->user()->tenant_id)
                ->whereNull('read_at')
                ->groupBy('type')
                ->get();

            $counts = $data->groupBy('type');

            $type = 'App\Notifications\NewTicket';
            $this->unreadTicketCount = isset($counts[$type]) ? $counts[$type][0]->total : 0;


            $type = 'App\Notifications\NewExpenseAdmin';
            $this->unreadExpenseCount = isset($counts[$type]) ? $counts[$type][0]->total : 0;

            $type = 'App\Notifications\NewIssue';
            $this->unreadIssuesCount = isset($counts[$type]) ? $counts[$type][0]->total : 0;

            */
            $this->new_log_count = CRMActivityLog::log_count();
            $this->new_opportunity_count = CRMActivityLog::opportunity_count();
            $this->new_job_moving_count = CRMActivityLog::job_moving_count();

            $this->stickyNotes = $this->user->sticky;

            //START::For New Lead Popup
            $this->op_type = Lists::sys_job_type();
            $this->companies_list = Companies::where(['tenant_id'=>auth()->user()->tenant_id,'active'=>'Y'])->get();                
            //END::For New Lead Popup

            return $next($request);
        });
    }

    public static function objectToArray(&$object)
    {
        return @json_decode(json_encode($object), true);
    }

    public function logProjectActivity($projectId, $text)
    {
        $activity = new ProjectActivity();
        $activity->project_id = $projectId;
        $activity->activity = $text;
        $activity->save();
    }

    public function logUserActivity($userId, $text)
    {
        $activity = new UserActivity();
        $activity->user_id = $userId;
        $activity->activity = $text;
        $activity->save();
    }

    public function logSearchEntry($searchableId, $title, $route)
    {
        $search = new UniversalSearch();
        $search->searchable_id = $searchableId;
        $search->title = $title;
        $search->route_name = $route;
        $search->save();
    }
}
