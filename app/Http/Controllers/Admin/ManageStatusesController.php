<?php

namespace App\Http\Controllers\Admin;

use App\CRMLeads;
use App\CRMLeadStatuses;
use App\CRMOpPipelines;
use App\Helper\Reply;
use App\CRMOpPipelineStatuses;
use App\CRMOpportunities;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ManageStatusesController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.statuses');
        $this->pageIcon = 'ti-file';
    }

    public function index()
    {
        $this->op_pipelines = CRMOpPipelines::all();
        return view('admin.statuses.index', $this->data);
    }

    // lead statuses
    public function store(Request $request)
    {
        $max = CRMLeadStatuses::where('crm_lead_statuses.tenant_id', '=', auth()->user()->tenant_id)->max('sort_order');
        $obj = new CRMLeadStatuses();
        $obj->lead_status = $request->input('lead_status');
        $obj->created_date = time();
        $obj->updated_date = time();
        $obj->sort_order = intval($max) + 1;
        $obj->tenant_id = auth()->user()->tenant_id;
        $obj->save();

        return response()->json([
            'error' => '0',
            'message' => __('messages.leadStatusesCreated')
        ]);
    }

    public function edit($id)
    {
        $response['error'] = 1;
        $this->row = CRMLeadStatuses::where(['id' => $id])->first();
        if ($this->row) {
            $html = view('admin.statuses.lead.edit', $this->data)->render();
            $response['error'] = 0;
            $response['html'] = $html;
        }
        return json_encode($response);
    }

    public function update(Request $request, $id)
    {
        $id = $request->input('lead_status_id');
        $obj = CRMLeadStatuses::findOrFail($id);
        $old_status = $obj->lead_status;
        $obj->lead_status = $request->input('lead_status');
        $obj->updated_date = time();
        $obj->save();


        $crm_leads = CRMLeads::where('tenant_id', '=', auth()->user()->tenant_id)->where('lead_status', '=', $old_status)->count('*');
        if ($crm_leads > 0) {
            CRMLeads::where('tenant_id', '=', auth()->user()->tenant_id)
                ->where('lead_status', '=', $old_status)
                ->update(['lead_status' => $request->input('lead_status')]);
        }

        return response()->json([
            'error' => '0',
            'message' => __('messages.leadStatusesUpdated')
        ]);
    }

    public function data(Request $request)
    {
        $result = CRMLeadStatuses::select('crm_lead_statuses.id', 'crm_lead_statuses.sort_order', 'crm_lead_statuses.lead_status')
            ->where('crm_lead_statuses.tenant_id', '=', auth()->user()->tenant_id)
            ->orderBy('crm_lead_statuses.sort_order', 'asc')->get();


        return DataTables::of($result)
            ->addColumn('action', function ($row) {
                $crm_leads = CRMLeads::where('tenant_id', '=', auth()->user()->tenant_id)
                    ->where('lead_status', '=', $row->lead_status)->count('*');
                if ($crm_leads > 0) {
                    return '<div class="btn-group">
                    <a class="btn btn-sm btn-primary m-r-10 leadStatuses-edit-btn" data-toggle="modal" data-target="#edit_lead_status" data-row-id="' . $row->id . '" href="#" ><i class="fa fa-pencil"></i></a>
                    <a class="btn btn-sm btn-danger leadStatuses-delete-btn" href="javascript:;"  data-toggle="modal" data-target="#delete_lead_status" data-row-id="' . $row->id . '"  data-row-status="' . $row->lead_status . '"><i class="fa fa-trash"></i></a>
                    </div>';
                } else {
                    return '<div class="btn-group">
                    <a class="btn btn-sm btn-primary m-r-10 leadStatuses-edit-btn" data-toggle="modal" data-target="#edit_lead_status" data-row-id="' . $row->id . '" href="#" ><i class="fa fa-pencil"></i></a>
                    <a class="btn btn-sm btn-danger leadStatuses-remove-btn" href="javascript:;" data-row-id="' . $row->id . '"><i class="fa fa-trash"></i></a>
                    </div>';
                }
            })
            ->rawColumns(['action'])
            // ->removeColumn('id')
            ->make(true);
    }

    public function delete($id)
    {
        $response['error'] = 1;
        $this->row = CRMLeadStatuses::findOrFail($id);
        $this->existing_leads = CRMLeads::where('tenant_id', '=', auth()->user()->tenant_id)->where('lead_status', '=', $this->row->lead_status)->count('*');
        $this->lead_statuses = CRMLeadStatuses::where('tenant_id', '=', auth()->user()->tenant_id)->where('lead_status', '!=', $this->row->lead_status)->orderBy('sort_order', 'asc')->get();
        $html = view('admin.statuses.lead.delete', $this->data)->render();
        $response['error'] = 0;
        $response['html'] = $html;
        return json_encode($response);
    }

    public function ajaxDeleteLeadStatuses(Request $request)
    {
        $id = $request->input('lead_status_id');
        $obj = CRMLeadStatuses::findOrFail($id);

        CRMLeads::where('tenant_id', '=', auth()->user()->tenant_id)
            ->where('lead_status', '=', $obj->lead_status)
            ->update(['lead_status' => $request->input('new_lead_status')]);

        CRMLeadStatuses::destroy($id);
        $response['error'] = 0;
        $response['message'] = __('messages.leadStatusesDeleted');
        return json_encode($response);
    }

    public function ajaxDestroyLeadStatus(Request $request)
    {
        CRMLeadStatuses::destroy($request->id);
        $response['error'] = 0;
        $response['message'] = __('messages.leadStatusesDeleted');
        return json_encode($response);
    }

    public function ajaxUpdateLeadStatusReorder(Request $request)
    {
        $params = $request->input('params');
        $response['error'] = 1;
        // dd($params);
        if ($params) {
            foreach ($params as $key => $val) {
                if (!empty($key) && !empty($val)) {
                    CRMLeadStatuses::where('id', '=', $key)->update(['sort_order' => $val]);
                }
            }
            $response['error'] = 0;
            $response['message'] = 'Sorting has been updated';
            return json_encode($response);
        }
        return json_encode($response);
    }

    // pipeline statuses
    public function storePipeline(Request $request)
    {
        $max = CRMOpPipelineStatuses::where('crm_op_pipeline_statuses.tenant_id', '=', auth()->user()->tenant_id)->max('sort_order');
        $obj = new CRMOpPipelineStatuses();
        $obj->pipeline_status = $request->input('pipeline_status');
        $obj->pipeline_id = $request->input('pipeline_id');
        $obj->created_date = time();
        $obj->updated_date = time();
        $obj->sort_order = intval($max) + 1;
        $obj->tenant_id = auth()->user()->tenant_id;
        $obj->save();

        return response()->json([
            'error' => '0',
            'message' => __('messages.pipelineStatusesCreated')
        ]);
    }

    public function editPipeline($id)
    {
        $response['error'] = 1;
        $this->row = CRMOpPipelineStatuses::where(['id' => $id])->first();
        if ($this->row) {
            $this->op_pipelines = CRMOpPipelines::all();
            $html = view('admin.statuses.pipeline.edit', $this->data)->render();
            $response['error'] = 0;
            $response['html'] = $html;
        }
        return json_encode($response);
    }

    public function updatePipeline(Request $request)
    {
        $id = $request->input('pipeline_status_id');
        $obj = CRMOpPipelineStatuses::findOrFail($id);
        $old_status = $obj->pipeline_status;
        $obj->pipeline_status = $request->input('pipeline_status');
        $obj->updated_date = time();
        $obj->save();


        $crm_leads = CRMOpportunities::where('tenant_id', '=', auth()->user()->tenant_id)->where('op_status', '=', $old_status)->count('*');
        if ($crm_leads > 0) {
            CRMOpportunities::where('tenant_id', '=', auth()->user()->tenant_id)
                ->where('op_status', '=', $old_status)
                ->update(['op_status' => $request->input('pipeline_status')]);
        }

        return response()->json([
            'error' => '0',
            'message' => __('messages.pipelineStatusesUpdated')
        ]);
    }

    public function dataPipeline(Request $request)
    {
        $result = CRMOpPipelineStatuses::select('crm_op_pipeline_statuses.id', 'crm_op_pipeline_statuses.sort_order', 'crm_op_pipeline_statuses.pipeline_status', 'crm_op_pipeline_statuses.pipeline_id')
            ->where('crm_op_pipeline_statuses.tenant_id', '=', auth()->user()->tenant_id)
            ->orderBy('crm_op_pipeline_statuses.sort_order', 'asc')->get();


        return DataTables::of($result)
            ->addColumn('action', function ($row) {
                $crm_leads = CRMOpportunities::where('tenant_id', '=', auth()->user()->tenant_id)
                    ->where('op_status', '=', $row->pipeline_status)->count('*');
                if ($crm_leads > 0) {
                    return '<div class="btn-group">
                    <a class="btn btn-sm btn-primary m-r-10 pipelineStatuses-edit-btn" data-toggle="modal" data-target="#edit_pipeline_status" data-row-id="' . $row->id . '" href="#" ><i class="fa fa-pencil"></i></a>
                    <a class="btn btn-sm btn-danger pipelineStatuses-delete-btn" href="javascript:;"  data-toggle="modal" data-target="#delete_pipeline_status" data-row-id="' . $row->id . '"  data-row-status="' . $row->pipeline_status . '"><i class="fa fa-trash"></i></a>
                    </div>';
                } else {
                    return '<div class="btn-group">
                    <a class="btn btn-sm btn-primary m-r-10 pipelineStatuses-edit-btn" data-toggle="modal" data-target="#edit_pipeline_status" data-row-id="' . $row->id . '" href="#" ><i class="fa fa-pencil"></i></a>
                    <a class="btn btn-sm btn-danger pipelineStatuses-remove-btn" href="javascript:;" data-row-id="' . $row->id . '"><i class="fa fa-trash"></i></a>
                    </div>';
                }
            })
            ->editColumn('pipeline_id', function ($row) {
                return '<div class="badge" style="color: #000;background-color:' . $row->pipeline->color_code . '">' . $row->pipeline->pipeline . '</div>';
            })
            ->rawColumns(['action', 'pipeline_id'])
            ->make(true);
    }

    public function deletePipeline($id)
    {
        $response['error'] = 1;
        $this->row = CRMOpPipelineStatuses::findOrFail($id);
        $this->existing_pipelines = CRMOpportunities::where('tenant_id', '=', auth()->user()->tenant_id)->where('op_status', '=', $this->row->pipeline_status)->count('*');
        $this->pipeline_statuses = CRMOpPipelineStatuses::where('tenant_id', '=', auth()->user()->tenant_id)->where('pipeline_status', '!=', $this->row->pipeline_status)->orderBy('sort_order', 'asc')->get();
        $html = view('admin.statuses.pipeline.delete', $this->data)->render();
        $response['error'] = 0;
        $response['html'] = $html;
        return json_encode($response);
    }

    public function ajaxDeletePipelineStatuses(Request $request)
    {
        $id = $request->input('pipeline_status_id');
        $obj = CRMOpPipelineStatuses::findOrFail($id);

        CRMOpportunities::where('tenant_id', '=', auth()->user()->tenant_id)
            ->where('op_status', '=', $obj->pipeline_status)
            ->update(['op_status' => $request->input('new_pipeline_status')]);

        CRMOpPipelineStatuses::destroy($id);
        $response['error'] = 0;
        $response['message'] = __('messages.pipelineStatusesDeleted');
        return json_encode($response);
    }

    public function ajaxDestroyPipelineStatus(Request $request)
    {
        CRMOpPipelineStatuses::destroy($request->id);
        $response['error'] = 0;
        $response['message'] = __('messages.pipelineStatusesDeleted');
        return json_encode($response);
    }

    public function ajaxUpdatePipelineStatusReorder(Request $request)
    {
        $params = $request->input('params');
        $response['error'] = 1;
        if ($params) {
            foreach ($params as $key => $val) {
                if (!empty($key) && !empty($val)) {
                    CRMOpPipelineStatuses::where('id', '=', $key)->update(['sort_order' => $val]);
                }
            }
            $response['error'] = 0;
            $response['message'] = 'Sorting has been updated';
            return json_encode($response);
        }
        return json_encode($response);
    }
}
