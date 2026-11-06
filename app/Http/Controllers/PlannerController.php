<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\Hotel;
use App\Models\TravelPlan;
use App\Services\Ai\AiNarrator;
use App\Services\Ai\Planner;
use App\Services\Currency\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlannerController extends Controller
{
    public const STYLES = ['budget', 'comfortable', 'luxury', 'family', 'adventure', 'historical', 'shopping', 'religious'];
    public const INTERESTS = ['history', 'nature', 'religion', 'shopping', 'family', 'adventure', 'culture'];

    public function create(Request $request, AiNarrator $ai)
    {
        return view('planner.create', [
            'countries' => Country::published()->with(['cities' => fn ($q) => $q->published()])->orderBy('slug')->get(),
            'styles' => self::STYLES, 'interests' => self::INTERESTS, 'hotelTypes' => Hotel::TYPES,
            'aiEnabled' => $ai->enabled(),
            'prefill' => $request->only(['country', 'city']),
        ]);
    }

    public function store(Request $request, Planner $planner, CurrencyService $cur)
    {
        $data = $request->validate([
            'departure_city' => 'required|string|max:80',
            'city_id' => 'required|exists:cities,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'travelers' => 'required|integer|between:1,12',
            'children' => 'nullable|integer|between:0,10',
            'budget' => 'nullable|numeric|min:0|max:1000000000',
            'currency' => 'required|string|size:3',
            'style' => 'required|in:'.implode(',', self::STYLES),
            'interests' => 'nullable|array', 'interests.*' => 'in:'.implode(',', self::INTERESTS),
            'accommodation' => 'nullable|in:'.implode(',', Hotel::TYPES),
            'food' => 'nullable|array', 'food.*' => 'in:uzbek,halal,local,international',
            'transport' => 'nullable|in:taxi,public,mixed',
        ]);
        abort_unless($cur->all()->has(strtoupper($data['currency'])), 422);
        $city = City::published()->with('country')->findOrFail($data['city_id']);

        $start = \Illuminate\Support\Carbon::parse($data['start_date']);
        $end = \Illuminate\Support\Carbon::parse($data['end_date']);
        $data['days'] = $start->diffInDays($end) + 1;
        $data['budget_usd'] = isset($data['budget']) && $data['budget'] !== null ? $cur->toUsd((float) $data['budget'], strtoupper($data['currency'])) : null;

        $result = $planner->build($data, $city, app()->getLocale());

        $plan = DB::transaction(function () use ($request, $data, $city, $result) {
            $plan = TravelPlan::create([
                'user_id' => $request->user()?->id, 'country_id' => $city->country_id, 'city_id' => $city->id,
                'title' => $city->tr('name').' — '.$data['days'].' '.__('ui.days_short'),
                'input' => collect($data)->except(['_token'])->all(), 'generated_by' => $result['generated_by'],
                'start_date' => $data['start_date'], 'end_date' => $data['end_date'],
                'total_usd' => $result['total_usd'], 'summary' => $result['summary'], 'tips' => $result['tips'],
            ]);
            foreach ($result['days'] as $d) {
                $day = $plan->days()->create(['day_number' => $d['day_number'], 'title' => $d['title'], 'cost_usd' => $d['cost_usd']]);
                foreach ($d['items'] as $it) $day->items()->create($it);
            }
            return $plan;
        });

        if (! $request->user()) session()->push('guest_plans', $plan->id);
        return redirect()->route('planner.show', $plan);
    }

    public function show(Request $request, TravelPlan $plan)
    {
        $this->authorizePlan($request, $plan);
        $plan->load('days.items', 'city.country', 'country');
        $points = $plan->days->map(fn ($d) => $d->items->filter(fn ($i) => $i->lat && $i->lng && $i->kind !== 'transport')->map(fn ($i) => [
            'name' => $i->title, 'lat' => (float) $i->lat, 'lng' => (float) $i->lng, 'type' => $i->kind,
        ])->values());
        return view('planner.show', compact('plan', 'points'));
    }

    public function destroy(Request $request, TravelPlan $plan)
    {
        abort_unless($plan->user_id === $request->user()->id, 403);
        $plan->delete();
        return back()->with('status', __('ui.deleted'));
    }

    private function authorizePlan(Request $request, TravelPlan $plan): void
    {
        $u = $request->user();
        $ok = ($u && ($plan->user_id === $u->id || $u->isAdmin()))
            || (! $plan->user_id && in_array($plan->id, (array) session('guest_plans', [])));
        abort_unless($ok, 403);
    }
}
