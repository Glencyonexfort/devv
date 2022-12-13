<?php

namespace App\Http\Controllers\Admin;

use App\Companies;
use App\CRMActivityLog;
use App\CRMLeadStatuses;
use App\CRMOpStatusLog;
use App\DailyDriverVehicleCheck;
use App\DailyDriverVehicleCheckDetails;
use App\InvoiceItems;
use App\JobsMoving;
use App\JobsMovingLegs;
use App\JobsMovingLegsTeam;
use App\JobsMovingStatusLog;
use App\PplPeople;
use App\User;
use App\VehicleChecklistGroup;
use App\VehicleChecklistDefinition;
use App\Vehicles;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.report.salesReport');
        $this->pageIcon = 'icon-graph';
    }

    public function index()
    {
        $this->from_date = Carbon::parse(Carbon::now()->subMonth())->format($this->global->date_format);
        $this->to_date = Carbon::parse(Carbon::now())->format($this->global->date_format);
        // $this->lead_statuses = CRMLeadStatuses::select('id', 'lead_status')->where('tenant_id', '=', auth()->user()->tenant_id)->orderBy('sort_order', 'asc')->get();

        $from_date = Carbon::createFromFormat($this->global->date_format, $this->from_date)->toDateString();
        $to_date = Carbon::createFromFormat($this->global->date_format, $this->to_date)->toDateString();

        $users = User::allPeopleWithSystemUsersWithNoDriver();
        $reports = null;
        for ($i = 0; $i < count($users); $i++) 
        {
            $user = User::find($users[$i]->user_id);

           if($user)
           {
                $reports[$i]['name'] = $user->name;

                $reports[$i]['QuotesCreated'] = CRMOpStatusLog::where('crm_opportunities.user_id', $user->id)
                    ->join('crm_opportunities', 'crm_opportunities.id', '=', 'crm_op_status_log.crm_opportunity_id')
                    ->where('crm_op_status_log.new_status', 'New')
                    ->whereBetween('crm_op_status_log.created_at', [$from_date, $to_date])
                    ->count();

                $reports[$i]['jobsConfirmed'] = JobsMovingStatusLog::where(['jobs_moving_status_log.new_status'=>'New', 'crm_opportunities.user_id'=>$user->id])
                    ->join('jobs_moving', 'jobs_moving.job_id', '=', 'jobs_moving_status_log.job_id')
                    ->join('crm_opportunities', 'crm_opportunities.id', '=', 'jobs_moving.crm_opportunity_id')
                    ->whereBetween('jobs_moving_status_log.created_at', [$from_date, $to_date])
                    ->count();

                $reports[$i]['QuotesLost'] = CRMOpStatusLog::where('crm_opportunities.user_id', $user->id)
                    ->join('crm_opportunities', 'crm_opportunities.id', '=', 'crm_op_status_log.crm_opportunity_id')
                    ->where('crm_op_status_log.new_status', 'Lost')
                    ->whereBetween('crm_op_status_log.created_at', [$from_date, $to_date])
                    ->count();

                $reports[$i]['emailSend'] = CRMActivityLog::where('user_id', '=', $user->id)
                    ->where('log_type', 3)
                    ->whereBetween('log_date', [$from_date, $to_date])
                    ->count();

                $sales = InvoiceItems::where('crm_opportunities.user_id', '=', $user->id)
                    ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
                    ->join('jobs_moving', 'jobs_moving.job_id', '=', 'invoices.job_id')
                    ->join('crm_opportunities', 'crm_opportunities.id', '=', 'jobs_moving.crm_opportunity_id')
                    ->whereBetween('invoice_items.created_at', [$from_date, $to_date])
                    ->sum('invoice_items.amount');
                    
                $reports[$i]['totalSales'] = $this->global->currency_symbol.''.number_format((float) $sales, 2, '.', ',');
           }
        }
        $this->users = $users;
        $this->reports = $reports;
        return view('admin.reports.sales-report.index', $this->data);
    }

    public function getdata(Request $request)
    {
        if($request->user_id)
        {
            $user = User::find($request->user_id);
            $from_date = Carbon::createFromFormat('d/m/Y', $request->created_date_start)->format('Y-m-d');
            $to_date = Carbon::createFromFormat('d/m/Y', $request->created_date_end)->format('Y-m-d h:i:s');
    
            $quotesCreated = CRMOpStatusLog::where('crm_opportunities.user_id', $user->id)
            ->join('crm_opportunities', 'crm_opportunities.id', '=', 'crm_op_status_log.crm_opportunity_id')
            ->where('crm_op_status_log.new_status', 'New')
            ->whereBetween('crm_op_status_log.created_at', [$from_date, $to_date])
            ->count();

            $jobsConfirmed = JobsMovingStatusLog::where(['jobs_moving_status_log.new_status'=>'New', 'crm_opportunities.user_id'=>$user->id])
                    ->join('jobs_moving', 'jobs_moving.job_id', '=', 'jobs_moving_status_log.job_id')
                    ->join('crm_opportunities', 'crm_opportunities.id', '=', 'jobs_moving.crm_opportunity_id')
                    ->whereBetween('jobs_moving_status_log.created_at', [$from_date, $to_date])
                    ->count();
    
            $quotesLost = CRMOpStatusLog::where('crm_opportunities.user_id', $user->id)
            ->join('crm_opportunities', 'crm_opportunities.id', '=', 'crm_op_status_log.crm_opportunity_id')
            ->where('crm_op_status_log.new_status', 'Lost')
            ->whereBetween('crm_op_status_log.created_at', [$from_date, $to_date])
            ->count();
    
            $emailSend = CRMActivityLog::where('user_id', '=', $user->id)
            ->where('log_type', 3)
            ->whereBetween('log_date', [$from_date, $to_date])
            ->count();
    
            $totalSales = InvoiceItems::where('crm_opportunities.user_id', '=', $user->id)
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->join('jobs_moving', 'jobs_moving.job_id', '=', 'invoices.job_id')
            ->join('crm_opportunities', 'crm_opportunities.id', '=', 'jobs_moving.crm_opportunity_id')
            ->whereBetween('invoice_items.created_at', [$from_date, $to_date])
            ->sum('invoice_items.amount');

            return response()->json([
                'success' => 1,
                'quotesCreated' => $quotesCreated,
                'jobsConfirmed' => $jobsConfirmed,
                'quotesLost' => $quotesLost,
                'emailSend' => $emailSend,
                'totalSales' => number_format((float) $totalSales, 2, '.', ''),
                'user' => $user->name,
            ], 200);
        }
        else
        {
            $from_date = Carbon::createFromFormat('d/m/Y', $request->created_date_start)->format('Y-m-d');
            $to_date = Carbon::createFromFormat('d/m/Y', $request->created_date_end)->format('Y-m-d h:i:s');

            $users = User::allPeopleWithSystemUsers();
            $reports = null;
            for ($i = 0; $i < count($users); $i++) {
                $user = User::findOrFail($users[$i]->user_id);

                $reports[$i]['name'] = $user->name;

                $reports[$i]['QuotesCreated'] = CRMOpStatusLog::where('crm_opportunities.user_id', $user->id)
                    ->join('crm_opportunities', 'crm_opportunities.id', '=', 'crm_op_status_log.crm_opportunity_id')
                    ->where('crm_op_status_log.new_status', 'New')
                    ->whereBetween('crm_op_status_log.created_at', [$from_date, $to_date])
                    ->count();

                $reports[$i]['jobsConfirmed'] = JobsMovingStatusLog::where(['jobs_moving_status_log.new_status'=>'New', 'crm_opportunities.user_id'=>$user->id])
                    ->join('jobs_moving', 'jobs_moving.job_id', '=', 'jobs_moving_status_log.job_id')
                    ->join('crm_opportunities', 'crm_opportunities.id', '=', 'jobs_moving.crm_opportunity_id')
                    ->whereBetween('jobs_moving_status_log.created_at', [$from_date, $to_date])
                    ->count();                

                $reports[$i]['QuotesLost'] = CRMOpStatusLog::where('crm_opportunities.user_id', $user->id)
                    ->join('crm_opportunities', 'crm_opportunities.id', '=', 'crm_op_status_log.crm_opportunity_id')
                    ->where('crm_op_status_log.new_status', 'Lost')
                    ->whereBetween('crm_op_status_log.created_at', [$from_date, $to_date])
                    ->count();

                $reports[$i]['emailSend'] = CRMActivityLog::where('user_id', '=', $user->id)
                    ->where('log_type', 3)
                    ->whereBetween('log_date', [$from_date, $to_date])
                    ->count();

                $sales = InvoiceItems::where('crm_opportunities.user_id', '=', $user->id)
                    ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
                    ->join('jobs_moving', 'jobs_moving.job_id', '=', 'invoices.job_id')
                    ->join('crm_opportunities', 'crm_opportunities.id', '=', 'jobs_moving.crm_opportunity_id')
                    ->whereBetween('invoice_items.created_at', [$from_date, $to_date])
                    ->sum('invoice_items.amount');                    
                    
                $reports[$i]['totalSales'] = $this->global->currency_symbol.''.number_format((float) $sales, 2, '.', ',');
            }
            $this->reports = $reports;

            return response()->json([
                'success' => 2,
                'reports' => $this->reports
            ], 201);
        }
    }

    public function operationsReport()
    {
        $this->pageTitle = __('app.report.operationsReport');
        $this->pageIcon = 'icon-graph';

        $this->from_date = Carbon::parse(Carbon::now()->subMonth())->format($this->global->date_format);
        $this->to_date = Carbon::parse(Carbon::now())->format($this->global->date_format);
        $this->lead_statuses = CRMLeadStatuses::select('id', 'lead_status')->where('tenant_id', '=', auth()->user()->tenant_id)->orderBy('sort_order', 'asc')->get();

        $from_date = Carbon::createFromFormat($this->global->date_format, $this->from_date)->toDateString();
        $to_date = Carbon::createFromFormat($this->global->date_format, $this->to_date)->toDateString();

        $users = User::allPeople();
        $reports = null;
        for ($i = 0; $i < count($users); $i++) 
        {
            // $numberOfJobs = JobsMovingLegs::where('driver_id', $users[$i]->id)
            //                         ->whereBetween('created_at', [$from_date, $to_date])
            //                         ->count();
            $numberOfJobs = JobsMovingLegsTeam::where(['jobs_moving_legs_team.tenant_id' => auth()->user()->tenant_id, 'jobs_moving_legs_team.people_id' => $users[$i]->id])
                                    ->leftjoin('jobs_moving_legs', 'jobs_moving_legs.id', 'jobs_moving_legs_team.leg_id')
                                    ->whereDate('jobs_moving_legs.leg_date', '>=', $from_date)
                                    ->whereDate('jobs_moving_legs.leg_date', '<=', $to_date)
                                    ->count();
            
            if($numberOfJobs >= 1)
            {
                $reports[$i]['name'] = $users[$i]->name;

                $reports[$i]['NumberOfJobs'] = $numberOfJobs;

                // $times = JobsMovingLegs::where('driver_id', $users[$i]->id)
                //                 ->whereBetween('created_at', [$from_date, $to_date])
                //                 ->get();
                $times = JobsMovingLegsTeam::where(['jobs_moving_legs_team.tenant_id' => auth()->user()->tenant_id, 'jobs_moving_legs_team.people_id' => $users[$i]->id])
                                ->leftjoin('jobs_moving_legs', 'jobs_moving_legs.id', 'jobs_moving_legs_team.leg_id')
                                ->whereDate('jobs_moving_legs.leg_date', '>=', $from_date)
                                ->whereDate('jobs_moving_legs.leg_date', '<=', $to_date)
                                ->get();
                $sum = 0;
                for ($j = 0; $j < count($times); $j++) 
                {
                    if ($times[$j]->actual_start_time != null && $times[$j]->actual_finish_time != null) 
                    {
                        $start = Carbon::parse($times[$j]->actual_start_time);
                        $end = Carbon::parse($times[$j]->actual_finish_time);
                        $totalDuration = 0;
                        $totalDuration = $start->diffInMinutes($end);
                        $sum +=  $this->minutesIntoTime(intval($totalDuration));
                    }
                }
                $reports[$i]['Hours'] = number_format((float)$sum, 2, '.', '');;
            }
        }

        $this->users = $users;
        $this->reports = $reports;
        return view('admin.reports.operations-report.index', $this->data);
    }

    public function operationsData(Request $request)
    {
        if($request->user_id!="")
        {
            $success=0;
            $this->people = DB::table('ppl_people')->where('id', $request->user_id)->first();
            $startDate = Carbon::createFromFormat('d/m/Y', $request->created_date_start)->format('Y-m-d');
            $endDate = Carbon::createFromFormat('d/m/Y', $request->created_date_end)->format('Y-m-d');
            
            $reports = null;
            // $numberOfJobs = JobsMovingLegs::where('driver_id', $request->user_id)
            //                                         ->whereBetween('created_at', [$startDate, $endDate])
            //                                         ->count();
            $numberOfJobs = JobsMovingLegsTeam::where(['jobs_moving_legs_team.tenant_id' => auth()->user()->tenant_id, 'jobs_moving_legs_team.people_id' => $request->user_id])
                        ->leftjoin('jobs_moving_legs', 'jobs_moving_legs.id', 'jobs_moving_legs_team.leg_id')
                        ->whereDate('jobs_moving_legs.leg_date', '>=', $startDate)
                        ->whereDate('jobs_moving_legs.leg_date', '<=', $endDate)
                        ->count();
                                                            
            if($numberOfJobs > 0)
            {
                $reports['name'] = $this->people->first_name.' '.$this->people->last_name;
                $reports['NumberOfJobs'] = $numberOfJobs;

                // $times = JobsMovingLegs::where('driver_id', $request->user_id)
                //                 ->whereBetween('created_at', [$startDate, $endDate])
                //                 ->get();
                $times = JobsMovingLegsTeam::where(['jobs_moving_legs_team.tenant_id' => auth()->user()->tenant_id, 'jobs_moving_legs_team.people_id' => $request->user_id])
                                ->leftjoin('jobs_moving_legs', 'jobs_moving_legs.id', 'jobs_moving_legs_team.leg_id')
                                ->whereDate('jobs_moving_legs.leg_date', '>=', $startDate)
                                ->whereDate('jobs_moving_legs.leg_date', '<=', $endDate)
                                ->get();
                
                $sum = 0;
                for ($j = 0; $j < count($times); $j++) 
                {

                    if ($times[$j]->actual_start_time != null && $times[$j]->actual_finish_time != null) 
                    {

                        $start = Carbon::parse($times[$j]->actual_start_time);
                        $end = Carbon::parse($times[$j]->actual_finish_time);
                        $totalDuration = 0;
                        $totalDuration = $start->diffInMinutes($end);
                        $sum +=  $this->minutesIntoTime(intval($totalDuration));
                    }
                }
                $reports['Hours'] = number_format((float)$sum, 2, '.', '');;
                $success=1;
            }

            return response()->json([
                'success' => $success,
                'NumberOfJobs' => $reports['NumberOfJobs'],
                'Hours' => $reports['Hours'],
                'user' => $reports['name'],
            ], 201);
        }
        else
        {
            $success=0;
            $users = User::allPeople();
            $startDate = Carbon::createFromFormat('d/m/Y', $request->created_date_start)->format('Y-m-d');
            $endDate = Carbon::createFromFormat('d/m/Y', $request->created_date_end)->format('Y-m-d');

            $reports = null;
            for ($i = 0; $i < count($users); $i++) 
            {
                
    
                // $numberOfJobs = JobsMovingLegs::where('driver_id', $users[$i]->id)
                //                                             ->whereBetween('created_at', [$startDate, $endDate])
                //                                             ->count();
                $numberOfJobs = JobsMovingLegsTeam::where(['jobs_moving_legs_team.tenant_id' => auth()->user()->tenant_id, 'jobs_moving_legs_team.people_id' => $users[$i]->id])
                                                ->leftjoin('jobs_moving_legs', 'jobs_moving_legs.id', 'jobs_moving_legs_team.leg_id')
                                                ->whereDate('jobs_moving_legs.leg_date', '>=', $startDate)
                                                ->whereDate('jobs_moving_legs.leg_date', '<=', $endDate)
                                                ->count();
                
                if($numberOfJobs >= 1)
                {
                    $reports[$i]['name'] = $users[$i]->name;
                    $reports[$i]['NumberOfJobs'] = $numberOfJobs;
                    // $times =JobsMovingLegs::where('driver_id', $users[$i]->id)
                    //     ->whereBetween('created_at', [$startDate, $endDate])
                    //     ->get();
                    $times = JobsMovingLegsTeam::where(['jobs_moving_legs_team.tenant_id' => auth()->user()->tenant_id, 'jobs_moving_legs_team.people_id' => $users[$i]->id])
                                        ->leftjoin('jobs_moving_legs', 'jobs_moving_legs.id', 'jobs_moving_legs_team.leg_id')
                                        ->whereDate('jobs_moving_legs.leg_date', '>=', $startDate)
                                        ->whereDate('jobs_moving_legs.leg_date', '<=', $endDate)
                                        ->get();

                    $sum = 0;
                    for ($j = 0; $j < count($times); $j++) 
                    {
                        if ($times[$j]->est_start_time != null && $times[$j]->est_finish_time != null) 
                        {
                            $start = Carbon::parse($times[$j]->actual_start_time);
                            $end = Carbon::parse($times[$j]->actual_finish_time);
                            $totalDuration = 0;
                            $totalDuration = $start->diffInMinutes($end);
                            $sum +=  $this->minutesIntoTime(intval($totalDuration));
                        }
                    }
                    $reports[$i]['Hours'] = number_format((float)$sum, 2, '.', '');;
                    $success=2;
                }
            }
            $this->reports = $reports;

            return response()->json([
                'success' => $success,
                'reports' => $this->reports,
            ], 201);
        }
    }

    private function minutesIntoTime($minutes){
        $minutes = $minutes / 60;
        $total_hours = ceil($minutes * 4) / 4;
        // $total_hours = number_format((float)$total_hours, 2, '.', '');
        return $total_hours;
    }

    public function leadReport()
    {
        $this->pageTitle = __('app.report.leadReport');
        $this->pageIcon = 'icon-graph';
        // dd($this->list_options);

        $this->from_date = Carbon::parse(Carbon::now()->subMonth())->format($this->global->date_format);
        $this->to_date = Carbon::parse(Carbon::now())->format($this->global->date_format);
        $this->lead_statuses = CRMLeadStatuses::select('id', 'lead_status')->where('tenant_id', '=', auth()->user()->tenant_id)->orderBy('sort_order', 'asc')->get();

        $from_date = Carbon::createFromFormat($this->global->date_format, $this->from_date)->toDateString();
        $to_date = Carbon::createFromFormat($this->global->date_format, $this->to_date)->toDateString();

        // dd($to_date);
        $source = $this->list_options;
        $reports = null;
        for ($i = 0; $i < count($source); $i++) 
        {
            $reports[$i]['source'] = $source[$i]->options;

            $reports[$i]['QuotesCreated'] = CRMOpStatusLog::where('crm_op_status_log.new_status', 'New')
                ->join('jobs_moving', 'jobs_moving.crm_opportunity_id', 'crm_op_status_log.crm_opportunity_id')
                ->where('jobs_moving.lead_info', $source[$i]->options)
                ->where('jobs_moving.tenant_id', auth()->user()->tenant_id)
                ->whereDate('crm_op_status_log.created_at', '>=', $from_date)
                ->whereDate('crm_op_status_log.created_at', '<=', $to_date)
                ->count();

            $reports[$i]['jobsConfirmed'] = JobsMovingStatusLog::where('jobs_moving_status_log.new_status', 'New')
                ->join('jobs_moving', 'jobs_moving.job_id', 'jobs_moving_status_log.job_id')
                ->where('jobs_moving.lead_info', $source[$i]->options)
                ->where('jobs_moving.tenant_id', auth()->user()->tenant_id)
                ->whereDate('jobs_moving_status_log.created_at', '>=', $from_date)
                ->whereDate('jobs_moving_status_log.created_at', '<=', $to_date)
                ->count();

            $reports[$i]['QuotesLost'] = CRMOpStatusLog::where('crm_op_status_log.new_status', 'Lost')
                ->join('jobs_moving', 'jobs_moving.crm_opportunity_id', 'crm_op_status_log.crm_opportunity_id')
                ->where('jobs_moving.lead_info', $source[$i]->options)
                ->where('jobs_moving.tenant_id', auth()->user()->tenant_id)
                ->whereDate('crm_op_status_log.created_at', '>=', $from_date)
                ->whereDate('crm_op_status_log.created_at', '<=', $to_date)
                ->count();


            $sales = InvoiceItems::where('jobs_moving.lead_info', $source[$i]->options)
                ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
                ->join('jobs_moving', 'jobs_moving.job_id', '=', 'invoices.job_id')
                ->where('jobs_moving.tenant_id', auth()->user()->tenant_id)
                ->whereDate('jobs_moving.created_at', '>=', $from_date)
                ->whereDate('jobs_moving.created_at', '<=', $to_date)
                ->sum('invoice_items.amount');
                
            $reports[$i]['totalSales'] = $this->global->currency_symbol.''.number_format((float) $sales, 2, '.', ',');
        
        }

        $this->reports = $reports;
        return view('admin.reports.lead-report.index', $this->data);
    }

    public function leadData(Request $request)
    {
        if($request->source)
        {
            $from_date = Carbon::createFromFormat('d/m/Y', $request->created_date_start)->format('Y-m-d');
            $to_date = Carbon::createFromFormat('d/m/Y', $request->created_date_end)->format('Y-m-d h:i:s');
    
            $quotesCreated = CRMOpStatusLog::where('crm_op_status_log.new_status', 'New')
                ->join('jobs_moving', 'jobs_moving.crm_opportunity_id', 'crm_op_status_log.crm_opportunity_id')
                ->where('jobs_moving.lead_info', $request->source)
                ->where('jobs_moving.tenant_id', auth()->user()->tenant_id)
                ->whereDate('crm_op_status_log.created_at', '>=', $from_date)
                ->whereDate('crm_op_status_log.created_at', '<=', $to_date)
                ->count();
    
            $jobsConfirmed = JobsMovingStatusLog::where('jobs_moving_status_log.new_status', 'New')
                ->join('jobs_moving', 'jobs_moving.job_id', 'jobs_moving_status_log.job_id')
                ->where('jobs_moving.lead_info', $request->source)
                ->where('jobs_moving.tenant_id', auth()->user()->tenant_id)
                ->whereDate('jobs_moving_status_log.created_at', '>=', $from_date)
                ->whereDate('jobs_moving_status_log.created_at', '<=', $to_date)
                ->count();
    
            $quotesLost = CRMOpStatusLog::where('crm_op_status_log.new_status', 'Lost')
                ->join('jobs_moving', 'jobs_moving.crm_opportunity_id', 'crm_op_status_log.crm_opportunity_id')
                ->where('jobs_moving.lead_info', $request->source)
                ->where('jobs_moving.tenant_id', auth()->user()->tenant_id)
                ->whereDate('crm_op_status_log.created_at', '>=', $from_date)
                ->whereDate('crm_op_status_log.created_at', '<=', $to_date)
                ->count();
    
            $totalSales = InvoiceItems::where('jobs_moving.lead_info', $request->source)
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->join('jobs_moving', 'jobs_moving.job_id', '=', 'invoices.job_id')
            ->where('jobs_moving.tenant_id', auth()->user()->tenant_id)
            ->whereDate('jobs_moving.created_at', '>=', $from_date)
            ->whereDate('jobs_moving.created_at', '<=', $to_date)
            ->sum('invoice_items.amount');

            return response()->json([
                'success' => 1,
                'quotesCreated' => $quotesCreated,
                'jobsConfirmed' => $jobsConfirmed,
                'quotesLost' => $quotesLost,
                'totalSales' => $this->global->currency_symbol.''.number_format((float) $totalSales, 2, '.', ''),
                'source' => $request->source,
            ], 200);
        }
        else
        {
            $from_date = Carbon::createFromFormat('d/m/Y', $request->created_date_start)->format('Y-m-d');
            $to_date = Carbon::createFromFormat('d/m/Y', $request->created_date_end)->format('Y-m-d h:i:s');

            $source = $this->list_options;
            $reports = null;
            for ($i = 0; $i < count($source); $i++) 
            {
                $reports[$i]['source'] = $source[$i]->options;

                $reports[$i]['QuotesCreated'] = CRMOpStatusLog::where('crm_op_status_log.new_status', 'New')
                    ->join('jobs_moving', 'jobs_moving.crm_opportunity_id', 'crm_op_status_log.crm_opportunity_id')
                    ->where('jobs_moving.lead_info', $source[$i]->options)
                    ->where('jobs_moving.tenant_id', auth()->user()->tenant_id)
                    ->whereDate('crm_op_status_log.created_at', '>=', $from_date)
                    ->whereDate('crm_op_status_log.created_at', '<=', $to_date)
                    ->count();

                $reports[$i]['jobsConfirmed'] = JobsMovingStatusLog::where('jobs_moving_status_log.new_status', 'New')
                    ->join('jobs_moving', 'jobs_moving.job_id', 'jobs_moving_status_log.job_id')
                    ->where('jobs_moving.lead_info', $source[$i]->options)
                    ->where('jobs_moving.tenant_id', auth()->user()->tenant_id)
                    ->whereDate('jobs_moving_status_log.created_at', '>=', $from_date)
                    ->whereDate('jobs_moving_status_log.created_at', '<=', $to_date)
                    ->count();

                $reports[$i]['QuotesLost'] = CRMOpStatusLog::where('crm_op_status_log.new_status', 'Lost')
                    ->join('jobs_moving', 'jobs_moving.crm_opportunity_id', 'crm_op_status_log.crm_opportunity_id')
                    ->where('jobs_moving.lead_info', $source[$i]->options)
                    ->where('jobs_moving.tenant_id', auth()->user()->tenant_id)
                    ->whereDate('crm_op_status_log.created_at', '>=', $from_date)
                    ->whereDate('crm_op_status_log.created_at', '<=', $to_date)
                    ->count();


                $sales = InvoiceItems::where('jobs_moving.lead_info', $source[$i]->options)
                    ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
                    ->join('jobs_moving', 'jobs_moving.job_id', '=', 'invoices.job_id')
                    ->where('jobs_moving.tenant_id', auth()->user()->tenant_id)
                    ->whereDate('jobs_moving.created_at', '>=', $from_date)
                    ->whereDate('jobs_moving.created_at', '<=', $to_date)
                    ->sum('invoice_items.amount');
                    
                $reports[$i]['totalSales'] = $this->global->currency_symbol.''.number_format((float) $sales, 2, '.', ',');
            
            }

            $this->reports = $reports;
            return response()->json([
                'success' => 2,
                'reports' => $this->reports
            ], 201);
        }
    }

    public function dailyVehicleCheck()
    {
        $this->pageTitle = __('app.report.dailyVehicleCheck');
        $this->pageIcon = 'icon-truck';
        // dd($this->list_options);

        $this->companies = Companies::where('tenant_id', auth()->user()->tenant_id)->first();
        $this->tables = VehicleChecklistGroup::where(['tenant_id' => auth()->user()->tenant_id, 'deleted' => 'N'])->get();
        $this->from_date = Carbon::parse(Carbon::now()->subMonth())->format($this->global->date_format);
        $this->to_date = Carbon::parse(Carbon::now())->format($this->global->date_format);
        $this->vehicles = Vehicles::where(['tenant_id' => auth()->user()->tenant_id])->orderBy('vehicle_name', 'asc')->get();

        $from_date = Carbon::createFromFormat($this->global->date_format, $this->from_date)->toDateString();
        $to_date = Carbon::createFromFormat($this->global->date_format, $this->to_date)->toDateString();

        // dd($this->companies);
        return view('admin.reports.daily-vehicle-check.index', $this->data);
    }

    public function getChecklistData(Request $request)
    {
        if($request->vehicle_id)
        {
            $from_date = Carbon::createFromFormat('d/m/Y', $request->created_date_start)->format('Y-m-d');
            $to_date = Carbon::createFromFormat('d/m/Y', $request->created_date_end)->format('Y-m-d');

            $data = null;
            $reports = DailyDriverVehicleCheck::where(['tenant_id' => auth()->user()->tenant_id, 'vehicle_id' => $request->vehicle_id])
                                    ->whereDate('date_of_check', '>=', $from_date)
                                    ->whereDate('date_of_check', '<=', $to_date)
                                    ->orderBy('date_of_check', 'ASC')
                                    ->get();
            // $this->drivers = User::driverList();
            if($reports)
            {
                for($i = 0; $i < count($reports); $i++)
                {
                    $driverName = PplPeople::select(DB::raw("CONCAT(ppl_people.first_name,' ',ppl_people.last_name) AS name,ppl_people.id as id"))->where(['id' => $reports[$i]->driver_id, 'tenant_id' => auth()->user()->tenant_id])->first();
                    $vehicleName = Vehicles::select('vehicle_name')->where(['id' => $reports[$i]->vehicle_id, 'tenant_id' => auth()->user()->tenant_id])->first();
                    $data[$i]['id'] = $reports[$i]->id;
                    $data[$i]['date'] = date('d/m/Y', strtotime($reports[$i]->date_of_check));
                    $data[$i]['vehicle'] = ($vehicleName) ? $vehicleName->vehicle_name : '';
                    $data[$i]['driver'] = ($driverName) ? $driverName->name : '';
                    $data[$i]['report'] = ($vehicleName) ? $vehicleName->vehicle_name.' '.$data[$i]['date'] : '';
                }
            }

            $this->reports = $data;

            return response()->json([
                'success' => 2,
                'reports' => $this->reports
            ], 201);
        }
        else
        {
            $from_date = Carbon::createFromFormat('d/m/Y', $request->created_date_start)->format('Y-m-d');
            $to_date = Carbon::createFromFormat('d/m/Y', $request->created_date_end)->format('Y-m-d');

            $data = null;
            $reports = DailyDriverVehicleCheck::where(['tenant_id' => auth()->user()->tenant_id])
                                    ->whereDate('date_of_check', '>=', $from_date)
                                    ->whereDate('date_of_check', '<=', $to_date)
                                    ->orderBy('date_of_check', 'ASC')
                                    ->get();
            // $this->drivers = User::driverList();
            if($reports)
            {
                for($i = 0; $i < count($reports); $i++)
                {
                    $driverName = PplPeople::select(DB::raw("CONCAT(ppl_people.first_name,' ',ppl_people.last_name) AS name,ppl_people.id as id"))->where(['id' => $reports[$i]->driver_id, 'tenant_id' => auth()->user()->tenant_id])->first();
                    $vehicleName = Vehicles::select('vehicle_name')->where(['id' => $reports[$i]->vehicle_id, 'tenant_id' => auth()->user()->tenant_id])->first();
                    $data[$i]['id'] = $reports[$i]->id;
                    $data[$i]['date'] = date('d/m/Y', strtotime($reports[$i]->date_of_check));
                    $data[$i]['vehicle'] = ($vehicleName) ? $vehicleName->vehicle_name : '';
                    $data[$i]['driver'] = ($driverName) ? $driverName->name : '';
                    $data[$i]['report'] = ($vehicleName) ? $vehicleName->vehicle_name.' '.$data[$i]['date'] : '';
                }
            }

            $this->reports = $data;

            return response()->json([
                'success' => 2,
                'reports' => $this->reports
            ], 201);
        }
    }

    public function getPopupData(Request $request)
    {
        $data = DailyDriverVehicleCheck::where(['id' => $request->daily_driver_vehicle_check_id, 'tenant_id' => auth()->user()->tenant_id])->first();

        $driverName = PplPeople::select(DB::raw("CONCAT(ppl_people.first_name,' ',ppl_people.last_name) AS name,ppl_people.id as id"))->where(['id' => $data->driver_id, 'tenant_id' => auth()->user()->tenant_id])->first();
        $vehicleName = Vehicles::select('vehicle_name')->where(['id' => $data->vehicle_id, 'tenant_id' => auth()->user()->tenant_id])->first();
                    
        $response['date'] = date('d/m/Y', strtotime($data->date_of_check));
        $response['time'] = date('h:i A', strtotime($data->time_of_check));
        $response['driver'] = $driverName->name;
        $response['vehicle'] = $vehicleName->vehicle_name;
        $response['odometer'] = $data->start_odometer;
        $response['fuel'] = $data->fuel_percent;
        $response['notes'] = $data->notes;

        $details = DailyDriverVehicleCheckDetails::where(['daily_check_id' => $data->id, 'tenant_id' => auth()->user()->tenant_id])->get();

        $viewTables = null;
        $groups = VehicleChecklistGroup::where(['tenant_id' => auth()->user()->tenant_id])->get();
        if($groups && $details)
        {
            foreach($groups as $group)
            {
                $viewTables[] = view('admin.reports.daily-vehicle-check.table')->with(['group' => $group, 'finalData' => $details])->render();
            }
        }
        return response()->json([
            'success' => 1,
            'data' => $response,
            'viewTables' => $viewTables
        ]);   
    }
}
