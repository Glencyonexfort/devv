<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VehicleGroups;
use App\Helper\Reply;

class ManageVehicleGroupsController extends AdminBaseController
{
   public function __construct() {
        parent::__construct();
        $this->pageTitle = __('app.menu.vehicleGroups');
        $this->pageIcon = 'ti-file';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $this->vehicleGroups = VehicleGroups::where(['tenant_id'=> auth()->user()->tenant_id, 'deleted'=>0])->orderBy('group_name', 'asc')->get();

        return view('admin.vehicle-groups.index', $this->data);
    }

    public function ajaxCreateVehicleGroup(Request $request)
    {
        $group_name = $request->input('group_name');

        if (VehicleGroups::where(['tenant_id'=> auth()->user()->tenant_id])->where('group_name', '=', $group_name)->exists()) {
            $response['error'] = 1;
            $response['message'] = 'Vehicle Group already exist.';
            return json_encode($response);
         }

        $model = new VehicleGroups();
        $model->tenant_id  = auth()->user()->tenant_id;        
        $model->group_name = $group_name;
        $model->created_at = time();
        if ($model->save()) {
            $vehicleGroups = VehicleGroups::where(['tenant_id'=> auth()->user()->tenant_id, 'deleted'=>0])->orderBy('id', 'asc')->get();
            $response['error'] = 0;
            $response['message'] = 'Vehicle Group has been added';
            $response['vehicleGroups_html'] = view('admin.vehicle-groups.groupname_grid')->with(['vehicleGroups' => $vehicleGroups])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxUpdateVehicleGroup(Request $request)
    {
        $vehiclegroupid = $request->input('vehiclegroupid');
        $group_name = $request->input('group_name');

        $model = VehicleGroups::find($vehiclegroupid);
        $model->group_name = $group_name;
        $model->updated_at = time();
        if ($model->save()) {
            $vehicleGroups = VehicleGroups::where(['tenant_id'=> auth()->user()->tenant_id, 'deleted'=>0])->orderBy('id', 'asc')->get();
            $response['error'] = 0;
            $response['id'] = $vehiclegroupid;
            $response['message'] = 'Vehicle Group has been updated';
            $response['vehicleGroups_html'] = view('admin.vehicle-groups.groupname_grid')->with(['vehicleGroups' => $vehicleGroups])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxDestroyVehicleGroup(Request $request)
    {
        $obj = VehicleGroups::findOrFail($request->id);
        $obj->deleted = 1;
        $obj->updated_at = time();
        $obj->save();
        $vehicleGroups = VehicleGroups::where(['tenant_id'=> auth()->user()->tenant_id, 'deleted'=>0])->orderBy('group_name', 'asc')->get();
        $response['error'] = 0;
        $response['message'] = 'Vehicle Group has been deleted';
        $response['vehicleGroups_html'] = view('admin.vehicle-groups.groupname_grid')->with(['vehicleGroups' => $vehicleGroups])->render();
        return json_encode($response);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
