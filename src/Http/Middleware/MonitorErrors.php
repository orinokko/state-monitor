<?php

namespace Orinoko\StateMonitor\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Mail;
use Orinoko\StateMonitor\Mail\ErrorHandled;

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
            // Same as getallheaders(), just with lowercase keys
            $headers = array_map(function($x){
                return $x[0];
            }, $request->headers->all());
            //dump($request);
            $url = $request->url();
            $code = $response->exception->getCode();
            $message = $response->exception->getMessage();
            $file = $response->exception->getFile();
            $line = $response->exception->getLine();
            $trace = $response->exception->getTrace();
            $traceString = $response->exception->getTraceAsString();
            $time = date('d.m.Y H:i:s');
            $app = config('app');
            /*dump($code);
            dump($message);
            dump($file);
            dump($line);/**/
            //dump($traceString);
            if(config('state-monitor.use-local')){
                Mail::to(config('state-monitor.alert-email'))
                    ->send(new ErrorHandled(compact('time','app','code','message','file','line','trace','traceString','headers','url')));
            }else{
                // send to mailer
            }
            // save to storage
            if(config('state-monitor.use-bigquery')){
                //dd(1);
            }
        }

        return $response;
    }
}
