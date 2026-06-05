<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\LiveClass;
use Illuminate\View\View;

class LiveClassController extends Controller
{
    public function index(): View
    {
        $upcoming = LiveClass::with('batch')
            ->active()
            ->whereIn('status', ['scheduled', 'live'])
            ->where('scheduled_at', '>=', now()->subHours(2))
            ->orderBy('scheduled_at')
            ->get();

        $past = LiveClass::active()
            ->where(function ($q) {
                $q->where('status', 'completed')
                    ->orWhere('scheduled_at', '<', now()->subHours(2));
            })
            ->orderByDesc('scheduled_at')
            ->limit(20)
            ->get();

        return view('portal.live-classes.index', compact('upcoming', 'past'));
    }
}
