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
        // Use safe_query to prevent crashes when DB unavailable
        $iparrays = safe_query(function() {
            return DB::table('ipaddresses')->pluck('ipaddress')->toArray();
        }, []);

        $userip = $request->ip();

        if (in_array($userip, $iparrays)) {
            abort(403, "You are restricted to access the site.");
        }

        return $next($request);
    }
}
