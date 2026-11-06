<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AirlineController;
use App\Http\Controllers\AssistantController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\PlannerController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\VisaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/search/suggest', [HomeController::class, 'suggest'])->name('search.suggest');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

// Til, valyuta, mavzu
Route::post('/locale/{locale}', [PreferenceController::class, 'locale'])->name('locale');
Route::post('/currency/{code}', [PreferenceController::class, 'currency'])->name('currency');
Route::post('/theme/{theme}', [PreferenceController::class, 'theme'])->name('theme');

// Katalog
Route::get('/countries', [CountryController::class, 'index'])->name('countries.index');
Route::get('/countries/{country}', [CountryController::class, 'show'])->name('countries.show');
Route::get('/cities/{city}', [CityController::class, 'show'])->name('cities.show');

foreach (['attractions', 'hotels', 'restaurants'] as $kind) {
    Route::get("/$kind", [PlaceController::class, 'index'])->defaults('kind', $kind)->name("$kind.index");
    Route::get("/$kind/{place}", [PlaceController::class, 'show'])->defaults('kind', $kind)->name("$kind.show");
}
Route::get('/uzbek-food', [PlaceController::class, 'uzbekFood'])->name('uzbek-food');

Route::get('/airlines', [AirlineController::class, 'index'])->name('airlines.index');
Route::get('/flights/compare', [AirlineController::class, 'compare'])->name('flights.compare');
Route::get('/airlines/{airline}', [AirlineController::class, 'show'])->name('airlines.show');
Route::get('/visa', [VisaController::class, 'index'])->name('visa.index');
Route::get('/map', [MapController::class, 'index'])->name('map');
Route::get('/map/data', [MapController::class, 'data'])->name('map.data');
Route::get('/guides', [GuideController::class, 'index'])->name('guides.index');
Route::get('/guides/{guide}', [GuideController::class, 'show'])->name('guides.show');

// Valyuta
Route::get('/currency', [CurrencyController::class, 'index'])->name('currency.index');
Route::get('/currency/convert', [CurrencyController::class, 'convert'])->name('currency.convert');

// Rejalashtirish
Route::get('/planner', [PlannerController::class, 'create'])->name('planner');
Route::post('/planner', [PlannerController::class, 'store'])->middleware('throttle:planner')->name('planner.store');
Route::get('/planner/{plan}', [PlannerController::class, 'show'])->name('planner.show');
Route::get('/budget', [BudgetController::class, 'create'])->name('budget');
Route::get('/budget/check', [BudgetController::class, 'check'])->name('budget.check');
Route::post('/assistant', [AssistantController::class, 'ask'])->middleware('throttle:assistant')->name('assistant.ask');

// Autentifikatsiya
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:auth');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:auth');
    Route::get('/forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'email'])->middleware('throttle:auth')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'form'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
    Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [VerifyEmailController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('/email/verification-notification', [VerifyEmailController::class, 'send'])->middleware('throttle:6,1')->name('verification.send');

    Route::post('/favorites/{type}/{id}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::post('/reviews/{type}/{id}', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/budget', [BudgetController::class, 'store'])->name('budget.store');

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/favorites', [DashboardController::class, 'favorites'])->name('favorites');
        Route::get('/plans', [DashboardController::class, 'plans'])->name('plans');
        Route::get('/budgets', [DashboardController::class, 'budgets'])->name('budgets');
        Route::get('/notifications', [DashboardController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/read', [DashboardController::class, 'readNotifications'])->name('notifications.read');
        Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [DashboardController::class, 'updatePassword'])->name('password.update');
        Route::delete('/account', [DashboardController::class, 'destroyAccount'])->name('account.destroy');
        Route::delete('/plans/{plan}', [PlannerController::class, 'destroy'])->name('plans.destroy');
        Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy'])->name('budgets.destroy');
    });
});

// Admin panel
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('index');

    Route::middleware('can:manage-system')->group(function () {
        Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [Admin\UserController::class, 'show'])->name('users.show');
        Route::put('/users/{user}', [Admin\UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [Admin\UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/currencies', [Admin\CurrencyController::class, 'index'])->name('currencies.index');
        Route::post('/currencies/refresh', [Admin\CurrencyController::class, 'refresh'])->name('currencies.refresh');
        Route::put('/currencies/{currency}', [Admin\CurrencyController::class, 'update'])->name('currencies.update');

        Route::get('/settings', [Admin\SettingController::class, 'edit'])->name('settings');
        Route::put('/settings', [Admin\SettingController::class, 'update'])->name('settings.update');
    });

    // Umumiy CRUD (config/admin.php)
    Route::get('/r/{resource}', [Admin\ResourceController::class, 'index'])->name('resource.index');
    Route::get('/r/{resource}/create', [Admin\ResourceController::class, 'create'])->name('resource.create');
    Route::post('/r/{resource}', [Admin\ResourceController::class, 'store'])->name('resource.store');
    Route::get('/r/{resource}/{id}/edit', [Admin\ResourceController::class, 'edit'])->name('resource.edit');
    Route::put('/r/{resource}/{id}', [Admin\ResourceController::class, 'update'])->name('resource.update');
    Route::delete('/r/{resource}/{id}', [Admin\ResourceController::class, 'destroy'])->name('resource.destroy');
});
