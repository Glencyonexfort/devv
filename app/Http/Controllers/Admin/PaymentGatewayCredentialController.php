<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\PaymentGatewayCredentials;
use App\Http\Requests\PaymentGateway\UpdateGatewayCredentials;

class PaymentGatewayCredentialController extends AdminBaseController
{
    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('app.menu.paymentGatewayCredential');
        $this->pageIcon = 'icon-key';
    }

    public function index(){
        $this->credentials = PaymentGatewayCredentials::where('tenant_id', '=', auth()->user()->tenant_id)->first();
        return view('admin.payment-gateway-credentials.edit', $this->data);
    }

    public function update(UpdateGatewayCredentials $request, $id) {
        $credential = PaymentGatewayCredentials::findOrFail($id);
        $credential->tenant_id = auth()->user()->tenant_id;
        $credential->paypal_client_id = $request->paypal_client_id;
        $credential->paypal_secret = $request->paypal_secret;
        ($request->paypal_status) ? $credential->paypal_status = 'active' : $credential->paypal_status = 'deactive';

        $credential->stripe_client_id = $request->stripe_client_id;
        $credential->stripe_secret = $request->stripe_secret;
        $credential->stripe_webhook_secret = $request->stripe_webhook_secret;
        ($request->stripe_status) ? $credential->stripe_status = 'active' : $credential->stripe_status = 'deactive';
        $credential->save();

        return Reply::success(__('messages.settingsUpdated'));
    }
}
