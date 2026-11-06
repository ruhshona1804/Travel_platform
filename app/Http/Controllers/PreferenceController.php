<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;

class PreferenceController extends Controller
{
    public function locale(Request $request, string $locale)
    {
        abort_unless(array_key_exists($locale, enabled_locales()), 404);
        session(['locale' => $locale]);
        $request->user()?->update(['locale' => $locale]);
        return back();
    }

    public function currency(Request $request, string $code)
    {
        $code = strtoupper($code);
        abort_unless(Currency::where('code', $code)->where('is_active', true)->exists(), 404);
        session(['currency' => $code]);
        $request->user()?->update(['currency' => $code]);
        return back();
    }

    public function theme(Request $request, string $theme)
    {
        abort_unless(in_array($theme, ['light', 'dark', 'system'], true), 404);
        $request->user()?->update(['theme' => $theme]);
        return response()->noContent();
    }
}
