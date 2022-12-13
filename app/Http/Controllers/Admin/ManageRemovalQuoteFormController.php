<?php

namespace App\Http\Controllers\Admin;

use App\Companies;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\JobsMovingPricingAdditional;
use App\JobsMovingLocalMoves;
use App\JobsMovingPricingRegions;
use App\SysCountryStates;
use App\OrganisationSettings;
use App\Helper\Reply;
use App\JobsMovingAutoQuoting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;

class ManageRemovalQuoteFormController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Removals Quote Form';
        $this->pageIcon = 'icon-arrow-right15';
    }

    public function index()
    {
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
                $this->code_script = '<!-- Get quote embed code start -->
                <script src="' . url('/') . '/cfcd208495d565ef66e7dff9f98764da.js?key=' . $param . '"></script>
                <!-- Get quote embed code end -->';
            }

            if (isset($request['script2']) && !empty($request['script2'])) {
                $param = auth()->user()->tenant_id . '&company_id=' . $company . '&mode=open';
                $param = base64_encode($param);
                $this->code_script2 = '<!-- Get quote embed code start -->
                <script src="' . url('/') . '/cfcd208495d565ef66e7dff9f98764da.js?key=' . $param . '"></script>
                <!-- Get quote embed code end -->';
            }
        }

        $this->jobs_moving_auto_quoting  = JobsMovingAutoQuoting::where(['tenant_id' => auth()->user()->tenant_id])->first();
        return view('admin.removal-quote-form.index', $this->data);
    }

    public function saveRemovalQuoteFormSettings(Request $request, $id)
    {
        if (!empty($id)) {
            $obj =  JobsMovingAutoQuoting::findOrFail($id);
        } else {
            $obj = new JobsMovingAutoQuoting();
            $obj->created_at = Carbon::now();
        }
        $obj->tenant_id = auth()->user()->tenant_id;
        $obj->quote_form_redirect_after_submit = ($request->quote_form_redirect_after_submit == 'Y') ? 'Y' : 'N';
        $obj->quote_form_redirect_url = $request->quote_form_redirect_url;

        $obj->updated_at = Carbon::now();
        $obj->updated_by = auth()->user()->id;
        $obj->save();

        return Reply::success(__('messages.removalQuoteFormSettingsUpdated'));
    }
}
