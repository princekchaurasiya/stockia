<?php

namespace App\Http\Controllers;

use App\Models\DataSourceLink;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $dataSourceLinks = DataSourceLink::where('is_active', true)->orderBy('name')->get();
        return view('home', compact('dataSourceLinks'));
    }
}
