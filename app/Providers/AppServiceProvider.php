<?php

namespace App\Providers;

use App\Events\SaleCreated;
use App\Models\Sale;
use App\Observers\SaleObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use App\Listeners\DispatchSaleCreated;

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

        Sale::observe(SaleObserver::class);
    }
}
