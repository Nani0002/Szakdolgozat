<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        view()->composer('layouts.menu', function ($view) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $extras = $view->getData()['navActions'] ?? [];
            $view->with([
                'userUrls' => $user?->getUserUrls(request()->is('worksheet/*') || request()->is('worksheet')),
                'navUrls' => User::getNavUrls($user->role ?? null, $extras),
            ]);
        });
    }
}
