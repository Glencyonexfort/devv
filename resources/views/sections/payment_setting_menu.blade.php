<div class="sidebar sidebar-light sidebar-component sidebar-component-left sidebar-expand-md">
    <div class="sidebar-content">
        <div class="card mb-2">
            <div class="card-body p-0">
                <ul class="nav nav-sidebar">
                    <li class="nav-item">
                        <a href="{{ route('admin.settings.index') }}" class="nav-link text-danger"><i class="ti-arrow-left"></i> @lang('app.menu.settings')</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a href="{{ route('admin.payment-gateway-credential.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.payment-gateway-credential.index') active @endif"><i class="icon-key"></i> @lang('app.menu.onlinePayment')</a>
                    </li> -->
                    <li class="nav-item">
                        <a href="{{ route('admin.offline-payment-setting.index') }}" class="nav-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.offline-payment-setting.index') active @endif"><i class="icon-key"></i> @lang('app.menu.offlinePaymentMethod')</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
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