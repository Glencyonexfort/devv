<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\OHSChecklist;
use Illuminate\Support\Facades\DB;

class OHSChecklistController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.ohsChecklist');
        $this->pageIcon = 'ti-file';
    }

    public function index()
    {
        $this->checklist = DB::table('ohs_checklist_definitions')->where('tenant_id', auth()->user()->tenant_id)->get();
        return view('admin.ohs-checklist.index', $this->data);
    }

    public function store(Request $request)
    {
        $model = new OHSChecklist();
        
        $model->tenant_id  = auth()->user()->tenant_id;        
        $model->checklist = $request->checklist;
        $model->created_at = time();
        $model->created_by = auth()->user()->id;
        if ($model->save()) {

            $checklist = DB::table('ohs_checklist_definitions')->where('tenant_id', auth()->user()->tenant_id)->get();

            $response['error'] = 0;
            $response['message'] = 'Checklist has been added';
            $response['checklist_html'] = view('admin.ohs-checklist.checklist_grid')->with(['checklist' => $checklist])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function update(Request $request)
    {
        $local_moves_id = $request->local_moves_id;
        $model = OHSChecklist::find($local_moves_id);
        $model->checklist = $request->checklist;
        $model->updated_at = time();
        //print_r($model);exit;
        if ($model->save()) {
            $checklist = DB::table('ohs_checklist_definitions')->where('tenant_id', auth()->user()->tenant_id)->get();

            $response['error'] = 0;
            $response['message'] = 'Checklist has been updated';
            $response['checklist_html'] = view('admin.ohs-checklist.checklist_grid')->with(['checklist' => $checklist])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function delete(Request $request)
    {
        OHSChecklist::destroy($request->id);
        $checklist = DB::table('ohs_checklist_definitions')->where('tenant_id', auth()->user()->tenant_id)->get();

        $response['error'] = 0;
        $response['message'] = 'Checklist has been deleted';
        $response['checklist_html'] = view('admin.ohs-checklist.checklist_grid')->with(['checklist' => $checklist])->render();
        return json_encode($response);
    }
}
