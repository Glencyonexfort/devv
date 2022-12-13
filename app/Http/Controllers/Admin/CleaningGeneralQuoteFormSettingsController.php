<?php

namespace App\Http\Controllers\Admin;

use App\Companies;
use Illuminate\Http\Request;
use App\Helper\Reply;
use App\JobsCleaningQuoteFormSetup;
use App\ListTypes;
use App\ProductCategories;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;

class CleaningGeneralQuoteFormSettingsController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.generalCleaningQuoteFormSettings');
        $this->pageIcon = 'ti-file';
    }

    public function index()
    {
        $this->quote_form_setup  = JobsCleaningQuoteFormSetup::where(['tenant_id' => auth()->user()->tenant_id])->where('job_type_id', '=', '1')->first();
        $this->product_categories = ProductCategories::select('id', 'category_name')->where(['tenant_id' => auth()->user()->tenant_id])->get();
        $this->list_types = ListTypes::select('id', 'list_name')->where(['tenant_id' => auth()->user()->tenant_id])->get();
        
        $request = Input::get();
        $this->code_script = null;
        $this->code_script2 = null;
        $this->show_script = '';
        $this->show_script2 = '';
        $this->companies = Companies::where('tenant_id', '=', auth()->user()->tenant_id)->get();

        if (isset($request['company']) && !empty($request['company'])) {
            $company = $request['company'];
            if (isset($request['script1']) && !empty($request['script1'])) {
                $param = auth()->user()->tenant_id . '&company_id=' . $company . '&mode=close';
                $param = base64_encode($param);
                $this->code_script = '<!-- Get cleaning quote embed code start -->
                <script src="' . url('/') . '/c81e728d9d4c2f636f067f89cc14862c.js?key=' . $param . '"></script>
                <!-- Get cleaning quote embed code end -->';
            }

            if (isset($request['script2']) && !empty($request['script2'])) {
                $param = auth()->user()->tenant_id . '&company_id=' . $company . '&mode=open';
                $param = base64_encode($param);
                $this->code_script2 = '<!-- Get cleaning quote embed code start -->
                <script src="' . url('/') . '/c81e728d9d4c2f636f067f89cc14862c.js?key=' . $param . '"></script>
                <!-- Get cleaning quote embed code end -->';
            }
        }

        return view('admin.cleaning-general-quote-form-settings.index', $this->data);
    }


    public function saveGeneralQuoteFormSettings(Request $request, $id)
    {
        // dd($request);
        if (!empty($id)) {
            $obj = JobsCleaningQuoteFormSetup::findOrFail($id);
        } else {
            $obj = new JobsCleaningQuoteFormSetup();
        }
        $obj->tenant_id = auth()->user()->tenant_id;
        $obj->job_type_id = '1';
        $obj->services_category_id = $request->services_category_id;
        $obj->min_cleaners = $request->min_cleaners;
        $obj->max_cleaners = $request->max_cleaners;
        $obj->min_hours = $request->min_hours;
        $obj->max_hours = $request->max_hours;
        $obj->time_selector_list_type_id = $request->time_selector_list_type_id;
        $obj->extras_category_id = $request->extras_category_id;
        $obj->questions_list_type_id = @implode(',', $request->questions_list_type_id);

        $obj->updated_at = Carbon::now();
        $obj->updated_by = auth()->user()->id;
        $obj->save();

        return Reply::success(__('messages.generalCleaningQuoteFormSettingsUpdated'));
    }


    // public function createFormSettings(Request $request)
    // {
    //     //$autoQuoting = JobsMovingAutoQuoting::where(['tenant_id'=> auth()->user()->tenant_id])->first();
    //     $obj = new JobsMovingAutoQuoting();
    //     $obj->tenant_id = auth()->user()->tenant_id;
    //     $obj->tax_id_for_quote = $request->tax_id_for_quote;
    //     $obj->initial_op_status_id = $request->initial_op_status_id;
    //     $obj->auto_quote_enabled = $request->has('auto_quote_enabled') && $request->input('auto_quote_enabled') == 'Y' ? 'Y' : 'N';

    //     $obj->quoted_op_status_id = $request->quoted_op_status_id;
    //     $obj->failed_op_status_id = $request->failed_op_status_id;
    //     $obj->send_quote_fail_email_to = $request->send_quote_fail_email_to;
    //     $obj->send_auto_quote_email_to_customer = $request->has('send_auto_quote_email_to_customer') && $request->input('send_auto_quote_email_to_customer') == 'Y' ? 'Y' : 'N';

    //     $obj->quote_email_template_id = $request->quote_email_template_id;
    //     $obj->fail_email_template_id = $request->fail_email_template_id;
    //     $obj->created_by = $this->user->id;
    //     //$obj->updated_by = $this->user->id;
    //     $obj->save();

    //     return Reply::success(__('messages.auotoQuoteSaved'));
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }
}
