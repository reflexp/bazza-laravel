<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cookie;

use App\Models\Clients;

class ClientSession
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
        $clientCookie = Cookie::get(config('constants.clientCookieName'));

        $clientInfo = Clients::where('sessionHash', $clientCookie ?? 'NaN')->first();

        if($clientInfo)
        {
            View::share('clientInfo', $clientInfo);
            $request->attributes->add(['clientInfo' => $clientInfo]);

            return $next($request);
        }
        else
        {
            return redirect('/login');
        }
    }
}
