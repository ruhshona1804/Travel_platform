<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Flight;

class BudgetEstimator
{
    /**
     * Mamlakat uchun taxminiy byudjet stsenariylari (USD). Barchasi — taxmin.
     */
    public function scenarios(Country $country, int $travelers, int $days): array
    {
        $flight = Flight::whereHas('toCity', fn ($q) => $q->where('country_id', $country->id))
            ->whereNotNull('est_price_usd')->min('est_price_usd');

        $out = [];
        foreach (['budget', 'comfortable', 'luxury'] as $style) {
            $daily = $country->dailyBudget($style);
            if ($daily === null) continue;
            $land = $daily * $days * $travelers;
            $fl = $flight !== null ? (float) $flight * $travelers : null;
            $out[$style] = [
                'daily' => $daily,
                'land' => $land,
                'flights' => $fl,
                'total' => $land + ($fl ?? 0),
                'flights_known' => $fl !== null,
            ];
        }
        return $out;
    }

    /** "Menda $X bilan boraman" — qaysi uslub mos kelishi. */
    public function canAfford(Country $country, float $budgetUsd, int $travelers, int $days): array
    {
        $sc = $this->scenarios($country, $travelers, $days);
        $fits = collect($sc)->filter(fn ($s) => $s['total'] <= $budgetUsd)->keys()->all();
        return ['scenarios' => $sc, 'fits' => $fits, 'enough' => ! empty($fits)];
    }

    /** Jami va ulushlar. */
    public function summarize(array $items, int $travelers, int $days): array
    {
        $total = array_sum($items);
        return [
            'total' => $total,
            'per_person' => $travelers > 0 ? $total / $travelers : $total,
            'per_day' => $days > 0 ? $total / $days : $total,
        ];
    }
}
