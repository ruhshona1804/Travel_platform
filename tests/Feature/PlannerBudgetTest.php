<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Country;
use App\Models\TravelPlan;
use App\Models\User;
use App\Services\Ai\Planner;
use App\Services\BudgetEstimator;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PlannerBudgetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        config(['travel.ai.api_key' => null]);
    }

    private function payload(array $over = []): array
    {
        return array_merge([
            'departure_city' => 'Toshkent', 'city_id' => City::where('slug', 'istanbul')->value('id'),
            'start_date' => now()->addDays(10)->toDateString(), 'end_date' => now()->addDays(16)->toDateString(),
            'travelers' => 2, 'children' => 0, 'budget' => 1000, 'currency' => 'USD', 'style' => 'budget',
            'interests' => ['history'], 'food' => ['uzbek'],
        ], $over);
    }

    public function test_guest_can_generate_rule_based_plan_with_seven_days(): void
    {
        $res = $this->post('/planner', $this->payload());
        $plan = TravelPlan::firstOrFail();
        $res->assertRedirect(route('planner.show', $plan));
        $this->assertSame('rules', $plan->generated_by);
        $this->assertCount(7, $plan->days);
        $this->assertNull($plan->user_id);
        $this->assertGreaterThan(0, $plan->total_usd);
        $this->get(route('planner.show', $plan))->assertOk()->assertSee(__('ui.plan_disclaimer'));
    }

    public function test_plan_only_uses_database_places_and_marks_costs_as_estimates(): void
    {
        $this->post('/planner', $this->payload());
        $plan = TravelPlan::first();
        $names = \App\Models\Attraction::pluck('name')->map(fn ($n) => $n['uz'])->all();
        foreach ($plan->days->flatMap->items->where('kind', 'attraction') as $item) {
            $this->assertContains($item->title, $names);
            $this->assertTrue($item->cost_estimated);
        }
        $this->assertTrue($plan->summary['flight']['sample'] ?? false); // parvoz faqat namunaviy
    }

    public function test_guest_plan_is_private_to_session(): void
    {
        $this->post('/planner', $this->payload());
        $plan = TravelPlan::first();
        $this->flushSession();
        $this->get(route('planner.show', $plan))->assertForbidden();
    }

    public function test_user_plan_is_owned_and_deletable_only_by_owner(): void
    {
        $owner = User::factory()->create(); $other = User::factory()->create();
        $this->actingAs($owner)->post('/planner', $this->payload());
        $plan = TravelPlan::first();
        $this->assertSame($owner->id, $plan->user_id);
        $this->actingAs($other)->get(route('planner.show', $plan))->assertForbidden();
        $this->delete(route('dashboard.plans.destroy', $plan))->assertForbidden();
        $this->actingAs($owner)->delete(route('dashboard.plans.destroy', $plan))->assertRedirect();
        $this->assertDatabaseCount('travel_plans', 0);
    }

    public function test_over_budget_is_reported(): void
    {
        $this->post('/planner', $this->payload(['budget' => 50, 'style' => 'luxury']));
        $plan = TravelPlan::first();
        $this->assertTrue($plan->summary['over_budget']);
    }

    public function test_budget_in_other_currency_is_converted(): void
    {
        $this->post('/planner', $this->payload(['budget' => 12000000, 'currency' => 'UZS']));
        $this->assertEqualsWithDelta(1000, TravelPlan::first()->summary['budget_usd'], 1);
    }

    public function test_planner_validation(): void
    {
        $this->post('/planner', $this->payload(['end_date' => now()->toDateString(), 'start_date' => now()->addDays(3)->toDateString()]))->assertSessionHasErrors('end_date');
        $this->post('/planner', $this->payload(['city_id' => 9999]))->assertSessionHasErrors('city_id');
        $this->post('/planner', $this->payload(['style' => 'hacker']))->assertSessionHasErrors('style');
        $this->post('/planner', $this->payload(['currency' => 'ZZZ']))->assertStatus(422);
    }

    public function test_ai_provider_only_enriches_text_and_failure_falls_back(): void
    {
        config(['travel.ai.api_key' => 'test-key', 'travel.ai.provider' => 'anthropic']);
        Http::fake(['api.anthropic.com/*' => Http::response(['content' => [['type' => 'text', 'text' => '{"summary":"<b>Zo\'r reja</b>","day_titles":["Tarix"],"tips":["Erta turing"]}']]])]);
        $this->post('/planner', $this->payload());
        $plan = TravelPlan::first();
        $this->assertSame('ai', $plan->generated_by);
        $this->assertSame("Zo'r reja", $plan->summary['narrative']); // HTML olib tashlanadi
        $this->assertSame('Tarix', $plan->days->first()->title);
        $this->assertCount(7, $plan->days);

        TravelPlan::query()->delete();
        Http::swap(new \Illuminate\Http\Client\Factory());
        Http::fake(['api.anthropic.com/*' => Http::response('boom', 500)]);
        $this->post('/planner', $this->payload());
        $this->assertSame('rules', TravelPlan::first()->generated_by);
    }

    public function test_assistant_reports_missing_configuration(): void
    {
        $this->postJson('/assistant', ['message' => 'Salom'])->assertOk()->assertJsonPath('ok', false);
        $this->postJson('/assistant', ['message' => ''])->assertStatus(422);
    }

    public function test_budget_math(): void
    {
        $est = app(BudgetEstimator::class);
        $s = $est->summarize(['flights' => 300, 'food' => 200, 'hotel' => 500], 2, 5);
        $this->assertEquals(1000, $s['total']);
        $this->assertEquals(500, $s['per_person']);
        $this->assertEquals(200, $s['per_day']);
    }

    public function test_can_i_travel_with_500_dollars(): void
    {
        $turkey = Country::where('slug', 'turkey')->first();
        $r = app(BudgetEstimator::class)->canAfford($turkey, 500, 1, 5);
        $this->assertArrayHasKey('budget', $r['scenarios']);
        $this->assertTrue($r['scenarios']['budget']['total'] > 0);
        $r2 = app(BudgetEstimator::class)->canAfford($turkey, 100, 4, 10);
        $this->assertFalse($r2['enough']);
        $this->getJson('/budget/check?'.http_build_query(['country' => $turkey->id, 'budget' => 500, 'currency' => 'USD', 'travelers' => 1, 'days' => 5]))
            ->assertOk()->assertJsonStructure(['scenarios', 'fits', 'enough', 'budget_usd']);
    }

    public function test_budget_can_be_saved_and_converted_to_usd(): void
    {
        $u = User::factory()->create();
        $this->actingAs($u)->post('/budget', [
            'title' => 'Turkiya', 'travelers' => 2, 'days' => 7, 'currency' => 'UZS',
            'items' => ['flights' => 6000000, 'food' => 1200000, 'bogus' => 5],
        ])->assertRedirect(route('dashboard.budgets'));
        $b = $u->budgets()->with('items')->first();
        $this->assertCount(2, $b->items);
        $uzs = \App\Models\Currency::find('UZS')->rate_per_usd;
        $this->assertEqualsWithDelta(7200000 / $uzs, $b->totalUsd(), 0.01);
        $this->get('/dashboard/budgets')->assertOk()->assertSee('Turkiya');
        $other = User::factory()->create();
        $this->actingAs($other)->delete(route('dashboard.budgets.destroy', $b))->assertForbidden();
    }

    public function test_budget_save_requires_login(): void
    {
        $this->post('/budget', ['title' => 'x', 'travelers' => 1, 'days' => 1, 'currency' => 'USD', 'items' => ['food' => 1]])->assertRedirect('/login');
    }
}
