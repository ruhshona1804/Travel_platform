<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\User;
use Database\Seeders\CurrencySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CurrencySeeder::class);
    }

    public function test_first_visit_is_uzbek_regardless_of_browser_language(): void
    {
        $this->withHeaders(['Accept-Language' => 'ru-RU,ru;q=0.9'])->get('/')
            ->assertOk()->assertSee('Sayohat qilish endi har qachongidan ham oson!', false);
    }

    public function test_language_switching_works_for_all_six_languages(): void
    {
        $expected = ['uz' => 'Mamlakatlar', 'ru' => 'Страны', 'en' => 'Countries', 'kk' => 'Елдер', 'ky' => 'Өлкөлөр', 'kaa' => 'Mámleketler'];
        foreach ($expected as $code => $word) {
            $this->post("/locale/$code")->assertRedirect();
            $this->get('/countries')->assertOk()->assertSee($word, false);
        }
        $this->post('/locale/xx')->assertNotFound();
    }

    public function test_locale_is_saved_for_logged_in_user(): void
    {
        $u = User::factory()->create();
        $this->actingAs($u)->post('/locale/en');
        $this->assertSame('en', $u->fresh()->locale);
    }

    public function test_every_language_defines_the_same_core_keys_as_uzbek(): void
    {
        $uz = array_keys(require lang_path('uz/ui.php'));
        foreach (['ru', 'en', 'kk', 'ky', 'kaa'] as $l) {
            $missing = array_diff($uz, array_keys(require lang_path("$l/ui.php")));
            $this->assertEmpty($missing, "$l/ui.php missing: ".implode(', ', array_slice($missing, 0, 5)));
        }
    }

    public function test_translatable_model_falls_back_to_uzbek(): void
    {
        $c = new Country(['name' => ['uz' => 'Turkiya', 'en' => 'Turkey']]);
        app()->setLocale('en'); $this->assertSame('Turkey', $c->tr('name'));
        app()->setLocale('ru'); $this->assertSame('Turkiya', $c->tr('name'));
        app()->setLocale('kaa'); $this->assertSame('Turkiya', $c->tr('name'));
    }

    public function test_admin_field_labels_exist_for_all_resources(): void
    {
        $labels = require lang_path('uz/admin.php');
        foreach (config('admin') as $res => $cfg) {
            $this->assertArrayHasKey($res, $labels, "admin.$res");
            foreach ($cfg['fields'] as $f) $this->assertArrayHasKey('f_'.$f['name'], $labels, "admin.f_{$f['name']}");
            foreach ($cfg['columns'] as $c) $this->assertArrayHasKey('f_'.explode(':', $c)[0], $labels, "admin.f_$c");
        }
    }
}
