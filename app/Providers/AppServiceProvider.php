<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (app()->isProduction()) {
            URL::forceScheme('https');
        }

        $target = storage_path('app/public');
        $link = public_path('storage');

        if (! File::exists($link) && File::exists($target)) {
            File::link($target, $link);
        }
    }
}