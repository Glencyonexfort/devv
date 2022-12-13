<?php

namespace App\Console\Commands;

use App\Companies;
use Illuminate\Console\Command;
use App\CRMActivityLog;
use App\CRMActivityLogAttachment;
use App\CRMContactDetail;
use App\CRMContacts;
use App\CRMLeads;
use App\CRMOpPipelineStatuses;
use App\CRMOpportunities;
use App\CustomerDetails;
use App\Customers;
use App\EmailTemplateAttachments;
use App\EmailTemplates;
use App\Invoice;
use App\InvoiceSetting;
use App\InvoiceItems;
use App\JobsMoving;
use App\JobsMovingInventory;
use App\JobsMovingLegs;
use App\Mail\CustomerMail;
use App\Mail\sendMail;
use App\OrganisationSettings;
use App\QuoteItem;
use App\Quotes;
use App\Setting;
use App\SMSTemplates;
use App\Tax;
use App\TenantApiDetail;
use App\User;
use App\Vehicles;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;

class AutoQuote extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto-quote';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to auto quote removals.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    //START:: Removal Auto Qoute Program
    public function handle()
    {
        ini_set('max_execution_time', 0);
        // Get All Tenants with auto quote enabled----------------------//
    
        $tenants = DB::table('jobs_moving_auto_quoting as t1')
            ->leftJoin('crm_op_pipeline_statuses as t2', 't2.id', '=', 't1.initial_op_status_id')
            ->select('t1.*', 't2.pipeline_status')
            ->where(['t1.auto_quote_enabled' => 'Y'])
            ->get();
    
        if (count($tenants)) {
            echo '<pre>';
            print_r($tenants);
            foreach ($tenants as $tenant) {
    
                // Job Moving Price for each tenant-------------------//
    
                $job_price = DB::table('jobs_moving_pricing_additional as t1')
                    ->select('t1.*')
                    ->where(['t1.tenant_id' => $tenant->tenant_id])
                    ->first();
    
                // echo '<pre>';
                //     print_r($job_price);
                //     echo '</pre>';
    
                // Job Moving for each tenant-------------------------//
    
                $job_moving = DB::table('crm_opportunities')
                    ->join('jobs_moving', 'jobs_moving.crm_opportunity_id', '=', 'crm_opportunities.id')
                    ->select('jobs_moving.*', 'crm_opportunities.lead_id')
                    ->where(['crm_opportunities.tenant_id' => $tenant->tenant_id, 'crm_opportunities.op_status' => $tenant->pipeline_status])
                    ->whereNotNull('jobs_moving.pickup_property_type')
                    ->whereNotNull('jobs_moving.pickup_suburb')
                    ->whereNotNull('jobs_moving.delivery_suburb')
                    ->get();
                // echo '<pre>';
                //     print_r($job_moving);
                //     echo '</pre>';
                // Check Job Moving
                if (count($job_moving)) {
                    foreach ($job_moving as $job) {
    
                        $var_cbm = 0;
                        $var_pickup_region_id = '';
                        $var_pickup_region = '';
                        $var_pickup_km_nearest_region = 0;
                        $var_drop_off_region_id = '';
                        $var_drop_off_region = '';
                        $var_drop_off_km_nearest_region = 0;
                        $var_pickup_excess_charges = 0;
                        $var_drop_off_excess_charges = 0;
                        $var_pickup_stairs_lift_charges = 0;
                        $var_drop_off_stairs_lift_charges = 0;
                        $var_status = '';
                        $var_fail_reason = '';
                        $var_removal_fee = 0;
                        $cbm_per_bedroom = 0;
                        $cbm_per_living_room = 0;
                        $var_removal_fee = 0;
                        $var_price_structure = '';
                        $var_use_hourly_pricing = '';
                        $var_depot_to_pickup_time = 0;
                        $var_drop_off_to_depot_time = 0;
                        $var_pickup_to_dropoff_time = 0;
                        $var_loading_unloading_time = 0;
                        $var_excess_time = 0;
                        $var_hourly_rate = 0;
                        $var_total_time = 0;
                        $est_first_leg_start_time="";
    
                        //*************************/ Step 1 : Calculate the cbm **********************//
    
                        //start:: property type House or Flat
                        if ($job->pickup_property_type == "House" || $job->pickup_property_type == "Flat") {
                            $other_rooms = explode(',', $job->pickup_other_rooms);
    
                            $pickup_other_rooms_value = DB::table('property_category_options as t1')
                                ->where(['t1.tenant_id' => $tenant->tenant_id])
                                ->whereIn('t1.options', $other_rooms)
                                ->sum('t1.m3_value');
    
                            $speciality_items = explode(',', $job->pickup_speciality_items);
    
                            $pickup_speciality_items = DB::table('property_category_options as t1')
                                ->where(['t1.tenant_id' => $tenant->tenant_id])
                                ->whereIn('t1.options', $speciality_items)
                                ->sum('t1.m3_value');
    
                            $pickup_furnishing = DB::table('property_category_options as t1')
                                ->select('t1.other_value')
                                ->where(['t1.tenant_id' => $tenant->tenant_id, 't1.options' => $job->pickup_furnishing])
                                ->first();
    
                            if (isset($job_price)) {
                                $cbm_per_bedroom = $job_price->cbm_per_bedroom;
                                $cbm_per_living_room = $job_price->cbm_per_living_room;
                            }
                            $var_cbm = ($job->pickup_bedrooms * $cbm_per_bedroom) + ($job->pickup_living_areas * $cbm_per_living_room) + $pickup_other_rooms_value + $pickup_speciality_items;
    
                            if (isset($pickup_furnishing)) {
                                $var_cbm = $var_cbm * $pickup_furnishing->other_value;
                            }
                            //end:: property type House or Flat
    
                        } elseif ($job->pickup_property_type == "Storage Facility") {
                            $var_cbm = $job->storage_cbm;
                        }

                        echo '<br/><fieldset>Tenant:' . $tenant->tenant_id;
                        echo '<br/>Job Id:' . $job->job_id;
                        echo '<br/>Calculated CBM:' . $var_cbm;
                        echo '<br/><br/>Pickup Suburb: ' . $job->pickup_suburb;
                        echo '<br/><br/>Delivery Suburb: ' . $job->delivery_suburb;
    
                        //*********************/ Step 2 : Calculate pickup suburb's nearest region and distance ********************//
    
                        $pickupRegionDist = $this->calculateDistancebyNearestRegion($tenant->tenant_id, $job->pickup_suburb);
                        if ($pickupRegionDist['is_true'] == 1) {
                            $region_pickup_detail = explode('|', $pickupRegionDist['region']);
                            $var_pickup_region_id = $region_pickup_detail['0'];
                            $var_pickup_region = $region_pickup_detail['1'];
                        }
                        $var_pickup_km_nearest_region = $pickupRegionDist['min_distance'];
    
                        //********************* Step 3 : Calculate delivery suburb's nearest region and distance ********************
    
                        $deliveryRegionDist = $this->calculateDistancebyNearestRegion($tenant->tenant_id, $job->delivery_suburb);
                        if ($deliveryRegionDist['is_true'] == 1) {
                            $region_drop_off_detail = explode('|', $deliveryRegionDist['region']);
                            $var_drop_off_region_id = $region_drop_off_detail['0'];
                            $var_drop_off_region = $region_drop_off_detail['1'];
                        }
                        $var_drop_off_km_nearest_region = $deliveryRegionDist['min_distance'];
    
                        echo '<br/><br/>DISTANCE';
                        echo '<br/>var_pickup_region_id: ' . $var_pickup_region_id;
                        echo '<br/>var_pickup_region: ' . $var_pickup_region;
                        echo '<br/>var_pickup_km_nearest_region: ' . $var_pickup_km_nearest_region;
    
                        echo '<br/>var_drop_off_region_id: ' . $var_drop_off_region_id;
                        echo '<br/>var_drop_off_region: ' . $var_drop_off_region;
                        echo '<br/>var_drop_off_km_nearest_region: ' . $var_drop_off_km_nearest_region;
    
                        if ($job_price) {
    
                            //************************* Step 3.5 : Find the Price Stucture*******************************
    
                            $var_use_hourly_pricing = $job_price->use_hourly_pricing_local_moves;
                            if ($var_pickup_region_id == $var_drop_off_region_id && $var_use_hourly_pricing == "Y") {
                                $var_price_structure = 'Hourly';
                            } else {
                                $var_price_structure = 'Fixed';
                            }
                            echo '<br/><br/>var_price_structure: ' . $var_price_structure;
    
                            //********************* Step 4 : Calculate pickup excess charges and lift/stairs charges ********************
    
                            // ************* (a) pickup excess charges
                            if ($var_price_structure == 'Fixed') {
                                if ($var_pickup_km_nearest_region > $job_price->excess_km_range_max) {
                                    $var_status = 'Fail';
                                    $var_fail_reason = 'Autoquote Failed reason: Pickup Suburb is greater than Maximum Excess Km of the region';
                                    echo "<br/><br/>FAIL:::::" . $var_fail_reason;
                                } elseif ($var_pickup_km_nearest_region > $job_price->excess_km_range) {
                                    $var_pickup_excess_km = $var_pickup_km_nearest_region - $job_price->excess_km_range;
                                    $var_pickup_excess_charges = $var_pickup_excess_km * $job_price->price_per_excess_km * 2;
                                } else {
                                    $var_pickup_excess_charges = 0;
                                }
                            } else {
                                $var_pickup_excess_charges = 0;
                            }
    
                            // ************* (b) pickup lift/stairs charges
    
                            if (empty($job->pickup_floor) || is_null($job->pickup_floor)) {
                                $var_pickup_stairs_lift_charges = 0;
                            } elseif ($job->pickup_floor > 0 && $job->pickup_has_lift == 'N') {
                                $var_pickup_stairs_lift_charges = $job_price->stairs_access_charge_per_floor_per_cbm * $job->pickup_floor * $var_cbm;
                            } elseif ($job->pickup_floor > 0 && $job->pickup_has_lift == 'Y') {
                                $var_pickup_stairs_lift_charges = $job_price->lift_access_charge_per_cbm * $var_cbm;
                            } else {
                                $var_pickup_stairs_lift_charges = 0;
                            }
    
                            //********************* Step 5 : Calculate delivery excess charges and lift/stairs charges ********************
    
                            // ************* (a) delivery excess charges
                            if ($var_price_structure == 'Fixed') {
                                if ($var_drop_off_km_nearest_region > $job_price->excess_km_range_max) {
                                    $var_status = 'Fail';
                                    $var_fail_reason = 'Autoquote Failed reason: Drop off Suburb is greater than Maximum Excess Km of the region';
                                    echo "<br/><br/>FAIL:::::" . $var_fail_reason;
                                } elseif ($var_drop_off_km_nearest_region > $job_price->excess_km_range) {
                                    $var_drop_off_excess_km = $var_drop_off_km_nearest_region - $job_price->excess_km_range;
                                    $var_drop_off_excess_charges = $var_drop_off_excess_km * $job_price->price_per_excess_km * 2;
                                } else {
                                    $var_drop_off_excess_charges = 0;
                                }
                            } else {
                                $var_drop_off_excess_charges = 0;
                            }
                            // ************* (b) delivery lift/stairs charges
    
                            if (empty($job->drop_off_floor) || is_null($job->drop_off_floor)) {
                                $var_drop_off_stairs_lift_charges = 0;
                            } elseif ($job->drop_off_floor > 0 && $job->drop_off_has_lift == 'N') {
                                $var_drop_off_stairs_lift_charges = $job_price->stairs_access_charge_per_floor_per_cbm * $job->drop_off_floor * $var_cbm;
                            } elseif ($job->drop_off_floor > 0 && $job->drop_off_has_lift == 'Y') {
                                $var_drop_off_stairs_lift_charges = $job_price->lift_access_charge_per_cbm * $var_cbm;
                            } else {
                                $var_drop_off_stairs_lift_charges = 0;
                            }
                        }
                        echo '<br/><br/>_______________________________________';
                        echo '<br/><br/>var_pickup_excess_charges: ' . $var_pickup_excess_charges;
                        echo '<br/>var_pickup_stairs_lift_charges: ' . $var_pickup_stairs_lift_charges;
                        echo '<br/><br/>var_drop_off_excess_charges: ' . $var_drop_off_excess_charges;
                        echo '<br/>var_drop_off_stairs_lift_charges: ' . $var_drop_off_stairs_lift_charges;
                        echo '<br/><br/>_______________________________________';
    
                        //****************************** Step 6 : Calculate the Removal Fee ************************************
                        if ($var_price_structure == 'Hourly') {
                            $depot_locations = DB::table('jobs_moving_depot_locations as t1')
                                ->select('t1.*')
                                ->where(['t1.tenant_id' => $tenant->tenant_id, 't1.region_id' => $var_pickup_region_id])
                                ->get();
                            if (count($depot_locations)) {
                                if($job_price->hourly_pricing_include_depot_pickup=='Y'){
                                //**************** */ Find Duration between Pickup Suburb to Closest Depot
    
                                    $pickupRegionTime = $this->calculateTimebyNearestRegion($job->pickup_suburb, $depot_locations, $tenant->tenant_id);
                                    if ($pickupRegionTime['is_true'] == 1) {
                                        $region_pickup_time_detail = explode('|', $pickupRegionTime['region']);
                                        $var_depot_to_pickup_id = $region_pickup_time_detail['0'];
                                        $var_depot_to_pickup_region = $region_pickup_time_detail['1'];
                                    }
                                    $var_depot_to_pickup_time = $pickupRegionTime['min_duration'];
                                }else{
                                    $var_depot_to_pickup_time=0;
                                    $var_depot_to_pickup_id = NULL;
                                    $var_depot_to_pickup_region = NULL;
                                }
                                
                                if($job_price->hourly_pricing_include_drop_off_depot=='Y'){
                                //**************** */ Find Duration between Drop off Suburb to Closest Depot
    
                                    $deliveryRegionTime = $this->calculateTimebyNearestRegion($job->delivery_suburb, $depot_locations, $tenant->tenant_id);
                                    if ($deliveryRegionTime['is_true'] == 1) {
                                        $region_delivery_time_detail = explode('|', $deliveryRegionTime['region']);
                                        $var_drop_off_to_depot_id = $region_delivery_time_detail['0'];
                                        $var_drop_off_to_depot_region = $region_delivery_time_detail['1'];
                                    }
                                    $var_drop_off_to_depot_time = $deliveryRegionTime['min_duration'];
                                }else{
                                    $var_drop_off_to_depot_time = 0;
                                    $var_drop_off_to_depot_id = NULL;
                                    $var_drop_off_to_depot_region = NULL;
                                }
                                echo '<br/><br/>TIME';
                                echo '<br/>var_depot_to_pickup_id: ' . $var_depot_to_pickup_id;
                                echo '<br/>var_depot_to_pickup_region: ' . $var_depot_to_pickup_region;
                                echo '<br/>var_depot_to_pickup_time: ' . $var_depot_to_pickup_time;
                                echo '<br/>var_drop_off_to_depot_id: ' . $var_drop_off_to_depot_id;
                                echo '<br/>var_drop_off_to_depot_region: ' . $var_drop_off_to_depot_region;
                                echo '<br/>var_drop_off_to_depot_time: ' . $var_drop_off_to_depot_time;
    
                                //**************** */ Find Duration between Pickup Suburb to Delivery Suburb
    
                                if($job_price->hourly_pricing_include_pickup_drop_off=='Y'){                                    
                                    $var_pickup_to_dropoff_time = $this->getDistance($job->pickup_suburb, $job->delivery_suburb, 'T');
                                }else{
                                    $var_pickup_to_dropoff_time=0;
                                }
                                echo '<br/><br/><br/>var_pickup_to_dropoff_time: ' . $var_pickup_to_dropoff_time;
    
                                $duration_rate = DB::table('jobs_moving_local_moves')
                                    ->select('loading_mins', 'unloading_mins', 'hourly_rate')
                                    ->where('tenant_id', '=', $tenant->tenant_id)
                                    ->where('min_cbm', '<=', $var_cbm)
                                    ->where('max_cbm', '>=', $var_cbm)
                                    ->first();
                                if ($duration_rate) {
                                    $var_loading_unloading_time = $duration_rate->loading_mins + $duration_rate->unloading_mins;
                                    $var_hourly_rate = $duration_rate->hourly_rate;
                                }

                                if($job_price->hourly_pricing_include_loading_time=='N' && $job_price->hourly_pricing_include_unloading_time=='N'){
                                    $var_loading_unloading_time = 0;
                                }
                                
                                if($job_price->hourly_pricing_include_depot_pickup=='N' && $job_price->hourly_pricing_include_drop_off_depot=='N'){
                                    $var_excess_time = 0;
                                }else{

                                    $var_excess_time = $var_depot_to_pickup_time + $var_drop_off_to_depot_time;
        
                                    if ($var_excess_time < $job_price->local_move_excess_minutes_tier1) {
        
                                        $var_excess_time = $job_price->local_move_excess_minutes_tier1;
                                    } elseif ($var_excess_time < $job_price->local_move_excess_minutes_tier2) {
        
                                        $var_excess_time = $job_price->local_move_excess_minutes_tier2;
                                    }
                                }

                                echo '<br/><br/>_______________________________________';
                                echo '<br/><br/>var_excess_time: ' . $var_excess_time;
                                echo '<br/><br/>var_loading_unloading_time: ' . $var_loading_unloading_time;
                                echo '<br/><br/>var_hourly_rate: ' . $var_hourly_rate;
    
                                $var_total_time = $var_excess_time + $var_pickup_to_dropoff_time + $var_loading_unloading_time;
    
                                $var_removal_fee = (($var_total_time / 60) * $var_hourly_rate) + $var_pickup_stairs_lift_charges + $var_drop_off_stairs_lift_charges;
                            } else {
                                $var_status = 'Fail';
                                $var_fail_reason = 'Autoquote Failed reason: This is a local move and there is no depot in this region';
                                echo "<br/><br/>FAIL:::::" . $var_fail_reason;
                            }
                            //Hourly end
                        } else {
                            $region_to_region = DB::table('jobs_moving_region_to_region_pricing as t1')
                                ->select('t1.*')
                                ->where(['t1.tenant_id' => $tenant->tenant_id, 't1.from_region_id' => $var_pickup_region_id, 't1.to_region_id' => $var_drop_off_region_id])
                                ->first();
                            if ($region_to_region) {
                                $var_removal_fee = $region_to_region->price_flat + ($var_cbm * $region_to_region->price_per_cbm)
                                    + $var_pickup_excess_charges
                                    + $var_drop_off_excess_charges
                                    + $var_pickup_stairs_lift_charges
                                    + $var_drop_off_stairs_lift_charges;
    
                                if ($var_removal_fee < $region_to_region->min_price) {
                                    $var_removal_fee = $region_to_region->min_price;
                                }
                            } else {
                                $var_status = 'Fail';
                                $var_fail_reason = 'Autoquote Failed reason: No records found in the Region to Region pricing table';
                                echo "<br/><br/>FAIL:::::" . $var_fail_reason;
                            }
                        }
                        echo '<br/><br/>_______________________________________';
                        echo '<br/><br/>REMOVAL FEE: ' . $var_removal_fee;

                        //****************************** Step 7 : Update jobs_moving table ************************************
                        if ($var_price_structure == 'Hourly') {
                            JobsMoving::where('job_id', $job->job_id)
                                ->update([
                                    'total_cbm' => $var_cbm,
                                    'pickup_region' => $var_pickup_region,
                                    'drop_off_region' => $var_drop_off_region,
                                    'price_structure' => $var_price_structure,
                                    'hourly_rate' => $var_hourly_rate,
                                    'calculated_excess_mins' => $var_excess_time,
                                    'calculated_total_mins' => $var_total_time
    
                                ]);
                        } else {
                            JobsMoving::where('job_id', $job->job_id)
                                ->update([
                                    'total_cbm' => $var_cbm,
                                    'pickup_region' => $var_pickup_region,
                                    'drop_off_region' => $var_drop_off_region,
                                    'pickup_km_nearest_region' => $var_pickup_km_nearest_region,
                                    'drop_off_km_nearest_region' => $var_drop_off_km_nearest_region,
                                    'pickup_excess_charges' => $var_pickup_excess_charges,
                                    'drop_off_excess_charges' => $var_drop_off_excess_charges,
                                    'price_structure' => $var_price_structure,
                                ]);
                        }
                        if ($var_status == 'Fail') {
                            $OppStatus = CRMOpPipelineStatuses::where(['id' => $tenant->failed_op_status_id, 'tenant_id' => $tenant->tenant_id])->first();
                            if ($OppStatus) {
                                CRMOpportunities::where('id', $job->crm_opportunity_id)
                                    ->update([
                                        'op_status' => $OppStatus->pipeline_status,
                                    ]);
                            }
                            //Add Activity Log
                            $data['log_message'] = $var_fail_reason;
                            $data['lead_id'] = $job->customer_id;
                            $data['job_id'] = $job->job_id;
                            $data['tenant_id'] = $tenant->tenant_id;
                            $data['log_type'] = 7; // Failed Auto Qoute Program
                            $data['log_date'] = Carbon::now();
                            $model = CRMActivityLog::create($data);
                            //--
                        } else {
                            $OppStatus = CRMOpPipelineStatuses::where(['id' => $tenant->quoted_op_status_id, 'tenant_id' => $tenant->tenant_id])->first();
                            if ($OppStatus) {
                                CRMOpportunities::where('id', $job->crm_opportunity_id)
                                    ->update([
                                        'op_status' => $OppStatus->pipeline_status,
                                        'value' => $var_removal_fee,
                                    ]);
                            }
                            //Add Activity Log
                            $data['log_message'] = "Auto quote completed.";
                            $data['lead_id'] = $job->customer_id;
                            $data['job_id'] = $job->job_id;
                            $data['tenant_id'] = $tenant->tenant_id;
                            $data['log_type'] = 7; // Success Auto Qoute Program
                            $data['log_date'] = Carbon::now();
                            $model = CRMActivityLog::create($data);
                            unset($data);
                            //--
                        }
    
                        //****************************** Step 8 : Insert/Update quotes table ************************************
    
                        if ($var_status != 'Fail') {
                            $quotes = Quotes::where(['crm_opportunity_id' => $job->crm_opportunity_id, 'tenant_id' => $tenant->tenant_id])->get();
                            $quote_tax = Tax::where(['id' => $tenant->tax_id_for_quote, 'tenant_id' => $tenant->tenant_id])->first();
    
                            if ($var_price_structure == 'Hourly') {
                                $unit_price = $var_hourly_rate;
                                $quantity = $var_total_time / 60;
                            } else {
                                $unit_price = $var_removal_fee;
                                $quantity = 1;
                            }
    
                            if ($quote_tax) {
                                $quote_amount = ($unit_price * $quantity * (1 + $quote_tax->rate_percent / 100));
                            } else {
                                $quote_amount = $var_removal_fee;
                            }
    
                            echo '<br/><br/>unit_price: ' . $unit_price;
                            echo '<br/><br/>tax_rate_percent: ' . $quote_tax->rate_percent;
                            echo '<br/><br/>quantity: ' . $quantity;
                            echo '<br/><br/>quote_amount: ' . $quote_amount;
    
                            if (count($quotes)) {
                                foreach ($quotes as $quote) {
                                    Quotes::where('id', $quote->crm_opportunity_id)
                                        ->update([
                                            'quote_date' => Carbon::now(),
                                            'quote_accepted' => 'N',
                                            'quote_version' => DB::raw('quote_version+1'),
                                            'updated_date' => Carbon::now(),
                                        ]);
                                    //----Delete Qoute Items
                                    QuoteItem::where('quote_id', $quote->id)->delete();
    
                                    $data2['tenant_id'] = $quote->tenant_id;
                                    $data2['quote_id'] = $quote->id;
                                    $data2['product_id'] = $tenant->quote_line_item_product_id;  
                                    $data2['name'] = 'Removal Fee';
                                    $data2['description'] = "From " . $job->pickup_suburb . ", to " . $job->delivery_suburb;
                                    $data2['type'] = "item";
                                    $data2['unit_price'] = $unit_price;
                                    $data2['quantity'] = $quantity;
                                    $data2['tax_id'] = $tenant->tax_id_for_quote;
                                    $data2['amount'] = $quote_amount;
                                    $data2['created_date'] = Carbon::now();
                                    $QuoteItem = QuoteItem::create($data2);
                                    unset($data2);
                                }
                            } else {
                                //If No Qoute found then add new Qoute
                                $data['tenant_id'] = $tenant->tenant_id;
                                $data['crm_opportunity_id'] = $job->crm_opportunity_id;
                                $data['quote_number'] = $job->job_number;
                                $data['sys_job_type'] = "Moving";
                                $data['job_id'] = $job->job_id;
                                $data['quote_date'] = Carbon::now();
                                $data['created_date'] = Carbon::now();
                                $Quote = Quotes::create($data);
                                unset($data);
    
                                if (isset($Quote->id)) {
                                    $data2['tenant_id'] = $tenant->tenant_id;
                                    $data2['quote_id'] = $Quote->id;
                                    $data2['product_id'] = $tenant->quote_line_item_product_id;  
                                    $data2['name'] = 'Removal Fee';
                                    $data2['description'] = "From " . $job->pickup_suburb . ", to " . $job->delivery_suburb;
                                    $data2['type'] = "item";
                                    $data2['unit_price'] = $unit_price;
                                    $data2['quantity'] = $quantity;
                                    $data2['tax_id'] = $tenant->tax_id_for_quote;
                                    $data2['amount'] = $quote_amount;
                                    $data2['created_date'] = Carbon::now();
                                    $QuoteItem = QuoteItem::create($data2);
                                    unset($data2);
                                }
                            }
                        }
    
                        //****************************** Step 8.5 : Generating Quote PDF ************************************
                        $global = Setting::where('tenant_id', auth()->user()->tenant_id)->first();
                        $quote_file_url = $this->generateQuote($job->crm_opportunity_id,$job->tenant_id,$global);
                        $quote_sms_file_url = substr($quote_file_url, strrpos($quote_file_url, '/public' )+1);

                        //****************************** Step 8.6 : Generating Work Order PDF ************************************
    
                        $workorder_file_url = $this->generateWorkorder($job->job_id,$job->tenant_id);
                        $workrder_sms_file_url = substr($workorder_file_url, strrpos($workorder_file_url, '/public' )+1);
                        $this->job_leg_start_time = JobsMovingLegs::where('job_id', '=', $job->job_id)->pluck("est_start_time")->first();
                        if($this->job_leg_start_time){
                            $est_first_leg_start_time=$this->job_leg_start_time;
                        } 
                        //****************************** Step 9 : Send Quote email to lead or Send Fail email to user ************************************
                        if ($tenant->send_auto_quote_email_to_customer == 'Y') {
    
                            $crm_contacts = CRMContacts::where('lead_id', '=', $job->customer_id)->first();
                            $crm_contact_email = CRMContactDetail::where('contact_id', '=', $crm_contacts->id)->where('detail_type', '=', 'Email')->first();
                            $crm_contact_phone = CRMContactDetail::where('contact_id', '=', $crm_contacts->id)->where('detail_type', '=', 'Mobile')->first();
                            $customer_email = ($crm_contact_email)? $crm_contact_email->detail:'';
                            $customer_phone = ($crm_contact_phone)? $crm_contact_phone->detail:'';
                            $organisation_settings = OrganisationSettings::where('tenant_id', '=', $job->tenant_id)->first();
                            $paidAmount = 0;
                            $totalAmount = 0;

                            $name = explode(" ", $crm_contacts->name, 2);
                            if(count($name)>1){
                                $l_firstname = $name[0];
                                $l_lastname = $name[1];
                            }else{
                                $l_firstname = $crm_contacts->name;
                                $l_lastname = '';
                            }

                            //inventory_list parameter
                            $mov_inv = new JobsMovingInventory();
                            $inv_list = $mov_inv->getInventoryListForEmail($job->tenant_id, $job->job_id);
                            //-->

                            $external_inventory_form_param = base64_encode('tenant_id='.$job->tenant_id.'&job_id='.$job->job_id);
                            $external_inventory_form = request()->getSchemeAndHttpHost().'/removals-inventory-form/'.$external_inventory_form_param;   

                        $data = [
                            'job_id' => $job->job_number,
                            'first_name' => $l_firstname,
                            'last_name' => $l_lastname,
                            'pickup_suburb' => $job->pickup_suburb,
                            'delivery_suburb' => $job->delivery_suburb,
                            'pickup_address' => $job->pickup_address." ".$job->pickup_suburb." ".$job->pickup_post_code,
                            'delivery_address' => $job->drop_off_address." ".$job->delivery_suburb." ".$job->drop_off_post_code,
                            'mobile' => $customer_phone,
                            'email' => $customer_email,
                            'job_date' => date('d-m-Y', strtotime($job->job_date)),
                            'total_amount' => $totalAmount,
                            'total_paid' => $paidAmount,
                            'total_due' => ($totalAmount - $paidAmount),
                            'external_inventory_form' => $external_inventory_form,
                            'inventory_list' => $inv_list,
                            'book_now_button' => '',
                            'user_first_name' => '',
                            'user_last_name' => '',
                            'est_start_time' => $job->job_start_time,
                            'est_first_leg_start_time' => $est_first_leg_start_time,
                            'user_email_signature' => ''
                        ];
                        $files = [];
    
                            if ($var_status != 'Fail') {
                                //Sending Success email
                                $email_template = EmailTemplates::where('id', '=', $tenant->quote_email_template_id)->first();
                                if ($email_template) {                                                                     
                                    $emailData = $this->setEmailParameter($email_template->email_subject, $email_template->email_body, $data);
                                    $email_data['from_email'] = $email_template->from_email;
                                    $email_data['from_name'] = $email_template->from_email_name;
                                    $email_data['email_subject'] = $emailData['subject'];
                                    $email_data['email_body'] = $emailData['body'];
                                    $email_data['reply_to'] = $email_template->from_email;
                                    //$organisation_settings->company_email;
                                    //$email_data['job_id'] = $job->job_id;
                                    $email_data['job_id'] = $job->customer_id; /*Added by Mobeen for TAG value, as email opened in PostMarkApp works using the LEAD_ID*/ 
                                    $email_data['cc'] = '';
                                    $email_data['bcc'] = '';
                                    $email_data['to'] = $customer_email;                                    
    
                                        if($email_template->attach_quote=='Y'){
                                                $files[]=$quote_file_url;
                                        }
    
                                    }
                                    
                                    $email_data['files'] = $files;
                                    Mail::to($email_data['to'])->send(new CustomerMail($email_data));
                                    echo '<br/><br/>Success Email Sent';

                                    //Add Activity Log for email                                   
                                    $activitydata['lead_id'] = $job->customer_id;
                                    $activitydata['job_id'] = $job->job_id;
                                    $activitydata['tenant_id'] = $tenant->tenant_id;
                                    $activitydata['log_type'] = 3; 
                                    $activitydata['log_from'] = $email_data['from_email'];
                                    $activitydata['log_to'] = $email_data['to'];
                                    $activitydata['log_subject'] = $email_data['email_subject'];
                                    $activitydata['log_message'] = $email_data['email_body'];
                                    $activitydata['log_date'] = Carbon::now();
                                    $model = CRMActivityLog::create($activitydata);
                                    if($model){
                                        //start:: Email Attachment log
                                        if(count($files)){
                                            $attach['attachment_type'] = $crm_contacts->name;
                                            $attach['attachment_content'] = $quote_file_url;
                                            $attach['log_id'] = $model->id;
                                            $attach['tenant_id'] = $tenant->tenant_id;
                                            $attach['created_at'] = Carbon::now();
                                            $attach['updated_at'] = Carbon::now();
                                            $model2 = CRMActivityLogAttachment::create($attach);
                                        }
                                        //end :: Email Attachment log
                                        echo '<br/><br/>Email Log Inserted';
                                    }

                                    //START:: Send SMS
                                    //END:: Send SMS
                                                            
                            } else {
                                //Sending fail email
                                $email_template = EmailTemplates::where('id', '=', $tenant->fail_email_template_id)->first();
                                if ($email_template) {                                                                      
                                    $emailData = $this->setEmailParameter($email_template->email_subject, $email_template->email_body, $data);
                                    $email_data['from_email'] = $email_template->from_email;
                                    $email_data['from_name'] = $email_template->from_email_name;
                                    $email_data['email_subject'] = $emailData['subject'];
                                    $email_data['email_body'] = $emailData['body'];
                                    $email_data['reply_to'] = $organisation_settings->company_email;
                                    $email_data['cc'] = [];
                                    $email_data['bcc'] = [];
                                    $email_data['to'] = $tenant->send_quote_fail_email_to;
                                    Mail::to($email_data['to'])->send(new sendMail($email_data));
                                    echo '<br/><br/>Failed Email Sent';
                                }else{
                                    echo '<br/><br/>No Failed Email Template found';
                                }
                            }
                            
                        }
                        //End auto quoting email
                        //****************************** Step 10 : Send Quote SMS to lead ************************************
                        if ($tenant->send_auto_quote_sms_to_customer == 'Y') {
                            $time_format = 'H:i A';
                            $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', auth()->user()->tenant_id)->first();
                            if($this->organisation_settings){
                                $time_format=$this->organisation_settings->time_format;
                            }
                            $quote_file_url_sms = url('/'.$quote_sms_file_url);
                            $crm_contacts = CRMContacts::where('lead_id', '=', $job->customer_id)->first();
                            $crm_contact_email = CRMContactDetail::where('contact_id', '=', $crm_contacts->id)->where('detail_type', '=', 'Email')->first();
                            $crm_contact_phone = CRMContactDetail::where('contact_id', '=', $crm_contacts->id)->where('detail_type', '=', 'Mobile')->first();
                            $name = explode(" ", $crm_contacts->name, 2);
                            if(count($name)>1){
                                $l_firstname = $name[0];
                                $l_lastname = $name[1];
                            }else{
                                $l_firstname = $crm_contacts->name;
                                $l_lastname = '';
                            }
                            $customer_email = ($crm_contact_email)? $crm_contact_email->detail:'';
                            $customer_phone = ($crm_contact_phone)? $crm_contact_phone->detail:'';
                            $organisation_settings = OrganisationSettings::where('tenant_id', '=', $job->tenant_id)->first();
                            $paidAmount = 0;
                            $totalAmount = 0;
                            $external_inventory_form_param = base64_encode('tenant_id='.$job->tenant_id.'&job_id='.$job->job_id);
                            $external_inventory_form = request()->getSchemeAndHttpHost().'/removals-inventory-form/'.$external_inventory_form_param;

                        $sms_data = [
                            'job_id' => $job->job_number,
                            'first_name' => $l_firstname,
                            'last_name' => $l_lastname,
                            'pickup_suburb' => $this->job->pickup_suburb,
                            'delivery_suburb' => $this->job->delivery_suburb,
                            'mobile' => $customer_phone,
                            'email' => $customer_email,
                            'pickup_address' => $this->job->pickup_address." ".$this->job->pickup_suburb." ".$this->job->pickup_post_code,
                            'delivery_address' => $this->job->drop_off_address." ".$this->job->delivery_suburb." ".$this->job->drop_off_post_code,
                            'job_date' => date('d-m-Y', strtotime($job->job_date)),
                            'user_first_name' => '',
                            'user_last_name' => '',
                            'est_start_time' => date($time_format, strtotime($this->job->job_start_time)),
                            'est_first_leg_start_time' => date($time_format, strtotime($est_first_leg_start_time)),  
                            'total_amount' => $totalAmount,
                            'total_paid' => $paidAmount,
                            'total_due' => ($totalAmount - $paidAmount),
                            'external_inventory_form' => $external_inventory_form
                        ];
                            $this->sendSMS($job->customer_id,$tenant->tenant_id,$customer_phone,$tenant->quote_sms_template_id,$quote_file_url_sms,$sms_data);
                        }
                        //END:: Send Quote SMS to lead
                        echo '</fieldset>';
                    } // end inner for loop
                }
                //break;
            } // end outer for loop
        }
    }
private function calculateDistancebyNearestRegion($tenant_id, $suburb)
{
    $region_suburb_name = DB::table('jobs_moving_pricing_regions as t1')
        ->select('t1.*')
        ->where(['t1.tenant_id' => $tenant_id])
        ->get();
    $distanceArr = array();
    if (count($region_suburb_name)) {
        foreach ($region_suburb_name as $region) {
            if (isset($region->id) && !empty($region->id)) {
                $distanceArr[$region->id . '|' . $region->region_suburb_name] = $this->getDistance($suburb, $region->region_suburb_name, 'K');
            }
        }
    }

    if (count($distanceArr)) {
        $returnArr = array(
            'region' => min(array_keys($distanceArr, min($distanceArr))),
            'min_distance' => min($distanceArr),
            'is_true' => 1
        );
    } else {
        $returnArr = array(
            'region' => 0,
            'min_distance' => 0,
            'is_true' => 0
        );
    }
    return $returnArr;
}

private function calculateTimebyNearestRegion($from_location, $depot_locations)
{
    $TimeArr = array();
    if (count($depot_locations)) {
        foreach ($depot_locations as $depot) {
            if (isset($depot->id) && !empty($depot->id)) {
                $TimeArr[$depot->id . '|' . $depot->depot_suburb] = $this->getDistance($from_location, $depot->depot_suburb, 'T');
            }
        }
    }
    if (count($TimeArr)) {
        $returnArr = array(
            'region' => min(array_keys($TimeArr, min($TimeArr))),
            'min_duration' => min($TimeArr),
            'is_true' => 1
        );
    } else {
        $returnArr = array(
            'region' => 0,
            'min_duration' => 0,
            'is_true' => 0
        );
    }
    return $returnArr;
}

//************************ */ Get Distance Between to Address via Google API*********************//

private function getDistance($addressFrom, $addressTo, $unit)
{
    //Change address format
    $formattedAddrFrom = str_replace(' ', '+', $addressFrom);
    $formattedAddrTo = str_replace(' ', '+', $addressTo);
    $url = 'https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $formattedAddrFrom . '&destinations=' . $formattedAddrTo . '&key=AIzaSyB2SMtaVBlqC5v72gqS716BX8R5oXklaFc';
    $getDistance = $this->curl_get_file_contents($url);

    $getDistanceDecode = json_decode($getDistance);

    if ($unit == 'K') {
        $kmValue = $getDistanceDecode->rows[0]->elements[0]->distance->value;
        if ($kmValue) {
            $roundValue = round(($kmValue / 1000), 1);
        } else {
            $roundValue = 0;
        }
    } else {
        $timeValue = $getDistanceDecode->rows[0]->elements[0]->duration->value;
        if ($timeValue) {
            $roundValue = round(($timeValue / 60), 1); //convert seconds to minutes
        } else {
            $roundValue = 0;
        }
    }

    return $roundValue;
}

private function curl_get_file_contents($URL)
{
    $c = curl_init();
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $URL);
    $contents = curl_exec($c);
    curl_close($c);

    if ($contents) return $contents;
    else return FALSE;
}
//END:: Removal Auto Qoute Program

// START:: Set Email Template parameters
private function setEmailParameter($email_subject, $email_body, $data)
{

    $subject = $email_subject;
    if (preg_match_all("/{(.*?)}/", $subject, $m)) {
        foreach ($m[1] as $i => $varname) {
            $subject = str_replace($m[0][$i], sprintf('%s', $data[$varname]), $subject);
        }
    }

    $template = $email_body;

    if (preg_match_all("/{(.*?)}/", $template, $m)) {
        foreach ($m[1] as $i => $varname) {
            $template = str_replace($m[0][$i], sprintf('%s', $data[$varname]), $template);
        }
    }
    $response = [
        'subject' => $subject,
        'body' => $template
    ];
    return $response;
}
//END:: EMail Template Parameters

public function generateQuote($opportunity_id,$tenant_id,$global){
    
    //try{
    $this->opportunity = CRMOpportunities::where('id', '=', $opportunity_id)->where('tenant_id', '=', $tenant_id)->first();
        $this->taxs = Tax::where(['tenant_id' => $tenant_id])->first();
        $this->sub_total = 0;
        $this->quote_total = 0;
        $this->total_tax = 0;
        $this->deposit_required = 0;
        $this->booking_fee = 0;
        $this->count = 0;
        $this->show_estimate_range=0;
        $this->estimate_lower_percent=0;
        $this->stripe_connected=0;
        $this->sub_total_after_discount=0;

            $stripe = TenantApiDetail::where('tenant_id', $tenant_id)
                    ->where('provider', 'Stripe')->first();
            if($stripe){
                if(isset($stripe->account_key) && !empty($stripe->account_key)){
                    $this->stripe_connected=1;
                }
            }
            // Job Moving Price for the tenant-------------------//
        $job_price_additional = DB::table('jobs_moving_pricing_additional as t1')
        ->select('t1.*')
        ->where(['t1.tenant_id' => $tenant_id])
        ->first();
        
        $this->job = JobsMoving::where('crm_opportunity_id', '=', $this->opportunity->id)->where('tenant_id', '=', $tenant_id)->first();            
        $this->quote = Quotes::where('crm_opportunity_id', '=', $this->opportunity->id)->where('tenant_id', '=', $tenant_id)->first();
        if ($this->quote) {
            $this->quoteItems = QuoteItem::where('quote_id', '=', $this->quote->id)->get();

            $sub_total = QuoteItem::select(DB::raw('sum(quote_items.unit_price * quote_items.quantity) as total'))
                ->where('quote_items.quote_id', '=', $this->quote->id)->first();
            $this->sub_total = $sub_total->total;

            // if ($this->quoteItems) {
            //     foreach ($this->quoteItems as $qitm) {
            //         $subtotal = floatval($qitm->amount);
            //         $this->quote_total += $subtotal;
            //         if (isset($this->taxs->rate_percent) && floatval($qitm->amount) > 0)
            //             $this->tax_total += floatval($this->taxs->rate_percent) * ((floatval($subtotal)) / 100);
            //     }
            // }

            if($this->taxs){
                $rate_percent=$this->taxs->rate_percent;
            }
            if($this->quote->discount_type=="percent"){
                $this->sub_total_after_discount = $this->sub_total - ($this->quote->discount/100 * $this->sub_total);
            }else{
                $this->sub_total_after_discount = $this->sub_total - $this->quote->discount;
            }
            $this->total_tax = ($rate_percent * $this->sub_total_after_discount)/100;
            $this->quote_total = $this->total_tax + $this->sub_total_after_discount; 

        }
    if($this->opportunity->op_type=="Moving"){            
        if ($this->job->price_structure == 'Fixed') {
            if ($job_price_additional->is_deposit_for_fixed_pricing_fixed_amt == 'Y') {
                $this->deposit_required = $job_price_additional->deposit_amount_fixed_pricing;
            } else {
                $this->deposit_required = $job_price_additional->deposit_percent_fixed_pricing * $this->quote_total;
            }
        } else {
            if($job_price_additional->hourly_pricing_has_booking_fee=='Y'){
                $this->booking_fee = $job_price_additional->hourly_pricing_booking_fee;
            }else{
                if ($job_price_additional->is_deposit_for_hourly_pricing_fixed_amt == 'Y') {
                    $this->deposit_required = $job_price_additional->deposit_amount_hourly_pricing;
                } else {
                    $this->deposit_required = $job_price_additional->deposit_percent_hourly_pricing * $this->quote_total;
                }
            }
            //Show Estimate Range 
            if($job_price_additional->hourly_pricing_min_pricing_percent>0){
                $this->show_estimate_range=1;
                $this->estimate_lower_percent=$job_price_additional->hourly_pricing_min_pricing_percent;
            }
            //--
        }
    }elseif($this->opportunity->op_type=="Cleaning"){
        if($job_price_additional->hourly_pricing_has_booking_fee=='Y'){
            $this->booking_fee = $job_price_additional->hourly_pricing_booking_fee;
        }else{
            if ($job_price_additional->is_deposit_for_hourly_pricing_fixed_amt == 'Y') {
                $this->deposit_required = $job_price_additional->deposit_amount_hourly_pricing;
            } else {
                $this->deposit_required = $job_price_additional->deposit_percent_hourly_pricing * $this->quote_total;
            }
        }
     }

        // following line removed in FORT-34 
        // $this->deposit_required = (floatval($this->grand_total) / 100) * 25;
        if ($this->job) {
            $this->companies = Companies::where('id', '=', $this->job->company_id)->where('tenant_id', '=', $tenant_id)->first();
            // dd($this->companies);
        }
        $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', $tenant_id)->first();
        $this->crm_leads = CRMLeads::where('id', '=', $this->opportunity->lead_id)->first();
        $this->crm_contacts = CRMContacts::where('lead_id', '=', $this->opportunity->lead_id)->first();
        $this->crm_contact_email = CRMContactDetail::where('contact_id', '=', $this->crm_contacts->id)->where('detail_type', '=', 'Email')->first();
        $this->crm_contact_phone = CRMContactDetail::where('contact_id', '=', $this->crm_contacts->id)->where('detail_type', '=', 'Mobile')->first();     
        $this->company_logo_exists = false;
        
        //Book now url
        if($this->job->price_structure=='Hourly' && $job_price_additional->hourly_pricing_has_booking_fee=='Y'){
            $this->url_params = base64_encode('quote_id=' . $this->quote->id . '&booking_fee=' . $this->booking_fee);
            $this->url_link = request()->getSchemeAndHttpHost() . '/pay-now-booking-fee/' . $this->url_params;
            $is_booking_fee=1;
        }else{
            $this->url_params = base64_encode('quote_id=' . $this->quote->id . '&deposit_required=' . $this->deposit_required);
            $this->url_link = request()->getSchemeAndHttpHost() . '/pay-now/' . $this->url_params;
            $is_booking_fee=0;
        }
        $filename = time();
        if (isset($this->companies)) {

            $file_number = 1;
            if (!empty($this->quote->quote_file_name)) {
                $filename = str_replace('.pdf', '', $this->quote->quote_file_name);
                $fn_ary = explode('_', $filename);
                $file_number = intval($fn_ary[2]) + 1;
            }

            if (File::exists(public_path() . '/user-uploads/company-logo/' . $this->companies->logo)) {
                $this->company_logo_exists = true;
            }

            $company_name = $this->cleanString($this->companies->company_name);

            $filename = 'Estimate_' . $company_name . '_'  . $this->quote->quote_number.'_'.$file_number. '.pdf';
            if (File::exists(public_path() . '/quote-files/' . $filename)) {
                File::delete(public_path() . '/quote-files/' . $filename);
            }
            $this->customer_detail = CustomerDetails::where('customer_id', '=', $this->job->customer_id)->first();
            $this->invoice_settings = InvoiceSetting::where('tenant_id', auth()->user()->tenant_id)->first();

            $pdf = app('dompdf.wrapper');
            $pdf->loadView('admin.crm-leads.quote', 
            [
                'global'=>$global,
                'organisation_settings'=>$this->organisation_settings,
                'companies'=>$this->companies,
                'invoice_settings'=>$this->invoice_settings,
                'settings'=>$this->settings,
                'company_logo_exists'=>$this->company_logo_exists,
                'count'=>0,
                'crm_contact_phone'=>$this->crm_contact_phone,
                'crm_contact_email'=>$this->crm_contact_email,
                'crm_contacts'=>$this->crm_contacts,
                'crm_leads'=>$this->crm_leads,
                'job'=>$this->job,
                'quote'=>$this->quote,
                'quoteItems'=>$this->quoteItems,
                'taxs'=>$this->taxs,
                'deposit_required'=>$this->deposit_required,
                'sub_total'=>$this->sub_total,
                'total_tax'=>$this->total_tax,
                'quote_total'=>$this->quote_total,
                'sub_total_after_discount'=>$this->sub_total_after_discount,
                'url_link'=>$this->url_link,
                'is_booking_fee'=>$is_booking_fee,
                'booking_fee'=>$this->booking_fee,
                'show_estimate_range' => $this->show_estimate_range,
                'estimate_lower_percent' => $this->estimate_lower_percent,
                'stripe_connected' => $this->stripe_connected,
                'customer_detail' => $this->customer_detail,
                'balance_payment' => $this->balance_payment
                
            ]);
            // return $pdf->stream(); // to view pdf
            // return $pdf->download('tmp.pdf');
            $pdf->save(public_path().'/quote-files/' . $filename);
            
            if (File::exists(public_path() . '/quote-files/' . $this->quote->quote_file_name)) {
                File::delete(public_path() . '/quote-files/' . $this->quote->quote_file_name);
            }

            $this->quote->quote_file_name = $filename;
            $this->quote->save();
            return public_path('quote-files') . '/' . $this->quote->quote_file_name;
    }    
}

public function generateWorkorder($job_id, $tenant_id){

    ini_set('max_execution_time', 300);
    ini_set('memory_limit', '3000M');
        $this->vehicle_name = '';
        $this->start_time = '';
        $this->dispatch_notes = '';
        $this->job_driver_id = '';
        $this->job_offsider_ids = '';
        $this->job = JobsMoving::find($job_id);

        $this->companies = Companies::where('id', '=', $this->job->company_id)->first();
        $this->crm_leads = CRMLeads::where('id', '=', $this->job->customer_id)->first();
        $this->customer_detail = CustomerDetails::where('customer_id', '=', $this->job->customer_id)->first();
        $this->crm_contacts = CRMContacts::where('lead_id', '=', $this->job->customer_id)->first();
        $this->crm_contact_email = CRMContactDetail::where('contact_id', '=', $this->crm_contacts->id)->where('detail_type', '=', 'Email')->first();
        $this->crm_contact_phone = CRMContactDetail::where('contact_id', '=', $this->crm_contacts->id)->where('detail_type', '=', 'Mobile')->first();
        $this->crm_contact_email = ($this->crm_contact_email) ? $this->crm_contact_email->detail : '';
        $this->crm_contact_phone = ($this->crm_contact_phone) ? $this->crm_contact_phone->detail : '';
        $this->company_logo_exists = false;

        $this->invoice = Invoice::where(['job_id' => $job_id, 'sys_job_type' => 'Moving'])
        ->where('tenant_id', '=', auth()->user()->tenant_id)
        ->first();
        $this->invoice_items = InvoiceItems::where('invoice_id', '=', $this->invoice->id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $this->drivers = User::driverList();
        $this->people = User::allPeople();

        $this->job_leg = JobsMovingLegs::where('job_id', '=', $this->job->job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        if($this->job_leg){
            $this->vehicle_name = Vehicles::where('id', $this->job_leg->vehicle_id)->pluck('vehicle_name')->first();
            $this->start_time = $this->job_leg->est_start_time;
            $this->dispatch_notes = $this->job_leg->notes;
            $this->job_driver_id = $this->job_leg->driver_id;
            $this->job_offsider_ids = $this->job_leg->offsider_ids;
        }

        if (!empty($this->job->work_order_file_name)) {
            $filename = str_replace('.pdf', '', $this->job->work_order_file_name);
            $fn_ary = explode('_', $filename);
        }

        $filename = 'Work_Order_Job_' . $this->job->job_number . '_' . rand() . '.pdf';

        if ($this->companies) {
            if (File::exists(public_path() . '/user-uploads/company-logo/' . $this->companies->logo)) {
                $this->company_logo_exists = true;
            }
        }

        $pdf = app('dompdf.wrapper');
        $html = view('admin.list-jobs.workorder-pdf', [
            'companies'=>$this->companies,
            'job'=>$this->job,
            'company_logo_exists'=>$this->company_logo_exists,
            'crm_leads'=>$this->crm_leads,
            'customer_detail'=>$this->customer_detail,
            'vehicle_name'=>$this->vehicle_name,
            'start_time'=>$this->start_time,
            'dispatch_notes'=>$this->dispatch_notes,
            'job_offsider_ids'=>$this->job_offsider_ids,
            'job_driver_id'=>$this->job_driver_id,
            'invoice_items'=>$this->invoice_items,
            'drivers'=>$this->drivers,
            'people'=>$this->peoples
        ]);
        //$pdf->loadView('admin.list-jobs.invoice', $this->data);
        $pdf->loadHtml($html, 'UTF-8');
        $pdf->getDomPDF()->set_option("enable_php", true);
        // return $pdf->stream(); // to view pdf
        // return $pdf->download('tmp.pdf');
        $pdf->save('invoice-files/' . $filename);

        if (File::exists(public_path() . '/invoice-files/' . $this->job->work_order_file_name)) {
            File::delete(public_path() . '/invoice-files/' . $this->job->work_order_file_name);
        }
        $this->job->work_order_file_name = $filename;
        $this->job->save();

        return public_path('invoice-files') . '/' . $this->job->work_order_file_name;;
}

private function cleanString($string) {
    $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
 
    return preg_replace('/[^A-Za-z\_]/', '', $string); // Removes special chars.
 }

private function sendSMS($lead_id,$tenant_id,$sms_to,$template_id,$pdf_link,$sms_data){
    $tenant_details = \App\TenantDetail::where('tenant_id', $tenant_id)->first();
    $template = SMSTemplates::where(['id' => $template_id])->first();
    $companies = Companies::where(['tenant_id'=>$tenant_id,'active'=>'Y'])->first();
    $sms_from = $companies->sms_number;        

    if ($tenant_details->sms_credit <= 0) {
        $response['error'] = 1;
        $response['message'] = '(‘Not enough credit to buy SMS. Please buy SMS credits.';
        //Add Activity Log
        $data['log_message'] = 'Not enough credit to buy SMS. Please buy SMS credits.';
        $data['lead_id'] = $lead_id;
        $data['log_from'] = $sms_from;
        $data['log_to'] = $sms_to;
        $data['tenant_id'] = $tenant_id;
        $data['log_type'] = 7; // Activity SMS Fail
        $data['log_date'] = Carbon::now();
        $model = CRMActivityLog::create($data);
    } else {
        $sys_api_details = \App\SysApiSettings::where('type', '=', 'sms_gateway')->first();
        $sys_api_details->user;
        $sys_api_details->password;
        $smsbody = $this->setSMSParameter($template->sms_message,$sms_data);

        if($template->attach_quote=='Y'){
            $sms_message = $smsbody."\n".$pdf_link;
        }else{
            $sms_message = $smsbody;
        }

        $sms_total_credits = ceil(strlen($sms_message)/160);

        $username = $sys_api_details->user;
        $password = $sys_api_details->password;

        $content = 'username=' . rawurlencode($username) .
            '&password=' . rawurlencode($password) .
            '&to=' . rawurlencode($sms_to) .
            '&from=' . rawurlencode($sms_from) .
            '&message=' . rawurlencode($sms_message) .
            '&maxsplit=5'.
            '&ref=' . rawurlencode($lead_id);
        //Send SMS
        $smsbroadcast_response = $this->sendSMSFunc($content);
        $response_lines = explode("\n", $smsbroadcast_response);
        //--
        foreach ($response_lines as $data_line) {
            $message_data = "";
            $message_data = explode(':', $data_line);
            if ($message_data[0] == "OK") {
                //Update company credit
                $tenant_total_credits = $tenant_details->sms_credit;
                $subtractCredits = $tenant_details->sms_credit - $tenant_total_credits;
                $subtractCredits = $tenant_total_credits - $sms_total_credits;
                $tenant_details->id = $tenant_id;
                $UpdateTenantCredits = \App\TenantDetail::where('tenant_id', '=', $tenant_id)->update(array('sms_credit' => $subtractCredits));
                //---

                //Add Activity Log
                $data['log_message'] = $sms_message;
                $data['lead_id'] = $lead_id;
                $data['log_from'] = $sms_from;
                $data['log_to'] = $sms_to;
                $data['tenant_id'] = $tenant_id;
                $data['log_type'] = 8; // Activity SMS
                $data['log_date'] = Carbon::now();
                $model = CRMActivityLog::create($data);                    
            }
        }
    }
}
protected function sendSMSFunc($content)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://api.smsbroadcast.com.au/api-adv.php?' . $content);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    if ($output === false) {
        //echo "Error Number:".curl_errno($ch)."<br>";
        //echo "Error String:".curl_error($ch);
    }
    //dd($output[]);
    curl_close($ch);
    return $output;
}
private function setSMSParameter($body, $data)
{
    $template = $body;
    if (preg_match_all("/{(.*?)}/", $template, $m)) {
        foreach ($m[1] as $i => $varname) {
            $template = str_replace($m[0][$i], sprintf('%s', $data[$varname]), $template);
        }
    }
    return $template;
}

}
