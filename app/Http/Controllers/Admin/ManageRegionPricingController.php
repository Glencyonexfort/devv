<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\JobsMovingPricingAdditional;
use App\JobsMovingLocalMoves;
use App\JobsMovingPricingRegions;
use App\SysCountryStates;
use App\OrganisationSettings;
use App\Helper\Reply;

class ManageRegionPricingController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Pricing Regions';
        $this->pageIcon = 'ti-map';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function pricingRegion()
    {
        $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $this->regionPricings = JobsMovingPricingRegions::where(['jobs_moving_pricing_regions.tenant_id' => auth()->user()->tenant_id])
            ->join('sys_country_states', 'sys_country_states.state_id', '=', 'jobs_moving_pricing_regions.state_id')
            ->orderBy('jobs_moving_pricing_regions.state_id', 'asc')
            ->orderBy('jobs_moving_pricing_regions.region_name', 'asc')->get();
        $this->sys_country_states = SysCountryStates::where('country_id', '=', $this->organisation_settings->business_country_id)->get();

        return view('admin.pricing-region.index', $this->data);
    }

    public function ajaxCreateRegionPricing(Request $request)
    {
        $model = new JobsMovingPricingRegions();
        $model->tenant_id = auth()->user()->tenant_id;
        $model->state_id = $request->input('state_id');
        $model->region_name = $request->input('region_name');
        $model->region_suburb_name = $request->input('region_suburb_name');
        $model->created_by = auth()->user()->id;
        $model->created_at = time();
        if ($model->save()) {
            $this->regionPricings = JobsMovingPricingRegions::where(['jobs_moving_pricing_regions.tenant_id' => auth()->user()->tenant_id])
            ->orderBy('jobs_moving_pricing_regions.state_id', 'asc')
            ->orderBy('jobs_moving_pricing_regions.region_name', 'asc');

            $response['error'] = 0;
            $response['message'] = 'Pricing Region has been added';
            $response['regionpricing_html'] = view('admin.pricing-region.pricing_region_grid')->with(['regionPricings' => $this->regionPricings])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxUpdateRegionPricing(Request $request)
    {
        $pricing_region_id = $request->input('pricing_region_id');
        $model = JobsMovingPricingRegions::find($pricing_region_id);
        $model->state_id = $request->input('state_id');
        $model->region_name = $request->input('region_name');
        $model->region_suburb_name = $request->input('region_suburb_name');
        $model->updated_by = auth()->user()->id;
        $model->updated_at = time();
        if ($model->save()) {
            $this->regionPricings = JobsMovingPricingRegions::where(['jobs_moving_pricing_regions.tenant_id' => auth()->user()->tenant_id])
                ->orderBy('jobs_moving_pricing_regions.state_id', 'asc')
                ->orderBy('jobs_moving_pricing_regions.region_name', 'asc');

            $response['error'] = 0;
            $response['id'] = $pricing_region_id;
            $response['message'] = 'Pricing Region has been updated';
            $response['regionpricing_html'] = view('admin.pricing-region.pricing_region_grid')->with(['regionPricings' => $this->regionPricings])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxDestroyRegionPricing(Request $request)
    {
        JobsMovingPricingRegions::destroy($request->id);
        $this->regionPricings = JobsMovingPricingRegions::where(['jobs_moving_pricing_regions.tenant_id' => auth()->user()->tenant_id])
            ->orderBy('jobs_moving_pricing_regions.state_id', 'asc')
            ->orderBy('jobs_moving_pricing_regions.region_name', 'asc');
        $response['error'] = 0;
        $response['message'] = 'Pricing Region has been deleted';
        $response['regionpricing_html'] = view('admin.pricing-region.pricing_region_grid')->with(['regionPricings' => $this->regionPricings])->render();
        return json_encode($response);
    }
}
