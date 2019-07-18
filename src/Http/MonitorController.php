<?php

namespace Orinoko\StateMonitor\Http;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Orinoko\StateMonitor\Facades\Monitor as MonitorFacade;

class MonitorController //extends Controller
{
    public function index()
    {
        dd('installed');
    }

    public function monitorAddEvent(Request $request)
    {
        $message = $request->input('message','');
        $priority = $request->input('priority',0);
        $url = $request->input('url','');
        $method = $request->input('method','');
        $params = [];
        $user = $request->input('user','');
        $domain = $request->input('domain','');
        MonitorFacade::storeEvent($message,$priority,$url,$method,$params,$user,$domain);
    }
}
