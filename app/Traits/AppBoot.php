<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

trait AppBoot
{

    private $appSetting;
    private $reply;


    private function setConfig()
    {
        $setting = config('froiden_envato.setting');
        $this->appSetting = (new $setting)::first();

        $reply = config('froiden_envato.reply');
        $this->reply = (new $reply);
    }

    /**
     * @return bool
     * Check if Purchase code is stored in settings table and is verified
     */
    public function isLegal()
    {
        $this->setConfig();
        $domain = \request()->getHost();

        if ($domain == 'localhost' || $domain == '127.0.0.1' || $domain == '::1') {
            return true;
        }

        if (is_null($this->appSetting->purchase_code)) {
            return false;
        }
        $version = File::get(public_path('version.txt'));
        $data = [
            'purchaseCode' => $this->appSetting->purchase_code,
            'domain' => $domain,
            'itemId' => config('froiden_envato.envato_item_id'),
            'appUrl' => urlencode(url()->full()),
            'version' => $version,
        ];

        $response = $this->curl($data);

        if ($response['status'] == 'success') {
            return true;
        }

        return false;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * Show verify page for verification
     */
    public function verifyPurchase()
    {
        return view('vendor.verify-purchase.index');
    }

    /**
     * @param Request $request
     * @return array
     * Send request on froiden envato server to validate
     */
    public function purchaseVerified(Request $request)
    {
        $this->setConfig();
        $request->validate([
            'purchase_code' => 'required|max:255',
        ]);

        $version = File::get(public_path('version.txt'));

        $postData = [
            'purchaseCode' => $request->purchase_code,
            'domain' => \request()->getHost(),
            'itemId' => config('froiden_envato.envato_item_id'),
            'appUrl' => urlencode(url()->full()),
            'version' => $version,
        ];

        // Send request to froiden server to validate the license
        $response = $this->curl($postData);

        if ($response['status'] === 'success') {

            $this->saveToSettings($request);

            return $this->reply->success($response['message'] . ' <a href="' . route(config('froiden_envato.redirectRoute')) . '">Click to go back</a>');
        }

        return $this->reply->error($response['message']);
    }


    /**
     * @param $request
     * Save purchase code to settings table
     */
    public function saveToSettings($request)
    {
        $this->setConfig();
        $setting = $this->appSetting;
        $setting->purchase_code = $request->purchase_code;
        $setting->save();
    }

    /**
     * @param $postData
     * @return mixed
     * Curl post to the server
     */
    public function curl($postData)
    {
        // Verify purchase

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, config('froiden_envato.verify_url'));

            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            $response = json_decode($server_output, true);
            curl_close($ch);

            return $response;

        } catch (\Exception $e) {

            return [
                'status' => 'success',
                'messages' => 'Your purchase code is successfully verified'
            ];
        }

    }

}
