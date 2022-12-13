<?php

namespace App\Http\Controllers\Admin;

use App\BackLoadingTripJobs;
use App\BackLoadingTrips;
use App\Companies;
use App\CRMContactDetail;
use App\CRMContacts;
use App\CRMLeads;
use App\Invoice;
use Illuminate\Http\Request;
use App\JobsMoving;
use App\JobsMovingLegs;
use App\ListTypes;
use App\OrganisationSettings;
use App\User;
use App\Vehicles;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\File;

class BackloadingController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.backloading');
        $this->pageIcon = 'icon-truck';
    }

    public function index()
    {
        $this->from_date = Carbon::parse(Carbon::now()->subMonth())->format($this->global->date_format);
        $this->to_date = Carbon::parse(Carbon::now()->addMonth())->format($this->global->date_format);
        $from_date = Carbon::createFromFormat($this->global->date_format, $this->from_date)->toDateString();
        $to_date = Carbon::createFromFormat($this->global->date_format, $this->to_date)->toDateString();
        $this->vehicles = Vehicles::select('id','vehicle_name', 'license_plate_number')->where(['tenant_id' => auth()->user()->tenant_id, 'active' => 'Y'])->get();
        $this->drivers = User::driverList();
        $this->status = ListTypes::where(['list_types.tenant_id' => auth()->user()->tenant_id, 'list_types.list_name' => 'Backloading Status'])
                                ->join('list_options', 'list_options.list_type_id', 'list_types.id')
                                ->get();
        $this->records = BackLoadingTrips::select(
                                'backloading_trips.id AS id',
                                'backloading_trips.trip_name',
                                'backloading_trips.start_city',
                                'backloading_trips.finish_city',
                                'backloading_trips.start_date',
                                'backloading_trips.finish_date',
                                'backloading_trips.waybill_number',
                                'vehicles.license_plate_number',
                                'vehicles.vehicle_name',
                                'vehicles.cubic_capacity'
                                )
                                ->where(['backloading_trips.tenant_id' => auth()->user()->tenant_id])
                                ->leftjoin('vehicles', 'vehicles.id', 'backloading_trips.vehicle_id')
                                ->whereDate('backloading_trips.start_date', '>=', $from_date)
                                ->whereDate('backloading_trips.start_date', '<=', $to_date)
                                ->where('backloading_trips.deleted', '=', 'N')
                                ->get();
        $data = null;
        
        if(count($this->records))
        {
            foreach($this->records as $record)
            {
                $trip_jobs = BackLoadingTripJobs::where(['tenant_id' => auth()->user()->tenant_id, 'trip_id' => $record->id])->get();
                $response = $this->calculateBarPercentage($trip_jobs, $record->cubic_capacity);
                $data[] = array(
                    'id' => $record->id,
                    'trip_name' => $record->trip_name,
                    'start_city' => $record->start_city,
                    'finish_city' => $record->finish_city,
                    'license_plate_number' => $record->license_plate_number,
                    'vehicle_name' => $record->vehicle_name,
                    'waybill_number' => $record->waybill_number,
                    'start_date' => $record->start_date,
                    'finish_date' => $record->finish_date,
                    'capacity_loading' => $response['sum'] ? $response['sum'] : 0,
                    'all_jobs' => $response['all_jobs'] ? $response['all_jobs'] : null
                );
            }
        }
        $this->trips = $data;

        return view('admin.backloading.index', $this->data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required',
            'trip_name' => 'required',
            'start_city' => 'required',
            'finish_city' => 'required',
            'start_date' => 'required',
            'finish_date' => 'required',
            'driver_id' => 'required'
        ]);

        $waybill_number = BackLoadingTrips::select(DB::raw('MAX(waybill_number) as max_waybill'))->where('tenant_id', '=', auth()->user()->tenant_id)->first();

        $model = new BackLoadingTrips();
        $model->tenant_id = auth()->user()->tenant_id;
        $model->waybill_number = $waybill_number->max_waybill + 1;
        $model->vehicle_id = $request->vehicle_id;
        $model->trip_name = $request->trip_name;
        $model->start_city = $request->start_city;
        $model->finish_city = $request->finish_city;
        $model->start_date = $request->start_date;
        $model->finish_date = $request->finish_date;
        $model->driver_id = $request->driver_id;
        $model->notes = $request->trip_notes;
        if($model->save())
        {
            $response['error'] = 0;
            $response['message'] = 'New Trip has been added!';
            return $response;
        }
        else{
            $response['error'] = 1;
            $response['message'] = 'Something went wrong!';
            return $response;
        }

    }

    private function calculateBarPercentage($jobs, $cubic_capacity)
    {
        $all_jobs = null;
        $sum = 0;
        if(count($jobs))
        {
            foreach($jobs as $trip_job)
            {
                $single_job = JobsMoving::where(['tenant_id' => auth()->user()->tenant_id, 'job_id' => $trip_job->job_id])->first();
                $all_jobs[] = [
                    'job_id' => $trip_job->job_id,
                    'job_number' => $single_job->job_number
                ];
                if($single_job->total_cbm != null)
                {
                    $sum += $single_job->total_cbm;
                }
            }

            if($cubic_capacity > 0)
            {
                $sum = (int)$sum;
                $sum = ($sum / $cubic_capacity) * 100;
                $sum = (int)$sum;
            }
            else{
                $sum = 0;
            }
        }
        $response['all_jobs'] = $all_jobs;
        $response['sum'] = $sum;
        return $response;
    }

    public function destroy(Request $request)
    {
        BackLoadingTrips::where(['tenant_id' => auth()->user()->tenant_id, 'id' => $request->trip_id])
                        ->update([
                            'deleted'=> 'Y'
                        ]);
        $response['message'] = 'Trip has been deleted successfully!';
        $response['error'] = 0;
        return json_encode($response);
    }

    public function getData(Request $request)
    {
        $from_date = Carbon::createFromFormat($this->global->date_format, $request->created_date_start)->format('Y-m-d');
        $to_date = Carbon::createFromFormat($this->global->date_format, $request->created_date_end)->format('Y-m-d');
        $this->vehicles = Vehicles::select('id','vehicle_name', 'license_plate_number')->where(['tenant_id' => auth()->user()->tenant_id, 'active' => 'Y'])->get();
        $this->drivers = User::driverList();
        $this->status = ListTypes::where(['list_types.tenant_id' => auth()->user()->tenant_id, 'list_types.list_name' => 'Backloading Status'])
                                ->join('list_options', 'list_options.list_type_id', 'list_types.id')
                                ->get();
        $records = BackLoadingTrips::select(
                                'backloading_trips.id AS id',
                                'backloading_trips.trip_name',
                                'backloading_trips.start_city',
                                'backloading_trips.finish_city',
                                'backloading_trips.start_date',
                                'backloading_trips.finish_date',
                                'backloading_trips.waybill_number',
                                'vehicles.license_plate_number',
                                'vehicles.vehicle_name',
                                'vehicles.cubic_capacity'
                                )
                                ->where(['backloading_trips.tenant_id' => auth()->user()->tenant_id])
                                ->leftjoin('vehicles', 'vehicles.id', 'backloading_trips.vehicle_id')
                                // ->leftjoin('backloading_trip_jobs', 'backloading_trip_jobs.trip_id', 'backloading_trips.id')
                                ->whereDate('backloading_trips.start_date', '>=', $from_date)
                                ->whereDate('backloading_trips.start_date', '<=', $to_date)
                                ->where('backloading_trips.deleted', '=', 'N');

        if($request->vehicle_id != null)
        {
            $records = $records->where('backloading_trips.vehicle_id', $request->vehicle_id);
        }

        if($request->status != null)
        {
            $records = $records->where('backloading_trips.trip_status', $request->status);
        }

        $records = $records->get();
        $this->records = $records;

        $data = null;
        
        if(count($this->records))
        {
            foreach($this->records as $record)
            {
                $all_jobs = null;
                $trip_jobs = BackLoadingTripJobs::where(['tenant_id' => auth()->user()->tenant_id, 'trip_id' => $record->id])->get();
                $sum = 0;
                if(count($trip_jobs))
                {
                    foreach($trip_jobs as $trip_job)
                    {
                        $single_job = JobsMoving::where(['tenant_id' => auth()->user()->tenant_id, 'job_id' => $trip_job->job_id])->first();
                        $all_jobs[] = [
                            'job_id' => $trip_job->job_id,
                            'job_number' => $single_job->job_number
                        ];
                        if($single_job->total_cbm != null)
                        {
                            $sum += $single_job->total_cbm;
                        }
                    }

                    if($record->cubic_capacity > 0)
                    {
                        $sum = (int)$sum;
                        $sum = ($sum / $record->cubic_capacity) * 100;
                        $sum = (int)$sum;
                    }
                    else{
                        $sum = 100;
                    }
                }
                $data[] = array(
                    'id' => $record->id,
                    'trip_name' => $record->trip_name,
                    'start_city' => $record->start_city,
                    'finish_city' => $record->finish_city,
                    'license_plate_number' => $record->license_plate_number,
                    'vehicle_name' => $record->vehicle_name,
                    'waybill_number' => $record->waybill_number,
                    'start_date' => $record->start_date,
                    'finish_date' => $record->finish_date,
                    'capacity_loading' => $sum,
                    'all_jobs' => $all_jobs
                );
            }
        }
        $this->trips = $data;
        
        $response['error'] = 0;
        $response['html'] = view('admin.backloading.trip_grid', $this->data)->render();
        return $response;
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $from_date = Carbon::createFromFormat($this->global->date_format, $request->created_date_start)->toDateString();
        $to_date = Carbon::createFromFormat($this->global->date_format, $request->created_date_end)->toDateString();

        $searchData = BackLoadingTrips::select(
                                'backloading_trips.id AS id',
                                'backloading_trips.trip_name',
                                'backloading_trips.start_city',
                                'backloading_trips.finish_city',
                                'backloading_trips.start_date',
                                'backloading_trips.finish_date',
                                'backloading_trips.waybill_number',
                                'vehicles.license_plate_number',
                                'vehicles.vehicle_name',
                                'vehicles.cubic_capacity'
                                );

        if($search != null)
        {
            $searchData = $searchData->like('backloading_trips.trip_name', $request->search);
            // $searchData = $searchData->where(function($query) use ($search) {
            //     $query->orWhere('backloading_trips.trip_name', 'LIKE', $search);
            // });
            // $searchData = $searchData->like('backloading_trips.start_city', $request->search);   
        }
        $searchData = $searchData->where(['backloading_trips.tenant_id' => auth()->user()->tenant_id])
                                ->leftjoin('vehicles', 'vehicles.id', 'backloading_trips.vehicle_id')
                                ->whereDate('backloading_trips.start_date', '>=', $from_date)
                                ->whereDate('backloading_trips.start_date', '<=', $to_date)
                                ->where('backloading_trips.deleted', '=', 'N')
                                ->get();

        $this->records = $searchData;
        $data = null;

        if(count($this->records))
        {
            foreach($this->records as $record)
            {
                $trip_jobs = BackLoadingTripJobs::where(['tenant_id' => auth()->user()->tenant_id, 'trip_id' => $record->id])->get();
                $response = $this->calculateBarPercentage($trip_jobs, $record->cubic_capacity);
                $data[] = array(
                    'id' => $record->id,
                    'trip_name' => $record->trip_name,
                    'start_city' => $record->start_city,
                    'finish_city' => $record->finish_city,
                    'license_plate_number' => $record->license_plate_number,
                    'vehicle_name' => $record->vehicle_name,
                    'waybill_number' => $record->waybill_number,
                    'start_date' => $record->start_date,
                    'finish_date' => $record->finish_date,
                    'capacity_loading' => $response['sum'] ? $response['sum'] : 0,
                    'all_jobs' => $response['all_jobs'] ? $response['all_jobs'] : null
                );
            }
        }
        $this->trips = $data;
        
        $response['error'] = 0;
        $response['html'] = view('admin.backloading.trip_grid', $this->data)->render();
        return $response;
    }

    public function assignJob(Request $request)
    {
        $this->from_date = Carbon::parse(Carbon::now()->subMonth())->format($this->global->date_format);
        $this->to_date = Carbon::parse(Carbon::now())->format($this->global->date_format);
        $this->trip = BackloadingTrips::select(
                                            'backloading_trips.id',
                                            'backloading_trips.trip_name',
                                            'backloading_trips.start_city',
                                            'backloading_trips.finish_city',
                                            'backloading_trips.start_date',
                                            'backloading_trips.finish_date',
                                            'backloading_trips.notes',
                                            'backloading_trips.waybill_file_name',
                                            'backloading_trips.vehicle_id',
                                            'backloading_trips.driver_id',
                                            'backloading_trips.waybill_number',
                                            'vehicles.vehicle_name',
                                            'vehicles..license_plate_number',
                                            'vehicles..cubic_capacity',
                                            'ppl_people.first_name',
                                            'ppl_people.last_name',
                                        )
                                        ->where(['backloading_trips.tenant_id' => auth()->user()->tenant_id, 'backloading_trips.id' => $request->trip_id])
                                        ->leftjoin('vehicles', 'vehicles.id', 'backloading_trips.vehicle_id')
                                        ->leftjoin('ppl_people', 'ppl_people.id', 'backloading_trips.driver_id')
                                        ->first();
        
        $this->vehicles = Vehicles::select('id','vehicle_name', 'license_plate_number')->where(['tenant_id' => auth()->user()->tenant_id, 'active' => 'Y'])->get();
        $this->drivers = User::driverList();

        $trip_jobs = BackLoadingTripJobs::where(['tenant_id' => auth()->user()->tenant_id, 'trip_id' => $this->trip->id])->get();
        $this->total_jobs_cbm=0;
        if($trip_jobs){
            foreach($trip_jobs as $jobs){
                $this->total_jobs_cbm += JobsMoving::where(['job_id'=> $jobs->job_id,'tenant_id'=>auth()->user()->tenant_id])->pluck('total_cbm')->first();
            }
        }
        //exit;
        $this->barData = $this->calculateBarPercentage($trip_jobs, $this->trip->cubic_capacity);

        return view('admin.backloading.assign_job', $this->data);
    }

    public function updateTrip(Request $request)
    {
        $this-> trip = BackLoadingTrips::where(['tenant_id' => auth()->user()->tenant_id, 'id' => $request->trip_id])
                        ->update([
                            'trip_name' => $request->trip_name,
                            'start_city' => $request->start_city,
                            'finish_city' => $request->finish_city,
                            'start_date' => $request->start_date,
                            'finish_date' => $request->finish_date,
                            'vehicle_id' => $request->vehicle_id,
                            'driver_id' => $request->driver_id,
                            'notes' => $request->notes
                        ]);
        if($this->trip > 0)
        {
            $this->trip = BackloadingTrips::select(
                            'backloading_trips.id',
                            'backloading_trips.trip_name',
                            'backloading_trips.start_city',
                            'backloading_trips.finish_city',
                            'backloading_trips.start_date',
                            'backloading_trips.finish_date',
                            'backloading_trips.notes',
                            'backloading_trips.waybill_file_name',
                            'backloading_trips.waybill_number',
                            'backloading_trips.vehicle_id',
                            'backloading_trips.driver_id',
                            'vehicles.vehicle_name',
                            'vehicles.cubic_capacity',
                            'vehicles..license_plate_number',
                            'ppl_people.first_name',
                            'ppl_people.last_name',
                        )
                        ->where(['backloading_trips.tenant_id' => auth()->user()->tenant_id, 'backloading_trips.id' => $request->trip_id])
                        ->leftjoin('vehicles', 'vehicles.id', 'backloading_trips.vehicle_id')
                        ->leftjoin('ppl_people', 'ppl_people.id', 'backloading_trips.driver_id')
                        ->first();

            $this->vehicles = Vehicles::select('id','vehicle_name', 'license_plate_number')->where(['tenant_id' => auth()->user()->tenant_id, 'active' => 'Y'])->get();
            $this->drivers = User::driverList();

            $trip_jobs = BackLoadingTripJobs::where(['tenant_id' => auth()->user()->tenant_id, 'trip_id' => $this->trip->id])->get();
            $this->total_jobs_cbm=0;
            if($trip_jobs){
                foreach($trip_jobs as $jobs){
                    $this->total_jobs_cbm += JobsMoving::where(['job_id'=> $jobs->job_id,'tenant_id'=>auth()->user()->tenant_id])->pluck('total_cbm')->first();
                }
            }

            $response['error'] = 0;
            $response['message'] = 'Trip has been updates SuccessFully!';
            $response['html'] = view('admin.backloading.trip_side_grid', $this->data)->render();
            return $response;
        }
        else
        {
            $response['error'] = 1;
            $response['message'] = 'Something Went wrong';
            return $response;
        }
    }

    public function getTripJobs(Request $request)
    {
        $this->trip_id = $request->trip_id;
        $this->jobs = BackLoadingTripJobs::select(
                                    'jobs_moving.job_id',
                                    'jobs_moving.job_number',
                                    'jobs_moving.customer_id',
                                    'jobs_moving_legs.leg_date as job_date',
                                    'jobs_moving.pickup_suburb',
                                    'jobs_moving.delivery_suburb',
                                    'jobs_moving_legs.leg_status as job_status',
                                    'jobs_moving.payment_instructions',
                                    'jobs_moving.pickup_contact_name',
                                    'jobs_moving.total_cbm',
                                    'backloading_trip_jobs.leg_id',
                                )
                                ->where(['backloading_trip_jobs.tenant_id' => auth()->user()->tenant_id, 'backloading_trip_jobs.trip_id' => $this->trip_id])
                                ->leftjoin('jobs_moving', 'jobs_moving.job_id', 'backloading_trip_jobs.job_id')
                                ->leftjoin('jobs_moving_legs', 'jobs_moving_legs.job_id', 'jobs_moving.job_id')
                                ->groupBy('jobs_moving.job_id')
                                ->get();

            return DataTables::of($this->jobs)
            ->editColumn('job_number', function ($row) {
                return '<a class="badge bg-blue" href="' . route("admin.list-jobs.view-job", $row->job_id) . '" >' . $row->job_number . '</a>';
                
            })
            ->editColumn('leg_number', function ($row) {
                $leg = JobsMovingLegs::where(['tenant_id' => auth()->user()->tenant_id, 'id' => $row->leg_id, 'job_id' => $row->job_id])->first();
                return $leg->leg_number;
            })
            ->editColumn('name', function ($row) {
                $crmlead_name = CRMleads::where(['id' => $row->customer_id, 'tenant_id' => auth()->user()->tenant_id])->pluck('name')->first();
                return $crmlead_name;
            })
            ->editColumn('cbm', function ($row) {
                return $row->total_cbm;
            })
            ->editColumn('job_date', function ($row) {
                $date = Carbon::parse($row->job_date);
                return $date->format('d-m-Y');
            }) 
            ->editColumn('pickup_suburb', function ($row) {
                return $row->pickup_suburb;
            })                            
            ->editColumn('drop_off_suburb', function ($row) {
                return $row->delivery_suburb;
            })
            ->editColumn('job_status', function ($row) {
                return $row->job_status;
            })
            ->editColumn('payment_status', function ($row) {
                $invoice = Invoice::where(['job_id'=> $row->job_id,'sys_job_type'=>'Moving'])->where('tenant_id', '=', auth()->user()->tenant_id)->first();
                if ($invoice):
                    $paid = $invoice->getPaidAmount();
                    $amount = $invoice->getTotalAmount();
                    if (floatval( (string) $amount ) > floatval( (string) $paid) && floatval( (string) $paid)>0) {
                        return 'Partially Paid';
                    } elseif (floatval( (string) $paid) == 0) {
                        return 'Unpaid';
                    } else {
                        return 'Paid';
                    }
                endif;
                return '-';
            })
            ->editColumn('action', function ($row) {
                $result =  '<div class="list-icons float-right">'.
                            '<div class="dropdown">'.
                                '<a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown"><i class="icon-menu"></i></a>'.
                                    '<div class="dropdown-menu dropdown-menu-right">'.
                                        '<a href="#" class="dropdown-item trip-unassign-job" data-trip_id="'. $this->trip_id .'" data-job_id="'. $row->job_id .'" data-leg_id="'. $row->leg_id .'" ><i class="fa fa-clipboard"></i> Unassign Job</a>'.                                                        
                                    '</div>'.
                            '</div>'.
                        '</div>';
                return $result;
            })
            ->rawColumns(['job_number', 'leg_number', 'cbm', 'job_date', 'pickup_suburb', 'drop_off_suburb', 'job_status', 'payment_status', 'name', 'action'])
            ->make(true);
    }

    public function tripUnassignJob(Request $request)
    {
        $trip_job = BackLoadingTripJobs::where(['tenant_id' => auth()->user()->tenant_id, 'trip_id' => $request->trip_id, 'job_id' => $request->job_id, 'leg_id' => $request->leg_id])->delete();
        if($trip_job > 0)
        {
            $this->trip = BackloadingTrips::select(
                                        'backloading_trips.id',
                                        'backloading_trips.waybill_file_name',
                                        'vehicles..cubic_capacity',
                                    )
                                    ->where(['backloading_trips.tenant_id' => auth()->user()->tenant_id, 'backloading_trips.id' => $request->trip_id])
                                    ->leftjoin('vehicles', 'vehicles.id', 'backloading_trips.vehicle_id')
                                    ->leftjoin('ppl_people', 'ppl_people.id', 'backloading_trips.driver_id')
                                    ->first();
            $trip_jobs = BackLoadingTripJobs::where(['tenant_id' => auth()->user()->tenant_id, 'trip_id' => $this->trip->id])->get();
            $this->barData = $this->calculateBarPercentage($trip_jobs, $this->trip->cubic_capacity);

            $response['error'] = 0;
            $response['message'] = 'Job has been Unassign SuccessFully!';
            $response['barData'] = $this->barData;
            return $response;
        }
        else
        {
            $response['error'] = 1;
            $response['message'] = 'Something Went wrong';
            return $response;
        }
    }

    public function tripAssignJob(Request $request)
    {
        $model = new BackLoadingTripJobs();
        $model->tenant_id = auth()->user()->tenant_id;
        $model->trip_id = $request->trip_id;
        $model->job_id = $request->job_id;
        $model->leg_id = $request->leg_id;
        $model->created_by = auth()->user()->id;
        if($model->save())
        {
            $this->trip = BackloadingTrips::select(
                                        'backloading_trips.id',
                                        'backloading_trips.waybill_file_name',
                                        'vehicles..cubic_capacity',
                                    )
                                    ->where(['backloading_trips.tenant_id' => auth()->user()->tenant_id, 'backloading_trips.id' => $request->trip_id])
                                    ->leftjoin('vehicles', 'vehicles.id', 'backloading_trips.vehicle_id')
                                    ->leftjoin('ppl_people', 'ppl_people.id', 'backloading_trips.driver_id')
                                    ->first();
            $trip_jobs = BackLoadingTripJobs::where(['tenant_id' => auth()->user()->tenant_id, 'trip_id' => $this->trip->id])->get();
            $this->barData = $this->calculateBarPercentage($trip_jobs, $this->trip->cubic_capacity);

            $response['error'] = 0;
            $response['message'] = 'This job is assign to this trip!';
            $response['barData'] = $this->barData;
            return $response;
        }
        else
        {
            $response['error'] = 1;
            $response['message'] = 'SomeThing Went Wrong';
            return $response;
        }
    }

    public function getSearchJobs(Request $request)
    {
        $this->trip_id = $request->trip_id;
        $trip_jobs_leg_ids = BackLoadingTripJobs::where(['tenant_id' => auth()->user()->tenant_id])
                                        ->pluck('leg_id');
        
        $from_date = Carbon::createFromFormat($this->global->date_format, $request->created_date_start)->format('Y-m-d');
        $to_date = Carbon::createFromFormat($this->global->date_format, $request->created_date_end)->format('Y-m-d');

        $this->jobs = JobsMovingLegs::select(
                                'jobs_moving.job_id',
                                'jobs_moving.job_number',
                                'jobs_moving.customer_id',
                                'jobs_moving_legs.leg_date as job_date',
                                'jobs_moving.pickup_suburb',
                                'jobs_moving.delivery_suburb',
                                'jobs_moving_legs.leg_status as job_status',
                                'jobs_moving.payment_instructions',
                                'jobs_moving.pickup_contact_name',
                                'jobs_moving.total_cbm',
                                'jobs_moving_legs.leg_number',
                                'jobs_moving_legs.id AS leg_id'
                            )
                            ->where(['jobs_moving.tenant_id' => auth()->user()->tenant_id, 'jobs_moving.opportunity' => 'N'])
                            ->whereDate('jobs_moving_legs.leg_date', '>=', $from_date)
                            ->whereDate('jobs_moving_legs.leg_date', '<=', $to_date)
                            ->whereNotIn('jobs_moving_legs.id', $trip_jobs_leg_ids)
                            ->join('jobs_moving', 'jobs_moving.job_id', 'jobs_moving_legs.job_id')
                            ->orderBy('jobs_moving.job_id', 'desc')
                            ->get();

        return DataTables::of($this->jobs)
        ->editColumn('job_number', function ($row) {
            return '<a class="badge bg-blue" href="' . route("admin.list-jobs.view-job", $row->job_id) . '" >' . $row->job_number . '</a>';
        })
        ->editColumn('leg_number', function ($row) {
            return $row->leg_number;
        })
        ->editColumn('name', function ($row) {
            $crmlead_name = CRMleads::where(['id' => $row->customer_id, 'tenant_id' => auth()->user()->tenant_id])->pluck('name')->first();
            return $crmlead_name;
        })
        ->editColumn('cbm', function ($row) {
            return $row->total_cbm;
        })
        ->editColumn('job_date', function ($row) {
        $date = Carbon::parse($row->job_date);
            return $date->format('d-m-Y');
        }) 
        ->editColumn('pickup_suburb', function ($row) {
            return $row->pickup_suburb;
        })                            
        ->editColumn('drop_off_suburb', function ($row) {
            return $row->delivery_suburb;
        })
        ->editColumn('job_status', function ($row) {
            return $row->job_status;
        })
        ->editColumn('payment_status', function ($row) {
            $invoice = Invoice::where(['job_id'=> $row->job_id,'sys_job_type'=>'Moving'])->where('tenant_id', '=', auth()->user()->tenant_id)->first();
                if ($invoice):
                    $paid = $invoice->getPaidAmount();
                    $amount = $invoice->getTotalAmount();
                    if (floatval( (string) $amount ) > floatval( (string) $paid) && floatval( (string) $paid)>0) {
                        return '<p class="job-label-txt orange-status job-status">Partially Paid</p>';
                    } elseif (floatval( (string) $paid) == 0) {
                        return '<p class="job-label-txt orange-status job-status">Unpaid</p>';
                    } else {
                        return '<p class="job-label-txt green-status job-status">Paid</p>';
                    }
                endif;
                return '-';
        })
        ->editColumn('action', function ($row) {
            $result =  '<div class="list-icons float-right">'.
                '<div class="dropdown">'.
                    '<a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown"><i class="icon-menu"></i></a>'.
                        '<div class="dropdown-menu dropdown-menu-right">'.
                            '<a href="#" class="dropdown-item trip-assign-job" data-trip_id="'. $this->trip_id .'" data-leg_id="'. $row->leg_id .'" data-job_id="'. $row->job_id .'" ><i class="fa fa-clipboard"></i> Assign Job</a>'.                                                        
                        '</div>'.
                '</div>'.
            '</div>';
            return $result;
        })
        ->rawColumns(['job_number', 'leg_number', 'cbm', 'job_date', 'pickup_suburb', 'drop_off_suburb', 'job_status', 'payment_status', 'name', 'action'])
        ->make(true);
    }

    public function generateWaybill($trip_id)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '3000M');

        try {
            $this->backloading_trip = null;
            $this->backloading_trip_jobs = null;

            $this->backloading_trip = BackLoadingTrips::where(['tenant_id' => auth()->user()->tenant_id, 'id' => $trip_id])->first();
            $this->backloading_trip_jobs = BackLoadingTripJobs::where(['tenant_id' => auth()->user()->tenant_id, 'trip_id' => $trip_id])->get();

            // $this->companies = Companies::where(['id' => $this->job->company_id, 'tenant_id' => auth()->user()->tenant_id])->first();
            $this->companies = Companies::where(['tenant_id' => auth()->user()->tenant_id])->first();
            $this->company_logo_exists = false;
            $post_data = null;

            foreach($this->backloading_trip_jobs as $trip_job)
            {
                $job = JobsMoving::where(['tenant_id' => auth()->user()->tenant_id, 'job_id' => $trip_job->job_id])->first();
                $job_leg = JobsMovingLegs::where(['tenant_id' => auth()->user()->tenant_id, 'id' => $trip_job->leg_id, 'job_id' => $trip_job->job_id])->first();
                $crm_leads = CRMLeads::where(['tenant_id' => auth()->user()->tenant_id, 'id' => $job->customer_id])->first();

                $crm_contacts = CRMContacts::where(['lead_id' => $job->customer_id, 'tenant_id' => auth()->user()->tenant_id])->first();
                // dd($crm_contacts);
                $crm_contact_phone = CRMContactDetail::where(['contact_id' => $crm_contacts->id, 'tenant_id' => auth()->user()->tenant_id])->where('detail_type', '=', 'Mobile')->first();

                $post_data[] = array(
                    'job_number' => ($job) ? $job->job_number : null,
                    'total_cbm' => ($job) ? $job->total_cbm : null,
                    'pickup_address' => ($job_leg) ? $job_leg->pickup_address : null,
                    'drop_off_address' => ($job_leg) ? $job_leg->drop_off_address : null,
                    'notes' => ($job_leg) ? $job_leg->notes : null,
                    'name' => ($crm_leads->name) ? $crm_leads->name : null,
                    'mobile' => ($crm_contact_phone) ? $crm_contact_phone->detail : null,
                );
            }

            $this->jobs_detail = $post_data;

            $file_number = 1;
            if (!empty($this->backloading_trip->waybill_file_name)) {
                $filename = str_replace('.pdf', '', $this->backloading_trip->waybill_file_name);
                $fn_ary = explode('_', $filename);
                $file_number = intval($fn_ary[2]) + 1;
            }

            $filename = 'Waybill_' . $this->backloading_trip->waybill_number . '_' . rand() . '.pdf';

            if ($this->companies) {
                if (File::exists(public_path() . '/user-uploads/company-logo/' . $this->companies->logo)) {
                    $this->company_logo_exists = true;
                }
            }

            $this->organisation_settings = OrganisationSettings::where(['tenant_id' => auth()->user()->tenant_id])->first();

            // dd($this->companies);

            $pdf = app('dompdf.wrapper');
            // return view('admin.backloading.waybill_pdf', $this->data);
            $html = view('admin.backloading.waybill_pdf', $this->data);
            //$pdf->loadView('admin.list-jobs.invoice', $this->data);
            $pdf->loadHtml($html, 'UTF-8');
            
            $pdf->getDomPDF()->set_option("enable_php", true);

            // return $pdf->stream(); // to view pdf
            // return $pdf->download('tmp.pdf');
            $pdf->save('waybill-files/' . $filename);

            if (File::exists(public_path() . '/waybill-files/' . $this->backloading_trip->waybill_file_name)) {
                File::delete(public_path() . '/waybill-files/' . $this->backloading_trip->waybill_file_name);
            }
            
            BackLoadingTrips::where(['tenant_id' => auth()->user()->tenant_id, 'id' => $trip_id])
                            ->update([
                                'waybill_file_name' => $filename
                            ]);

            $response['error'] = 0;
            $response['message'] = 'Waybill generated successfully';
            return json_encode($response);
        } catch (Exception $ex) {
            $response['error'] = 1;
            $response['message'] = $ex->getMessage();
            return json_encode($response);
        }
    }

    public function downloadWaybill($trip_id)
    {
        $response['error'] = 1;
        try {
            $this->trip = BackLoadingTrips::where(['tenant_id' => auth()->user()->tenant_id, 'id' => $trip_id])->first();

            if ($this->trip) {
                $file_url = public_path('waybill-files') . '/' . $this->trip->waybill_file_name;
                if (!empty($this->trip->waybill_file_name) && file_exists($file_url)) {
                    $response['error'] = 0;
                    $response['url'] = url('waybill-files') . '/' . $this->trip->waybill_file_name;
                }
            }
            return json_encode($response);
        } catch (Exception $ex) {
            $response['error'] = 1;
            $response['message'] = $ex->getMessage();
            return json_encode($response);
        }
    }
}
