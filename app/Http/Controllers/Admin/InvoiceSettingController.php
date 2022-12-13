<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Requests\UpdateInvoiceSetting;
use App\InvoiceSetting;
use App\CRMOpPipelineStatuses;
use App\Product;

class InvoiceSettingController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.invoiceSettings');
        $this->pageIcon = 'icon-file-text';
    }

    public function index()
    {
        $this->invoiceSetting = InvoiceSetting::where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $this->opp_statuses = CRMOpPipelineStatuses::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('sort_order', 'asc')->get();
        $this->products = Product::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        return view('admin.invoice-settings.edit', $this->data);
    }

    public function update(UpdateInvoiceSetting $request)
    {
        $setting = InvoiceSetting::where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $setting->invoice_prefix = $request->invoice_prefix;
        //$setting->template       = $request->template;
        $setting->due_after      = $request->due_after;
        $setting->invoice_terms  = $request->invoice_terms;
        // $setting->gst_number     = $request->gst_number;
        // $setting->show_gst       = $request->has('show_gst') ? 'yes' : 'no';
        $setting->cc_processing_fee_percent  = $request->cc_processing_fee_percent;
        $setting->cc_processing_product_id  = $request->cc_processing_product_id;
        $setting->stripe_pre_authorised_op_status  = $request->stripe_pre_authorised_op_status;
        $setting->stripe_pre_authorise = ($request->input('stripe_pre_authorise') == 'Y' ? 'Y' : 'N');
        $setting->save();

        return Reply::success(__('messages.settingsUpdated'));
    }
}
