<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function notice(Request $request)
    {
        return $request->user()->hasVerifiedEmail() ? redirect()->route('dashboard.index') : view('auth.verify-email');
    }

    public function verify(Request $request, string $id, string $hash)
    {
        $user = $request->user();
        abort_unless((string) $user->getKey() === $id && hash_equals(sha1($user->getEmailForVerification()), $hash), 403);
        if (! $user->hasVerifiedEmail() && $user->markEmailAsVerified()) {
            event(new Verified($user));
        }
        return redirect()->route('dashboard.index')->with('status', __('ui.email_verified'));
    }

    public function send(Request $request)
    {
        if (! $request->user()->hasVerifiedEmail()) $request->user()->sendEmailVerificationNotification();
        return back()->with('status', __('ui.verification_sent'));
    }
}
