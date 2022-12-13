<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MovingInventoryGroups;
use App\MovingInventoryDefinitions;
use App\Helper\Reply;

class ManageInventoryDefinitionsController extends AdminBaseController
{
   public function __construct() {
        parent::__construct();
        $this->pageTitle = __('app.menu.inventoryDefinitions');
        $this->pageIcon = 'ti-file';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $this->inventoryGroups = MovingInventoryGroups::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('group_name', 'asc')->get();

        $this->inventoryDefinitions = MovingInventoryDefinitions::select(
                        'moving_inventory_groups.group_name','moving_inventory_definitions.id', 'moving_inventory_definitions.group_id','moving_inventory_definitions.item_name', 'moving_inventory_definitions.cbm', 'moving_inventory_definitions.special_item', 'moving_inventory_definitions.notes')
                        ->join('moving_inventory_groups', 'moving_inventory_groups.id', '=', 'moving_inventory_definitions.group_id')
                        ->where(['moving_inventory_definitions.tenant_id'=>auth()->user()->tenant_id])
                        ->orderBy('moving_inventory_groups.group_name', 'asc')
                        ->orderBy('moving_inventory_definitions.item_name', 'asc')
                        ->get();

        //dd(count($this->inventoryDefinitions));

        return view('admin.inventory-definitions.index', $this->data);
    }

    public function ajaxCreateInventoryDefinition(Request $request)
    {

        $model = new MovingInventoryDefinitions();
        $model->tenant_id      = auth()->user()->tenant_id;        
        $model->item_name      = $request->input('item_name');
        $model->group_id       = $request->input('group_id');
        $model->cbm            = $request->input('cbm');
        $model->special_item   = $request->input('special_item');
        $model->created_at     = time();
        //print_r($model);exit;
        if ($model->save()) {
            
            $inventoryGroups = MovingInventoryGroups::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('group_name', 'asc')->get();

            $inventoryDefinitions = MovingInventoryDefinitions::select(
                        'moving_inventory_groups.group_name','moving_inventory_definitions.id', 'moving_inventory_definitions.group_id','moving_inventory_definitions.item_name', 'moving_inventory_definitions.cbm', 'moving_inventory_definitions.special_item', 'moving_inventory_definitions.notes')
                        ->join('moving_inventory_groups', 'moving_inventory_groups.id', '=', 'moving_inventory_definitions.group_id')
                        ->where(['moving_inventory_definitions.tenant_id'=>auth()->user()->tenant_id])
                        ->orderBy('moving_inventory_groups.group_name', 'asc')
                        ->orderBy('moving_inventory_definitions.item_name', 'asc')
                        ->get();


            $response['error'] = 0;
            //$response['id'] = $local_moves_id;
            $response['message'] = 'Inventory Definition has been added';
            $response['inventoryDefinitions_html'] = view('admin.inventory-definitions.inventorydefinition_grid')->with(['inventoryGroups' => $inventoryGroups, 'inventoryDefinitions' => $inventoryDefinitions])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxUpdateInventoryDefinition(Request $request)
    {
        $updateid = $request->input('updateid');
        $model = MovingInventoryDefinitions::find($updateid);
        $model->item_name      = $request->input('item_name');
        $model->group_id       = $request->input('group_id');
        $model->cbm            = $request->input('cbm');
        $model->special_item   = $request->input('special_item');
        $model->updated_at = time();
        //print_r($model);exit;
        if ($model->save()) {
            $inventoryGroups = MovingInventoryGroups::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('group_name', 'asc')->get();

            $inventoryDefinitions = MovingInventoryDefinitions::select(
                        'moving_inventory_groups.group_name','moving_inventory_definitions.id', 'moving_inventory_definitions.group_id','moving_inventory_definitions.item_name', 'moving_inventory_definitions.cbm', 'moving_inventory_definitions.special_item', 'moving_inventory_definitions.notes')
                        ->join('moving_inventory_groups', 'moving_inventory_groups.id', '=', 'moving_inventory_definitions.group_id')
                        ->where(['moving_inventory_definitions.tenant_id'=>auth()->user()->tenant_id])
                        ->orderBy('moving_inventory_groups.group_name', 'asc')
                        ->orderBy('moving_inventory_definitions.item_name', 'asc')
                        ->get();

            $response['error'] = 0;
            $response['id'] = $updateid;
            $response['message'] = 'Inventory Definition has been updated';
            $response['inventoryDefinitions_html'] = view('admin.inventory-definitions.inventorydefinition_grid')->with(['inventoryGroups' => $inventoryGroups, 'inventoryDefinitions' => $inventoryDefinitions])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxDestroyInventorDefinition(Request $request)
    {
        MovingInventoryDefinitions::destroy($request->id);
        
        $inventoryGroups = MovingInventoryGroups::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('group_name', 'asc')->get();

        $inventoryDefinitions = MovingInventoryDefinitions::select(
                        'moving_inventory_groups.group_name','moving_inventory_definitions.id', 'moving_inventory_definitions.group_id','moving_inventory_definitions.item_name', 'moving_inventory_definitions.cbm', 'moving_inventory_definitions.special_item', 'moving_inventory_definitions.notes')
                        ->join('moving_inventory_groups', 'moving_inventory_groups.id', '=', 'moving_inventory_definitions.group_id')
                        ->where(['moving_inventory_definitions.tenant_id'=>auth()->user()->tenant_id])
                        ->orderBy('moving_inventory_groups.group_name', 'asc')
                        ->orderBy('moving_inventory_definitions.item_name', 'asc')
                        ->get();

        $response['error'] = 0;
        $response['message'] = 'Inventory Definition has been deleted';
        $response['inventoryDefinitions_html'] = view('admin.inventory-definitions.inventorydefinition_grid')->with(['inventoryGroups' => $inventoryGroups, 'inventoryDefinitions' => $inventoryDefinitions])->render();
        return json_encode($response);
    }
    
}
