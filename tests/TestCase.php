<?php
namespace Orinoko\StateMonitor\Tests;

use Orinoko\StateMonitor\Monitor;
use Orinoko\StateMonitor\MonitorServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
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