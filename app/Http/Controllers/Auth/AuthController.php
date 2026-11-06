<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }
    public function showRegister() { return view('auth.register'); }

    public function login(Request $request)
    {
        $cred = $request->validate(['email' => 'required|email', 'password' => 'required|string']);
        $user = User::where('email', $cred['email'])->first();
        if ($user?->is_suspended) {
            return back()->withErrors(['email' => __('ui.err_suspended')])->onlyInput('email');
        }
        if (! Auth::attempt($cred, $request->boolean('remember'))) {
            return back()->withErrors(['email' => __('ui.err_credentials')])->onlyInput('email');
        }
        $request->session()->regenerate();
        $u = Auth::user();
        if ($u->locale) session(['locale' => $u->locale]);
        if ($u->currency) session(['currency' => $u->currency]);
        ActivityLog::record('login');
        return redirect()->intended(route('dashboard.index'));
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:190|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        $user = User::create($data + ['locale' => app()->getLocale()]);
        event(new Registered($user));
        $user->notify(new WelcomeNotification());
        Auth::login($user);
        $request->session()->regenerate();
        ActivityLog::record('register');
        return redirect()->route('dashboard.index')->with('status', __('ui.registered'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
