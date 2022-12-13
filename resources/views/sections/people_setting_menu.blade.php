@if(!in_array('people_settings',$user_permissions))
<div class="sidebar sidebar-light sidebar-component sidebar-component-left sidebar-expand-md">
    <div class="sidebar-content">
        <!-- Sub navigation -->
        <div class="card mb-2">
            <div class="card-body p-0">
                <ul class="nav nav-sidebar">
                    <li class="nav-item">
                        @if(!in_array('roles_permissions',$user_permissions))
                        <a href="{{ route('admin.manage-roles') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.manage-permission' || \Illuminate\Support\Facades\Route::currentRouteName() == 'admin.role-permissions') active @endif"><i class="icon-list"></i> @lang('app.menu.rolesAndPermission')</a>
                        @endif
                    </li>                  
                </ul>
            </div>
        </div>
    </div>
</div>
@endif