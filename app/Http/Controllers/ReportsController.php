<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ReportsController extends Controller
{
    public function index(): View
    {
        return view('reports.index');
    }
}
