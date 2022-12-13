<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Requests\JobTemplatesMoving\StoreJobTemplatesMoving;
use App\JobTemplatesMoving;
use App\JobTemplatesMovingAttachment;
use App\Setting;
use App\Companies;
use App\PropertyCategory;
use App\PropertyCategoryOptions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ManagePropertyCategoryOptionsController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.propertyCategoryOptions');
        $this->pageIcon = 'icon-arrow-right15';
    }

    public function index()
    {
        return view('admin.property-category-options.index', $this->data);
    }

    public function create()
    {

        $this->property_categories = PropertyCategory::where('active', '=', 'Y')->get();
        return view('admin.property-category-options.create', $this->data);
    }

    public function store(Request $request)
    {
        if ($request->input('options') != '' && $request->input('m3_value') != '' && $request->input('money_value') != '') :
            $template = new PropertyCategoryOptions();
            $template->options = $request->input('options');
            $template->category_id = $request->input('category_id');
            $template->m3_value = $request->input('m3_value');
            $template->other_value = $request->input('other_value');
            $template->active = ($request->input('active') == 'Y' ? 'Y' : 'N');
            $template->created_at = time();
            $template->updated_at = time();
            $template->tenant_id = auth()->user()->tenant_id;

            $template->save();
            return Reply::redirect(route('admin.property-category-options.index'), __('messages.propertyCategoryOptionsCreated'));
        endif;
    }

    public function edit($id)
    {
        $this->row = PropertyCategoryOptions::findOrFail($id);
        $this->property_categories = PropertyCategory::where('active', '=', 'Y')->get();
        return view('admin.property-category-options.edit', $this->data);
    }

    public function update(Request $request, $id)
    {
        $template = PropertyCategoryOptions::findOrFail($id);
        $template->options = $request->input('options');
        $template->category_id = $request->input('category_id');
        $template->m3_value = $request->input('m3_value');
        $template->other_value = $request->input('other_value');
        $template->active = ($request->input('active') == 'Y' ? 'Y' : 'N');
        $template->updated_at = time();
        $template->save();

        // return Reply::redirect(route('admin.property-category-options.index'), __('messages.PropertyCategoryOptionsUpdated'));
        return redirect()->route('admin.property-category-options.index')->with('message', __('messages.PropertyCategoryOptionsUpdated'));
    }

    public function data(Request $request)
    {
        $result = PropertyCategoryOptions::select('property_category_options.id', 'property_category_options.category_id', 'property_category_options.options')
            ->where('property_category_options.tenant_id', '=', auth()->user()->tenant_id)
            ->orderBy('property_category_options.options', 'asc')->get();


        return DataTables::of($result)
            ->addColumn('action', function ($row) {
                return '<div class="btn-group m-r-10">
                            <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info btn-outline  dropdown-toggle waves-effect waves-light" type="button">Action <span class="caret"></span></button>
                            <ul role="menu" class="dropdown-menu">
                              <li><a href="' . route("admin.property-category-options.edit", $row->id) . '" ><i class="fa fa-pencil"></i> Edit</a></li>
                              <li><a class="sa-params" href="javascript:;" data-row-id="' . $row->id . '"><i class="fa fa-times"></i> Delete</a></li>
                            </ul>
                          </div>
                          ';
            })
            ->editColumn('property_category', function ($row) {
                $category =  PropertyCategory::select('category')->where('id', $row->category_id)->first();
                if($category)
                    return $category->category;
                else
                    return '';
            })
            ->rawColumns(['action'])
            ->removeColumn('id')
            ->make(true);
    }

    public function destroy($id)
    {
        PropertyCategoryOptions::destroy($id);
        return Reply::success(__('messages.propertyCategoryOptionsDeleted'));
    }

    public function export()
    {

        $result = PropertyCategoryOptions::select('property_category_options.id', 'property_category_options.job_template_name', 'companies.company_name', 'property_category_options.pickup_instructions', 'property_category_options.drop_off_instructions', 'property_category_options.payment_instructions', 'property_category_options.default1', 'property_category_options.created_at', 'property_category_options.updated_at')->join('companies', 'companies.id', '=', 'property_category_options.company_id')
            ->where('property_category_options.tenant_id', '=', auth()->user()->tenant_id)
            ->orderBy('property_category_options.job_template_name', 'asc')->get();

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
        Excel::create('property_category_options', function ($excel) use ($exportArray) {

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
}
