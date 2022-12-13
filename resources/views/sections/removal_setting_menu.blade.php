<div class="sidebar sidebar-light sidebar-component sidebar-component-left sidebar-expand-md">
    <div class="sidebar-content">
        <!-- Sub navigation -->
        <div class="card mb-2">
            <div class="card-body p-0">
                <ul class="nav nav-sidebar">
                    <li class="font-weight-bold" style="margin-left: 30px;margin-bottom:5px;color:#000">General</li>                    
                    {{-- @if(!in_array('job_templates',$user_permissions))
                    <li class="nav-item">
                        <a href="{{ route('admin.job-templates.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.job-templates.index') active @endif"><i class="icon-newspaper2"></i> @lang('app.menu.job_templates')</a>
                    </li>
                    @endif --}}
                    {{-- @php
                        dd(\Illuminate\Support\Facades\Route::currentRouteName());
                    @endphp --}}
                    @if(!in_array('vehicles',$user_permissions))
                    <li class="nav-item">
                        <a href="{{ route('admin.vehicles.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == "admin.vehicles.index") active @endif"><i class="icon-truck"></i> @lang('app.menu.vehicles')</a>
                    </li>
                    @endif
                    @if(!in_array('vehicle_groups',$user_permissions))
                    <li class="nav-item">
                        <a href="{{ route('admin.vehicleGroups.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == "admin.vehicleGroups.index") active @endif"><i class="icon-arrow-right15"></i> @lang('app.menu.vehicleGroups')</a>
                    </li>
                    @endif
                    @if(!in_array('vehicles_daily_checklist',$user_permissions))
                    <li class="nav-item">
                        <a href="{{ route('admin.vehiclesDailyChecklist.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == "admin.vehiclesDailyChecklist.index") active @endif"><i class="icon-arrow-right15"></i> @lang('app.menu.vehiclesDailyChecklist')</a>
                    </li>
                    @endif
                    @if(!in_array('ohs_checklist',$user_permissions))
                    <li class="nav-item">
                        <a href="{{ route('admin.ohsChecklist.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.ohsChecklist.index') active @endif"><i class="icon-arrow-right15"></i> @lang('app.menu.ohsChecklist')</a>
                    </li>
                    @endif
                    @if(!in_array('inventory_groups',$user_permissions))
                    <li class="nav-item">
                        <a href="{{ route('admin.inventoryGroups.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.inventoryGroups') active @endif"><i class="icon-arrow-right15"></i> @lang('app.menu.inventoryGroups')</a>
                    </li>
                    @endif
                    @if(!in_array('inventory_definitions',$user_permissions))
                    <li class="nav-item">
                        <a href="{{ route('admin.inventoryDefinitions.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.inventoryDefinitions') active @endif"><i class="icon-arrow-right15"></i> @lang('app.menu.inventoryDefinitions')</a>
                    </li>
                    @endif
                    @if(!in_array('property_category_options',$user_permissions))
                    <li class="nav-item">
                        <a href="{{ route('admin.property-category-options.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.property-category-options') active @endif"><i class="icon-arrow-right15"></i> @lang('app.menu.propertyCategoryOptions')</a>
                    </li>
                    @endif
                    <li class="font-weight-bold" style="margin-left: 30px;margin-bottom:5px;color:#000">Auto Quote</li>
                    <!-- <li class="nav-item">
                    <div class="checkbox checkbox-danger" style="padding-left: 1.4rem;">
                        <input id="default1" name="default1" value="Y" checked="&quot;&quot;" type="checkbox">
                        <label for="default1" style="padding-left: 17px;">Enable Auto Quote</label>
                    </div>
                    </li> -->
                    @if(!in_array('enable_auto_quote',$user_permissions))
                    <li class="nav-item">
                        <a href="{{ route('admin.enableAutoQuote.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.enableAutoQuote') active @endif"><i class="icon-arrow-right15"></i> Enable Auto Quote</a>
                    </li>
                    @endif
                    @if(!in_array('price_settings',$user_permissions))
                    <li class="nav-item">
                        <a href="{{ route('admin.pricingSettings.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.pricingSettings') active @endif"><i class="icon-arrow-right15"></i> Pricing Settings</a>
                    </li>
                    @endif
                    @if(!in_array('local_moves_hourly_settings',$user_permissions))
                    <li class="nav-item">
                        <a href="{{ route('admin.hourlySettings.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.hourlySettings') active @endif"><i class="icon-arrow-right15"></i> Local Moves Hourly Settings</a>
                    </li>
                    @endif
                    @if(!in_array('pricing_regions',$user_permissions))
                    <li class="nav-item">
                        <a href="{{ route('admin.pricing-region') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.pricing-region') active @endif"><i class="icon-arrow-right15"></i> Pricing Regions</a>
                    </li>
                    @endif
                    @if(!in_array('region_region_pricing',$user_permissions))
                    <li class="nav-item">
                        <a href="{{ route('admin.region-to-region-pricing') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.region-to-region-pricing') active @endif"><i class="icon-coin-dollar"></i> Region to Region Pricing</a>
                    </li>
                    @endif
                    @if(!in_array('removals_quote_form',$user_permissions))
                    <li class="nav-item">
                        <a href="{{ route('admin.removal-quote-form') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.removal-quote-form') active @endif"><i class="icon-arrow-right15"></i> Removals Quote Form</a>
                    </li>    
                    @endif                
                </ul>
            </div>
        </div>
    </div>
</div>