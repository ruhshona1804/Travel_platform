<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        if (! config('travel.google.enabled')) {
            return redirect()->route('login')->withErrors(['email' => __('ui.google_not_configured')]);
        }
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        if (! config('travel.google.enabled')) return redirect()->route('login');
        try {
            $g = Socialite::driver('google')->user();
        } catch (\Throwable) {
            return redirect()->route('login')->withErrors(['email' => __('ui.err_google')]);
        }
        $user = User::where('google_id', $g->getId())->orWhere('email', $g->getEmail())->first();
        if (! $user) {
            $user = User::create(['name' => $g->getName() ?: $g->getEmail(), 'email' => $g->getEmail(), 'password' => Str::random(40), 'locale' => app()->getLocale()]);
            $user->forceFill(['email_verified_at' => now()])->save();
        }
        if ($user->is_suspended) return redirect()->route('login')->withErrors(['email' => __('ui.err_suspended')]);
        $user->forceFill(['google_id' => $g->getId()])->save();
        Auth::login($user, true);
        return redirect()->route('dashboard.index');
    }
}
