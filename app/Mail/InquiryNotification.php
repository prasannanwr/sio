<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InquiryNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $product;
    public $inquiryData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Product $product, $inquiryData)
    {
        $this->product = $product;
        $this->inquiryData = $inquiryData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Product Inquiry')
            ->view('emails.inquiry_notification');
    }
}
