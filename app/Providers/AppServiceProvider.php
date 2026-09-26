<?php

namespace App\Providers;

use App\Payments\Contracts\PaymentProvider;
use App\Payments\MockPaymentProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentProvider::class, function (): PaymentProvider {
            return match (config('payouts.driver', 'mock')) {
                default => $this->app->make(MockPaymentProvider::class),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
    }
}
