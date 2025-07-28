<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\SettingsHelper;
use App\Models\OwnerComplaint;
use App\Models\OwnerLoan;
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
        View::share('settings', SettingsHelper::class);

        View::composer('*', function ($view) {
            $pendingComplaintsCount = OwnerComplaint::where('status', 'pending')->count();
            $pendingOwnerLoanCount = OwnerLoan::where('status', 'pending')->count();
            $view->with('pendingComplaintsCount', $pendingComplaintsCount)
            ->with('pendingOwnerLoanCount', $pendingOwnerLoanCount);
        });
    }
}
