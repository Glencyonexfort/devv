<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Requests\SMSTemplates\StoreSMSTemplate;
use App\Setting;
use App\SMSTemplates;
use App\Companies;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ManageSMSTemplatesController extends AdminBaseController {

    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('app.menu.smsTemplates');
        $this->pageIcon = 'ti-file';
//        $this->middleware(function ($request, $next) {
//            if (!in_array('estimates', $this->user->modules)) {
//                abort(403);
//            }
//            return $next($request);
//        });
    }

    public function index() {
        return view('admin.sms-templates.index', $this->data);
    }

    public function create() {

        $this->companies = Companies::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        return view('admin.sms-templates.create', $this->data);
    }

    public function store(StoreSMSTemplate $request) {

        $template = new SMSTemplates();
        $template->sms_template_name = $request->input('sms_template_name');
        $template->company_id = $request->input('company_id');
        //$template->sms_subject = $request->input('sms_subject');
        $template->sms_message = $request->input('sms_message');
        $template->active = ($request->input('active') == 'Y' ? 'Y' : 'N');
        $template->attach_quote = ($request->input('attach_quote') == 'Y' ? 'Y' : 'N');
        $template->attach_invoice = ($request->input('attach_invoice') == 'Y' ? 'Y' : 'N');
        $template->created_at = time();
        $template->updated_at = time();
        $template->tenant_id = auth()->user()->tenant_id;

        $template->save();

        return Reply::redirect(route('admin.sms-templates.index'), __('messages.smsTemplateCreated'));
    }

    public function edit($id) {
        $this->template = SMSTemplates::findOrFail($id);
        $this->companies = Companies::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        return view('admin.sms-templates.edit', $this->data);
    }

    public function update(StoreSMSTemplate $request, $id) {

        $template = SMSTemplates::findOrFail($id);
        $template->sms_template_name = $request->input('sms_template_name');
        $template->company_id = $request->input('company_id');
        //$template->sms_subject = $request->input('sms_subject');
        $template->sms_message = $request->input('sms_message');
        $template->active = ($request->input('active') == 'Y' ? 'Y' : 'N');
        $template->attach_quote = ($request->input('attach_quote') == 'Y' ? 'Y' : 'N');
        $template->attach_invoice = ($request->input('attach_invoice') == 'Y' ? 'Y' : 'N');
        $template->updated_at = time();

        $template->save();

        return Reply::redirect(route('admin.sms-templates.index'), __('messages.smsTemplateUpdated'));
    }

    public function data(Request $request) {
       // dd($request);
        $result = SMSTemplates::select('sms_templates.id', 'sms_templates.sms_template_name', 'companies.company_name', 'sms_templates.active')
                ->leftjoin('companies', 'companies.id', '=', 'sms_templates.company_id')
                ->where('sms_templates.tenant_id', '=', auth()->user()->tenant_id);
        $result = $result->orderBy('sms_templates.sms_template_name', 'asc')->get();


        return DataTables::of($result)
                        ->addColumn('action', function ($row) {
                            return '<div class="btn-group m-r-10">
                            <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info btn-outline  dropdown-toggle waves-effect waves-light" type="button">Action <span class="caret"></span></button>
                            <ul role="menu" class="dropdown-menu">
                              <li><a href="' . route("admin.sms-templates.edit", $row->id) . '" ><i class="fa fa-pencil"></i> Edit</a></li>
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
        SMSTemplates::destroy($id);
        return Reply::success(__('messages.smsTemplateDeleted'));
    }

    public function export() {

        $result = SMSTemplates::select('sms_templates.id', 'companies.company_name', 'sms_templates.sms_template_name', 'sms_templates.sms_subject', 'sms_templates.sms_message', 'sms_templates.active', 'sms_templates.created_at', 'sms_templates.updated_at')
                ->join('companies', 'companies.id', '=', 'sms_templates.company_id')
                ->where('sms_templates.tenant_id', '=', auth()->user()->tenant_id);
        $result = $result->orderBy('sms_templates.sms_template_name', 'asc')->get();

        // Initialize the array which will be passed into the Excel
        // generator.
        $exportArray = [];

        // Define the Excel spreadsheet headers
        $exportArray[] = ['ID', 'Company Name', 'SMS Template Name', 'SMS Subject', 'SMS Message', 'Status', 'Created At', 'Updated At'];

        // Convert each member of the returned collection into an array,
        // and append it to the payments array.
        foreach ($result as $row) {
            $exportArray[] = $row->toArray();
        }

        // Generate and return the spreadsheet
        Excel::create('sms_templates', function ($excel) use ($exportArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('SMS Templates');
            $excel->setCreator('Website')->setCompany($this->companyName);
            $excel->setDescription('SMS Templates file');

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
