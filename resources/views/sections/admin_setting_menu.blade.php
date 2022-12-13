<div class="sidebar sidebar-light sidebar-component sidebar-component-left sidebar-expand-md">
    <div class="sidebar-content">
        <!-- Sub navigation -->
        <div class="card mb-2">
            <div class="card-body p-0">
                <ul class="nav nav-sidebar">
                    <!-- <li class="nav-item-header">Actions</li> -->
                    <li class="nav-item">
                        @if(!in_array('organisation_settings',$user_permissions))
                        <a href="{{ route('admin.settings.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.settings.index') active @endif"><i class="icon-cog3"></i> @lang('app.menu.organisationSettings')</a>
                        @endif

                    </li>
                    <li class="nav-item">
                        @if(!in_array('companies',$user_permissions))
                        <a href="{{ route('admin.companies.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.companies.index') active @endif"><i class="icon-office"></i> @lang('app.menu.companies')</a>
                        @endif
                    </li>                    
                    <li class="nav-item">
                        @if(!in_array('servicing_cities',$user_permissions))
                        <a href="{{ route('admin.servicing-cities') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.servicing-cities') active @endif"><i class="icon-arrow-right15"></i> Servicing Cities</a>
                        @endif
                    </li>
                    <li class="nav-item">
                        @if(!in_array('list_type_options',$user_permissions))
                        <a href="{{ route('admin.list-type-options.index') }}" class="nav-link"><i class="icon-list3"></i> @lang('app.menu.listTypesAndOptions')</a>
                        @endif
                    </li>
                    <li class="nav-item">
                        @if(!in_array('profile_settings',$user_permissions))
                        <a href="{{ route('admin.profile-settings.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.profile-settings.index') active @endif"><i class="icon-user"></i> @lang('app.menu.profileSettings')</a>
                        @endif
                    </li>
                    <li class="nav-item">
                        @if(!in_array('payment_credentials',$user_permissions))
                        <a href="{{ route('admin.offline-payment-setting.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.offline-payment-setting.index') active @endif"><i class="icon-coin-dollar"></i> @lang('app.menu.paymentGatewayCredential')</a>
                        @endif
                    </li>
                    <!-- <li class="nav-item">
                        <a href="{{ route('admin.invoice-settings.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.invoice-settings.index') active @endif"><i class="icon-file-text"></i> @lang('app.menu.invoiceSettings')</a>
                    </li> -->
                    @if(in_array('attendance',$modules))
                    <li class="nav-item">
                        <a href="{{ route('admin.attendance-settings.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.attendance-settings.index') active @endif"><i class="icon-list3"></i> @lang('app.menu.attendanceSettings')</a>
                    </li>
                    @endif
                    @if(in_array('leaves',$modules))
                    <li class="nav-item">
                        <a href="{{ route('admin.leaves-settings.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.leaves-settings.index') active @endif"><i class="icon-list3"></i> @lang('app.menu.leaveSettings')</a>
                    </li>
                    @endif
                    <!-- <li class="nav-item">
                        <a href="{{ route('admin.custom-fields.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.custom-fields.index') active @endif"><i class="icon-list3"></i> @lang('app.menu.customFields')</a>
                    </li> -->
                    <li class="nav-item">
                        @if(!in_array('buy_sms_credits',$user_permissions))
                        <a href="{{ route('admin.sms-credits.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.sms-credits.index') active @endif"><i class="icon-envelop4"></i> @lang('app.menu.smsCredits')</a>
                        @endif
                    </li>
                    <li class="nav-item">
                        @if(!in_array('connect_stripe',$user_permissions))
                        <a href="{{ route('admin.connect-stripe.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.connect-stripe.index') active @endif"><i class="icon-loop"></i> @lang('app.menu.connectStripe')</a>
                        @endif
                    </li>
                    <li class="nav-item">
                        @if(!in_array('connect_xero',$user_permissions))
                        <a href="{{ route('admin.connect-xero.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.connect-xero.index') active @endif"><i class="icon-loop"></i> @lang('app.menu.connectXero')</a>
                        @endif
                    </li>
                    <li class="nav-item">
                        @if(!in_array('connect_myob',$user_permissions))
                        <a href="{{ route('admin.connect-myob.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.connect-myob.index') active @endif"><i class="icon-loop"></i> @lang('app.menu.connectMyob')</a>
                        @endif
                    </li>
                    <li class="nav-item">
                        @if(!in_array('configure_email',$user_permissions))
                        <a href="{{ route('admin.configure-email.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.configure-email.index') active @endif"><i class="icon-envelope"></i> @lang('app.menu.configureEmail')</a>
                        @endif
                    </li>
                    <li class="nav-item">
                        @if(!in_array('coverfreight',$user_permissions))
                        <a href="{{ route('admin.coverfreight.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.coverfreight.index') active @endif"><i class="icon-shield-check"></i> @lang('app.menu.coverFreight')</a>
                        @endif
                    </li>
                    @if(in_array('messages',$modules))
                    <li class="nav-item">
                        <a href="{{ route('admin.message-settings.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.message-settings.index') active @endif"><i class="icon-cog2"></i> @lang('app.menu.messageSettings')</a>
                    </li>
                    @endif
                    @if(in_array('leads',$modules))
                    <li class="nav-item">
                        <a href="{{ route('admin.lead-source-settings.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.lead-source-settings.index') active @endif"><i class="icon-cog2"></i> @lang('app.lead') @lang('app.menu.settings')</a>
                    </li>
                    @endif
                    @if(in_array('timelogs',$modules))
                    <li class="nav-item">
                        <a href="{{ route('admin.log-time-settings.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.lead-source-settings.index') active @endif"><i class="icon-cog2"></i> @lang('app.timeLog') @lang('app.menu.settings')</a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- <ul class="nav tabs-vertical"> -->
    <!--{{--<li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.email-settings.index') active @endif hide">--}}-->
    <!--{{--<a href="{{ route('admin.email-settings.index') }}">@lang('app.menu.notificationSettings')</a></li>--}}-->
    <!--     <li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.currency.index') active @endif">
        <a href="{{ route('admin.currency.index') }}">@lang('app.menu.currencySettings')</a></li> -->
    <!--{{--<li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.theme-settings.index') active @endif hide">--}}-->
    <!--{{--<a href="{{ route('admin.theme-settings.index') }}">@lang('app.menu.themeSettings')</a></li>--}}-->
    <!--{{--<li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.ticket-agents.index') active @endif hide">--}}-->
    <!--{{--<a href="{{ route('admin.ticket-agents.index') }}">@lang('app.menu.ticketSettings')</a></li>--}}-->
    <!--{{--<li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.update-settings.index') active @endif hide">--}}-->
    <!--{{--<a href="{{ route('admin.update-settings.index') }}">@lang('app.menu.updates')</a></li>--}}-->
    <!--{{--<li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.module-settings.index') active @endif hide">--}}-->
    <!--{{--<a href="{{ route('admin.module-settings.index') }}">@lang('app.menu.moduleSettings')</a></li>--}}-->
    <!-- <li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.role-permission.index') active @endif">
        <a href="{{ route('admin.role-permission.index') }}">@lang('app.menu.rolesPermission')</a></li> -->

    <!--{{--<li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.storage-settings.index') active @endif hide">--}}-->
    <!--{{--<a href="{{ route('admin.storage-settings.index') }}">@lang('app.menu.storageSettings')</a></li>--}}-->
    <!--{{--<li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.language-settings.index') active @endif hide">--}}-->
    <!--{{--<a href="{{ route('admin.language-settings.index') }}">@lang('app.language') @lang('app.menu.settings')</a></li>--}}-->


    <!--{{--<li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.task-settings.index') active @endif hide">--}}-->
    <!--{{--<a href="{{ route('admin.task-settings.index') }}">@lang('app.task') @lang('app.menu.settings')</a></li>--}}-->
<!-- </ul> -->

<script src="{{ asset('plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
<script>
    var screenWidth = $(window).width();
    if (screenWidth <= 768) {
        $('.tabs-vertical').each(function() {
            var list = $(this),
                select = $(document.createElement('select')).insertBefore($(this).hide()).addClass('settings_dropdown form-control');
            $('>li a', this).each(function() {
                var target = $(this).attr('target'),
                    option = $(document.createElement('option'))
                    .appendTo(select)
                    .val(this.href)
                    .html($(this).html())
                    .click(function() {
                        if (target === '_blank') {
                            window.open($(this).val());
                        } else {
                            window.location.href = $(this).val();
                        }
                    });
                if (window.location.href == option.val()) {
                    option.attr('selected', 'selected');
                }
            });
            list.remove();
        });
        $('.settings_dropdown').change(function() {
            window.location.href = $(this).val();
        })
    }
</script>