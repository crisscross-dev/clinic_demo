<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\InventoryItem;
use App\Models\PatientInfo;
use App\Auth\AdminUserProvider;

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
        // Force HTTPS for all URLs if not running locally
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }

        // Register custom admin user provider for password reset
        Auth::provider('admin_eloquent', function ($app, array $config) {
            Log::info('Creating admin_eloquent provider', ['config' => $config]);
            return new AdminUserProvider($app['hash'], $config['model']);
        });

        // Share stock counts and pending count with sidebar
        View::composer('layouts.sidebar', function ($view) {
            $lowStockCount = InventoryItem::all()->filter(function ($item) {
                $threshold = $item->low_stock_reminder ?? 5;
                return $item->total_stock > 0 && $item->total_stock < $threshold;
            })->count();

            $outOfStockCount = InventoryItem::where('total_stock', '<=', 0)->count();

            // Count pending patients (same filter as PendingPatientController)
            $pendingCount = PatientInfo::where('status', 'pending')
                ->whereNotNull('department')
                ->where('department', '!=', '')
                ->count();

            $view->with(compact('lowStockCount', 'outOfStockCount', 'pendingCount'));
        });
    }
}
