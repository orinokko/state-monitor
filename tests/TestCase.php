<?php
namespace Orinoko\StateMonitor\Test;

use Orinoko\StateMonitor\Monitor;
use Orinoko\StateMonitor\MonitorServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    /**
     * Load package service provider
     * @param  \Illuminate\Foundation\Application $app
     * @return Orinoko\StateMonitor\MonitorServiceProvider
     */
    protected function getPackageProviders($app)
    {
        return [MonitorServiceProvider::class];
    }
    /**
     * Load package alias
     * @param  \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Monitor' => Monitor::class,
        ];
    }
}