<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\JobsMoving;
use App\StorageTypes;
use App\StorageUnitAllocation;
use App\StorageUnits;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StorageController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.storage');
        $this->pageIcon = 'icon-new-tab';
    }

    public function index()
    {
        $this->storage_types = StorageTypes::where('tenant_id', '=', auth()->user()->tenant_id)
            ->where(['active' => '1', 'deleted' => '0'])->get();
        $this->allocation_status = ['Reserved', 'Occupied'];
        return view('admin.storage.index', $this->data);
    }
    public function data(Request $request)
    {
        //print_r($request->all());exit;
        try {
        $result = StorageUnitAllocation::select([
            'storage_unit_allocation.*',
            'storage_units.serial_number as unit_no',
            'storage_units.serial_number as unit_no',
            'jobs_moving.job_number as job_no',
            'crm_leads.name as customer_name'
        ])
            ->where([
                'storage_unit_allocation.tenant_id'=>auth()->user()->tenant_id,
                'storage_unit_allocation.deleted'=>'0'
            ])
            ->join('storage_units', 'storage_units.id', '=', 'storage_unit_allocation.unit_id')
            ->join('jobs_moving', 'jobs_moving.job_id', '=', 'storage_unit_allocation.job_id')
            ->join('crm_leads', 'crm_leads.id', '=', 'jobs_moving.customer_id');
            //->join('crm_leads', 'crm_leads.id', '=', 'crm_opportunities.lead_id');
            if ($request->from_date !== null && $request->from_date != 'null' && $request->from_date != '' && $request->from_date != 'undefined') {
                $from_date = Carbon::createFromFormat($this->global->date_format, $request->from_date)->toDateString();
                $result = $result->where(DB::raw('DATE(storage_unit_allocation.from_date)'), '=', $from_date);
            }
            if ($request->to_date !== null && $request->to_date != 'null' && $request->to_date != '' && $request->to_date != 'undefined') {
                $to_date = Carbon::createFromFormat($this->global->date_format, $request->to_date)->toDateString();
                $result = $result->where(DB::raw('DATE(storage_unit_allocation.to_date)'), '=', $to_date);
            }
            if ($request->storage_unit !== null && $request->storage_unit != 'null' && $request->storage_unit != '') {
                $storage_unit = explode(",", $request->storage_unit);
                $result = $result->wherein('storage_unit_allocation.unit_id', $storage_unit);
            }
            if ($request->allocation_status !== null && $request->allocation_status != 'null' && $request->allocation_status != '') {
                $allocation_status = explode(",", $request->allocation_status);
                $result = $result->wherein('storage_unit_allocation.allocation_status', $allocation_status);
            }
            $result = $result->orderBy('storage_unit_allocation.id', 'DESC')->get();                    
            return DataTables::of($result)
                ->addColumn('unit_no', function ($row) {
                    return $row->unit_no;
                })
                ->addColumn('allocation_status', function ($row) {
                    return $row->allocation_status;
                })
                ->addColumn('job_type', function ($row) {
                    return $row->job_type;
                })
                ->addColumn('job_no', function ($row) {
                    return $row->job_no;
                })
                ->addColumn('customer_name', function ($row) {
                    return $row->customer_name;
                })
                ->addColumn('from_date', function ($row) {
                    return date('d/m/Y', strtotime($row->from_date));                    
                })
                ->addColumn('to_date', function ($row) {
                    return date('d/m/Y', strtotime($row->to_date));                    
                })
                ->make(true);
         } catch (Exception $ex) {
             dd($ex->getMessage());
         }
    }
    public function getStorageUnitList(Request $request)
    {
        $storage_types = $request->storage_type;
        $storage_units_list = StorageUnits::select("storage_units.*")
        ->where(['storage_units.deleted'=>'0','storage_units.active'=>'1',
                'storage_units.tenant_id'=> auth()->user()->tenant_id
                ])
        ->wherein('storage_units.storage_type_id', $storage_types)
        ->orderBy('storage_units.serial_number', 'ASC')->get();
        if(count($storage_units_list)>0){
            return json_encode(['error'=>0, 'data'=>$storage_units_list]);
        }else{
            return json_encode(['error'=>1,'message'=>'No Storage Unit Found!']);
        }
    }
    // START:: Storage Types ---------------->
    public function storageTypes()
    {
        return view('admin.storage.storage-types.index', $this->data);
    }
    public function storageTypesCreate()
    {
        return view('admin.storage.storage-types.create', $this->data);
    }
    public function storageTypesStore(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required',
            'inside_cubic_capacity' => 'required|numeric',
            'max_gross_weight_kg' => 'required|numeric',
            'tare_weight_kg' => 'required|numeric',
            'ext_length_m' => 'required|numeric',
            'ext_width_m' => 'required|numeric',
            'ext_height_m' => 'required|numeric',
            'int_length_m' => 'required|numeric',
            'int_width_m' => 'required|numeric',
            'int_height_m' => 'required|numeric',
        ]);
        $data = [
            'name' => $request->name,
            'inside_cubic_capacity' => $request->inside_cubic_capacity,
            'max_gross_weight_kg' => $request->max_gross_weight_kg,
            'tare_weight_kg' => $request->tare_weight_kg,
            'ext_length_m' => $request->ext_length_m,
            'ext_width_m' => $request->ext_width_m,
            'ext_height_m' => $request->ext_height_m,
            'int_length_m' => $request->int_length_m,
            'int_width_m' => $request->int_width_m,
            'int_height_m' => $request->int_height_m,
            'tenant_id' => auth()->user()->tenant_id,
            'created_by' => auth()->user()->id,
            'created_date' => Carbon::now(),
            'updated_date' => Carbon::now(),
        ];
        $modal = StorageTypes::create($data);

        return Reply::redirect(route('admin.storage-types'), __('messages.storageTypeCreated'));
    }
    public function storageTypesEdit($id)
    {
        $this->storage_type = StorageTypes::find($id);
        return view('admin.storage.storage-types.edit', $this->data);
    }
    public function storageTypesUpdate(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required',
            'inside_cubic_capacity' => 'required|numeric',
            'max_gross_weight_kg' => 'required|numeric',
            'tare_weight_kg' => 'required|numeric',
            'ext_length_m' => 'required|numeric',
            'ext_width_m' => 'required|numeric',
            'ext_height_m' => 'required|numeric',
            'int_length_m' => 'required|numeric',
            'int_width_m' => 'required|numeric',
            'int_height_m' => 'required|numeric',
        ]);
        $data = $request->all();
        $id = $data['id'];
        unset($data['_token'], $data['id']);
        $data['active'] = ($data['active'] == 'Y' ? '1' : '0');
        $data['updated_by'] = auth()->user()->id;
        $data['updated_date'] = Carbon::now();
        $modal = StorageTypes::where('id', $id)->update($data);

        return Reply::redirect(route('admin.storage-types'), __('messages.storageTypeUpdated'));
    }
    public function storageTypesDestroy($id)
    {
        $storage_unit_count = StorageUnits::where('storage_type_id', $id)->count();
        if ($storage_unit_count == 0) {
            $modal = StorageTypes::where('id', $id)->update(['deleted' => '1']);
            return Reply::success(__('messages.storageTypeDeleted'));
        } else {
            return Reply::error(__('Storage Type cannot be deleted!'));
        }

    }
    public function storageTypesData()
    {
        $result = StorageTypes::where('tenant_id', '=', auth()->user()->tenant_id)
            ->where(['deleted' => '0']);
        $result = $result->orderBy('name', 'ASC')->get();

        return DataTables::of($result)
            ->addColumn('action', function ($row) {
                return '<div class="btn-group m-r-10">
                            <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info btn-outline  dropdown-toggle waves-effect waves-light" type="button">Action <span class="caret"></span></button>
                            <ul role="menu" class="dropdown-menu">
                              <li><a href="' . route("admin.storage-types.edit", $row->id) . '" ><i class="fa fa-pencil"></i> Edit</a></li>
                              <li><a class="sa-params" href="javascript:;" data-row-id="' . $row->id . '"><i class="fa fa-times"></i> Delete</a></li>
                            </ul>
                          </div>
                          ';
            })
            ->editColumn('storage_type', function ($row) {
                return ucfirst($row->name);
            })
            ->editColumn('dimensions', function ($row) {
                $dimension = $row->ext_length_m . ' X ' . $row->ext_width_m . ' X ' . $row->ext_height_m;
                return $dimension;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    // END:: Storage Types ----------------->
    // START:: Storage Units ----------------->
    public function storageUnits()
    {
        return view('admin.storage.storage-units.index', $this->data);
    }
    public function storageUnitsCreate()
    {
        $this->storage_types = StorageTypes::where('tenant_id', '=', auth()->user()->tenant_id)
            ->where(['active' => '1', 'deleted' => '0'])->orderBy('name', 'ASC')->get();
        return view('admin.storage.storage-units.create', $this->data);
    }
    public function storageUnitsStore(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'serial_number' => 'required|numeric',
            'storage_type_id' => 'required|numeric',
            'manufacturer_serial_number' => 'required',
        ]);
        $data = [
            'name' => $request->name,
            'serial_number' => $request->serial_number,
            'storage_type_id' => $request->storage_type_id,
            'manufacturer_serial_number' => $request->manufacturer_serial_number,
            'tenant_id' => auth()->user()->tenant_id,
            'active' => ($request->active == 'Y' ? '1' : '0'),
            'created_by' => auth()->user()->id,
            'created_date' => Carbon::now(),
            'updated_date' => Carbon::now(),
        ];
        $modal = StorageUnits::create($data);

        return Reply::redirect(route('admin.storage-units'), __('messages.storageUnitCreated'));
    }
    public function storageUnitsEdit($id)
    {
        $this->storage_types = StorageTypes::where('tenant_id', '=', auth()->user()->tenant_id)
            ->where(['active' => '1', 'deleted' => '0'])->orderBy('name', 'ASC')->get();
        $this->storage_unit = StorageUnits::find($id);
        return view('admin.storage.storage-units.edit', $this->data);
    }
    public function storageUnitsUpdate(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required',
            'serial_number' => 'required|numeric',
            'storage_type_id' => 'required|numeric',
            'manufacturer_serial_number' => 'required',
        ]);
        $data = $request->all();
        $id = $data['id'];
        unset($data['_token'], $data['id']);
        $data['active'] = (isset($data['active']) && ($data['active'] == 'Y') ? '1' : '0');
        $data['updated_by'] = auth()->user()->id;
        $data['updated_date'] = Carbon::now();
        $modal = StorageUnits::where('id', $id)->update($data);

        return Reply::redirect(route('admin.storage-units'), __('messages.storageUnitUpdated'));
    }
    public function storageUnitsDestroy($id)
    {
        $storage_allocation_count = StorageUnitAllocation::where(['unit_id' => $id, 'deleted' => '0'])->count();
        if ($storage_allocation_count == 0) {
            $modal = StorageUnits::where('id', $id)->update(['deleted' => '1']);
            return Reply::success(__('messages.storageUnitDeleted'));
        } else {
            return Reply::error(__('Storage Unit cannot be deleted!'));
        }

    }
    public function storageUnitsData()
    {
        $result = StorageUnits::select("storage_units.*", "storage_types.name as type_name")
            ->join('storage_types', 'storage_types.id', 'storage_units.storage_type_id')
            ->where('storage_units.tenant_id', '=', auth()->user()->tenant_id)
            ->where(['storage_units.deleted' => '0'])
            ->orderBy('storage_units.serial_number', 'ASC')->get();

        return DataTables::of($result)
            ->addColumn('action', function ($row) {
                return '<div class="btn-group m-r-10">
                            <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info btn-outline  dropdown-toggle waves-effect waves-light" type="button">Action <span class="caret"></span></button>
                            <ul role="menu" class="dropdown-menu">
                              <li><a href="' . route("admin.storage-units.edit", $row->id) . '" ><i class="fa fa-pencil"></i> Edit</a></li>
                              <li><a class="sa-params" href="javascript:;" data-row-id="' . $row->id . '"><i class="fa fa-times"></i> Delete</a></li>
                            </ul>
                          </div>
                          ';
            })
            ->editColumn('serial_number', function ($row) {
                return ucfirst($row->serial_number);
            })
            ->editColumn('storage_type', function ($row) {
                return ucfirst($row->type_name);
            })
            ->editColumn('active', function ($row) {
                return ($row->active == 1) ? 'Y' : 'N';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    // END:: Storage Units ----------------->

    public function findAvailableStorageUnits(Request $request)
    {
        $storage_type = $request->storage_unit_id;
        $from = Carbon::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d');
        $date_result = Carbon::parse($from)->gt($to);
        if ($date_result && $from != $to) {
            $response['error'] = 1;
            $response['message'] = "End Date should be equal to or greater than the Start Date!";
        } else {
            $data = StorageUnits::getAvailableUnits($storage_type, $from, $to);
            if (count($data) > 0) {
                $response['data'] = $data;
                $response['error'] = 0;
                $response['message'] = "Available Storage Units Found!";
            } else {
                $response['data'] = $data;
                $response['error'] = 1;
                $response['message'] = "No Available Storage Units!";
            }
        }
        return json_encode($response);
    }

    public function ajaxSaveStorageReservation(Request $request)
    {
        $crm_opportunity_id = $request->crm_opportunity_id;
        $lead_id = $request->lead_id;
        $from = Carbon::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d');
        $date_result = Carbon::parse($from)->gt($to);
        if ($date_result && $from != $to) {
            $response['error'] = 1;
            $response['message'] = "To Date should be equal to or greater than the From Date!";
        } else {
            $unit_id = $request->unit_id;
            $allocation_status = $request->allocation_status;
            $is_ok = StorageUnits::checkUnitAvailability($unit_id, $from, $to);
            if ($is_ok == true) {
                if($lead_id==0){ // If it is from Job page
                    $job_id = $request->job_id;
                }else{
                    //If it is from Lead Page
                    $job_id = JobsMoving::where(['crm_opportunity_id' => $crm_opportunity_id])->pluck("job_id")->first();
                }
                $data = [
                    'name' => $request->name,
                    'job_id' => $job_id,
                    'unit_id' => $unit_id,
                    'job_type' => 'Moving',
                    'allocation_status' => $allocation_status,
                    'from_date' => $from,
                    'to_date' => $to,
                    'tenant_id' => auth()->user()->tenant_id,
                    'created_by' => auth()->user()->id,
                    'created_date' => Carbon::now(),
                    'updated_date' => Carbon::now(),
                ];
                $modal = StorageUnitAllocation::create($data);
                $response['error'] = 0;
                $response['message'] = "Reservation has been saved!";

            } else {
                $response['error'] = 1;
                $response['message'] = "Storage Unit already reserved in giving date range";
            }
        }
        return json_encode($response);
    }
    public function ajaxDestroyStorageReservation(Request $request)
    {
        $modal = StorageUnitAllocation::where('id', $request->id)->update(['deleted' => '1']);
        $response['error'] = 0;
        $response['message'] = "Reservation has been deleted!";
        return json_encode($response);
    }
}
