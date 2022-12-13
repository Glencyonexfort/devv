<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\JobsMovingPricingAdditional;
use App\JobsMovingLocalMoves;
use App\JobsMovingDepotLocations;
use App\JobsMovingPricingRegions;
use App\Helper\Reply;

class ManageHourlySettingsController extends AdminBaseController
{
   public function __construct() {
        parent::__construct();
        $this->pageTitle = __('app.menu.hourlySettings');
        $this->pageIcon = 'ti-file';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->pricingAdditional = JobsMovingPricingAdditional::where(['tenant_id'=> auth()->user()->tenant_id])->first();

        $this->truckSizes = JobsMovingLocalMoves::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('truck_size_in_ton', 'asc')->get();

        $this->depotLocations = JobsMovingDepotLocations::select(
                        'jobs_moving_pricing_regions.region_name','jobs_moving_pricing_regions.region_suburb_name','jobs_moving_depot_locations.id', 'jobs_moving_depot_locations.region_id','jobs_moving_depot_locations.depot_suburb')
                        ->leftjoin('jobs_moving_pricing_regions', 'jobs_moving_pricing_regions.id', '=', 'jobs_moving_depot_locations.region_id')
                        ->where(['jobs_moving_depot_locations.tenant_id'=>auth()->user()->tenant_id])
                        ->orderBy('jobs_moving_pricing_regions.region_name', 'asc')
                        ->orderBy('jobs_moving_depot_locations.depot_suburb', 'asc')
                        ->get();

        $this->pricingRegions = JobsMovingPricingRegions::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('region_name', 'asc')->get();

        return view('admin.hourly-settings.index', $this->data);
    }

    public function saveHourlySettingsData(Request $request, $id) 
    {
        //dd();
        $pricingAdditional = JobsMovingPricingAdditional::findOrFail($id);

        $pricingAdditional->local_move_excess_minutes_tier1 = $request->local_move_excess_minutes_tier1;
        $pricingAdditional->local_move_excess_minutes_tier2 = $request->local_move_excess_minutes_tier2;
        $pricingAdditional->use_hourly_pricing_local_moves = $request->has('use_hourly_pricing_local_moves') && $request->input('use_hourly_pricing_local_moves') == 'Y' ? 'Y' : 'N';

        $pricingAdditional->is_deposit_for_hourly_pricing_fixed_amt = $request->has('is_deposit_for_hourly_pricing_fixed_amt') && $request->input('is_deposit_for_hourly_pricing_fixed_amt') == 'Y' ? 'Y' : 'N';

        if($request->has('is_deposit_for_hourly_pricing_fixed_amt') && $request->input('is_deposit_for_hourly_pricing_fixed_amt') == 'Y')
        {
            $pricingAdditional->deposit_amount_hourly_pricing = $request->deposit_amount_hourly_pricing;
        } else {
            $pricingAdditional->deposit_percent_hourly_pricing = ($request->deposit_percent_hourly_pricing)/100;
        }

        $pricingAdditional->hourly_pricing_min_pricing_percent = ($request->hourly_pricing_min_pricing_percent)/100;
        
        $pricingAdditional->hourly_pricing_include_depot_pickup = $request->has('hourly_pricing_include_depot_pickup') && $request->input('hourly_pricing_include_depot_pickup') == 'Y' ? 'Y' : 'N';
        
        $pricingAdditional->hourly_pricing_include_drop_off_depot = $request->has('hourly_pricing_include_drop_off_depot') && $request->input('hourly_pricing_include_drop_off_depot') == 'Y' ? 'Y' : 'N';

        $pricingAdditional->hourly_pricing_include_loading_time = $request->has('hourly_pricing_include_loading_time') && $request->input('hourly_pricing_include_loading_time') == 'Y' ? 'Y' : 'N';

        $pricingAdditional->hourly_pricing_include_unloading_time = $request->has('hourly_pricing_include_unloading_time') && $request->input('hourly_pricing_include_unloading_time') == 'Y' ? 'Y' : 'N';

        $pricingAdditional->hourly_pricing_include_pickup_drop_off = $request->has('hourly_pricing_include_pickup_drop_off') && $request->input('hourly_pricing_include_pickup_drop_off') == 'Y' ? 'Y' : 'N';

        $pricingAdditional->hourly_pricing_has_booking_fee = $request->has('hourly_pricing_has_booking_fee') && $request->input('hourly_pricing_has_booking_fee') == 'Y' ? 'Y' : 'N';

        if($request->has('hourly_pricing_has_booking_fee') && $request->input('hourly_pricing_has_booking_fee') == 'Y')
        {
            $pricingAdditional->hourly_pricing_booking_fee = $request->hourly_pricing_booking_fee;
        }
        $pricingAdditional->hourly_pricing_min_hours = $request->hourly_pricing_min_hours;
        $pricingAdditional->updated_by = $this->user->id;
        $pricingAdditional->save();

        return Reply::success(__('messages.hourlySettingsUpdated'));
    }


    public function createHourlySettingsData(Request $request) 
    {
        $pricingAdditional = new JobsMovingPricingAdditional();
        $pricingAdditional->tenant_id = auth()->user()->tenant_id;
        
        $pricingAdditional->local_move_excess_minutes_tier1 = $request->local_move_excess_minutes_tier1;
        $pricingAdditional->local_move_excess_minutes_tier2 = $request->local_move_excess_minutes_tier2;

        $pricingAdditional->use_hourly_pricing_local_moves = $request->has('use_hourly_pricing_local_moves') && $request->input('use_hourly_pricing_local_moves') == 'Y' ? 'Y' : 'N';

        $pricingAdditional->is_deposit_for_hourly_pricing_fixed_amt = $request->has('is_deposit_for_hourly_pricing_fixed_amt') && $request->input('is_deposit_for_hourly_pricing_fixed_amt') == 'Y' ? 'Y' : 'N';

        if($request->has('is_deposit_for_hourly_pricing_fixed_amt') && $request->input('is_deposit_for_hourly_pricing_fixed_amt') == 'Y')
        {
            $pricingAdditional->deposit_amount_hourly_pricing = $request->deposit_amount_hourly_pricing;
        } else {
            $pricingAdditional->deposit_percent_hourly_pricing = ($request->deposit_percent_hourly_pricing)/100;
            
        }

        $pricingAdditional->hourly_pricing_min_pricing_percent = ($request->hourly_pricing_min_pricing_percent)/100;

        $pricingAdditional->hourly_pricing_include_depot_pickup = $request->has('hourly_pricing_include_depot_pickup') && $request->input('hourly_pricing_include_depot_pickup') == 'Y' ? 'Y' : 'N';

        $pricingAdditional->hourly_pricing_include_drop_off_depot = $request->has('hourly_pricing_include_drop_off_depot') && $request->input('hourly_pricing_include_drop_off_depot') == 'Y' ? 'Y' : 'N';

        $pricingAdditional->hourly_pricing_include_loading_time = $request->has('hourly_pricing_include_loading_time') && $request->input('hourly_pricing_include_loading_time') == 'Y' ? 'Y' : 'N';

        $pricingAdditional->hourly_pricing_include_unloading_time = $request->has('hourly_pricing_include_unloading_time') && $request->input('hourly_pricing_include_unloading_time') == 'Y' ? 'Y' : 'N';

        $pricingAdditional->hourly_pricing_include_pickup_drop_off = $request->has('hourly_pricing_include_pickup_drop_off') && $request->input('hourly_pricing_include_pickup_drop_off') == 'Y' ? 'Y' : 'N';

        $pricingAdditional->hourly_pricing_has_booking_fee = $request->has('hourly_pricing_has_booking_fee') && $request->input('hourly_pricing_has_booking_fee') == 'Y' ? 'Y' : 'N';

        if($request->has('hourly_pricing_has_booking_fee') && $request->input('hourly_pricing_has_booking_fee') == 'Y')
        {
            $pricingAdditional->hourly_pricing_booking_fee = $request->hourly_pricing_booking_fee;
        }


        $pricingAdditional->created_by = $this->user->id;
        $pricingAdditional->save();

        return Reply::success(__('messages.hourlyettingsSaved'));
    }


     public function ajaxCreatedepotLocation(Request $request)
    {
        $model = new JobsMovingDepotLocations();
        $model->tenant_id = auth()->user()->tenant_id;
        $model->region_id = $request->input('region_id');
        $model->depot_suburb = $request->input('depot_suburb');
        $model->created_by = auth()->user()->id;
        $model->created_at = time();
        $model->updated_by = auth()->user()->id;
        $model->updated_at = time();
        //print_r($model);exit;
        if ($model->save()) {
            $depotLocations = JobsMovingDepotLocations::select(
                        'jobs_moving_pricing_regions.region_name','jobs_moving_pricing_regions.region_suburb_name','jobs_moving_depot_locations.id', 'jobs_moving_depot_locations.region_id','jobs_moving_depot_locations.depot_suburb')
                        ->leftjoin('jobs_moving_pricing_regions', 'jobs_moving_pricing_regions.id', '=', 'jobs_moving_depot_locations.region_id')
                        ->where(['jobs_moving_depot_locations.tenant_id'=>auth()->user()->tenant_id])
                        ->orderBy('jobs_moving_pricing_regions.region_name', 'asc')
                        ->orderBy('jobs_moving_depot_locations.depot_suburb', 'asc')
                        ->get();
            $pricingRegions = JobsMovingPricingRegions::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('region_name', 'asc')->get();

            $response['error'] = 0;
            $response['message'] = 'Regional Depot has been added';
            $response['depotlocation_html'] = view('admin.hourly-settings.regionaldepot_grid')->with(['depotLocations' => $depotLocations, 'pricingRegions' => $pricingRegions])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxUpdatedepotLocation(Request $request)
    {
        $depot_location_id = $request->input('depot_location_id');
        $model = JobsMovingDepotLocations::find($depot_location_id);
        $model->region_id = $request->input('region_id');
        $model->depot_suburb = $request->input('depot_suburb');
        $model->updated_by = auth()->user()->id;
        $model->updated_at = time();
        //print_r($model);exit;
        if ($model->save()) {
            $depotLocations = JobsMovingDepotLocations::select(
                        'jobs_moving_pricing_regions.region_name','jobs_moving_pricing_regions.region_suburb_name','jobs_moving_depot_locations.id', 'jobs_moving_depot_locations.region_id','jobs_moving_depot_locations.depot_suburb')
                        ->leftjoin('jobs_moving_pricing_regions', 'jobs_moving_pricing_regions.id', '=', 'jobs_moving_depot_locations.region_id')
                        ->where(['jobs_moving_depot_locations.tenant_id'=>auth()->user()->tenant_id])
                        ->orderBy('jobs_moving_pricing_regions.region_name', 'asc')
                        ->orderBy('jobs_moving_depot_locations.depot_suburb', 'asc')
                        ->get();
            $pricingRegions = JobsMovingPricingRegions::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('region_name', 'asc')->get();

            $response['error'] = 0;
            $response['id'] = $depot_location_id;
            $response['message'] = 'Regional Depot has been updated';
            $response['depotlocation_html'] = view('admin.hourly-settings.regionaldepot_grid')->with(['depotLocations' => $depotLocations, 'pricingRegions' => $pricingRegions])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxDestroyDepotLocation(Request $request)
    {
        JobsMovingDepotLocations::destroy($request->id);
        $depotLocations = JobsMovingDepotLocations::select(
                        'jobs_moving_pricing_regions.region_name','jobs_moving_pricing_regions.region_suburb_name','jobs_moving_depot_locations.id', 'jobs_moving_depot_locations.region_id','jobs_moving_depot_locations.depot_suburb')
                        ->leftjoin('jobs_moving_pricing_regions', 'jobs_moving_pricing_regions.id', '=', 'jobs_moving_depot_locations.region_id')
                        ->where(['jobs_moving_depot_locations.tenant_id'=>auth()->user()->tenant_id])
                        ->orderBy('jobs_moving_pricing_regions.region_name', 'asc')
                        ->orderBy('jobs_moving_depot_locations.depot_suburb', 'asc')
                        ->get();
        $pricingRegions = JobsMovingPricingRegions::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('region_name', 'asc')->get();
        $response['error'] = 0;
        $response['message'] = 'Regional Depot has been deleted';
        $response['depotlocation_html'] = view('admin.hourly-settings.regionaldepot_grid')->with(['depotLocations' => $depotLocations, 'pricingRegions' => $pricingRegions])->render();
        return json_encode($response);
    }


    public function ajaxCreateTruckSize(Request $request)
    {
        $model = new JobsMovingLocalMoves();
        $model->tenant_id = auth()->user()->tenant_id;
        $model->min_cbm = $request->input('min_cbm');
        $model->max_cbm = $request->input('max_cbm');
        $model->truck_size_in_ton = $request->input('truck_size_in_ton');
        $model->loading_mins = $request->input('loading_mins');
        $model->unloading_mins = $request->input('unloading_mins');
        $model->hourly_rate = $request->input('hourly_rate');
        $model->created_by = auth()->user()->id;
        $model->created_at = time();
        //print_r($model);exit;
        if ($model->save()) {
            $truckSizes = JobsMovingLocalMoves::where(['tenant_id' => auth()->user()->tenant_id])->orderBy('truck_size_in_ton', 'ASC')->get();

            $response['error'] = 0;
            //$response['id'] = $local_moves_id;
            $response['message'] = 'Truck Size based rate has been added';
            $response['trucksize_html'] = view('admin.hourly-settings.trucksize_grid')->with(['truckSizes' => $truckSizes])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxUpdateTruckSize(Request $request)
    {
        $local_moves_id = $request->input('local_moves_id');
        $model = JobsMovingLocalMoves::find($local_moves_id);
        $model->min_cbm = $request->input('min_cbm');
        $model->max_cbm = $request->input('max_cbm');
        $model->truck_size_in_ton = $request->input('truck_size_in_ton');
        $model->loading_mins = $request->input('loading_mins');
        $model->unloading_mins = $request->input('unloading_mins');
        $model->hourly_rate = $request->input('hourly_rate');
        $model->updated_by = auth()->user()->id;
        $model->updated_at = time();
        //print_r($model);exit;
        if ($model->save()) {
            $truckSizes = JobsMovingLocalMoves::where(['tenant_id' => auth()->user()->tenant_id])->orderBy('truck_size_in_ton', 'ASC')->get();

            $response['error'] = 0;
            $response['id'] = $local_moves_id;
            $response['message'] = 'Truck Size based rate has been updated';
            $response['trucksize_html'] = view('admin.hourly-settings.trucksize_grid')->with(['truckSizes' => $truckSizes])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxDestroyTruckSize(Request $request)
    {
        JobsMovingLocalMoves::destroy($request->id);
        $truckSizes = JobsMovingLocalMoves::where(['tenant_id' => auth()->user()->tenant_id])->orderBy('truck_size_in_ton', 'ASC')->get();
        $response['error'] = 0;
        $response['message'] = 'Truck Size based rate has been deleted';
        $response['trucksize_html'] = view('admin.hourly-settings.trucksize_grid')->with(['truckSizes' => $truckSizes])->render();
        return json_encode($response);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
