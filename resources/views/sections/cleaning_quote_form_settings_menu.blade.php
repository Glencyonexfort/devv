<div class="sidebar sidebar-light sidebar-component sidebar-component-left sidebar-expand-md">
    <div class="sidebar-content">
        <!-- Sub navigation -->
        <div class="card mb-2">
            <div class="card-body p-0">
                <ul class="nav nav-sidebar">
                    <li class="font-weight-bold" style="margin-left: 30px;margin-bottom:5px;color:#000">Auto Quote</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.generalQuoteFormSettings.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.generalQuoteFormSettings') active @endif"><i class="icon-arrow-right15"></i> @lang('app.menu.generalCleaningQuoteFormSettings')</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.leaseQuoteFormSettings.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.leaseQuoteFormSettings') active @endif"><i class="icon-arrow-right15"></i> @lang('app.menu.leaseCleaningQuoteFormSettings')</a>
                    </li>  
                    <li class="nav-item">
                        <a href="{{ route('admin.jobs-cleaning-pricing.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.jobsCleaningPricing') active @endif"><i class="icon-arrow-right15"></i> @lang('app.menu.endOfLeasePricing')</a>
                    </li>                    
                    <li class="nav-item">
                        <a href="{{ route('admin.enableCleaningAutoQuote.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.enableCleaningAutoQuote') active @endif"><i class="icon-arrow-right15"></i> Enable Auto Quote</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.cleaningShifts') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.cleaningShifts') active @endif"><i class="icon-arrow-right15"></i> @lang('app.menu.cleaningShifts')</a>
                    </li>  
                    <li class="nav-item">
                        <a href="{{ route('admin.cleaningTeams') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.cleaningTeams') active @endif"><i class="icon-arrow-right15"></i> @lang('app.menu.cleaningTeams')</a>
                    </li>        
                    <li class="nav-item">
                        <a href="{{ route('admin.cleaningTeamMembers') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.cleaningTeamMembers') active @endif"><i class="icon-arrow-right15"></i> @lang('app.menu.cleaningTeamMembers')</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>