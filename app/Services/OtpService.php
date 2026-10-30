<?php

namespace App\Services;

use App\Mail\OtpVerificationMail;
use App\Models\AuthOtp;
use App\Models\SmtpConfiguration;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class OtpService
{
    public function create(User $user, string $purpose, string $channel, string $identifier): string
    {
        $identifier = $this->normalizeIdentifier($identifier);

        AuthOtp::where('user_id', $user->id)
            ->where('purpose', $purpose)
            ->where('channel', $channel)
            ->where('identifier', $identifier)
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);

        $code = (string) random_int(100000, 999999);

        AuthOtp::create([
            'user_id' => $user->id,
            'identifier' => $identifier,
            'channel' => $channel,
            'purpose' => $purpose,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes((int) config('auth_api.otp.ttl_minutes', 10)),
        ]);

        Log::info('Authentication OTP generated', [
            'user_id' => $user->id,
            'channel' => $channel,
            'purpose' => $purpose,
            'identifier' => $identifier,
            'otp' => $code,
        ]);

        if ($channel === AuthOtp::CHANNEL_EMAIL) {
            $this->sendEmailOtp($identifier, $user, $purpose, $code);
        }

        return $code;
    }

    /**
     * Deliver the OTP to the user's email over SMTP. Failures are logged so a transient
     * mail error never blocks signup — the user can request a new code via resend.
     */
    protected function sendEmailOtp(string $email, User $user, string $purpose, string $code): void
    {
        try {
            // Use the active SMTP configuration managed in admin (smtp-configurations) instead of .env.
            SmtpConfiguration::applyActiveIfAny();
            Mail::purge('smtp');

            Mail::to($email)->send(new OtpVerificationMail(
                $code,
                $purpose,
                (int) config('auth_api.otp.ttl_minutes', 10),
                $user->name
            ));
        } catch (\Throwable $e) {
            Log::error('Failed to send OTP email', [
                'user_id' => $user->id,
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function verify(string $identifier, string $purpose, string $code): AuthOtp
    {
        $otp = AuthOtp::where('identifier', $this->normalizeIdentifier($identifier))
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->latest()
            ->first();

        if (! $otp || $otp->expires_at->isPast()) {
            throw ValidationException::withMessages([
                'otp' => ['The OTP is invalid or has expired.'],
            ]);
        }

        if ($otp->attempts >= (int) config('auth_api.otp.max_attempts', 5)) {
            $otp->forceFill(['consumed_at' => now()])->save();

            throw ValidationException::withMessages([
                'otp' => ['Too many OTP attempts. Please request a new OTP.'],
            ]);
        }

        if (! Hash::check($code, $otp->code_hash)) {
            $otp->increment('attempts');

            throw ValidationException::withMessages([
                'otp' => ['The OTP is invalid or has expired.'],
            ]);
        }

        $otp->forceFill(['consumed_at' => now()])->save();

        return $otp;
    }

    public function normalizeIdentifier(string $identifier): string
    {
        $identifier = trim($identifier);

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return strtolower($identifier);
        }

        return preg_replace('/[\s\-\(\)]/', '', $identifier);
    }
}
