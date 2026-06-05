<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LearningController extends Controller
{
    public function index(): View
    {
        return view('learning.index');
    }
}

