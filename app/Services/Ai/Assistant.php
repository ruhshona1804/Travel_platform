<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;

/**
 * AI yordamchi (chat). Foydalanuvchi qaysi tilda yozsa, shu tilda javob beradi.
 * API sozlanmagan bo'lsa, null qaytaradi.
 */
class Assistant
{
    public function enabled(): bool
    {
        return config('travel.ai.provider') === 'anthropic' && filled(config('travel.ai.api_key'));
    }

    public function ask(string $message): ?string
    {
        if (! $this->enabled()) return null;
        $system = "You are the travel assistant of '".app_name()."', a travel platform for travelers from Uzbekistan. "
            .'Always answer in the SAME language the user writes in (default Uzbek, Latin script). '
            .'Be concise and practical. Never claim to know live prices, flight schedules, hotel availability or current visa rules; '
            .'tell the user to verify on official sources.';
        try {
            $res = Http::withHeaders(['x-api-key' => config('travel.ai.api_key'), 'anthropic-version' => '2023-06-01'])
                ->timeout(config('travel.ai.timeout'))
                ->post('https://api.anthropic.com/v1/messages', [
                    'model' => config('travel.ai.model'), 'max_tokens' => 700, 'system' => $system,
                    'messages' => [['role' => 'user', 'content' => mb_substr($message, 0, 1500)]],
                ]);
            return $res->ok() ? strip_tags((string) $res->json('content.0.text')) : null;
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }
}
