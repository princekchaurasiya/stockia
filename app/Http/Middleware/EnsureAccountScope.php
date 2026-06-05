<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountScope
{
    /**
     * Ensure the authenticated user has access to the current account context.
     * SuperAdmin: no account required (global). Admin: must have account_id. User: must have account_id.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        // SuperAdmin has no account; global access.
        if ($user->role === 'superadmin') {
            return $next($request);
        }

        // Admin must own an account (for manage links, users, etc.).
        if ($user->role === 'admin') {
            if ($user->account_id === null) {
                abort(403, __('stockia.account.missing_account'));
            }
            return $next($request);
        }

        // User may have account_id (belongs to an admin's account).
        return $next($request);
    }
}
