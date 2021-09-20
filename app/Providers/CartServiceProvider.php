<?php

namespace App\Providers;

use App\Repositories\Cart\CartRepositoryCookie;
use App\Repositories\Cart\CartRepositoryDatabase;
use App\Repositories\Cart\CartRepositoryInterface;
use App\Repositories\Cart\CartRepositorySession;
use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(CartRepositoryInterface::class,function (){
            if (config('cart.driver') == 'session'){
                return new CartRepositorySession();
            }
            if (config('cart.driver') == 'cookie'){
                return new CartRepositoryCookie();
            }
            return  new CartRepositoryDatabase();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
