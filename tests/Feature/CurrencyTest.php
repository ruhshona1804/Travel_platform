<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\User;
use App\Services\Currency\CurrencyService;
use App\Services\Currency\ExchangeRateProvider;
use Database\Seeders\CurrencySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CurrencySeeder::class);
        Currency::where('code', 'UZS')->update(['rate_per_usd' => 12000]);
        app(CurrencyService::class)->flush();
    }

    /** Haqiqiy tarmoqqa chiqmaslik uchun soxta provayder bilan xizmat. */
    private function serviceWith(?array $rates): CurrencyService
    {
        $svc = new CurrencyService(new class($rates) implements ExchangeRateProvider {
            public function __construct(private ?array $r) {}
            public function fetchRatesPerUsd(): ?array { return $this->r; }
        });
        $this->app->instance(CurrencyService::class, $svc);
        return $svc;
    }

    public function test_conversion_math(): void
    {
        $svc = app(CurrencyService::class);
        $this->assertEqualsWithDelta(1200000, $svc->fromUsd(100, 'UZS'), 0.01);
        $this->assertEqualsWithDelta(100, $svc->toUsd(1200000, 'UZS'), 0.01);
        $this->assertEqualsWithDelta(92, $svc->convert(100, 'USD', 'EUR'), 0.01);
    }

    public function test_dual_price_format(): void
    {
        $this->assertSame('$100 ≈ 1,200,000 UZS', price(100.0));
        session(['currency' => 'UZS']);
        $this->assertSame('1,200,000 UZS ≈ $100', price(100.0));
    }

    public function test_seeded_rates_are_flagged_stale_not_live(): void
    {
        $this->assertTrue(app(CurrencyService::class)->isStale());
        $this->get('/currency')->assertSee(__('ui.rates_stale'));
    }

    public function test_refresh_uses_provider_and_marks_fresh(): void
    {
        $svc = $this->serviceWith(['UZS' => 12500.0, 'EUR' => 0.9]);
        $this->assertTrue($svc->refresh());
        $this->assertEquals(12500, Currency::find('UZS')->rate_per_usd);
        $this->assertFalse($svc->isStale());
    }

    public function test_provider_failure_keeps_saved_rates(): void
    {
        $this->assertFalse($this->serviceWith(null)->refresh());
        $this->assertEquals(12000, Currency::find('UZS')->rate_per_usd);
    }

    public function test_manual_rates_survive_api_refresh(): void
    {
        Currency::where('code', 'EUR')->update(['is_manual' => true, 'rate_per_usd' => 0.5]);
        $this->serviceWith(['EUR' => 0.95, 'UZS' => 12100.0])->refresh();
        $this->assertEquals(0.5, Currency::find('EUR')->rate_per_usd);
    }

    public function test_convert_endpoint_and_validation(): void
    {
        $this->getJson('/currency/convert?amount=100&from=USD&to=UZS')->assertOk()->assertJsonPath('result', 1200000)->assertJsonPath('stale', true);
        $this->getJson('/currency/convert?amount=abc&from=USD&to=UZS')->assertStatus(422);
        $this->getJson('/currency/convert?amount=1&from=USD&to=ZZZ')->assertStatus(422);
    }

    public function test_currency_switch_persists_in_session_and_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post('/currency/UZS')->assertRedirect();
        $this->assertSame('UZS', session('currency'));
        $this->assertSame('UZS', $user->fresh()->currency);
        $this->post('/currency/XXX')->assertNotFound();
    }

    public function test_admin_can_set_manual_rate(): void
    {
        $admin = User::factory()->create(); $admin->forceFill(['role' => 'admin'])->save();
        $this->actingAs($admin)->put('/admin/currencies/TRY', ['rate_per_usd' => 40, 'is_manual' => 1, 'is_active' => 1])->assertSessionHasNoErrors();
        $c = Currency::find('TRY');
        $this->assertEquals(40, $c->rate_per_usd);
        $this->assertTrue($c->is_manual);
        $this->assertSame('manual', $c->source);
    }
}
