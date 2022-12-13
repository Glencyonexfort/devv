<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/login', 'API\LoginController@login');
Route::post('/login', 'API\LoginController@login');
//APi for Zapier
Route::post('/create-removal-opportunity', 'API\RemovalOpportunityController@createOpportunity');
Route::post('/create-eol-cleaning-opportunity', 'API\EOLCleaningController@createEOLCleaningOpportunity');
Route::post('/get-company-list', 'API\EOLCleaningController@getCompanylist');
Route::post('/get-city-list', 'API\EOLCleaningController@getCitylist');
Route::post('/postmarkapp-email-bounced', 'PostMarkAppController@processInboundEmailBounced');


Route::middleware('auth:api')->group(function() {

    Route::get('/start-job', 'API\ListJobsController@startJob');
    Route::post('/start-job', 'API\ListJobsController@startJob');

    Route::get('/complete-job', 'API\ListJobsController@completeJob');
    Route::post('/complete-job', 'API\ListJobsController@completeJob');

    Route::get('/accept-job', 'API\ListJobsController@acceptJob');
    Route::post('/accept-job', 'API\ListJobsController@acceptJob');


    Route::get('/get-job-list', 'API\ListJobsController@getJobList');
    Route::post('/get-job-list', 'API\ListJobsController@getJobList');

    Route::get('/get-job-detail', 'API\ListJobsController@getJobDetail');
    Route::post('/get-job-detail', 'API\ListJobsController@getJobDetail');

    Route::get('/get-payment_details', 'API\ListJobsController@getPaymentDetails');
    Route::post('/get-payment-details', 'API\ListJobsController@getPaymentDetails');

    Route::get('/upload-attachment-to-job', 'API\ListJobsController@uploadAttachmentToJob');
    Route::post('/upload-attachment-to-job', 'API\ListJobsController@uploadAttachmentToJob');

    Route::get('/delete-attachment-from-job', 'API\ListJobsController@deleteAttachmentFromJob');
    Route::post('/delete-attachment-from-job', 'API\ListJobsController@deleteAttachmentFromJob');

    Route::get('/get-user-profile', 'API\UserController@getUserProfile');
    Route::post('/get-user-profile', 'API\UserController@getUserProfile');
     
    Route::get('/update-user-profile', 'API\UserController@updateUserProfile');
    Route::post('/update-user-profile', 'API\UserController@updateUserProfile');

    Route::get('/pre-job-customer-sign-off', 'API\ListJobsController@customerPreJobSignOff');
    Route::post('/pre-job-customer-sign-off', 'API\ListJobsController@customerPreJobSignOff');

    Route::get('/customer-sign-off', 'API\ListJobsController@customerSignOff');
    Route::post('/customer-sign-off', 'API\ListJobsController@customerSignOff');

    Route::get('/get-job-address', 'API\ListJobsController@getJobAddress');
    Route::post('/get-job-address', 'API\ListJobsController@getJobAddress');

    Route::get('/stripe-pay', 'API\ListJobsController@stripePay');
    Route::post('/stripe-pay', 'API\ListJobsController@stripePay');

    Route::get('/invoice-pay', 'API\ListJobsController@invoicePay');
    Route::post('/invoice-pay', 'API\ListJobsController@invoicePay');

    Route::get('/get-products', 'API\ListJobsController@getProducts');
    Route::post('/get-products', 'API\ListJobsController@getProducts');
    

    Route::get('/get-pending-approval-invoice-items', 'API\ListJobsController@getPendingApprovalInvoiceItems');
    Route::post('/get-pending-approval-invoice-items', 'API\ListJobsController@getPendingApprovalInvoiceItems');

    Route::get('/send-for-approval-and-payment', 'API\ListJobsController@sendForApprovalAndPayment');
    Route::post('/send-for-approval-and-payment', 'API\ListJobsController@sendForApprovalAndPayment');

    Route::get('/register-device-token', 'API\LoginController@registerDeviceToken');
    Route::post('/register-device-token', 'API\LoginController@registerDeviceToken');
    
    Route::get('/push', 'API\ListJobsController@push');
    Route::post('/push', 'API\ListJobsController@push');

    Route::get('/update-actual-hours', 'API\ListJobsController@updateActualhours');
    Route::post('/update-actual-hours', 'API\ListJobsController@updateActualhours');

    Route::get('/generate-invoice', 'API\ListJobsController@generateInvoice');
    Route::post('/generate-invoice', 'API\ListJobsController@generateInvoice');    
    
    Route::get('/get-offsiders', 'API\ListJobsController@getOffsiders');
    Route::post('/get-offsiders', 'API\ListJobsController@getOffsiders');

    Route::get('/get-job-inventory', 'API\ListJobsController@getJobInventory');
    Route::post('/get-job-inventory', 'API\ListJobsController@getJobInventory');    

    Route::get('/get-payment-methods', 'API\ListJobsController@getPaymentMethods');
    Route::post('/get-payment-methods', 'API\ListJobsController@getPaymentMethods');

    Route::get('/get-notes-list', 'API\ListJobsController@getNotesList');
    Route::post('/get-notes-list', 'API\ListJobsController@getNotesList');

    Route::get('/save-notes', 'API\ListJobsController@saveNotes');
    Route::post('/save-notes', 'API\ListJobsController@saveNotes');

    Route::get('/add-items-to-invoice', 'API\ListJobsController@addItemsToInvoice');
    Route::post('/add-items-to-invoice', 'API\ListJobsController@addItemsToInvoice');

    Route::get('/email-invoice-to-customer', 'API\ListJobsController@emailInvoiceToCustomer');
    Route::post('/email-invoice-to-customer', 'API\ListJobsController@emailInvoiceToCustomer');    

    Route::get('/get-job-trips', 'API\ListJobsController@getJobTrips');
    Route::post('/get-job-trips', 'API\ListJobsController@getJobTrips');

    Route::get('/get-material-issue-list', 'API\ListJobsController@getJobPackingMaterialIssueList');
    Route::post('/get-material-issue-list', 'API\ListJobsController@getJobPackingMaterialIssueList');

    Route::get('/update-material-issue', 'API\ListJobsController@updateJobPackingMaterialIssue');
    Route::post('/update-material-issue', 'API\ListJobsController@updateJobPackingMaterialIssue');

    Route::get('/delete-material-issue', 'API\ListJobsController@deleteJobPackingMaterialIssue');
    Route::post('/delete-material-issue', 'API\ListJobsController@deleteJobPackingMaterialIssue');

    Route::get('/get-stockable-items', 'API\ListJobsController@getStockableItems');
    Route::post('/get-stockable-items', 'API\ListJobsController@getStockableItems');

    Route::get('/add-job-packing-material-issue', 'API\ListJobsController@addJobPackingMaterialIssue');
    Route::post('/add-job-packing-material-issue', 'API\ListJobsController@addJobPackingMaterialIssue');

    Route::get('/get-material-return-list', 'API\ListJobsController@getJobPackingMaterialReturnList');
    Route::post('/get-material-return-list', 'API\ListJobsController@getJobPackingMaterialReturnList');

    Route::get('/update-material-return', 'API\ListJobsController@updateJobPackingMaterialReturn');
    Route::post('/update-material-return', 'API\ListJobsController@updateJobPackingMaterialReturn');

    Route::get('/delete-material-return', 'API\ListJobsController@deleteJobPackingMaterialReturn');
    Route::post('/delete-material-return', 'API\ListJobsController@deleteJobPackingMaterialReturn');

    Route::get('/add-job-packing-material-return', 'API\ListJobsController@addJobPackingMaterialReturn');
    Route::post('/add-job-packing-material-return', 'API\ListJobsController@addJobPackingMaterialReturn');

    Route::get('/update-generate-invoice-packing-material', 'API\ListJobsController@updateGenerateInvoicePackingMaterial');
    Route::post('/update-generate-invoice-packing-material', 'API\ListJobsController@updateGenerateInvoicePackingMaterial');

    Route::get('/get-ohs-checklist', 'API\ListJobsController@getOhsChecklist');
    Route::post('/get-ohs-checklist', 'API\ListJobsController@getOhsChecklist');

    Route::get('/add-ohs-checklist', 'API\ListJobsController@addOhsChecklist');
    Route::post('/add-ohs-checklist', 'API\ListJobsController@addOhsChecklist');

    Route::get('/get-vehicle-checklist', 'API\ListJobsController@getVehicleChecklist');
    Route::post('/get-vehicle-checklist', 'API\ListJobsController@getVehicleChecklist');

    Route::get('/add-vehicle-checklist', 'API\ListJobsController@addVehicleChecklist');
    Route::post('/add-vehicle-checklist', 'API\ListJobsController@addVehicleChecklist');
});
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});