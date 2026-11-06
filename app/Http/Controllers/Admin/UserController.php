<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = User::query();
        if ($s = trim((string) $request->query('q'))) $q->where(fn ($w) => $w->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%"));
        if ($r = $request->query('role')) $q->where('role', $r);
        return view('admin.users.index', ['users' => $q->latest()->paginate(20)->withQueryString()]);
    }

    public function show(User $user)
    {
        return view('admin.users.show', [
            'user' => $user, 'logs' => ActivityLog::where('user_id', $user->id)->latest()->limit(20)->get(),
            'plans' => $user->travelPlans()->count(), 'favs' => $user->favorites()->count(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $actor = $request->user();
        abort_if($user->isSuperAdmin() && ! $actor->isSuperAdmin(), 403);
        $data = $request->validate(['role' => ['required', Rule::in(User::ROLES)], 'is_suspended' => 'nullable|boolean']);
        if ($data['role'] === 'super_admin') Gate::authorize('assign-super-admin');
        $suspend = $request->boolean('is_suspended');
        abort_if($user->id === $actor->id && ($suspend || $data['role'] !== $user->role), 422, __('ui.err_self_change'));
        abort_if($user->isSuperAdmin() && $data['role'] !== 'super_admin' && User::where('role', 'super_admin')->count() === 1, 422, __('ui.err_last_super'));
        $user->forceFill(['role' => $data['role'], 'is_suspended' => $suspend])->save();
        ActivityLog::record('user.update', $user->email);
        return back()->with('status', __('ui.saved'));
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($user->id === $request->user()->id, 422, __('ui.err_self_change'));
        abort_if($user->isSuperAdmin() && ! $request->user()->isSuperAdmin(), 403);
        abort_if($user->isSuperAdmin() && User::where('role', 'super_admin')->count() === 1, 422, __('ui.err_last_super'));
        ActivityLog::record('user.delete', $user->email);
        $user->delete();
        return redirect()->route('admin.users.index')->with('status', __('ui.deleted'));
    }
}
