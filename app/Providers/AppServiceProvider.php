<?php

namespace App\Providers;

use App\Payment\Gateways\TamaraGateway;
use App\Payment\PaymentService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('payment.tamara', function () {
            return new PaymentService(new TamaraGateway());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
