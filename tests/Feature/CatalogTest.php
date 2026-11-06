<?php

namespace Tests\Feature;

use App\Models\Attraction;
use App\Models\Country;
use App\Models\Favorite;
use App\Models\Hotel;
use App\Models\User;
use Database\Seeders\CurrencySeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_all_public_pages_render_without_errors(): void
    {
        $country = Country::where('slug', 'turkey')->first();
        $urls = ['/', '/countries', '/countries/turkey', '/cities/istanbul', '/attractions', '/hotels', '/restaurants', '/uzbek-food', '/airlines', '/airlines/turkish-airlines',
            '/flights/compare', '/flights/compare?city=1', '/visa', '/map', '/map/data', '/guides', '/guides/birinchi-marta-xorijga', '/currency', '/planner', '/budget',
            '/search?q=ist', '/search/suggest?q=ist', '/sitemap.xml', '/login', '/register', '/forgot-password',
            '/attractions/'.Attraction::first()->slug, '/hotels/'.Hotel::first()->slug, '/restaurants/'.\App\Models\Restaurant::first()->slug];
        foreach ($urls as $u) $this->get($u)->assertOk();
        $this->get('/does-not-exist')->assertNotFound();
        $this->assertNotNull($country);
    }

    public function test_country_filters(): void
    {
        $this->get('/countries?continent=europe')->assertOk()->assertSee('Fransiya')->assertDontSee('Tailand');
        $this->get('/countries?q=istanbul')->assertOk()->assertSee('Turkiya');
        $this->get('/countries?budget=low')->assertOk()->assertSee('Malayziya');
        $this->get('/countries?type=luxury&season=winter')->assertOk();
    }

    public function test_attraction_filters_and_sort(): void
    {
        $this->get('/attractions?category=shopping')->assertOk()->assertSee('Katta bozor')->assertDontSee('Ayasofya');
        $this->get('/attractions?max_price=0')->assertOk()->assertSee('Katta bozor')->assertDontSee('Burj Xalifa');
        $this->get('/hotels?category=luxury&sort=price')->assertOk();
        $this->get('/restaurants?category=uzbek')->assertOk()->assertSee("Namuna o'zbek oshxonasi");
    }

    public function test_demo_data_is_labelled_and_visa_is_not_claimed_verified(): void
    {
        $this->get('/countries/turkey')->assertSee('Namunaviy (demo) ma')->assertSee(__('ui.verif_unverified'));
        $this->assertDatabaseMissing('visa_requirements', ['verification_status' => 'verified']);
        $this->get('/visa')->assertSee(__('ui.visa_warning'));
        $this->get('/hotels/'.Hotel::first()->slug)->assertSee(__('ui.no_availability'));
    }

    public function test_places_without_coordinates_do_not_break_pages(): void
    {
        $hotel = Hotel::first();
        $this->assertNull($hotel->lat);
        $this->get('/hotels/'.$hotel->slug)->assertOk()->assertSee(__('ui.no_map_data'));
    }

    public function test_flights_are_labelled_as_sample_without_live_provider(): void
    {
        $this->get('/flights/compare?city=1')->assertSee(__('ui.sample'))->assertSee(__('ui.flights_no_live'));
    }

    public function test_map_data_endpoint_filters_types(): void
    {
        $all = $this->getJson('/map/data?types=attraction')->assertOk()->json();
        $this->assertNotEmpty($all);
        $this->assertSame(['attraction'], array_values(array_unique(array_column($all, 'type'))));
        foreach ($all as $p) $this->assertNotNull($p['lat']);
        $this->assertEmpty($this->getJson('/map/data?types=uzbek')->json()); // demo restoranlarda koordinata yo'q
    }

    public function test_favorites_toggle_requires_login_and_works(): void
    {
        $a = Attraction::first();
        $this->post("/favorites/attraction/{$a->id}")->assertRedirect('/login');
        $u = User::factory()->create();
        $this->actingAs($u)->post("/favorites/attraction/{$a->id}")->assertRedirect();
        $this->assertSame(1, Favorite::where('user_id', $u->id)->count());
        $this->get('/dashboard/favorites')->assertOk()->assertSee($a->tr('name'));
        $this->post("/favorites/attraction/{$a->id}");
        $this->assertSame(0, Favorite::where('user_id', $u->id)->count());
        $this->post('/favorites/spaceship/1')->assertNotFound();
    }

    public function test_review_is_saved_and_sanitised(): void
    {
        $a = Attraction::first();
        $u = User::factory()->create();
        $this->actingAs($u)->post("/reviews/attraction/{$a->id}", ['rating' => 5, 'body' => '<script>alert(1)</script>Ajoyib'])->assertSessionHasNoErrors();
        $this->assertSame('alert(1)Ajoyib', $a->reviews()->first()->body);
        $this->post("/reviews/attraction/{$a->id}", ['rating' => 9])->assertSessionHasErrors('rating');
    }

    public function test_search_suggest(): void
    {
        $this->getJson('/search/suggest?q=Istan')->assertOk()->assertJsonFragment(['label' => 'Istanbul']);
        $this->getJson('/search/suggest?q=i')->assertOk()->assertExactJson([]);
    }
}
