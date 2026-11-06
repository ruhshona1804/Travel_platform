<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $u = $request->user();
        return view('dashboard.index', [
            'favCount' => $u->favorites()->count(), 'planCount' => $u->travelPlans()->count(), 'budgetCount' => $u->budgets()->count(),
            'plans' => $u->travelPlans()->latest()->limit(3)->get(),
            'unread' => $u->unreadNotifications()->count(),
        ]);
    }

    public function favorites(Request $request)
    {
        $favs = $request->user()->favorites()->with('favoritable')->latest()->get()->filter(fn ($f) => $f->favoritable);
        return view('dashboard.favorites', ['groups' => $favs->groupBy(fn ($f) => strtolower(class_basename($f->favoritable_type)))]);
    }

    public function plans(Request $request)
    {
        return view('dashboard.plans', ['plans' => $request->user()->travelPlans()->with('city')->latest()->paginate(10)]);
    }

    public function budgets(Request $request)
    {
        return view('dashboard.budgets', ['budgets' => $request->user()->budgets()->with('items')->latest()->paginate(10)]);
    }

    public function notifications(Request $request)
    {
        return view('dashboard.notifications', ['notifications' => $request->user()->notifications()->paginate(15)]);
    }

    public function readNotifications(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return back();
    }

    public function profile(Request $request)
    {
        return view('dashboard.profile', ['currencies' => Currency::where('is_active', true)->pluck('code')]);
    }

    public function updateProfile(Request $request)
    {
        $u = $request->user();
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => ['required', 'email', 'max:190', Rule::unique('users')->ignore($u->id)],
            'locale' => ['nullable', Rule::in(array_keys(config('travel.locales')))],
            'currency' => ['nullable', Rule::exists('currencies', 'code')],
            'theme' => 'nullable|in:light,dark,system',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        if ($request->hasFile('avatar')) {
            if ($u->avatar) Storage::disk('public')->delete($u->avatar);
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } else {
            unset($data['avatar']);
        }
        $emailChanged = $data['email'] !== $u->email;
        $u->fill($data);
        if ($emailChanged) {
            $u->email_verified_at = null;
        }
        $u->save();
        if ($emailChanged) $u->sendEmailVerificationNotification();
        if (! empty($data['locale'])) session(['locale' => $data['locale']]);
        if (! empty($data['currency'])) session(['currency' => $data['currency']]);
        return back()->with('status', __('ui.saved'));
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        $request->user()->update(['password' => $data['password']]);
        return back()->with('status', __('ui.saved'));
    }

    public function destroyAccount(Request $request)
    {
        $request->validate(['password' => 'required|current_password']);
        $u = $request->user();
        abort_if($u->isSuperAdmin() && \App\Models\User::where('role', 'super_admin')->count() === 1, 403, __('ui.err_last_super'));
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $u->delete();
        return redirect()->route('home')->with('status', __('ui.account_deleted'));
    }
}
