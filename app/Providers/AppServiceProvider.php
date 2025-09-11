<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Spatie\MediaLibrary\Support\UrlGenerator\UrlGeneratorFactory;
use App\Media\CustomMediaUrlGenerator;
use Illuminate\Support\Facades\View;

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
        Paginator::useBootstrap();
        View::composer('*', function ($view) {
            if (request()->route()) {
                $view->with('partner_slug', request()->route('partner_slug'));
                $view->with('table_code', request()->route('table_code'));
            }
        });
        // app(UrlGeneratorFactory::class)->setCustomUrlGenerator(CustomMediaUrlGenerator::class);
    }
}
