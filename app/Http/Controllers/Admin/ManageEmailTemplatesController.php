<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Requests\EmailTemplates\StoreEmailTemplate;
use App\Setting;
use App\EmailTemplates;
use App\Companies;
use App\EmailTemplateAttachments;
use App\TenantApiDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ManageEmailTemplatesController extends AdminBaseController {

    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('app.menu.emailTemplates');
        $this->pageIcon = 'ti-file';
//        $this->middleware(function ($request, $next) {
//            if (!in_array('estimates', $this->user->modules)) {
//                abort(403);
//            }
//            return $next($request);
//        });
    }

    public function index() {
        return view('admin.email-templates.index', $this->data);
    }

    public function create() {

        $this->companies = Companies::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        //Cover Freight Insurance setting
        $coverFreight = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'CoverFreight'])->first();            
        if($coverFreight){ 
            $this->coverFreight_connected=true;
        }else{
            $this->coverFreight_connected=false;
        }
        return view('admin.email-templates.create', $this->data);
    }

    public function store(StoreEmailTemplate $request) {

        $template = new EmailTemplates();
        $template->email_template_name = $request->input('email_template_name');
        $template->company_id = $request->input('company_id');
        $template->email_subject = $request->input('email_subject');
        $template->email_body = $request->input('email_body');
        $template->from_email_name = $request->input('from_email_name');
        $template->from_email = $request->input('from_email');
        $template->active = ($request->input('active') == 'Y' ? 'Y' : 'N');
        $template->attach_quote = ($request->input('attach_quote') == 'Y' ? 'Y' : 'N');
        $template->attach_invoice = ($request->input('attach_invoice') == 'Y' ? 'Y' : 'N');
        $template->attach_work_order = ($request->input('attach_work_order') == 'Y' ? 'Y' : 'N');
        $template->attach_pod = ($request->input('attach_pod') == 'Y' ? 'Y' : 'N');
        $template->attach_insurance = ($request->input('attach_insurance') == 'Y' ? 'Y' : 'N');
        $template->attach_storage_invoice = ($request->input('attach_storage_invoice') == 'Y' ? 'Y' : 'N');
        $template->created_at = time();
        $template->updated_at = time();
        $template->tenant_id = auth()->user()->tenant_id;

        $template->save();

        return Reply::redirect(route('admin.email-templates.index'), __('messages.emailTemplateCreated'));
    }

    public function edit($id) {
        $this->template = EmailTemplates::findOrFail($id);
        $this->companies = Companies::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $this->attachments = EmailTemplateAttachments::where(['email_template_id'=>$id,'tenant_id'=>auth()->user()->tenant_id])->get();
        //Cover Freight Insurance setting
        $coverFreight = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'CoverFreight'])->first();            
        if($coverFreight){ 
            $this->coverFreight_connected=true;
        }else{
            $this->coverFreight_connected=false;
        }
        
        return view('admin.email-templates.edit', $this->data);
    }

    public function update(StoreEmailTemplate $request, $id) {

        $template = EmailTemplates::findOrFail($id);
        $template->email_template_name = $request->input('email_template_name');
        $template->company_id = $request->input('company_id');
        $template->from_email_name = $request->input('from_email_name');
        $template->from_email = $request->input('from_email');
        $template->email_subject = $request->input('email_subject');
        $template->email_body = $request->input('email_body');
        $template->active = ($request->input('active') == 'Y' ? 'Y' : 'N');
        $template->attach_quote = ($request->input('attach_quote') == 'Y' ? 'Y' : 'N');
        $template->attach_invoice = ($request->input('attach_invoice') == 'Y' ? 'Y' : 'N');
        $template->attach_work_order = ($request->input('attach_work_order') == 'Y' ? 'Y' : 'N');
        $template->attach_pod = ($request->input('attach_pod') == 'Y' ? 'Y' : 'N');
        $template->attach_insurance = ($request->input('attach_insurance') == 'Y' ? 'Y' : 'N');
        $template->attach_storage_invoice = ($request->input('attach_storage_invoice') == 'Y' ? 'Y' : 'N');
        $template->updated_at = time();
        $template->save();

        return Reply::redirect(route('admin.email-templates.index'), __('messages.emailTemplateUpdated'));
    }

    public function storeTemplateAttachment(Request $request){
        $template_id = $request->input('templateid');
        if ($request->hasFile('attachment')) {
            $image = $request->file('attachment');
            $input['filename'] = $template_id . '-' . date('Y') . '-' . $image->getClientOriginalName();
            $destinationPath = public_path('/user-uploads/tenants/' . auth()->user()->tenant_id);
            File::isDirectory($destinationPath) or File::makeDirectory($destinationPath, 0777, true, true);
            $request->attachment->move($destinationPath, $input['filename']);

            $location = '/user-uploads/tenants/' . auth()->user()->tenant_id.'/'.$input['filename'];            
            $object = new EmailTemplateAttachments;
            $object->tenant_id = auth()->user()->tenant_id;
            $object->email_template_id = $template_id;
            $object->created_by = auth()->user()->id;
            $object->updated_by = auth()->user()->id;
            $object->created_at = date('Y-m-d h:i:s');
            $object->updated_at = date('Y-m-d h:i:s');
            $object->attachment_file_name = $input['filename'];
            $object->attachment_file_location = $location;
            $object->save();

            $attachments = EmailTemplateAttachments::where(['email_template_id'=>$template_id,'tenant_id'=>auth()->user()->tenant_id])->get();
            $response['html'] = view('admin.email-templates.attachment_grid')->with(['attachments'=>$attachments])->render();
            $response['error']=0;
            $response['message']='Attachment upload successfully';
        }else{
            $response['error']=1;
            $response['message']='Please choose attachment..';            
        }
        return json_encode($response);
    }

    public function viewTemplateAttachment($id) {
        try {
            $this->attachments = EmailTemplateAttachments::where('tenant_id', '=', auth()->user()->tenant_id)->where('id', '=', $id)->first();
            if ($this->attachments) {
                if ($this->attachments->attachment_file_location) {
                    $destinationPath = public_path();
                    return response()->download($destinationPath . '/' . $this->attachments->attachment_file_location);
                }
            }

            return redirect(route('admin.email-templates.edit', $id));
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function removeTemplateAttachment(Request $request) {
        $id = $request->input('id');
        try {
            $attachment = EmailTemplateAttachments::where('id', '=', $id)->first();
            $template_id = $attachment->email_template_id;
            File::delete(public_path($attachment->attachment_file_location));
            $attachment->delete();
            $attachments = EmailTemplateAttachments::where(['email_template_id'=>$template_id,'tenant_id'=>auth()->user()->tenant_id])->get();
            $response['html'] = view('admin.email-templates.attachment_grid')->with(['attachments'=>$attachments])->render();;
            $response['error']=0;
            $response['message']='Attachment delete successfully';
            return json_encode($response);
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function data(Request $request) {
        $result = EmailTemplates::select('email_templates.id', 'email_templates.email_template_name', 'companies.company_name', 'email_templates.active')
                ->join('companies', 'companies.id', '=', 'email_templates.company_id')
                ->where('email_templates.tenant_id', '=', auth()->user()->tenant_id);
        $result = $result->orderBy('email_templates.email_template_name', 'asc')->get();


        return DataTables::of($result)
                        ->addColumn('action', function ($row) {
                            return '<div class="btn-group m-r-10">
                            <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info btn-outline  dropdown-toggle waves-effect waves-light" type="button">Action <span class="caret"></span></button>
                            <ul role="menu" class="dropdown-menu">
                              <li><a href="' . route("admin.email-templates.edit", $row->id) . '" ><i class="fa fa-pencil"></i> Edit</a></li>
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
        EmailTemplates::destroy($id);
        return Reply::success(__('messages.emailTemplateDeleted'));
    }

    public function export() {

        $result = EmailTemplates::select('email_templates.id', 'companies.company_name', 'email_templates.email_template_name', 'email_templates.email_subject', 'email_templates.email_body', 'email_templates.active', 'email_templates.created_at', 'email_templates.updated_at')
                ->join('companies', 'companies.id', '=', 'email_templates.company_id')
                ->where('email_templates.tenant_id', '=', auth()->user()->tenant_id);
        $result = $result->orderBy('email_templates.email_template_name', 'asc')->get();

        // Initialize the array which will be passed into the Excel
        // generator.
        $exportArray = [];

        // Define the Excel spreadsheet headers
        $exportArray[] = ['ID', 'Company Name', 'Email Template Name', 'Email Subject', 'Email Body', 'Status', 'Created At', 'Updated At'];

        // Convert each member of the returned collection into an array,
        // and append it to the payments array.
        foreach ($result as $row) {
            $exportArray[] = $row->toArray();
        }

        // Generate and return the spreadsheet
        Excel::create('email_templates', function ($excel) use ($exportArray) {

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
