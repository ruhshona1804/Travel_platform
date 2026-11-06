<?php

namespace Database\Seeders;

use App\Models\TravelGuide;
use Illuminate\Database\Seeder;

class GuideSeeder extends Seeder
{
    public function run(): void
    {
        $guides = [
            ['birinchi-marta-xorijga', ["uz" => "Birinchi marta xorijga: nimalarni bilish kerak", "ru" => "Первый раз за границу: что нужно знать", "en" => "Your first trip abroad: what to know"],
                ["uz" => "Hujjatlar, sug'urta va byudjetni oldindan rejalashtiring.", "ru" => "Заранее спланируйте документы, страховку и бюджет.", "en" => "Plan documents, insurance and budget ahead of time."],
                ["uz" => "1. Xorijiy pasport muddatini tekshiring — ko'p mamlakatlar kamida 6 oy talab qiladi (rasmiy manbada aniqlang).\n2. Viza talablarini faqat rasmiy davlat saytlaridan oling.\n3. Sayohat sug'urtasini rasmiylashtiring.\n4. Naqd pul va karta uchun zaxira rejasini tuzing.\n5. Muhim hujjatlarning nusxalarini telefon va qog'ozda saqlang.", "ru" => "1. Проверьте срок действия загранпаспорта.\n2. Визовые требования смотрите только на официальных сайтах.\n3. Оформите страховку.\n4. Подготовьте запасной план по деньгам.\n5. Храните копии документов.", "en" => "1. Check passport validity.\n2. Get visa rules only from official government sites.\n3. Buy travel insurance.\n4. Have a backup plan for cash and cards.\n5. Keep copies of key documents."]],
            ['byudjetni-tejash', ["uz" => "Xorijda byudjetni qanday tejash mumkin", "ru" => "Как экономить бюджет за границей", "en" => "How to save money abroad"],
                ["uz" => "Oddiy odatlar sayohat xarajatlarini sezilarli kamaytiradi.", "ru" => "Простые привычки заметно снижают расходы.", "en" => "Simple habits noticeably cut travel costs."],
                ["uz" => "• Aeroport transferi uchun jamoat transporti yoki rasmiy ilovalarni solishtiring.\n• Turistik markazdan biroz uzoqroq joyda mehmonxona tanlang.\n• Mahalliy oshxona va o'zbek oshxonalarini solishtiring.\n• Valyuta kursini bank va almashtirish punktlarida taqqoslang.", "ru" => "• Сравнивайте общественный транспорт и официальные приложения.\n• Выбирайте жильё чуть дальше от центра.\n• Сравнивайте местные кафе.\n• Сравнивайте курсы обмена.", "en" => "• Compare public transport and official apps for airport transfers.\n• Pick lodging slightly away from the tourist centre.\n• Compare local eateries.\n• Compare exchange rates."]],
            ['ozbek-taomlari', ["uz" => "Xorijda o'zbek taomlarini qanday topish mumkin", "ru" => "Как найти узбекскую кухню за границей", "en" => "Finding Uzbek food abroad"],
                ["uz" => "Xaritadagi «O'zbek oshxonalari» bo'limidan foydalaning.", "ru" => "Используйте раздел «Узбекская кухня» на карте.", "en" => "Use the Uzbek food section on the map."],
                ["uz" => "Platformadagi «Xorijda o'zbek taomlari» bo'limida mamlakat va shahar bo'yicha oshxonalarni ko'rishingiz mumkin. Borishdan oldin ish vaqti va narxlarni restoranning rasmiy sahifasi orqali tekshiring.", "ru" => "В разделе «Узбекская кухня» можно смотреть заведения по странам и городам. Перед визитом проверьте часы работы и цены.", "en" => "Browse venues by country and city in the Uzbek food section. Check opening hours and prices before visiting."]],
        ];
        foreach ($guides as [$slug, $title, $excerpt, $body]) {
            TravelGuide::updateOrCreate(['slug' => $slug], ['title' => $title, 'excerpt' => $excerpt, 'body' => $body]);
        }
    }
}
