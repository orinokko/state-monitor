<?php

namespace Orinoko\StateMonitor;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\DB;
use Orinoko\StateMonitor\Facades\Monitor as MonitorFacade;

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

        putenv('GOOGLE_CLOUD_PROJECT='.config('state-monitor.bigquery-project'));
        putenv('GOOGLE_APPLICATION_CREDENTIALS='.base_path().config('state-monitor.bigquery-path'));

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\InstallCommand::class,
            ]);
        }

        // middleware
        $this->app['router']->pushMiddlewareToGroup('web', Http\Middleware\MonitorErrors::class);
        $this->app['router']->pushMiddlewareToGroup('api', Http\Middleware\MonitorErrors::class);

        if(config('state-monitor.log-queries')) { // ToDo: remote control
            DB::listen(function ($query) {
                MonitorFacade::storeQuery($query->sql,$query->bindings,$query->time);
            });
        }
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
        $loader->alias('Monitor', MonitorFacade::class);
        $this->app->singleton('monitor', function () {
            return new Monitor();
        });
        // middleware
        $this->app['router']->aliasMiddleware('state-monitor-errors', Http\Middleware\MonitorErrors::class);
    }
}
