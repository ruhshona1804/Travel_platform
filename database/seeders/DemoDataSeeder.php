<?php

namespace Database\Seeders;

use App\Models\Airline;
use App\Models\Airport;
use App\Models\Attraction;
use App\Models\City;
use App\Models\Country;
use App\Models\Flight;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\TransportService;
use App\Models\VisaRequirement;
use Illuminate\Database\Seeder;

/**
 * DEMO MA'LUMOTLAR. Mehmonxona/restoran nomlari va narxlar namunaviy (is_demo=true), real emas.
 * Diqqatga sazovor joylarning koordinatalari ommaviy xaritalardan olingan taxminiy qiymatlar.
 * Viza qoidalari "tekshirilmagan" deb belgilangan — admin rasmiy manba bilan tasdiqlaydi.
 */
class DemoDataSeeder extends Seeder
{
    private function t(string $uz, ?string $ru = null, ?string $en = null, ?string $kk = null, ?string $ky = null, ?string $kaa = null): array
    {
        return array_filter(compact('uz', 'ru', 'en', 'kk', 'ky', 'kaa'));
    }

    public function run(): void
    {
        $countries = [
            ['turkey', 'TR', $this->t('Turkiya', 'Турция', 'Turkey', 'Түркия', 'Түркия', 'Túrkiya'), $this->t('Anqara', 'Анкара', 'Ankara'), 'asia', 'Turk', 'TRY', 'UTC+3', 'spring,autumn', ['budget', 'comfortable', 'family', 'historical', 'shopping', 'religious', 'adventure'], [35, 70, 160], 100,
                $this->t("Turkiya — Yevropa va Osiyo tutashgan joyda joylashgan mamlakat. Istanbul, Antalya va Kappadokiya eng mashhur yo'nalishlar. Ko'plab o'zbekistonliklar bu yerga tarixiy joylar, dengiz va xarid uchun boradi.", 'Турция расположена на стыке Европы и Азии. Стамбул, Анталья и Каппадокия — самые популярные направления.', 'Turkey sits where Europe meets Asia. Istanbul, Antalya and Cappadocia are the most popular destinations.'),
                $this->t("O'rta dengiz qirg'oqlarida yoz issiq va quruq, qish yumshoq. Istanbulda to'rt fasl aniq seziladi. Eng qulay mavsum — bahor va kuz.", 'На побережье Средиземного моря лето жаркое и сухое, зима мягкая. Лучшее время — весна и осень.', 'Mediterranean coast: hot dry summers and mild winters. Spring and autumn are the most comfortable seasons.'),
                $this->t("Turistik hududlar odatda xavfsiz. Gavjum joylarda hamyon va hujjatlarni ehtiyot qiling, faqat litsenziyali taksi va rasmiy ilovalardan foydalaning.", 'Туристические районы в целом безопасны. В людных местах следите за кошельком и документами.', 'Tourist areas are generally safe. Watch your wallet and documents in crowded places.'),
                $this->t("Masjidlarga kirishda yelka va tizzalar yopiq bo'lishi, oyoq kiyim yechilishi kerak. Bozorlarda savdolashish odatiy hol.", 'При посещении мечетей плечи и колени должны быть закрыты, обувь снимают. В базарах принято торговаться.', 'Cover shoulders and knees and remove shoes in mosques. Bargaining is normal in bazaars.'),
                $this->t("Istanbulda metro, tramvay, Marmaray va avtobuslar uchun yagona «Istanbulkart» kartasi qulay. Shaharlararo yo'nalishlarda avtobus va ichki reyslar rivojlangan.", 'В Стамбуле удобна единая карта «Истанбулкарт» для метро, трамваев, Мармарая и автобусов.', 'In Istanbul a single Istanbulkart works on metro, tram, Marmaray and buses.'),
                ['Uber', 'BiTaksi', 'Istanbulkart', 'Google Maps'], ['Politsiya / tez yordam / yong\'in' => '112']],
            ['uae', 'AE', $this->t('Birlashgan Arab Amirliklari', 'Объединённые Арабские Эмираты', 'United Arab Emirates', 'Біріккен Араб Әмірліктері', 'Бириккен Араб Эмираттары', 'Birlesken Arab Ámirlikleri'), $this->t('Abu-Dabi', 'Абу-Даби', 'Abu Dhabi'), 'asia', 'Arab, Ingliz', 'AED', 'UTC+4', 'autumn,winter,spring', ['comfortable', 'luxury', 'family', 'shopping'], [60, 130, 350], 95,
                $this->t("BAA — zamonaviy arxitektura, xarid markazlari va qulay infratuzilma mamlakati. Dubay va Abu-Dabi eng ko'p tashrif buyuriladigan shaharlar.", 'ОАЭ — страна современной архитектуры, торговых центров и удобной инфраструктуры. Дубай и Абу-Даби — самые посещаемые города.', 'The UAE is known for modern architecture, shopping malls and convenient infrastructure. Dubai and Abu Dhabi are the most visited cities.'),
                $this->t("Yozda juda issiq (40°C dan yuqori bo'lishi mumkin), eng qulay vaqt — noyabrdan martgacha.", 'Летом очень жарко (может быть выше 40°C), лучшее время — с ноября по март.', 'Summers are very hot (can exceed 40°C); the best time is November to March.'),
                $this->t("Xavfsizlik darajasi yuqori, ammo mahalliy qonunlar qat'iy. Jamoat joylarida xulq-atvor va kiyim qoidalariga rioya qiling.", 'Уровень безопасности высокий, но местные законы строги. Соблюдайте правила поведения и одежды в общественных местах.', 'Safety is high but local laws are strict. Respect conduct and dress rules in public places.'),
                $this->t("Ramazon oyida kunduzi jamoat joylarida ovqatlanish va ichish cheklangan. Alkogol va boshqa masalalarda mahalliy qonunlarni oldindan o'rganing.", null, 'During Ramadan, eating and drinking in public during daylight is restricted. Check local laws in advance.'),
                $this->t("Dubayda metro va tramvay RTA tomonidan boshqariladi; «Nol» kartasi bilan to'lanadi. Taksi ilovalari keng tarqalgan.", null, 'Dubai metro and tram are run by RTA and paid with a Nol card. Ride-hailing apps are widely used.'),
                ['Careem', 'Uber', 'RTA Dubai'], ['Politsiya' => '999', 'Tez yordam' => '998']],
            ['malaysia', 'MY', $this->t('Malayziya', 'Малайзия', 'Malaysia', 'Малайзия', 'Малайзия', 'Malayziya'), $this->t('Kuala-Lumpur', 'Куала-Лумпур', 'Kuala Lumpur'), 'asia', 'Malay, Ingliz', 'MYR', 'UTC+8', 'spring,summer,autumn', ['budget', 'comfortable', 'family', 'adventure', 'shopping', 'religious'], [30, 65, 150], 85,
                $this->t("Malayziya — tropik tabiat, zamonaviy shaharlar va rang-barang madaniyat. Musulmonlar uchun halol ovqat topish oson.", 'Малайзия — тропическая природа, современные города и яркая культура. Халяльную еду легко найти.', 'Malaysia offers tropical nature, modern cities and a rich blend of cultures. Halal food is easy to find.'),
                $this->t("Yil bo'yi issiq va nam, yomg'irlar tez-tez bo'ladi. Qirg'oq mintaqalarida mavsumlar turlicha.", null, 'Hot and humid all year with frequent rain. Seasons differ by coastal region.'),
                $this->t("Odatda xavfsiz. Gavjum bozor va transportda mayda o'g'irliklardan ehtiyot bo'ling.", null, 'Generally safe. Beware of petty theft in markets and on public transport.'),
                $this->t("Masjid va diniy joylarda kiyim yopiq bo'lishi kerak. O'ng qo'ldan foydalanish odobli hisoblanadi.", null, 'Dress modestly at mosques and religious sites. Using the right hand is considered polite.'),
                $this->t("Kuala-Lumpurda LRT, MRT va monorels bor; «Touch \'n Go» kartasi qulay. Grab ilovasi keng ishlatiladi.", null, "Kuala Lumpur has LRT, MRT and monorail; a Touch 'n Go card is handy. The Grab app is widely used."),
                ['Grab', "Touch 'n Go", 'Google Maps'], ['Favqulodda' => '999']],
            ['thailand', 'TH', $this->t('Tailand', 'Таиланд', 'Thailand', 'Тайланд', 'Таиланд', 'Tayland'), $this->t('Bangkok', 'Бангкок', 'Bangkok'), 'asia', 'Tay', 'THB', 'UTC+7', 'autumn,winter', ['budget', 'comfortable', 'family', 'adventure', 'luxury'], [35, 80, 200], 98,
                $this->t("Tailand — plyajlar, ibodatxonalar va mazali ko'cha taomlari mamlakati. Bangkok orqali orollarga va shimolga yo'l olinadi.", 'Таиланд — страна пляжей, храмов и вкусной уличной еды. Через Бангкок можно добраться до островов и на север.', 'Thailand is a land of beaches, temples and tasty street food. Bangkok is the gateway to the islands and the north.'),
                $this->t("Eng qulay vaqt — noyabrdan fevralgacha (quruq va nisbatan salqin). Iyundan oktyabrgacha yomg'ir mavsumi.", null, 'Best from November to February (dry and cooler). Rainy season runs June to October.'),
                $this->t("Turistlar uchun odatda xavfsiz. Tuk-tuk va taksida narxni oldindan kelishing yoki hisoblagichdan foydalaning.", null, 'Generally safe for tourists. Agree the fare in advance or use the meter in tuk-tuks and taxis.'),
                $this->t("Ibodatxonalarda yelka va tizzalar yopiq bo'lishi shart. Qirol oilasiga hurmatsizlik qonun bilan taqiqlangan.", null, 'Shoulders and knees must be covered in temples. Disrespect to the royal family is illegal.'),
                $this->t("Bangkokda BTS va MRT tez va qulay; Grab va Bolt ilovalari bor.", null, 'Bangkok has fast BTS and MRT lines; Grab and Bolt apps are available.'),
                ['Grab', 'Bolt', 'Google Maps'], ['Turistik politsiya' => '1155', 'Favqulodda' => '191']],
        ];
        $rest = [ // [slug, iso2, names, capital, continent, languages, currency, tz, seasons, types, budgets, popularity]
            ['saudi-arabia', 'SA', $this->t('Saudiya Arabistoni', 'Саудовская Аравия', 'Saudi Arabia', 'Сауд Арабиясы', 'Сауд Арабиясы', 'Saudiya Arabstanı'), $this->t('Ar-Riyod', 'Эр-Рияд', 'Riyadh'), 'asia', 'Arab', 'SAR', 'UTC+3', 'winter', ['religious'], [60, 120, 300], 80],
            ['south-korea', 'KR', $this->t('Janubiy Koreya', 'Южная Корея', 'South Korea', 'Оңтүстік Корея', 'Түштүк Корея', 'Qubla Koreya'), $this->t('Seul', 'Сеул', 'Seoul'), 'asia', 'Koreys', 'KRW', 'UTC+9', 'spring,autumn', ['comfortable', 'shopping'], [60, 120, 280], 75],
            ['japan', 'JP', $this->t('Yaponiya', 'Япония', 'Japan', 'Жапония', 'Жапония', 'Yaponiya'), $this->t('Tokio', 'Токио', 'Tokyo'), 'asia', 'Yapon', 'JPY', 'UTC+9', 'spring,autumn', ['comfortable', 'historical', 'luxury'], [70, 140, 320], 78],
            ['france', 'FR', $this->t('Fransiya', 'Франция', 'France', 'Франция', 'Франция', 'Franciya'), $this->t('Parij', 'Париж', 'Paris'), 'europe', 'Frantsuz', 'EUR', 'UTC+1', 'spring,summer,autumn', ['comfortable', 'luxury', 'historical', 'shopping'], [90, 170, 400], 82],
            ['italy', 'IT', $this->t('Italiya', 'Италия', 'Italy', 'Италия', 'Италия', 'Italiya'), $this->t('Rim', 'Рим', 'Rome'), 'europe', 'Italyan', 'EUR', 'UTC+1', 'spring,autumn', ['comfortable', 'historical', 'luxury'], [85, 160, 380], 80],
            ['germany', 'DE', $this->t('Germaniya', 'Германия', 'Germany', 'Германия', 'Германия', 'Germaniya'), $this->t('Berlin', 'Берлин', 'Berlin'), 'europe', 'Nemis', 'EUR', 'UTC+1', 'spring,summer,autumn', ['comfortable', 'historical', 'family'], [80, 150, 350], 70],
            ['united-kingdom', 'GB', $this->t('Buyuk Britaniya', 'Великобритания', 'United Kingdom', 'Ұлыбритания', 'Улуу Британия', 'Uliwbritaniya'), $this->t('London', 'Лондон', 'London'), 'europe', 'Ingliz', 'GBP', 'UTC+0', 'spring,summer', ['comfortable', 'luxury', 'historical'], [100, 190, 450], 72],
            ['egypt', 'EG', $this->t('Misr', 'Египет', 'Egypt', 'Мысыр', 'Египет', 'Mısır'), $this->t('Qohira', 'Каир', 'Cairo'), 'africa', 'Arab', 'EGP', 'UTC+2', 'autumn,winter,spring', ['budget', 'historical', 'family'], [35, 70, 170], 84],
            ['indonesia', 'ID', $this->t('Indoneziya', 'Индонезия', 'Indonesia', 'Индонезия', 'Индонезия', 'Indoneziya'), $this->t('Jakarta', 'Джакарта', 'Jakarta'), 'asia', 'Indonez', 'IDR', 'UTC+7', 'spring,summer', ['budget', 'adventure', 'luxury'], [35, 80, 220], 76],
            ['georgia', 'GE', $this->t('Gruziya', 'Грузия', 'Georgia', 'Грузия', 'Грузия', 'Gruziya'), $this->t('Tbilisi', 'Тбилиси', 'Tbilisi'), 'asia', 'Gruzin', 'GEL', 'UTC+4', 'spring,summer,autumn', ['budget', 'comfortable', 'adventure'], [40, 80, 180], 74],
            ['azerbaijan', 'AZ', $this->t('Ozarbayjon', 'Азербайджан', 'Azerbaijan', 'Әзірбайжан', 'Азербайжан', 'Ázerbayjan'), $this->t('Boku', 'Баку', 'Baku'), 'asia', 'Ozarbayjon', 'AZN', 'UTC+4', 'spring,autumn', ['budget', 'comfortable', 'historical'], [45, 85, 190], 73],
        ];

        $cm = [];
        foreach ($countries as $i => [$slug, $iso, $name, $cap, $cont, $langs, $cur, $tz, $seasons, $types, $bud, $pop, $sum, $clim, $safe, $cust, $trans, $apps, $emerg]) {
            $cm[$slug] = Country::updateOrCreate(['slug' => $slug], [
                'iso2' => $iso, 'name' => $name, 'capital' => $cap, 'summary' => $sum, 'climate' => $clim, 'safety' => $safe, 'customs' => $cust,
                'local_transport' => $trans, 'continent' => $cont, 'languages' => $langs, 'currency_code' => $cur, 'timezone' => $tz,
                'best_season' => $seasons, 'travel_types' => $types, 'local_apps' => $apps, 'emergency_numbers' => $emerg,
                'daily_budget_budget' => $bud[0], 'daily_budget_comfort' => $bud[1], 'daily_budget_luxury' => $bud[2], 'popularity' => $pop, 'is_demo' => true,
            ]);
        }
        foreach ($rest as [$slug, $iso, $name, $cap, $cont, $langs, $cur, $tz, $seasons, $types, $bud, $pop]) {
            $cm[$slug] = Country::updateOrCreate(['slug' => $slug], [
                'iso2' => $iso, 'name' => $name, 'capital' => $cap, 'continent' => $cont, 'languages' => $langs, 'currency_code' => $cur, 'timezone' => $tz,
                'best_season' => $seasons, 'travel_types' => $types, 'daily_budget_budget' => $bud[0], 'daily_budget_comfort' => $bud[1], 'daily_budget_luxury' => $bud[2],
                'popularity' => $pop, 'is_demo' => true,
            ]);
        }

        $cities = [
            ['istanbul', 'turkey', $this->t('Istanbul', 'Стамбул', 'Istanbul', 'Стамбул', 'Стамбул', 'Istanbul'), 41.0082, 28.9784, 100, $this->t("Istanbul — ikki qit'a o'rtasidagi tarixiy shahar: Ayasofya, Topqapi saroyi, Katta bozor va Bosfor sayohatlari.", 'Стамбул — исторический город на двух континентах: Айя-София, дворец Топкапы, Гранд-базар и прогулки по Босфору.', 'Istanbul is a historic city spanning two continents: Hagia Sophia, Topkapi Palace, the Grand Bazaar and Bosphorus cruises.')],
            ['antalya', 'turkey', $this->t('Antalya', 'Анталья', 'Antalya', 'Анталия', 'Анталия', 'Antalya'), 36.8969, 30.7133, 92, $this->t("Antalya — O'rta dengiz sohilidagi kurort: Kaleichi eski shahri, plyajlar va qadimiy Aspendos teatri.", 'Анталья — курорт на побережье Средиземного моря: старый город Калеичи, пляжи и античный театр Аспендос.', 'Antalya is a Mediterranean resort: the Kaleiçi old town, beaches and the ancient theatre of Aspendos.')],
            ['cappadocia', 'turkey', $this->t('Kappadokiya', 'Каппадокия', 'Cappadocia', 'Каппадокия', 'Каппадокия', 'Kappadokiya'), 38.6431, 34.8289, 88, $this->t("Kappadokiya — g'alati tosh shakllari, g'or uylar va havo sharlari bilan mashhur hudud.", 'Каппадокия — регион, известный причудливыми скалами, пещерными домами и воздушными шарами.', 'Cappadocia is famous for its fairy chimneys, cave dwellings and hot-air balloons.')],
            ['dubai', 'uae', $this->t('Dubay', 'Дубай', 'Dubai', 'Дубай', 'Дубай', 'Dubay'), 25.2048, 55.2708, 97, $this->t("Dubay — dunyodagi eng baland bino Burj Xalifa, xarid markazlari va zamonaviy sayohat shahri.", 'Дубай — город с самым высоким зданием мира Бурдж-Халифа, торговыми центрами и современным туризмом.', 'Dubai is home to the world\'s tallest building, Burj Khalifa, huge malls and modern tourism.')],
            ['kuala-lumpur', 'malaysia', $this->t('Kuala-Lumpur', 'Куала-Лумпур', 'Kuala Lumpur', 'Куала-Лумпур', 'Куала-Лумпур', 'Kuala-Lumpur'), 3.1390, 101.6869, 85, $this->t("Kuala-Lumpur — Petronas minoralari, Batu g'orlari va xilma-xil taomlar shahri.", 'Куала-Лумпур — город башен Петронас, пещер Бату и разнообразной кухни.', 'Kuala Lumpur is the city of the Petronas Towers, Batu Caves and diverse cuisine.')],
            ['bangkok', 'thailand', $this->t('Bangkok', 'Бангкок', 'Bangkok', 'Бангкок', 'Бангкок', 'Bangkok'), 13.7563, 100.5018, 96, $this->t("Bangkok — qirollik saroyi, ibodatxonalar, suzuvchi bozorlar va ko'cha taomlari shahri.", 'Бангкок — город королевского дворца, храмов, плавучих рынков и уличной еды.', 'Bangkok is a city of royal palaces, temples, floating markets and street food.')],
        ];
        $ct = [];
        foreach ($cities as [$slug, $country, $name, $lat, $lng, $pop, $sum]) {
            $ct[$slug] = City::updateOrCreate(['slug' => $slug], ['country_id' => $cm[$country]->id, 'name' => $name, 'summary' => $sum, 'lat' => $lat, 'lng' => $lng, 'popularity' => $pop, 'is_demo' => true]);
        }

        foreach ([['istanbul', 'IST', 'Istanbul Airport', 41.2753, 28.7519], ['istanbul', 'SAW', 'Sabiha Gökçen International Airport', 40.8986, 29.3092], ['antalya', 'AYT', 'Antalya Airport', 36.8987, 30.8005],
                 ['cappadocia', 'NAV', 'Nevşehir Kapadokya Airport', 38.7719, 34.5345], ['dubai', 'DXB', 'Dubai International Airport', 25.2532, 55.3657],
                 ['kuala-lumpur', 'KUL', 'Kuala Lumpur International Airport', 2.7456, 101.7099], ['bangkok', 'BKK', 'Suvarnabhumi Airport', 13.6900, 100.7501]] as [$c, $iata, $n, $la, $lo]) {
            Airport::updateOrCreate(['iata' => $iata], ['city_id' => $ct[$c]->id, 'name' => $n, 'lat' => $la, 'lng' => $lo]);
        }

        // [slug, city, category, names, lat, lng, hours, entrance(est), duration, rating(null), popularity]
        $attr = [
            ['ayasofya', 'istanbul', 'historical', $this->t('Ayasofya', 'Айя-София', 'Hagia Sophia'), 41.0086, 28.9802, null, 25, 60, 100, "Vizantiya davrida qurilgan mashhur me'moriy yodgorlik."],
            ['topkapi-saroyi', 'istanbul', 'museum', $this->t('Topqapi saroyi', 'Дворец Топкапы', 'Topkapi Palace'), 41.0115, 28.9834, null, 30, 180, 95, "Usmonli sultonlarining sobiq qarorgohi, hozirda muzey."],
            ['moviy-machit', 'istanbul', 'religious', $this->t("Sulton Ahmad (Moviy) masjidi", 'Мечеть Султанахмет (Голубая)', 'Sultan Ahmed (Blue) Mosque'), 41.0054, 28.9768, null, 0, 45, 94, "Oltita minorali mashhur masjid. Kirishda kiyim qoidalariga rioya qiling."],
            ['bazilika-sisterna', 'istanbul', 'historical', $this->t('Bazilika sardobasi', 'Цистерна Базилика', 'Basilica Cistern'), 41.0084, 28.9778, null, 25, 40, 90, "Yer osti qadimiy suv ombori."],
            ['katta-bozor', 'istanbul', 'shopping', $this->t('Katta bozor', 'Гранд-базар', 'Grand Bazaar'), 41.0107, 28.9680, null, 0, 120, 93, "Dunyodagi eng qadimgi va eng katta yopiq bozorlardan biri."],
            ['galata-minorasi', 'istanbul', 'historical', $this->t('Galata minorasi', 'Галатская башня', 'Galata Tower'), 41.0256, 28.9741, null, 30, 60, 88, "Shahar panoramasi ochiladigan o'rta asr minorasi."],
            ['sulaymoniya-machiti', 'istanbul', 'religious', $this->t('Sulaymoniya masjidi', 'Мечеть Сулеймание', 'Süleymaniye Mosque'), 41.0162, 28.9640, null, 0, 45, 84, "Mimar Sinan asarlaridan biri."],
            ['kaleichi', 'antalya', 'historical', $this->t('Kaleichi (eski shahar)', 'Калеичи (старый город)', 'Kaleiçi (Old Town)'), 36.8841, 30.7056, null, 0, 150, 90, "Tor ko'chalar va tarixiy uylar."],
            ['hadrian-darvozasi', 'antalya', 'historical', $this->t('Hadrian darvozasi', 'Ворота Адриана', "Hadrian's Gate"), 36.8844, 30.7070, null, 0, 20, 70, "Rim davridagi tantanali darvoza."],
            ['antalya-muzeyi', 'antalya', 'museum', $this->t('Antalya muzeyi', 'Музей Анталии', 'Antalya Museum'), 36.8857, 30.6819, null, 10, 120, 72, "Mintaqa arxeologiyasi bo'yicha asosiy muzey."],
            ['aspendos', 'antalya', 'historical', $this->t('Aspendos teatri', 'Театр Аспендос', 'Aspendos Theatre'), 36.9392, 31.1722, null, 15, 90, 80, "Eng yaxshi saqlangan Rim teatrlaridan biri."],
            ['goreme-muzeyi', 'cappadocia', 'museum', $this->t("Göreme ochiq osmon muzeyi", 'Музей под открытым небом Гёреме', 'Göreme Open Air Museum'), 38.6431, 34.8453, null, 20, 120, 92, "Qoya cherkovlari va freskalar."],
            ['uchisar-qalasi', 'cappadocia', 'historical', $this->t("Uchisar qal'asi", 'Крепость Учхисар', 'Uçhisar Castle'), 38.6307, 34.8043, null, 8, 60, 78, "Kappadokiyaning eng baland nuqtalaridan biri."],
            ['derinkuyu', 'cappadocia', 'historical', $this->t('Derinkuyu yer osti shahri', 'Подземный город Деринкую', 'Derinkuyu Underground City'), 38.3742, 34.7342, null, 12, 75, 80, "Ko'p qavatli qadimiy yer osti shahri."],
            ['burj-xalifa', 'dubai', 'entertainment', $this->t('Burj Xalifa', 'Бурдж-Халифа', 'Burj Khalifa'), 25.1972, 55.2744, null, 45, 120, 100, "Dunyodagi eng baland bino; ko'rish maydonchalari bor."],
            ['dubay-moll', 'dubai', 'shopping', $this->t('Dubay Moll', 'Дубай Молл', 'The Dubai Mall'), 25.1985, 55.2796, null, 0, 240, 96, "Dunyoning eng yirik xarid markazlaridan biri."],
            ['dubay-freym', 'dubai', 'entertainment', $this->t('Dubay Freym', 'Дубай Фрейм', 'Dubai Frame'), 25.2350, 55.3004, null, 15, 60, 80, "Shaharning eski va yangi qismlarini ko'rsatuvchi inshoot."],
            ['al-fahidi', 'dubai', 'cultural', $this->t('Al-Fahidi tarixiy mahallasi', 'Исторический квартал Аль-Фахиди', 'Al Fahidi Historical District'), 25.2637, 55.2972, null, 0, 90, 75, "Dubayning eski uslubdagi mahallasi."],
            ['jumeyra-machiti', 'dubai', 'religious', $this->t('Jumeyra masjidi', 'Мечеть Джумейра', 'Jumeirah Mosque'), 25.2335, 55.2655, null, 0, 45, 70, "Turistlar uchun ochiq masjid (rasmiy sayohatlar orqali)."],
            ['petronas', 'kuala-lumpur', 'entertainment', $this->t('Petronas egizak minoralari', 'Башни Петронас', 'Petronas Twin Towers'), 3.1579, 101.7116, null, 25, 90, 98, "Shahar ramzi, ko'rish maydonchasi bor."],
            ['batu-gorlari', 'kuala-lumpur', 'religious', $this->t("Batu g'orlari", 'Пещеры Бату', 'Batu Caves'), 3.2379, 101.6840, null, 0, 120, 90, "Hindu ibodatxonasi joylashgan ohaktosh g'orlari."],
            ['kl-minorasi', 'kuala-lumpur', 'entertainment', $this->t('KL televideniye minorasi', 'Телебашня KL', 'KL Tower'), 3.1528, 101.7038, null, 15, 60, 72, "Panoramali ko'rish maydonchasi."],
            ['merdeka', 'kuala-lumpur', 'historical', $this->t('Merdeka maydoni', 'Площадь Мердека', 'Merdeka Square'), 3.1488, 101.6933, null, 0, 40, 68, "Mustaqillik e'lon qilingan tarixiy maydon."],
            ['markaziy-bozor-kl', 'kuala-lumpur', 'shopping', $this->t('Markaziy bozor', 'Центральный рынок', 'Central Market'), 3.1446, 101.6955, null, 0, 90, 66, "Hunarmandchilik buyumlari bozori."],
            ['katta-saroy', 'bangkok', 'historical', $this->t('Katta qirollik saroyi', 'Большой дворец', 'Grand Palace'), 13.7500, 100.4913, null, 16, 150, 100, "Tay qirollarining tarixiy saroy majmuasi. Kiyim qoidalari qat'iy."],
            ['vat-po', 'bangkok', 'religious', $this->t('Vat Po', 'Ват Пхо', 'Wat Pho'), 13.7465, 100.4927, null, 8, 75, 92, "Yotgan Budda haykali bilan mashhur ibodatxona."],
            ['vat-arun', 'bangkok', 'religious', $this->t('Vat Arun', 'Ват Арун', 'Wat Arun'), 13.7437, 100.4889, null, 3, 60, 90, "Chao Phraya daryosi bo'yidagi «Tong ibodatxonasi»."],
            ['chatuchak', 'bangkok', 'shopping', $this->t('Chatuchak bozori', 'Рынок Чатучак', 'Chatuchak Market'), 13.7999, 100.5503, 'Odatda hafta oxirlari', 0, 180, 86, "Dunyodagi eng yirik hafta oxiri bozorlaridan biri."],
            ['jim-tompson', 'bangkok', 'museum', $this->t('Jim Tompson uyi', 'Дом Джима Томпсона', 'Jim Thompson House'), 13.7491, 100.5285, null, 5, 60, 65, "Tay ipagi savdogarining uy-muzeyi."],
        ];
        foreach ($attr as [$slug, $city, $cat, $name, $lat, $lng, $hours, $fee, $dur, $pop, $desc]) {
            Attraction::updateOrCreate(['slug' => $slug], [
                'country_id' => $ct[$city]->country_id, 'city_id' => $ct[$city]->id, 'category' => $cat, 'name' => $name,
                'description' => $this->t($desc), 'lat' => $lat, 'lng' => $lng, 'opening_hours' => $hours, 'entrance_usd' => $fee,
                'duration_min' => $dur, 'popularity' => $pop, 'is_demo' => true, 'price_verified' => false,
            ]);
        }

        // Namunaviy mehmonxonalar: nomlar va narxlar DEMO; koordinatalar kiritilmagan.
        $hotelTypes = [['budget', 1, 35, 3, 3.9], ['family', 2, 75, 4, 4.2], ['luxury', 3, 190, 5, 4.6]];
        $hotelNames = ['istanbul' => 'Sultanahmet', 'antalya' => 'Kaleichi', 'cappadocia' => 'Göreme', 'dubai' => 'Deira', 'kuala-lumpur' => 'Bukit Bintang', 'bangkok' => 'Sukhumvit'];
        foreach ($hotelNames as $slug => $area) {
            foreach ($hotelTypes as [$type, $n, $price, $stars, $rating]) {
                Hotel::updateOrCreate(['slug' => "namuna-mehmonxona-$slug-$n"], [
                    'country_id' => $ct[$slug]->country_id, 'city_id' => $ct[$slug]->id, 'type' => $type,
                    'name' => $this->t("Namuna mehmonxona — $area ($n)", "Пример отеля — $area ($n)", "Sample hotel — $area ($n)"),
                    'description' => $this->t("Bu namunaviy (demo) yozuv. Haqiqiy mehmonxona emas; nom, narx va xususiyatlar tasdiqlanmagan.", 'Это демонстрационная запись, не реальный отель.', 'This is a demonstration entry, not a real hotel.'),
                    'address' => $area, 'price_usd' => $price, 'stars' => $stars, 'rating' => null,
                    'facilities' => ['Wi-Fi', 'Nonushta', 'Konditsioner'], 'checkin' => '14:00', 'checkout' => '12:00',
                    'popularity' => 50 + $n * 10, 'is_demo' => true, 'price_verified' => false,
                ]);
            }
        }

        // Namunaviy restoranlar (demo).
        $rcats = [['uzbek', 'O\'zbek oshxonasi', true, true, 9, 4.3], ['halal', 'Halol mahalliy taomlar', false, true, 8, 4.1], ['local', 'Mahalliy oshxona', false, false, 7, 4.0]];
        foreach ($hotelNames as $slug => $area) {
            foreach ($rcats as $k => [$cat, $cuisine, $uz, $halal, $price, $rating]) {
                Restaurant::updateOrCreate(['slug' => "namuna-restoran-$slug-$cat"], [
                    'country_id' => $ct[$slug]->country_id, 'city_id' => $ct[$slug]->id, 'category' => $cat, 'cuisine' => $cuisine,
                    'is_uzbek' => $uz, 'is_halal' => $halal, 'avg_meal_usd' => $price, 'rating' => null, 'address' => $area,
                    'name' => $this->t($uz ? "Namuna o'zbek oshxonasi — $area" : "Namuna restoran — $area ($cat)", $uz ? "Пример узбекского ресторана — $area" : "Пример ресторана — $area", $uz ? "Sample Uzbek restaurant — $area" : "Sample restaurant — $area"),
                    'description' => $this->t("Bu namunaviy (demo) yozuv. Haqiqiy restoran emas; narx va reyting tasdiqlanmagan."),
                    'popularity' => 60 - $k * 10, 'is_demo' => true, 'price_verified' => false,
                ]);
            }
        }

        // Aviakompaniyalar: nomi va rasmiy sayti haqiqiy; ta'riflar umumiy.
        $airlines = [
            ['uzbekistan-airways', 'HY', 'Uzbekistan Airways', 'https://www.uzairways.com', "Toshkent", 4.0, "O'zbekistonning milliy aviakompaniyasi."],
            ['turkish-airlines', 'TK', 'Turkish Airlines', 'https://www.turkishairlines.com', 'Istanbul', 4.4, "Turkiyaning milliy aviakompaniyasi; Istanbul orqali ko'plab yo'nalishlar."],
            ['centrum-air', 'C6', 'Centrum Air', 'https://centrum.air', 'Toshkent', 3.8, "O'zbekistonlik xususiy aviakompaniya."],
            ['flydubai', 'FZ', 'Flydubai', 'https://www.flydubai.com', 'Dubay', 4.0, "Dubayda joylashgan arzon narxli aviakompaniya."],
            ['emirates', 'EK', 'Emirates', 'https://www.emirates.com', 'Dubay', 4.6, "Dubay aviakompaniyasi; dunyo bo'ylab yo'nalishlar."],
            ['qatar-airways', 'QR', 'Qatar Airways', 'https://www.qatarairways.com', 'Doha', 4.6, "Qatar milliy aviakompaniyasi; Doha orqali ulanishlar."],
            ['air-astana', 'KC', 'Air Astana', 'https://airastana.com', 'Olmaota / Ostona', 4.1, "Qozog'iston milliy aviakompaniyasi."],
        ];
        $am = [];
        foreach ($airlines as [$slug, $code, $name, $site, $hub, $rating, $desc]) {
            $am[$slug] = Airline::updateOrCreate(['slug' => $slug], ['code' => $code, 'name' => $name, 'website' => $site, 'baggage_url' => $site, 'hub' => $hub, 'rating' => null, 'description' => $this->t($desc), 'is_demo' => true]);
        }

        // Namunaviy reyslar: davomiylik taxminiy, narx namunaviy, jadval kiritilmagan.
        $flights = [
            ['uzbekistan-airways', 'istanbul', 275, true, null, 23, 320], ['turkish-airlines', 'istanbul', 270, true, null, 23, 360],
            ['turkish-airlines', 'antalya', 420, false, 'Istanbul', 23, 400], ['turkish-airlines', 'cappadocia', 430, false, 'Istanbul', 23, 410],
            ['flydubai', 'dubai', 220, true, null, 20, 280], ['emirates', 'dubai', 225, true, null, 30, 420], ['uzbekistan-airways', 'dubai', 220, true, null, 23, 340],
            ['uzbekistan-airways', 'bangkok', 330, true, null, 20, 480], ['qatar-airways', 'bangkok', 600, false, 'Doha', 25, 560],
            ['qatar-airways', 'kuala-lumpur', 690, false, 'Doha', 25, 620], ['turkish-airlines', 'kuala-lumpur', 780, false, 'Istanbul', 23, 640],
        ];
        Flight::query()->delete();
        foreach ($flights as [$a, $c, $dur, $direct, $transit, $bag, $price]) {
            Flight::create(['airline_id' => $am[$a]->id, 'from_city' => 'Toshkent', 'to_city_id' => $ct[$c]->id, 'duration_min' => $dur, 'is_direct' => $direct, 'transit' => $transit, 'baggage_kg' => $bag, 'est_price_usd' => $price, 'is_sample' => true]);
        }

        // Transport (narxlar tasdiqlanmagan — kiritilmagan)
        $tr = [
            ['istanbul', 'metro', 'Metro Istanbul', 'https://www.metro.istanbul', "Metro, tramvay va Marmaray uchun «Istanbulkart» ishlatiladi."],
            ['istanbul', 'taxi', 'BiTaksi', 'https://bitaksi.com', "Mahalliy taksi ilovasi."], ['istanbul', 'taxi', 'Uber', 'https://www.uber.com', "Taksi chaqirish ilovasi."],
            ['antalya', 'taxi', 'BiTaksi', 'https://bitaksi.com', "Mahalliy taksi ilovasi."], ['cappadocia', 'car_rental', "Avtomobil ijarasi", null, "Hududni mustaqil kezish uchun qulay; shartlarni rasmiy kompaniyalardan aniqlang."],
            ['dubai', 'metro', 'Dubai Metro (RTA)', 'https://www.rta.ae', "Metro va tramvay RTA tomonidan boshqariladi."], ['dubai', 'taxi', 'Careem', 'https://www.careem.com', "Taksi chaqirish ilovasi."], ['dubai', 'taxi', 'Uber', 'https://www.uber.com', "Taksi chaqirish ilovasi."],
            ['kuala-lumpur', 'metro', 'Rapid KL (LRT/MRT)', 'https://myrapid.com.my', "Jamoat transporti operatori."], ['kuala-lumpur', 'taxi', 'Grab', 'https://www.grab.com', "Mintaqadagi asosiy taksi ilovasi."],
            ['bangkok', 'metro', 'BTS Skytrain', 'https://www.bts.co.th', "Yuqori yo'l poyezdi."], ['bangkok', 'metro', 'MRT', 'https://www.mrta.co.th', "Yer osti metro."], ['bangkok', 'taxi', 'Grab', 'https://www.grab.com', "Taksi chaqirish ilovasi."], ['bangkok', 'taxi', 'Bolt', 'https://bolt.eu', "Taksi chaqirish ilovasi."],
        ];
        TransportService::query()->delete();
        foreach ($tr as [$c, $type, $name, $site, $desc]) {
            TransportService::create(['city_id' => $ct[$c]->id, 'type' => $type, 'name' => $name, 'website' => $site, 'description' => $this->t($desc), 'price_verified' => false, 'is_demo' => true]);
        }

        // Viza: qoidalar TEKSHIRILMAGAN; rasmiy manbaga havola beriladi.
        foreach ([['turkey', 'https://www.mfa.gov.tr/'], ['uae', 'https://u.ae/en/information-and-services/visa-and-emirates-id'], ['malaysia', 'https://www.imi.gov.my/'], ['thailand', 'https://www.thaievisa.go.th/']] as [$c, $url]) {
            VisaRequirement::updateOrCreate(['country_id' => $cm[$c]->id], [
                'status' => 'check_official', 'official_url' => $url, 'source_url' => $url, 'verification_status' => 'unverified',
                'notes' => $this->t("Viza talablari hali admin tomonidan tasdiqlanmagan. Safardan oldin rasmiy manbani tekshiring.", 'Визовые требования ещё не подтверждены администратором. Проверьте официальный источник перед поездкой.', 'Visa requirements have not yet been verified by an administrator. Check the official source before travelling.'),
            ]);
        }
    }
}
