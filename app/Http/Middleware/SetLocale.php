<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $available = array_keys(enabled_locales());
        // Birinchi tashrifda har doim o'zbek tili; keyin sessiya yoki foydalanuvchi tanlovi.
        $locale = session('locale') ?? $request->user()?->locale ?? 'uz';
        app()->setLocale(in_array($locale, $available, true) ? $locale : 'uz');
        return $next($request);
    }
}
