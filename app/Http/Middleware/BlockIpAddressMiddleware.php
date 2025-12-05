<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Ipaddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class BlockIpAddressMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            if (!Schema::hasTable('ipaddresses')) {
                return $next($request);
            }

            $ipaddress = DB::table('ipaddresses')->pluck('ipaddress');
            $iparrays = $ipaddress->toArray();
            $userip = $request->ip();

            if (in_array($userip, $iparrays)) {
                 abort(403, "You are restricted to access the site.");
            }

        } catch (\Exception $e) {
            // If DB is down or any error occurs, log and allow the request to continue
            Log::warning('BlockIpAddressMiddleware skipped: ' . $e->getMessage());
            return $next($request);
        }
        return $next($request);
    }
}
