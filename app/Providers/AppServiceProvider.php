<?php

namespace App\Providers;

use App\Models\Storefront;
use App\Policies\StorefrontPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Storefront::class, StorefrontPolicy::class);
    }
}
