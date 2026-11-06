<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Currency;
use App\Services\Currency\CurrencyService;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index(CurrencyService $svc)
    {
        return view('admin.currencies', [
            'currencies' => Currency::orderBy('code')->get(), 'updatedAt' => $svc->updatedAt(),
            'provider' => config('travel.exchange.provider'), 'stale' => $svc->isStale(),
        ]);
    }

    public function refresh(CurrencyService $svc)
    {
        $ok = $svc->refresh();
        ActivityLog::record('currency.refresh', $ok ? 'ok' : 'failed');
        return back()->with($ok ? 'status' : 'error', $ok ? __('ui.rates_updated') : __('ui.rates_failed'));
    }

    public function update(Request $request, Currency $currency, CurrencyService $svc)
    {
        $data = $request->validate([
            'rate_per_usd' => 'required|numeric|gt:0|max:1000000000',
            'is_manual' => 'nullable|boolean', 'is_active' => 'nullable|boolean',
        ]);
        abort_if($currency->code === 'USD' && ! $request->boolean('is_active'), 422);
        $manual = $request->boolean('is_manual');
        $currency->update([
            'rate_per_usd' => $currency->code === 'USD' ? 1 : $data['rate_per_usd'],
            'is_manual' => $manual, 'is_active' => $request->boolean('is_active'),
            'source' => $manual ? 'manual' : $currency->source, 'rate_updated_at' => now(),
        ]);
        $svc->flush();
        ActivityLog::record('currency.update', $currency->code);
        return back()->with('status', __('ui.saved'));
    }
}
