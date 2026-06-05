<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ImpersonationController extends Controller
{
    /**
     * Start impersonating an admin. SuperAdmin only.
     * Allowed: superadmin → admin only.
     * Never: admin → *, superadmin → superadmin, superadmin → user.
     */
    public function store(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->role !== 'superadmin') {
            abort(403);
        }

        if ($user->role === 'superadmin') {
            abort(403, __('stockia.impersonation.cannot_impersonate_superadmin'));
        }

        if ($user->role !== 'admin') {
            abort(403, __('stockia.impersonation.invalid_target'));
        }

        if (! $user->exists || $user->account_id === null) {
            abort(404, __('stockia.impersonation.missing_account'));
        }

        $impersonatorId = $request->user()->id;
        session()->put('impersonator_id', $impersonatorId);
        Auth::loginUsingId($user->id);

        Log::info('Impersonation started', [
            'impersonator_id' => $impersonatorId,
            'impersonated_id' => $user->id,
        ]);

        return redirect()->route('dashboard')->with('success', __('stockia.impersonation.started'));
    }

    /**
     * Stop impersonation and restore SuperAdmin session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $impersonatorId = session('impersonator_id');
        if ($impersonatorId === null) {
            return redirect()->route('dashboard')->with('error', __('stockia.impersonation.not_impersonating'));
        }

        $user = User::find($impersonatorId);
        if (! $user) {
            session()->forget('impersonator_id');
            Auth::logout();
            return redirect()->route('login')->with('error', __('stockia.impersonation.deleted_admin'));
        }

        session()->forget('impersonator_id');
        Auth::loginUsingId($impersonatorId);

        Log::info('Impersonation stopped', ['impersonator_id' => $impersonatorId]);

        return redirect()->route('dashboard')->with('success', __('stockia.impersonation.stopped'));
    }
}
