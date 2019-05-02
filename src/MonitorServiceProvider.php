<?php

namespace Orinoko\StateMonitor;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Orinoko\StateMonitor\Facades\Monitor;

class MonitorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(realpath(__DIR__.'/../migrations'));
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'state-monitor');

        $this->publishes([
            __DIR__.'/config/state-monitor.php' => config_path('state-monitor.php')
        ], 'config');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // config
        $this->mergeConfigFrom(
            __DIR__.'/config/state-monitor.php', 'state-monitor'
        );
        // route
        include __DIR__.'/routes/web.php';
        $this->app->make('Orinoko\StateMonitor\Http\MonitorController');
        // facade
        $loader = AliasLoader::getInstance();
        $loader->alias('Monitor', Monitor::class);
        $this->app->singleton('Monitor', function () {
            return new Monitor();
        });
        // middleware
        $this->app['router']->aliasMiddleware('state-monitor-errors', Http\Middleware\MonitorErrors::class);
    }
}
