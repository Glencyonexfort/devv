<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Xero Laravel configuration
    |--------------------------------------------------------------------------
    |
    | This file is for storing the configuration for the Langley Foxall
    | Xero Laravel package.
    |
    */

    'apps' => [
        'default' => [
            'client_id'     => "BFF096D9071F4C83A969BE8730CB0073",
            'client_secret' => "nh-43enb0eK8D3G8QA6fdLxgNSdREXaU56wZmqTIBUAAeO1o",
            'redirect_uri'  => "https://app.onexfort.com/admin/settings/connect-xero/callback",
            'scope'         => 'openid email profile offline_access accounting.settings accounting.transactions accounting.contacts',
        ],
    ],
];
