<?php

namespace App\Services\Currency;

interface ExchangeRateProvider
{
    /** @return array<string,float>|null  1 USD = X birlik (kod => kurs). Xatolikda null. */
    public function fetchRatesPerUsd(): ?array;
}
