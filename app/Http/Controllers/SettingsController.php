<?php

namespace App\Http\Controllers;

use App\Support\SettingsMap;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('settings.index', [
            'groups' => SettingsMap::groupsFor(auth()->user()),
        ]);
    }
}
