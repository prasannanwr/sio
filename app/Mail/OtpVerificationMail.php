<?php

namespace App\Mail;

use App\Models\AuthOtp;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $code;
    public string $purpose;
    public int $ttlMinutes;
    public ?string $name;

    public function __construct(string $code, string $purpose, int $ttlMinutes, ?string $name = null)
    {
        $this->code = $code;
        $this->purpose = $purpose;
        $this->ttlMinutes = $ttlMinutes;
        $this->name = $name;
    }

    public function build()
    {
        $subject = $this->purpose === AuthOtp::PURPOSE_PASSWORD_RESET
            ? env('APP_NAME') . ' - Password Reset Code'
            : env('APP_NAME') . ' - Email Verification Code';

        return $this->subject($subject)->view('emails.otp-verification');
    }
}
