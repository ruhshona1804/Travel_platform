<?php

namespace App\Console\Commands;

use App\Services\Currency\CurrencyService;
use Illuminate\Console\Command;

class UpdateExchangeRates extends Command
{
    protected $signature = 'currency:update';
    protected $description = 'Valyuta kurslarini sozlangan provayderdan yangilash';

    public function handle(CurrencyService $svc): int
    {
        if ($svc->refresh()) {
            $this->info('Kurslar yangilandi.');
            return self::SUCCESS;
        }
        $this->warn("Kurslarni yangilab bo'lmadi. Oxirgi saqlangan kurslar ishlatiladi.");
        return self::FAILURE;
    }
}
