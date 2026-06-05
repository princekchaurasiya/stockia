<?php

namespace App\Http\Controllers;

use App\Models\InformationLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class InformationWebsiteController extends Controller
{
    /**
     * List all information websites (global + account-specific) for the authenticated user.
     * Cached 5 minutes; invalidated when admin creates/updates/deletes a link.
     */
    public function index(Request $request): View
    {
        $accountId = $request->user()->account_id;
        $cacheKey = 'information_links_sidebar_' . ($accountId ?? 'global');

        $links = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($accountId) {
            return InformationLink::query()
                ->where('is_active', true)
                ->where(function ($query) use ($accountId) {
                    $query->whereNull('account_id')
                        ->orWhere('account_id', $accountId);
                })
                ->orderByRaw('account_id IS NOT NULL')
                ->orderBy('sort_order')
                ->get(['id', 'title', 'url', 'sort_order']);
        });

        return view('information-websites.index', compact('links'));
    }
}
