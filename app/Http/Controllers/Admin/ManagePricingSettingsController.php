<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\JobsMovingPricingAdditional;
use App\Helper\Reply;

class ManagePricingSettingsController extends AdminBaseController
{

    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('app.menu.pricingSettings');
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

        return view('admin.pricing-settings.index', $this->data);
    }

    public function savePricingSettingsData(Request $request, $id) 
    {
        //dd();
        $pricingAdditional = JobsMovingPricingAdditional::findOrFail($id);
        //$pricingAdditional->tenant_id = auth()->user()->tenant_id;
        $pricingAdditional->cbm_per_bedroom = $request->cbm_per_bedroom;
        $pricingAdditional->minimum_goods_value = $request->minimum_goods_value;
        $pricingAdditional->goods_value_per_cbm = $request->goods_value_per_cbm;
        $pricingAdditional->cbm_per_living_room = $request->cbm_per_living_room;

        $pricingAdditional->price_per_excess_km = $request->price_per_excess_km;
        $pricingAdditional->excess_km_range = $request->excess_km_range;
        $pricingAdditional->excess_km_range_max = $request->excess_km_range_max;
        $pricingAdditional->is_deposit_for_fixed_pricing_fixed_amt = $request->has('is_deposit_for_fixed_pricing_fixed_amt') && $request->input('is_deposit_for_fixed_pricing_fixed_amt') == 'Y' ? 'Y' : 'N';
        $pricingAdditional->inventory_pdf_show_cbm = $request->has('inventory_pdf_show_cbm') && $request->input('inventory_pdf_show_cbm') == 'Y' ? 'Y' : 'N';

        $pricingAdditional->stairs_access_charge_per_floor_per_cbm = $request->stairs_access_charge_per_floor_per_cbm;
        $pricingAdditional->lift_access_charge_per_cbm = $request->lift_access_charge_per_cbm;

        if($request->has('is_deposit_for_fixed_pricing_fixed_amt') && $request->input('is_deposit_for_fixed_pricing_fixed_amt') == 'Y')
        {
            $pricingAdditional->deposit_amount_fixed_pricing = $request->deposit_amount_fixed_pricing;
        } else {
            $pricingAdditional->deposit_percent_fixed_pricing = ($request->deposit_percent_fixed_pricing)/100;
        }
        
        $pricingAdditional->updated_by = $this->user->id;
        $pricingAdditional->save();

        return Reply::success(__('messages.pricingSettingsUpdated'));
    }


    public function createPricingSettingsData(Request $request) 
    {
        $pricingAdditional = new JobsMovingPricingAdditional();
        $pricingAdditional->tenant_id = auth()->user()->tenant_id;
        $pricingAdditional->minimum_goods_value = $request->minimum_goods_value;
        $pricingAdditional->goods_value_per_cbm = $request->goods_value_per_cbm;
        $pricingAdditional->cbm_per_bedroom = $request->cbm_per_bedroom;
        $pricingAdditional->cbm_per_living_room = $request->cbm_per_living_room;

        $pricingAdditional->price_per_excess_km = $request->price_per_excess_km;
        $pricingAdditional->excess_km_range = $request->excess_km_range;
        $pricingAdditional->excess_km_range_max = $request->excess_km_range_max;
        $pricingAdditional->is_deposit_for_fixed_pricing_fixed_amt = $request->has('is_deposit_for_fixed_pricing_fixed_amt') && $request->input('is_deposit_for_fixed_pricing_fixed_amt') == 'Y' ? 'Y' : 'N';
        $pricingAdditional->inventory_pdf_show_cbm = $request->has('inventory_pdf_show_cbm') && $request->input('inventory_pdf_show_cbm') == 'Y' ? 'Y' : 'N';

        $pricingAdditional->stairs_access_charge_per_floor_per_cbm = $request->stairs_access_charge_per_floor_per_cbm;
        $pricingAdditional->lift_access_charge_per_cbm = $request->lift_access_charge_per_cbm;

        if($request->has('is_deposit_for_fixed_pricing_fixed_amt') && $request->input('is_deposit_for_fixed_pricing_fixed_amt') == 'Y')
        {
            $pricingAdditional->deposit_amount_fixed_pricing = $request->deposit_amount_fixed_pricing;
        } else {
            $pricingAdditional->deposit_percent_fixed_pricing = ($request->deposit_percent_fixed_pricing)/100;
        }
        
        $pricingAdditional->created_by = $this->user->id;
        $pricingAdditional->save();

        return Reply::success(__('messages.pricingSettingsSaved'));
    }
    
}
