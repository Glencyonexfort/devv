<?php
namespace App\Http\Controllers\Admin;
use App\AttendanceSetting;
use Illuminate\Http\Request;
use App\Helper\Reply;
use App\Currency;
use App\Invoice;
use App\JobsMovingLogs;
use App\LeadFollowUp;
use App\Leave;
use App\LogTimeFor;
use App\Payment;
use App\Project;
use App\ProjectActivity;
use App\ProjectTimeLog;
use App\Task;
use App\TaskboardColumn;
use App\Ticket;
use App\Traits\CurrencyExchange;
use App\UserActivity;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
class AdminDashboardController extends AdminBaseController
{
    use CurrencyExchange;
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.dashboard');
        $this->pageIcon = 'icon-speedometer';
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /// Redirect to Inbox for default home page
        return redirect(route('admin.inbox'));
        ///
        $this->versionUpdate();
        $taskBoardColumn = TaskboardColumn::where(['tenant_id' => auth()->user()->tenant_id])->get();
        $incompletedTaskColumn = $taskBoardColumn->filter(function ($value, $key) {
            return $value->slug == 'incomplete';
        })->first();
        $completedTaskColumn = $taskBoardColumn->filter(function ($value, $key) {
            return $value->slug == 'completed';
        })->first();
        // Getting Attendance setting data
        $this->attendanceSettings = AttendanceSetting::where(['tenant_id' => auth()->user()->tenant_id])->first();
        //Getting Maximum Check-ins in a day
        $this->maxAttandenceInDay = $this->attendanceSettings->clockin_in_day;
        $this->counts = DB::table('users')
            ->select(
                DB::raw('(select count(users.id) from `users` inner join role_user on role_user.user_id=users.id inner join roles on roles.id=role_user.role_id WHERE roles.name = "client" and users.status = "active") as totalClients'),
                DB::raw('(select count(users.id) from `users` inner join role_user on role_user.user_id=users.id inner join roles on roles.id=role_user.role_id WHERE roles.name = "employee" and users.status = "active") as totalEmployees'),
                DB::raw('(select count(projects.id) from `projects`) as totalProjects'),
                DB::raw('(select sum(project_time_logs.total_minutes) from `project_time_logs`) as totalHoursLogged'),
                DB::raw('(select count(tasks.id) from `tasks` where tasks.board_column_id='.$completedTaskColumn->id.') as totalCompletedTasks'),
                DB::raw('(select count(tasks.id) from `tasks` where tasks.board_column_id='.$incompletedTaskColumn->id.') as totalPendingTasks'),
                DB::raw('(select count(attendances.id) from `attendances` inner join users as atd_user on atd_user.id=attendances.user_id where DATE(attendances.clock_in_time) = CURDATE() and atd_user.status = "active") as totalTodayAttendance'),
                //                DB::raw('(select count(issues.id) from `issues` where status="pending") as totalPendingIssues'),
                DB::raw('(select count(tickets.id) from `tickets` where (status="open" or status="pending")) as totalUnResolvedTickets'),
                DB::raw('(select count(tickets.id) from `tickets` where (status="resolved" or status="closed")) as totalResolvedTickets')
            )
            ->first();
        $timeLog = intdiv($this->counts->totalHoursLogged, 60) . ' hrs ';
        if (($this->counts->totalHoursLogged % 60) > 0) {
            $timeLog .= ($this->counts->totalHoursLogged % 60) . ' mins';
        }
        $this->counts->totalHoursLogged = $timeLog;
        $pament_last_month = Payment::where('tenant_id', '=', auth()->user()->tenant_id)
            ->whereMonth('paid_on', date('m', strtotime('-1 months')))
            ->sum('amount');
        $this->counts->paymentLastMonth = $pament_last_month;
        $pament_this_month = Payment::where('tenant_id', '=', auth()->user()->tenant_id)
            ->whereMonth('paid_on', date('m'))
            ->sum('amount');
        $this->counts->paymentThisMonth = $pament_this_month;
        $total_unpaid_invoices = Invoice::where('status', '<>', 'paid')
            ->where('tenant_id', '=', auth()->user()->tenant_id)
            ->count();
        $this->counts->totalUnpaidInvoices = $total_unpaid_invoices;
        $unread_emails = JobsMovingLogs::leftJoin('jobs_moving', 'jobs_moving.job_id', '=', 'jobs_moving_logs.job_id')
            ->select('jobs_moving.job_id', 'jobs_moving.job_number', 'jobs_moving_logs.id', 'jobs_moving_logs.email_from', 'jobs_moving_logs.email_subject', 'jobs_moving_logs.log_date')
            ->where(['jobs_moving_logs.log_type_id' => 5, 'jobs_moving_logs.tenant_id' => auth()->user()->tenant_id])
            ->where('jobs_moving_logs.email_status', 'Unread')
            ->orderBy('jobs_moving_logs.log_date', 'desc')
            ->get();
        $this->unreadEmails = $unread_emails;
        $this->pendingTasks = Task::with('project')
            ->where(['tasks.board_column_id' => $incompletedTaskColumn->id, 'tenant_id' => auth()->user()->tenant_id])
            ->where(DB::raw('DATE(due_date)'), '<=', Carbon::today()->format('Y-m-d'))
            ->orderBy('due_date', 'desc')
            ->get();
        $this->pendingLeadFollowUps = LeadFollowUp::with('lead')->where(DB::raw('DATE(next_follow_up_date)'), '<=', Carbon::today()->format('Y-m-d'))
            ->join('leads', 'leads.id', 'lead_follow_up.lead_id')
            ->where(['leads.next_follow_up' => 'yes', 'tenant_id' => auth()->user()->tenant_id])
            ->get();
        $this->newTickets = Ticket::where(['status' => 'open', 'tenant_id' => auth()->user()->tenant_id])
            ->orderBy('id', 'desc')->get();
        $this->projectActivities = ProjectActivity::with('project')->where(['project_activity.tenant_id' => auth()->user()->tenant_id])
            ->join('projects', 'projects.id', '=', 'project_activity.project_id')
            ->whereNull('projects.deleted_at')->select('project_activity.*')
            ->limit(15)->orderBy('id', 'desc')->get();
        $this->userActivities = UserActivity::with('user')->where(['tenant_id' => auth()->user()->tenant_id])->limit(15)->orderBy('id', 'desc')->get();
        $this->feedbacks = Project::with('client')->where(['tenant_id' => auth()->user()->tenant_id])->whereNotNull('feedback')->limit(5)->get();
        $locale = strtolower($this->global->locale);
        $darSkyLangs = array('ar', 'az', 'be', 'bg', 'bs', 'ca', 'cs', 'da', 'de', 'el', 'en', 'es', 'et', 'fi', 'fr', 'he', 'hr', 'hu', 'id', 'is', 'it', 'ja', 'ka', 'ko', 'kw', 'lv', 'nb', 'nl', 'no', 'pl', 'pt', 'ro', 'ru', 'sk', 'sl', 'sr', 'sv', 'tet', 'tr', 'uk', 'x-pig-latin', 'zh', 'zh-tw');
        if (!in_array($locale, $darSkyLangs)) {
            $locale = 'en';
        }
        if (!is_null($this->global->latitude)) {
            // // get current weather
            // $client = new Client();
            // $res = $client->request('GET', 'https://api.darksky.net/forecast/'.$this->global->weather_key.'/' . $this->global->latitude . ',' . $this->global->longitude . '?units=auto&exclude=minutely,daily&lang=' . $locale, ['verify' => false]);
            // $weather = $res->getBody();
            // $this->weather = json_decode($weather, true);
        }
        // earning chart
        $this->currencies = Currency::all();
        $this->currentCurrencyId = $this->global->currency_id;
        $this->fromDate = Carbon::today()->timezone($this->global->timezone)->subDays(60);
        $this->toDate = Carbon::today()->timezone($this->global->timezone);
        $invoices = DB::table('payments')
            ->join('currencies', 'currencies.id', '=', 'payments.currency_id')
            ->where('paid_on', '>=', $this->fromDate)
            ->where('paid_on', '<=', $this->toDate)
            ->where(['payments.status' => 'complete', 'tenant_id' => auth()->user()->tenant_id])
            ->groupBy('paid_on')
            ->orderBy('paid_on', 'ASC')
            ->get([
                DB::raw('DATE_FORMAT(paid_on,"%Y-%m-%d") as date'),
                DB::raw('sum(amount) as total'),
                'currencies.currency_code',
                'currencies.is_cryptocurrency',
                'currencies.usd_price',
                'currencies.exchange_rate'
            ]);
        $chartData = array();
        foreach ($invoices as $chart) {
            if ($chart->currency_code != $this->global->currency->currency_code) {
                if ($chart->is_cryptocurrency == 'yes') {
                    if ($chart->exchange_rate == 0) {
                        if ($this->updateExchangeRates()) {
                            $usdTotal = ($chart->total * $chart->usd_price);
                            $chartData[] = ['date' => $chart->date, 'total' => floor($usdTotal / $chart->exchange_rate)];
                        }
                    } else {
                        $usdTotal = ($chart->total * $chart->usd_price);
                        $chartData[] = ['date' => $chart->date, 'total' => floor($usdTotal / $chart->exchange_rate)];
                    }
                } else {
                    if ($chart->exchange_rate == 0) {
                        if ($this->updateExchangeRates()) {
                            $chartData[] = ['date' => $chart->date, 'total' => floor($chart->total / $chart->exchange_rate)];
                        }
                    } else {
                        $chartData[] = ['date' => $chart->date, 'total' => floor($chart->total / $chart->exchange_rate)];
                    }
                }
            } else {
                $chartData[] = ['date' => $chart->date, 'total' => round($chart->total, 2)];
            }
        }
        $this->chartData = json_encode($chartData);
        $this->leaves = Leave::with('user','type')->where('status', '<>', 'rejected')->where(['tenant_id' => auth()->user()->tenant_id])->get();
        $this->logTimeFor = LogTimeFor::where(['tenant_id' => auth()->user()->tenant_id])->first();
        $this->activeTimerCount = ProjectTimeLog::with('user')->where(['tenant_id' => auth()->user()->tenant_id])
            ->whereNull('end_time')
            ->join('users', 'users.id', '=', 'project_time_logs.user_id');
        if ($this->logTimeFor != null && $this->logTimeFor->log_time_for == 'task') {
            $this->activeTimerCount = $this->activeTimerCount->join('tasks', 'tasks.id', '=', 'project_time_logs.task_id');
            $projectName = 'tasks.heading as project_name';
        } else {
            $this->activeTimerCount = $this->activeTimerCount->join('projects', 'projects.id', '=', 'project_time_logs.project_id');
            $projectName = 'projects.project_name';
        }
        $this->activeTimerCount = $this->activeTimerCount
            ->select('project_time_logs.*', $projectName, 'users.name')
            ->count();
        return view('admin.dashboard.index', $this->data);
    }
    public function setEmailAsRead(Request $request)
    {
        $id = $request->id;
        $jobs_moving_logs = JobsMovingLogs::find($id);
        if($jobs_moving_logs){
            if($jobs_moving_logs->email_status == 'Unread'){
                $jobs_moving_logs->email_status = 'Read';
                $jobs_moving_logs->save();
            }
        }
        return Reply::success(__('messages.messageRead'));
    }
    private function versionUpdate()
    {
        try {
            $client = new Client();
            $res = $client->request('GET', config('froiden_envato.updater_file_path'), ['verify' => false]);
            $lastVersion = $res->getBody();
            $lastVersion = json_decode($lastVersion, true);
            if ($lastVersion['version'] > File::get('version.txt')) {
                $this->lastVersion = $lastVersion['version'];
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}