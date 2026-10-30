<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankDetailsController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CurrencyExchangeRateController;
use App\Http\Controllers\DemoMailController;
use App\Http\Controllers\DispatcherController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EUFundsTransferRatesController;
use App\Http\Controllers\EUFundTransferOrderController;
use App\Http\Controllers\FedExController;
use App\Http\Controllers\IntlFundsTransferRatesController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrderBatchController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ShippingRateController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\IntlFundTransferOrderController;
use App\Http\Controllers\myController;
use App\Http\Controllers\PaymentRequestController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\SubAdminController;
use App\Http\Controllers\UserFundsController;
use App\Http\Controllers\WalkInCustomerController;
use App\Http\Controllers\WalkInCustomerOrder;
use App\Http\Controllers\WalkInOrderAgents;
use App\Mail\DispacherNotificationMail;
use App\Models\Country;
use App\Models\EUFundTransferOrder;
use App\Models\IntlFundTransferOrder;
use App\Models\Order;
use App\Models\ShippingCost;
use App\Models\User;
use App\Models\UserFunds;
use App\Models\WalkInCustomer;
use App\Notifications\OrderStatusNotification;
use App\Notifications\PaymentRequestNotification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

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

Route::get('/send-mail', [DemoMailController::class,'index'] );
Route::get('/test', function () {
    // Mail::to('abc@gmail.com')->send(new DispacherNotificationMail());
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_08_24_161206_create_coutries_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_08_24_161227_create_cities_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_08_24_161251_create_states_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_08_24_224820_create_agents_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_08_25_000008_add_registration_status_field_to_users_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_08_25_111402_update_enum_values_in_users_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_08_25_175459_add_new_fields_in_dispatchers_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_08_25_212638_create_shipping_costs_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_08_25_222121_create_city_shipping_costs_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_08_26_230605_create_currency_exchange_rates_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_08_30_095304_create_batchlogs_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_08_30_102134_create_batchorder_logs_table.php'
    // ]);
    //  Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_08_30_182206_change_column_batchorder_logs_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_08_30_182434_change_column_batchlogs_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_08_30_182959_change_column_order_batches_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_08_30_184059_add_column_order_batches_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_08_31_121341_remove_contrained_from_orders_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_08_31_145846_add_new_columns_orders_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_08_30_185559_add_column_new_order_batches_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_08_31_190378_add_new_columns_orderss_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_08_31_190623_add_new_columns_order_packages_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_08_31_222045_add_current_location_address_into_batchlogs_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_09_04_124546_create_bank_details_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_09_04_161629_create_payment_requests_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_09_04_171247_change_datatype_in_payment_requests_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_09_05_164933_add_new_column_in_users_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_09_06_200454_add_tow_column_in_orders_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_09_07_114906_add_cummercial_invoice_in_orders_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_09_07_220352_create_notifications_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_09_11_044833_add_column_in_users_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_09_11_190430_create_contactus_table.php'
    // ]); 
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_09_12_110923_add_column_users_table.php'
    // ]);
    // Artisan::call('migrate', [
    //     '--path' => 'database/migrations/2024_09_12_125123_add_column_user_funds_table.php'
    // ]);
    Artisan::call('migrate', [
        '--path' => 'database/migrations/2024_09_14_163630_add_column_walk_in_customers_table.php'
    ]);
    Artisan::call('migrate', [
        '--path' => 'database/migrations/2024_09_14_163705_add_column_orders_table.php'
    ]);
    Artisan::call('migrate', [
        '--path' => 'database/migrations/2024_09_14_171213_add_column_contact_us_table.php'
    ]);
    return "Success";
});

Route::get('/', [HomeController::class, 'landing'])->name('landing');
Route::get('register', [HomeController::class, 'register'])->name('register');
Route::get('allNotifications', [HomeController::class, 'allNotifications'])->name('allNotifications');
Route::get('markAsRead', [HomeController::class, 'markAsRead'])->name('markAsRead');

Route::get('/set-language/{locale}', function ($locale) {
    app()->setLocale($locale);
    session()->put('locale', $locale);
    return redirect()->back();
})->name('set-lang');

Route::resource('my-wallet', UserFundsController::class)->middleware(['auth']);

Route::get('/payment_page', function () {
    return view('payment');
})->name('payment_page');

Route::get('/shipping_page', function () {
    return view('shipping_page');
})->name('shipping_page');

Route::get('/tup_up_page', function () {
    return view('prepaid');
})->name('tup_up_page');

Route::get('/pick_up_point_page', function () {
    return view('pickup_point');
})->name('pick_up_point_page');

Route::get('/spdi_page', function () {
    return view('spdi_page');
})->name('spdi_page');

Route::get('/pec_page', function () {
    return view('pec_page');
})->name('pec_page');

Route::get('/roadside_page', function () {
    return view('roadside_page');
})->name('roadside_page');

Route::get('/ticket_resale_page', function () {
    return view('ticket_page');
})->name('ticket_resale_page');

Route::get('/gas_page', function () {
    return view('gas_page');
})->name('gas_page');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/who_we_are', function () {
    return view('who_we_are');
})->name('who_we_are');

Route::get('/order_guide', function () {
    return view('order_guide');
})->name('order_guide');

Route::get('/how_to_pack', function () {
    return view('how_to_pack');
})->name('how_to_pack');

Route::get('/how_to_measure', function () {
    return view('how_to_measure');
})->name('how_to_measure');

Route::get('/prohibited_goods', function () {
    return view('prohibited_goods');
})->name('prohibited_goods');



Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->middleware(['auth'])->name('home');

Route::get('admin/settings', [AdminController::class, 'admin_settings'])->name('admin.settings')->middleware(['auth']);
Route::post('updatePassword', [AuthController::class, 'updatePassword'])->name('updatePassword')->middleware(['auth']);

Route::resource('locations', LocationController::class)->middleware(['auth']);
Route::get('locationList', [LocationController::class, 'locationList'])->name('locationList')->middleware(['auth']);

Route::resource('shipping_rates', ShippingRateController::class)->middleware(['auth']);
Route::get('shippingRatesList', [ShippingRateController::class, 'shippingRatesList'])->name('shippingRatesList')->middleware(['auth']);
Route::get('citiseShippingRates/{countryId}', [ShippingRateController::class, 'citiseShippingRates'])->name('cities.shipping.rates')->middleware(['auth']);
Route::get('citiseShippingCountry/{countryId}', [ShippingRateController::class, 'citiseShippingCountry'])->name('cities.shipping.country')->middleware(['auth']);
Route::get('citiesShippingRatesList/{countryId}', [ShippingRateController::class, 'citiesShippingRatesList'])->name('citiesShippingRatesList')->middleware(['auth']);
Route::get('countryShippingRatesList/{countryId}', [ShippingRateController::class, 'countryShippingRatesList'])->name('countryShippingRatesList')->middleware(['auth']);

Route::get('weightShippingRates/{countryId}', [ShippingRateController::class, 'weightShippingRates'])->name('weight.shipping.rates')->middleware(['auth']);
Route::get('weightShippingRatesList/{countryId}', [ShippingRateController::class, 'weightShippingRatesList'])->name('weightShippingRatesList')->middleware(['auth']);

Route::get('getWeightShippingCost/{id}', [ShippingRateController::class, 'getWeightShippingCost'])->name('get-weight-shipping-cost')->middleware(['auth']);
Route::post('updateWeightShippingCost', [ShippingRateController::class, 'updateWeightShippingCost'])->name('shipping-cost-update')->middleware(['auth']);


Route::get('getCityShippingCost/{id}', [ShippingRateController::class, 'getCityShippingCost'])->name('get-city-shipping-cost')->middleware(['auth']);
Route::post('saveCityShippingCostPercentage/{countryId}', [ShippingRateController::class, 'saveCityShippingCostPercentage'])->name('save-city-shipping-cost-percentage')->middleware(['auth']);
Route::post('saveCountryShippingCostPercentage/{countryId}', [ShippingRateController::class, 'saveCountryShippingCostPercentage'])->name('save-country-shipping-cost-percentage')->middleware(['auth']);

Route::get('currencyExchangeView', [CurrencyExchangeRateController::class, 'index'])->name('currency-view')->middleware(['auth']);
Route::get('currencyExchangeRateList', [CurrencyExchangeRateController::class, 'currencyExchangeRateList'])->name('currencyExchangeRateList')->middleware(['auth']);
Route::post('assignCurrency/{userId}', [CurrencyExchangeRateController::class, 'assignCurrency'])->name('assignCurrency');
// 


Route::get('getCurrencyExchangeRate/{id}', [CurrencyExchangeRateController::class, 'getCurrencyExchangeRate'])->name('getCurrencyExchangeRate')->middleware(['auth']);
Route::post('updateCurrencyExchangeRate', [CurrencyExchangeRateController::class, 'updateCurrencyExchangeRate'])->name('updateCurrencyExchangeRate')->middleware(['auth']);
Route::post('storeCurrencyExchangeRate', [CurrencyExchangeRateController::class, 'storeCurrencyExchangeRate'])->name('storeCurrencyExchangeRate')->middleware(['auth']);
Route::get('removeCurrencyExchangeRate/{id}', [CurrencyExchangeRateController::class, 'removeCurrencyExchangeRate'])->name('removeCurrencyExchangeRate')->middleware(['auth']);

Route::resource('eu_fund_rates', EUFundsTransferRatesController::class)->middleware(['auth']);
Route::get('EUFundsTransferRatesList', [EUFundsTransferRatesController::class, 'EUFundsTransferRatesList'])->name('EUFundsTransferRatesList')->middleware(['auth']);


Route::resource('intl_funds_rate', IntlFundsTransferRatesController::class)->middleware(['auth']);
Route::get('IntlFundsTransferRatesList', [IntlFundsTransferRatesController::class, 'IntlFundsTransferRatesList'])->name('IntlFundsTransferRatesList')->middleware(['auth']);

Route::get('/locations-search', [LocationController::class, 'search'])->name('locations.search');
Route::get('/rates-fetch', [ShippingRateController::class, 'rates_fetch'])->name('rates.fetch');
Route::get('myOrdersList', [OrderController::class, 'myOrdersList'])->name('myOrdersList')->middleware(['auth']);
Route::post('cancelOrder', [OrderController::class, 'cancelOrder'])->name('cancelOrder')->middleware(['auth']);

Route::get('allOrders', [OrderController::class, 'allOrders'])->name('allOrders')->middleware(['auth']);
Route::get('allOrdersList', [OrderController::class, 'allOrdersList'])->name('allOrdersList')->middleware(['auth']);
Route::get('cummercial_invoice', [OrderController::class, 'cummercialInvoice'])->name('cummercial_invoice');

Route::resource('orders', OrderController::class)->middleware(['auth']);

Route::get('allUsers', [AdminController::class, 'allUsers'])->name('allUsers')->middleware(['auth']);
Route::get('allUsersList', [AdminController::class, 'allUsersList'])->name('allUsersList')->middleware(['auth']);
Route::get('allAgentList', [WalkInCustomerController::class, 'allAgentList'])->name('allAgentList')->middleware(['auth']);
Route::get('allDispatcherList', [WalkInCustomerController::class, 'allDispatcherList'])->name('allDispatcherList')->middleware(['auth']);


Route::resource('walk_in_customers', WalkInCustomerController::class)->middleware(['auth']);
Route::get('allWalkInCusList', [WalkInCustomerController::class, 'allWalkInCusList'])->name('allWalkInCusList')->middleware(['auth']);
Route::get('allMobileUserList', [WalkInCustomerController::class, 'allMobileUserList'])->name('allMobileUserList')->middleware(['auth']);

Route::post('unblockUser', [AdminController::class, 'unblockUser'])->name('unblockUser')->middleware(['auth']);
Route::post('blockUser', [AdminController::class, 'blockUser'])->name('blockUser')->middleware(['auth']);

Route::post('makeAdmin', [AdminController::class, 'makeAdmin'])->name('makeAdmin')->middleware(['auth']);
Route::post('makeDispatcher', [AdminController::class, 'makeDispatcher'])->name('makeDispatcher')->middleware(['auth']);
Route::post('makeUser', [AdminController::class, 'makeUser'])->name('makeUser')->middleware(['auth']);

Route::post('unapproveWalkInCusKYC', [WalkInCustomerController::class, 'unapproveWalkInCusKYC'])->name('unapproveWalkInCusKYC')->middleware(['auth']);
Route::post('approveWalkInCusKYC', [WalkInCustomerController::class, 'approveWalkInCusKYC'])->name('approveWalkInCusKYC')->middleware(['auth']);

Route::post('unapproveAgentKYC', [WalkInCustomerController::class, 'unapproveAgentKYC'])->name('unapproveAgentKYC')->middleware(['auth']);
Route::post('approveAgentKYC', [WalkInCustomerController::class, 'approveAgentKYC'])->name('approveAgentKYC')->middleware(['auth']);

Route::post('unapproveMobileUserKYC', [WalkInCustomerController::class, 'unapproveMobileUserKYC'])->name('unapproveMobileUserKYC')->middleware(['auth']);
Route::post('approveMobileUserKYC', [WalkInCustomerController::class, 'approveMobileUserKYC'])->name('approveMobileUserKYC')->middleware(['auth']);


Route::get('dispatchOrders', [OrderController::class, 'dispatchOrders'])->name('dispatchOrders')->middleware(['auth']);
Route::get('dispatchOrdersList', [OrderController::class, 'dispatchOrdersList'])->name('dispatchOrdersList')->middleware(['auth']);

Route::get('agentsOrders', [OrderController::class, 'agentsOrders'])->name('agentsOrders')->middleware(['auth']);
Route::get('agentsOrdersList', [OrderController::class, 'agentsOrdersList'])->name('agentsOrdersList')->middleware(['auth']);

Route::get('dispatchEUFundOrders', [EUFundTransferOrderController::class, 'dispatchEUFundOrders'])->name('dispatchEUFundOrders')->middleware(['auth']);
Route::get('dispatchEUFundOrdersList', [EUFundTransferOrderController::class, 'dispatchEUFundOrdersList'])->name('dispatchEUFundOrdersList')->middleware(['auth']);

Route::get('dispatchIntlFundOrders', [IntlFundTransferOrderController::class, 'dispatchIntlFundOrders'])->name('dispatchIntlFundOrders')->middleware(['auth']);
Route::get('dispatchIntlFundOrdersList', [IntlFundTransferOrderController::class, 'dispatchIntlFundOrdersList'])->name('dispatchIntlFundOrdersList')->middleware(['auth']);

Route::get('adminEUFundOrders', [EUFundTransferOrderController::class, 'adminEUFundOrders'])->name('adminEUFundOrders')->middleware(['auth']);
Route::get('adminEUFundOrdersList', [EUFundTransferOrderController::class, 'adminEUFundOrdersList'])->name('adminEUFundOrdersList')->middleware(['auth']);

Route::get('adminIntlFundOrders', [IntlFundTransferOrderController::class, 'adminIntlFundOrders'])->name('adminIntlFundOrders')->middleware(['auth']);
Route::get('adminIntlFundOrdersList', [IntlFundTransferOrderController::class, 'adminIntlFundOrdersList'])->name('adminIntlFundOrdersList')->middleware(['auth']);

Route::post('unapproveEUFundTransfer', [EUFundTransferOrderController::class, 'unapproveEUFundTransfer'])->name('unapproveEUFundTransfer')->middleware(['auth']);
Route::post('approveEUFundTransfer', [EUFundTransferOrderController::class, 'approveEUFundTransfer'])->name('approveEUFundTransfer')->middleware(['auth']);

Route::post('unapproveIntlFundTransfer', [IntlFundTransferOrderController::class, 'unapproveIntlFundTransfer'])->name('unapproveIntlFundTransfer')->middleware(['auth']);
Route::post('approveIntlFundTransfer', [IntlFundTransferOrderController::class, 'approveIntlFundTransfer'])->name('approveIntlFundTransfer')->middleware(['auth']);

Route::get('dispatcher/settings', [DispatcherController::class, 'settings'])->name('dispatcher.settings')->middleware(['auth']);
// Route::get('dispatcher/accept', [DispatcherController::class, 'accept'])->name('dispatcher.accept')->middleware(['auth']);
// Route::get('dispatcher/accept-search', [DispatcherController::class, 'accept_search'])->name('dispatcher.accept.search')->middleware(['auth']);

Route::get('createDispatcher', [DispatcherController::class, 'createDispatcher'])->name('create.dispatcher')->middleware(['auth']);

Route::resource('subAdmin', SubAdminController::class)->middleware(['auth']);
Route::get('subAdminList', [SubAdminController::class, 'subAdminList'])->name('subAdminList')->middleware(['auth']);
Route::resource('blogEntry', BlogController::class)->middleware(['auth']);
Route::get('blogList', [BlogController::class, 'blogList'])->name('blogList')->middleware(['auth']);
Route::resource('batches', OrderBatchController::class)->middleware(['auth']);
Route::get('batchList', [OrderBatchController::class, 'batchList'])->name('batchList')->middleware(['auth']);

Route::resource('dispatchers', DispatcherController::class)->middleware(['auth']);
Route::resource('agents', AgentController::class)->middleware(['auth']);

Route::get('agent/accept', [AgentController::class, 'accept'])->name('agent.accept')->middleware(['auth']);
Route::get('agent/accept-search', [AgentController::class, 'accept_search'])->name('agent.accept.search')->middleware(['auth']);

Route::get('agentSetting', [AgentController::class, 'settings'])->name('agent.profile')->middleware(['auth']);
Route::get('editAgentSitting/{id}', [AgentController::class, 'editAgentSitting'])->name('agent.editSitting')->middleware(['auth']);


//paymentRequesr route

Route::get('paymentRequestget', [PaymentRequestController::class, 'index'])->name('paymentRequestget');
Route::post('paymentRequestpost', [PaymentRequestController::class, 'store'])->name('paymentRequestpost');
Route::get('admin-paymentRequestget/{id}', [PaymentRequestController::class, 'adminget'])->name('admin-paymentRequestget');

Route::get('admin-accept-paymentRequest/{id}', [PaymentRequestController::class, 'acceptPaymentRequest'])->name('admin-accept-paymentRequest');
Route::get('admin-reject-paymentRequest/{id}', [PaymentRequestController::class, 'rejectPaymentRequest'])->name('admin-reject-paymentRequest');
Route::get('get-transit', [PaymentRequestController::class, 'getTransit'])->name('get-transit');


Route::get('batchOrdersList/{batch_id}', [OrderBatchController::class, 'batchOrdersList'])->name('batchOrdersList')->middleware(['auth']);
Route::get('batchOrderEdit/{order_id}', [OrderBatchController::class, 'batchOrderEdit'])->name('batchOrderEdit')->middleware(['auth']);
// Route::post('batchOrderEdit/{order_id}', [OrderBatchController::class, 'batchOrderEdit'])->name('batchOrderEdit')->middleware(['auth']);

Route::get('/batches-search', [OrderBatchController::class, 'search'])->name('batches.search')->middleware(['auth']);

Route::get('payment-summary', [PaymentController::class, 'payment_summary'])->middleware(['auth'])->name('payment.summary');
// Route::get('payment-confirm-pay/{order_id}', [PaymentController::class, 'confirm_pay'])->middleware(['auth'])->name('payment.confirm.pay');
Route::get('myPaymentsList', [PaymentController::class, 'myPaymentsList'])->name('myPaymentsList')->middleware(['auth']);
Route::get('my-payments', [PaymentController::class, 'index'])->name('my.payments')->middleware(['auth']);

Route::get('all-payments', [PaymentController::class, 'allPayments'])->name('all.payments')->middleware(['auth']);
Route::get('allPaymentsList', [PaymentController::class, 'allPaymentsList'])->name('allPaymentsList')->middleware(['auth']);


Route::get('/tax-fetch', [WalkInCustomerController::class, 'tax_fetch'])->name('tax.fetch');
Route::resource('walk_in_customer_order', WalkInCustomerOrder::class)->middleware(['auth']);

Route::resource('walkInOrderAgents', WalkInOrderAgents::class)->middleware(['auth']);
Route::post('cancelWalkInAgentOrder', [WalkInOrderAgents::class, 'cancelOrder'])->name('cancelWalkInAgentOrder')->middleware(['auth']);
Route::post('orderAccept/{order_id}', [WalkInOrderAgents::class, 'orderAccept'])->name('orderAccept')->middleware(['auth']);
Route::post('orderPickedUp/{order_id}', [WalkInOrderAgents::class, 'orderPickedUp'])->name('orderPickedUp')->middleware(['auth']);

Route::get('myWalkInOrdersList', [WalkInCustomerOrder::class, 'myOrdersList'])->name('myWalkInOrdersList')->middleware(['auth']);

Route::get('allWalkInOrders', [WalkInCustomerOrder::class, 'allOrders'])->name('allWalkInOrders')->middleware(['auth']);
Route::get('allWalkInOrdersList', [WalkInCustomerOrder::class, 'allOrdersList'])->name('allWalkInOrdersList')->middleware(['auth']);

Route::resource('eu_fund_trasfer_order', EUFundTransferOrderController::class);
Route::resource('intl_fund_trasfer_order', IntlFundTransferOrderController::class);

Route::get('eu_rates_fetch', [EUFundTransferOrderController::class, 'ratesFetch'])->name('eu_rates.fetch');
Route::get('intl_rates_fetch', [IntlFundTransferOrderController::class, 'ratesFetch'])->name('intl_rates.fetch');
Route::get('track-shipping/{tracking_id}', [HomeController::class, 'trackShipping'])->name('track.shipping');
Route::get('track-eufund/{tracking_id}', [HomeController::class, 'trackEUFund'])->name('track.eufund');
Route::get('track-intlfund/{tracking_id}', [HomeController::class, 'trackIntlFund'])->name('track.intlfund');

//fedex
Route::get('fedex-auth', [FedExController::class, 'fedExAuth']);
Route::get('fedex-rate', [FedExController::class, 'estimateCost']);

//global trade products

Route::resource('products', ProductController::class)->middleware(['auth']);

Route::get('showcase', [ProductController::class, 'showcase'])->name('showcase');

Route::get('privacy', [DocumentController::class, 'privacy'])->name('privacy');
Route::get('terms', [DocumentController::class, 'terms'])->name('terms');
Route::get('faqs', [DocumentController::class, 'faqs'])->name('faqs');
Route::get('help', [DocumentController::class, 'help'])->name('help');

Route::get('blogs', [BlogController::class, 'blogs'])->name('blogs');
Route::get('blog-details/{id}', [BlogController::class, 'blogDetails'])->name('blog-details');

Route::post('/inquiries', [InquiryController::class, 'store'])->name('inquiries.store');

// Route::get('/order-bach', function () {
//     return view('emails.order-batch');
// });
//bank details routes

// Route::get('bankdetails-list', [BankDetailsController::class, 'index'])->name('bankdetails-list');
// Route::post('bankdetails-store', [BankDetailsController::class, 'store'])->name('bankdetails-store');
// Route::get('bankdetails-create', [BankDetailsController::class, 'create'])->name('bankdetails-create');
// Route::get('bankdetails-edit', [BankDetailsController::class, 'edit'])->name('bankdetails-edit');
// Route::put('bankdetails-update', [BankDetailsController::class, 'update'])->name('bankdetails-update');
// Route::delete('bankdetails-delete', [BankDetailsController::class, 'destroy'])->name('bankdetails-delete');

Route::resource('bank_details', BankDetailsController::class)->middleware(['auth']);
Route::post('assignBankDetails/{id}', [BankDetailsController::class, 'assignBankDetails'])->name('assignBankDetails');

Route::group(['prefix' => 'contact', 'as' => 'contact.'], function () {
    Route::get('list', [ContactUsController::class, 'index'])->name('list');
    Route::get('show/{id}', [ContactUsController::class, 'show'])->name('show');
    Route::post('save', [ContactUsController::class, 'store'])->name('save');
    Route::delete('destroy', [ContactUsController::class, 'destroy'])->name('destroy');
});

Route::post('ajax-get-cities/{stateId}', [CityController::class, 'getCities'])->name('ajax-get-cities');
Route::post('ajax-get-country-cities/{countryId}', [CityController::class, 'getCountryCities'])->name('ajax-get-country-cities');
Route::post('ajax-get-country-state/{countryId}', [CityController::class, 'getCountryState'])->name('ajax-get-country-state');
Route::get('ajax-edit-countries', [CityController::class, 'editCountriy'])->name('ajax-edit-countries');
Route::get('ajax-get-paced-orders', [CityController::class, 'getOrder'])->name('ajax-get-paced-orders');

Route::post('ajax-get-states/{countryId}', [StateController::class, 'getStates'])->name('ajax-get-states');
Route::get('ajax-get-countries/', [CountryController::class, 'getCountries'])->name('ajax-get-countries');
Route::post('ajax-get-batchlogs', [CityController::class, 'getBatchLogs'])->name('ajax-get-batchlogs');

Route::get('notification', [myController::class, 'notification'])->name('notification');

Route::get('/import-test', function () {
    ShippingCost::truncate();
    set_time_limit(10000);


    $file = public_path('shipping_costs.csv');

    // Open the file for reading
    if (($handle = fopen($file, 'r')) !== false) {
        // Get the first row, which contains the column headers
        $header = fgetcsv($handle, 50000, ';');
        // dump($header);
        $csvData = [];

        while (($row = fgetcsv($handle, 50000, ';')) !== false) {
            // dd($row);

            $csvData[] = array_combine($header, $row);
        }

        fclose($handle);
    }

    foreach ($csvData as $key => $csvs) {
        foreach ($csvs as $c_key => $value) {
            if ($c_key == 'Weight') {
                continue;
            }
            ShippingCost::create([
                'weight' => $csvs['Weight'],
                'country_name' => $c_key,
                'country_iso_2' => substr($c_key, 0, 2),
                'country_id' => Country::where('iso2', substr($c_key, 0, 2))->first()->id,
                'cost' => $value,
            ]);
        }
    }

    return $csvData;
});
