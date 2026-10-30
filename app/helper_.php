<?php

use App\Models\CurrencyExchangeRate;
use Carbon\Carbon;
use App\Models\UserFunds;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Psy\Readline\Userland;

if (!function_exists('getAccountbalances')) {
    function getAccountbalances($user_id)
    {
        $today = Carbon::today();
        $spent = UserFunds::where('flag', 'credit')->where('user_id', $user_id)->sum('amount');
        $earnt = UserFunds::where('flag', 'debit')->where('user_id', $user_id)->sum('amount');

        $bal = $earnt - $spent;
        $earntToday = UserFunds::where('flag', 'debit')->where('user_id', $user_id)->where('description', '!=', 'Wallet Funding')
            ->whereDate('created_at', $today)
            ->sum('amount');

        $spentToday = UserFunds::where('flag', 'credit')->where('user_id', $user_id)
            ->whereDate('created_at', $today)
            ->sum('amount');

        $earntAdminToday = UserFunds::where('flag', 'debit')->where('user_id', $user_id)->where('description', '!=', 'Admin Wallet Funding')
            ->whereDate('created_at', $today)
            ->sum('amount');

        $spentAdminToday = UserFunds::where('flag', 'credit')->where('user_id', $user_id)->where('description', '!=', 'Admin Transfer Requested amount from Wallet')
            ->whereDate('created_at', $today)
            ->sum('amount');

        return [
            'balance' => $bal,
            'earningsToday' => $earntToday,
            'spentToday' => $spentToday,
            'totalAmount' => $earnt,
            'totalSpendings' => $spent,
            'totalAdminEarningsToday' => $earntAdminToday,
            'totalAdminSpentToday' => $spentAdminToday,
        ];
    }
}

if (!function_exists('updateAccountBalance')) {

    function updateAccountBalance($user_id, $amount, $id, $flag = 'debit', $description = '', $status = null, $currency = 'EUR')
    {
        try {
            $u = new UserFunds;
            $u->user_id = $user_id;
            $u->amount = $amount;
            $u->flag = $flag;
            $u->status = $status;
            $u->transId = $id;
            $u->description = $description;
            $u->currency = $currency;
            $u->save();
            return $u;
        } catch (Exception $e) {
            Log::error("Failed to update account balance: " . $e->getMessage(), [$e]);
            return false;
        }
    }
}

if (!function_exists('accountTxExists')) {

    function accountTxExists($transId)
    {
        $r = UserFunds::where('transId', $transId)->first();
        if ($r) {
            return $r;
        } else {
            return false;
        }
    }
}


if (!function_exists('public_directory')) {
    function public_directory($path = '')
    {
        if (app()->environment('production')) {
            return base_path('../public_html') . ($path ? '/' . $path : $path);
        }

        return base_path('public') . ($path ? '/' . $path : $path);
    }
}

if (!function_exists('toEuro')) {
    function toEuro($currency_id, $price)
    {
        if ($currency_id > 0) {
            $currency = CurrencyExchangeRate::find($currency_id);
            $result = $price / $currency->currency_rate;
            return $result;
        } else {
            return $price;
        }
    }
}

if (!function_exists('fromEuroView')) {
    function fromEuroView($currency_id, $price)
    {
        if ($currency_id > 0) {
            $currency = CurrencyExchangeRate::find($currency_id);
            if (isset($currency)) {
                $result = number_format($price * $currency->exchange_rate, 2);
                return $result . ' ' . $currency->country->currency_symbol;
            }
            $result = number_format($price, 2);
            return $result . ' ' . "€";
        } else {
            $result = number_format($price, 2);
            return $result . ' ' . "€";
        }
    }
}
