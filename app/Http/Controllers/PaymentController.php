<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Payment;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Stripe;

use App\Mail\PaymentEmail;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{

    public function geocodeAddress($postAddress)
    {
        // Replace with your Google Maps Geocoding API key
        $apiKey = env('GOOGLE_MAP_API_KEY');

        // Geocode the office postcode
        $officeResponse = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => $postAddress,
            'key' => $apiKey,
        ]);

        $officeData = $officeResponse->json();
        // dd($officeData);

        if ($officeResponse->failed() || $officeData['status'] !== 'OK' || empty($officeData['results'])) {
            // Handle geocoding error
            return null;
        }

        // Get the latitude and longitude of the office
        $officeLat = $officeData['results'][0]['geometry']['location']['lat'];
        $officeLng = $officeData['results'][0]['geometry']['location']['lng'];

        return ['lat' => $officeLat, 'lng' => $officeLng];
    }

    public function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the Earth in kilometers

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $latDiff = $lat2 - $lat1;
        $lonDiff = $lon2 - $lon1;

        $a = sin($latDiff / 2) ** 2 + cos($lat1) * cos($lat2) * sin($lonDiff / 2) ** 2;
        $c = 2 * asin(sqrt($a));

        $distance = $earthRadius * $c; // Distance in kilometers

        return $distance;
    }

    /**
     * the payment summary page
     */

    public function payment_summary(Request $request)
    {
        $request->validate([
            'order_id' => 'required'
        ]);

        try {
            $o = Order::where('id', $request->order_id)->first();

            if ($o) {
                // Example usage
                // $pickupPostAddress = $o->pickup_address1 . ', ' . $o->pickup_city . ', ' . $o->pickup_zip . ', ' . $o->pickup_country;
                // $deliveryPostAddress = $o->delivery_address1 . ', ' . $o->delivery_city . ', ' . $o->delivery_zip . ', ' . $o->delivery_country;

                // $pickup_pos = $this->geocodeAddress($pickupPostAddress);
                // $delivery_pos = $this->geocodeAddress($deliveryPostAddress);

                // // dd($pickup_pos);
                // $pickup_distance = $this->haversineDistance($pickup_pos['lat'], $pickup_pos['lng'], $o->pickup_location->latitude, $o->pickup_location->longitude);
                // $delivery_distance = $this->haversineDistance($delivery_pos['lat'], $delivery_pos['lng'], $o->delivery_location->latitude, $o->delivery_location->longitude);

                //calculate costs
                $shippingCost = $o->shipping_rate->price;
                // $pickupCost = $o->shipping_rate->pickup_cost_per_km * $pickup_distance;
                // $deliveryCost = $o->shipping_rate->delivery_cost_per_km * $delivery_distance;
                $pickupCost = 0;
                $deliveryCost = 0;
                $subTotal = $shippingCost + $pickupCost + $deliveryCost;
                $taxRate = 0;
                $taxCost = $subTotal + $taxRate;
                $totalCost = $subTotal + $taxCost;

                ///new stripe client instance
                // $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

                //if an existing payment entry for this order is/not found
                $pp = Payment::where('order_id', $o->id)->first();
                if (!$pp) {
                    // //cretae an brand new stripe payment intent
                    // $stripeIntent =  $stripe->paymentIntents->create([
                    //     'amount' => round($totalCost) * 100,
                    //     'currency' => 'eur',
                    //     'automatic_payment_methods' => ['enabled' => true],
                    // ]);

                    // //crete an new payment entry
                    // $pp = new Payment;
                    // $pp->order_id = $o->id;
                    // $pp->customer_id = Auth::user()->customer->id;
                    // $pp->amt_expected = round($totalCost);
                    // $pp->ref = $stripeIntent->id;
                    // $pp->save();

                    if (getAccountbalances(Auth::id())['balance'] >= $totalCost) {
                        $u = updateAccountBalance(Auth::id(), ($taxCost), $o->tracking_id, 'credit', 'Order ' . $o->tracking_id);
                        $c = updateAccountBalance(Auth::id(), ($totalCost * 0.015), $o->tracking_id, 'debit', 'Order Commision ' . $o->tracking_id);

                        // //crete an new payment entry
                        $pp = new Payment;
                        $pp->order_id = $o->id;
                        $pp->customer_id = Auth::user()->customer->id;
                        $pp->amt_expected = round($totalCost);
                        $pp->ref = $o->tracking_id;
                        $pp->status = 'done';
                        $pp->save();
                        //set the status of the order
                        Order::where('id', $o->id)->update([
                            'status' => 'placed'
                        ]);

                        //send email
                        Mail::to($o->customer->user->email)->send(new PaymentEmail($o));

                        Mail::to($o->pickup_email)->send(new PaymentEmail($o));

                        Mail::to($o->delivery_email)->send(new PaymentEmail($o));

                        //update email sent status
                        Payment::where('order_id', $o->id)->update([
                            'email_sent' => true
                        ]);
                    } else {
                        return back()->with(['message' => 'Insufficent Balance to complete Order']);
                    }
                }
                // else {
                //     $stripeIntent = $stripe->paymentIntents->retrieve($pp->ref);
                // }

                // if ($stripeIntent->status == 'succeeded') {
                //     Payment::where('order_id', $o->id)->update([
                //         'amt_paid' => $stripeIntent->amount_received / 100,
                //         'status' => 'done',
                //         'misc' => json_encode($stripeIntent->getLastResponse())
                //     ]);
                //     //set the status of the order
                //     Order::where('id', $o->id)->update([
                //         'status' => 'placed'
                //     ]);
                //     //check if reciept has been sent
                //     if ($pp->email_sent == false) {
                //         //send email
                //         Mail::to($o->customer->user->email)->send(new PaymentEmail($o));

                //         Mail::to($o->pickup_email)->send(new PaymentEmail($o));

                //         Mail::to($o->delivery_email)->send(new PaymentEmail($o));

                //         //update email sent status
                //         Payment::where('order_id', $o->id)->update([
                //             'email_sent' => true
                //         ]);
                //     }
                // } else if ($stripeIntent->status == 'processing') {
                //     Payment::where('order_id', $o->id)->update([
                //         'status' => 'pending'
                //     ]);
                //     // return redirect()->route('payment.summary', ['order_id' => $pp->order_id]);
                // } else if ('requires_payment_method') {
                //     Payment::where('order_id', $o->id)->update([
                //         'status' => 'failed'
                //     ]);
                //     // return redirect()->route('payment.summary', ['order_id' => $pp->order_id]);
                // } else {
                // }
                $o = Order::where('id', $request->order_id)->first();
                return view('users.payment.show', [
                    'order' => $o,
                    // 'pickup_distance' => $pickup_distance,
                    // 'delivery_distance' => $delivery_distance,
                    'payment_entry' => $pp,
                    // 'stripeIntent' => $stripeIntent
                ]);
            } else {
                return back()->with(['message' => 'Order does not exist']);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            return back()->with('message', "An error occured " . $e->getMessage())->withInput();
        }
    }

    // public function confirm_pay($order_id)
    // {
    //     try {
    //         //get the payment entry for the order
    //         $pp = Payment::where('order_id', $order_id)->first();
    //         ///new stripe client instance
    //         $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
    //         DB::beginTransaction();
    //         $i = $stripe->paymentIntents->retrieve($pp->ref);
    //         if ($i->status == 'succeeded') {
    //             Payment::where('order_id', $order_id)->update([
    //                 'amt_paid' => $i->amount_received,
    //                 'status' => 'done'
    //             ]);
    //             return redirect()->route('payment.summary', ['order_id' => $pp->order_id]);
    //         } else if ($i->status == 'processing') {
    //             Payment::where('order_id', $order_id)->update([
    //                 'status' => 'pending'
    //             ]);
    //             return redirect()->route('payment.summary', ['order_id' => $pp->order_id]);
    //         } else if ('requires_payment_method') {
    //             Payment::where('order_id', $order_id)->update([
    //                 'status' => 'failed'
    //             ]);
    //             return redirect()->route('payment.summary', ['order_id' => $pp->order_id]);
    //         } else {
    //         }
    //         DB::commit();
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error($e->getMessage(), ['exception' => $e]);
    //         return back()->with('message', "An error occured " . $e->getMessage())->withInput();
    //     }
    // }

    public function index()
    {
        return view('users.payment.index');
    }

    public function allPayments()
    {
        return view('admin.payments.index');
    }

    public function myPaymentsList()
    {
        $payments = Payment::with(['order'])->where('customer_id', Auth::user()->customer->id)->orderBy('created_at', 'DESC')->get();

        return Datatables::of($payments)
            ->addIndexColumn()
            ->addColumn('status', function ($pay) {
                $mar = "<span class= 'badge bg-secondary'>" . $pay->status . "</span>";
                return $mar;
            })
            ->addColumn('date', function ($pay) {
                $mar = "Created At:" . $pay->created_at . "</br>";
                return $mar;
            })
            ->editColumn('order_id', function ($pay) {
                $mar = "Tracking Id:" . $pay->order->tracking_id . "</br>";
                return $mar;
            })
            ->addColumn('amount', function ($pay) {
                $mar = "Amount Expected:" . $pay->amt_expected . "</br>";
                $mar .= "Amount Paid:" . $pay->amt_paid . "</br>";
                return $mar;
            })
            ->addColumn('customer', function ($pay) {
                $mar = "Name:" . $pay->order->customer->user->name . "</br>";
                $mar .= "Email:" . $pay->order->customer->user->email . "</br>";
                return $mar;
            })
            ->addColumn('view', function ($pay) {
                $url = route('payment.summary', ['order_id' => $pay->order_id]);
                return '<a href="' . $url . '" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> View/ Track</a>';
            })
            ->rawColumns(['status', 'view', 'customer', 'amount', 'order_id', 'date'])
            ->make(true);
    }

    public function allPaymentsList()
    {
        $payments = Payment::with(['order'])->orderBy('created_at', 'DESC')->get();

        return Datatables::of($payments)
            ->addIndexColumn()
            ->addColumn('status', function ($pay) {
                $mar = "<span class= 'badge bg-secondary'>" . $pay->status . "</span>";
                return $mar;
            })
            ->addColumn('date', function ($pay) {
                $mar = "Created At:" . $pay->created_at . "</br>";
                return $mar;
            })
            ->addColumn('date', function ($pay) {
                $mar = "Created At:" . $pay->created_at . "</br>";
                return $mar;
            })
            ->editColumn('order_id', function ($pay) {
                $mar = "Tracking Id:" . $pay->order->tracking_id . "</br>";
                return $mar;
            })
            ->addColumn('amount', function ($pay) {
                $mar = "Amount Expected:" . $pay->amt_expected . "</br>";
                $mar .= "Amount Paid:" . $pay->amt_paid . "</br>";
                return $mar;
            })
            ->addColumn('customer', function ($pay) {
                $mar = "Name:" . $pay->order->customer->user->name . "</br>";
                $mar .= "Email:" . $pay->order->customer->user->email . "</br>";
                return $mar;
            })
            ->addColumn('view', function ($pay) {
                $url = route('payment.summary', ['order_id' => $pay->order_id]);
                return '<a href="' . $url . '" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> View/ Track</a>';
            })
            ->rawColumns(['status', 'view', 'customer', 'amount', 'order_id', 'date'])
            ->make(true);
    }
}
