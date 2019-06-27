<?php

namespace Teka\RoundRobin;

use Illuminate\Support\ServiceProvider;

class RoundRobinServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('round-robin', function ($app){
            return new RoundRobin();
        });
    }

    public function provides()
    {
        return [
            'round-robin'
        ];
    }
}
