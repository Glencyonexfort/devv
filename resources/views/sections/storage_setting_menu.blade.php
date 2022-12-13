@if(!in_array('storage_settings',$user_permissions))
<div class="sidebar sidebar-light sidebar-component sidebar-component-left sidebar-expand-md">
    <!-- Sidebar content -->
    <div class="sidebar-content">
        <div class="card">
            <div class="card-body p-0">
                <ul class="nav nav-sidebar" data-nav-type="accordion">
                    <li class="font-weight-bold" style="margin-left: 30px;margin-bottom:5px;color:#000">General</li>
                    <li class="nav-item">
                        @if(!in_array('storage_types',$user_permissions))
                            <li class="nav-item"><a href="{{ route('admin.storage-types') }}" class="nav-link"><i class="icon-newspaper2"></i> @lang('app.menu.storageTypes')</a></li>
                        @endif
                        @if(!in_array('storage_units',$user_permissions))
                            <li class="nav-item"><a href="{{ route('admin.storage-units') }}" class="nav-link"><i class="icon-arrow-right15"></i> @lang('app.menu.storageUnits')</a></li>
                        @endif
                        @if(!in_array('units_unavailability',$user_permissions))
                            <li class="nav-item"><a href="{{ route('admin.storage-units-unavailability') }}" class="nav-link"><i class="icon-arrow-right15"></i> @lang('app.menu.unitsUnavailability')</a></li>                    
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endif