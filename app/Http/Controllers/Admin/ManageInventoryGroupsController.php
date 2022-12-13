<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MovingInventoryGroups;
use App\Helper\Reply;

class ManageInventoryGroupsController extends AdminBaseController
{
   public function __construct() {
        parent::__construct();
        $this->pageTitle = __('app.menu.inventoryGroups');
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

        return view('admin.inventory-groups.index', $this->data);
    }

    public function ajaxCreateInventoryGroup(Request $request)
    {
        $getlastGroupId = MovingInventoryGroups::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('group_id', 'desc')->first();

        $model = new MovingInventoryGroups();
        $model->tenant_id  = auth()->user()->tenant_id;        
        $model->group_name = $request->input('group_name');
        if($getlastGroupId){
            $model->group_id   = ($getlastGroupId->group_id)+1;
        } else {
            $model->group_id   = 1;
        }
        $model->created_at = time();
        //print_r($model);exit;
        if ($model->save()) {
            $inventoryGroups = MovingInventoryGroups::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('group_name', 'asc')->get();

            $response['error'] = 0;
            //$response['id'] = $local_moves_id;
            $response['message'] = 'Inventory Group has been added';
            $response['inventorygroups_html'] = view('admin.inventory-groups.groupname_grid')->with(['inventoryGroups' => $inventoryGroups])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxUpdateInventoryGroup(Request $request)
    {
        $local_moves_id = $request->input('local_moves_id');
        $model = MovingInventoryGroups::find($local_moves_id);
        $model->group_name = $request->input('group_name');
        $model->updated_at = time();
        //print_r($model);exit;
        if ($model->save()) {
            $inventoryGroups = MovingInventoryGroups::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('group_name', 'asc')->get();

            $response['error'] = 0;
            $response['id'] = $local_moves_id;
            $response['message'] = 'Inventory Group has been updated';
            $response['inventorygroups_html'] = view('admin.inventory-groups.groupname_grid')->with(['inventoryGroups' => $inventoryGroups])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxDestroyInventoryGroup(Request $request)
    {
        MovingInventoryGroups::destroy($request->id);
        $inventoryGroups = MovingInventoryGroups::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('group_name', 'asc')->get();
        $response['error'] = 0;
        $response['message'] = 'Inventory Group has been deleted';
        $response['inventorygroups_html'] = view('admin.inventory-groups.groupname_grid')->with(['inventoryGroups' => $inventoryGroups])->render();
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
