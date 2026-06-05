<?php

namespace App\Http\Controllers;

use App\Models\Nifty50Extended;
use Illuminate\View\View;

class Nifty50ExtendedController extends Controller
{
    public function index(): View
    {
        $rows = Nifty50Extended::orderBy('sort_order')->get();
        return view('nifty50-extended.index', compact('rows'));
    }
}
