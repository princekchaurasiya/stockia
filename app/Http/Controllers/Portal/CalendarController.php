<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(): View
    {
        $events = CalendarEvent::active()
            ->where('event_date', '>=', now()->startOfMonth()->subMonth())
            ->orderBy('event_date')
            ->get()
            ->groupBy(fn ($e) => $e->event_date->format('Y-m'));

        return view('portal.calendar.index', compact('events'));
    }
}
