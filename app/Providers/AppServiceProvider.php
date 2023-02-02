<?php

namespace App\Providers;

use App\Http\Controllers\Chat\Chat;
use App\Interfaces\Chat\RoomInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
//        Model::preventLazyLoading(! app()->isProduction() );
        Model::preventLazyLoading(false);
        Schema::defaultStringLength(125);
        $this->app->singleton(RoomInterface::class, Chat::class);
        date_default_timezone_set('America/Bogota');
        if( app()->isProduction()) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
