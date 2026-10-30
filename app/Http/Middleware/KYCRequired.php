<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class KYCRequired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $status = (auth()->user()->kyc) ? auth()->user()->kyc->status : false;

        if ($status == 'approved') {
            return $next($request);
        }

        // User has not completed KYC, return an error response
        return response()->json(['status' => false, 'data' => $status, 'message' => 'Please complete your KYC or wait for approval first'], 403);
    }
}
