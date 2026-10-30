<?php

namespace App\Http\Controllers;

use App\Constraints\UserTypes;
use App\Constraints\DeliveryManStatus;
use App\Constraints\DeliveryTrackingStatus;
use App\Constraints\OrderStatus;
use App\Models\ContactUs;
use App\Models\DeliveryManPayout;
use App\Models\DeliveryManPayoutItem;
use App\Models\DeliveryManPayoutLog;
use App\Models\DeliveryTrackingEvent;
use App\Models\EUFundsTransferRates;
use App\Models\EUFundTransferOrder;
use App\Models\IntlFundsTransferRates;
use App\Models\IntlFundTransferOrder;
use App\Models\Vehicle;
use App\Models\VtuTransaction;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Models\UserFunds;
use App\Models\WalkInCustomer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['landing', 'trackShipping']);
        // dd(App::currentLocale());
    }

    public function adminReports()
    {
        $rep = [];
        // $rep['total_users'] = User::count();
        // $rep['total_blocked_users'] = User::where('blocked', true)->count();

        // $rep['total_customers'] = WalkInCustomer::count();
        // $rep['total_approved_users'] = WalkInCustomer::where('kyc_status', 'approved')->count();

        // $rep['total_orders'] = Order::count();
        // $rep['total_unpaid_orders'] = Order::where('status', 'unpaid')->count();
        // $rep['totsl_paid_orders'] = Order::where('status', '!=', 'unpaid')->count();
        // $rep['total_in_transit_orders'] = Order::where('status', 'in_transit')->count();
        // $rep['total_delivered_orders'] = Order::where('status', 'delivered')->count();
        // $rep['total_cancelled_orders'] = Order::where('status', 'cancelled')->count();
        // $rep['total_picked_up_orders'] = Order::where('status', 'picked_up')->count();

        // $rep['total_payments'] = Payment::where('status', 'done')->count();
        // $rep['total_payments_pending'] = Payment::where('status', 'pending')->count();
        // $rep['total_payments_failed'] = Payment::where('status', 'failed')->count();
        // $rep['total_payments_value'] = Payment::where('status', 'done')->sum('amt_paid');

        // $rep['total_eu_funds'] = EUFundTransferOrder::count();
        // $rep['total_eu_funds_done'] = EUFundTransferOrder::where('tx_status', 'done')->count();
        // $rep['total_eu_funds_rejected'] = EUFundTransferOrder::where('tx_status', 'rejected')->count();
        // $rep['total_eu_funds_value'] = EUFundTransferOrder::where('tx_status', 'done')->sum('s_amount');

        // $rep['total_intl_funds'] = IntlFundTransferOrder::count();
        // $rep['total_intl_funds_done'] = IntlFundTransferOrder::where('tx_status', 'done')->count();
        // $rep['total_intl_funds_rejected'] = IntlFundTransferOrder::where('tx_status', 'rejected')->count();
        // $rep['total_intl_funds_value'] = IntlFundTransferOrder::where('tx_status', 'done')->sum('s_amount');

        // $rep = [];
        $rep['total_orders'] = Order::count();
        $rep['total_in_transit_orders'] = Order::where('status', 'in_transit')->count();
        $rep['total_delivered_orders'] = Order::where('status', 'delivered')->count();
        $rep['total_placed_orders'] = Order::where('status', 'placed')->count();
        $rep['total_cancelled_orders'] = Order::where('status', 'cancelled')->count();
        $rep['total_assigned_orders'] = Order::where('status', 'assigned')->count();
        $rep['totalWalletAmout'] = UserFunds::where('flag', 'debit')->where('user_id', Auth::user()->id)->sum('amount');
        $rep['toatalSpendAmount'] = UserFunds::where('flag', 'credit')->where('user_id', Auth::user()->id)->sum('amount');
        $rep['total_unpaid_orders'] = Order::where('status', 'unpaid')->count();
        $rep['total_today_spent'] = UserFunds::where('flag', 'credit')->whereDate('created_at', Carbon::today())->sum('amount');
        $get_customer = Order::pluck('customer_id')->toArray();
        $rep['customer'] = count(array_unique($get_customer));
        $rep['total_sales'] = Order::sum('val_of_goods');
        $rep['total_users'] = User::where('blocked', false)->count('name');
        
        $customer_ids = Order::pluck('customer_id')->toArray();
        $rep['customers_list'] = WalkInCustomer::whereIn('id', $customer_ids)->orderBy('id', 'DESC')->paginate(10);
        $rep['latest_orders'] = Order::orderBy('id', 'DESC')->paginate(20);
        $rep['contacts'] = ContactUs::orderBy('id', 'DESC')->paginate(5);
        // dd($rep['contacts']);
        // $rep['contacts'] = Order::
        $rep['total_transit'] = UserFunds::get();
        return $rep;
    }

    public function adminDashboard()
    {
        $dashboard = [
            'users' => [
                'total' => User::count(),
                'active' => User::where('blocked', false)->count(),
                'blocked' => User::where('blocked', true)->count(),
                'delivery_men' => User::whereIn('user_type', ['dispatcher', 'courier'])->count(),
                'customers' => User::where('user_type', 'user')->count(),
                'admins' => User::where('user_type', 'admin')->count(),
                'new_today' => User::whereDate('created_at', Carbon::today())->count(),
            ],
            'vehicles' => $this->vehicleDashboard(),
            'vtu' => $this->vtuDashboard(),
            'deliveries' => $this->deliveryDashboard(),
            'payouts' => $this->payoutDashboard(),
            'reports' => $this->reportsDashboard(),
            'logs' => $this->logsDashboard(),
        ];

        return $dashboard;
    }

    protected function vehicleDashboard(): array
    {
        if (! Schema::hasTable('vehicles')) {
            return [
                'total' => 0,
                'active' => 0,
                'assigned' => 0,
                'unassigned' => 0,
                'bike' => 0,
                'car' => 0,
                'van' => 0,
                'truck' => 0,
                'recent' => collect(),
            ];
        }

        return [
            'total' => Vehicle::count(),
            'active' => Vehicle::where('status', Vehicle::STATUS_ACTIVE)->count(),
            'assigned' => Vehicle::whereNotNull('delivery_man_id')->count(),
            'unassigned' => Vehicle::whereNull('delivery_man_id')->count(),
            'bike' => Vehicle::where('type', Vehicle::TYPE_BIKE)->count(),
            'car' => Vehicle::where('type', Vehicle::TYPE_CAR)->count(),
            'van' => Vehicle::where('type', Vehicle::TYPE_VAN)->count(),
            'truck' => Vehicle::where('type', Vehicle::TYPE_TRUCK)->count(),
            'recent' => Vehicle::with('deliveryMan')->latest()->limit(5)->get(),
        ];
    }

    protected function vtuDashboard(): array
    {
        if (! Schema::hasTable('vtu_transactions')) {
            return [
                'total' => 0,
                'successful' => 0,
                'pending' => 0,
                'failed' => 0,
                'amount' => 0,
                'today' => 0,
                'recent' => collect(),
            ];
        }

        return [
            'total' => VtuTransaction::count(),
            'successful' => VtuTransaction::where('status', VtuTransaction::STATUS_SUCCESSFUL)->count(),
            'pending' => VtuTransaction::whereIn('status', [
                VtuTransaction::STATUS_PENDING,
                VtuTransaction::STATUS_PROCESSING,
            ])->count(),
            'failed' => VtuTransaction::where('status', VtuTransaction::STATUS_FAILED)->count(),
            'amount' => VtuTransaction::where('status', VtuTransaction::STATUS_SUCCESSFUL)->sum('amount'),
            'today' => VtuTransaction::whereDate('created_at', Carbon::today())->count(),
            'recent' => VtuTransaction::with('user')->latest()->limit(5)->get(),
        ];
    }

    protected function deliveryDashboard(): array
    {
        $summary = [];

        foreach (DeliveryTrackingStatus::values() as $status) {
            $query = Order::query();
            $this->applyDeliveryStatusFilter($query, $status);
            $summary[$status] = $query->count();
        }

        return [
            'total' => Order::count(),
            'today' => Order::whereDate('created_at', Carbon::today())->count(),
            'pending' => $summary[DeliveryTrackingStatus::PENDING] ?? 0,
            'accepted' => $summary[DeliveryTrackingStatus::ACCEPTED] ?? 0,
            'on_the_way' => $summary[DeliveryTrackingStatus::ON_THE_WAY] ?? 0,
            'delivered' => $summary[DeliveryTrackingStatus::DELIVERED] ?? 0,
            'recent' => Order::with('deliveryMan')->latest()->limit(6)->get(),
        ];
    }

    protected function payoutDashboard(): array
    {
        if (! Schema::hasTable('delivery_man_payouts')) {
            return [
                'total' => 0,
                'pending' => 0,
                'processing' => 0,
                'paid' => 0,
                'failed' => 0,
                'pending_amount' => 0,
                'paid_amount' => 0,
                'recent' => collect(),
            ];
        }

        return [
            'total' => DeliveryManPayout::count(),
            'pending' => DeliveryManPayout::where('status', DeliveryManPayout::STATUS_PENDING)->count(),
            'processing' => DeliveryManPayout::where('status', DeliveryManPayout::STATUS_PROCESSING)->count(),
            'paid' => DeliveryManPayout::where('status', DeliveryManPayout::STATUS_PAID)->count(),
            'failed' => DeliveryManPayout::where('status', DeliveryManPayout::STATUS_FAILED)->count(),
            'pending_amount' => DeliveryManPayout::whereIn('status', [
                DeliveryManPayout::STATUS_PENDING,
                DeliveryManPayout::STATUS_PROCESSING,
            ])->sum('total_amount'),
            'paid_amount' => Schema::hasTable('delivery_man_payout_items')
                ? DeliveryManPayoutItem::where('status', DeliveryManPayoutItem::STATUS_PAID)->sum('amount')
                : 0,
            'recent' => DeliveryManPayout::with('deliveryMan')->latest()->limit(5)->get(),
        ];
    }

    protected function reportsDashboard(): array
    {
        return [
            'contacts' => Schema::hasTable('contact_us') ? ContactUs::count() : 0,
            'orders_value' => Order::sum('val_of_goods'),
            'shipping_revenue' => Order::sum('shipping_cost'),
            'payments_done' => Schema::hasTable('payments') ? Payment::where('status', 'done')->count() : 0,
            'payments_value' => Schema::hasTable('payments') ? Payment::where('status', 'done')->sum('amt_paid') : 0,
            'latest_orders' => Order::with('deliveryMan')->latest()->limit(5)->get(),
            'latest_contacts' => Schema::hasTable('contact_us') ? ContactUs::latest()->limit(5)->get() : collect(),
        ];
    }

    protected function logsDashboard(): array
    {
        return [
            'tracking_events' => Schema::hasTable('delivery_tracking_events') ? DeliveryTrackingEvent::count() : 0,
            'payout_logs' => Schema::hasTable('delivery_man_payout_logs') ? DeliveryManPayoutLog::count() : 0,
            'latest_tracking_events' => Schema::hasTable('delivery_tracking_events')
                ? DeliveryTrackingEvent::with(['deliveryMan', 'performedBy'])->latest()->limit(5)->get()
                : collect(),
            'latest_payout_logs' => Schema::hasTable('delivery_man_payout_logs')
                ? DeliveryManPayoutLog::with('performedBy')->latest()->limit(5)->get()
                : collect(),
        ];
    }

    protected function applyDeliveryStatusFilter(Builder $query, string $status): void
    {
        $query->where(function (Builder $query) use ($status) {
            if ($status === DeliveryTrackingStatus::DELIVERED) {
                $query->where('delivery_man_status', DeliveryManStatus::DELIVERED)
                    ->orWhereIn('status', [OrderStatus::DELIVERED, OrderStatus::CUSTOMER_RECEIVED]);
            } elseif ($status === DeliveryTrackingStatus::ON_THE_WAY) {
                $query->where('delivery_man_status', DeliveryManStatus::ON_THE_WAY)
                    ->orWhere('delivery_man_status', DeliveryManStatus::ENGAGE_WITH_PARCEL)
                    ->orWhere('status', OrderStatus::IN_TRANSIT);
            } elseif ($status === DeliveryTrackingStatus::ACCEPTED) {
                $query->where('delivery_man_status', DeliveryManStatus::ACCEPTED)
                    ->orWhereIn('status', [OrderStatus::ASSIGNED, OrderStatus::IN_DELIVERY_MAN]);
            } else {
                $query->whereNull('dispatcher_id')
                    ->orWhereIn('status', [OrderStatus::UNPAID, OrderStatus::PLACED, OrderStatus::PROCESSING, OrderStatus::NEW_ORDER]);
            }
        });
    }

    public function customerReports()
    {
        $rep = [];
        if(Auth::user()->customer){
        $rep['total_orders'] = Order::where('customer_id', Auth::user()->customer->id)->count();
        $rep['total_unpaid_orders'] = Order::where('status', 'unpaid')->where('customer_id', Auth::user()->customer->id)->count();
        $rep['totsl_paid_orders'] = Order::where('status', '!=', 'unpaid')->where('customer_id', Auth::user()->customer->id)->count();
        $rep['total_in_transit_orders'] = Order::where('status', 'in_transit')->where('customer_id', Auth::user()->customer->id)->count();
        $rep['total_delivered_orders'] = Order::where('status', 'delivered')->where('customer_id', Auth::user()->customer->id)->count();
        $rep['total_cancelled_orders'] = Order::where('status', 'cancelled')->where('customer_id', Auth::user()->customer->id)->count();

        $rep['total_payments'] = Payment::where('status', 'done')->where('customer_id', Auth::user()->customer->id)->count();
        $rep['total_payments_pending'] = Payment::where('status', 'pending')->where('customer_id', Auth::user()->customer->id)->count();
        $rep['total_payments_failed'] = Payment::where('status', 'failed')->where('customer_id', Auth::user()->customer->id)->count();
        $rep['total_payments_value'] = Payment::where('status', 'done')->where('customer_id', Auth::user()->customer->id)->sum('amt_paid');
        }
        return $rep;
    }

    public function agentReports()
    {
        $rep = [];
        $rep['total_orders'] = Order::where('agent_id', Auth::user()->id)->count();
        $rep['total_in_transit_orders'] = Order::where('status', 'in_transit')->where('agent_id', Auth::user()->id)->count();
        $rep['total_delivered_orders'] = Order::where('status', 'delivered')->where('agent_id', Auth::user()->id)->count();
        $rep['total_placed_orders'] = Order::where('status', 'placed')->where('agent_id', Auth::user()->id)->count();
        $rep['total_cancelled_orders'] = Order::where('status', 'cancelled')->where('agent_id', Auth::user()->id)->count();
        $rep['total_assigned_orders'] = Order::where('status', 'assigned')->where('agent_id', Auth::user()->id)->count();
        $rep['total_unpaid_orders'] = Order::where('status', 'unpaid')->where('agent_id', Auth::user()->id)->count();
        $rep['totalWalletAmout'] = UserFunds::where('flag', 'debit')->where('user_id', Auth::user()->id)->sum('amount');
        $rep['toatalSpendAmount'] = UserFunds::where('flag', 'credit')->where('user_id', Auth::user()->id)->sum('amount');

        $rep['total_today_spent'] = UserFunds::where('flag', 'credit')->where('user_id', Auth::user()->id)->whereDate('created_at', Carbon::today())->sum('amount');
        $get_customer = Order::where('agent_id', Auth::user()->id)->pluck('customer_id')->toArray();
        $rep['customer'] = count(array_unique($get_customer));
        $rep['total_sales'] = Order::where('agent_id', Auth::user()->id)->sum('val_of_goods');
        // dd($totalWalletAmout);
        $customer_ids = Order::where('agent_id', Auth::user()->id)->pluck('customer_id')->toArray();
        $rep['customers_list'] = WalkInCustomer::whereIn('id', $customer_ids)->orderBy('id', 'DESC')->paginate(10);
        $rep['latest_orders'] = Order::orderBy('id', 'DESC')->paginate(10);

        //total transit 
        $rep['total_transit'] = UserFunds::get();
        return $rep;
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $admin_rep = [];
        $dispatcher_rep = [];
        $user_rep = [];
        $totalUsers = 0;
        $totalDispatures = 0;
        $totalAgents = 0;
        if (Auth::user()->user_type == 'admin') {
            $dispatcher_rep = $this->adminReports();
            $admin_dashboard = $this->adminDashboard();
            $totalUsers = User::where('user_type', '!=', 'admin')->count();
            $totalDispatures = User::where('user_type', 'dispatcher')->count();
            $totalAgents = User::where('user_type', 'agnet')->count();
        }
         else if (Auth::user()->user_type == UserTypes::SUB_ADMIN) {
            $dispatcher_rep = [];
            // $user_rep = $this->customerReports();
        }
         else if (Auth::user()->user_type == 'agent') {
            $dispatcher_rep = $this->agentReports();
            // $user_rep = $this->customerReports();
        }
         else {
            $user_rep = $this->customerReports();
        }
        return view('home', [
            'totalUsers' => $totalUsers,
             'totalDispatures' => $totalDispatures,
              'totalAgents' => $totalAgents,
               'dispatcher_rep' => $dispatcher_rep,
               'admin_dashboard' => $admin_dashboard ?? null]);
    }

    public function landing(Request $request)
    {
        $p = Product::limit(10)->get();
        if (!empty($request->s_country_eu) && !empty($request->rx_country_eu)) {
            $rate = EUFundsTransferRates::where('s_country_eu', $request->s_country_eu)
                ->where('rx_country_eu', $request->rx_country_eu)
                ->where('min_amt', '<=', $request->rx_amount_eu)
                ->where('max_amt', '>=', $request->rx_amount_eu)
                ->where('status', 1)->first();

            return view('welcome', ['eu_rate' => $rate]);
        } else if (!empty($request->s_country) && !empty($request->rx_country)) {
            $rate = IntlFundsTransferRates::where('s_country', $request->s_country)
                ->where('rx_country', $request->rx_country)
                ->where('min_amt', '<=', $request->rx_amount)
                ->where('max_amt', '>=', $request->rx_amount)
                ->where('status', 1)->first();


            return view('welcome', ['global_rate' => $rate, 'products' => $p]);
        } else {
            return view('welcome', ['products' => $p]);
        }
    }

    public function trackShipping($tracking_id)
    {
        $r = Order::where('tracking_id', $tracking_id)->first();
        return view('shiping-track', ['order' => $r]);
    }

    public function allNotifications()
    {
        $user = Auth::user();
        $notifications = $user->unreadNotifications;
        return response()->json([
            'status' => true,
            'notifications' => $notifications,
            'count' => count($notifications)
        ]);
    }

    public function markAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        return response()->json([
            'status' => true,
            'messges' => "All Notifications are Marked"
        ]);
    }
}
