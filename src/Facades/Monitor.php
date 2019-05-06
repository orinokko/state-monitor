<?php

namespace Orinoko\StateMonitor\Facades;

use Illuminate\Support\Facades\Facade;

class Monitor extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'monitor';
    }

}