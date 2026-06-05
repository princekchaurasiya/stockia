<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InformationLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class InformationLinkController extends Controller
{
    /**
     * List information links. SuperAdmin: all. Admin: global + their account links.
     * Table is rendered by Livewire component InformationLinksTable.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        if ($user->role !== 'superadmin' && $user->account_id === null) {
            abort(403, __('stockia.account.missing_account'));
        }

        return view('admin.information_links.index');
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        if ($user->role === 'superadmin') {
            $isGlobal = true;
            $accountId = null;
        } else {
            if ($user->account_id === null) {
                abort(403, __('stockia.account.missing_account'));
            }
            $isGlobal = false;
            $accountId = $user->account_id;
        }

        return view('admin.information_links.create', ['isGlobal' => $isGlobal, 'accountId' => $accountId]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'account_id' => 'nullable|exists:accounts,id',
        ], [], [
            'title' => __('stockia.information_link.title'),
            'url' => __('stockia.information_link.url'),
            'sort_order' => __('stockia.information_link.sort_order'),
        ]);

        $accountId = $validated['account_id'] ?? null;
        if ($user->role === 'admin') {
            if ($user->account_id === null) {
                abort(403, __('stockia.account.missing_account'));
            }
            $accountId = $user->account_id;
        }

        $link = InformationLink::create([
            'title' => $validated['title'],
            'url' => $validated['url'],
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active', true),
            'created_by' => $user->id,
            'account_id' => $accountId,
        ]);

        Log::info('Information link created', ['id' => $link->id, 'title' => $link->title]);

        $this->clearInformationLinksCache($link->account_id);

        return redirect()->route('admin.information_links.index')->with('success', __('stockia.information_link.created'));
    }

    public function edit(Request $request, InformationLink $information_link): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->role === 'superadmin') {
            // can edit any
        } elseif ($user->role === 'admin') {
            if ($user->account_id === null || $information_link->account_id !== $user->account_id) {
                abort(403);
            }
        } else {
            abort(403);
        }

        return view('admin.information_links.edit', ['link' => $information_link]);
    }

    public function update(Request $request, InformationLink $information_link): RedirectResponse
    {
        $user = $request->user();

        if ($user->role === 'superadmin') {
            // can edit any
        } elseif ($user->role === 'admin') {
            if ($user->account_id === null || $information_link->account_id !== $user->account_id) {
                abort(403);
            }
        } else {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [], [
            'title' => __('stockia.information_link.title'),
            'url' => __('stockia.information_link.url'),
            'sort_order' => __('stockia.information_link.sort_order'),
        ]);

        $information_link->update([
            'title' => $validated['title'],
            'url' => $validated['url'],
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        Log::info('Information link updated', ['id' => $information_link->id]);

        $this->clearInformationLinksCache($information_link->account_id);

        return redirect()->route('admin.information_links.index')->with('success', __('stockia.information_link.updated'));
    }

    public function destroy(Request $request, InformationLink $information_link): RedirectResponse
    {
        $user = $request->user();

        if ($user->role === 'superadmin') {
            // can delete any
        } elseif ($user->role === 'admin') {
            if ($user->account_id === null || $information_link->account_id !== $user->account_id) {
                abort(403);
            }
        } else {
            abort(403);
        }

        $id = $information_link->id;
        $title = $information_link->title;
        $accountId = $information_link->account_id;
        $information_link->delete();

        Log::info('Information link deleted', ['id' => $id, 'title' => $title]);

        $this->clearInformationLinksCache($accountId);

        return redirect()->route('admin.information_links.index')->with('success', __('stockia.information_link.deleted'));
    }

    private function clearInformationLinksCache(?int $accountId): void
    {
        Cache::forget('information_links_sidebar_global');
        if ($accountId !== null) {
            Cache::forget('information_links_sidebar_' . $accountId);
        }
    }
}
