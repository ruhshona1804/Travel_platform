<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Sahifalarda tarjima qilinmagan xom kalitlar (masalan "ui.nav_home") ko'rinib qolmasligini tekshiradi. */
class NoRawTranslationKeysTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_raw_keys_leak_on_any_page_in_any_language(): void
    {
        config(['travel.admin' => ['name' => 'Root', 'email' => 'root@example.test', 'password' => 'Sup3r-Secret-Pass']]);
        $this->seed(DatabaseSeeder::class);
        $root = User::where('email', 'root@example.test')->first();
        $hotel = \App\Models\Hotel::first(); $attr = \App\Models\Attraction::first(); $rest = \App\Models\Restaurant::first();

        $public = ['/', '/countries', '/countries/turkey', '/cities/istanbul', '/attractions', '/attractions/'.$attr->slug, '/hotels', '/hotels/'.$hotel->slug,
            '/restaurants', '/restaurants/'.$rest->slug, '/uzbek-food', '/airlines', '/airlines/turkish-airlines', '/flights/compare?city=1', '/visa', '/map',
            '/guides', '/currency', '/planner', '/budget', '/search?q=ist', '/login', '/register', '/forgot-password', '/nope'];
        $private = ['/dashboard', '/dashboard/favorites', '/dashboard/plans', '/dashboard/budgets', '/dashboard/notifications', '/dashboard/profile',
            '/admin', '/admin/users', '/admin/currencies', '/admin/settings', '/admin/r/countries', '/admin/r/countries/create', '/admin/r/hotels/create', '/admin/r/visas/create', '/admin/r/flights/create'];

        $bad = [];
        foreach (array_keys(config('travel.locales')) as $loc) {
            $this->flushSession();
            $this->post("/locale/$loc");
            foreach ($public as $u) $this->scan($this->get($u)->getContent(), "$loc $u", $bad);
            $this->actingAs($root);
            foreach ($private as $u) $this->scan($this->get($u)->getContent(), "$loc $u", $bad);
            $this->app['auth']->logout();
        }
        $this->assertEmpty($bad, "Xom kalitlar:\n".implode("\n", array_slice($bad, 0, 15)));
    }

    private function scan(string $html, string $where, array &$bad): void
    {
        $html = preg_replace('#<(script|style)\b.*?</\1>#si', '', $html);
        $text = strip_tags(preg_replace('#<[^>]+\b(?:placeholder|aria-label|title|alt|value)="([^"]*)"[^>]*>#', ' $1 ', $html));
        if (preg_match_all('/\b(?:ui|admin)\.[a-z][a-z0-9_]*\b/', $text.' '.$html, $m)) {
            foreach (array_unique($m[0]) as $k) {
                if (str_contains($html, 'href="'.$k) || preg_match('#(https?://|/)[^\s"]*'.preg_quote($k, '#').'#', $html)) continue;
                $bad[] = "$where => $k";
            }
        }
    }
}
