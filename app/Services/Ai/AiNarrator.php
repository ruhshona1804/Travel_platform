<?php

namespace App\Services\Ai;

use App\Models\City;
use Illuminate\Support\Facades\Http;

/**
 * Ixtiyoriy AI qatlami (sozlanadigan provayder). U faqat matn (xulosa, sarlavha, maslahat) yozadi;
 * narx, bandlik yoki parvoz jadvalini o'zgartira olmaydi. Xatolikda null qaytaradi —
 * shunda qoidalarga asoslangan reja ishlatiladi.
 */
class AiNarrator
{
    public function enabled(): bool
    {
        return config('travel.ai.provider') === 'anthropic' && filled(config('travel.ai.api_key'));
    }

    public function narrate(City $city, array $days, array $summary, array $input, string $locale): ?array
    {
        if (! $this->enabled()) return null;

        $langName = config("travel.locales.$locale.name", 'Uzbek');
        $compact = collect($days)->map(fn ($d) => [
            'day' => $d['day_number'],
            'places' => collect($d['items'])->whereIn('kind', ['attraction', 'restaurant'])->pluck('title')->values(),
        ]);

        $system = "You write short travel copy for an Uzbek-focused travel platform. Reply ONLY with JSON: "
            .'{"summary": string, "day_titles": string[], "tips": string[]}. '
            ."Write in {$langName}. Use ONLY the places given. Never invent prices, hotel availability, flight schedules or visa rules. "
            .'day_titles must have exactly one short title per day. tips: max 5 practical tips.';
        $user = json_encode(['city' => $city->tr('name'), 'days' => $compact, 'style' => $input['style'] ?? null, 'interests' => $input['interests'] ?? []], JSON_UNESCAPED_UNICODE);

        try {
            $res = Http::withHeaders(['x-api-key' => config('travel.ai.api_key'), 'anthropic-version' => '2023-06-01'])
                ->timeout(config('travel.ai.timeout'))
                ->post('https://api.anthropic.com/v1/messages', [
                    'model' => config('travel.ai.model'),
                    'max_tokens' => 1200,
                    'system' => $system,
                    'messages' => [['role' => 'user', 'content' => $user]],
                ]);
            if (! $res->ok()) return null;
            $text = $res->json('content.0.text', '');
            if (preg_match('/\{.*\}/s', $text, $m)) {
                $data = json_decode($m[0], true);
                if (is_array($data)) {
                    return [
                        'summary' => is_string($data['summary'] ?? null) ? strip_tags($data['summary']) : null,
                        'day_titles' => array_map(fn ($t) => strip_tags((string) $t), (array) ($data['day_titles'] ?? [])),
                        'tips' => array_map(fn ($t) => strip_tags((string) $t), (array) ($data['tips'] ?? [])),
                    ];
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }
        return null;
    }
}
