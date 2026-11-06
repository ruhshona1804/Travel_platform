<?php

use Illuminate\Support\Facades\Schedule;

// Valyuta kurslarini avtomatik yangilash (cron: * * * * * php artisan schedule:run)
Schedule::command('currency:update')->everySixHours();
