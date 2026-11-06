<?php

namespace App\Http\Controllers;

use App\Services\Ai\Assistant;
use Illuminate\Http\Request;

class AssistantController extends Controller
{
    public function ask(Request $request, Assistant $assistant)
    {
        $data = $request->validate(['message' => 'required|string|min:2|max:1500']);
        if (! $assistant->enabled()) {
            return response()->json(['ok' => false, 'answer' => __('ui.ai_not_configured')], 200);
        }
        $answer = $assistant->ask($data['message']);
        return response()->json(['ok' => (bool) $answer, 'answer' => $answer ?? __('ui.ai_unavailable')]);
    }
}
