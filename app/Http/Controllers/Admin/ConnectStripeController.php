<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\TenantApiDetail;
use Illuminate\Support\Facades\Session;
use Stripe\Stripe;

class ConnectStripeController extends AdminBaseController
{
    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('app.menu.connectStripe');
        $this->pageIcon = 'icon-loop';        
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->stripe = TenantApiDetail::where('tenant_id', auth()->user()->tenant_id)
                    ->where('provider', 'Stripe')->first();
        return view('admin.connect-stripe.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $session = \request()->session()->getId();
        $url = config('services.stripe.connect').$session;
        return redirect($url);
    }
    public function responseConnectStripe(Request $request)
    {
        $user = Auth::user();
        $tenant_api_details = TenantApiDetail::where('tenant_id', $user->tenant_id)
                ->where('provider', 'Stripe')->first();

        // Stripe API secret key
        $secret_key = env('STRIPE_SECRET');
        Stripe::setApiKey($secret_key);

        try { 
            $resp = \Stripe\OAuth::token(array(
                'grant_type' => 'authorization_code',
                'code' => $request->code
            ));
        } catch (\Stripe\Error\OAuth\OAuthBase $e) {
            Session::flash('message', $e->getMessage()); 
            Session::flash('alert-class', 'alert-danger'); 
            return redirect(route('admin.connect-stripe.index'));
        }
        
        if(!$tenant_api_details){
            $tenant_api_details = new TenantApiDetail();
            $tenant_api_details->tenant_id = $user->tenant_id;
            $tenant_api_details->provider = 'Stripe';
            $tenant_api_details->account_key = $request->code;
            $tenant_api_details->variable1 = $resp->stripe_user_id;
            $tenant_api_details->save();
        }
        else{
            if(!empty($tenant_api_details->account_key)){
                Session::flash('message', __('messages.alreadyConnectedStripe')); 
                Session::flash('alert-class', 'alert-danger'); 
                return redirect(route('admin.connect-stripe.index'));
            }
            $tenant_api_details->account_key = $request->code;
            $tenant_api_details->variable1 = $resp->stripe_user_id;
            $tenant_api_details->save();
        }
        Session::flash('message', __('messages.ConnectedStripe')); 
        Session::flash('alert-class', 'alert-success'); 
        return redirect(route('admin.connect-stripe.index'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
