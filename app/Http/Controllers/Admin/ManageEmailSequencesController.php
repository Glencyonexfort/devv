<?php

namespace App\Http\Controllers\Admin;

use App\Companies;
use App\Helper\Reply;
use App\Http\Requests\EmailSequences\StoreEmailSequence;
use App\Lists;
use App\EmailTemplates;
use App\EmailSequenceSettings;
use App\CRMOpPipelineStatuses;
use App\SMSTemplates;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ManageEmailSequencesController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.emailSequences');
        $this->pageIcon = 'ti-file';
    }

    public function index()
    {
        return view('admin.email-sequences.index', $this->data);
    }

    public function create()
    {
        $this->email_templates = EmailTemplates::where(['tenant_id' => auth()->user()->tenant_id])->get();
        $this->sms_templates = SMSTemplates::where(['tenant_id' => auth()->user()->tenant_id])->get();
        $this->companies = Companies::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $this->sys_job_type = Lists::sys_job_type();
        $this->job_status = Lists::job_status();
        $this->pipeline_statuses = CRMOpPipelineStatuses::where('tenant_id', '=', auth()->user()->tenant_id)->orderBy('sort_order', 'asc')->get();
        return view('admin.email-sequences.create', $this->data);
    }

    public function store(StoreEmailSequence $request)
    {        
        $obj = new EmailSequenceSettings();
        $obj->sequence_name = $request->input('sequence_name');
        $obj->sequence_description = $request->input('sequence_description');
        $obj->check_frequency = $request->input('check_frequency');
        $obj->company_id = $request->input('company_id');
        $obj->is_opportunity = ($request->input('is_opportunity') == 'Y' ? 'Y' : 'N');
        if ($request->input('is_opportunity') == 'Y') {
            if($request->input('pipeline_status') == $request->input('pipeline_status2')){
                $response['error']=1;
                $response['message']='Post Status and the Initial Status should not be the same';
                return json_encode($response);exit;
            }
            $obj->initial_status = $request->input('pipeline_status');
            $obj->post_status = $request->input('pipeline_status2');
        } else {
            if($request->input('initial_status') == $request->input('post_status')){
                $response['error']=1;
                $response['message']='Post Status and the Initial Status should not be the same';
                return json_encode($response);exit;
            }
            $obj->sys_job_type = $request->input('sys_job_type');
            $obj->initial_status = $request->input('initial_status');
            $obj->post_status = $request->input('post_status');
        }        
        $obj->send_email = ($request->input('send_email') == 'Y' ? 'Y' : 'N');        
        $obj->email_template_id = $request->input('email_template_id');
        $obj->from_email = $request->input('from_email');
        $obj->from_email_name = $request->input('from_email_name');

        $obj->send_sms = ($request->input('send_sms') == 'Y' ? 'Y' : 'N');
        $obj->sms_template_id = $request->input('sms_template_id');
        $obj->from_sms_number_name = $request->input('from_sms_number_name');

        if ($request->input('sequence_type') == 'Job Date') {
            $obj->days_before_after_job_date = $request->input('days_before_after_job_date');
        }else{
            $obj->days_after_initial_status = $request->input('days_after_initial_status');
        }
        $obj->sequence_type = $request->input('sequence_type');
        $obj->active = ($request->input('active') == 'Y' ? 'Y' : 'N');
        $obj->created_at = time();
        $obj->updated_at = time();
        $obj->tenant_id = auth()->user()->tenant_id;
        $obj->save();

        return Reply::redirect(route('admin.email-sequences.index'), __('messages.emailTemplateCreated'));
    }

    public function edit($id)
    {
        $this->email_templates = EmailTemplates::where(['tenant_id' => auth()->user()->tenant_id])->get();
        $this->sms_templates = SMSTemplates::where(['tenant_id' => auth()->user()->tenant_id])->get();
        $this->companies = Companies::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $this->sys_job_type = Lists::sys_job_type();
        $this->job_status = Lists::job_status();
        $this->pipeline_statuses = CRMOpPipelineStatuses::where('tenant_id', '=', auth()->user()->tenant_id)->orderBy('sort_order', 'asc')->get();

        $this->sequence = EmailSequenceSettings::findOrFail($id);
        return view('admin.email-sequences.edit', $this->data);
    }

    public function update(StoreEmailSequence $request, $id)
    {

        $obj = EmailSequenceSettings::findOrFail($id);
        $obj->sequence_name = $request->input('sequence_name');
        $obj->sequence_description = $request->input('sequence_description');
        $obj->check_frequency = $request->input('check_frequency');
        $obj->company_id = $request->input('company_id');
        $obj->is_opportunity = ($request->input('is_opportunity') == 'Y' ? 'Y' : 'N');
        if ($request->input('is_opportunity') == 'Y') {
            if($request->input('pipeline_status') == $request->input('pipeline_status2')){
                $response['error']=1;
                $response['message']='Post Status and the Initial Status should not be the same';
                return json_encode($response);exit;
            }
            $obj->initial_status = $request->input('pipeline_status');
            $obj->post_status = $request->input('pipeline_status2');
        } else {
            if($request->input('initial_status') == $request->input('post_status')){
                $response['error']=1;
                $response['message']='Post Status and the Initial Status should not be the same';
                return json_encode($response);exit;
            }
            $obj->sys_job_type = $request->input('sys_job_type');
            $obj->initial_status = $request->input('initial_status');
            $obj->post_status = $request->input('post_status');
        }
        $obj->send_email = ($request->input('send_email') == 'Y' ? 'Y' : 'N');       
        $obj->email_template_id = $request->input('email_template_id');
        $obj->from_email = $request->input('from_email');
        $obj->from_email_name = $request->input('from_email_name');

        $obj->send_sms = ($request->input('send_sms') == 'Y' ? 'Y' : 'N');
        $obj->sms_template_id = $request->input('sms_template_id');
        $obj->from_sms_number_name = $request->input('from_sms_number_name');

        if ($request->input('sequence_type') == 'Job Date') {
            $obj->days_before_after_job_date = $request->input('days_before_after_job_date');
        }else{
            $obj->days_after_initial_status = $request->input('days_after_initial_status');
        }
        $obj->sequence_type = $request->input('sequence_type');
        $obj->active = ($request->input('active') == 'Y' ? 'Y' : 'N');
        $obj->updated_at = time();
        $obj->save();

        return Reply::redirect(route('admin.email-sequences.index'), __('messages.emailSequenceUpdated'));
    }

    public function data(Request $request)
    {
        $result = EmailSequenceSettings::select('email_sequence_settings.id', 'email_sequence_settings.sequence_name', 'email_sequence_settings.sequence_description', 'email_sequence_settings.active')
            ->where('email_sequence_settings.tenant_id', '=', auth()->user()->tenant_id)
            ->orderBy('email_sequence_settings.sequence_name', 'asc')->get();


        return DataTables::of($result)
            ->addColumn('action', function ($row) {
                return '<div class="btn-group m-r-10">
                            <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info btn-outline  dropdown-toggle waves-effect waves-light" type="button">Action <span class="caret"></span></button>
                            <ul role="menu" class="dropdown-menu">
                              <li><a href="' . route("admin.email-sequences.edit", $row->id) . '" ><i class="fa fa-pencil"></i> Edit</a></li>
                              <li><a class="sa-params" href="javascript:;" data-row-id="' . $row->id . '"><i class="fa fa-times"></i> Delete</a></li>
                            </ul>
                          </div>
                          ';
            })
            ->rawColumns(['action'])
            ->removeColumn('id')
            ->make(true);
    }

    public function destroy($id)
    {
        EmailSequenceSettings::destroy($id);
        return Reply::success(__('messages.emailSequenceDeleted'));
    }

    public function export()
    {

        $result = EmailSequenceSettings::select('email_sequence_settings.id', 'email_sequence_settings.sequence_name', 'email_sequence_settings.sequence_description', 'email_sequence_settings.active')
            ->where('email_sequence_settings.tenant_id', '=', auth()->user()->tenant_id)
            ->orderBy('email_sequence_settings.sequence_name', 'asc')->get();

        // Initialize the array which will be passed into the Excel
        // generator.
        $exportArray = [];

        // Define the Excel spreadsheet headers
        $exportArray[] = ['Sequence Name', 'Sequence Description', 'Status ', 'Action'];

        // Convert each member of the returned collection into an array,
        // and append it to the payments array.
        foreach ($result as $row) {
            $exportArray[] = $row->toArray();
        }

        // Generate and return the spreadsheet
        Excel::create('email_sequence_settings', function ($excel) use ($exportArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Email Templates');
            $excel->setCreator('Website')->setCompany($this->companyName);
            $excel->setDescription('Email Templates file');

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
}
