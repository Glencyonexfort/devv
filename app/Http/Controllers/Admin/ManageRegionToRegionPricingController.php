<?php

namespace App\Http\Controllers\Admin;

use App\JobsMovingPricingRegions;
use Illuminate\Http\Request;
use App\JobsMovingRegionToRegionPricing;
use App\SysCountryStates;
use App\OrganisationSettings;
use Illuminate\Support\Facades\DB;

class ManageRegionToRegionPricingController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Region to Region Pricing';
        $this->pageIcon = 'ti-map';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $this->regionToRegionPricings = JobsMovingRegionToRegionPricing::select(DB::raw("*, jobs_moving_region_to_region_pricing.id as id"))
            ->where(['jobs_moving_region_to_region_pricing.tenant_id' => auth()->user()->tenant_id])
            ->join('jobs_moving_pricing_regions', 'jobs_moving_pricing_regions.id', '=', 'jobs_moving_region_to_region_pricing.from_region_id')
            ->where('jobs_moving_region_to_region_pricing.tenant_id', '=', auth()->user()->tenant_id)
            ->where('jobs_moving_pricing_regions.tenant_id', '=', auth()->user()->tenant_id)
            ->orderBy('jobs_moving_pricing_regions.region_name', 'asc')
            ->orderBy('jobs_moving_region_to_region_pricing.to_region_id', 'asc')
            ->orderBy('jobs_moving_region_to_region_pricing.cbm_min', 'asc')->get();
        $this->jobs_pricing_regions = JobsMovingPricingRegions::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        // dd($this->regionToRegionPricings);
        return view('admin.region-to-region-pricing.index', $this->data);
    }

    public function ajaxCreateRegionToRegionPricing(Request $request)
    {
        $model = new JobsMovingRegionToRegionPricing();
        $model->tenant_id = auth()->user()->tenant_id;
        $model->from_region_id = $request->input('from_region_id');
        $model->to_region_id = $request->input('to_region_id');
        $model->cbm_min = $request->input('cbm_min');
        $model->cbm_max = $request->input('cbm_max');
        $model->price_flat = $request->input('price_flat');
        $model->price_per_cbm = $request->input('price_per_cbm');
        $model->min_price = $request->input('min_price');
        $model->created_by = auth()->user()->id;
        $model->created_at = time();
        if ($model->save()) {
            $this->regionToRegionPricings = JobsMovingRegionToRegionPricing::where(['jobs_moving_region_to_region_pricing.tenant_id' => auth()->user()->tenant_id])
            ->join('jobs_moving_pricing_regions', 'jobs_moving_pricing_regions.id', '=', 'jobs_moving_region_to_region_pricing.from_region_id')
            ->where('jobs_moving_region_to_region_pricing.tenant_id', '=', auth()->user()->tenant_id)
            ->where('jobs_moving_pricing_regions.tenant_id', '=', auth()->user()->tenant_id)
            ->orderBy('jobs_moving_pricing_regions.region_name', 'asc')
            ->orderBy('jobs_moving_region_to_region_pricing.to_region_id', 'asc')
            ->orderBy('jobs_moving_region_to_region_pricing.cbm_min', 'asc')->get();
            $this->jobs_pricing_regions = JobsMovingPricingRegions::where('tenant_id', '=', auth()->user()->tenant_id)->get();

            $response['error'] = 0;
            $response['message'] = 'Region To Region Pricing has been added';
            $response['regionpricing_html'] = view('admin.region-to-region-pricing.region_pricing_grid')->with(['regionToRegionPricings' => $this->regionToRegionPricings, 'jobs_pricing_regions' => $this->jobs_pricing_regions, 'global' => $this->global])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxUpdateRegionToRegionPricing(Request $request)
    {
        $pricing_region_id = $request->input('pricing_region_id');
        $model = JobsMovingRegionToRegionPricing::find($pricing_region_id);
        $model->from_region_id = $request->input('from_region_id');
        $model->to_region_id = $request->input('to_region_id');
        $model->cbm_min = $request->input('cbm_min');
        $model->cbm_max = $request->input('cbm_max');
        $model->price_flat = $request->input('price_flat');
        $model->price_per_cbm = $request->input('price_per_cbm');
        $model->min_price = $request->input('min_price');
        $model->updated_by = auth()->user()->id;
        $model->updated_at = time();
        if ($model->save()) {
            $this->regionToRegionPricings = JobsMovingRegionToRegionPricing::where(['jobs_moving_region_to_region_pricing.tenant_id' => auth()->user()->tenant_id])
            ->join('jobs_moving_pricing_regions', 'jobs_moving_pricing_regions.id', '=', 'jobs_moving_region_to_region_pricing.from_region_id')
            ->where('jobs_moving_region_to_region_pricing.tenant_id', '=', auth()->user()->tenant_id)
            ->where('jobs_moving_pricing_regions.tenant_id', '=', auth()->user()->tenant_id)
            ->orderBy('jobs_moving_pricing_regions.region_name', 'asc')
            ->orderBy('jobs_moving_region_to_region_pricing.to_region_id', 'asc')
            ->orderBy('jobs_moving_region_to_region_pricing.cbm_min', 'asc')->get();
            $this->jobs_pricing_regions = JobsMovingPricingRegions::where('tenant_id', '=', auth()->user()->tenant_id)->get();

            $response['error'] = 0;
            $response['id'] = $pricing_region_id;
            $response['message'] = 'Region To Region Pricing has been updated';
            $response['regionpricing_html'] = view('admin.region-to-region-pricing.region_pricing_grid')->with(['regionToRegionPricings' => $this->regionToRegionPricings, 'jobs_pricing_regions' => $this->jobs_pricing_regions, 'global' => $this->global,])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxDestroyRegionToRegionPricing(Request $request)
    {
        JobsMovingRegionToRegionPricing::destroy($request->id);
        $this->regionToRegionPricings = JobsMovingRegionToRegionPricing::where(['jobs_moving_region_to_region_pricing.tenant_id' => auth()->user()->tenant_id])
            ->join('jobs_moving_pricing_regions', 'jobs_moving_pricing_regions.id', '=', 'jobs_moving_region_to_region_pricing.from_region_id')
            ->where('jobs_moving_region_to_region_pricing.tenant_id', '=', auth()->user()->tenant_id)
            ->where('jobs_moving_pricing_regions.tenant_id', '=', auth()->user()->tenant_id)
            ->orderBy('jobs_moving_pricing_regions.region_name', 'asc')
            ->orderBy('jobs_moving_region_to_region_pricing.to_region_id', 'asc')
            ->orderBy('jobs_moving_region_to_region_pricing.cbm_min', 'asc')->get();
            $this->jobs_pricing_regions = JobsMovingPricingRegions::where('tenant_id', '=', auth()->user()->tenant_id)->get();

        $response['error'] = 0;
        $response['message'] = 'Region To Region Pricing has been deleted';
        $response['regionpricing_html'] = view('admin.region-to-region-pricing.region_pricing_grid')->with(['regionToRegionPricings' => $this->regionToRegionPricings, 'jobs_pricing_regions' => $this->jobs_pricing_regions, 'global' => $this->global,])->render();
        return json_encode($response);
    }
}
