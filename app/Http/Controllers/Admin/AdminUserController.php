<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * List admins (SuperAdmin only). Table is rendered by Livewire AdminUsersTable.
     */
    public function index(Request $request): View
    {
        if ($request->user()->role !== 'superadmin') {
            abort(403);
        }

        return view('admin.admins.index');
    }

    /**
     * Show create admin form (SuperAdmin only).
     */
    public function create(Request $request): View
    {
        if ($request->user()->role !== 'superadmin') {
            abort(403);
        }

        return view('admin.admins.create');
    }

    /**
     * Create admin account and user. SuperAdmin only.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->role !== 'superadmin') {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [], [
            'name' => __('stockia.account.admin_name'),
            'email' => __('stockia.account.admin_email'),
            'password' => __('stockia.account.admin_password'),
        ]);

        $admin = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
        ]);

        $account = Account::create([
            'name' => $validated['name'] . ' Account',
            'owner_id' => $admin->id,
        ]);

        $admin->update(['account_id' => $account->id]);

        return redirect()->route('admin.admins.index')->with('success', __('stockia.account.admin_created'));
    }
}
