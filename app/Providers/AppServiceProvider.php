<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Madcoda\Youtube;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Youtube::class, function ($app) {
            return new Youtube(['key' => config('app.youtube_api_key')]);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
