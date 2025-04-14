<?php

namespace Faizulramir\Senangpay;

use Illuminate\Support\ServiceProvider;

class SenangpayServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/senangpay.php',
            'senangpay'
        );

        $this->app->singleton('senangpay', function ($app) {
            return new Senangpay();
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/senangpay.php' => config_path('senangpay.php'),
        ], 'senangpay-config');
    }
}