<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Boshlang'ich KURSLAR TAXMINIY (source=seed) va "eskirgan" deb belgilanadi.
     * `php artisan currency:update` jonli kurslarni yuklaydi.
     */
    public function run(): void
    {
        $rows = [
            ['USD', 'US Dollar', '$', 1], ['UZS', "O'zbek so'mi", "so'm", 12000], ['EUR', 'Euro', '€', 0.92],
            ['TRY', 'Turkish Lira', '₺', 34], ['AED', 'UAE Dirham', 'AED', 3.6725], ['GBP', 'British Pound', '£', 0.79],
            ['RUB', 'Russian Ruble', '₽', 92], ['KZT', 'Kazakhstani Tenge', '₸', 480], ['KGS', 'Kyrgyzstani Som', 'KGS', 86],
            ['MYR', 'Malaysian Ringgit', 'RM', 4.5], ['THB', 'Thai Baht', '฿', 35], ['SAR', 'Saudi Riyal', 'SAR', 3.75],
            ['KRW', 'South Korean Won', '₩', 1350], ['JPY', 'Japanese Yen', '¥', 150], ['EGP', 'Egyptian Pound', 'E£', 48],
            ['IDR', 'Indonesian Rupiah', 'Rp', 15800], ['GEL', 'Georgian Lari', '₾', 2.7], ['AZN', 'Azerbaijani Manat', '₼', 1.7],
            ['CNY', 'Chinese Yuan', 'CN¥', 7.2], ['INR', 'Indian Rupee', '₹', 83],
        ];
        foreach ($rows as [$code, $name, $symbol, $rate]) {
            Currency::firstOrCreate(['code' => $code], ['name' => $name, 'symbol' => $symbol, 'rate_per_usd' => $rate, 'source' => 'seed', 'is_active' => true]);
        }
    }
}
