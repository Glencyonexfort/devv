@if(!in_array('finance_settings',$user_permissions))
<div class="sidebar sidebar-light sidebar-component sidebar-component-left sidebar-expand-md">
    <div class="sidebar-content">
        <div class="card">
            <div class="card-body p-0">
                <ul class="nav nav-sidebar" data-nav-type="accordion">
                    @if(!in_array('products',$user_permissions))
                    <li class="nav-item"><a href="{{ route('admin.products.index') }}" class="nav-link"><i class="icon-basket"></i> @lang('app.menu.products')</a></li>
                    @endif
                    @if(!in_array('product_categories',$user_permissions))
                    <li class="nav-item"><a href="{{ route('admin.product-categories') }}" class="nav-link"><i class="icon-list"></i> @lang('app.menu.product_categories')</a></li>
                    @endif
                    @if(!in_array('taxes',$user_permissions))
                    <li class="nav-item"><a href="{{ route('admin.manage-taxes') }}" class="nav-link"><i class="icon-percent"></i> @lang('app.menu.taxes')</a></li>
                    @endif
                    @if(!in_array('invoice_settings',$user_permissions))
                    <li class="nav-item"><a href="{{ route('admin.invoice-settings.index') }}" class="nav-link"><i class="icon-cog52"></i> @lang('app.menu.invoiceSettings')</a></li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endif