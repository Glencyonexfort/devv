<?php
/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('check-auth-login', ['uses' => 'HomeController@checkAuthLogin'])->name('check-auth-login');
Route::get('/invoice/{id}', ['uses' => 'HomeController@invoice'])->name('front.invoice');
Route::get('/', ['uses' => 'HomeController@login']);
Route::get('/registration', ['uses' => 'TenantController@create'])->name('registration');
Route::post('/registration-generate-opt', ['uses' => 'TenantController@generateOpt'])->name('registration-generate-opt');
Route::post('/verify-opt', ['uses' => 'TenantController@verifyOpt'])->name('verify-opt');
Route::post('/registration', ['uses' => 'TenantController@store']);
Route::get('/checkemail',['uses'=>'TenantController@checkEmail']);
Route::get('/sess', ['uses' => 'QuoteController@sess']);
Route::get('/quote/{id}/{company_id}', ['uses' => 'QuoteController@create']);
Route::get('/quote', ['uses' => 'QuoteController@store'])->name('quote-store');
Route::post('/quote', ['uses' => 'QuoteController@store'])->name('quote-store');

Route::get('/quote-cleaning/{id}/{company_id}', ['uses' => 'QuoteCleaningController@create']);
Route::get('/quote-cleaning', ['uses' => 'QuoteCleaningController@store'])->name('quote-cleaning-store');
Route::post('/quote-cleaning', ['uses' => 'QuoteCleaningController@store'])->name('quote-cleaning-store');
Route::get('/quote-cleaning/payLater', ['uses' => 'QuoteCleaningController@payLater'])->name('quote-cleaning-pay-later');
Route::get('/quote-cleaning/payNow', ['uses' => 'QuoteCleaningController@payNow'])->name('quote-cleaning-pay-now');

Route::get('/quote-lease-cleaning/{id}/{company_id}/{city_id}/{discount}', ['uses' => 'QuoteLeaseCleaningController@create']);
Route::get('/quote-lease-cleaning', ['uses' => 'QuoteLeaseCleaningController@store'])->name('quote-lease-cleaning-store');
Route::post('/quote-lease-cleaning', ['uses' => 'QuoteLeaseCleaningController@store'])->name('quote-lease-cleaning-store');
Route::get('/quote-lease-cleaning/payLater', ['uses' => 'QuoteLeaseCleaningController@payLater'])->name('quote-lease-cleaning-pay-later');
Route::get('/quote-lease-cleaning/payNow', ['uses' => 'QuoteLeaseCleaningController@payNow'])->name('quote-lease-cleaning-pay-now');
Route::get('/quote-lease-cleaning/ajaxStartTime', ['uses' => 'QuoteLeaseCleaningController@ajaxStartTime']);

//Pay-Now-Stripe
Route::get('/pay-now/{params}', ['uses' => 'StripePaymentController@payNow'])->name('pay-now');
Route::get('/pay-now-booking-fee/{params}', ['uses' => 'StripePaymentController@payNowBookingFee'])->name('pay-now-booking-fee');
Route::get('/pay-now-inv/{params}', ['uses' => 'StripePaymentController@payNowInvoice'])->name('pay-now-inv');
Route::get('/pay-now-pending-amount/{params}', ['uses' => 'StripePaymentController@payNowPendingAmount'])->name('pay-now-pending-amount');
Route::post('/paymentCharge', ['uses' => 'StripePaymentController@paymentCharge'])->name('paymentCharge');
Route::post('/paymentChargeApproval', ['uses' => 'StripePaymentController@paymentChargeApproval'])->name('paymentChargeApproval');

//External Inventory Form
Route::get('/removals-inventory-form/{params}', ['uses' => 'ExternalInventoryController@inventoryForm'])->name('removals-inventory-form');
Route::post('/get-inventory-data-external/{job_id}', ['uses' => 'ExternalInventoryController@getInventoryDetails'])->name('get-inventory-data-external');
Route::post('/save-inventory-data-external/{job_id}', ['uses' => 'ExternalInventoryController@saveInventoryData'])->name('save-inventory-data-external');
Route::post('/save-inventory-miscellanceous-data-external/{id}', ['uses' => 'ExternalInventoryController@saveInventoryMiscellanceousData'])->name('save-inventory-miscellanceous-data-external');
Route::post('/delete-inventory-data-external', ['uses' => 'ExternalInventoryController@deleteInventoryData'])->name('delete-inventory-data-external');

//Auto Qoute
Route::get('/run_cleaning_auto_quote_program', ['uses' => 'QuoteController@autoQuoteCleaning'])->name('run_removal_auto_quote_program');
Route::get('/run_removal_auto_quote_program', ['uses' => 'QuoteController@autoQuote'])->name('run_removal_auto_quote_program');
// Paypal IPN
Route::post('verify-ipn', array('as' => 'verify-ipn', 'uses' => 'PaypalIPNController@verifyIPN'));
Route::post('/verify-webhook', 'StripeWebhookController@verifyStripeWebhook');

Route::post('/postmarkapp-email-opened', array('as' => 'postmarkapp-email-opened', 'uses' => 'PostMarkAppController@processInboundEmailOpened'));
Route::post('/postmarkapp-email-received', array('as' => 'postmarkapp-email-received', 'uses' => 'PostMarkAppController@processInboundEmailReceived'));
Route::get('postmarkapp-email-bounced', array('as' => 'postmarkapp-email-bounced', 'uses' => 'PostMarkAppController@processInboundEmailBounced'));

//Route::post('/email-opened', ['uses' => 'HomeController@processInboundEmailOpened'])->name('front.email-opened');
//Route::post('/email-recieved', ['uses' => 'HomeController@processInboundEmailReceived'])->name('front.email-recieved');

//Clear Cache facade value:
Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    return '<h1>Cache facade value cleared</h1>';
});


//Clear View cache:
Route::get('/view-clear', function() {
    $exitCode = Artisan::call('view:clear');
    return '<h1>View cache cleared</h1>';
});

//Clear Config cache:
Route::get('/config-clear', function() {
    $exitCode = Artisan::call('config:clear');
    return '<h1>Config Clear</h1>';
});

//Clear Config cache:
Route::get('/config-cache', function() {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Config Cache</h1>';
});

Route::group(
    ['namespace' => 'Client', 'prefix' => 'client', 'as' => 'client.'],
    function () {
        Route::post('stripe/{invoiceId}', array('as' => 'stripe', 'uses' => 'StripeController@paymentWithStripe',));
        Route::post('stripe-public/{invoiceId}', array('as' => 'stripe-public', 'uses' => 'StripeController@paymentWithStripePublic',));
        // route for post request
        Route::get('paypal-public/{invoiceId}', array('as' => 'paypal-public', 'uses' => 'PaypalController@paymentWithpaypalPublic',));
        Route::get('paypal/{invoiceId}', array('as' => 'paypal', 'uses' => 'PaypalController@paymentWithpaypal',));
        // route for check status responce
        Route::get('paypal', array('as' => 'status', 'uses' => 'PaypalController@getPaymentStatus',));
        Route::get('paypal-recurring', array('as' => 'paypal-recurring', 'uses' => 'PaypalController@payWithPaypalRecurrring',));
    }
);
Auth::routes();
Route::group(['middleware' => 'auth'], function () {
    // Data Migration Route
    Route::get('/data-migration', ['uses' => 'TenantController@dataMigrate'])->name('data.migration');
    // Admin routes
    Route::group(
        ['namespace' => 'Admin', 'prefix' => 'admin', 'as' => 'admin.'],
        function () {
            Route::get('/', 'CRMActivityController@index');
            Route::get('/dashboard', 'AdminDashboardController@index')->name('dashboard');
            Route::post('/ajax-main-search', 'CRMActivityController@ajaxMainSearch')->name('ajax-main-search');
            Route::get('/inbox', 'CRMActivityController@index')->name('inbox');
            Route::post('/getActivityData', 'CRMActivityController@getActivityData')->name('getActivityData');
            Route::post('/updateActivityData', 'CRMActivityController@updateActivityData')->name('updateActivityData');
            Route::get('/updateActivityDataInIds', 'CRMActivityController@updateActivityDataInIds')->name('updateActivityDataInIds');
            Route::post('/set-email-as-read', 'AdminDashboardController@setEmailAsRead')->name('setEmailAsRead');
            Route::get('clients/export/{status?}/{client?}', ['uses' => 'ManageClientsController@export'])->name('clients.export');
            Route::get('clients/data', ['uses' => 'ManageClientsController@data'])->name('clients.data');
            Route::get('clients/create/{clientID?}', ['uses' => 'ManageClientsController@create'])->name('clients.create');
            Route::resource('clients', 'ManageClientsController', ['expect' => ['create']]);
            // Route::get('leads/export/{followUp?}/{client?}', ['uses' => 'LeadController@export'])->name('leads.export');
            // Route::get('leads/data', ['uses' => 'LeadController@data'])->name('leads.data');
            // Route::post('leads/change-status', ['uses' => 'LeadController@changeStatus'])->name('leads.change-status');
            // Route::get('leads/follow-up/{leadID}', ['uses' => 'LeadController@followUpCreate'])->name('leads.follow-up');
            // Route::get('leads/followup/{leadID}', ['uses' => 'LeadController@followUpShow'])->name('leads.followup');
            // Route::post('leads/follow-up-store', ['uses' => 'LeadController@followUpStore'])->name('leads.follow-up-store');
            // Route::get('leads/follow-up-edit/{id?}', ['uses' => 'LeadController@editFollow'])->name('leads.follow-up-edit');
            // Route::post('leads/follow-up-update', ['uses' => 'LeadController@UpdateFollow'])->name('leads.follow-up-update');
            // Route::get('leads/follow-up-sort', ['uses' => 'LeadController@followUpSort'])->name('leads.follow-up-sort');
            // Route::resource('leads', 'LeadController');
            // Lead Files
            // Route::get('lead-files/download/{id}', ['uses' => 'LeadFilesController@download'])->name('lead-files.download');
            // Route::get('lead-files/thumbnail', ['uses' => 'LeadFilesController@thumbnailShow'])->name('lead-files.thumbnail');
            // Route::resource('lead-files', 'LeadFilesController');
            // Proposal routes

            //Removal Settings
            Route::get('pricing-setting', ['uses' => 'ManagePricingSettingsController@pricingSetting'])->name('pricing-setting');
            Route::get('local-move-setting', ['uses' => 'ManagePricingSettingsController@localMoveSetting'])->name('local-move-setting');

            Route::get('proposals/data/{id?}', ['uses' => 'ProposalController@data'])->name('proposals.data');
            Route::get('proposals/download/{id}', ['uses' => 'ProposalController@download'])->name('proposals.download');
            Route::get('proposals/create/{leadID?}', ['uses' => 'ProposalController@create'])->name('proposals.create');
            Route::resource('proposals', 'ProposalController', ['expect' => ['create']]);
            // Holidays
            Route::get('holidays/calendar-month', 'HolidaysController@getCalendarMonth')->name('holidays.calendar-month');
            Route::get('holidays/view-holiday/{year?}', 'HolidaysController@viewHoliday')->name('holidays.view-holiday');
            Route::get('holidays/mark_sunday', 'HolidaysController@Sunday')->name('holidays.mark-sunday');
            Route::get('holidays/calendar/{year?}', 'HolidaysController@holidayCalendar')->name('holidays.calendar');
            Route::get('holidays/mark-holiday', 'HolidaysController@markHoliday')->name('holidays.mark-holiday');
            Route::post('holidays/mark-holiday-store', 'HolidaysController@markDayHoliday')->name('holidays.mark-holiday-store');
            Route::resource('holidays', 'HolidaysController');

            Route::group(
                ['prefix' => 'moving-settings'],
                function () {
                    Route::get('companies', ['uses' => 'ManageCompaniesController@index'])->name('companies.index');
                    Route::get('companies/data', ['uses' => 'ManageCompaniesController@data'])->name('companies.data');
                    Route::get('companies/export', ['uses' => 'ManageCompaniesController@export'])->name('companies.export');
                    Route::resource('companies', 'ManageCompaniesController');
                    Route::get('email-templates', ['uses' => 'ManageEmailTemplatesController@index'])->name('email-templates.index');
                    Route::post('storeTemplateAttachment', ['uses' => 'ManageEmailTemplatesController@storeTemplateAttachment'])->name('storeTemplateAttachment');
                    Route::post('removeTemplateAttachment', ['uses' => 'ManageEmailTemplatesController@removeTemplateAttachment'])->name('removeTemplateAttachment');
                    Route::get('viewTemplateAttachment/{id}', ['uses' => 'ManageEmailTemplatesController@viewTemplateAttachment'])->name('viewTemplateAttachment');
                    Route::get('email-templates/data', ['uses' => 'ManageEmailTemplatesController@data'])->name('email-templates.data');
                    Route::get('email-templates/export', ['uses' => 'ManageEmailTemplatesController@export'])->name('email-templates.export');
                    Route::resource('email-templates', 'ManageEmailTemplatesController');

                    Route::get('email-sequences', ['uses' => 'ManageEmailSequencesController@index'])->name('email-sequences.index');
                    Route::get('email-sequences/data', ['uses' => 'ManageEmailSequencesController@data'])->name('email-sequences.data');
                    Route::get('email-sequences/export', ['uses' => 'ManageEmailSequencesController@export'])->name('email-sequences.export');
                    Route::resource('email-sequences', 'ManageEmailSequencesController');

                    Route::get('sms-templates', ['uses' => 'ManageSMSTemplatesController@index'])->name('sms-templates.index');
                    Route::get('sms-templates/data', ['uses' => 'ManageSMSTemplatesController@data'])->name('sms-templates.data');
                    Route::get('sms-templates/export', ['uses' => 'ManageSMSTemplatesController@export'])->name('sms-templates.export');
                    Route::resource('sms-templates', 'ManageSMSTemplatesController');

                    Route::get('statuses', ['uses' => 'ManageStatusesController@index'])->name('statuses.index');
                    Route::get('statuses/data', ['uses' => 'ManageStatusesController@data'])->name('statuses.data');
                    Route::get('statuses/edit/{id}', ['uses' => 'ManageStatusesController@edit'])->name('statuses.edit');
                    Route::post('ajaxDestroyLeadStatus', ['uses' => 'ManageStatusesController@ajaxDestroyLeadStatus'])->name('statuses.ajaxDestroyLeadStatus');
                    Route::get('statuses/delete/{id}', ['uses' => 'ManageStatusesController@delete'])->name('statuses.delete');
                    Route::post('ajaxDeleteLeadStatuses', ['uses' => 'ManageStatusesController@ajaxDeleteLeadStatuses'])->name('statuses.ajaxDeleteLeadStatuses');
                    Route::post('ajaxUpdateLeadStatusReorder', ['uses' => 'ManageStatusesController@ajaxUpdateLeadStatusReorder'])->name('statuses.ajaxUpdateLeadStatusReorder');

                    Route::get('statuses/dataPipeline', ['uses' => 'ManageStatusesController@dataPipeline'])->name('statuses.dataPipeline');
                    Route::get('statuses/editPipeline/{id}', ['uses' => 'ManageStatusesController@editPipeline'])->name('statuses.editPipeline');
                    Route::post('statuses/storePipeline', ['uses' => 'ManageStatusesController@storePipeline'])->name('statuses.storePipeline');
                    Route::post('statuses/updatePipeline', ['uses' => 'ManageStatusesController@updatePipeline'])->name('statuses.updatePipeline');
                    Route::post('ajaxDestroyPipelineStatus', ['uses' => 'ManageStatusesController@ajaxDestroyPipelineStatus'])->name('statuses.ajaxDestroyPipelineStatus');
                    Route::get('statuses/deletePipeline/{id}', ['uses' => 'ManageStatusesController@deletePipeline'])->name('statuses.deletePipeline');
                    Route::post('ajaxDeletePipelineStatuses', ['uses' => 'ManageStatusesController@ajaxDeletePipelineStatuses'])->name('statuses.ajaxDeletePipelineStatuses');
                    Route::post('ajaxUpdatePipelineStatusReorder', ['uses' => 'ManageStatusesController@ajaxUpdatePipelineStatusReorder'])->name('statuses.ajaxUpdatePipelineStatusReorder');

                    Route::resource('statuses', 'ManageStatusesController');

                    Route::get('job-templates', ['uses' => 'ManageJobTemplatesMovingController@index'])->name('job-templates.index');
                    Route::get('job-templates/data', ['uses' => 'ManageJobTemplatesMovingController@data'])->name('job-templates.data');
                    Route::delete('job-templates/destroy-attachment/{id?}', ['uses' => 'ManageJobTemplatesMovingController@destroyTemplateAttachment'])->name('job-templates.destroy-attachment');
                    Route::get('job-templates/export', ['uses' => 'ManageJobTemplatesMovingController@export'])->name('job-templates.export');
                    Route::resource('job-templates', 'ManageJobTemplatesMovingController');
                    
                    Route::get('vehicles', ['uses' => 'ManageVehiclesController@index'])->name('vehicles.index');
                    Route::get('vehicles/data', ['uses' => 'ManageVehiclesController@data'])->name('vehicles.data');
                    Route::get('vehicles/export', ['uses' => 'ManageVehiclesController@export'])->name('vehicles.export');
                    Route::resource('vehicles', 'ManageVehiclesController');

                    Route::get('vehiclegroups', ['uses' => 'ManageVehicleGroupsController@index'])->name('vehicle-groups.index');
                    Route::post('ajaxUpdateVehicleGroup', ['uses' => 'ManageVehicleGroupsController@ajaxUpdateVehicleGroup'])->name('vehiclegroups.ajaxUpdateVehicleGroup');
                    Route::post('ajaxCreateVehicleGroup', ['uses' => 'ManageVehicleGroupsController@ajaxCreateVehicleGroup'])->name('vehiclegroups.ajaxCreateVehicleGroup');
                    Route::post('ajaxDestroyVehicleGroup', ['uses' => 'ManageVehicleGroupsController@ajaxDestroyVehicleGroup'])->name('vehiclegroups.ajaxDestroyVehicleGroup');
                    Route::resource('vehicleGroups', 'ManageVehicleGroupsController');

                    Route::get('/vehicles-daily-checklist', ['uses' => 'VehiclesDailyChecklistController@index'])->name('vehiclesDailyChecklist.index');
                    Route::post('/ajaxCreateGroup', ['uses' => 'VehiclesDailyChecklistController@store'])->name('vehiclesDailyChecklist.group.store');
                    Route::post('/ajaxUpdateGroup', ['uses' => 'VehiclesDailyChecklistController@update'])->name('vehiclesDailyChecklist.group.update');
                    Route::post('/ajaxDestroyGroup', ['uses' => 'VehiclesDailyChecklistController@delete'])->name('vehiclesDailychecklist.group.destroy');

                    Route::post('ajaxGetGroup', ['uses' => 'VehiclesDailyChecklistController@getGroup'])->name('vehiclesDailyChecklist.groupChecklist.get');
                    Route::post('/ajaxCreateGroupChecklist', ['uses' => 'VehiclesDailyChecklistController@storeGroupChecklist'])->name('vehiclesDailyChecklist.groupChecklist.store');
                    Route::post('/ajaxUpdateGroupChecklist', ['uses' => 'VehiclesDailyChecklistController@updateGroupChecklist'])->name('vehiclesDailyChecklist.groupChecklist.update');
                    Route::post('/ajaxDestroyGroupChecklist', ['uses' => 'VehiclesDailyChecklistController@deleteGroupChecklist'])->name('vehiclesDailychecklist.groupChecklist.destroy');
                    Route::post('/ajaxLoadGroupChecklist', ['uses' => 'VehiclesDailyChecklistController@loadGroupchecklist'])->name('vehiclesDailyChecklist.groupChecklist.load');


                    Route::get('/ohs-checklist', ['uses' => 'OHSChecklistController@index'])->name('ohsChecklist.index');
                    Route::post('/ajaxCreateChecklist', ['uses' => 'OHSChecklistController@store'])->name('ohsChecklist.store');
                    Route::post('/ajaxUpdateChecklist', ['uses' => 'OHSChecklistController@update'])->name('ohsChecklist.update');
                    Route::post('/ajaxDestroyChecklist', ['uses' => 'OHSChecklistController@delete'])->name('ohsChecklist.destroy');

                    //Backloading Routes
                    Route::get('backloading', ['uses' => 'BackloadingController@index'])->name('backloading.index');
                    Route::post('create-new-trip', ['uses' => 'BackloadingController@store'])->name('backloading.store');
                    Route::post('delete-backloading-trip', ['uses' => 'BackloadingController@destroy'])->name('backloading.destroy');
                    Route::get('ajax-backloading-data', ['uses' => 'BackloadingController@getData'])->name('backloading-getData');
                    Route::get('ajax-search-trip', ['uses' => 'BackloadingController@search'])->name('backloading.search');
                    Route::get('trip-assign-job-page', ['uses' => 'BackloadingController@assignJob'])->name('backloading.assignJob');
                    Route::post('update-trip', ['uses' => 'BackloadingController@updateTrip'])->name('backloading.updateTrip');
                    Route::get('get-trip-jobs', ['uses' => 'BackloadingController@getTripJobs'])->name('backloading.getTripJobs');
                    Route::get('get-search-jobs', ['uses' => 'BackloadingController@getSearchJobs'])->name('backloading.getSearchJobs');
                    Route::post('trip-unassign-job', ['uses' => 'BackloadingController@tripUnassignJob'])->name('backloading.tripUnassignJob');
                    Route::post('trip-assign-job', ['uses' => 'BackloadingController@tripAssignJob'])->name('backloading.tripAssignJob');
                    Route::get('generate-waybill/{id}', ['uses' => 'BackloadingController@generateWaybill'])->name('backloading.generate-waybill');
                    Route::get('download-waybill/{id}', ['uses' => 'BackloadingController@downloadWaybill'])->name('backloading.download-waybill');

                    Route::get('vehicle-unavailability', ['uses' => 'ManageVehiclesController@vehicleUnavailability'])->name('vehicle-unavailability');
                    Route::get('vehicle-unavailability/data', ['uses' => 'ManageVehiclesController@vehicleUnavailabilityData'])->name('vehicle-unavailability.data');
                    Route::post('vehicle-unavailabilityStore', ['uses' => 'ManageVehiclesController@vehicleUnavailabilityStore'])->name('vehicle-unavailability.store');
                    Route::get('vehicle-unavailability/edit/{id}', ['uses' => 'ManageVehiclesController@vehicleUnavailabilityEdit'])->name('vehicle-unavailability.edit');
                    Route::post('vehicle-unavailabilityUpdate', ['uses' => 'ManageVehiclesController@vehicleUnavailabilityUpdate'])->name('vehicle-unavailability.update');
                    Route::get('vehicle-unavailability/destroy/{id}', ['uses' => 'ManageVehiclesController@vehicleUnavailabilityDestroy'])->name('vehicle-unavailability.destroy');

                    Route::get('vehicle-unavailability-calender/{id?}', ['uses' => 'ManageVehiclesController@vehicleUnavailabilityCalender'])->name('vehicle-unavailability-calender');
                    Route::get('vehicle-unavailability-calender-data/{id?}', ['uses' => 'ManageVehiclesController@vehicleUnavailabilityCalenderData'])->name('vehicle-unavailability.calender-data');                    


                    Route::get('enableAutoQuote', ['uses' => 'ManageAutoQuoteController@index'])->name('auto-quote.index');
                    Route::post('saveAutoQuoteData/{id}', ['uses' => 'ManageAutoQuoteController@saveAutoQuoteData'])->name('enableAutoQuote.saveAutoQuoteData');
                    Route::post('createAutoQuoteData/{id}', ['uses' => 'ManageAutoQuoteController@createAutoQuoteData'])->name('enableAutoQuote.createAutoQuoteData');
                    Route::resource('enableAutoQuote', 'ManageAutoQuoteController');


                    Route::get('pricingSettings', ['uses' => 'ManagePricingSettingsController@index'])->name('pricing-settings.index');
                    Route::post('savePricingSettingsData/{id}', ['uses' => 'ManagePricingSettingsController@savePricingSettingsData'])->name('pricingSettings.savePricingSettingsData');
                    Route::post('createPricingSettingsData', ['uses' => 'ManagePricingSettingsController@createPricingSettingsData'])->name('pricingSettings.createPricingSettingsData');
                    Route::resource('pricingSettings', 'ManagePricingSettingsController');


                    Route::get('hourlySettings', ['uses' => 'ManageHourlySettingsController@index'])->name('pricing-settings.index');
                    Route::post('saveHourlySettingsData/{id}', ['uses' => 'ManageHourlySettingsController@saveHourlySettingsData'])->name('hourlySettings.saveHourlySettingsData');
                    Route::post('createHourlySettingsData', ['uses' => 'ManageHourlySettingsController@createHourlySettingsData'])->name('hourlySettings.createHourlySettingsData');
                    Route::get('pricing-region', ['uses' => 'ManageRegionPricingController@pricingRegion'])->name('pricing-region');
                    Route::get('region-to-region-pricing', ['uses' => 'ManageRegionToRegionPricingController@index'])->name('region-to-region-pricing');
                    Route::get('removal-quote-form', ['uses' => 'ManageRemovalQuoteFormController@index'])->name('removal-quote-form');
                    Route::post('saveRemovalQuoteFormSettings/{id}', ['uses' => 'ManageRemovalQuoteFormController@saveRemovalQuoteFormSettings'])->name('removal-quote-form.saveRemovalQuoteFormSettings');

                    Route::post('ajaxUpdateRegionToRegionPricing', ['uses' => 'ManageRegionToRegionPricingController@ajaxUpdateRegionToRegionPricing'])->name('pricing-region.ajaxUpdateRegionToRegionPricing');
                    Route::post('ajaxCreateRegionToRegionPricing', ['uses' => 'ManageRegionToRegionPricingController@ajaxCreateRegionToRegionPricing'])->name('pricing-region.ajaxCreateRegionToRegionPricing');
                    Route::post('ajaxDestroyRegionToRegionPricing', ['uses' => 'ManageRegionToRegionPricingController@ajaxDestroyRegionToRegionPricing'])->name('pricing-region.ajaxDestroyRegionToRegionPricing');


                    Route::post('ajaxUpdateRegionPricing', ['uses' => 'ManageRegionPricingController@ajaxUpdateRegionPricing'])->name('pricing-region.ajaxUpdateRegionPricing');
                    Route::post('ajaxCreateRegionPricing', ['uses' => 'ManageRegionPricingController@ajaxCreateRegionPricing'])->name('pricing-region.ajaxCreateRegionPricing');
                    Route::post('ajaxDestroyRegionPricing', ['uses' => 'ManageRegionPricingController@ajaxDestroyRegionPricing'])->name('pricing-region.ajaxDestroyRegionPricing');

                    Route::post('ajaxUpdateTruckSize', ['uses' => 'ManageHourlySettingsController@ajaxUpdateTruckSize'])->name('hourlySettings.ajaxUpdateTruckSize');
                    Route::post('ajaxCreateTruckSize', ['uses' => 'ManageHourlySettingsController@ajaxCreateTruckSize'])->name('hourlySettings.ajaxCreateTruckSize');
                    Route::post('ajaxDestroyTruckSize', ['uses' => 'ManageHourlySettingsController@ajaxDestroyTruckSize'])->name('hourlySettings.ajaxDestroyTruckSize');


                    Route::post('ajaxUpdatedepotLocation', ['uses' => 'ManageHourlySettingsController@ajaxUpdatedepotLocation'])->name('hourlySettings.ajaxUpdatedepotLocation');
                    Route::post('ajaxCreatedepotLocation', ['uses' => 'ManageHourlySettingsController@ajaxCreatedepotLocation'])->name('hourlySettings.ajaxCreatedepotLocation');
                    Route::post('ajaxDestroyDepotLocation', ['uses' => 'ManageHourlySettingsController@ajaxDestroyDepotLocation'])->name('hourlySettings.ajaxDestroyDepotLocation');

                    Route::resource('hourlySettings', 'ManageHourlySettingsController');


                    Route::get('inventoryGroups', ['uses' => 'ManageInventoryGroupsController@index'])->name('inventory-groups.index');
                    Route::post('ajaxUpdateInventoryGroup', ['uses' => 'ManageInventoryGroupsController@ajaxUpdateInventoryGroup'])->name('inventoryGroups.ajaxUpdateInventoryGroup');
                    Route::post('ajaxCreateInventoryGroup', ['uses' => 'ManageInventoryGroupsController@ajaxCreateInventoryGroup'])->name('inventoryGroups.ajaxCreateInventoryGroup');
                    Route::post('ajaxDestroyInventoryGroup', ['uses' => 'ManageInventoryGroupsController@ajaxDestroyInventoryGroup'])->name('inventoryGroups.ajaxDestroyInventoryGroup');
                    Route::resource('inventoryGroups', 'ManageInventoryGroupsController');

                    Route::get('inventoryDefinitions', ['uses' => 'ManageInventoryDefinitionsController@index'])->name('inventory-definitions.index');

                    Route::post('ajaxUpdateInventoryDefinition', ['uses' => 'ManageInventoryDefinitionsController@ajaxUpdateInventoryDefinition'])->name('inventoryDefinitions.ajaxUpdateInventoryDefinition');

                    Route::post('ajaxCreateInventoryDefinition', ['uses' => 'ManageInventoryDefinitionsController@ajaxCreateInventoryDefinition'])->name('inventoryDefinitions.ajaxCreateInventoryDefinition');

                    Route::post('ajaxDestroyInventorDefinition', ['uses' => 'ManageInventoryDefinitionsController@ajaxDestroyInventorDefinition'])->name('inventoryDefinitions.ajaxDestroyInventorDefinition');

                    Route::resource('inventoryDefinitions', 'ManageInventoryDefinitionsController');

                    Route::get('property-category-options', ['uses' => 'ManagePropertyCategoryOptionsController@index'])->name('property-category-options.index');
                    Route::get('property-category-options/data', ['uses' => 'ManagePropertyCategoryOptionsController@data'])->name('property-category-options.data');
                    Route::get('property-category-options/export', ['uses' => 'ManagePropertyCategoryOptionsController@export'])->name('property-category-options.export');
                    Route::resource('property-category-options', 'ManagePropertyCategoryOptionsController');                                        
                    
                }
            );
            Route::group(
                ['prefix' => 'moving'],
                function () {
                    Route::get('list-jobs', ['uses' => 'ListJobsController@index'])->name('list-jobs.index');
                    Route::get('list-jobs/data', ['uses' => 'ListJobsController@data'])->name('list-jobs.data');
                    Route::get('list-jobs/excel/{created_start_date?}/{created_date_end?}/{removal_date_start?}/{removal_date_end?}/{job_status?}/{payment_status?}/{hide_deleted_archived?}', ['uses' => 'ListJobsController@excel'])->name('list-jobs.excel');

                    Route::get('list-jobs/export', ['uses' => 'ListJobsController@export'])->name('list-jobs.export');
                    Route::get('new-job', ['uses' => 'ListJobsController@new_job'])->name('list-jobs.new-job');
                    Route::get('edit-job/{id}', ['uses' => 'ListJobsController@edit_job'])->name('list-jobs.edit-job');
                    Route::get('inventory/{id}', ['uses' => 'ListJobsController@inventory'])->name('list-jobs.inventory');
                    Route::post('save-inventory-data/{id}', ['uses' => 'ListJobsController@saveInventoryData'])->name('list-jobs.save-inventory-data');
                    Route::post('save-inventory-miscellanceous-data/{id}', ['uses' => 'ListJobsController@saveInventoryMiscellanceousData'])->name('list-jobs.save-inventory-miscellanceous-data');
                    Route::post('delete-inventory-data', ['uses' => 'ListJobsController@deleteInventoryData'])->name('list-jobs.delete-inventory-data');
                    Route::post('delete-inventory-miscllanceous-data', ['uses' => 'ListJobsController@deleteInventoryMiscllanceousData'])->name('list-jobs.delete-inventory-miscllanceous-data');
                    Route::post('get-inventory-data', ['uses' => 'ListJobsController@getInventoryDetails'])->name('list-jobs.get-inventory-data');
                    Route::get('operations/{id}', ['uses' => 'ListJobsController@operations'])->name('list-jobs.operations');
                    Route::get('operations-add-leg/{id}', ['uses' => 'ListJobsController@operationsAddLeg'])->name('list-jobs.operations-add-leg');
                    Route::get('operations-delete-leg/{job_id}/{leg_id}', ['uses' => 'ListJobsController@operationsDeleteLeg'])->name('list-jobs.operations-delete-leg');
                    Route::post('operations-save-data/{job_id}', ['uses' => 'ListJobsController@operationsSaveData'])->name('list-jobs.operations-save-data');
                    Route::get('invoice/{id}', ['uses' => 'ListJobsController@invoice'])->name('list-jobs.invoice');
                    // Route::get('generate-quote/{id}', ['uses' => 'ListJobsController@generateQuote'])->name('list-jobs.generate-quote');
                    // Route::get('download-quote/{id}', ['uses' => 'ListJobsController@downloadQuote'])->name('list-jobs.download-quote');
                    Route::get('view-quote/{id}', ['uses' => 'ListJobsController@viewQuote'])->name('list-jobs.view-quote');
                    Route::get('view-attachment/{id}', ['uses' => 'ListJobsController@viewAttachment'])->name('list-jobs.view-attachment');

                    Route::get('view-job-template-attachment/{id}', ['uses' => 'ListJobsController@viewJobTemplateAttachment'])->name('list-jobs.view-job-template-attachment');

                    Route::get('list-jobs/generateInvoice/{id}/{type}', ['uses' => 'ListJobsController@generateInvoice'])->name('list-jobs.generateInvoice');
                    Route::get('list-jobs/downloadInvoice/{id}', ['uses' => 'ListJobsController@downloadInvoice'])->name('list-jobs.downloadInvoice');

                    Route::get('list-jobs/generateStorageInvoice/{id}/{type}', ['uses' => 'ListJobsController@generateStorageInvoice'])->name('list-jobs.generateStorageInvoice');
                    Route::get('list-jobs/downloadStorageInvoice/{id}', ['uses' => 'ListJobsController@downloadStorageInvoice'])->name('list-jobs.downloadStorageInvoice');

                    Route::get('list-jobs/generateWorkOrder/{id}/{type}', ['uses' => 'ListJobsController@generateWorkOrder'])->name('list-jobs.generateWorkOrder');
                    Route::get('list-jobs/downloadWordOrder/{id}', ['uses' => 'ListJobsController@downloadWordOrder'])->name('list-jobs.downloadWordOrder');
                    // Route::get('generate-invoice/{id}', ['uses' => 'ListJobsController@generateInvoice'])->name('list-jobs.generate-invoice');
                    // Route::get('download-invoice/{id}', ['uses' => 'ListJobsController@downloadInvoice'])->name('list-jobs.download-invoice');
                    Route::get('list-jobs/generatePod/{id}/{type}', ['uses' => 'ListJobsController@generatePod'])->name('list-jobs.generatePod');
                    Route::get('list-jobs/downloadPod/{id}', ['uses' => 'ListJobsController@downloadPod'])->name('list-jobs.downloadPod');

                    Route::get('list-jobs/generateInventoryPdf/{id}/{type}', ['uses' => 'ListJobsController@generateInventoryPdf'])->name('list-jobs.generateInventoryPdf');
                    Route::get('list-jobs/downloadInventoryPdf/{id}', ['uses' => 'ListJobsController@downloadInventoryPdf'])->name('list-jobs.downloadInventoryPdf');
                    
                    Route::get('delete-job/{id?}', ['uses' => 'ListJobsController@destroyJob'])->name('list-jobs.delete-job');
                    Route::get('view-invoice/{id}', ['uses' => 'ListJobsController@viewInvoice'])->name('list-jobs.view-invoice');
                    Route::get('generate-inventory-list/{id}', ['uses' => 'ListJobsController@generateInventoryList'])->name('list-jobs.generate-inventory-list');
                    Route::get('download-inventory-list/{id}', ['uses' => 'ListJobsController@downloadInventoryList'])->name('list-jobs.download-inventory-list');
                    Route::get('view-inventory-list/{id}', ['uses' => 'ListJobsController@viewInventoryList'])->name('list-jobs.view-inventory-list');
                    Route::get('email/{id}', ['uses' => 'ListJobsController@email'])->name('list-jobs.email');
                    Route::post('email/{id}', ['uses' => 'ListJobsController@emailSend'])->name('list-jobs.email-send');
                    Route::get('attachment/{id}', ['uses' => 'ListJobsController@attachment'])->name('list-jobs.attachment');
                    Route::get('sms/{id}', ['uses' => 'ListJobsController@sms'])->name('list-jobs.sms');
                    Route::post('sms/{id}', ['uses' => 'ListJobsController@smsSend'])->name('list-jobs.sms-send');
                    Route::post('get-sms-template/{id}', ['uses' => 'ListJobsController@getSMSTemplate'])->name('list-jobs.get-sms-template');

                    Route::get('insurance/{id}', ['uses' => 'ListJobsController@insurance'])->name('list-jobs.insurance');
                    Route::get('send-quote-to-customer/{id}', ['uses' => 'ListJobsController@sendQuoteToCustomer'])->name('list-jobs.send-quote-to-customer');
                    Route::post('attachment/{id}', ['uses' => 'ListJobsController@attachmentUpload'])->name('list-jobs.attachment-upload');
                    Route::post('get-email-template/{id}', ['uses' => 'ListJobsController@getEmailTemplate'])->name('list-jobs.get-email-template');
                    Route::get('job-schedule/{vehicleGroup?}', ['uses' => 'ListJobsController@jobSchedule'])->name('list-jobs.job-schedule');
                    Route::get('calendar-resources/{vehicleGroup?}', ['uses' => 'ListJobsController@getVehicles'])->name('list-jobs.calendar-resources');
                    Route::get('calendar-events/{vehicleGroup?}', ['uses' => 'ListJobsController@getJobs'])->name('list-jobs.calendar-events');

                    // Daily Dairy Routes
                    Route::get('/daily-diary', ['uses' => 'ListJobsController@dailyDiary'])->name('jobs.daily-diary');
                    Route::get('/get-daily-diary-data', ['uses' => 'ListJobsController@getDailyDiaryData'])->name('jobs.get-daily-diary-data');
                    Route::get('/get-daily-diary-today', ['uses' => 'ListJobsController@getDailyDiaryToday'])->name('jobs.get-daily-diary-today');
                    Route::get('/get-daily-diary-right-arrow', ['uses' => 'ListJobsController@getDailyDiaryRightArrow'])->name('jobs.get-daily-diary-right-arrow');
                    Route::get('/get-daily-diary-left-arrow', ['uses' => 'ListJobsController@getDailyDiaryLeftArrow'])->name('jobs.get-daily-diary-left-arrow');
                    Route::get('/get-daily-diary-vehicles', ['uses' => 'ListJobsController@getDailyDiaryVehicles'])->name('jobs.get-daily-diary-vehicles');
                    Route::get('/get-daily-diary-drivers', ['uses' => 'ListJobsController@getDailyDiaryDrivers'])->name('jobs.get-daily-diary-drivers');
                    Route::get('/get-daily-diary-offsiders', ['uses' => 'ListJobsController@getDailyDiaryOffsiders'])->name('jobs.get-daily-diary-offsiders');
                    Route::post('/update-daily-diary', ['uses' => 'ListJobsController@updateDailyDiary'])->name('jobs.update-daily-diary');

                    Route::get('updateScheduleEvent', ['uses' => 'ListJobsController@updateScheduleEvent'])->name('list-jobs.updateScheduleEvent');

                    Route::get('drivers', ['uses' => 'ListJobsController@drivers'])->name('list-jobs.driver-list');

                    Route::get('drivers/data', ['uses' => 'ListJobsController@driversData'])->name('list-jobs.drivers-data');
                    Route::get('drivers/create', ['uses' => 'ListJobsController@createDriver'])->name('list-jobs.create-driver');
                    Route::get('drivers/edit/{id}', ['uses' => 'ListJobsController@editDriver'])->name('list-jobs.edit-driver');
                    Route::post('drivers/store', ['uses' => 'ListJobsController@storeDriver'])->name('list-jobs.store-driver');
                    Route::post('drivers/update/{id}', ['uses' => 'ListJobsController@updateDriver'])->name('list-jobs.update-driver');

                    Route::delete('delete-driver/{id?}', ['uses' => 'ListJobsController@destroyDriver'])->name('list-jobs.delete-driver');

                    Route::post('updateScheduleEvent', ['uses' => 'ListJobsController@updateScheduleEventPost'])->name('list-jobs.updateScheduleEvent');
                    Route::get('job-logs-body/{id}', ['uses' => 'ListJobsController@getJobsLogsBody'])->name('list-jobs.job-logs-body');

                    Route::get('view-job/{id}', ['uses' => 'ListJobsController@viewJob'])->name('list-jobs.view-job');
                    Route::post('ajaxSaveInvoice', ['uses' => 'ListJobsController@ajaxSaveInvoice'])->name('list-jobs.ajaxSaveInvoice');
                    Route::post('ajaxCalculateChargePrice', ['uses' => 'ListJobsController@ajaxCalculateChargePrice'])->name('list-jobs.ajaxCalculateChargePrice');                  
                    Route::post('ajaxReCalculateChargePrice', ['uses' => 'ListJobsController@ajaxReCalculateChargePrice'])->name('list-jobs.ajaxReCalculateChargePrice');                  
                    Route::post('ajaxUpdateInvoice', ['uses' => 'ListJobsController@ajaxUpdateInvoice'])->name('list-jobs.ajaxUpdateInvoice');
                    Route::post('ajaxDestroyInvoiceItem', ['uses' => 'ListJobsController@ajaxDestroyInvoiceItem'])->name('list-jobs.ajaxDestroyInvoiceItem');
                    Route::post('ajaxSavePayment', ['uses' => 'ListJobsController@ajaxSavePayment'])->name('list-jobs.ajaxSavePayment');
                    Route::post('list-jobs/ajaxChargeStripePayment', ['uses' => 'ListJobsController@ajaxChargeStripePayment'])->name('list-jobs.ajaxChargeStripePayment');
                    Route::post('ajaxUpdatePayment', ['uses' => 'ListJobsController@ajaxUpdatePayment'])->name('list-jobs.ajaxUpdateInvoice');
                    Route::post('ajaxDestroyPaymentItem', ['uses' => 'ListJobsController@ajaxDestroyPaymentItem'])->name('list-jobs.ajaxDestroyPaymentItem');
                    Route::post('ajaxSaveInvoiceDiscount', ['uses' => 'ListJobsController@ajaxSaveInvoiceDiscount'])->name('list-jobs.ajaxSaveInvoiceDiscount');

                    // Job Detail

                    Route::post('ajaxUpdateJobDetail', ['uses' => 'ListJobsController@ajaxUpdateJobDetail'])->name('list-jobs.ajaxUpdateJobDetail');
                    Route::post('ajaxUpdateJobPickup', ['uses' => 'ListJobsController@ajaxUpdateJobPickup'])->name('list-jobs.ajaxUpdateJobPickup');
                    Route::post('ajaxUpdateJobDropoff', ['uses' => 'ListJobsController@ajaxUpdateJobDropoff'])->name('list-jobs.ajaxUpdateJobDropoff');
                    Route::post('ajaxSaveJobOperation', ['uses' => 'ListJobsController@ajaxSaveJobOperation'])->name('list-jobs.ajaxSaveJobOperation');
                    Route::post('ajaxUpdateJobOperation', ['uses' => 'ListJobsController@ajaxUpdateJobOperation'])->name('list-jobs.ajaxUpdateJobOperation');
                    Route::post('ajaxDestroyJobOperation', ['uses' => 'ListJobsController@ajaxDestroyJobOperation'])->name('list-jobs.ajaxDestroyJobOperation');
                    Route::post('ajaxSaveJobOperationTrip', ['uses' => 'ListJobsController@ajaxSaveJobOperationTrip'])->name('list-jobs.ajaxSaveJobOperationTrip');
                    Route::post('ajaxUpdateJobOperationTrip', ['uses' => 'ListJobsController@ajaxUpdateJobOperationTrip'])->name('list-jobs.ajaxUpdateJobOperationTrip');
                    Route::post('ajaxDestroyJobOperationTrip', ['uses' => 'ListJobsController@ajaxDestroyJobOperationTrip'])->name('list-jobs.ajaxDestroyJobOperationTrip');                    
                    Route::post('ajaxUpdateActualhours', ['uses' => 'ListJobsController@ajaxUpdateActualhours'])->name('list-jobs.ajaxUpdateActualhours');
                    Route::post('ajaxUpdateRegenrateInvoice', ['uses' => 'ListJobsController@ajaxUpdateRegenrateInvoice'])->name('list-jobs.ajaxUpdateRegenrateInvoice');                    
                    Route::post('ajaxNotifyDriver', ['uses' => 'ListJobsController@ajaxNotifyDriver'])->name('list-jobs.ajaxNotifyDriver');
                    Route::post('ajaxNotifyOffsider', ['uses' => 'ListJobsController@ajaxNotifyOffsider'])->name('list-jobs.ajaxNotifyOffsider');
                    Route::post('sendPushNotification', ['uses' => 'ListJobsController@sendPushNotification'])->name('list-jobs.sendPushNotification');                    
                    Route::post('ajaxReassignDriver', ['uses' => 'ListJobsController@ajaxReassignDriver'])->name('list-jobs.ajaxReassignDriver');
                    
                    //For Offsiders
                    Route::post('ajaxSaveJobOperationOffsider', ['uses' => 'ListJobsController@ajaxSaveJobOperationOffsider'])->name('list-jobs.ajaxSaveJobOperationOffsider');
                    Route::post('ajaxUpdateJobOperationOffsider', ['uses' => 'ListJobsController@ajaxUpdateJobOperationOffsider'])->name('list-jobs.ajaxUpdateJobOperationOffsider');
                    Route::post('ajaxDestroyJobOperationOffsider', ['uses' => 'ListJobsController@ajaxDestroyJobOperationOffsider'])->name('list-jobs.ajaxDestroyJobOperationOffsider');

                    //Storage Module
                    Route::post('storageTabContent', ['uses' => 'ListJobsController@storageTabContent'])->name('list-jobs.storageTabContent');

                    // Matrial Issues and Material Returns Routes
                    Route::post('ajaxSaveMaterialIssue', ['uses' => 'ListJobsController@ajaxSaveMaterialIssue'])->name('list-jobs.ajaxSaveMaterialIssue');
                    Route::put('ajaxUpdateMaterialIssue', ['uses' => 'ListJobsController@ajaxUpdateMaterialIssue'])->name('list-jobs.ajaxUpdateMaterialIssue');
                    Route::post('ajaxDestroyMaterialIssue', ['uses' => 'ListJobsController@ajaxDestroyMaterialIssue'])->name('list-jobs.ajaxDestroyMaterialIssue');

                    Route::post('ajaxSaveMaterialReturn', ['uses' => 'ListJobsController@ajaxSaveMaterialReturn'])->name('list-jobs.ajaxSaveMaterialReturn');
                    Route::put('ajaxUpdateMaterialReturn', ['uses' => 'ListJobsController@ajaxUpdateMaterialReturn'])->name('list-jobs.ajaxUpdateMaterialReturn');
                    Route::post('ajaxDestroyMaterialReturn', ['uses' => 'ListJobsController@ajaxDestroyMaterialReturn'])->name('list-jobs.ajaxDestroyMaterialReturn');
                    Route::get('ajaxGetMaterialReturn', ['uses' => 'ListJobsController@ajaxGetMaterialReturn'])->name('list-jobs.ajaxGetMaterialReturn');

                    Route::post('ajaxUpdateAndGenerateInvoice', ['uses' => 'ListJobsController@ajaxUpdateAndGenerateInvoice'])->name('list-jobs.ajaxUpdateAndGenerateInvoice');

                    //Inventory Section
                    Route::post('ajaxUpdateCbmManually', ['uses' => 'ListJobsController@ajaxUpdateCbmManually'])->name('list-jobs.ajaxUpdateCbmManually');
                    Route::resource('list-jobs', 'ListJobsController');
                }
            );
            // Start:: Storage Module
            Route::group(
                ['prefix' => 'storage'],
                function () {
                    Route::get('units-list', ['uses' => 'StorageController@index'])->name('units-list.index');
                    Route::get('storage-data', ['uses' => 'StorageController@data'])->name('storage.data');
                    Route::post('get-storage-units', ['uses' => 'StorageController@getStorageUnitList'])->name('storage.get-storage-units');
                    Route::post('find-available-storage-units', ['uses' => 'StorageController@findAvailableStorageUnits'])->name('find-available-storage-units');
                    Route::post('ajaxSaveStorageReservation', ['uses' => 'StorageController@ajaxSaveStorageReservation'])->name('ajaxSaveStorageReservation');
                    Route::post('ajaxDestroyStorageReservation', ['uses' => 'StorageController@ajaxDestroyStorageReservation'])->name('ajaxDestroyStorageReservation');

                    Route::get('storage-types', ['uses' => 'StorageController@storageTypes'])->name('storage-types');
                    Route::get('storage-types/data', ['uses' => 'StorageController@storageTypesData'])->name('storage-types.data');
                    Route::get('storage-types/create', ['uses' => 'StorageController@storageTypesCreate'])->name('storage-types.create');
                    Route::post('storage-types/store', ['uses' => 'StorageController@storageTypesStore'])->name('storage-types.store');
                    Route::get('storage-types/edit/{id}', ['uses' => 'StorageController@storageTypesEdit'])->name('storage-types.edit');
                    Route::post('storage-types/update', ['uses' => 'StorageController@storageTypesUpdate'])->name('storage-types.update');
                    Route::delete('storage-types/destroy/{id?}', ['uses' => 'StorageController@storageTypesDestroy'])->name('storage-types.destroy');

                    Route::get('storage-units', ['uses' => 'StorageController@storageUnits'])->name('storage-units');
                    Route::get('storage-units/data', ['uses' => 'StorageController@storageUnitsData'])->name('storage-units.data');
                    Route::get('storage-units/create', ['uses' => 'StorageController@storageUnitsCreate'])->name('storage-units.create');
                    Route::post('storage-units/store', ['uses' => 'StorageController@storageUnitsStore'])->name('storage-units.store');
                    Route::get('storage-units/edit/{id}', ['uses' => 'StorageController@storageUnitsEdit'])->name('storage-units.edit');
                    Route::post('storage-units/update', ['uses' => 'StorageController@storageUnitsUpdate'])->name('storage-units.update');
                    Route::delete('storage-units/destroy/{id?}', ['uses' => 'StorageController@storageUnitsDestroy'])->name('storage-units.destroy');

                    Route::get('storage-units', ['uses' => 'StorageController@storageUnits'])->name('storage-units');

                    Route::get('storage-units-unavailability', ['uses' => 'StorageController@unitsUnavailability'])->name('storage-units-unavailability');
                    Route::post('ajaxSaveStorageReservation', ['uses' => 'StorageController@ajaxSaveStorageReservation'])->name('ajaxSaveStorageReservation');

                }
            );
            // END:: Storage Module
            Route::group(
                ['prefix' => 'cleaning-settings', 'middleware' => ['access.module:2']],
                function () {
                    Route::get('generalQuoteFormSettings', ['uses' => 'CleaningGeneralQuoteFormSettingsController@index'])->name('generalQuoteFormSettings.index');
                    Route::post('saveGeneralQuoteFormSettings/{id}', ['uses' => 'CleaningGeneralQuoteFormSettingsController@saveGeneralQuoteFormSettings'])->name('generalQuoteFormSettings.saveGeneralQuoteFormSettings');
                    Route::resource('generalQuoteFormSettings', 'CleaningGeneralQuoteFormSettingsController');
                    
                    Route::get('leaseQuoteFormSettings', ['uses' => 'CleaningLeaseQuoteFormSettingsController@index'])->name('leaseQuoteFormSettings.index');
                    Route::get('leaseQuoteFormSettings/data', ['uses' => 'CleaningLeaseQuoteFormSettingsController@data'])->name('leaseQuoteFormSettings.data');
                    Route::post('saveLeaseQuoteFormSettings/{id}', ['uses' => 'CleaningLeaseQuoteFormSettingsController@saveLeaseQuoteFormSettings'])->name('leaseQuoteFormSettings.saveLeaseQuoteFormSettings');
                    
                    
                    Route::get('jobsCleaningPricing', ['uses' => 'ManageJobsCleaningPricingController@index'])->name('jobs-cleaning-pricing.index');Route::post('ajaxUpdatejobsCleaningPricing', ['uses' => 'ManageJobsCleaningPricingController@ajaxUpdatejobsCleaningPricing'])->name('jobsCleaningPricing.ajaxUpdatejobsCleaningPricing');
                    Route::post('ajaxCreateJobsCleaningPricing', ['uses' => 'ManageJobsCleaningPricingController@ajaxCreateJobsCleaningPricing'])->name('jobsCleaningPricing.ajaxCreateJobsCleaningPricing');
                    Route::post('ajaxUpdateJobsCleaningPricing', ['uses' => 'ManageJobsCleaningPricingController@ajaxUpdatejobsCleaningPricing'])->name('jobsCleaningPricing.ajaxUpdatejobsCleaningPricing');
                    Route::post('ajaxDestroyInventorDefinition', ['uses' => 'ManageJobsCleaningPricingController@ajaxDestroyInventorDefinition'])->name('jobsCleaningPricing.ajaxDestroyInventorDefinition');
                    Route::resource('JobsCleaningPricing', 'ManageJobsCleaningPricingController');

                    Route::get('enableCleaningAutoQuote', ['uses' => 'ManageCleaningAutoQuoteController@index'])->name('auto-quote.index');
                    Route::post('saveAutoQuoteData/{id}', ['uses' => 'ManageCleaningAutoQuoteController@saveAutoQuoteData'])->name('enableCleaningAutoQuote.saveAutoQuoteData');
                    Route::post('createAutoQuoteData/{id}', ['uses' => 'ManageCleaningAutoQuoteController@createAutoQuoteData'])->name('enableCleaningAutoQuote.createAutoQuoteData');
                    Route::resource('enableCleaningAutoQuote', 'ManageCleaningAutoQuoteController');

                    //Cleaning Shift
                    Route::get('cleaning-shifts', ['uses' => 'CleaningLeaseQuoteFormSettingsController@cleaningShifts'])->name('cleaningShifts');
                    Route::post('ajaxLoadCleaningShifts', ['uses' => 'CleaningLeaseQuoteFormSettingsController@ajaxLoadCleaningShifts'])->name('ajaxLoadCleaningShifts');
                    Route::post('ajaxSaveCleaningShifts', ['uses' => 'CleaningLeaseQuoteFormSettingsController@ajaxSaveCleaningShifts'])->name('ajaxSaveCleaningShifts');
                    Route::post('ajaxUpdateCleaningShifts', ['uses' => 'CleaningLeaseQuoteFormSettingsController@ajaxUpdateCleaningShifts'])->name('ajaxUpdateCleaningShifts');
                    Route::post('ajaxDestroyCleaningShifts', ['uses' => 'CleaningLeaseQuoteFormSettingsController@ajaxDestroyCleaningShifts'])->name('ajaxDestroyCleaningShifts');
                    //Cleaning Teams
                    Route::get('cleaning-teams', ['uses' => 'CleaningLeaseQuoteFormSettingsController@cleaningTeams'])->name('cleaningTeams');
                    Route::post('ajaxLoadCleaningTeams', ['uses' => 'CleaningLeaseQuoteFormSettingsController@ajaxLoadCleaningTeams'])->name('ajaxLoadCleaningTeams');
                    Route::post('ajaxSaveCleaningTeams', ['uses' => 'CleaningLeaseQuoteFormSettingsController@ajaxSaveCleaningTeams'])->name('ajaxSaveCleaningTeams');
                    Route::post('ajaxUpdateCleaningTeams', ['uses' => 'CleaningLeaseQuoteFormSettingsController@ajaxUpdateCleaningTeams'])->name('ajaxUpdateCleaningTeams');
                    Route::post('ajaxDestroyCleaningTeams', ['uses' => 'CleaningLeaseQuoteFormSettingsController@ajaxDestroyCleaningTeams'])->name('ajaxDestroyCleaningTeams');
                    //Cleaning Team Members
                    Route::get('cleaning-team-members', ['uses' => 'CleaningLeaseQuoteFormSettingsController@cleaningTeamMembers'])->name('cleaningTeamMembers');
                    Route::post('ajaxLoadCleaningTeamMembers', ['uses' => 'CleaningLeaseQuoteFormSettingsController@ajaxLoadCleaningTeamMembers'])->name('ajaxLoadCleaningTeamMembers');
                    Route::post('ajaxSaveCleaningTeamMembers', ['uses' => 'CleaningLeaseQuoteFormSettingsController@ajaxSaveCleaningTeamMembers'])->name('ajaxSaveCleaningTeamMembers');
                    Route::post('ajaxUpdateCleaningTeamMembers', ['uses' => 'CleaningLeaseQuoteFormSettingsController@ajaxUpdateCleaningTeamMembers'])->name('ajaxUpdateCleaningTeamMembers');
                    Route::post('ajaxDestroyCleaningTeamMembers', ['uses' => 'CleaningLeaseQuoteFormSettingsController@ajaxDestroyCleaningTeamMembers'])->name('ajaxDestroyCleaningTeamMembers');

                    
                    Route::resource('leaseQuoteFormSettings', 'CleaningLeaseQuoteFormSettingsController');
                }
            );
            Route::group(
                ['prefix' => 'cleaning', 'middleware' => ['access.module:2']],
                function () {
                    Route::get('list-jobs', ['uses' => 'ListJobsCleaningController@index'])->name('list-jobs-cleaning.index');
                    Route::get('list-jobs/data', ['uses' => 'ListJobsCleaningController@data'])->name('list-jobs-cleaning.data');
                    Route::get('view-job/{id}', ['uses' => 'ListJobsCleaningController@viewJob'])->name('list-jobs-cleaning.view-job');
                    Route::post('ajaxUpdateJobDetail', ['uses' => 'ListJobsCleaningController@ajaxUpdateJobDetail'])->name('list-jobs-cleaning.ajaxUpdateJobDetail');
                    Route::post('ajaxUpdateTeamRoaster', ['uses' => 'ListJobsCleaningController@ajaxUpdateTeamRoaster'])->name('list-jobs-cleaning.ajaxUpdateTeamRoaster');
                    Route::post('ajaxNotifyTeamLead', ['uses' => 'ListJobsCleaningController@ajaxNotifyTeamLead'])->name('list-jobs-cleaning.ajaxNotifyTeamLead');
                    Route::post('sendPushNotification', ['uses' => 'ListJobsCleaningController@sendPushNotification'])->name('list-jobs-cleaning.sendPushNotification');
                    Route::post('ajaxReassignTeam', ['uses' => 'ListJobsCleaningController@ajaxReassignTeam'])->name('list-jobs-cleaning.ajaxReassignTeam');
                    Route::post('ajaxUpdateAdditionalInfo', ['uses' => 'ListJobsCleaningController@ajaxUpdateAdditionalInfo'])->name('list-jobs-cleaning.ajaxUpdateAdditionalInfo');

                    Route::get('team-calendar', ['uses' => 'ListJobsCleaningController@teamCalendar'])->name('list-jobs-cleaning.team-calendar');
                    Route::get('calendar-resources', ['uses' => 'ListJobsCleaningController@getTeams'])->name('list-jobs-cleaning.calendar-resources');
                    Route::get('calendar-events', ['uses' => 'ListJobsCleaningController@getJobs'])->name('list-jobs-cleaning.calendar-events');

                    Route::get('team-roster', ['uses' => 'TeamRosterController@index'])->name('team-roster-cleaning.index');
                    Route::get('team-roster/data', ['uses' => 'TeamRosterController@data'])->name('team-roster-cleaning.data');
                    /*Route::get('updateScheduleEvent', ['uses' => 'ListJobsCleaningController@updateScheduleEvent'])->name('list-jobs-cleaning.updateScheduleEvent');*/                    
                }
            );

            Route::group(
                ['prefix' => 'peopleoperations'],
                function () {
                    Route::get('list-employees', ['uses' => 'PeopleOperationsController@index'])->name('list-employees.index');
                    Route::get('list-employees/data', ['uses' => 'PeopleOperationsController@data'])->name('list-employees.data');
                    /*Route::get('edit-employee/{id}', ['uses' => 'PeopleOperationsController@editEmployee'])->name('edit-employee');*/

                    Route::resource('list-employees', 'PeopleOperationsController');                    
                    Route::get('manage-roles', ['uses' => 'RolesAndPermissionController@manageRoles'])->name('manage-roles');
                    Route::post('ajax-create-role', ['uses' => 'RolesAndPermissionController@ajaxCreateRole'])->name('ajax-create-role');
                    Route::post('ajax-destroy-role', ['uses' => 'RolesAndPermissionController@ajaxDestroyRole'])->name('ajax-destroy-role');
                    Route::post('ajax-update-role', ['uses' => 'RolesAndPermissionController@ajaxUpdateRole'])->name('ajax-update-role');
                    Route::post('ajax-update-role-permissions', ['uses' => 'RolesAndPermissionController@updateRolePermissions'])->name('ajax-update-role-permissions');
                    Route::get('role-permissions/{id}', ['uses' => 'RolesAndPermissionController@rolePermissions'])->name('role-permissions');
                }

            );
            Route::group(
                ['prefix' => 'employees'],
                function () {
                    Route::get('employees/free-employees', ['uses' => 'ManageEmployeesController@freeEmployees'])->name('employees.freeEmployees');
                    Route::get('employees/docs-create/{id}', ['uses' => 'ManageEmployeesController@docsCreate'])->name('employees.docs-create');
                    Route::get('employees/tasks/{userId}/{hideCompleted}', ['uses' => 'ManageEmployeesController@tasks'])->name('employees.tasks');
                    Route::get('employees/time-logs/{userId}', ['uses' => 'ManageEmployeesController@timeLogs'])->name('employees.time-logs');
                    Route::get('employees/data', ['uses' => 'ManageEmployeesController@data'])->name('employees.data');
                    Route::get('employees/export/{status?}/{employee?}/{role?}', ['uses' => 'ManageEmployeesController@export'])->name('employees.export');
                    Route::post('employees/assignRole', ['uses' => 'ManageEmployeesController@assignRole'])->name('employees.assignRole');
                    Route::post('employees/assignProjectAdmin', ['uses' => 'ManageEmployeesController@assignProjectAdmin'])->name('employees.assignProjectAdmin');
                    Route::resource('employees', 'ManageEmployeesController');

                    Route::resource('teams', 'ManageTeamsController');
                    Route::resource('employee-teams', 'ManageEmployeeTeamsController');
                    Route::get('employee-docs/download/{id}', ['uses' => 'EmployeeDocsController@download'])->name('employee-docs.download');
                    Route::resource('employee-docs', 'EmployeeDocsController');
                }
            );
            Route::group(
                ['prefix' => 'crm'],
                function () {
                    Route::get('crm-leads/data', ['uses' => 'CRMLeadsController@index'])->name('crm-leads.data');
                    Route::get('crm-leads/residential', ['uses' => 'CRMLeadsController@residential'])->name('crm-leads.residential');
                    Route::get('crm-leads/commercial', ['uses' => 'CRMLeadsController@commercial'])->name('crm-leads.commercial');
                    Route::get('view-opportunity/{id}/{opportunity_id?}', ['uses' => 'CRMLeadsController@view'])->name('crm-leads.view');
                    Route::get('view-customer-leads/{id}', ['uses' => 'CRMLeadsController@viewCustomerLeads'])->name('crm-leads.view-customer-leads');
                    Route::get('ajaxGetCustomerOpportunityData', ['uses' => 'CRMLeadsController@ajaxGetCustomerOpportunityData'])->name('crm-leads.ajaxGetCustomerOpportunityData');
                    Route::get('ajaxGetCustomerJobData', ['uses' => 'CRMLeadsController@ajaxGetCustomerJobData'])->name('crm-leads.ajaxGetCustomerJobData');
                    Route::post('ajax-save-customer-detail', ['uses' => 'CRMLeadsController@ajaxSaveCustomerDetail'])->name('crm-leads.ajax-save-customer-detail');
                    Route::get('crm-leads/residentialData', ['uses' => 'CRMLeadsController@residentialData'])->name('crm-leads.residentialData');
                    Route::get('crm-leads/commercialData', ['uses' => 'CRMLeadsController@commercialData'])->name('crm-leads.commercialData');
                    Route::get('crm-leads/export', ['uses' => 'CRMLeadsController@export'])->name('crm-leads.export');
                    Route::post('crm-leads/ajaxStoreLead', ['uses' => 'CRMLeadsController@ajaxStore'])->name('crm-leads.ajaxStoreLead');
                    Route::post('crm-leads/ajaxFindLeads', ['uses' => 'CRMLeadsController@ajaxFindLeads'])->name('crm-leads.ajaxFindLeads');
                    Route::post('crm-leads/ajaxFindLeadsByNumber', ['uses' => 'CRMLeadsController@ajaxFindLeadsByNumber'])->name('crm-leads.ajaxFindLeadsByNumber');
                    Route::get('crm-leads/leads-popup-grid', ['uses' => 'CRMLeadsController@ajaxLeadsGrid'])->name('crm-leads.leads-popup-grid');
                    Route::post('crm-leads/ajaxUpdateLeadStatus', ['uses' => 'CRMLeadsController@ajaxUpdateLeadStatus'])->name('crm-leads.ajaxUpdateLeadStatus');
                    //Task section
                    Route::post('crm-leads/ajaxStoreTask', ['uses' => 'CRMLeadsController@ajaxStoreTask'])->name('crm-leads.ajaxStoreTask');
                    Route::post('crm-leads/ajaxUpdateTask', ['uses' => 'CRMLeadsController@ajaxUpdateTask'])->name('crm-leads.ajaxUpdateTask');
                    Route::post('crm-leads/ajaxDestroyTask', ['uses' => 'CRMLeadsController@ajaxDestroyTask'])->name('crm-leads.ajaxDestroyTask');
                    //Opportunity section
                    Route::post('crm-leads/ajaxStoreOpportunity', ['uses' => 'CRMLeadsController@ajaxStoreOpportunity'])->name('crm-leads.ajaxStoreOpportunity');
                    Route::post('crm-leads/ajaxUpdateOpportunity', ['uses' => 'CRMLeadsController@ajaxUpdateOpportunity'])->name('crm-leads.ajaxUpdateOpportunity');
                    Route::post('crm-leads/ajaxDestroyOpportunity', ['uses' => 'CRMLeadsController@ajaxDestroyOpportunity'])->name('crm-leads.ajaxDestroyOpportunity');
                    //Contact section
                    Route::post('crm-leads/ajaxStoreContact', ['uses' => 'CRMLeadsController@ajaxStoreContact'])->name('crm-leads.ajaxStoreContact');
                    Route::post('crm-leads/ajaxUpdateContact', ['uses' => 'CRMLeadsController@ajaxUpdateContact'])->name('crm-leads.ajaxUpdateContact');
                    Route::post('crm-leads/ajaxDestroyContact', ['uses' => 'CRMLeadsController@ajaxDestroyContact'])->name('crm-leads.ajaxDestroyContact');
                    //Activity Note section
                    Route::post('crm-leads/ajaxStoreNote', ['uses' => 'CRMLeadsController@ajaxStoreNote'])->name('crm-leads.ajaxStoreNote');
                    Route::post('crm-leads/ajaxUpdateNote', ['uses' => 'CRMLeadsController@ajaxUpdateNote'])->name('crm-leads.ajaxUpdateNote');
                    Route::post('crm-leads/ajaxDestroyNote', ['uses' => 'CRMLeadsController@ajaxDestroyNote'])->name('crm-leads.ajaxDestroyNote');
                    Route::get('crm-leads/getActivitiesForCustom', ['uses' => 'CRMLeadsController@getActivitiesForCustom'])->name('crm-leads.getActivitiesForCustom');
                    //Activity SMS section
                    Route::post('crm-leads/getSmsTemplate/{id}', ['uses' => 'CRMLeadsController@getSmsTemplate'])->name('crm-leads.getSmsTemplate');
                    Route::post('crm-leads/ajaxSendSms', ['uses' => 'CRMLeadsController@ajaxSendSms'])->name('crm-leads.ajaxSendSms');
                    //Activity Email section
                    Route::post('crm-leads/ajaxFindContactEmail', ['uses' => 'CRMLeadsController@ajaxFindContactEmail'])->name('crm-leads.ajaxFindContactEmail');
                    Route::post('crm-leads/getEmailTemplate/{id}', ['uses' => 'CRMLeadsController@getEmailTemplate'])->name('crm-leads.getEmailTemplate');
                    Route::post('crm-leads/ajaxSendEmail', ['uses' => 'CRMLeadsController@ajaxSendEmail'])->name('crm-leads.ajaxSendEmail');
                    //Estimate Section
                    Route::post('crm-leads/ajaxSaveEstimate', ['uses' => 'CRMLeadsController@ajaxSaveEstimate'])->name('crm-leads.ajaxSaveEstimate');
                    Route::post('crm-leads/ajaxUpdateEstimate', ['uses' => 'CRMLeadsController@ajaxUpdateEstimate'])->name('crm-leads.ajaxUpdateEstimate');
                    Route::post('crm-leads/ajaxLoadQuoteItem', ['uses' => 'CRMLeadsController@ajaxLoadQuoteItem'])->name('crm-leads.ajaxLoadQuoteItem');
                    Route::post('crm-leads/ajaxDestroyQuoteItem', ['uses' => 'CRMLeadsController@ajaxDestroyQuoteItem'])->name('crm-leads.ajaxDestroyQuoteItem');
                    Route::get('crm-leads/generateQuote/{id}', ['uses' => 'CRMLeadsController@generateQuote'])->name('crm-leads.generateQuote');
                    Route::get('crm-leads/downloadQuote/{id}', ['uses' => 'CRMLeadsController@downloadQuote'])->name('crm-leads.downloadQuote');
                    Route::get('crm-leads/generate-insurance-quote/{id}', ['uses' => 'CRMLeadsController@generateInsuranceQuote'])->name('crm-leads.generate-insurance-quote');
                    Route::get('crm-leads/download-insurance-quote/{id}', ['uses' => 'CRMLeadsController@downloadInsuranceQuote'])->name('crm-leads.download-insurance-quote');
                    Route::post('crm-leads/ajaxSaveEstimateDiscount', ['uses' => 'CRMLeadsController@ajaxSaveEstimateDiscount'])->name('crm-leads.ajaxSaveEstimateDiscount');
                    Route::post('crm-leads/ajaxSaveDepositRequired', ['uses' => 'CRMLeadsController@ajaxSaveDepositRequired'])->name('crm-leads.ajaxSaveDepositRequired');
                    Route::post('crm-leads/ajaxSetProductDescParameter', ['uses' => 'CRMLeadsController@ajaxSetProductDescParameter'])->name('crm-leads.ajaxSetProductDescParameter');
                    
                    // Removal Section                    
                    Route::post('crm-leads/ajaxLoadJobDetail', ['uses' => 'CRMLeadsController@ajaxLoadJobDetail'])->name('crm-leads.ajaxLoadJobDetail');
                    Route::post('crm-leads/ajaxLoadCleaningJobDetail', ['uses' => 'CRMLeadsController@ajaxLoadCleaningJobDetail'])->name('crm-leads.ajaxLoadCleaningJobDetail');
                    
                    Route::post('crm-leads/ajaxUpdateRemovalBookingDetail', ['uses' => 'CRMLeadsController@ajaxUpdateRemovalBookingDetail'])->name('crm-leads.ajaxUpdateRemovalBookingDetail');
                    Route::post('crm-leads/ajaxUpdateRemovalPropertyDetail', ['uses' => 'CRMLeadsController@ajaxUpdateRemovalPropertyDetail'])->name('crm-leads.ajaxUpdateRemovalPropertyDetail');
                    Route::post('crm-leads/ajaxUpdateRemovalMovingFrom', ['uses' => 'CRMLeadsController@ajaxUpdateRemovalMovingFrom'])->name('crm-leads.ajaxUpdateRemovalMovingFrom');
                    Route::post('crm-leads/ajaxUpdateRemovalMovingTo', ['uses' => 'CRMLeadsController@ajaxUpdateRemovalMovingTo'])->name('crm-leads.ajaxUpdateRemovalMovingTo');
                    // Cleaning Section 
                    Route::post('crm-leads/ajaxUpdateCleaningEndOfLease', ['uses' => 'CRMLeadsController@ajaxUpdateCleaningEndOfLease'])->name('crm-leads.ajaxUpdateCleaningEndOfLease');
                    Route::post('crm-leads/ajaxUpdateCleaningQuestion', ['uses' => 'CRMLeadsController@ajaxUpdateCleaningQuestion'])->name('crm-leads.ajaxUpdateCleaningQuestion');                    

                    Route::post('crm-leads/ajaxUpdateRemovalTop', ['uses' => 'CRMLeadsController@ajaxUpdateRemovalTop'])->name('crm-leads.ajaxUpdateRemovalTop');
                    Route::post('crm-leads/ajaxUpdateRemovalMiddleLeft', ['uses' => 'CRMLeadsController@ajaxUpdateRemovalMiddleLeft'])->name('crm-leads.ajaxUpdateRemovalMiddleLeft');
                    Route::post('crm-leads/ajaxUpdateRemovalMiddleRight', ['uses' => 'CRMLeadsController@ajaxUpdateRemovalMiddleRight'])->name('crm-leads.ajaxUpdateRemovalMiddleRight');
                    Route::post('crm-leads/ajaxUpdateRemovalBottomLeft', ['uses' => 'CRMLeadsController@ajaxUpdateRemovalBottomLeft'])->name('crm-leads.ajaxUpdateRemovalBottomLeft');
                    Route::post('crm-leads/ajaxUpdateRemovalBottomRight', ['uses' => 'CRMLeadsController@ajaxUpdateRemovalBottomRight'])->name('crm-leads.ajaxUpdateRemovalBottomRight');
                    Route::post('crm-leads/ajaxRemovalsConfirmBooking', ['uses' => 'CRMLeadsController@ajaxRemovalsConfirmBooking'])->name('crm-leads.ajaxRemovalsConfirmBooking');
                    //Email and notes attachment
                    Route::post('crm-leads/uploadActivityAttachment', ['uses' => 'CRMLeadsController@uploadActivityAttachment'])->name('crm-leads.uploadActivityAttachment');
                    Route::post('crm-leads/removeActivityAttachment', ['uses' => 'CRMLeadsController@removeActivityAttachment'])->name('crm-leads.removeActivityAttachment');
                    Route::get('crm-leads/viewActivityAttachment/{id}', ['uses' => 'CRMLeadsController@viewActivityAttachment'])->name('crm-leads.viewActivityAttachment');
                    //Storage Section
                    
                    Route::post('crm-leads/storageTabContent', ['uses' => 'CRMLeadsController@storageTabContent'])->name('crm-leads.storageTabContent');

                    //Copy Job And Opportunity Routes
                    Route::get('crm-leads/ajaxJobPopupData', ['uses' => 'CRMLeadsController@ajaxJobPopupData'])->name('crm-leads.ajaxJobPopupData');
                    Route::get('crm-leads/ajaxOpportunityPopupData', ['uses' => 'CRMLeadsController@ajaxOpportunityPopupData'])->name('crm-leads.ajaxOpportunityPopupData');

                    Route::post('crm-leads/ajaxSaveJob', ['uses' => 'CRMLeadsController@ajaxSaveJob'])->name('crm-leads.ajaxSaveJob');
                    Route::post('crm-leads/ajaxSaveOpportunity', ['uses' => 'CRMLeadsController@ajaxSaveOpportunity'])->name('crm-leads.ajaxSaveOpportunity');

                    Route::resource('crm-leads', 'CRMLeadsController');
                }
            );
            Route::group(
                ['prefix' => 'report'],
                function () {
                    // Sales Report Routes
                    Route::get('sales-report', ['uses' => 'ReportController@index'])->name('sales-report');
                    Route::post('get-sales-data', ['uses' => 'ReportController@getdata'])->name('sales-report.getdata');


                    //Operations Report
                    Route::get('crm-operations-report', ['uses' => 'ReportController@operationsReport'])->name('crm-operations-report');
                    Route::get('crm-operations-get-data', ['uses' => 'ReportController@operationsData'])->name('crm-operations-get-data');

                     //Lead Report
                     Route::get('crm-lead-report', ['uses' => 'ReportController@leadReport'])->name('crm-lead-report');
                     Route::get('crm-lead-get-data', ['uses' => 'ReportController@leadData'])->name('crm-lead-get-data');

                     //Daily Vehicle Check
                     Route::get('/crm-daily-vehicle-check', ['uses' => 'ReportController@dailyVehicleCheck'])->name('crm-daily-vehicle-check');
                     Route::get('/daily-vehicle-checklist-get-data', ['uses' => 'ReportController@getChecklistData'])->name('crm-checklist-data');
                     Route::get('/crm-daily-check-popup-data', ['uses' => 'ReportController@getPopupData'])->name('crm-daily-check-popup-data');
                }
            );
            Route::group(
                ['prefix' => 'opportunity'],
                function () {
                    Route::get('pipeline', ['uses' => 'OpportunityController@pipeline'])->name('opportunity.pipeline');
                    Route::post('movestatus', ['uses' => 'OpportunityController@movestatus'])->name('opportunity.movestatus');
                    Route::get('listdata', ['uses' => 'OpportunityController@listdata'])->name('opportunity.listdata');
                    Route::get('opportunity/data', ['uses' => 'OpportunityController@data'])->name('opportunity.data');
                }
            );
            Route::get('projects/archive-data', ['uses' => 'ManageProjectsController@archiveData'])->name('projects.archive-data');
            Route::get('projects/archive', ['uses' => 'ManageProjectsController@archive'])->name('projects.archive');
            Route::get('projects/archive-restore/{id?}', ['uses' => 'ManageProjectsController@archiveRestore'])->name('projects.archive-restore');
            Route::get('projects/archive-delete/{id?}', ['uses' => 'ManageProjectsController@archiveDestroy'])->name('projects.archive-delete');
            Route::get('projects/export/{status?}/{clientID?}', ['uses' => 'ManageProjectsController@export'])->name('projects.export');
            Route::get('projects/data', ['uses' => 'ManageProjectsController@data'])->name('projects.data');
            Route::get('projects/ganttData', ['uses' => 'ManageProjectsController@ganttData'])->name('projects.ganttData');
            Route::get('projects/gantt', ['uses' => 'ManageProjectsController@gantt'])->name('projects.gantt');
            Route::post('projects/updateStatus/{id}', ['uses' => 'ManageProjectsController@updateStatus'])->name('projects.updateStatus');
            Route::resource('projects', 'ManageProjectsController');
            Route::get('project-template/data', ['uses' => 'ProjectTemplateController@data'])->name('project-template.data');
            Route::resource('project-template', 'ProjectTemplateController');
            Route::post('project-template-members/save-group', ['uses' => 'ProjectMemberTemplateController@storeGroup'])->name('project-template-members.storeGroup');
            Route::resource('project-template-member', 'ProjectMemberTemplateController');
            Route::resource('project-template-task', 'ProjectTemplateTaskController');
            Route::post('projectCategory/store-cat', ['uses' => 'ManageProjectCategoryController@storeCat'])->name('projectCategory.store-cat');
            Route::get('projectCategory/create-cat', ['uses' => 'ManageProjectCategoryController@createCat'])->name('projectCategory.create-cat');
            Route::resource('projectCategory', 'ManageProjectCategoryController');
            Route::post('taskCategory/store-cat', ['uses' => 'ManageTaskCategoryController@storeCat'])->name('taskCategory.store-cat');
            Route::get('taskCategory/create-cat', ['uses' => 'ManageTaskCategoryController@createCat'])->name('taskCategory.create-cat');
            Route::resource('taskCategory', 'ManageTaskCategoryController');
            Route::get('notices/data', ['uses' => 'ManageNoticesController@data'])->name('notices.data');
            Route::get('notices/export/{startDate}/{endDate}', ['uses' => 'ManageNoticesController@export'])->name('notices.export');
            Route::resource('notices', 'ManageNoticesController');
            Route::get('settings/change-language', ['uses' => 'OrganisationSettingsController@changeLanguage'])->name('settings.change-language');
            Route::resource('settings', 'OrganisationSettingsController', ['only' => ['edit', 'update', 'index', 'change-language']]);
            Route::group(
                ['prefix' => 'settings'],
                function () {
                    Route::get('email-settings/sent-test-email', ['uses' => 'EmailNotificationSettingController@sendTestEmail'])->name('email-settings.sendTestEmail');
                    Route::post('email-settings/updateMailConfig', ['uses' => 'EmailNotificationSettingController@updateMailConfig'])->name('email-settings.updateMailConfig');
                    Route::resource('email-settings', 'EmailNotificationSettingController');
                    Route::resource('profile-settings', 'AdminProfileSettingsController');
                    Route::get('currency/exchange-key', ['uses' => 'CurrencySettingController@currencyExchangeKey'])->name('currency.exchange-key');
                    Route::post('currency/exchange-key-store', ['uses' => 'CurrencySettingController@currencyExchangeKeyStore'])->name('currency.exchange-key-store');
                    Route::resource('currency', 'CurrencySettingController');
                    Route::get('currency/exchange-rate/{currency}', ['uses' => 'CurrencySettingController@exchangeRate'])->name('currency.exchange-rate');
                    Route::get('currency/update/exchange-rates', ['uses' => 'CurrencySettingController@updateExchangeRate'])->name('currency.update-exchange-rates');
                    Route::resource('currency', 'CurrencySettingController');
                    Route::post('theme-settings/activeTheme', ['uses' => 'ThemeSettingsController@activeTheme'])->name('theme-settings.activeTheme');
                    Route::resource('theme-settings', 'ThemeSettingsController');
                    // Log time
                    Route::resource('log-time-settings', 'LogTimeSettingsController');
                    Route::resource('task-settings', 'TaskSettingsController', ['only' => ['index', 'store']]);
                    Route::resource('payment-gateway-credential', 'PaymentGatewayCredentialController');
                    // Route::resource('invoice-settings', 'InvoiceSettingController');
                    Route::get('slack-settings/sendTestNotification', ['uses' => 'SlackSettingController@sendTestNotification'])->name('slack-settings.sendTestNotification');
                    Route::post('slack-settings/updateSlackNotification/{id}', ['uses' => 'SlackSettingController@updateSlackNotification'])->name('slack-settings.updateSlackNotification');
                    Route::resource('slack-settings', 'SlackSettingController');
                    Route::get('push-notification-settings/sendTestNotification', ['uses' => 'PushNotificationController@sendTestNotification'])->name('push-notification-settings.sendTestNotification');
                    Route::post('push-notification-settings/updatePushNotification/{id}', ['uses' => 'PushNotificationController@updatePushNotification'])->name('push-notification-settings.updatePushNotification');
                    Route::resource('push-notification-settings', 'PushNotificationController');
                    Route::post('update-settings/deleteFile', ['uses' => 'UpdateDatabaseController@deleteFile'])->name('update-settings.deleteFile');
                    Route::get('update-settings/install', ['uses' => 'UpdateDatabaseController@install'])->name('update-settings.install');
                    Route::get('update-settings/manual-update', ['uses' => 'UpdateDatabaseController@manual'])->name('update-settings.manual');
                    Route::resource('update-settings', 'UpdateDatabaseController');
                    Route::post('ticket-agents/update-group/{id}', ['uses' => 'TicketAgentsController@updateGroup'])->name('ticket-agents.update-group');
                    Route::resource('ticket-agents', 'TicketAgentsController');
                    Route::resource('ticket-groups', 'TicketGroupsController');
                    Route::get('ticketTypes/createModal', ['uses' => 'TicketTypesController@createModal'])->name('ticketTypes.createModal');
                    Route::resource('ticketTypes', 'TicketTypesController');
                    Route::get('lead-source-settings/createModal', ['uses' => 'LeadSourceSettingController@createModal'])->name('leadSetting.createModal');
                    Route::resource('lead-source-settings', 'LeadSourceSettingController');
                    Route::get('lead-status-settings/createModal', ['uses' => 'LeadStatusSettingController@createModal'])->name('leadSetting.createModal');
                    Route::resource('lead-status-settings', 'LeadStatusSettingController');
                    Route::get('offline-payment-setting/createModal', ['uses' => 'OfflinePaymentSettingController@createModal'])->name('offline-payment-setting.createModal');
                    Route::resource('offline-payment-setting', 'OfflinePaymentSettingController');
                    Route::get('ticketChannels/createModal', ['uses' => 'TicketChannelsController@createModal'])->name('ticketChannels.createModal');
                    Route::resource('ticketChannels', 'TicketChannelsController');
                    Route::post('replyTemplates/fetch-template', ['uses' => 'TicketReplyTemplatesController@fetchTemplate'])->name('replyTemplates.fetchTemplate');
                    Route::resource('replyTemplates', 'TicketReplyTemplatesController');
                    Route::resource('attendance-settings', 'AttendanceSettingController');
                    Route::resource('leaves-settings', 'LeavesSettingController');
                    Route::get('data', ['uses' => 'AdminCustomFieldsController@getFields'])->name('custom-fields.data');
                    Route::resource('custom-fields', 'AdminCustomFieldsController');
                    // Message settings
                    Route::resource('message-settings', 'MessageSettingsController');
                    // Storage settings
                    Route::resource('storage-settings', 'StorageSettingsController');
                    // Storage settings
                    Route::resource('language-settings', 'LanguageSettingsController');
                    // Module settings
                    Route::resource('module-settings', 'ModuleSettingsController');
                    Route::resource('sms-credits', 'SMSCreditsController');

                    Route::post('buy-sms-credits', ['uses' => 'SMSCreditsController@buyCredits'])->name('buy-sms-credits');
                    Route::get('connect-stripe/response', 'ConnectStripeController@responseConnectStripe');
                    Route::resource('connect-stripe', 'ConnectStripeController');

                    Route::get('connect-xero/callback', 'ConnectXeroController@handleCallbackFromXero');
                    Route::get('connect-xero/authorize', 'ConnectXeroController@redirectUserToXero')->name('connect-xero.authorize');
                    Route::post('connect-xero/storeConfig', 'ConnectXeroController@storeConfig')->name('connect-xero.storeConfig');
                    Route::get('connect-xero/syncInvoice', 'ConnectXeroController@syncInvoice')->name('connect-xero.syncInvoice');
                    Route::get('connect-xero/disconnect', 'ConnectXeroController@disconnect')->name('connect-xero.disconnect');
                    Route::resource('connect-xero', 'ConnectXeroController');

                    Route::get('connect-myob/callback', 'ConnectMyobController@handleCallbackFromMyob');
                    Route::get('connect-myob/authorize', 'ConnectMyobController@redirectUserToMyob')->name('connect-myob.authorize');
                    Route::post('connect-myob/storeCompanyDetail', 'ConnectMyobController@storeCompanyDetail')->name('connect-myob.storeCompanyDetail');
                    Route::post('connect-myob/storeConfig', 'ConnectMyobController@storeConfig')->name('connect-myob.storeConfig');
                    Route::get('connect-myob/syncInvoice', 'ConnectMyobController@syncInvoice')->name('connect-myob.syncInvoice');
                    Route::get('connect-myob/disconnect', 'ConnectMyobController@disconnect')->name('connect-myob.disconnect');
                    Route::resource('connect-myob', 'ConnectMyobController');

                    Route::post('coverfreight/disconnect', 'CoverFreightController@disconnect')->name('coverfreight.disconnect');
                    Route::resource('coverfreight', 'CoverFreightController');
                    
                    //Configure Email
                    Route::post('configure-email/configureDomain', 'AdminConfigureEmailController@configureDomain');
                    Route::post('configure-email/updateEmail', 'AdminConfigureEmailController@updateEmail');
                    Route::post('configure-email/verifyPostMarkDKIM', 'AdminConfigureEmailController@verifyPostMarkDKIM');
                    Route::post('configure-email/verifyPostMarkReturnPath', 'AdminConfigureEmailController@verifyPostMarkReturnPath');
                    Route::resource('configure-email', 'AdminConfigureEmailController');

                    Route::get('list-type-options', ['uses' => 'ListTypeOptionsSettings@index'])->name('list-type-options.index');

                    Route::post('ajaxUpdateListType', ['uses' => 'ListTypeOptionsSettings@ajaxUpdateListType'])->name('list-type-options.ajaxUpdateListType');
                    Route::post('ajaxCreateListType', ['uses' => 'ListTypeOptionsSettings@ajaxCreateListType'])->name('list-type-options.ajaxCreateListType');
                    Route::post('ajaxDestroyListType', ['uses' => 'ListTypeOptionsSettings@ajaxDestroyListType'])->name('list-type-options.ajaxDestroyListType');

                    Route::post('ajaxLoadListOptions', ['uses' => 'ListTypeOptionsSettings@ajaxLoadListOptions'])->name('list-type-options.ajaxLoadListOptions');

                    
                    Route::post('ajaxCreateListOption', ['uses' => 'ListTypeOptionsSettings@ajaxCreateListOption'])->name('list-type-options.ajaxCreateListOption');
                    Route::post('ajaxUpdateListOption', ['uses' => 'ListTypeOptionsSettings@ajaxUpdateListOption'])->name('list-type-options.ajaxUpdateListOption');
                    Route::post('ajaxDestroyListOption', ['uses' => 'ListTypeOptionsSettings@ajaxDestroyListOption'])->name('list-type-options.ajaxDestroyListOption');

                    Route::post('ajaxGetListTypes', ['uses' => 'ListTypeOptionsSettings@ajaxGetListTypes'])->name('list-type-options.ajaxGetListTypes');

                    Route::resource('list-type-options', 'ListTypeOptionsSettings');

                    
                    Route::get('servicing-cities', ['uses' => 'ManageServicingCitiesController@servicingCities'])->name('servicing-cities');
                    Route::post('ajaxUpdateServicingCities', ['uses' => 'ManageServicingCitiesController@ajaxUpdateServicingCities'])->name('servicing-cities.ajaxUpdateServicingCities');
                    Route::post('ajaxCreateServicingCities', ['uses' => 'ManageServicingCitiesController@ajaxCreateServicingCities'])->name('servicing-cities.ajaxCreateServicingCities');
                    Route::post('ajaxDestroyServicingCities', ['uses' => 'ManageServicingCitiesController@ajaxDestroyServicingCities'])->name('servicing-cities.ajaxDestroyServicingCities');
                }
            );
            Route::group(
                ['prefix' => 'projects'],
                function () {
                    Route::post('project-members/save-group', ['uses' => 'ManageProjectMembersController@storeGroup'])->name('project-members.storeGroup');
                    Route::resource('project-members', 'ManageProjectMembersController');
                    Route::post('tasks/data/{startDate?}/{endDate?}/{hideCompleted?}/{projectId?}', ['uses' => 'ManageTasksController@data'])->name('tasks.data');
                    Route::get('tasks/export/{startDate?}/{endDate?}/{projectId?}/{hideCompleted?}', ['uses' => 'ManageTasksController@export'])->name('tasks.export');
                    Route::post('tasks/sort', ['uses' => 'ManageTasksController@sort'])->name('tasks.sort');
                    Route::post('tasks/change-status', ['uses' => 'ManageTasksController@changeStatus'])->name('tasks.changeStatus');
                    Route::get('tasks/check-task/{taskID}', ['uses' => 'ManageTasksController@checkTask'])->name('tasks.checkTask');
                    Route::resource('tasks', 'ManageTasksController');
                    Route::post('files/store-link', ['uses' => 'ManageProjectFilesController@storeLink'])->name('files.storeLink');
                    Route::get('files/download/{id}', ['uses' => 'ManageProjectFilesController@download'])->name('files.download');
                    Route::get('files/thumbnail', ['uses' => 'ManageProjectFilesController@thumbnailShow'])->name('files.thumbnail');
                    Route::resource('files', 'ManageProjectFilesController');
                    Route::get('invoices/download/{id}', ['uses' => 'ManageInvoicesController@download'])->name('invoices.download');
                    Route::get('invoices/create-invoice/{id}', ['uses' => 'ManageInvoicesController@createInvoice'])->name('invoices.createInvoice');
                    Route::resource('invoices', 'ManageInvoicesController');
                    Route::resource('issues', 'ManageIssuesController');
                    Route::post('time-logs/stop-timer/{id}', ['uses' => 'ManageTimeLogsController@stopTimer'])->name('time-logs.stopTimer');
                    Route::get('time-logs/data/{id}', ['uses' => 'ManageTimeLogsController@data'])->name('time-logs.data');
                    Route::resource('time-logs', 'ManageTimeLogsController');
                    Route::get('milestones/detail/{id}', ['uses' => 'ManageProjectMilestonesController@detail'])->name('milestones.detail');
                    Route::get('milestones/data/{id}', ['uses' => 'ManageProjectMilestonesController@data'])->name('milestones.data');
                    Route::resource('milestones', 'ManageProjectMilestonesController');
                }
            );
            Route::group(
                ['prefix' => 'clients'],
                function () {
                    Route::get('projects/{id}', ['uses' => 'ManageClientsController@showProjects'])->name('clients.projects');
                    Route::get('invoices/{id}', ['uses' => 'ManageClientsController@showInvoices'])->name('clients.invoices');
                    Route::post('store-customer', ['uses' => 'ManageClientsController@storeCustomer'])->name('clients.storeCustomer');
                    Route::post('update-customer/{id}', ['uses' => 'ManageClientsController@updateCustomer'])->name('clients.updateCustomer');
                    Route::get('contacts/data/{id}', ['uses' => 'ClientContactController@data'])->name('contacts.data');
                    Route::resource('contacts', 'ClientContactController');
                }
            );
            Route::get('all-issues/data', ['uses' => 'ManageAllIssuesController@data'])->name('all-issues.data');
            Route::resource('all-issues', 'ManageAllIssuesController');
            Route::get('all-time-logs/show-active-timer', ['uses' => 'ManageAllTimeLogController@showActiveTimer'])->name('all-time-logs.show-active-timer');
            Route::get('all-time-logs/members/{projectId}', ['uses' => 'ManageAllTimeLogController@membersList'])->name('all-time-logs.members');
            Route::get('all-time-logs/export/{startDate?}/{endDate?}/{projectId?}/{employee?}', ['uses' => 'ManageAllTimeLogController@export'])->name('all-time-logs.export');
            Route::post('all-time-logs/data/{startDate?}/{endDate?}/{projectId?}/{employee?}', ['uses' => 'ManageAllTimeLogController@data'])->name('all-time-logs.data');
            Route::post('all-time-logs/stop-timer/{id}', ['uses' => 'ManageAllTimeLogController@stopTimer'])->name('all-time-logs.stopTimer');
            Route::resource('all-time-logs', 'ManageAllTimeLogController');
            // task routes
            Route::resource('task', 'ManageAllTasksController', ['only' => ['edit', 'update', 'index']]); // hack to make left admin menu item active
            Route::group(
                ['prefix' => 'task'],
                function () {
                    Route::get('all-tasks/export/{startDate?}/{endDate?}/{projectId?}/{hideCompleted?}', ['uses' => 'ManageAllTasksController@export'])->name('all-tasks.export');
                    Route::post('all-tasks/data/{startDate?}/{endDate?}/{hideCompleted?}/{projectId?}', ['uses' => 'ManageAllTasksController@data'])->name('all-tasks.data');
                    Route::get('all-tasks/members/{projectId}', ['uses' => 'ManageAllTasksController@membersList'])->name('all-tasks.members');
                    Route::get('all-tasks/ajaxCreate/{columnId}', ['uses' => 'ManageAllTasksController@ajaxCreate'])->name('all-tasks.ajaxCreate');
                    Route::get('all-tasks/reminder/{taskid}', ['uses' => 'ManageAllTasksController@remindForTask'])->name('all-tasks.reminder');
                    Route::resource('all-tasks', 'ManageAllTasksController');
                    // taskboard resource
                    Route::post('taskboard/updateIndex', ['as' => 'taskboard.updateIndex', 'uses' => 'AdminTaskboardController@updateIndex']);
                    Route::resource('taskboard', 'AdminTaskboardController');
                    // task calendar routes
                    Route::resource('task-calendar', 'AdminCalendarController');
                }
            );
            Route::get('sticky-note/createStickyNote/{id}', ['uses' => 'ManageStickyNotesController@createStickyNote'])->name('sticky-note.createStickyNote');
            Route::resource('sticky-note', 'ManageStickyNotesController');
            Route::resource('reports', 'TaskReportController', ['only' => ['edit', 'update', 'index']]); // hack to make left admin menu item active
            Route::group(
                ['prefix' => 'reports'],
                function () {
                    Route::get('task-report/data/{startDate?}/{endDate?}/{employeeId?}/{projectId?}', ['uses' => 'TaskReportController@data'])->name('task-report.data');
                    Route::get('task-report/export/{startDate?}/{endDate?}/{employeeId?}/{projectId?}', ['uses' => 'TaskReportController@export'])->name('task-report.export');
                    Route::resource('task-report', 'TaskReportController');
                    Route::resource('time-log-report', 'TimeLogReportController');
                    Route::resource('finance-report', 'FinanceReportController');
                    Route::resource('income-expense-report', 'IncomeVsExpenseReportController');
                    //region Leave Report routes
                    Route::get('leave-report/data/{startDate?}/{endDate?}/{employeeId?}', ['uses' => 'LeaveReportController@data'])->name('leave-report.data');
                    Route::get('leave-report/export/{id?}/{startDate?}/{endDate?}', 'LeaveReportController@export')->name('leave-report.export');
                    Route::get('leave-report/pending-leaves/{id?}', 'LeaveReportController@pendingLeaves')->name('leave-report.pending-leaves');
                    Route::get('leave-report/upcoming-leaves/{id?}', 'LeaveReportController@upcomingLeaves')->name('leave-report.upcoming-leaves');
                    Route::resource('leave-report', 'LeaveReportController');
                    //endregion
                }
            );
            Route::resource('search', 'AdminSearchController');
            Route::resource('finance', 'ManageEstimatesController', ['only' => ['edit', 'update', 'index']]); // hack to make left admin menu item active
            Route::group(
                ['prefix' => 'finance'],
                function () {
                    // Estimate routes
                    Route::get('estimates/data', ['uses' => 'ManageEstimatesController@data'])->name('estimates.data');
                    Route::get('estimates/download/{id}', ['uses' => 'ManageEstimatesController@download'])->name('estimates.download');
                    Route::get('estimates/export/{startDate}/{endDate}/{status}', ['uses' => 'ManageEstimatesController@export'])->name('estimates.export');
                    Route::resource('estimates', 'ManageEstimatesController');
                    //Expenses routes
                    Route::get('expenses/data', ['uses' => 'ManageExpensesController@data'])->name('expenses.data');
                    Route::get('expenses/export/{startDate}/{endDate}/{status}/{employee}', ['uses' => 'ManageExpensesController@export'])->name('expenses.export');
                    Route::resource('expenses', 'ManageExpensesController');
                    // All invoices list routes
                    Route::post('file/store', ['uses' => 'ManageAllInvoicesController@storeFile'])->name('invoiceFile.store');
                    Route::delete('file/destroy', ['uses' => 'ManageAllInvoicesController@destroyFile'])->name('invoiceFile.destroy');
                    Route::get('all-invoices/data', ['uses' => 'ManageAllInvoicesController@data'])->name('all-invoices.data');
                    Route::get('all-invoices/download/{id}', ['uses' => 'ManageAllInvoicesController@download'])->name('all-invoices.download');
                    Route::get('all-invoices/export/{startDate}/{endDate}/{status}/{projectID}', ['uses' => 'ManageAllInvoicesController@export'])->name('all-invoices.export');
                    Route::get('all-invoices/convert-estimate/{id}', ['uses' => 'ManageAllInvoicesController@convertEstimate'])->name('all-invoices.convert-estimate');
                    Route::get('all-invoices/convert-milestone/{id}', ['uses' => 'ManageAllInvoicesController@convertMilestone'])->name('all-invoices.convert-milestone');
                    Route::get('all-invoices/convert-proposal/{id}', ['uses' => 'ManageAllInvoicesController@convertProposal'])->name('all-invoices.convert-proposal');
                    Route::get('all-invoices/update-item', ['uses' => 'ManageAllInvoicesController@addItems'])->name('all-invoices.update-item');
                    Route::get('all-invoices/payment-detail/{invoiceID}', ['uses' => 'ManageAllInvoicesController@paymentDetail'])->name('all-invoices.payment-detail');
                    Route::get('all-invoices/add-payment/{invoiceID}', ['uses' => 'ManageAllInvoicesController@addPayment'])->name('all-invoices.add-payment');
                    Route::post('all-invoices/store-payment', ['uses' => 'ManageAllInvoicesController@storePayment'])->name('all-invoices.store-payment');

                    Route::resource('invoice-settings', 'InvoiceSettingController');
                    Route::get('all-invoices/add-invoice-item/{invoiceID}', ['uses' => 'ManageAllInvoicesController@addInvoiceItem'])->name('all-invoices.add-invoice-item');

                    Route::post('all-invoices/store-invoice-item', ['uses' => 'ManageAllInvoicesController@storeInvoiceItem'])->name('all-invoices.store-invoice-item');
                    Route::get('all-invoices/edit-invoice-item/{id?}', ['uses' => 'ManageAllInvoicesController@editInvoiceItem'])->name('all-invoices.edit-invoice-item');
                    Route::post('all-invoices/update-invoice-item', ['uses' => 'ManageAllInvoicesController@updateInvoiceItem'])->name('all-invoices.update-invoice-item');


                    Route::get('all-invoices/edit-invoice-payment/{id?}', ['uses' => 'ManageAllInvoicesController@editInvoicePayment'])->name('all-invoices.edit-invoice-payment');
                    Route::post('all-invoices/update-invoice-payment', ['uses' => 'ManageAllInvoicesController@updateInvoicePayment'])->name('all-invoices.update-invoice-payment');

                    Route::delete('all-invoices/destroy-invoice-item/{id?}', ['uses' => 'ManageAllInvoicesController@destroyInvoiceItem'])->name('all-invoices.destroy-invoice-item');
                    Route::delete('all-invoices/destroy-invoice-payment/{id?}', ['uses' => 'ManageAllInvoicesController@destroyInvoicePayment'])->name('all-invoices.destroy-invoice-payment');

                    Route::resource('all-invoices', 'ManageAllInvoicesController');
                    //Payments routes
                    Route::get('payments/export/{startDate}/{endDate}/{status}/{payment}', ['uses' => 'ManagePaymentsController@export'])->name('payments.export');
                    Route::get('payments/data', ['uses' => 'ManagePaymentsController@data'])->name('payments.data');
                    Route::get('payments/pay-invoice/{invoiceId}', ['uses' => 'ManagePaymentsController@payInvoice'])->name('payments.payInvoice');
                    Route::get('payments/download', ['uses' => 'ManagePaymentsController@downloadSample'])->name('payments.downloadSample');
                    Route::post('payments/import', ['uses' => 'ManagePaymentsController@importExcel'])->name('payments.importExcel');
                    Route::resource('payments', 'ManagePaymentsController');


                    //region Products Routes
                    Route::get('products/data', ['uses' => 'AdminProductController@data'])->name('products.data');
                    Route::get('products/export', ['uses' => 'AdminProductController@export'])->name('products.export');
                    Route::resource('products', 'AdminProductController');
                    
                    Route::get('product-categories', ['uses' => 'ManageProductCategoriesController@productCategories'])->name('product-categories');                    
                    
                    Route::post('ajaxUpdateProductCategories', ['uses' => 'ManageProductCategoriesController@ajaxUpdateProductCategories'])->name('product-categories.ajaxUpdateProductCategories');
                    Route::post('ajaxCreateProductCategories', ['uses' => 'ManageProductCategoriesController@ajaxCreateProductCategories'])->name('product-categories.ajaxCreateProductCategories');
                    Route::post('ajaxDestroyProductCategories', ['uses' => 'ManageProductCategoriesController@ajaxDestroyProductCategories'])->name('product-categories.ajaxDestroyProductCategories');

                    //taxes
                    Route::get('manage-taxes', ['uses' => 'ManageTaxController@index'])->name('manage-taxes');

                    Route::post('ajaxUpdateTax', ['uses' => 'ManageTaxController@ajaxUpdateTax'])->name('product-tax.ajaxUpdateTax');
                    Route::post('ajaxCreateTax', ['uses' => 'ManageTaxController@ajaxCreateTax'])->name('product-tax.ajaxCreateTax');
                    Route::post('ajaxDestroyTax', ['uses' => 'ManageTaxController@ajaxDestroyTax'])->name('product-tax.ajaxDestroyTax');
                }
            );
            //Ticket routes
            Route::get('tickets/export/{startDate?}/{endDate?}/{agentId?}/{status?}/{priority?}/{channelId?}/{typeId?}', ['uses' => 'ManageTicketsController@export'])->name('tickets.export');
            Route::get('tickets/data/{startDate?}/{endDate?}/{agentId?}/{status?}/{priority?}/{channelId?}/{typeId?}', ['uses' => 'ManageTicketsController@data'])->name('tickets.data');
            Route::get('tickets/refresh-count/{startDate?}/{endDate?}/{agentId?}/{status?}/{priority?}/{channelId?}/{typeId?}', ['uses' => 'ManageTicketsController@refreshCount'])->name('tickets.refreshCount');
            Route::resource('tickets', 'ManageTicketsController');
            // User message
            Route::post('message-submit', ['as' => 'user-chat.message-submit', 'uses' => 'AdminChatController@postChatMessage']);
            Route::get('user-search', ['as' => 'user-chat.user-search', 'uses' => 'AdminChatController@getUserSearch']);
            Route::resource('user-chat', 'AdminChatController');
            // attendance
            Route::get('attendances/export/{startDate?}/{endDate?}/{employee?}', ['uses' => 'ManageAttendanceController@export'])->name('attendances.export');
            Route::get('attendances/detail', ['uses' => 'ManageAttendanceController@attendanceDetail'])->name('attendances.detail');
            Route::get('attendances/data', ['uses' => 'ManageAttendanceController@data'])->name('attendances.data');
            Route::get('attendances/check-holiday', ['uses' => 'ManageAttendanceController@checkHoliday'])->name('attendances.check-holiday');
            Route::post('attendances/employeeData/{startDate?}/{endDate?}/{userId?}', ['uses' => 'ManageAttendanceController@employeeData'])->name('attendances.employeeData');
            Route::post('attendances/refresh-count/{startDate?}/{endDate?}/{userId?}', ['uses' => 'ManageAttendanceController@refreshCount'])->name('attendances.refreshCount');
            Route::get('attendances/attendance-by-date', ['uses' => 'ManageAttendanceController@attendanceByDate'])->name('attendances.attendanceByDate');
            Route::get('attendances/byDateData', ['uses' => 'ManageAttendanceController@byDateData'])->name('attendances.byDateData');
            Route::post('attendances/dateAttendanceCount', ['uses' => 'ManageAttendanceController@dateAttendanceCount'])->name('attendances.dateAttendanceCount');
            Route::resource('attendances', 'ManageAttendanceController');
            //Event Calendar
            Route::post('events/removeAttendee', ['as' => 'events.removeAttendee', 'uses' => 'AdminEventCalendarController@removeAttendee']);
            Route::resource('events', 'AdminEventCalendarController');
            // Role permission routes
            Route::post('role-permission/assignAllPermission', ['as' => 'role-permission.assignAllPermission', 'uses' => 'ManageRolePermissionController@assignAllPermission']);
            Route::post('role-permission/removeAllPermission', ['as' => 'role-permission.removeAllPermission', 'uses' => 'ManageRolePermissionController@removeAllPermission']);
            Route::post('role-permission/assignRole', ['as' => 'role-permission.assignRole', 'uses' => 'ManageRolePermissionController@assignRole']);
            Route::post('role-permission/detachRole', ['as' => 'role-permission.detachRole', 'uses' => 'ManageRolePermissionController@detachRole']);
            Route::post('role-permission/storeRole', ['as' => 'role-permission.storeRole', 'uses' => 'ManageRolePermissionController@storeRole']);
            Route::post('role-permission/deleteRole', ['as' => 'role-permission.deleteRole', 'uses' => 'ManageRolePermissionController@deleteRole']);
            Route::get('role-permission/showMembers/{id}', ['as' => 'role-permission.showMembers', 'uses' => 'ManageRolePermissionController@showMembers']);
            Route::resource('role-permission', 'ManageRolePermissionController');
            //Leaves
            Route::post('leaves/leaveAction', ['as' => 'leaves.leaveAction', 'uses' => 'ManageLeavesController@leaveAction']);
            Route::get('leaves/show-reject-modal', ['as' => 'leaves.show-reject-modal', 'uses' => 'ManageLeavesController@rejectModal']);
            Route::get('leaves/all-leaves', ['as' => 'leave.all-leaves', 'uses' => 'ManageLeavesController@allLeaves']);
            Route::get('leaves/data/{startDate?}/{endDate?}/{employeeId?}', ['as' => 'leaves.data', 'uses' => 'ManageLeavesController@data']);
            Route::resource('leaves', 'ManageLeavesController');
            // LeaveType Resource
            Route::resource('leaveType', 'ManageLeaveTypesController');
            //sub task routes
            Route::post('sub-task/changeStatus', ['as' => 'sub-task.changeStatus', 'uses' => 'ManageSubTaskController@changeStatus']);
            Route::resource('sub-task', 'ManageSubTaskController');
            //task comments
            Route::resource('task-comment', 'AdminTaskCommentController');
            //taxes
            Route::resource('taxes', 'TaxSettingsController');
            //endregion
        }
    );
    //Driver routes
    Route::group(
        ['prefix' => 'driver', 'as' => 'driver.'],
        function () {
            Route::get('dashboard', ['uses' => 'Member\MemberDashboardController@index'])->name('dashboard');
            Route::post('profile/updateOneSignalId', ['uses' => 'Member\MemberProfileController@updateOneSignalId'])->name('profile.updateOneSignalId');
            Route::resource('profile', 'Member\MemberProfileController');
            Route::get('list-jobs', ['uses' => 'Admin\ListJobsController@index'])->name('list-jobs.index');
            Route::get('list-jobs/data', ['uses' => 'Admin\ListJobsController@data'])->name('list-jobs.data');
            Route::group(
                ['prefix' => 'moving'],
                function () {

                    Route::get('edit-job/{id}', ['uses' => 'Admin\ListJobsController@edit_job'])->name('list-jobs.edit-job');
                    Route::post('list-jobs/change-status/{id}', ['uses' => 'Admin\ListJobsController@changeStatus'])->name('list-jobs.change-status');

                    Route::get('list-jobs', ['uses' => 'Admin\ListJobsController@index'])->name('list-jobs.index');
                    Route::get('list-jobs/data', ['uses' => 'Admin\ListJobsController@data'])->name('list-jobs.data');
                    Route::get('list-jobs/excel', ['uses' => 'Admin\ListJobsController@excel'])->name('list-jobs.excel');
                    Route::get('payment/{id}', ['uses' => 'Admin\ListJobsController@payment'])->name('list-jobs.payment');

                    Route::get('list-jobs/export', ['uses' => 'Admin\ListJobsController@export'])->name('list-jobs.export');
                    Route::get('new-job', ['uses' => 'Admin\ListJobsController@new_job'])->name('list-jobs.new-job');
                    Route::get('inventory/{id}', ['uses' => 'Admin\ListJobsController@inventory'])->name('list-jobs.inventory');
                    Route::post('save-inventory-data/{id}', ['uses' => 'Admin\ListJobsController@saveInventoryData'])->name('list-jobs.save-inventory-data');
                    Route::post('delete-inventory-data', ['uses' => 'Admin\ListJobsController@deleteInventoryData'])->name('list-jobs.delete-inventory-data');
                    Route::post('get-inventory-data', ['uses' => 'Admin\ListJobsController@getInventoryDetails'])->name('list-jobs.get-inventory-data');
                    Route::get('operations/{id}', ['uses' => 'Admin\ListJobsController@operations'])->name('list-jobs.operations');
                    Route::get('operations-add-leg/{id}', ['uses' => 'Admin\ListJobsController@operationsAddLeg'])->name('list-jobs.operations-add-leg');
                    Route::get('operations-delete-leg/{job_id}/{leg_id}', ['uses' => 'Admin\ListJobsController@operationsDeleteLeg'])->name('list-jobs.operations-delete-leg');
                    Route::post('operations-save-data/{job_id}', ['uses' => 'Admin\ListJobsController@operationsSaveData'])->name('list-jobs.operations-save-data');
                    Route::get('invoice/{id}', ['uses' => 'Admin\ListJobsController@invoice'])->name('list-jobs.invoice');
                    Route::get('generate-quote/{id}', ['uses' => 'Admin\ListJobsController@generateQuote'])->name('list-jobs.generate-quote');
                    Route::get('download-quote/{id}', ['uses' => 'Admin\ListJobsController@downloadQuote'])->name('list-jobs.download-quote');
                    Route::get('view-quote/{id}', ['uses' => 'Admin\ListJobsController@viewQuote'])->name('list-jobs.view-quote');
                    Route::get('view-attachment/{id}', ['uses' => 'Admin\ListJobsController@viewAttachment'])->name('list-jobs.view-attachment');

                    Route::get('view-job-template-attachment/{id}', ['uses' => 'Admin\ListJobsController@viewJobTemplateAttachment'])->name('list-jobs.view-job-template-attachment');

                    Route::get('view-invoice/{id}', ['uses' => 'Admin\ListJobsController@viewInvoice'])->name('list-jobs.view-invoice');
                    Route::get('generate-inventory-list/{id}', ['uses' => 'Admin\ListJobsController@generateInventoryList'])->name('list-jobs.generate-inventory-list');
                    Route::get('download-inventory-list/{id}', ['uses' => 'Admin\ListJobsController@downloadInventoryList'])->name('list-jobs.download-inventory-list');
                    Route::get('view-inventory-list/{id}', ['uses' => 'Admin\ListJobsController@viewInventoryList'])->name('list-jobs.view-inventory-list');
                    Route::get('email/{id}', ['uses' => 'Admin\ListJobsController@email'])->name('list-jobs.email');
                    Route::post('email/{id}', ['uses' => 'Admin\ListJobsController@emailSend'])->name('list-jobs.email-send');
                    Route::get('attachment/{id}', ['uses' => 'Admin\ListJobsController@attachment'])->name('list-jobs.attachment');
                    Route::get('insurance/{id}', ['uses' => 'Admin\ListJobsController@insurance'])->name('list-jobs.insurance');
                    Route::get('send-quote-to-customer/{id}', ['uses' => 'Admin\ListJobsController@sendQuoteToCustomer'])->name('list-jobs.send-quote-to-customer');
                    Route::post('attachment/{id}', ['uses' => 'Admin\ListJobsController@attachmentUpload'])->name('list-jobs.attachment-upload');
                    Route::post('get-email-template/{id}', ['uses' => 'Admin\ListJobsController@getEmailTemplate'])->name('list-jobs.get-email-template');
                    Route::get('job-schedule', ['uses' => 'Admin\ListJobsController@jobSchedule'])->name('list-jobs.job-schedule');
                    Route::get('calendar-resources', ['uses' => 'Admin\ListJobsController@getVehicles'])->name('list-jobs.calendar-resources');
                    Route::get('calendar-events', ['uses' => 'Admin\ListJobsController@getJobs'])->name('list-jobs.calendar-events');

                    Route::get('updateScheduleEvent', ['uses' => 'Admin\ListJobsController@updateScheduleEvent'])->name('list-jobs.updateScheduleEvent');

                    Route::get('drivers', ['uses' => 'Admin\ListJobsController@drivers'])->name('list-jobs.driver-list');

                    Route::get('drivers/data', ['uses' => 'Admin\ListJobsController@driversData'])->name('list-jobs.drivers-data');
                    Route::get('drivers/create', ['uses' => 'Admin\ListJobsController@createDriver'])->name('list-jobs.create-driver');
                    Route::get('drivers/edit/{id}', ['uses' => 'Admin\ListJobsController@editDriver'])->name('list-jobs.edit-driver');
                    Route::post('drivers/store', ['uses' => 'Admin\ListJobsController@storeDriver'])->name('list-jobs.store-driver');
                    Route::post('drivers/update/{id}', ['uses' => 'Admin\ListJobsController@updateDriver'])->name('list-jobs.update-driver');

                    Route::delete('delete-driver/{id?}', ['uses' => 'Admin\ListJobsController@destroyDriver'])->name('list-jobs.delete-driver');

                    Route::post('updateScheduleEvent', ['uses' => 'Admin\ListJobsController@updateScheduleEventPost'])->name('list-jobs.updateScheduleEvent');
                    Route::get('job-logs-body/{id}', ['uses' => 'Admin\ListJobsController@getJobsLogsBody'])->name('list-jobs.job-logs-body');

                    Route::get('view-job/{id}', ['uses' => 'ListJobsController@viewJob'])->name('list-jobs.view-job');
                    
                    Route::resource('list-jobs', 'Admin\ListJobsController');
                }
            );
            Route::resource('finance', 'Member\MemberEstimatesController', ['only' => ['edit', 'update', 'index']]); // hack to make left admin menu item active
            Route::group(
                ['prefix' => 'finance'],
                function () {
                    // Estimate routes
                    Route::get('estimates/data', ['uses' => 'Member\MemberEstimatesController@data'])->name('estimates.data');
                    Route::get('estimates/download/{id}', ['uses' => 'Member\MemberEstimatesController@download'])->name('estimates.download');
                    Route::resource('estimates', 'Member\MemberEstimatesController');
                    //Expenses routes
                    Route::get('expenses/data', ['uses' => 'Member\MemberExpensesController@data'])->name('expenses.data');
                    Route::resource('expenses', 'Member\MemberExpensesController');
                    // All invoices list routes
                    Route::post('file/store', ['uses' => 'Member\MemberAllInvoicesController@storeFile'])->name('invoiceFile.store');
                    Route::delete('file/destroy', ['uses' => 'Member\MemberAllInvoicesController@destroyFile'])->name('invoiceFile.destroy');
                    Route::get('all-invoices/data', ['uses' => 'Member\MemberAllInvoicesController@data'])->name('all-invoices.data');
                    Route::get('all-invoices/download/{id}', ['uses' => 'Member\MemberAllInvoicesController@download'])->name('all-invoices.download');
                    Route::get('all-invoices/convert-estimate/{id}', ['uses' => 'Member\MemberAllInvoicesController@convertEstimate'])->name('all-invoices.convert-estimate');
                    Route::get('all-invoices/update-item', ['uses' => 'Member\MemberAllInvoicesController@addItems'])->name('all-invoices.update-item');
                    Route::get('all-invoices/payment-detail/{invoiceID}', ['uses' => 'Member\MemberAllInvoicesController@paymentDetail'])->name('all-invoices.payment-detail');
                    Route::resource('all-invoices', 'Member\MemberAllInvoicesController');
                    //Payments routes
                    Route::get('payments/data', ['uses' => 'Member\MemberPaymentsController@data'])->name('payments.data');
                    Route::get('payments/pay-invoice/{invoiceId}', ['uses' => 'Member\MemberPaymentsController@payInvoice'])->name('payments.payInvoice');
                    Route::resource('payments', 'Member\MemberPaymentsController');
                }
            );
        }
    );
    // Employee routes
    Route::group(
        ['namespace' => 'Member', 'prefix' => 'member', 'as' => 'member.'],
        function () {
            Route::get('dashboard', ['uses' => 'MemberDashboardController@index'])->name('dashboard');
            Route::post('profile/updateOneSignalId', ['uses' => 'MemberProfileController@updateOneSignalId'])->name('profile.updateOneSignalId');
            Route::resource('profile', 'MemberProfileController');
            Route::get('projects/data', ['uses' => 'MemberProjectsController@data'])->name('projects.data');
            Route::resource('projects', 'MemberProjectsController');
            Route::get('project-template/data', ['uses' => 'ProjectTemplateController@data'])->name('project-template.data');
            Route::resource('project-template', 'ProjectTemplateController');
            Route::post('project-template-members/save-group', ['uses' => 'ProjectMemberTemplateController@storeGroup'])->name('project-template-members.storeGroup');
            Route::resource('project-template-member', 'ProjectMemberTemplateController');
            Route::resource('project-template-task', 'ProjectTemplateTaskController');
            // Route::get('leads/data', ['uses' => 'MemberLeadController@data'])->name('leads.data');
            // Route::post('leads/change-status', ['uses' => 'MemberLeadController@changeStatus'])->name('leads.change-status');
            // Route::get('leads/follow-up/{leadID}', ['uses' => 'MemberLeadController@followUpCreate'])->name('leads.follow-up');
            // Route::get('leads/followup/{leadID}', ['uses' => 'MemberLeadController@followUpShow'])->name('leads.followup');
            // Route::post('leads/follow-up-store', ['uses' => 'MemberLeadController@followUpStore'])->name('leads.follow-up-store');
            // Route::get('leads/follow-up-edit/{id?}', ['uses' => 'MemberLeadController@editFollow'])->name('leads.follow-up-edit');
            // Route::post('leads/follow-up-update', ['uses' => 'MemberLeadController@UpdateFollow'])->name('leads.follow-up-update');
            // Route::get('leads/follow-up-sort', ['uses' => 'MemberLeadController@followUpSort'])->name('leads.follow-up-sort');
            // Route::resource('leads', 'MemberLeadController');
            // Lead Files
            Route::get('lead-files/download/{id}', ['uses' => 'LeadFilesController@download'])->name('lead-files.download');
            Route::get('lead-files/thumbnail', ['uses' => 'LeadFilesController@thumbnailShow'])->name('lead-files.thumbnail');
            Route::resource('lead-files', 'LeadFilesController');
            // Proposal routes
            Route::get('proposals/data/{id?}', ['uses' => 'MemberProposalController@data'])->name('proposals.data');
            Route::get('proposals/download/{id}', ['uses' => 'MemberProposalController@download'])->name('proposals.download');
            Route::get('proposals/create/{leadID?}', ['uses' => 'MemberProposalController@create'])->name('proposals.create');
            Route::resource('proposals', 'MemberProposalController', ['expect' => ['create']]);
            Route::group(
                ['prefix' => 'projects'],
                function () {
                    Route::resource('project-members', 'MemberProjectsMemberController');
                    Route::post('tasks/data/{startDate?}/{endDate?}/{hideCompleted?}/{projectId?}', ['uses' => 'MemberTasksController@data'])->name('tasks.data');
                    Route::post('tasks/sort', ['uses' => 'MemberTasksController@sort'])->name('tasks.sort');
                    Route::post('tasks/change-status', ['uses' => 'MemberTasksController@changeStatus'])->name('tasks.changeStatus');
                    Route::get('tasks/check-task/{taskID}', ['uses' => 'MemberTasksController@checkTask'])->name('tasks.checkTask');
                    Route::resource('tasks', 'MemberTasksController');
                    Route::get('files/download/{id}', ['uses' => 'MemberProjectFilesController@download'])->name('files.download');
                    Route::get('files/thumbnail', ['uses' => 'MemberProjectFilesController@thumbnailShow'])->name('files.thumbnail');
                    Route::resource('files', 'MemberProjectFilesController');
                    Route::get('time-log/show-log/{id}', ['uses' => 'MemberTimeLogController@showTomeLog'])->name('time-log.show-log');
                    Route::get('time-log/data/{id}', ['uses' => 'MemberTimeLogController@data'])->name('time-log.data');
                    Route::post('time-log/store-time-log', ['uses' => 'MemberTimeLogController@storeTimeLog'])->name('time-log.store-time-log');
                    Route::post('time-log/update-time-log/{id}', ['uses' => 'MemberTimeLogController@updateTimeLog'])->name('time-log.update-time-log');
                    Route::resource('time-log', 'MemberTimeLogController');
                }
            );
            //sticky note
            Route::resource('sticky-note', 'MemberStickyNoteController');
            // User message
            Route::post('message-submit', ['as' => 'user-chat.message-submit', 'uses' => 'MemberChatController@postChatMessage']);
            Route::get('user-search', ['as' => 'user-chat.user-search', 'uses' => 'MemberChatController@getUserSearch']);
            Route::resource('user-chat', 'MemberChatController');
            //Notice
            Route::get('notices/data', ['uses' => 'MemberNoticesController@data'])->name('notices.data');
            Route::resource('notices', 'MemberNoticesController');
            // task routes
            Route::resource('task', 'MemberAllTasksController', ['only' => ['edit', 'update', 'index']]); // hack to make left admin menu item active
            Route::group(
                ['prefix' => 'task'],
                function () {
                    Route::post('all-tasks/data/{startDate?}/{endDate?}/{hideCompleted?}/{projectId?}', ['uses' => 'MemberAllTasksController@data'])->name('all-tasks.data');
                    Route::get('all-tasks/members/{projectId}', ['uses' => 'MemberAllTasksController@membersList'])->name('all-tasks.members');
                    Route::get('all-tasks/ajaxCreate/{columnId}', ['uses' => 'MemberAllTasksController@ajaxCreate'])->name('all-tasks.ajaxCreate');
                    Route::get('all-tasks/reminder/{taskid}', ['uses' => 'MemberAllTasksController@remindForTask'])->name('all-tasks.reminder');
                    Route::resource('all-tasks', 'MemberAllTasksController');
                    // taskboard resource
                    Route::post('taskboard/updateIndex', ['as' => 'taskboard.updateIndex', 'uses' => 'MemberTaskboardController@updateIndex']);
                    Route::resource('taskboard', 'MemberTaskboardController');
                    // task calendar routes
                    Route::resource('task-calendar', 'MemberCalendarController');
                }
            );
            Route::resource('finance', 'MemberEstimatesController', ['only' => ['edit', 'update', 'index']]); // hack to make left admin menu item active
            Route::group(
                ['prefix' => 'finance'],
                function () {
                    // Estimate routes
                    Route::get('estimates/data', ['uses' => 'MemberEstimatesController@data'])->name('estimates.data');
                    Route::get('estimates/download/{id}', ['uses' => 'MemberEstimatesController@download'])->name('estimates.download');
                    Route::resource('estimates', 'MemberEstimatesController');
                    //Expenses routes
                    Route::get('expenses/data', ['uses' => 'MemberExpensesController@data'])->name('expenses.data');
                    Route::resource('expenses', 'MemberExpensesController');
                    // All invoices list routes
                    Route::post('file/store', ['uses' => 'MemberAllInvoicesController@storeFile'])->name('invoiceFile.store');
                    Route::delete('file/destroy', ['uses' => 'MemberAllInvoicesController@destroyFile'])->name('invoiceFile.destroy');
                    Route::get('all-invoices/data', ['uses' => 'MemberAllInvoicesController@data'])->name('all-invoices.data');
                    Route::get('all-invoices/download/{id}', ['uses' => 'MemberAllInvoicesController@download'])->name('all-invoices.download');
                    Route::get('all-invoices/convert-estimate/{id}', ['uses' => 'MemberAllInvoicesController@convertEstimate'])->name('all-invoices.convert-estimate');
                    Route::get('all-invoices/update-item', ['uses' => 'MemberAllInvoicesController@addItems'])->name('all-invoices.update-item');
                    Route::get('all-invoices/payment-detail/{invoiceID}', ['uses' => 'MemberAllInvoicesController@paymentDetail'])->name('all-invoices.payment-detail');
                    Route::resource('all-invoices', 'MemberAllInvoicesController');
                    //Payments routes
                    Route::get('payments/data', ['uses' => 'MemberPaymentsController@data'])->name('payments.data');
                    Route::get('payments/pay-invoice/{invoiceId}', ['uses' => 'MemberPaymentsController@payInvoice'])->name('payments.payInvoice');
                    Route::resource('payments', 'MemberPaymentsController');
                }
            );
            // Ticket reply template routes
            Route::post('replyTemplates/fetch-template', ['uses' => 'MemberTicketReplyTemplatesController@fetchTemplate'])->name('replyTemplates.fetchTemplate');
            //Tickets routes
            Route::get('tickets/data', ['uses' => 'MemberTicketsController@data'])->name('tickets.data');
            Route::post('tickets/storeAdmin', ['uses' => 'MemberTicketsController@storeAdmin'])->name('tickets.storeAdmin');
            Route::post('tickets/updateAdmin/{id}', ['uses' => 'MemberTicketsController@updateAdmin'])->name('tickets.updateAdmin');
            Route::post('tickets/close-ticket/{id}', ['uses' => 'MemberTicketsController@closeTicket'])->name('tickets.closeTicket');
            Route::post('tickets/open-ticket/{id}', ['uses' => 'MemberTicketsController@reopenTicket'])->name('tickets.reopenTicket');
            Route::get('tickets/admin-data/{startDate?}/{endDate?}/{agentId?}/{status?}/{priority?}/{channelId?}/{typeId?}', ['uses' => 'MemberTicketsController@adminData'])->name('tickets.adminData');
            Route::get('tickets/refresh-count/{startDate?}/{endDate?}/{agentId?}/{status?}/{priority?}/{channelId?}/{typeId?}', ['uses' => 'MemberTicketsController@refreshCount'])->name('tickets.refreshCount');
            Route::resource('tickets', 'MemberTicketsController');
            //Ticket agent routes
            Route::get('ticket-agent/data/{startDate?}/{endDate?}/{status?}/{priority?}/{channelId?}/{typeId?}', ['uses' => 'MemberTicketsAgentController@data'])->name('ticket-agent.data');
            Route::get('ticket-agent/refresh-count/{startDate?}/{endDate?}/{status?}/{priority?}/{channelId?}/{typeId?}', ['uses' => 'MemberTicketsAgentController@refreshCount'])->name('ticket-agent.refreshCount');
            Route::post('ticket-agent/fetch-template', ['uses' => 'MemberTicketsAgentController@fetchTemplate'])->name('ticket-agent.fetchTemplate');
            Route::resource('ticket-agent', 'MemberTicketsAgentController');
            // attendance
            Route::get('attendances/detail', ['uses' => 'MemberAttendanceController@attendanceDetail'])->name('attendances.detail');
            Route::get('attendances/data', ['uses' => 'MemberAttendanceController@data'])->name('attendances.data');
            Route::get('attendances/check-holiday', ['uses' => 'MemberAttendanceController@checkHoliday'])->name('attendances.check-holiday');
            Route::post('attendances/storeAttendance', ['uses' => 'MemberAttendanceController@storeAttendance'])->name('attendances.storeAttendance');
            Route::post('attendances/employeeData/{startDate?}/{endDate?}/{userId?}', ['uses' => 'MemberAttendanceController@employeeData'])->name('attendances.employeeData');
            Route::post('attendances/refresh-count/{startDate?}/{endDate?}/{userId?}', ['uses' => 'MemberAttendanceController@refreshCount'])->name('attendances.refreshCount');
            Route::resource('attendances', 'MemberAttendanceController');
            // Holidays
            Route::get('holidays/view-holiday/{year?}', 'MemberHolidaysController@viewHoliday')->name('holidays.view-holiday');
            Route::get('holidays/calendar-month', 'MemberHolidaysController@getCalendarMonth')->name('holidays.calendar-month');
            Route::get('holidays/mark_sunday', 'MemberHolidaysController@Sunday')->name('holidays.mark-sunday');
            Route::get('holidays/calendar/{year?}', 'MemberHolidaysController@holidayCalendar')->name('holidays.calendar');
            Route::get('holidays/mark-holiday', 'MemberHolidaysController@markHoliday')->name('holidays.mark-holiday');
            Route::post('holidays/mark-holiday-store', 'MemberHolidaysController@markDayHoliday')->name('holidays.mark-holiday-store');
            Route::resource('holidays', 'MemberHolidaysController');
            // events
            Route::post('events/removeAttendee', ['as' => 'events.removeAttendee', 'uses' => 'MemberEventController@removeAttendee']);
            Route::resource('events', 'MemberEventController');
            // clients
            Route::group(
                ['prefix' => 'clients'],
                function () {
                    Route::get('projects/{id}', ['uses' => 'MemberClientsController@showProjects'])->name('clients.projects');
                    Route::get('invoices/{id}', ['uses' => 'MemberClientsController@showInvoices'])->name('clients.invoices');
                    Route::get('contacts/data/{id}', ['uses' => 'MemberClientContactController@data'])->name('contacts.data');
                    Route::resource('contacts', 'MemberClientContactController');
                }
            );
            Route::get('clients/data', ['uses' => 'MemberClientsController@data'])->name('clients.data');
            Route::resource('clients', 'MemberClientsController');
            Route::get('employees/docs-create/{id}', ['uses' => 'MemberEmployeesController@docsCreate'])->name('employees.docs-create');
            Route::get('employees/tasks/{userId}/{hideCompleted}', ['uses' => 'MemberEmployeesController@tasks'])->name('employees.tasks');
            Route::get('employees/time-logs/{userId}', ['uses' => 'MemberEmployeesController@timeLogs'])->name('employees.time-logs');
            Route::get('employees/data', ['uses' => 'MemberEmployeesController@data'])->name('employees.data');
            Route::get('employees/export', ['uses' => 'MemberEmployeesController@export'])->name('employees.export');
            Route::post('employees/assignRole', ['uses' => 'MemberEmployeesController@assignRole'])->name('employees.assignRole');
            Route::post('employees/assignProjectAdmin', ['uses' => 'MemberEmployeesController@assignProjectAdmin'])->name('employees.assignProjectAdmin');
            Route::resource('employees', 'MemberEmployeesController');
            Route::get('employee-docs/download/{id}', ['uses' => 'MemberEmployeeDocsController@download'])->name('employee-docs.download');
            Route::resource('employee-docs', 'MemberEmployeeDocsController');
            Route::get('all-time-logs/show-active-timer', ['uses' => 'MemberAllTimeLogController@showActiveTimer'])->name('all-time-logs.show-active-timer');
            Route::post('all-time-logs/stop-timer/{id}', ['uses' => 'MemberAllTimeLogController@stopTimer'])->name('all-time-logs.stopTimer');
            Route::post('all-time-logs/data/{startDate?}/{endDate?}/{projectId?}/{employee?}', ['uses' => 'MemberAllTimeLogController@data'])->name('all-time-logs.data');
            Route::get('all-time-logs/members/{projectId}', ['uses' => 'MemberAllTimeLogController@membersList'])->name('all-time-logs.members');
            Route::resource('all-time-logs', 'MemberAllTimeLogController');
            Route::post('leaves/leaveAction', ['as' => 'leaves.leaveAction', 'uses' => 'MemberLeavesController@leaveAction']);
            Route::get('leaves/data', ['as' => 'leaves.data', 'uses' => 'MemberLeavesController@data']);
            Route::resource('leaves', 'MemberLeavesController');
            Route::post('leaves-dashboard/leaveAction', ['as' => 'leaves-dashboard.leaveAction', 'uses' => 'MemberLeaveDashboardController@leaveAction']);
            Route::resource('leaves-dashboard', 'MemberLeaveDashboardController');
            //sub task routes
            Route::post('sub-task/changeStatus', ['as' => 'sub-task.changeStatus', 'uses' => 'MemberSubTaskController@changeStatus']);
            Route::resource('sub-task', 'MemberSubTaskController');
            //task comments
            Route::resource('task-comment', 'MemberTaskCommentController');
            //region Products Routes
            Route::get('products/data', ['uses' => 'MemberProductController@data'])->name('products.data');
            Route::resource('products', 'MemberProductController');
            //endregion
        }
    );
    // Client routes
    Route::group(
        ['namespace' => 'Client', 'prefix' => 'client', 'as' => 'client.'],
        function () {
            Route::resource('dashboard', 'ClientDashboardController');
            Route::resource('profile', 'ClientProfileController');
            // Project section
            Route::get('projects/data', ['uses' => 'ClientProjectsController@data'])->name('projects.data');
            Route::resource('projects', 'ClientProjectsController');
            Route::group(
                ['prefix' => 'projects'],
                function () {
                    Route::resource('project-members', 'ClientProjectMembersController');
                    Route::resource('tasks', 'ClientTasksController');
                    Route::get('files/download/{id}', ['uses' => 'ClientFilesController@download'])->name('files.download');
                    Route::get('files/thumbnail', ['uses' => 'ClientFilesController@thumbnailShow'])->name('files.thumbnail');
                    Route::resource('files', 'ClientFilesController');
                    Route::get('time-log/data/{id}', ['uses' => 'ClientTimeLogController@data'])->name('time-log.data');
                    Route::resource('time-log', 'ClientTimeLogController');
                    Route::get('project-invoice/download/{id}', ['uses' => 'ClientProjectInvoicesController@download'])->name('project-invoice.download');
                    Route::resource('project-invoice', 'ClientProjectInvoicesController');
                }
            );
            //sticky note
            Route::resource('sticky-note', 'ClientStickyNoteController');
            // Invoice Section
            Route::get('invoices/download/{id}', ['uses' => 'ClientInvoicesController@download'])->name('invoices.download');
            Route::resource('invoices', 'ClientInvoicesController');
            // Estimate Section
            Route::get('estimates/download/{id}', ['uses' => 'ClientEstimateController@download'])->name('estimates.download');
            Route::resource('estimates', 'ClientEstimateController');
            // Issues section
            Route::get('my-issues/data', ['uses' => 'ClientMyIssuesController@data'])->name('my-issues.data');
            Route::resource('my-issues', 'ClientMyIssuesController');
            // route for view/blade file
            Route::get('paywithpaypal', array('as' => 'paywithpaypal', 'uses' => 'PaypalController@payWithPaypal',));
            // change language
            Route::get('language/change-language', ['uses' => 'ClientProfileController@changeLanguage'])->name('language.change-language');
            //Tickets routes
            Route::get('tickets/data', ['uses' => 'ClientTicketsController@data'])->name('tickets.data');
            Route::post('tickets/close-ticket/{id}', ['uses' => 'ClientTicketsController@closeTicket'])->name('tickets.closeTicket');
            Route::post('tickets/open-ticket/{id}', ['uses' => 'ClientTicketsController@reopenTicket'])->name('tickets.reopenTicket');
            Route::resource('tickets', 'ClientTicketsController');
            Route::resource('events', 'ClientEventController');
            Route::resource('leaves', 'LeaveSettingController');
            // User message
            Route::post('message-submit', ['as' => 'user-chat.message-submit', 'uses' => 'ClientChatController@postChatMessage']);
            Route::get('user-search', ['as' => 'user-chat.user-search', 'uses' => 'ClientChatController@getUserSearch']);
            Route::resource('user-chat', 'ClientChatController');
            //task comments
            Route::resource('task-comment', 'ClientTaskCommentController');
        }
    );
    // Mark all notifications as readu
    Route::post('mark-notification-read', ['uses' => 'NotificationController@markAllRead'])->name('mark-notification-read');
    Route::get('show-all-member-notifications', ['uses' => 'NotificationController@showAllMemberNotifications'])->name('show-all-member-notifications');
    Route::get('show-all-client-notifications', ['uses' => 'NotificationController@showAllClientNotifications'])->name('show-all-client-notifications');
    Route::get('show-all-admin-notifications', ['uses' => 'NotificationController@showAllAdminNotifications'])->name('show-all-admin-notifications');
});