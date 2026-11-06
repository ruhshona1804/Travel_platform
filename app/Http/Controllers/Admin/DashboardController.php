<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Attraction;
use App\Models\City;
use App\Models\Country;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\TravelPlan;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.index', [
            'stats' => [
                'users' => User::count(), 'countries' => Country::count(), 'cities' => City::count(),
                'attractions' => Attraction::count(), 'hotels' => Hotel::count(), 'restaurants' => Restaurant::count(), 'plans' => TravelPlan::count(),
            ],
            'recentUsers' => User::latest()->limit(6)->get(),
            'activity' => ActivityLog::with('user')->latest()->limit(10)->get(),
        ]);
    }
}
