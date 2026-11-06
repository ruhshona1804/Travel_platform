<?php

namespace App\Services\Currency;

use Illuminate\Support\Facades\Http;

/** open.er-api.com — kalitsiz bepul manba (sozlanadigan adapter). */
class OpenErApiProvider implements ExchangeRateProvider
{
    public function fetchRatesPerUsd(): ?array
    {
        try {
            $res = Http::timeout(15)->get('https://open.er-api.com/v6/latest/USD');
            if ($res->ok() && ($res->json('result') === 'success') && is_array($res->json('rates'))) {
                return $res->json('rates');
            }
        } catch (\Throwable $e) {
            report($e);
        }
        return null;
    }
}
