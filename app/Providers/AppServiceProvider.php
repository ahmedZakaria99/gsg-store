<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
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
    public function boot()
    {
        Validator::extend('filter', function ($attribute, $value, $params) {
            foreach ($params as $word) {
                if (stripos($value, $word) !== false) {
                    $this->filtered = $word;
                    return false;
                }
            }
            return true;
        }, 'Some words are not allowed!');
    }
}