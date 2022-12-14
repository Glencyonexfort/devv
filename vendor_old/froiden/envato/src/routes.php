<?php

Route::group(['namespace' => 'Froiden\Envato\Controllers', 'middleware' => 'web'], function () {

    Route::get('verify-purchase', ['uses' => 'PurchaseVerificationController@verifyPurchase'])->name('verify-purchase');
    Route::post('purchase-verified', ['uses' => 'PurchaseVerificationController@purchaseVerified'])->name('purchase-verified');
    Route::get('update-database', ['uses' => 'UpdateScriptVersionController@updateDatabase'])->name('update-database');

    Route::get('clear-cache', ['uses' => 'UpdateScriptVersionController@clearCache']);
    Route::get('refresh-cache', ['uses' => 'UpdateScriptVersionController@refreshCache']);


    // update script version
    Route::group(['as' => 'admin.','prefix' => 'admin'], function () {
        Route::get('update-version/update', ['as' => 'updateVersion.update', 'uses' => 'UpdateScriptVersionController@update']);
        Route::get('update-version/download', ['as' => 'updateVersion.download', 'uses' => 'UpdateScriptVersionController@download']);
        Route::get('update-version/downloadPercent', ['as' => 'updateVersion.downloadPercent', 'uses' => 'UpdateScriptVersionController@downloadPercent']);
        Route::get('update-version/checkIfFileExtracted', ['as' => 'updateVersion.checkIfFileExtracted', 'uses' => 'UpdateScriptVersionController@checkIfFileExtracted']);
        Route::get('update-version/install', ['as' => 'updateVersion.install', 'uses' => 'UpdateScriptVersionController@install']);
    });
});
