<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Requests\JobTemplatesMoving\StoreJobTemplatesMoving;
use App\JobTemplatesMoving;
use App\JobTemplatesMovingAttachment;
use App\Setting;
use App\Companies;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ManageJobTemplatesMovingController extends AdminBaseController {

    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('app.menu.jobTemplates');
        $this->pageIcon = 'ti-file';
//        $this->middleware(function ($request, $next) {
//            if (!in_array('estimates', $this->user->modules)) {
//                abort(403);
//            }
//            return $next($request);
//        });
    }

    public function index() {
        return view('admin.job-templates.index', $this->data);
    }

    public function create() {

        $this->companies = Companies::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        return view('admin.job-templates.create', $this->data);
    }

    public function store(StoreJobTemplatesMoving $request) {

        $template = new JobTemplatesMoving();
        $template->job_template_name = $request->input('job_template_name');      
        $template->company_id = $request->input('company_id');
        $template->pickup_instructions = $request->input('pickup_instructions');
        $template->drop_off_instructions = $request->input('drop_off_instructions');
        $template->payment_instructions = $request->input('payment_instructions');
        $template->default1 = ($request->input('default1') == 'Y' ? 'Y' : '');
        $template->created_at = time();
        $template->updated_at = time();
        $template->tenant_id = auth()->user()->tenant_id;

        $template->save();
        return Reply::redirect(route('admin.job-templates.index'), __('messages.jobTemplatesMovingCreated'));
    }

    public function edit($id) {
        $this->template = JobTemplatesMoving::findOrFail($id);
        $this->template_attachments = \App\JobTemplatesMovingAttachment::where('job_template_id', $this->template->id)
                ->get();
        $this->companies = Companies::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        return view('admin.job-templates.edit', $this->data);
    }

    public function update(StoreJobTemplatesMoving $request, $id) {
        $template = JobTemplatesMoving::findOrFail($id);
        $template->job_template_name = $request->input('job_template_name');
        $template->company_id = $request->input('company_id');
        $template->pickup_instructions = $request->input('pickup_instructions');
        $template->drop_off_instructions = $request->input('drop_off_instructions');
        $template->payment_instructions = $request->input('payment_instructions');
        $template->default1 = ($request->input('default1') == 'Y' ? 'Y' : '');
        $template->updated_at = time();
        $template->save();
        
        if($request->hasfile('standard_attachments'))
         {
            $destinationPath = public_path('/job-template');
            foreach($request->file('standard_attachments') as $file)
            {
                $name = 'Job_Template_'.time().'.'.$file->extension();
                $file->move($destinationPath.'/', $name);
//                $job_template_moving_attachments = \App\JobTemplatesMovingAttachment::where('tenant_id', '=', auth()->user()->tenant_id)
//                        ->where('job_template_id', $template->id)
//                        ->first();
//                if(!$job_template_moving_attachments){
                    $job_template_moving_attachments = new \App\JobTemplatesMovingAttachment();
//                }
                $job_template_moving_attachments->tenant_id = auth()->user()->tenant_id;
                $job_template_moving_attachments->job_template_id = $template->id;
                $job_template_moving_attachments->attachment_file_name = $name;
                $job_template_moving_attachments->attachment_file_location = url('/job-template/');
                $job_template_moving_attachments->save();
            }
        }
        
        return redirect()->route('admin.job-templates.index')
                ->with('message', __('messages.jobTemplatesMovingUpdated'));
         
//        return Reply::redirect(route('admin.job-templates.index'), __('messages.jobTemplatesMovingUpdated'));
    }

    public function data(Request $request) {
        $result = jobTemplatesMoving::select('job_templates_moving.id', 'job_templates_moving.job_template_name')
                        ->where('job_templates_moving.tenant_id', '=', auth()->user()->tenant_id)
                        ->orderBy('job_templates_moving.job_template_name', 'asc')->get();


        return DataTables::of($result)
                        ->addColumn('action', function ($row) {
                            return '<div class="btn-group m-r-10">
                            <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info btn-outline  dropdown-toggle waves-effect waves-light" type="button">Action <span class="caret"></span></button>
                            <ul role="menu" class="dropdown-menu">
                              <li><a href="' . route("admin.job-templates.edit", $row->id) . '" ><i class="fa fa-pencil"></i> Edit</a></li>
                              <li><a class="sa-params" href="javascript:;" data-row-id="' . $row->id . '"><i class="fa fa-times"></i> Delete</a></li>
                            </ul>
                          </div>
                          ';
                        })
                        ->rawColumns(['action'])
                        ->removeColumn('id')
                        ->make(true);
    }

    public function destroy($id) {
        JobTemplatesMoving::destroy($id);
        return Reply::success(__('messages.jobTemplatesMovingDeleted'));
    }

    public function export() {

        $result = jobTemplatesMoving::select('job_templates_moving.id', 'job_templates_moving.job_template_name', 'companies.company_name', 'job_templates_moving.pickup_instructions', 'job_templates_moving.drop_off_instructions', 'job_templates_moving.payment_instructions', 'job_templates_moving.default1', 'job_templates_moving.created_at', 'job_templates_moving.updated_at')->join('companies', 'companies.id', '=', 'job_templates_moving.company_id')
                        ->where('job_templates_moving.tenant_id', '=', auth()->user()->tenant_id)
                        ->orderBy('job_templates_moving.job_template_name', 'asc')->get();

        // Initialize the array which will be passed into the Excel
        // generator.
        $exportArray = [];

        // Define the Excel spreadsheet headers
        $exportArray[] = ['ID', 'Job Template Name', 'Company Name', 'Pickup Instructions', 'Drop Off Instructions', 'Payment Instructions', 'Default', 'Created At', 'Updated At'];

        // Convert each member of the returned collection into an array,
        // and append it to the payments array.
        foreach ($result as $row) {
            $exportArray[] = $row->toArray();
        }

        // Generate and return the spreadsheet
        Excel::create('job_templates_moving', function ($excel) use ($exportArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Job Templates');
            $excel->setCreator('Website')->setCompany($this->companyName);
            $excel->setDescription('Job Templates file');

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


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyTemplateAttachment($id)
    {
        
        $attachment = JobTemplatesMovingAttachment::find($id);
        if ($attachment != null) {
                unlink(public_path('/job-template') . '/' . $attachment->attachment_file_name);
        }

        $attachment->delete();
        return Reply::dataOnly(['status' => 'success']);
    }

}
