<?php


use App\Http\Controllers\Api\SupportedBanksController;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\CargoHaulageBookingController;
use App\Http\Controllers\Api\CargoDocumentController;
use App\Http\Controllers\Api\CouponController as ApiCouponController;
use App\Http\Controllers\Api\DeliveryRatingController;
use App\Http\Controllers\Api\HelpCenterController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PickupRequestController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\CountriesController;
use App\Http\Controllers\Api\KycController;
use App\Http\Controllers\Api\BeneficiaryController;
use App\Http\Controllers\Api\BeneficiaryAccountController;
use App\Http\Controllers\Api\DeliveryManLocationController;
use App\Http\Controllers\Api\DeliveryManPayoutController;
use App\Http\Controllers\Api\DeliveryTrackingController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VehicleController as ApiVehicleController;
use App\Http\Controllers\Api\VtuController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\ShippingRateController;
use App\Http\Controllers\WalkInCustomerOrder;
use App\Models\City;
use App\Models\State;
use App\Models\SupportedBanks;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CmsController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WalkInOrderAgents;
use App\Http\Controllers\Api\DeliveryManController;
use App\Http\Controllers\Api\FreightQuotationController;
use App\Http\Controllers\Api\LocationShippingRateController;
use App\Http\Controllers\Api\ShippingController;



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

//webservice api
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', function () {
    return response()->json([
        'status' => true,
        'message' => 'SIO mobile API is running.',
        'data' => [
            'auth' => [
                'login' => '/api/auth/login',
                'signup' => '/api/auth/signup',
            ],
            'vtu' => [
                'dashboard' => '/api/vtu/dashboard',
                'services' => '/api/vtu/services',
                'providers' => '/api/vtu/providers?service_type=airtime',
                'products' => '/api/vtu/products?service_type=data&provider_code=mtn',
                'validate_customer' => '/api/vtu/validate-customer',
                'initialize_payment' => '/api/vtu/payments/initialize',
                'purchase' => '/api/vtu/purchase',
                'transactions' => '/api/vtu/transactions',
            ],
            'delivery_man_payouts' => [
                'dashboard' => '/api/admin/delivery-payouts/dashboard',
                'delivery_men' => '/api/admin/delivery-payouts/delivery-men',
                'pending_deliveries' => '/api/admin/delivery-payouts/pending-deliveries',
                'pending_payouts' => '/api/admin/delivery-payouts/pending',
                'make_payout' => '/api/admin/delivery-payouts/make',
                'bulk_payout' => '/api/admin/delivery-payouts/bulk',
                'wallet_balance' => '/api/admin/delivery-payouts/monnify/wallet-balance',
                'history' => '/api/admin/delivery-payouts/history',
            ],
            'delivery_tracking' => [
                'track' => '/api/deliveries/track/{tracking_id}',
                'my_deliveries' => '/api/deliveries',
                'admin_monitor' => '/api/admin/deliveries',
                'assign_delivery_man' => '/api/admin/deliveries/{tracking_id}/assign-delivery-man',
                'update_status' => '/api/deliveries/{tracking_id}/status',
                'logistics_monitor' => '/api/logistics/deliveries',
                'logistics_update_status' => '/api/logistics/deliveries/{tracking_id}/status',
                'internal_tracking_sync' => '/api/internal/tracking/events',
            ],
            'vehicles' => [
                'my_vehicles' => '/api/vehicles/my',
                'locations_search' => '/api/vehicles/locations/search?q=city',
                'rates' => '/api/vehicles/rates?origin_location=&destination_location=&weight=',
            ],
            'pickup_requests' => [
                'create' => '/api/pickup-requests',
            ],
            'cargo_haulage_bookings' => [
                'create' => '/api/cargo-haulage-bookings',
            ],
            'cargo_documents' => [
                'upload' => '/api/cargo-documents',
                'show' => '/api/cargo-documents/{tracking_id}',
            ],
            'location_rate_cargo_bookings' => [
                'create' => '/api/shipping/location-rate/cargo-booking',
            ],
            'freight_quotations' => [
                'create' => '/api/freight-quotations',
                'location_rate_quote' => '/api/shipping/location-rate/freight-quotation',
            ],
        ],
    ]);
});

// Mobile app API created by Prasanna
// Route::get('/cms/{slug}', [CmsController::class, 'show']);
Route::get('/cms/who-we-are', function () {
    return response()->json([
        'slug' => 'who_we_are',
        'content' => view('who_we_are')->render()
    ]);
});

Route::get('/cms/shipping-page', function () {
    return response()->json([
        'slug' => 'shipping_page',
        'content' => view('shipping_page')->render()
    ]);
});

Route::get('/cms/pick-up-point-page', function () {
    return response()->json([
        'slug' => 'pick_up_point_page',
        'content' => view('pickup_point')->render()
    ]);
});

Route::get('/cms/account-remove-info', function () {
    return response()->json([
        'slug' => 'account_remove_info',
        'content' => view('account_remove_info')->render()
    ]);
});

Route::get('privacy', [DocumentController::class, 'privacy']);
Route::get('blogs', [BlogController::class, 'blogs']);
Route::get('terms', [DocumentController::class, 'terms']);
Route::get('showcase', [ProductController::class, 'showcase']);

Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('walk-in-order-agents', WalkInOrderAgents::class);

    Route::post('walk-in-order-agents/{order}/cancel', [WalkInOrderAgents::class, 'cancelOrder']);

    Route::post('walk-in-order-agents/{order}/accept', [WalkInOrderAgents::class, 'orderAccept']);

    Route::post('walk-in-order-agents/{order}/picked-up', [WalkInOrderAgents::class, 'orderPickedUp']);

});

//ends here


Route::get('blogs', [BlogController::class, 'blogs'])->name('blogs');

Route::post('/login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('password-reset-request', [AuthController::class, 'requestPasswordReset']);
Route::post('password-reset-otp', [AuthController::class, 'passwordResetOTP']);
Route::post('password-reset-change', [AuthController::class, 'passwordResetChange']);

Route::get('/countries', [CountriesController::class, 'index']);
Route::get('/receiving-countries', [CountriesController::class, 'receiving']);
Route::get('/sending-countries', [CountriesController::class, 'sending']);
Route::get('/payout-channels', [CountriesController::class, 'getpayoutChannels']);
Route::get('/payout-channels/by-country', [CountriesController::class, 'getpayoutChannelsByCountry']);

Route::get('/help-center/categories', [HelpCenterController::class, 'categories'])->name('help-center.categories');
Route::get('/help-center/faqs', [HelpCenterController::class, 'faqs'])->name('help-center.faqs');

Route::get('/supported-banks', [SupportedBanksController::class, 'index']);
Route::get('/supported-banks/by-country', [SupportedBanksController::class, 'listByCountry']);

Route::post('/webhook/stripe', [WebhookController::class, 'handleStripeWebhook']);
Route::get('deliveries/track/{trackingId}', [DeliveryTrackingController::class, 'track'])->name('deliveries.track');
Route::get('deliveries/track/{trackingId}/status', [DeliveryTrackingController::class, 'status'])->name('deliveries.track.status');
Route::get('deliveries/track/{trackingId}/location', [DeliveryManLocationController::class, 'show'])->name('deliveries.track.location');
Route::get('deliveries/track/{trackingId}/delivery-man', [DeliveryTrackingController::class, 'deliveryManContact'])->name('deliveries.track.delivery-man');
Route::get('internal/tracking/{trackingId}', [DeliveryTrackingController::class, 'internalShow'])->name('internal.tracking.show');
Route::post('internal/tracking/events', [DeliveryTrackingController::class, 'internalSync'])->name('internal.tracking.events');
Route::post('shipping/location-rate/quote', [LocationShippingRateController::class, 'quote'])->name('shipping.location-rate.quote');
Route::post('shipping/location-rate/freight-quotation', [LocationShippingRateController::class, 'freightQuotation'])->name('shipping.location-rate.freight-quotation');
Route::post('tracking-numbers/generate', [LocationShippingRateController::class, 'generateTrackingNumber'])->name('tracking-numbers.generate');

//api endpoint for extimating shipping cost
Route::get('/rates-fetch-api', [ShippingRateController::class, 'rates_fetch_api'])->name('rates.fetch.api');
Route::post('/external-app-order', [WalkInCustomerOrder::class, 'external_app_order'])->name('external.app.order');

Route::prefix('auth')->group(function () {
    Route::post('signup', [ApiAuthController::class, 'signup'])->middleware('throttle:10,1')->name('auth.signup');
    Route::post('login', [ApiAuthController::class, 'login'])->middleware('throttle:10,1')->name('auth.login');
    Route::post('verify-otp', [ApiAuthController::class, 'verifyOtp'])->middleware('throttle:10,1')->name('auth.verify-otp');
    Route::post('resend-otp', [ApiAuthController::class, 'resendOtp'])->middleware('throttle:5,1')->name('auth.resend-otp');
    Route::post('forgot-password', [ApiAuthController::class, 'forgotPassword'])->middleware('throttle:5,1')->name('auth.forgot-password');
    Route::post('reset-password', [ApiAuthController::class, 'resetPassword'])->middleware('throttle:10,1')->name('auth.reset-password');

    Route::middleware('auth.jwt')->group(function () {
        Route::get('me', [ApiAuthController::class, 'me'])->name('auth.me');
        Route::post('logout', [ApiAuthController::class, 'logout'])->name('auth.logout');
        Route::post('refresh', [ApiAuthController::class, 'refresh'])->name('auth.refresh');
        Route::delete('delete-account', [ApiAuthController::class, 'deleteAccount'])->name('auth.delete-account');
    });
});

Route::post('payments/webhook', [PaymentController::class, 'webhook'])->name('payments.webhook');

Route::middleware('auth.jwt')->prefix('vtu')->name('vtu.')->group(function () {
    Route::get('dashboard', [VtuController::class, 'dashboard'])->name('dashboard');
    Route::get('services', [VtuController::class, 'services'])->name('services');
    Route::get('providers', [VtuController::class, 'providers'])->name('providers');
    Route::get('products', [VtuController::class, 'products'])->name('products');
    Route::post('validate-customer', [VtuController::class, 'validateCustomer'])->name('validate-customer');
    Route::post('payments/initialize', [VtuController::class, 'initializePayment'])->name('payments.initialize');
    Route::post('purchase', [VtuController::class, 'purchase'])->name('purchase');
    Route::get('transactions', [VtuController::class, 'transactions'])->name('transactions');
    Route::get('transactions/{reference}', [VtuController::class, 'show'])->name('transactions.show');
    Route::post('transactions/{reference}/refresh', [VtuController::class, 'refresh'])->name('transactions.refresh');
});

Route::middleware('auth.jwt')->group(function () {

    Route::post('/calculate-shipping', [ShippingController::class, 'calculateShipping']);
    Route::post('/available-delivery-men', [DeliveryManController::class, 'availableDeliveryMen']);
    Route::post('/freight-quotations', [FreightQuotationController::class, 'store'])->name('freight-quotations.store');
    Route::post('shipping/location-rate/cargo-booking', [LocationShippingRateController::class, 'bookCargo'])->name('shipping.location-rate.cargo-booking');
    Route::post('/pickup-requests', [PickupRequestController::class, 'store'])->name('pickup-requests.store');
    Route::post('/cargo-haulage-bookings', [CargoHaulageBookingController::class, 'store'])->name('cargo-haulage-bookings.store');
    Route::post('/cargo-documents', [CargoDocumentController::class, 'upload'])->name('cargo-documents.upload');
    Route::get('/cargo-documents/{trackingId}', [CargoDocumentController::class, 'show'])->name('cargo-documents.show');
    Route::post('payments/initialize', [PaymentController::class, 'initialize'])->name('payments.initialize');
    Route::get('payments/verify/{paymentReference}', [PaymentController::class, 'verify'])->name('payments.verify');
    Route::get('payments/{paymentReference}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');

    Route::get('coupons', [ApiCouponController::class, 'index'])->name('api.coupons.index');
    Route::get('drivers/nearby', [DeliveryManLocationController::class, 'nearby'])->name('drivers.nearby');
    Route::get('drivers/{deliveryManId}/ratings', [DeliveryRatingController::class, 'deliveryManRatings'])->name('drivers.ratings');
    Route::get('deliveries/{trackingId}/rating', [DeliveryRatingController::class, 'show'])->name('deliveries.rating.show');
    Route::post('deliveries/{trackingId}/rating', [DeliveryRatingController::class, 'store'])->name('deliveries.rating.store');

    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::match(['put', 'patch'], 'profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::match(['put', 'patch'], 'profile/preferred-currency', [ProfileController::class, 'updatePreferredCurrency'])->name('profile.preferred-currency');
    Route::get('profile/addresses', [ProfileController::class, 'addresses'])->name('profile.addresses.index');
    Route::post('profile/addresses', [ProfileController::class, 'storeAddress'])->name('profile.addresses.store');
    Route::match(['put', 'patch'], 'profile/addresses/{id}', [ProfileController::class, 'updateAddress'])->name('profile.addresses.update');
    Route::delete('profile/addresses/{id}', [ProfileController::class, 'destroyAddress'])->name('profile.addresses.destroy');
    Route::post('profile/addresses/{id}/default', [ProfileController::class, 'setDefaultAddress'])->name('profile.addresses.default');

    Route::get('deliveries', [DeliveryTrackingController::class, 'mine'])->name('deliveries.mine');
    Route::post('deliveries/location', [DeliveryManLocationController::class, 'update'])->name('deliveries.location.update');
    Route::post('deliveries/{trackingId}/accept', [DeliveryTrackingController::class, 'accept'])->name('deliveries.accept');
    Route::post('deliveries/{trackingId}/reject', [DeliveryTrackingController::class, 'reject'])->name('deliveries.reject');
    Route::post('deliveries/{trackingId}/status', [DeliveryTrackingController::class, 'updateStatus'])->name('deliveries.status');
    Route::get('vehicles/locations/search', [ApiVehicleController::class, 'searchLocations'])->name('api.vehicles.locations.search');
    Route::get('vehicles/rates', [ApiVehicleController::class, 'rates'])->name('vehicles.rates');
    Route::get('vehicles/my', [ApiVehicleController::class, 'mine'])->name('vehicles.mine');

    Route::prefix('logistics/deliveries')->name('logistics.deliveries.')->group(function () {
        Route::get('/', [DeliveryTrackingController::class, 'logisticsIndex'])->name('index');
        Route::get('{trackingId}', [DeliveryTrackingController::class, 'logisticsShow'])->name('show');
        Route::post('{trackingId}/status', [DeliveryTrackingController::class, 'logisticsUpdateStatus'])->name('status');
    });

    Route::prefix('admin/delivery-payouts')->name('admin.delivery-payouts.')->group(function () {
        Route::get('dashboard', [DeliveryManPayoutController::class, 'dashboard'])->name('dashboard');
        Route::get('delivery-men', [DeliveryManPayoutController::class, 'deliveryMen'])->name('delivery-men');
        Route::get('pending-deliveries', [DeliveryManPayoutController::class, 'pendingDeliveries'])->name('pending-deliveries');
        Route::get('pending', [DeliveryManPayoutController::class, 'pendingPayouts'])->name('pending');
        Route::post('make', [DeliveryManPayoutController::class, 'makePayout'])->name('make');
        Route::post('bulk', [DeliveryManPayoutController::class, 'bulkPayout'])->name('bulk');
        Route::post('mark-deliveries-paid', [DeliveryManPayoutController::class, 'markDeliveriesPaid'])->name('mark-deliveries-paid');
        Route::get('history', [DeliveryManPayoutController::class, 'history'])->name('history');
        Route::get('monnify/wallet-balance', [DeliveryManPayoutController::class, 'walletBalance'])->name('monnify.wallet-balance');
        Route::get('{reference}', [DeliveryManPayoutController::class, 'show'])->name('show');
        Route::get('{reference}/logs', [DeliveryManPayoutController::class, 'logs'])->name('logs');
        Route::post('{reference}/authorize-monnify', [DeliveryManPayoutController::class, 'authorizeMonnify'])->name('authorize-monnify');
        Route::post('{reference}/resend-monnify-otp', [DeliveryManPayoutController::class, 'resendMonnifyOtp'])->name('resend-monnify-otp');
        Route::post('{reference}/refresh-monnify', [DeliveryManPayoutController::class, 'refreshMonnify'])->name('refresh-monnify');
        Route::post('{reference}/mark-paid', [DeliveryManPayoutController::class, 'markPaid'])->name('mark-paid');
        Route::post('{reference}/mark-failed', [DeliveryManPayoutController::class, 'markFailed'])->name('mark-failed');
        Route::post('{reference}/cancel', [DeliveryManPayoutController::class, 'cancel'])->name('cancel');
    });

    Route::prefix('admin/deliveries')->name('admin.deliveries.')->group(function () {
        Route::get('/', [DeliveryTrackingController::class, 'adminIndex'])->name('index');
        Route::get('{trackingId}', [DeliveryTrackingController::class, 'adminShow'])->name('show');
        Route::post('{trackingId}/assign-delivery-man', [DeliveryTrackingController::class, 'assignDeliveryMan'])->name('assign-delivery-man');
        Route::post('{trackingId}/status', [DeliveryTrackingController::class, 'updateStatus'])->name('status');
    });
});

Route::group(['middleware' => ['jwt.auth']], function () {
    Route::get('/user-kyc-status/{userId}', [UserController::class, 'getKycInfo']);

    Route::get('/kyc/document-types', [KycController::class, 'documentTypes']);
    Route::get('/kyc/proof-of-address-types', [KycController::class, 'addressProofTypes']);
    Route::post('/kyc/personal-information', [KycController::class, 'submitPersonalInformation']);
    Route::post('/kyc/document-verification', [KycController::class, 'submitDocumentVerification']);
    Route::post('/kyc/selfie', [KycController::class, 'submitSelfie']);
    Route::post('/kyc/proof-of-address', [KycController::class, 'submitProofOfAddress']);


    Route::middleware(['kyc.required'])->group(function () {
        Route::post('/beneficiary/add-new-beneficiary', [BeneficiaryController::class, 'store']);
        Route::get('/beneficiary/search-beneficiaries', [BeneficiaryController::class, 'search']);
        Route::get('/beneficiary/get-beneficiaries', [BeneficiaryController::class, 'index']);

        Route::post('/beneficiary/add-beneficiary-account', [BeneficiaryAccountController::class, 'store']);
        Route::get('/beneficiaries/bank-accounts', [BeneficiaryAccountController::class, 'index']);
        Route::delete('/beneficiaries/bank-accounts/{account_id}', [BeneficiaryAccountController::class, 'delete']);


        Route::post('/transfer-money', [TransactionController::class, 'store']);
        Route::get('/get-transactions', [TransactionController::class, 'index']);

        // Route::get('/receiving-countries', [CountriesController::class, 'receiving']);
        // Route::get('/sending-countries', [CountriesController::class, 'sending']);

        Route::post('/create-payment-intent', [TransferController::class, 'createPaymentIntent']);
        Route::post('/create-transaction', [TransactionController::class, 'createPaymentIntent']);

        Route::post('/beneficiaries', [BeneficiaryController::class, 'create'])->name('beneficiaries.post');
        Route::put('/beneficiaries/{beneficiaryId}', [BeneficiaryController::class, 'updateBeneficiary'])->name('beneficiaries.put');
        Route::get('/beneficiaries', [BeneficiaryController::class, 'getBeneficiaries'])->name('beneficiaries.get');
        Route::delete('/beneficiaries/{beneficiaryId}', [BeneficiaryController::class, 'deleteBeneficiary']);
    });
});


Route::get('/test', function () {
    // $state = State::find(1665);
    $state = State::find(1666);

    // $cities = [
    //     ['city' => 'Agna', 'lat' => 45.1453, 'lng' => 12.0557],
    //     ['city' => 'Albignasego', 'lat' => 45.3439, 'lng' => 11.8712],
    //     ['city' => 'Anguillara Veneta', 'lat' => 45.1603, 'lng' => 11.7877],
    //     ['city' => 'Arre', 'lat' => 45.1988, 'lng' => 11.9369],
    //     ['city' => 'Arzergrande', 'lat' => 45.2719, 'lng' => 12.0292],
    //     ['city' => 'Bagnoli di Sopra', 'lat' => 45.1631, 'lng' => 11.8755],
    //     ['city' => 'Baone', 'lat' => 45.2421, 'lng' => 11.6638],
    //     ['city' => 'Barbona', 'lat' => 45.1282, 'lng' => 11.6344],
    //     ['city' => 'Battaglia Terme', 'lat' => 45.2776, 'lng' => 11.7842],
    //     ['city' => 'Boara Pisani', 'lat' => 45.0652, 'lng' => 11.7603],
    //     ['city' => 'Borgoricco', 'lat' => 45.5169, 'lng' => 11.9368],
    //     ['city' => 'Bosaro', 'lat' => 45.0138, 'lng' => 11.7269],
    //     ['city' => 'Cadoneghe', 'lat' => 45.4601, 'lng' => 11.9061],
    //     ['city' => 'Campodarsego', 'lat' => 45.5263, 'lng' => 11.9281],
    //     ['city' => 'Campodoro', 'lat' => 45.5281, 'lng' => 11.7396],
    //     ['city' => 'Camposampiero', 'lat' => 45.5662, 'lng' => 11.9298],
    //     ['city' => 'Campo San Martino', 'lat' => 45.5583, 'lng' => 11.7889],
    //     ['city' => 'Candiana', 'lat' => 45.1784, 'lng' => 11.9614],
    //     ['city' => 'Carceri', 'lat' => 45.1844, 'lng' => 11.5944],
    //     ['city' => 'Carmignano di Brenta', 'lat' => 45.6329, 'lng' => 11.6918],
    //     ['city' => 'Cartura', 'lat' => 45.2552, 'lng' => 11.8359],
    //     ['city' => 'Casale di Scodosia', 'lat' => 45.1772, 'lng' => 11.5276],
    //     ['city' => 'Casalserugo', 'lat' => 45.3014, 'lng' => 11.8942],
    //     ['city' => 'Castelbaldo', 'lat' => 45.1466, 'lng' => 11.4653],
    //     ['city' => 'Cervarese Santa Croce', 'lat' => 45.4645, 'lng' => 11.6782],
    //     ['city' => 'Cinto Euganeo', 'lat' => 45.2885, 'lng' => 11.6343],
    //     ['city' => 'Cittadella', 'lat' => 45.6442, 'lng' => 11.7848],
    //     ['city' => 'Conselve', 'lat' => 45.2332, 'lng' => 11.8721],
    //     ['city' => 'Correzzola', 'lat' => 45.2339, 'lng' => 12.0285],
    //     ['city' => 'Curtarolo', 'lat' => 45.5444, 'lng' => 11.8186],
    //     ['city' => 'Due Carrare', 'lat' => 45.2923, 'lng' => 11.8265],
    //     ['city' => 'Este', 'lat' => 45.2287, 'lng' => 11.6609],
    //     ['city' => 'Fontaniva', 'lat' => 45.6145, 'lng' => 11.7289],
    //     ['city' => 'Galliera Veneta', 'lat' => 45.6479, 'lng' => 11.7695],
    //     ['city' => 'Galzignano Terme', 'lat' => 45.3081, 'lng' => 11.7457],
    //     ['city' => 'Grantorto', 'lat' => 45.5843, 'lng' => 11.6981],
    //     ['city' => 'Granze', 'lat' => 45.1472, 'lng' => 11.7181],
    //     ['city' => 'Legnaro', 'lat' => 45.3439, 'lng' => 11.9588],
    //     ['city' => 'Limena', 'lat' => 45.4796, 'lng' => 11.8538],
    //     ['city' => 'Loreggia', 'lat' => 45.5743, 'lng' => 11.9438],
    //     ['city' => 'Lozzo Atestino', 'lat' => 45.2856, 'lng' => 11.5818],
    //     ['city' => 'Masera di Padova', 'lat' => 45.3214, 'lng' => 11.8632],
    //     ['city' => 'Masi', 'lat' => 45.0902, 'lng' => 11.5119],
    //     ['city' => 'Megliadino San Vitale', 'lat' => 45.1973, 'lng' => 11.5269],
    //     ['city' => 'Merlara', 'lat' => 45.1511, 'lng' => 11.4675],
    //     ['city' => 'Mestrino', 'lat' => 45.4762, 'lng' => 11.7423],
    //     ['city' => 'Monselice', 'lat' => 45.2396, 'lng' => 11.7478],
    //     ['city' => 'Montagnana', 'lat' => 45.2308, 'lng' => 11.4668],
    //     ['city' => 'Montegrotto Terme', 'lat' => 45.3412, 'lng' => 11.7867],
    //     ['city' => 'Noventa Padovana', 'lat' => 45.4206, 'lng' => 11.9376],
    //     ['city' => 'Ospedaletto Euganeo', 'lat' => 45.2185, 'lng' => 11.6005],
    //     ['city' => 'Padova', 'lat' => 45.4064, 'lng' => 11.8768],
    //     ['city' => 'Pernumia', 'lat' => 45.2572, 'lng' => 11.7763],
    //     ['city' => 'Piacenza d\'Adige', 'lat' => 45.1255, 'lng' => 11.5335],
    //     ['city' => 'Piazzola sul Brenta', 'lat' => 45.5487, 'lng' => 11.7877],
    //     ['city' => 'Piombino Dese', 'lat' => 45.5954, 'lng' => 11.9573],
    //     ['city' => 'Polverara', 'lat' => 45.3075, 'lng' => 11.9617],
    //     ['city' => 'Ponso', 'lat' => 45.1844, 'lng' => 11.6463],
    //     ['city' => 'Pontelongo', 'lat' => 45.2722, 'lng' => 12.0327],
    //     ['city' => 'Pozzonovo', 'lat' => 45.1877, 'lng' => 11.7723],
    //     ['city' => 'Rovolon', 'lat' => 45.3948, 'lng' => 11.6761],
    //     ['city' => 'Rubano', 'lat' => 45.4591, 'lng' => 11.8031],
    //     ['city' => 'Saccolongo', 'lat' => 45.4429, 'lng' => 11.7438],
    //     ['city' => 'San Giorgio delle Pertiche', 'lat' => 45.5536, 'lng' => 11.9314],
    //     ['city' => 'San Giorgio in Bosco', 'lat' => 45.6084, 'lng' => 11.7682],
    //     ['city' => 'San Martino di Lupari', 'lat' => 45.6443, 'lng' => 11.8106],
    //     ['city' => 'San Pietro Viminario', 'lat' => 45.2281, 'lng' => 11.7911],
    //     ['city' => 'Santa Giustina in Colle', 'lat' => 45.5761, 'lng' => 11.8753],
    //     ['city' => 'Sant\'Angelo di Piove di Sacco', 'lat' => 45.3353, 'lng' => 11.9566],
    //     ['city' => 'Sant\'Elena', 'lat' => 45.1933, 'lng' => 11.6762],
    //     ['city' => 'Selvazzano Dentro', 'lat' => 45.4109, 'lng' => 11.7965],
    //     ['city' => 'Solesino', 'lat' => 45.1756, 'lng' => 11.7238],
    //     ['city' => 'Stanghella', 'lat' => 45.1234, 'lng' => 11.7327],
    //     ['city' => 'Teolo', 'lat' => 45.3585, 'lng' => 11.6881],
    //     ['city' => 'Terrassa Padovana', 'lat' => 45.2365, 'lng' => 11.8816],
    //     ['city' => 'Tombolo', 'lat' => 45.6331, 'lng' => 11.8004],
    //     ['city' => 'Torreglia', 'lat' => 45.3263, 'lng' => 11.7443],
    //     ['city' => 'Trebaseleghe', 'lat' => 45.5707, 'lng' => 12.0123],
    //     ['city' => 'Tribano', 'lat' => 45.2291, 'lng' => 11.8238],
    //     ['city' => 'Urbana', 'lat' => 45.1759, 'lng' => 11.4622],
    //     ['city' => 'Veggiano', 'lat' => 45.4637, 'lng' => 11.7267],
    //     ['city' => 'Vescovana', 'lat' => 45.1289, 'lng' => 11.7243],
    //     ['city' => 'Vighizzolo d\'Este', 'lat' => 45.1633, 'lng' => 11.6354],
    //     ['city' => 'Vigodarzere', 'lat' => 45.4601, 'lng' => 11.8866],
    //     ['city' => 'Vigonza', 'lat' => 45.4327, 'lng' => 11.9454],
    //     ['city' => 'Villa Estense', 'lat' => 45.1837, 'lng' => 11.5875],
    //     ['city' => 'Villafranca Padovana', 'lat' => 45.5011, 'lng' => 11.7867],
    //     ['city' => 'Villanova di Camposampiero', 'lat' => 45.5077, 'lng' => 11.9537],
    //     ['city' => 'Vo\'', 'lat' => 45.3232, 'lng' => 11.6502]
    // ];
    
    foreach ($cities as $key => $value) {
        City::create([
            'name' => $value['city'],
            'state_id' => $state->id,
            'state_code' => $state->country_code,
            'country_id' => $state->country_id,
            'country_code' => $state->country_code,
            'latitude' => $value['lat'],
            'longitude' => $value['lng'],
            'created_at' => now(),
            'updated_at' => now(),
            'flag' => 1,
            'wikiDataId' => $state->wikiDataId ?? null,
        ]);
    }

    return "Success";
});
