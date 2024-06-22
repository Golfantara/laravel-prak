<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrasi layanan aplikasi
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Pengaturan paginator untuk menggunakan Bootstrap
        Paginator::useBootstrap();
    }
}
