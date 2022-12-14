<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Zizaco\Entrust\Entrust;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    use Notifiable, EntrustUserTrait;

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('status', '=', 'active');
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','tenant_id','mobile'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public $dates = ['created_at', 'updated_at'];

    protected $appends = [];

    /**
     * Route notifications for the Slack channel.
     *
     * @return string
     */
    public function routeNotificationForSlack()
    {
        $slack = SlackSetting::first();
        return $slack->slack_webhook;
    }

    public function routeNotificationForOneSignal()
    {
        return $this->onesignal_player_id;
    }


    public function client()
    {
        return $this->hasMany(ClientDetails::class, 'user_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }

    public function employee()
    {
        return $this->hasMany(EmployeeDetails::class, 'user_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'client_id');
    }

    public function member()
    {
        return $this->hasMany(ProjectMember::class, 'user_id');
    }

    public function role()
    {
        return $this->hasMany(RoleUser::class, 'user_id');
    }

    public function attendee()
    {
        return $this->hasMany(EventAttendee::class, 'user_id');
    }

    public function agent()
    {
        return $this->hasMany(TicketAgentGroups::class, 'agent_id');
    }

    public function group()
    {
        return $this->hasMany(EmployeeTeam::class, 'user_id');
    }

    public function skills()
    {
        return EmployeeSkill::select('skills.name')->join('skills', 'skills.id', 'employee_skills.skill_id')->where('user_id', $this->id)->pluck('name')->toArray();
    }


    public static function allClients()
    {
        return User::withoutGlobalScope('active')
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->leftJoin('client_details', 'users.id', '=', 'client_details.user_id')
            ->select('users.id', 'users.name', 'users.email', 'users.created_at', 'client_details.company_name')
            ->where('roles.name', 'client')
            ->get();
    }

    public static function allEmployees($exceptId = NULL)
    {
        $users = User::withoutGlobalScope('active')->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email', 'users.created_at')
            ->where('roles.name', '<>', 'client');

        if (!is_null($exceptId)) {
            $users->where('users.id', '<>', $exceptId);
        }

        $users->groupBy('users.id');
        return $users->get();
    }

    public static function allDrivers($exceptId = NULL)
    {
        $users = User::withoutGlobalScope('active')->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email', 'users.created_at')
            ->where('roles.name', '=', 'driver')
            ->where('users.tenant_id', auth()->user()->tenant_id);

        if (!is_null($exceptId)) {
            $users->where('users.id', '<>', $exceptId);
        }

        $users->groupBy('users.id');
        return $users->get();
    }

    public static function allPeople($exceptId = NULL)
    {
        $users = PplPeople::where(['ppl_people.deleted'=>'N'])
            ->where('ppl_people.tenant_id', auth()->user()->tenant_id)
            ->select(DB::raw("CONCAT(ppl_people.first_name,' ',ppl_people.last_name) AS name,ppl_people.id as id,ppl_people.user_id as user_id"))
            ->orderBy('ppl_people.first_name', 'ASC');
        return $users->get();
    }

    public static function allPeopleWithSystemUsers($exceptId = NULL)
    {
        $users = PplPeople::where(['ppl_people.deleted'=>'N'])
            ->where('ppl_people.tenant_id', auth()->user()->tenant_id)
            ->where('ppl_people.is_system_user', '=', 'Y')
            ->select(DB::raw("CONCAT(ppl_people.first_name,' ',ppl_people.last_name) AS name,ppl_people.id as id, ppl_people.user_id as user_id"))
            ->orderBy('ppl_people.first_name', 'ASC');
        return $users->get();
    }

    public static function allPeopleWithSystemUsersWithNoDriver($exceptId = NULL)
    {
        $users = PplPeople::where(['ppl_people.deleted'=>'N'])
            ->where('ppl_people.tenant_id', auth()->user()->tenant_id)
            ->where('ppl_people.is_system_user', '=', 'Y')
            ->select(DB::raw("CONCAT(ppl_people.first_name,' ',ppl_people.last_name) AS name,ppl_people.id as id, ppl_people.user_id as user_id"))
            ->leftjoin('role_user', 'role_user.user_id', 'ppl_people.user_id')
            ->where('role_user.role_id', '!=', 4);
        return $users->get();
    }

    public static function driverList($exceptId = NULL)
    {
        $users = PplPeople::where(['ppl_people.is_system_user'=>'Y','sys_modules.sys_job_type'=>'Moving','ppl_people.deleted'=>'N'])
            ->where('ppl_people.tenant_id', auth()->user()->tenant_id)
            ->join('sys_modules', 'ppl_people.sys_job_type', '=', 'sys_modules.id')            
            ->select(DB::raw("CONCAT(ppl_people.first_name,' ',ppl_people.last_name) AS name,ppl_people.id as id"))
            ->orderBy('ppl_people.first_name', 'ASC');
        return $users->get();
    }

    public static function allAdmins($exceptId = NULL)
    {
        $users = User::join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email', 'users.created_at')
            ->where('roles.name', 'admin');

        if (!is_null($exceptId)) {
            $users->where('users.id', '<>', $exceptId);
        }

        return $users->get();
    }

    public static function teamUsers($teamId)
    {
        $users = User::join('employee_teams', 'employee_teams.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'users.email', 'users.created_at')
            ->where('employee_teams.team_id', $teamId);

        return $users->get();
    }

    public static function userListLatest($userID, $term)
    {

        if ($term) {
            $termCnd = "and users.name like '%$term%'";
        } else {
            $termCnd = '';
        }

        $messageSetting = message_setting();

        if (auth()->user()->hasRole('admin')) {
            if ($messageSetting->allow_client_admin == 'no') {
                $termCnd .= "and roles.name != 'client'";
            }
        } elseif (auth()->user()->hasRole('employee')) {
            if ($messageSetting->allow_client_employee == 'no') {
                $termCnd .= "and roles.name != 'client'";
            }
        } elseif (auth()->user()->hasRole('client')) {
            if ($messageSetting->allow_client_admin == 'no') {
                $termCnd .= "and roles.name != 'admin'";
            }
            if ($messageSetting->allow_client_employee == 'no') {
                $termCnd .= "and roles.name != 'employee'";
            }
        }

        $query = DB::select("SELECT * FROM ( SELECT * FROM (
                    SELECT users.id,'0' AS groupId, users.name,  users.image,  users_chat.created_at as last_message, users_chat.message, users_chat.message_seen, users_chat.user_one
                    FROM users
                    INNER JOIN users_chat ON users_chat.from = users.id
                    LEFT JOIN role_user ON role_user.user_id = users.id
                    LEFT JOIN roles ON roles.id = role_user.role_id
                    WHERE users_chat.to = $userID $termCnd
                    UNION
                    SELECT users.id,'0' AS groupId, users.name,users.image, users_chat.created_at  as last_message, users_chat.message, users_chat.message_seen, users_chat.user_one
                    FROM users
                    INNER JOIN users_chat ON users_chat.to = users.id
                    LEFT JOIN role_user ON role_user.user_id = users.id
                    LEFT JOIN roles ON roles.id = role_user.role_id
                    WHERE users_chat.from = $userID  $termCnd
                    ) AS allUsers
                    ORDER BY  last_message DESC
                    ) AS allUsersSorted
                    GROUP BY id
                    ORDER BY  last_message DESC");

        return $query;
    }

    public static function isAdmin($userId)
    {
        $user = User::find($userId);
        if ($user) {
            return $user->hasRole('admin') ? true : false;
        }
        return false;
    }

    public static function isClient($userId)
    {
        $user = User::find($userId);
        if ($user) {
            return $user->hasRole('client') ? true : false;
        }
        return false;

    }

    public static function isEmployee($userId)
    {
        $user = User::find($userId);
        if ($user) {
            return $user->hasRole('employee') ? true : false;
        }
        return false;
    }

    public function getModulesAttribute()
    {
        return user_modules();
    }

    public function sticky()
    {
        return $this->hasMany(StickyNote::class, 'user_id')->orderBy('updated_at', 'desc');
    }

    public function user_chat()
    {
        return $this->hasMany(UserChat::class, 'to')->where('message_seen', 'no');
    }

    public function employee_details()
    {
        return $this->hasOne(EmployeeDetails::class);
    }
}
