<?php

namespace App\Providers;

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
        \Illuminate\Support\Facades\Route::bind('user', function ($value) {
            return \App\Models\User::where('id', $value)
                ->orWhere('username', $value)
                ->firstOrFail();
        });

        \Illuminate\Support\Facades\Route::bind('targetUser', function ($value) {
            return \App\Models\User::where('id', $value)
                ->orWhere('username', $value)
                ->firstOrFail();
        });
    }
}
