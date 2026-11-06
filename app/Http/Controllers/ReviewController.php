<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, string $type, int $id)
    {
        $class = FavoriteController::TYPES[$type] ?? abort(404);
        $data = $request->validate(['rating' => 'required|integer|between:1,5', 'body' => 'nullable|string|max:1500']);
        $model = $class::findOrFail($id);
        $request->user()->hasMany(\App\Models\Review::class)->updateOrCreate(
            ['reviewable_type' => $class, 'reviewable_id' => $model->id],
            ['rating' => $data['rating'], 'body' => strip_tags($data['body'] ?? '')]
        );
        return back()->with('status', __('ui.review_saved'));
    }
}
