<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\LogTimeFor;
use App\Project;
use App\ProjectTimeLog;
use App\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimeLogReportController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.timeLogReport');
        $this->pageIcon = 'ti-pie-chart';
        $this->logTimeFor = LogTimeFor::where(['tenant_id' => auth()->user()->tenant_id])->first();
    }

    public function index()
    {

        $this->projects = Project::where(['tenant_id' => auth()->user()->tenant_id])->get();
        $this->tasks = Task::where(['tenant_id' => auth()->user()->tenant_id])->get();
        $this->fromDate = Carbon::today()->subDays(30);
        $this->toDate = Carbon::today();

        $this->chartData = DB::table('project_time_logs');

        if($this->logTimeFor != null && $this->logTimeFor->log_time_for == 'task'){
            $this->chartData = $this->chartData->join('tasks', 'tasks.id', '=', 'project_time_logs.task_id');
        }else{
            $this->chartData = $this->chartData->join('projects', 'projects.id', '=', 'project_time_logs.project_id');
        }
        $this->chartData = $this->chartData->where('start_time', '>=', $this->fromDate)
            ->where('start_time', '<=', $this->toDate)
            ->where(['project_time_logs.tenant_id' => auth()->user()->tenant_id])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get([
                DB::raw('DATE_FORMAT(start_time,\'%d/%M/%y\') as date'),
                DB::raw('FLOOR(sum(total_minutes/60)) as total_hours')
            ])
        ->toJSON();

        return view('admin.reports.time-log.index', $this->data);
    }

    public function store(Request $request)
    {
        $fromDate = Carbon::createFromFormat($this->global->date_format, $request->startDate)->toDateString();
        $toDate = Carbon::createFromFormat($this->global->date_format, $request->endDate)->toDateString();
        $projectId = $request->projectId;

        $timeLog = ProjectTimeLog::select('start_time', DB::raw('DATE_FORMAT(start_time,\'%d/%M/%y\') as date'), DB::raw('FLOOR(sum(total_minutes/60)) as total_hours'))
            ->whereDate('start_time', '>=', $fromDate)
            ->whereDate('start_time', '<=', $toDate)
            ->where(['tenant_id' => auth()->user()->tenant_id]);

        if (!is_null($projectId)) {
            if ($this->logTimeFor != null && $this->logTimeFor->log_time_for == 'task') {
                $timeLog =  $timeLog->where('project_time_logs.task_id', '=', $projectId);
            } else {
                $timeLog =  $timeLog->where('project_time_logs.project_id', '=', $projectId);
            }
        }

        $timeLog = $timeLog->groupBy('date')
            ->orderBy('start_time', 'ASC')
            ->get()
            ->toJson();

        if (empty($timeLog)) {
            return Reply::error('No record found.');
        }
        return Reply::successWithData(__('messages.reportGenerated'), ['chartData' => $timeLog]);
    }
}
