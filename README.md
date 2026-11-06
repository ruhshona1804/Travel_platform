<<<<<<< HEAD
# Travel Platform

O'zbekistonlik sayohatchilar uchun xalqaro sayohat platformasi (Laravel 13, PHP 8.4, Tailwind 4, Alpine.js, Leaflet, Chart.js).
Nom va brend `Sozlamalar` (admin panel) yoki `APP_NAME` orqali o'zgaradi.

## O'rnatish
```bash
composer install
npm install && npm run build        # dev: npm run dev
cp .env.example .env
php artisan key:generate
```
`.env` da MySQL ma'lumotlarini yozing (`DB_*`), shuningdek **birinchi Super Admin** uchun `ADMIN_EMAIL` va `ADMIN_PASSWORD` (kamida 10 belgi) ni o'zingiz belgilang. Parol kodda saqlanmaydi.

## Ma'lumotlar bazasi (MySQL)
```sql
CREATE DATABASE travel_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'travel'@'localhost' IDENTIFIED BY 'kuchli-parol';
GRANT ALL ON travel_platform.* TO 'travel'@'localhost';
```
```bash
php artisan migrate --seed          # jadvallar + valyutalar + Super Admin + demo ma'lumotlar
php artisan storage:link
php artisan currency:update         # jonli valyuta kurslarini yuklash
php artisan serve
```
Kurslarni avtomatik yangilash uchun cron: `* * * * * php artisan schedule:run` (har 6 soatda).

## Admin kirish
`/login` sahifasida `ADMIN_EMAIL` / `ADMIN_PASSWORD` bilan kiring, so'ng `/admin`. Rollar: Super Admin, Admin, Muharrir (kontentni tahrirlaydi, o'chira olmaydi), Foydalanuvchi.

## API sozlamalari (barchasi ixtiyoriy)
| Funksiya | .env | Holat sozlanmasa |
|---|---|---|
| AI reja matnlari va yordamchi | `AI_API_KEY`, `AI_MODEL` | Qoidalarga asoslangan reja ishlaydi; yordamchi "sozlanmagan" deb javob beradi |
| Valyuta kurslari | `EXCHANGE_PROVIDER` (hozir `open_er_api`, kalitsiz) | Oxirgi saqlangan kurs + "eskirgan" ogohlantirishi |
| Google bilan kirish | `GOOGLE_CLIENT_ID/SECRET` | Tugma ko'rinmaydi |
| Jonli parvozlar | `FLIGHT_PROVIDER` | **Hali provayder yozilmagan**: faqat "namuna" reyslar, rasmiy saytlarga havolalar |
| Pochta (parol tiklash, tasdiqlash) | `MAIL_*` | `log` drayveri: xatlar `storage/logs` ga yoziladi |

## Halollik qoidalari
- Demo yozuvlar `is_demo` bilan belgilanadi; mehmonxona/restoran nomlari va narxlari namunaviy, ularda reyting va koordinata yo'q.
- Diqqatga sazovor joy koordinatalari ommaviy xaritalardan olingan taxminiy qiymatlar; narxlar tasdiqlanmagan ("Taxmin" belgisi).
- Viza ma'lumotlari "tekshirilmagan" holatda; admin rasmiy manba bilan tasdiqlaydi.
- Platforma chipta sotmaydi, xona bandligini ko'rsatmaydi.
- Rasmlar: demo uchun gradient + bayroq; haqiqiy foto admin paneldan yuklanadi.

## Testlar
`php artisan test` (SQLite xotirada).
=======
# Travel_platform
Sayohatni oson rejalashtirish 
>>>>>>> e9c3c91b88fd5af5e3e7a563cfa4bf8a375572e6
