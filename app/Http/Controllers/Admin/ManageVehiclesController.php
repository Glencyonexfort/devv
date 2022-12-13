<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Requests\Vehicles\StoreVehicle;
use App\Setting;
use App\VehicleGroups;
use App\Vehicles;
use App\VehicleUnavailability;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ManageVehiclesController extends AdminBaseController {

    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('app.menu.vehicles');
        $this->pageIcon = 'icon-truck';
    }

    public function index() {
        return view('admin.vehicles.index', $this->data);
    }

    public function create() {
        $this->vehicleGroups = VehicleGroups::where(['tenant_id'=> auth()->user()->tenant_id, 'deleted'=>0])->orderBy('group_name', 'asc')->get();
        return view('admin.vehicles.create', $this->data);
    }

    public function store(StoreVehicle $request) {
        $validatedData = $request->validate([
            'vehicle_name' => 'required',
            'vehicle_colour' => 'required'
        ]);
        $obj = new Vehicles();
        $obj->vehicle_name = $request->input('vehicle_name');
        $obj->vehicle_description = $request->input('vehicle_description');
        $obj->fuel_type = $request->input('fuel_type');
        $obj->category = $request->input('category');
        $obj->license_plate_number = $request->input('license_plate_number');
        $obj->manufacturer = $request->input('manufacturer');
        $obj->model = $request->input('model');
        $obj->model_year = $request->input('model_year');
        $obj->payload = $request->input('payload');
        $obj->cubic_capacity = $request->input('cubic_capacity');
        $obj->vehicle_colour = $request->input('vehicle_colour');        
        $obj->active = ($request->input('active') == 'Y' ? 'Y' : 'N');
        $obj->created_at = time();
        $obj->updated_at = time();
        $obj->tenant_id = auth()->user()->tenant_id;

        $obj->save();

        return Reply::redirect(route('admin.vehicles.index'), __('messages.vehiclesCreated'));
    }

    public function edit($id) {
        $this->vehicleGroups = VehicleGroups::where(['tenant_id'=> auth()->user()->tenant_id, 'deleted'=>0])->orderBy('group_name', 'asc')->get();
        $this->template = Vehicles::findOrFail($id);
        return view('admin.vehicles.edit', $this->data);
    }

    public function update(StoreVehicle $request, $id) {

        $obj = Vehicles::findOrFail($id);
        $obj->vehicle_name = $request->input('vehicle_name');
        $obj->vehicle_description = $request->input('vehicle_description');
        $obj->fuel_type = $request->input('fuel_type');
        $obj->category = $request->input('category');
        $obj->license_plate_number = $request->input('license_plate_number');
        $obj->manufacturer = $request->input('manufacturer');
        $obj->model = $request->input('model');
        $obj->model_year = $request->input('model_year');
        $obj->payload = $request->input('payload');
        $obj->cubic_capacity = $request->input('cubic_capacity');
        $obj->vehicle_colour = $request->input('vehicle_colour');
        $obj->active = ($request->input('active') == 'Y' ? 'Y' : 'N');
        $obj->updated_at = time();
        $obj->tenant_id = auth()->user()->tenant_id;

        $obj->save();

        return Reply::redirect(route('admin.vehicles.index'), __('messages.vehiclesUpdated'));
    }

    public function data(Request $request) {
        $result = Vehicles::select('vehicles.id', 'vehicles.vehicle_name', 'vehicles.license_plate_number', 'vehicles.payload', 'vehicles.active')
                ->where('vehicles.tenant_id', '=', auth()->user()->tenant_id);
        $result = $result->orderBy('vehicles.id', 'asc')->get();


        return DataTables::of($result)
                        ->addColumn('action', function ($row) {
                            return '<div class="btn-group m-r-10">
                            <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info btn-outline  dropdown-toggle waves-effect waves-light" type="button">Action <span class="caret"></span></button>
                            <ul role="menu" class="dropdown-menu">
                              <li><a href="' . route("admin.vehicles.edit", $row->id) . '" ><i class="fa fa-pencil"></i> Edit</a></li>
                              <li><a class="sa-params" href="javascript:;" data-row-id="' . $row->id . '"><i class="fa fa-times"></i> Delete</a></li>
                            </ul>
                          </div>';
                        })
                        ->rawColumns(['action'])
                        ->removeColumn('id')
                        ->make(true);
    }

    public function destroy($id) {
        Vehicles::destroy($id);
        return Reply::success(__('messages.vehiclesDeleted'));
    }

    public function export() {

        $result = Vehicles::select('vehicles.id', 'vehicles.vehicle_name', 'vehicles.license_plate_number', 'vehicles.category', 'vehicles.active', 'vehicles.created_at', 'vehicles.updated_at')
                ->where('vehicles.tenant_id', '=', auth()->user()->tenant_id);
        $result = $result->orderBy('vehicles.id', 'asc')->get();;

        // Initialize the array which will be passed into the Excel
        // generator.
        $exportArray = [];

        // Define the Excel spreadsheet headers
        $exportArray[] = ['ID', 'Vehicle Name', 'License Plate Number', 'Capacity in Tons', 'Active', 'Created At', 'Updated At'];

        // Convert each member of the returned collection into an array,
        // and append it to the payments array.
        foreach ($result as $row) {
            $exportArray[] = $row->toArray();
        }

        // Generate and return the spreadsheet
        Excel::create('vehicles', function ($excel) use ($exportArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Vehicles');
            $excel->setCreator('Website')->setCompany($this->companyName);
            $excel->setDescription('Vehicles file');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function ($sheet) use ($exportArray) {
                $sheet->fromArray($exportArray, null, 'A1', false, false);

                $sheet->row(1, function ($row) {

                    // call row manipulation methods
                    $row->setFont(array(
                        'bold' => true
                    ));
                });
            });
        })->download('xlsx');
    }

    public function vehicleUnavailability(){
        $this->pageTitle = "Vehicle Unavailability";
        $this->from_date = Carbon::parse(Carbon::now())->format($this->global->date_format);
        $this->to_date = Carbon::parse(Carbon::now()->addMonth())->format($this->global->date_format);

        $this->vehicles = Vehicles::select('id', 'vehicle_name')
            ->where('tenant_id', '=', auth()->user()->tenant_id)->where('active', '=', 'Y')
            ->get();
        $this->unavailable_vehicles = VehicleUnavailability::where(["tenant_id"=>auth()->user()->tenant_id])->get();
        return view('admin.vehicles.vehicle-unavailability', $this->data);
    }

    public function vehicleUnavailabilityData(Request $request){
            $result = VehicleUnavailability::select([
                'vehicle_unavailability.*',
                'vehicles.vehicle_name'
            ])
            ->join('vehicles', 'vehicles.id', '=', 'vehicle_unavailability.vehicle_id')
            ->where([
                    'vehicle_unavailability.tenant_id'=>auth()->user()->tenant_id,
                    'vehicle_unavailability.deleted'=>'N'
            ]);

                if ($request->from_date !== null && $request->from_date != 'null' && $request->from_date != '' && $request->from_date != 'undefined') {
                    $from_date = Carbon::createFromFormat($this->global->date_format, $request->from_date)->toDateString();
                    $result = $result->where(DB::raw('DATE(vehicle_unavailability.from_date)'), '>=', $from_date);
                }
                if ($request->to_date !== null && $request->to_date != 'null' && $request->to_date != '' && $request->to_date != 'undefined') {
                    $to_date = Carbon::createFromFormat($this->global->date_format, $request->to_date)->toDateString();
                    $result = $result->where(DB::raw('DATE(vehicle_unavailability.to_date)'), '<=', $to_date);
                }
                if ($request->vehicle !== null && $request->vehicle != 'null' && $request->vehicle != '') {
                    $vehicle_id = explode(",", $request->vehicle);
                    $result = $result->wherein('vehicle_unavailability.vehicle_id', $vehicle_id);
                }
                $result = $result->orderBy('vehicle_unavailability.id', 'DESC')->get();                    
                return DataTables::of($result)
                    ->addColumn('vehicle_name', function ($row) {
                        return $row->vehicle_name;
                    })
                    ->addColumn('from_date', function ($row) {
                        return date($this->global->date_format, strtotime($row->from_date));                    
                    })
                    ->addColumn('to_date', function ($row) {
                        return date($this->global->date_format, strtotime($row->to_date));                    
                    })
                    ->addColumn('from_time', function ($row) {
                        return date('h:i a', strtotime($row->from_time));
                    })
                    ->addColumn('to_time', function ($row) {
                        return date('h:i a', strtotime($row->to_time));
                    })
                    ->addColumn('reason', function ($row) {
                        return $row->reason; 
                    })
                    ->addColumn('action', function ($row) {
                        return '<div class="btn-group m-r-10">
                        <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info btn-outline  dropdown-toggle waves-effect waves-light" type="button">Action <span class="caret"></span></button>
                        <ul role="menu" class="dropdown-menu">
                          <li><a href="#" data-toggle="modal" data-target="#edit_unavailability_popup" class="edit-vehicle-unavailability" data-id="'.$row->id.'"><i class="fa fa-pencil"></i> Edit</a></li>
                          <li><a class="sa-params" href="javascript:;" data-row-id="' . $row->id . '"><i class="fa fa-times"></i> Delete</a></li>
                        </ul>
                      </div>';
                    })
                    ->rawColumns(['action'])
                    ->removeColumn('id')
                    ->make(true);
    }

    public function vehicleUnavailabilityDestroy($id) {
        vehicleUnavailability::destroy($id);
        return Reply::success('Vehicle Unavailability has been deleted.');
    }

    public function vehicleUnavailabilityStore(Request $request) {
        
        $validatedData = $request->validate([
            'vehicle_id' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
            'from_time' => 'required',
            'to_time' => 'required'
        ]);
        $data = $request->all();
        unset($data['_token']);
        $data['tenant_id'] = auth()->user()->tenant_id;
        $data['from_date'] = Carbon::createFromFormat('d/m/Y', $data['from_date'])->format('Y-m-d');
        $data['to_date'] = Carbon::createFromFormat('d/m/Y', $data['to_date'])->format('Y-m-d');
        $data['created_by'] = auth()->user()->id;
        $data['created_date'] = Carbon::now();
        //print_r($data);exit;
        $model = VehicleUnavailability::create($data);
        $response['error'] = 0;
        $response['message'] = "vehicle Unavailability has been created!";
        return json_encode($response);
    }

    public function vehicleUnavailabilityEdit($id) {
        $this->vehicles = Vehicles::select('id', 'vehicle_name')
            ->where('tenant_id', '=', auth()->user()->tenant_id)->where('active', '=', 'Y')
            ->get();
        $this->vehicle_unavailability = vehicleUnavailability::findOrFail($id);
        $this->vehicle_unavailability->from_date = Carbon::createFromFormat('Y-m-d', $this->vehicle_unavailability->from_date)->format('d/m/Y');
        $this->vehicle_unavailability->to_date = Carbon::createFromFormat('Y-m-d', $this->vehicle_unavailability->to_date)->format('d/m/Y');
        if($this->vehicle_unavailability){
            $response['error'] = 0;
            $response['html'] = view('admin.vehicles.vehicle-unavailability-edit', $this->data)->render();
        }else{
            $response['error'] = 1;
        }
        return json_encode($response);
    }

    public function vehicleUnavailabilityUpdate(Request $request) {

        $validatedData = $request->validate([
            'from_date' => 'required',
            'to_date' => 'required',
            'from_time' => 'required',
            'to_time' => 'required'
        ]);
        $data = $request->all();
        unset($data['_token'],$data['id']);
        $data['from_date'] = Carbon::createFromFormat('d/m/Y', $data['from_date'])->format('Y-m-d');
        $data['to_date'] = Carbon::createFromFormat('d/m/Y', $data['to_date'])->format('Y-m-d');
        $data['updated_by'] = auth()->user()->id;
        $data['updated_date'] = Carbon::now();
        VehicleUnavailability::where('id', $request->id)->update($data);
        $response['error'] = 0;
        $response['message'] = "vehicle Unavailability has been update!";
        return json_encode($response);
    }

    public function vehicleUnavailabilityCalender($id){
        $this->pageTitle = "Vehicle Unavailability Calender";
        $this->vehicles = Vehicles::select('id', 'vehicle_name')
            ->where('tenant_id', '=', auth()->user()->tenant_id)->where('active', '=', 'Y')
            ->get();
        $this->vehicle_id = $id;
        return view('admin.vehicles.vehicle-unavailability-calender', $this->data);
    }

    public function vehicleUnavailabilityCalenderData($id){

        $result = VehicleUnavailability::select([
            'vehicle_unavailability.*',
            'vehicles.vehicle_name'
        ])
        ->join('vehicles', 'vehicles.id', '=', 'vehicle_unavailability.vehicle_id')
        ->where([
                'vehicle_unavailability.tenant_id'=>auth()->user()->tenant_id,
                'vehicle_unavailability.deleted'=>'N'
        ]);
        if($id>0){
            $result = $result->where('vehicle_unavailability.vehicle_id',$id)->get();
        }else{
            $result = $result->get();
        }
        $unavailable_vehicles_list=[];
        if($result){
            foreach($result as $row){
                $unavailable_vehicles_list[] = array(
                    'id' => $row->id,
                    'allDay' => false,
                    'editable' => false,
                    'title' => $row->vehicle_name,
                    'mousehover_title' => $row->reason,                    
                    'start' => $row->from_date.'T'.$row->from_time,
                    'end' => $row->to_date.'T'.$row->to_time,
                    'displayEventTime' => true,
                    'displayEventEnd' => true,
                    'color' => '#546E7A'
                );
            }
        }
        return response()->json($unavailable_vehicles_list);
    }
}
