<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Requests\Companies\StoreCompany;
use App\Companies;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ManageCompaniesController extends AdminBaseController {

    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('app.menu.companies');
        $this->pageIcon = 'ti-file';
//        $this->middleware(function ($request, $next) {
//            if (!in_array('estimates', $this->user->modules)) {
//                abort(403);
//            }
//            return $next($request);
//        });
    }

    public function index() {
        return view('admin.companies.index', $this->data);
    }

    public function create() {
        return view('admin.companies.create', $this->data);
    }

    public function store(StoreCompany $request) {
        
        $company = new Companies();
        $company->company_name = $request->input('company_name');
        $company->contact_name = $request->input('contact_name');
        $company->email = $request->input('email');
        $company->address = $request->input('address');
        $company->sms_number = $request->input('sms_number');
        $company->phone = $request->input('phone');
        $company->payment_terms = $request->input('payment_terms');
        $company->customer_sign_off_checklist = $request->input('customer_sign_off_checklist');
        $company->customer_pre_job_checklist = $request->input('customer_pre_job_checklist');
        $company->work_order_instructions = $request->input('work_order_instructions');
        $company->pod_instructions = $request->input('pod_instructions');
        $company->abn = $request->input('abn');
        $company->default1 = ($request->input('default1') == 'Y' ? 'Y' : '');
        $company->active = ($request->input('active') == 'Y' ? 'Y' : 'N');
        $company->created_at = time();
        $company->updated_at = time();
        $company->tenant_id = auth()->user()->tenant_id;

        if ($request->hasFile('image')) {
            File::delete('user-uploads/company-logo/' . $company->logo);

            $company->logo = $request->image->hashName();
            $request->image->store('user-uploads/company-logo');

            // resize the image to a width of 300 and constrain aspect ratio (auto height)
            $img = Image::make('user-uploads/company-logo/' . $company->logo);
            $img->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save();
        }

        $company->save();

        return Reply::redirect(route('admin.companies.index'), __('messages.companyCreated'));
    }

    public function edit($id) {
        $this->company = Companies::findOrFail($id);
        return view('admin.companies.edit', $this->data);
    }

    public function update(StoreCompany $request, $id) {

        $company = Companies::findOrFail($id);
        $company->company_name = $request->input('company_name');
        $company->contact_name = $request->input('contact_name');
        $company->email = $request->input('email');
        $company->address = $request->input('address');
        $company->sms_number = $request->input('sms_number');
        $company->phone = $request->input('phone');
        $company->payment_terms = $request->input('payment_terms');
        $company->customer_sign_off_checklist = $request->input('customer_sign_off_checklist');
        $company->customer_pre_job_checklist = $request->input('customer_pre_job_checklist');
        $company->work_order_instructions = $request->input('work_order_instructions');
        $company->pod_instructions = $request->input('pod_instructions');
        $company->abn = $request->input('abn');
        $company->default1 = ($request->input('default1') == 'Y' ? 'Y' : '');
        $company->active = ($request->input('active') == 'Y' ? 'Y' : 'N');
        $company->updated_at = time();

        if ($request->hasFile('image')) {
            File::delete('user-uploads/company-logo/' . $company->logo);

            $company->logo = $request->image->hashName();
            $request->image->store('user-uploads/company-logo');

            // resize the image to a width of 300 and constrain aspect ratio (auto height)
            $img = Image::make('user-uploads/company-logo/' . $company->logo);
            $img->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save();
        }

        $company->update();

        return Reply::redirect(route('admin.companies.index'), __('messages.companyUpdated'));
    }

    public function data(Request $request) {
        $companies = Companies::select('companies.id', 'companies.company_name', 'companies.address', 'companies.contact_name', 'companies.abn', 'companies.default1')
                ->where('companies.tenant_id', '=', auth()->user()->tenant_id);
        $companies = $companies->orderBy('companies.company_name', 'asc')->get();


        return DataTables::of($companies)
                        ->addColumn('action', function ($row) {
                            return '<div class="btn-group m-r-10">
                            <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info btn-outline  dropdown-toggle waves-effect waves-light" type="button">Action <span class="caret"></span></button>
                            <ul role="menu" class="dropdown-menu">
                              <li><a href="' . route("admin.companies.edit", $row->id) . '" ><i class="fa fa-pencil"></i> Edit</a></li>
                              <li><a class="sa-params" href="javascript:;" data-company-id="' . $row->id . '"><i class="fa fa-times"></i> Delete</a></li>
                            </ul>
                          </div>
                          ';
                        })
                        ->rawColumns(['action'])
                        ->removeColumn('id')
                        ->make(true);
    }

    public function destroy($id) {
        Companies::destroy($id);
        return Reply::success(__('messages.companyDeleted'));
    }

    public function export() {
        
        $companies = Companies::select('companies.id', 'companies.company_name', 'companies.email', 'companies.address', 'companies.contact_name', 'companies.phone', 'companies.abn', 'companies.default1', 'companies.active', 'companies.created_at', 'companies.updated_at')
                ->where('companies.tenant_id', '=', auth()->user()->tenant_id);
 
        $companies = $companies->orderBy('companies.id', 'desc')->get();

        // Initialize the array which will be passed into the Excel
        // generator.
        $exportArray = [];

        // Define the Excel spreadsheet headers
        $exportArray[] = ['ID', 'Company Name', 'Email', 'Address', 'Contact Name', 'Phone', 'ABM', 'Default', 'Active', 'Created At', 'Updated At'];

        // Convert each member of the returned collection into an array,
        // and append it to the payments array.
        foreach ($companies as $row) {
            $exportArray[] = $row->toArray();
        }

        // Generate and return the spreadsheet
        Excel::create('companies', function ($excel) use ($exportArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Companies');
            $excel->setCreator('Website')->setCompany($this->companyName);
            $excel->setDescription('Companies list file');

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
