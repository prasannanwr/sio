<?php

namespace App\Http\Controllers;

use App\Mail\InquiryNotification;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\SmtpConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InquiryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'email' => 'required|email',
            'shipping_location' => 'required|string|max:255',
            'organization' => 'required|string|max:255',
        ]);

        $product = Product::find($request->product_id);

        Inquiry::create($request->except('_token'));

        $activeSmtp = SmtpConfiguration::applyActiveIfAny();
        $recipient = $activeSmtp ? $activeSmtp->from_address : env('MAIL_FROM_ADDRESS');

        try {
            Mail::to($recipient)->send(new InquiryNotification($product, $request->except('_token')));
        } catch (\Throwable $e) {
            Log::error('Inquiry notification email failed', [
                'recipient' => $recipient,
                'smtp_configuration_id' => $activeSmtp ? $activeSmtp->id : null,
                'error' => $e->getMessage(),
            ]);

            return back()->with('success', 'Your inquiry has been submitted successfully. (Confirmation email could not be sent.)');
        }

        return back()->with('success', 'Your inquiry has been submitted successfully.');
    }
}
