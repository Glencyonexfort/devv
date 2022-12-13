<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\JobsMoving;
use App\Companies;
use App\CRMContacts;
use App\CRMLeads;
use App\JobsMovingLogs;
use App\Lists;
use App\EmployeeDetails;
use App\Vehicles;
use App\Http\Requests\Admin\User\StoreUser;
use App\Http\Requests\Admin\User\UpdateDriver;
use App\Customers;
use App\Role;
use App\JobsMovingLegs;
use App\JobTemplatesMoving;
use App\Invoice;
use App\InvoiceItems;
use App\User;
use App\Http\Requests\ListJobs\StoreNewJob;
use App\JobsCleaningTeamRoster;
use App\JobsCleaningTeams;
use App\JobsCleaningType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Hash;

class TeamRosterController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.team_roster');
        $this->pageIcon = 'ti-file';
    }

    public function index()
    {
        $this->job_status = Lists::job_status();
        $this->payment_status = Lists::payment_status();
        $this->cleaning_job_types = JobsCleaningType::get();
        $this->cleaning_teams = JobsCleaningTeams::where('tenant_id', '=', auth()->user()->tenant_id)->where('active', '=', 'Y')->get();
        return view('admin.team-roster-cleaning.index', $this->data);
    }

    public function data(Request $request)
    {
        try {
            $result = JobsCleaningTeamRoster::select(
                'jobs_cleaning_team_roster.id',
                'jobs_cleaning_team_roster.job_id',
                'jobs_cleaning.job_number',
                'jobs_cleaning_team_roster.job_date as job_date',
                'jobs_cleaning_shifts.shift_name',
                'crm_contacts.name',
                'jobs_cleaning.address',
                'sys_cleaning_job_types.job_type_name'
            )
                ->leftjoin('jobs_cleaning', 'jobs_cleaning.job_id', 'jobs_cleaning_team_roster.job_id')
                ->leftjoin('jobs_cleaning_shifts', 'jobs_cleaning_shifts.id', 'jobs_cleaning_team_roster.job_shift_id')
                ->leftjoin('crm_contacts', 'crm_contacts.id', 'jobs_cleaning.customer_id')
                ->leftjoin('sys_cleaning_job_types', 'sys_cleaning_job_types.id', 'jobs_cleaning_team_roster.job_type_id');


            $result = $result->where('jobs_cleaning_team_roster.tenant_id', '=', auth()->user()->tenant_id);
            // $result = $result->where('jobs_cleaning_team_roster.job_date', '=', Carbon::today())
            //     ->orderBy('jobs_cleaning_team_roster.job_date', 'asc')
            //     ->orderBy('jobs_cleaning_team_roster.job_shift_id', 'asc')
            //     ->orderBy('jobs_cleaning_team_roster.team_id', 'asc');

                // echo $request->created_date_start;exit;
            if ($request->created_date_start !== null && $request->created_date_start != 'null' && $request->created_date_start != '') {
                $startDate = Carbon::createFromFormat($this->global->date_format, $request->created_date_start)->toDateString();
                $result = $result->where(DB::raw('DATE(jobs_cleaning_team_roster.`job_date`)'), '>=', $startDate);
            }
            if ($request->created_date_end !== null && $request->created_date_end != 'null' && $request->created_date_end != '') {
                $created_date_end = Carbon::createFromFormat($this->global->date_format, $request->created_date_end)->toDateString();
                $result = $result->where(DB::raw('DATE(jobs_cleaning_team_roster.`job_date`)'), '<=', $created_date_end);
            }
            if ($request->job_type_id !== null && $request->job_type_id != 'null' && $request->job_type_id != '') {
                $job_type_id = explode(",", $request->job_type_id);
                $result = $result->wherein('jobs_cleaning_team_roster.job_type_id', $job_type_id);
            }
            if ($request->team_id !== null && $request->team_id != 'null' && $request->team_id != '') {
                $team_id = explode(",", $request->team_id);
                $result = $result->wherein('jobs_cleaning_team_roster.team_id', $team_id);
            }

            // if ($request->hide_deleted_archived !== null && $request->hide_deleted_archived != 'null' && $request->hide_deleted_archived == '1') {
            //     $result = $result->where('jobs_cleaning_team_roster.deleted', '=', '0');
            // }
            $result = $result->get();
            return DataTables::of($result)
                ->addColumn('job_date', function ($row) {
                    return date('d/m/Y', strtotime($row->job_date));
                })
                ->editColumn('job_number', function ($row) {
                    return '<a class="badge bg-blue" href="' . route("admin.list-jobs-cleaning.view-job", $row->job_id) . '" >' . $row->job_number . '</a>';
                })
                // ->addColumn('action', function ($row) {
                //     return '<a class="sa-params badge badge-danger" href="javascript:;" data-row-id="' . $row->job_id . '"><i class="fa fa-trash"></i></a>';
                // })
                ->rawColumns(['action', 'job_number'])
                // ->removeColumns(['id', 'job_id'])
                ->make(true);
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }
}
