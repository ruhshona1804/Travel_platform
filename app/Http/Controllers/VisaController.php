<?php

namespace App\Http\Controllers;

use App\Models\Country;

class VisaController extends Controller
{
    public function index()
    {
        return view('visa.index', ['countries' => Country::published()->with('visa')->orderBy('slug')->get()]);
    }
}
