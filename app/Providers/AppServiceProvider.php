<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Encore\Admin\Config\Config;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(150);
        Config::load();

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->register(RepositoryServiceProvider::class);
    }
}
