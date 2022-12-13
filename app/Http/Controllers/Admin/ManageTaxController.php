<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SysCountryStates;
use App\OrganisationSettings;
use App\Helper\Reply;
use App\Tax;

class ManageTaxController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Taxes';
        $this->pageIcon = 'icon-percent';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $this->taxes = Tax::where(['tenant_id' => auth()->user()->tenant_id])
            ->orderBy('tax_name', 'asc')->get();

        return view('admin.taxes.index', $this->data);
    }

    public function ajaxCreateTax(Request $request)
    {
        $model = new Tax();
        $model->tenant_id = auth()->user()->tenant_id;
        $model->tax_name = $request->input('tax_name');
        $model->rate_percent = $request->input('rate_percent');
        $model->created_at = time();
        if ($model->save()) {
            $this->taxes = Tax::where(['tenant_id' => auth()->user()->tenant_id])->orderBy('tax_name', 'asc')->get();
            $response['error'] = 0;
            $response['message'] = 'Tax has been added';
            $response['html'] = view('admin.taxes.tax_grid')->with(['taxes' => $this->taxes])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxUpdateTax(Request $request)
    {
        $id = $request->input('id');
        $model = Tax::find($id);
        $model->tax_name = $request->input('tax_name');
        $model->rate_percent = $request->input('rate_percent');
        $model->updated_at = time();
        if ($model->save()) {
            $this->taxes = Tax::where(['tenant_id' => auth()->user()->tenant_id])->orderBy('tax_name', 'asc')->get();
            $response['error'] = 0;
            $response['message'] = 'Tax has been updated';
            $response['html'] = view('admin.taxes.tax_grid')->with(['taxes' => $this->taxes])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxDestroyTax(Request $request)
    {
        Tax::destroy($request->id);
        $this->taxes = Tax::where(['tenant_id' => auth()->user()->tenant_id])->orderBy('tax_name', 'asc')->get();
            $response['error'] = 0;
            $response['message'] = 'Tax has been updated';
            $response['html'] = view('admin.taxes.tax_grid')->with(['taxes' => $this->taxes])->render();
            return json_encode($response);
    }
}
