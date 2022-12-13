<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VehicleChecklistDefinition;
use App\VehicleChecklistGroup;

class VehiclesDailyChecklistController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.vehiclesDailyChecklist');
        $this->pageIcon = 'ti-reload';
    }
    
    public function index()
    {
        $this->group = VehicleChecklistGroup::where('tenant_id', auth()->user()->tenant_id)->get();
        return view('admin.vehicles-daily-checklist.index', $this->data);
    }

    public function store(Request $request)
    {
        if(VehicleChecklistGroup::where(['tenant_id' => auth()->user()->tenant_id, 'checklist_group' => $request->checklist_group])->first())
        {
            $response['error'] = 1;
            $response['message'] = 'This Checklist Group Already has been Created';
            return json_encode($response);
        }
        $model = new VehicleChecklistGroup();
        
        $model->tenant_id  = auth()->user()->tenant_id;        
        $model->checklist_group = $request->checklist_group;
        $model->created_at = time();
        $model->created_by = auth()->user()->id;
        if ($model->save()) {

            $group = VehicleChecklistGroup::where('tenant_id', auth()->user()->tenant_id)->get();

            $response['error'] = 0;
            $response['message'] = 'Group has been added';
            $response['chooseGroupList_html'] = view('admin.vehicles-daily-checklist.chooseGroupChecklist_grid')->with(['group' => $group])->render();
            $response['group_html'] = view('admin.vehicles-daily-checklist.group_grid')->with(['group' => $group])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function update(Request $request)
    {
        if(VehicleChecklistGroup::where(['tenant_id' => auth()->user()->tenant_id, 'checklist_group' => $request->checklist_group])->where('id', '!=', $request->group_id)->first())
        {
            $response['error'] = 1;
            $response['message'] = 'This Checklist Group Already has been Created';
            return json_encode($response);
        }
        $group_id = $request->group_id;
        $model = VehicleChecklistGroup::find($group_id);
        $model->checklist_group = $request->checklist_group;
        $model->updated_at = time();
        $model->updated_by = auth()->user()->id;
        if ($model->save()) {
            $group = VehicleChecklistGroup::where('tenant_id', auth()->user()->tenant_id)->get();

            $response['error'] = 0;
            $response['message'] = 'Group has been updated';
            $response['chooseGroupList_html'] = view('admin.vehicles-daily-checklist.chooseGroupChecklist_grid')->with(['group' => $group])->render();
            $response['group_html'] = view('admin.vehicles-daily-checklist.group_grid')->with(['group' => $group])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function delete(Request $request)
    {
        if(VehicleChecklistDefinition::where('group_id', $request->id)->first())
        {
            $response['error'] = 1;
            $response['message'] = 'This Checklist Group Connot Deleted Because it has some Definitions';
            return json_encode($response);
        }
        VehicleChecklistDefinition::where(['group_id' => $request->id, 'tenant_id' => auth()->user()->tenant_id])->delete();
        $group = VehicleChecklistGroup::where('tenant_id', auth()->user()->tenant_id)->get();

        $response['error'] = 0;
        $response['message'] = 'Group has been deleted';
        $response['chooseGroupList_html'] = view('admin.vehicles-daily-checklist.chooseGroupChecklist_grid')->with(['group' => $group])->render();
        $response['group_html'] = view('admin.vehicles-daily-checklist.group_grid')->with(['group' => $group])->render();
        return json_encode($response);
    }

    public function getGroup()
    {
        $group = VehicleChecklistGroup::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('checklist_group', 'asc')->get();
        $response = '';
        foreach ($group as $key) {
            $response.= '<option value="'.$key->id.'">'.$key->checklist_group.'</option>';
        }
        return json_encode($response);
    }

    public function loadGroupChecklist(Request $request)
    {
        $group_id = $request->group_id;

        if ($group_id) {

            $group = VehicleChecklistGroup::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('checklist_group', 'asc')->get();

            $group_checklist = VehicleChecklistDefinition::select(
                        'vehicle_checklist_definitions.id','vehicle_checklist_definitions.checklist', 'vehicle_checklist_definitions.group_id', 'vehicle_checklist_groups.checklist_group')
                        ->leftjoin('vehicle_checklist_groups', 'vehicle_checklist_groups.id', '=', 'vehicle_checklist_definitions.group_id')
                        ->where(['vehicle_checklist_definitions.tenant_id'=> auth()->user()->tenant_id, 'vehicle_checklist_definitions.group_id'=> $group_id])
                        ->orderBy('vehicle_checklist_groups.checklist_group', 'asc')
                        ->orderBy('vehicle_checklist_definitions.checklist', 'asc')
                        ->get();

            $response['error'] = 0;
            $response['message'] = 'Group Checklist has been added';
            $response['group_id'] = $request->input('group_id');
            $response['groupChecklist_html'] = view('admin.vehicles-daily-checklist.groupChecklist_grid')->with(['group' => $group, 'group_checklist' => $group_checklist])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function storeGroupChecklist(Request $request)
    {
        if(VehicleChecklistDefinition::where(['tenant_id' => auth()->user()->tenant_id, 'group_id' => $request->group_id, 'checklist' => $request->checklist])->first())
        {
            $response['error'] = 1;
            $response['message'] = 'This Checklist Already has been Created';
            return json_encode($response);
        }
        $model = new VehicleChecklistDefinition();
        
        $model->tenant_id  = auth()->user()->tenant_id;
        $model->group_id = $request->group_id;        
        $model->checklist = $request->checklist;
        $model->created_at = time();
        $model->created_by = auth()->user()->id;
        if ($model->save()) {
            $group = VehicleChecklistGroup::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('checklist_group', 'asc')->get();

            $group_checklist = VehicleChecklistDefinition::select(
                        'vehicle_checklist_definitions.id','vehicle_checklist_definitions.checklist', 'vehicle_checklist_definitions.group_id', 'vehicle_checklist_groups.checklist_group')
                        ->leftjoin('vehicle_checklist_groups', 'vehicle_checklist_groups.id', '=', 'vehicle_checklist_definitions.group_id')
                        ->where(['vehicle_checklist_definitions.tenant_id'=> auth()->user()->tenant_id, 'vehicle_checklist_definitions.group_id'=> $request->group_id])
                        ->orderBy('vehicle_checklist_groups.checklist_group', 'asc')
                        ->orderBy('vehicle_checklist_definitions.checklist', 'asc')
                        ->get();

            $response['error'] = 0;
            $response['message'] = 'Group Checklist has been added';
            $response['group_id'] = $request->input('group_id');
            $response['groupChecklist_html'] = view('admin.vehicles-daily-checklist.groupChecklist_grid')->with(['group' => $group, 'group_checklist' => $group_checklist])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function updateGroupChecklist(Request $request)
    {
        if(VehicleChecklistDefinition::where(['tenant_id' => auth()->user()->tenant_id, 'group_id' => $request->group_id, 'checklist' => $request->checklist])->where('id', '!=', $request->update_id)->first())
        {
            $response['error'] = 1;
            $response['message'] = 'This Checklist Already has been Created';
            return json_encode($response);
        }
        $group_id = $request->group_id;
        $group_checklist_id = $request->update_id;

        $model = VehicleChecklistDefinition::find($group_checklist_id);
        $model->group_id = $group_id;
        $model->checklist = $request->checklist;
        $model->updated_at = time();
        $model->updated_by = auth()->user()->id;
        if ($model->save()) {
            $group = VehicleChecklistGroup::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('checklist_group', 'asc')->get();

            $group_checklist = VehicleChecklistDefinition::select(
                        'vehicle_checklist_definitions.id','vehicle_checklist_definitions.checklist', 'vehicle_checklist_definitions.group_id', 'vehicle_checklist_groups.checklist_group')
                        ->leftjoin('vehicle_checklist_groups', 'vehicle_checklist_groups.id', '=', 'vehicle_checklist_definitions.group_id')
                        ->where(['vehicle_checklist_definitions.tenant_id'=> auth()->user()->tenant_id, 'vehicle_checklist_definitions.group_id'=> $request->selected_group_id])
                        ->orderBy('vehicle_checklist_groups.checklist_group', 'asc')
                        ->orderBy('vehicle_checklist_definitions.checklist', 'asc')
                        ->get();

            $response['error'] = 0;
            $response['message'] = 'Group Checklist has been updated';
            $response['group_id'] = $request->input('group_id');
            $response['groupChecklist_html'] = view('admin.vehicles-daily-checklist.groupChecklist_grid')->with(['group' => $group, 'group_checklist' => $group_checklist])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function deleteGroupChecklist(Request $request)
    {
        VehicleChecklistDefinition::destroy($request->id);
        $group = VehicleChecklistGroup::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('checklist_group', 'asc')->get();

        $group_checklist = VehicleChecklistDefinition::select(
                    'vehicle_checklist_definitions.id','vehicle_checklist_definitions.checklist', 'vehicle_checklist_definitions.group_id', 'vehicle_checklist_groups.checklist_group')
                    ->leftjoin('vehicle_checklist_groups', 'vehicle_checklist_groups.id', '=', 'vehicle_checklist_definitions.group_id')
                    ->where(['vehicle_checklist_definitions.tenant_id'=> auth()->user()->tenant_id, 'vehicle_checklist_definitions.group_id'=> $request->selected_group_id])
                    ->orderBy('vehicle_checklist_groups.checklist_group', 'asc')
                    ->orderBy('vehicle_checklist_definitions.checklist', 'asc')
                    ->get();

        $response['error'] = 0;
        $response['message'] = 'Group Checklist has been deleted';
        $response['group_id'] = $request->selected_group_id;
        $response['groupChecklist_html'] = view('admin.vehicles-daily-checklist.groupChecklist_grid')->with(['group' => $group, 'group_checklist' => $group_checklist])->render();
        return json_encode($response);
    }
}
