<?php

namespace App\Services\Currency;

use App\Models\Currency;
use Illuminate\Support\Facades\Cache;

class CurrencyService
{
    public function __construct(private ExchangeRateProvider $provider) {}

    public function all()
    {
        // Keshga faqat oddiy massivlar saqlanadi (Eloquent obyektlari emas).
        $rows = Cache::remember('currencies.active', 600, fn () => Currency::where('is_active', true)->orderBy('code')->get()
            ->map(fn ($c) => ['code' => $c->code, 'name' => $c->name, 'symbol' => $c->symbol, 'rate_per_usd' => (float) $c->rate_per_usd,
                'is_manual' => (bool) $c->is_manual, 'source' => $c->source, 'rate_updated_at' => $c->rate_updated_at?->toIso8601String()])->all());
        return collect($rows)->map(fn ($r) => (object) ($r + ['rate_updated_at_c' => null]))->each(function ($o) {
            $o->rate_updated_at = $o->rate_updated_at ? \Illuminate\Support\Carbon::parse($o->rate_updated_at) : null;
        })->keyBy('code');
    }

    public function flush(): void { Cache::forget('currencies.active'); }

    public function current(): string
    {
        $code = session('currency') ?? auth()->user()?->currency ?? config('travel.default_currency');
        return $this->all()->has($code) ? $code : 'USD';
    }

    public function rate(string $code): float
    {
        return (float) ($this->all()[$code]->rate_per_usd ?? 1.0);
    }

    public function convert(float $amount, string $from, string $to): float
    {
        return $amount / $this->rate($from) * $this->rate($to);
    }

    public function fromUsd(float $usd, string $to): float { return $usd * $this->rate($to); }
    public function toUsd(float $amount, string $from): float { return $amount / $this->rate($from); }

    public function symbol(string $code): string { return $this->all()[$code]->symbol ?? $code; }

    public function formatIn(float $amount, string $code): string
    {
        $decimals = in_array($code, ['UZS', 'KZT', 'KRW', 'JPY', 'IDR', 'KGS', 'RUB'], true) || abs($amount) >= 100 ? 0 : 2;
        $n = number_format($amount, $decimals, '.', ',');
        return in_array($code, ['USD', 'EUR', 'GBP'], true) ? $this->symbol($code).$n : $n.' '.$code;
    }

    /** Narx: tanlangan valyutada va UZS (yoki USD) da: "$100 ≈ 1,200,000 UZS". */
    public function format(?float $usd): string
    {
        if ($usd === null) return '—';
        $cur = $this->current();
        $other = $cur === 'UZS' ? 'USD' : 'UZS';
        return $this->formatIn($this->fromUsd($usd, $cur), $cur).' ≈ '.$this->formatIn($this->fromUsd($usd, $other), $other);
    }

    public function updatedAt(): ?\Illuminate\Support\Carbon
    {
        return $this->all()->where('is_manual', false)->where('source', 'api')->max('rate_updated_at');
    }

    /** UZS kursi API orqali yangilanmagan yoki eskirgan bo'lsa true. */
    public function isStale(): bool
    {
        $uzs = $this->all()['UZS'] ?? null;
        if (! $uzs || $uzs->source !== 'api' || ! $uzs->rate_updated_at) return true;
        return $uzs->rate_updated_at->lt(now()->subHours(config('travel.exchange.stale_after_hours')));
    }

    /** Provayderdan kurslarni yangilaydi. Muvaffaqiyatsiz bo'lsa eski kurslar saqlanadi. */
    public function refresh(): bool
    {
        $rates = $this->provider->fetchRatesPerUsd();
        if (! $rates) return false;
        foreach (Currency::where('is_manual', false)->get() as $c) {
            if (isset($rates[$c->code]) && $rates[$c->code] > 0) {
                $c->update(['rate_per_usd' => $rates[$c->code], 'source' => 'api', 'rate_updated_at' => now()]);
            }
        }
        $this->flush();
        return true;
    }
}
