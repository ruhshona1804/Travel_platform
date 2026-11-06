<?php

namespace App\Http\Controllers;

use App\Models\Attraction;
use App\Models\Country;
use App\Models\Hotel;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public const TYPES = ['country' => Country::class, 'attraction' => Attraction::class, 'hotel' => Hotel::class, 'restaurant' => Restaurant::class];

    public function toggle(Request $request, string $type, int $id)
    {
        $class = self::TYPES[$type] ?? abort(404);
        $model = $class::findOrFail($id);
        $existing = $request->user()->favorites()->where('favoritable_type', $class)->where('favoritable_id', $model->id);
        if ($existing->exists()) {
            $existing->delete();
            $msg = __('ui.fav_removed');
        } else {
            $request->user()->favorites()->create(['favoritable_type' => $class, 'favoritable_id' => $model->id]);
            $msg = __('ui.fav_added');
        }
        return back()->with('status', $msg);
    }
}
