<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class UploadsController extends Controller
{
    public function index(): View
    {
        return view('uploads.index');
    }
}
