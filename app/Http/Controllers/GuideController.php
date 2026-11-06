<?php

namespace App\Http\Controllers;

use App\Models\TravelGuide;

class GuideController extends Controller
{
    public function index()
    {
        return view('guides.index', ['guides' => TravelGuide::published()->latest()->paginate(9)]);
    }

    public function show(TravelGuide $guide)
    {
        abort_unless($guide->is_published, 404);
        return view('guides.show', compact('guide'));
    }
}
