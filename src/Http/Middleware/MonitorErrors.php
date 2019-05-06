<?php

namespace Orinoko\StateMonitor\Http\Middleware;

use Closure;
use Orinoko\StateMonitor\Facades\Monitor;

class MonitorErrors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ( !empty($response->exception)
            //&& $response->exception instanceof FormValidationException
        ) {
            Monitor::processRequest($request,$response);
        }

        return $response;
    }
}
