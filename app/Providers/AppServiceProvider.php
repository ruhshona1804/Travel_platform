<?php

namespace App\Providers;

use App\Services\Currency\CurrencyService;
use App\Services\Currency\ExchangeRateProvider;
use App\Services\Currency\OpenErApiProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Provayderni sozlama orqali almashtirish mumkin (config/travel.php -> exchange.provider)
        $this->app->bind(ExchangeRateProvider::class, fn () => match (config('travel.exchange.provider')) {
            default => new OpenErApiProvider(),
        });
        $this->app->singleton(CurrencyService::class);
    }

    public function boot(): void
    {
        Gate::define('manage-system', fn ($u) => $u->isAdmin() && ! $u->is_suspended);
        Gate::define('manage-users', fn ($u) => $u->isAdmin() && ! $u->is_suspended);
        Gate::define('assign-super-admin', fn ($u) => $u->isSuperAdmin());

        RateLimiter::for('auth', fn (Request $r) => Limit::perMinute(8)->by($r->ip().'|'.$r->input('email')));
        RateLimiter::for('planner', fn (Request $r) => Limit::perMinute(10)->by($r->user()?->id ?: $r->ip()));
        RateLimiter::for('assistant', fn (Request $r) => Limit::perMinute(12)->by($r->user()?->id ?: $r->ip()));

        View::composer('*', function ($view) {
            static $shared = null;
            $shared ??= [
                'currencyService' => app(CurrencyService::class),
            ];
            $view->with($shared);
        });
    }
}
