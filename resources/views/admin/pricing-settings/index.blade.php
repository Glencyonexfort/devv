@extends('layouts.app')

@section('page-title')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="{{ $pageIcon }} mr-2"></i> <span class="font-weight-semibold">{{ $pageTitle }}</span></h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
    <!-- <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> @lang('app.menu.home')</a>
                <span class="breadcrumb-item active">{{ $pageTitle }}</span>
            </div>
        </div>
    </div> -->
</div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">
    
@endpush

@section('content')

<div class="content">
    <div class="d-md-flex align-items-md-start">
            @include('sections.removal_setting_menu')
        <div style="flex:auto">
            <div class="card">
                <div class="card-header bg-white header-elements-inline">
                    <h6 class="card-title">@lang('modules.PricingSettings.boxTitle')</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            {!! Form::open(['id'=>'updatePricingSettings','class'=>'ajax-form','method'=>'POST']) !!}
                            <div class="form-body">
                                <div class="row">
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Goods Value per CBM</label>
                                            <div class="form-group form-group-feedback form-group-feedback-left">
                                            
                                                <input type="number" min="0.00" step="0.01" class="form-control" id="goods_value_per_cbm" name="goods_value_per_cbm" value="{{ $pricingAdditional ? $pricingAdditional->goods_value_per_cbm : '0.00' }}">
                                                <div class="form-control-feedback">
                                                    <span>{{ $global->currency_symbol }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Minimum Goods Value</label>
                                            <div class="form-group form-group-feedback form-group-feedback-left">
                                            
                                                <input type="number" min="0.00" step="0.01" class="form-control" id="minimum_goods_value" name="minimum_goods_value" value="{{ $pricingAdditional ? $pricingAdditional->minimum_goods_value : '0.00' }}">
                                                <div class="form-control-feedback">
                                                    <span>{{ $global->currency_symbol }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6" style="margin-bottom:15px;">
                                        <div class="form-group">
                                            <label>@lang('modules.PricingSettings.inventoryPdfShowCbm')</label>
                                            
                                            <div class="form-check form-check-switchery">
                                                <label class="form-check-label">
                                                    <input value="Y" name="inventory_pdf_show_cbm" id="inventory_pdf_show_cbm" type="checkbox" @if(isset($pricingAdditional) && $pricingAdditional->inventory_pdf_show_cbm  == 'Y') checked @endif class="js-switch class="js-switch form-check-input-switchery-danger" data-fouc data-color="#f96262">
                                                </label>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.PricingSettings.cbmPerBedroom')</label>
                                            <input type="number" min="0.00" step="0.01" class="form-control" id="cbm_per_bedroom" name="cbm_per_bedroom" value="{{ $pricingAdditional ? $pricingAdditional->cbm_per_bedroom : '0.00' }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.PricingSettings.cbmPerLivingRoom')</label>
                                            <input type="number" min="0.00" step="0.01" class="form-control" id="cbm_per_living_room" name="cbm_per_living_room" value="{{ $pricingAdditional ? $pricingAdditional->cbm_per_living_room : '0.00' }}">
                                        </div>
                                    </div>

                                        
                                    <div class="col-md-6">
                                        <label>@lang('modules.PricingSettings.stairsAccessCharge')</label>
                                        <div class="form-group form-group-feedback form-group-feedback-left">
                                            
                                            <input type="number" min="0.00" step="0.01" class="form-control" id="stairs_access_charge_per_floor_per_cbm" name="stairs_access_charge_per_floor_per_cbm" value="{{ $pricingAdditional ? $pricingAdditional->stairs_access_charge_per_floor_per_cbm : '0.00' }}">
                                            <div class="form-control-feedback">
                                                <span>{{ $global->currency_symbol }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">

                                        <label for="lift_access_charge_per_cbm">@lang('modules.PricingSettings.liftAccessCharge')</label>
                                        <div class="form-group form-group-feedback form-group-feedback-left">
                                            
                                            <input type="number" min="0.00" step="0.01" class="form-control" id="lift_access_charge_per_cbm" name="lift_access_charge_per_cbm" value="{{ $pricingAdditional ? $pricingAdditional->lift_access_charge_per_cbm : '0.00' }}">
                                            <div class="form-control-feedback">
                                                <span>{{ $global->currency_symbol }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <legend class="font-size-lg font-weight-bold"><mark>Fixed Rate Calculation:</mark></legend>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.PricingSettings.excessRangeStartKM')</label>
                                            <input type="number"  min="0.00" step="0.01" class="form-control" id="excess_km_range" name="excess_km_range" value="{{ $pricingAdditional ? $pricingAdditional->excess_km_range : '0.00' }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.PricingSettings.excessRangeMaxKM')</label>
                                            <input type="number" min="0.00" step="0.01" class="form-control form-control-sm" id="excess_km_range_max" name="excess_km_range_max" value="{{ $pricingAdditional ? $pricingAdditional->excess_km_range_max : '0.00' }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">

                                        <label>@lang('modules.PricingSettings.pricePerExcessKM')</label>
                                        <div class="form-group form-group-feedback form-group-feedback-left">
                                            
                                            <input type="number" min="0.00" step="0.01" class="form-control" id="price_per_excess_km" name="price_per_excess_km" value="{{ $pricingAdditional ? $pricingAdditional->price_per_excess_km : '0.00' }}">
                                            <div class="form-control-feedback">
                                                <span>{{ $global->currency_symbol }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    

                                    
                                    <!--/span-->
                                </div>

                                <legend class="font-size-lg font-weight-bold"><mark>Quote Deposit:</mark></legend>

                                <div class="row">

                                    
                                    <div class="col-md-6" style="margin-bottom:15px;">

                                        <div class="form-group">
                                            <label>@lang('modules.PricingSettings.isDepositFixedAmount')</label>
                                            
                                            <div class="form-check form-check-switchery">
                                                <label class="form-check-label">
                                                    <input value="Y" name="is_deposit_for_fixed_pricing_fixed_amt" id="is_deposit_for_fixed_pricing_fixed_amt" type="checkbox" @if(isset($pricingAdditional) && $pricingAdditional->is_deposit_for_fixed_pricing_fixed_amt  == 'Y') checked @endif class="js-switch class="js-switch form-check-input-switchery-danger" data-fouc data-color="#f96262">
                                                </label>
                                            </div>

                                        </div>
                                        
                                    </div>

                                    <div class="col-md-6">
                                    </div>

                                    <div class="col-md-6">

                                        <label>@lang('modules.PricingSettings.depositAmount')</label>
                                        <div class="form-group form-group-feedback form-group-feedback-left">
                                            
                                            <input type="number" min="0.00" step="0.01" class="form-control" id="deposit_amount_fixed_pricing" name="deposit_amount_fixed_pricing" value="{{ $pricingAdditional ? $pricingAdditional->deposit_amount_fixed_pricing : '0.00' }}" @if(isset($pricingAdditional) && $pricingAdditional->is_deposit_for_fixed_pricing_fixed_amt  == 'Y') enabled @else disabled @endif >
                                            <div class="form-control-feedback">
                                                <span>{{ $global->currency_symbol }}</span>
                                            </div>
                                        </div>
                                        
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.PricingSettings.depositPercent')</label>
                                            <input type="number" min="0.00" step="0.01" class="form-control form-control-sm" id="deposit_percent_fixed_pricing" name="deposit_percent_fixed_pricing" value="{{ $pricingAdditional ? ($pricingAdditional->deposit_percent_fixed_pricing)*100 : '0.00' }}" @if(isset($pricingAdditional) && $pricingAdditional->is_deposit_for_fixed_pricing_fixed_amt  == 'Y') disabled @else enabled @endif>
                                        </div>
                                    </div>
                                   
                                    

                                    
                                </div>
                                <!--/row-->

                               
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <hr>
                                        <button type="submit" id="save-form-2" class="btn btn-success m-r-10"><i class="fa fa-check"></i> @lang('app.save')</button>
                                        <!-- <button type="reset" class="btn btn-default">@lang('app.reset')</button> -->
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php 
if(isset($pricingAdditional)){
    $url = '/admin/moving-settings/savePricingSettingsData/'.$pricingAdditional->id;
} else {
    $url = '/admin/moving-settings/createPricingSettingsData';
}
//dd($url); ?>
</div>
@endsection

@push('footer-script')
    
<script src="{{ asset('plugins/bower_components/switchery/dist/switchery.min.js') }}"></script>

<script>
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    $('.js-switch').each(function() {
        new Switchery($(this)[0], $(this).data());
    });

    $("#is_deposit_for_fixed_pricing_fixed_amt").change(function() {
        if(this.checked) {
            $("#deposit_amount_fixed_pricing").prop("disabled", false);
            $("#deposit_percent_fixed_pricing").prop("disabled", true);
        } else {
            $("#deposit_amount_fixed_pricing").prop("disabled", true);
            $("#deposit_percent_fixed_pricing").prop("disabled", false);
        }
    });


    $('#save-form-2').click(function() {
        $.easyAjax({
            url: "{{ $url }}",
            container: '#updatePricingSettings',
            type: "POST",
            redirect: true,
            data: $('#updatePricingSettings').serialize(),
            success: function(data) {
                if (data.status == 'success') {
                    window.location.reload();
                }
            }
        })
    });
</script>
@endpush