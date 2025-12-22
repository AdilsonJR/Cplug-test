<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

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
        Response::macro('validationErrorApi', function (string $message,int $statusCode, array $errors = []) {
            return response()->json([
                'message' => $message,
                'errors' => $errors,
            ], $statusCode);
        });
    }
}
