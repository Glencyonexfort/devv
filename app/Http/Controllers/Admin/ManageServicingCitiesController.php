<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\JobsMovingPricingAdditional;
use App\JobsMovingLocalMoves;
use App\TenantServicingCities;
use App\SysCountryStates;
use App\OrganisationSettings;
use App\Helper\Reply;
use App\TenantApiDetail;

class ManageServicingCitiesController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Servicing Cities';
        $this->pageIcon = 'ti-map';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function servicingCities()
    {
        $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $this->servicingCities = TenantServicingCities::where(['deleted' => '0', 'tenant_servicing_cities.tenant_id' => auth()->user()->tenant_id])->orderBy('tenant_servicing_cities.servicing_city', 'asc')->get();

        $this->tenant_api_details = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'GoogleMaps'])->first();
        return view('admin.servicing-cities.index', $this->data);
    }

    public function ajaxCreateServicingCities(Request $request)
    {
        $model = new TenantServicingCities();
        $model->tenant_id = auth()->user()->tenant_id;
        $model->servicing_city = $request->input('servicing_city');
        $model->created_by = auth()->user()->id;
        $model->created_at = time();
        if ($model->save()) {
            $this->servicingCities = TenantServicingCities::where(['deleted' => '0','tenant_servicing_cities.tenant_id' => auth()->user()->tenant_id])
                ->orderBy('tenant_servicing_cities.servicing_city', 'asc');

            $response['error'] = 0;
            $response['message'] = 'Servicing City has been added';
            $response['response_html'] = view('admin.servicing-cities.partial_grid')->with(['servicingCities' => $this->servicingCities])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxUpdateServicingCities(Request $request)
    {
        $row_id = $request->input('row_id');
        $model = TenantServicingCities::find($row_id);
        $model->servicing_city = $request->input('servicing_city');
        $model->updated_by = auth()->user()->id;
        $model->updated_at = time();
        if ($model->save()) {
            $this->servicingCities = TenantServicingCities::where(['deleted' => '0','tenant_servicing_cities.tenant_id' => auth()->user()->tenant_id])
                ->orderBy('tenant_servicing_cities.servicing_city', 'asc');

            $response['error'] = 0;
            $response['id'] = $row_id;
            $response['message'] = 'Servicing City has been updated';
            $response['response_html'] = view('admin.servicing-cities.partial_grid')->with(['servicingCities' => $this->servicingCities])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxDestroyServicingCities(Request $request)
    {
        $row = TenantServicingCities::find($request->id);
        if ($row) {
            $row->deleted = '1';
            $row->save();
            $this->servicingCities = TenantServicingCities::where(['deleted' => '0','tenant_servicing_cities.tenant_id' => auth()->user()->tenant_id])
                ->orderBy('tenant_servicing_cities.servicing_city', 'asc');
            $response['error'] = 0;
            $response['message'] = 'Servicing City has been deleted';
            $response['response_html'] = view('admin.servicing-cities.partial_grid')->with(['servicingCities' => $this->servicingCities])->render();
            return json_encode($response);
        }
    }
}
