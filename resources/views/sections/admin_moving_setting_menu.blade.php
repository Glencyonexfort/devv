@if(!in_array('crm_settings',$user_permissions))
<div class="sidebar sidebar-light sidebar-component sidebar-component-left sidebar-expand-md">
    <!-- Sidebar content -->
    <div class="sidebar-content">
        <div class="card">
            <div class="card-body p-0">
                <ul class="nav nav-sidebar" data-nav-type="accordion">
                    <li class="font-weight-bold" style="margin-left: 30px;margin-bottom:5px;color:#000">@lang('app.menu.communication')</li>
                    <li class="nav-item">
                        @if(!in_array('email_templates',$user_permissions))
                            <li class="nav-item"><a href="{{ route('admin.email-templates.index') }}" class="nav-link"><i class="icon-envelop2"></i> @lang('app.menu.email_templates')</a></li>
                        @endif
                        @if(!in_array('sms_templates',$user_permissions))
                            <li class="nav-item"><a href="{{ route('admin.sms-templates.index') }}" class="nav-link"><i class="icon-bubbles5"></i> @lang('app.menu.sms_templates')</a></li>
                        @endif
                        @if(!in_array('email_sms_sequences',$user_permissions))
                            <li class="nav-item"><a href="{{ route('admin.email-sequences.index') }}" class="nav-link"><i class="icon-envelop3"></i> @lang('app.menu.emailSequences')</a></li>
                            <li class="nav-item-divider"></li>
                        @endif
                        @if(!in_array('statuses',$user_permissions))
                            <li class="font-weight-bold" style="margin-left: 30px;margin-bottom:5px;color:#000">@lang('app.menu.customisation')</li>
                            <li class="nav-item"><a href="{{ route('admin.statuses.index') }}" class="nav-link"><i class="icon-files-empty"></i> @lang('app.menu.statuses')</a></li>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endif