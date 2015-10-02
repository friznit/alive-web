<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Perform post-registration booting of services
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Implement custom validation, alpha_numeric but with spaces too!
         */
        Validator::extend('alpha_spaces', function($attribute, $value)
        {
            return preg_match('/(^[A-Za-z0-9 ]+$)+/', $value);
        });

        /**
         * orFail() Eloquent Functions return 404 page
         */
        app()->error(function (ModelNotFoundException $e) {
            return Response::make('Not Found', 404);
        });
    }

}