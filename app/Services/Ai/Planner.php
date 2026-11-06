<?php

namespace App\Services\Ai;

use App\Models\City;
use App\Models\Hotel;
use App\Models\Restaurant;

class Planner
{
    // Qo'llanma taxminlari (USD, bir kishi/kun). Tasdiqlangan narx EMAS.
    private const LOCAL_TRANSPORT_PER_DAY = ['budget' => 4, 'comfortable' => 12, 'luxury' => 35];
    private const INTEREST_CATEGORIES = [
        'history' => ['historical', 'museum', 'cultural'],
        'nature' => ['nature', 'mountain', 'beach', 'park'],
        'religion' => ['religious'],
        'shopping' => ['shopping'],
        'family' => ['family', 'park', 'entertainment'],
        'adventure' => ['mountain', 'nature', 'entertainment'],
        'culture' => ['cultural', 'museum', 'historical'],
    ];
    private const STYLE_CATEGORIES = [
        'historical' => ['historical', 'museum', 'cultural'],
        'shopping' => ['shopping'],
        'religious' => ['religious', 'historical'],
        'family' => ['family', 'park', 'entertainment'],
        'adventure' => ['mountain', 'nature', 'entertainment'],
    ];

    public function __construct(private AiNarrator $narrator, private \App\Services\FlightService $flights) {}

    /**
     * Qoidalarga asoslangan reja. Faqat bazadagi ma'lumotlardan foydalanadi;
     * narxlar taxminiy, mehmonxona bandligi va parvoz jadvali o'ylab topilmaydi.
     */
    public function build(array $in, City $city, string $locale): array
    {
        $days = max(1, min(21, (int) $in['days']));
        $adults = max(1, (int) $in['travelers']);
        $children = max(0, (int) ($in['children'] ?? 0));
        $people = $adults + $children;
        $style = $in['style'] ?? 'comfortable';
        $budgetStyle = in_array($style, ['budget', 'luxury'], true) ? $style : 'comfortable';
        $budgetUsd = $in['budget_usd'] ?? null;

        // Mehmonxona
        $nights = max(1, $days - 1);
        $rooms = (int) ceil($adults / 2) + ($children > 0 && $adults <= 2 ? 0 : 0);
        $hotel = $this->pickHotel($city, $in['accommodation'] ?? null, $budgetUsd ? $budgetUsd * 0.4 / $nights / $rooms : null, $budgetStyle);
        $hotelCost = $hotel && $hotel->price_usd !== null ? (float) $hotel->price_usd * $nights * $rooms : 0.0;

        // Parvoz (faqat bazadagi namunaviy yozuv)
        $flight = $this->flights->forCity($city->id)->first();
        $flightCost = $flight && $flight->est_price_usd !== null ? (float) $flight->est_price_usd * $people : 0.0;

        $attractions = $this->rankAttractions($city, $in['interests'] ?? [], $style);
        $restaurants = $this->rankRestaurants($city, $in['food'] ?? [], $budgetStyle);

        $perDay = (int) min(3, max(1, ceil(max(1, $attractions->count()) / $days)));
        $transportPerDay = self::LOCAL_TRANSPORT_PER_DAY[$budgetStyle] * $people;
        $planDays = [];
        $ai = 0; $ri = 0;
        $used = collect();

        for ($d = 1; $d <= $days; $d++) {
            $items = [];
            $pos = 0;
            if ($d === 1 && $hotel) {
                $items[] = $this->item('hotel', $hotel->tr('name'), __('ui.plan_checkin'), 0, $hotel, true, $pos++);
            }
            $todays = $attractions->slice($ai, $perDay)->values();
            $ai += $perDay;
            foreach ($this->nearestOrder($todays) as $a) {
                $cost = $a->entrance_usd !== null ? (float) $a->entrance_usd * $people : 0.0;
                $items[] = $this->item('attraction', $a->tr('name'),
                    $a->entrance_usd === null ? __('ui.plan_price_unknown') : null, $cost, $a, ! $a->price_verified, $pos++);
            }
            $mealCost = 0.0;
            for ($m = 0; $m < 2 && $restaurants->isNotEmpty(); $m++) {
                $r = $restaurants[$ri % $restaurants->count()];
                $ri++;
                $c = $r->avg_meal_usd !== null ? (float) $r->avg_meal_usd * $people : 0.0;
                $mealCost += $c;
                $items[] = $this->item('restaurant', $r->tr('name'), $r->is_uzbek ? __('ui.plan_uzbek_food') : null, $c, $r, true, $pos++);
            }
            $items[] = $this->item('transport', __('ui.plan_local_transport'), __('ui.plan_estimate'), $transportPerDay, null, true, $pos++);
            $dayCost = array_sum(array_column($items, 'cost_usd'));
            $planDays[] = ['day_number' => $d, 'title' => __('ui.plan_day', ['n' => $d]), 'cost_usd' => $dayCost, 'items' => $items];
        }

        $total = $flightCost + $hotelCost + array_sum(array_column($planDays, 'cost_usd'));
        $summary = [
            'flight' => $flight ? ['airline' => $flight->airline->name, 'price_usd' => $flightCost, 'from' => $flight->from_city, 'sample' => true] : null,
            'hotel' => $hotel ? ['name' => $hotel->tr('name'), 'price_usd' => $hotelCost, 'per_night' => $hotel->price_usd, 'nights' => $nights, 'rooms' => $rooms, 'slug' => $hotel->slug] : null,
            'hotel_cost' => $hotelCost, 'flight_cost' => $flightCost, 'people' => $people, 'days' => $days,
            'over_budget' => $budgetUsd !== null ? $total > $budgetUsd : null,
            'budget_usd' => $budgetUsd,
        ];

        $tips = [__('ui.plan_tip_prices'), __('ui.plan_tip_visa')];
        if ($budgetUsd !== null && $total > $budgetUsd) {
            $tips[] = __('ui.plan_tip_over', ['diff' => number_format($total - $budgetUsd, 0)]);
        }
        $generatedBy = 'rules';

        $ai = $this->narrator->narrate($city, $planDays, $summary, $in, $locale);
        if ($ai) {
            $summary['narrative'] = $ai['summary'] ?? null;
            $tips = array_merge($tips, array_slice($ai['tips'] ?? [], 0, 6));
            foreach ($planDays as $i => &$pd) {
                if (! empty($ai['day_titles'][$i])) $pd['title'] = (string) $ai['day_titles'][$i];
            }
            unset($pd);
            $generatedBy = 'ai';
        }

        return ['days' => $planDays, 'total_usd' => $total, 'summary' => $summary, 'tips' => $tips, 'generated_by' => $generatedBy];
    }

    private function item(string $kind, string $title, ?string $note, float $cost, $model, bool $estimated, int $pos): array
    {
        return [
            'kind' => $kind, 'title' => $title, 'note' => $note, 'cost_usd' => $cost, 'cost_estimated' => $estimated,
            'itemable_type' => $model ? $model::class : null, 'itemable_id' => $model?->id,
            'lat' => $model?->lat, 'lng' => $model?->lng, 'position' => $pos,
        ];
    }

    private function pickHotel(City $city, ?string $type, ?float $maxNight, string $style): ?Hotel
    {
        $q = $city->hotels()->published();
        $all = $q->get();
        if ($all->isEmpty()) return null;
        $typed = $type ? $all->where('type', $type) : $all;
        $pool = $typed->isNotEmpty() ? $typed : $all;
        if ($style === 'luxury') return $pool->sortByDesc('stars')->first();
        $fit = $maxNight ? $pool->filter(fn ($h) => $h->price_usd !== null && $h->price_usd <= $maxNight) : collect();
        $pool = $fit->isNotEmpty() ? $fit : $pool->sortBy(fn ($h) => $h->price_usd ?? PHP_INT_MAX);
        return $pool->sortByDesc(fn ($h) => (float) $h->rating)->first() ?? $pool->first();
    }

    private function rankAttractions(City $city, array $interests, string $style)
    {
        $want = collect($interests)->flatMap(fn ($i) => self::INTEREST_CATEGORIES[$i] ?? [])
            ->merge(self::STYLE_CATEGORIES[$style] ?? [])->unique();
        return $city->attractions()->published()->get()->sortByDesc(
            fn ($a) => ($want->contains($a->category) ? 100 : 0) + (float) $a->rating * 5 + $a->popularity / 100
        )->values();
    }

    private function rankRestaurants(City $city, array $food, string $style)
    {
        $all = $city->restaurants()->published()->get();
        $pref = $all->filter(fn ($r) => (in_array('uzbek', $food) && $r->is_uzbek) || (in_array('halal', $food) && $r->is_halal));
        $pool = $pref->isNotEmpty() ? $pref->concat($all->diff($pref)) : $all;
        if ($style === 'budget') {
            $pool = $pool->sortBy(fn ($r) => ($pref->contains($r) ? 0 : 1000) + (float) ($r->avg_meal_usd ?? 99))->values();
        }
        return $pool->values();
    }

    /** Eng yaqin qo'shni tartibi (kun ichida yo'lni qisqartirish uchun). */
    private function nearestOrder($items): array
    {
        $items = $items->all();
        if (count($items) < 3) return $items;
        $ordered = [array_shift($items)];
        while ($items) {
            $last = end($ordered);
            usort($items, fn ($a, $b) => ($a->distanceTo((float) $last->lat, (float) $last->lng) ?? 0) <=> ($b->distanceTo((float) $last->lat, (float) $last->lng) ?? 0));
            $ordered[] = array_shift($items);
        }
        return $ordered;
    }
}
