<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Country;
use App\Services\BudgetEstimator;
use App\Services\Currency\CurrencyService;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function create(Request $request)
    {
        return view('budget', [
            'countries' => Country::published()->orderBy('slug')->get(),
            'categories' => Budget::CATEGORIES,
        ]);
    }

    /** "Turkiyaga $500 bilan borsa bo'ladimi?" — taxminiy stsenariylar (JSON). */
    public function check(Request $request, BudgetEstimator $est, CurrencyService $cur)
    {
        $d = $request->validate([
            'country' => 'required|exists:countries,id', 'budget' => 'required|numeric|min:1|max:1000000000',
            'currency' => 'required|string|size:3', 'travelers' => 'required|integer|between:1,12', 'days' => 'required|integer|between:1,60',
        ]);
        abort_unless($cur->all()->has(strtoupper($d['currency'])), 422);
        $country = Country::findOrFail($d['country']);
        $usd = $cur->toUsd((float) $d['budget'], strtoupper($d['currency']));
        $r = $est->canAfford($country, $usd, (int) $d['travelers'], (int) $d['days']);
        $r['budget_usd'] = $usd;
        $r['country'] = $country->tr('name');
        return response()->json($r);
    }

    public function store(Request $request, CurrencyService $cur)
    {
        $d = $request->validate([
            'title' => 'required|string|max:120', 'country_id' => 'nullable|exists:countries,id',
            'travelers' => 'required|integer|between:1,50', 'days' => 'required|integer|between:1,365',
            'currency' => 'required|string|size:3',
            'items' => 'required|array', 'items.*' => 'nullable|numeric|min:0|max:1000000000',
        ]);
        $currency = strtoupper($d['currency']);
        abort_unless($cur->all()->has($currency), 422);
        $budget = $request->user()->budgets()->create([
            'title' => $d['title'], 'country_id' => $d['country_id'] ?? null, 'travelers' => $d['travelers'], 'days' => $d['days'], 'currency' => $currency,
        ]);
        foreach ($d['items'] as $cat => $amount) {
            if (in_array($cat, Budget::CATEGORIES, true) && $amount !== null) {
                $budget->items()->create(['category' => $cat, 'amount_usd' => $cur->toUsd((float) $amount, $currency)]);
            }
        }
        return redirect()->route('dashboard.budgets')->with('status', __('ui.saved'));
    }

    public function destroy(Request $request, Budget $budget)
    {
        abort_unless($budget->user_id === $request->user()->id, 403);
        $budget->delete();
        return back()->with('status', __('ui.deleted'));
    }
}
