<?php

namespace App\Http\Controllers;

use App\Services\Currency\CurrencyService;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index(CurrencyService $svc)
    {
        return view('currency', ['currencies' => $svc->all(), 'updatedAt' => $svc->updatedAt(), 'stale' => $svc->isStale()]);
    }

    public function convert(Request $request, CurrencyService $svc)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0|max:1000000000000',
            'from' => 'required|string|size:3',
            'to' => 'required|string|size:3',
        ]);
        $from = strtoupper($data['from']); $to = strtoupper($data['to']);
        abort_unless($svc->all()->has($from) && $svc->all()->has($to), 422);
        $result = $svc->convert((float) $data['amount'], $from, $to);
        return response()->json([
            'result' => $result,
            'formatted' => $svc->formatIn($result, $to),
            'rate' => $svc->convert(1, $from, $to),
            'stale' => $svc->isStale(),
            'updated_at' => $svc->updatedAt()?->toIso8601String(),
        ]);
    }
}
