<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <-- Ekle

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Canlı ortamda (veya Render üzerinde) tüm asset ve form linklerini zorla HTTPS yap
        if (config('app.env') === 'production' || str_contains(request()->url(), 'onrender.com')) {
            URL::forceScheme('https');
        }
    }
}